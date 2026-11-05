<?php

namespace App\Services\NominalRoll;

use App\Models\Contact;
use App\Models\Employment;
use App\Models\Staff;
use App\Services\StaffRecordService;
use App\Support\XlsxReader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Reads the Registry nominal roll workbook and works out, row by row, what
 * would change in the system. preview() changes nothing; apply() writes the
 * same result. No e-mail is ever sent from here.
 *
 *  - "NOMINAL ROLL" sheet: serving staff — creates or updates staff records,
 *    employment details and contacts.
 *  - "… DISENGAGEMENT" sheets: staff who left — records an Off-Employment
 *    (which changes their status) for staff already in the system.
 */
class NominalRollImporter
{
    private const COLUMNS = [
        'staff_no'   => ['STAFF ID NO', 'STAFF NO', 'STAFF ID'],
        'title'      => ['TITLE'],
        'names'      => ['NAMES OF STAFF', 'NAME OF STAFF'],
        'ippis'      => ['IPPIS'],
        'college'    => ['COLLEGE/INSTITUTE', 'COLLEGE'],
        'dept'       => ['DEPT./UNIT', 'DEPT'],
        'dob'        => ['DATE OF BIRTH'],
        'degree'     => ['HIGHEST DEGREE'],
        'gender'     => ['GENDER'],
        'first_public' => ['FIRST APPT. IN PUBLIC', 'FIRST APPT IN PUBLIC'],
        'first_funaab' => ['FIRST APPT. IN FUNAAB', 'FIRST APPT IN FUNAAB'],
        'confirmation' => ['DATE OF CONFIRMATION'],
        'designation'  => ['CURRENT DESIGNATION', 'DESIGNATION/POST'],
        'present_appt' => ['DATE OF PRESENT APPT'],
        'structure'  => ['SALARY STRUCTURE'],
        'level'      => ['SALARY LEVEL'],
        'level_step' => ['SALARY LEVEL/STEP', 'LEVEL/STEP'],
        'category'   => ['STAFF STATUS'],
        'zone'       => ['GEO-POL', 'GEO POL'],
        'state'      => ['STATE'],
        'lga'        => ['LGA'],
        'senatorial' => ['SENATORIAL'],
        'marital'    => ['MARITAL'],
        'phone'      => ['PHONE'],
        'email'      => ['E-MAIL', 'EMAIL'],
    ];

    /** Labels used in the preview for changed fields. */
    public const FIELD_LABELS = [
        'staff_title_id' => 'Title', 'surname' => 'Surname', 'firstname' => 'First name', 'othernames' => 'Other names',
        'dob' => 'Date of birth', 'gender_id' => 'Gender', 'state_id' => 'State', 'lga_id' => 'LGA',
        'senatorial_district_id' => 'Senatorial district', 'geo_zone_id' => 'Geo-political zone', 'marital_status_id' => 'Marital status',
        'staff_category_id' => 'Category', 'ippis_no' => 'IPPIS No.', 'division_id' => 'College / Division', 'department_id' => 'Department',
        'highest_degree' => 'Highest degree', 'date_public_first_appointment' => 'First appt. (public service)',
        'date_funaab_first_appointment' => 'First appt. (FUNAAB)', 'date_confirmation' => 'Confirmation', 'designation_title' => 'Designation',
        'date_present_appointment' => 'Present appt.', 'salary_structure_id' => 'Salary structure', 'salary_step_id' => 'Salary level/step',
        'contacts' => 'Contacts',
    ];

    private XlsxReader $book;
    private OrganisationMatcher $org;
    private array $lk = [];
    private bool $commit = false;
    private bool $updateExisting = true;

    public function __construct(private string $path) {}

    public function preview(): array
    {
        $this->commit = false;
        return $this->run();
    }

    /** @param int|null $by user id of the Registry officer */
    public function apply(?int $by = null, bool $updateExisting = true): array
    {
        $this->commit = true;
        $this->updateExisting = $updateExisting;
        @set_time_limit(900);
        return DB::transaction(fn () => $this->run($by));
    }

    private function run(?int $by = null): array
    {
        $this->book = new XlsxReader($this->path);
        $this->org  = new OrganisationMatcher();
        $this->loadLookups();

        $serving = $this->servingRows();
        $out = ['new' => [], 'update' => [], 'same' => 0, 'problems' => [], 'warnings' => [], 'added_lookups' => []];
        $seen = [];

        $existing = Staff::with(['employment', 'contacts', 'user'])->get()->keyBy(fn ($s) => strtoupper($s->staff_no));

        foreach ($serving as $i => $row) {
            $line = $row['_line'];
            $no   = strtoupper(trim((string) $row['staff_no']));
            $name = trim((string) $row['names']);

            if (isset($seen[$no])) {
                $out['problems'][] = ['line' => $line, 'staff_no' => $no, 'name' => $name, 'issues' => ["Listed twice (also on row {$seen[$no]}) — second entry ignored"]];
                continue;
            }
            $seen[$no] = $line;

            [$data, $issues, $warnings] = $this->normalise($row);
            if ($issues) {
                $out['problems'][] = ['line' => $line, 'staff_no' => $no, 'name' => $name, 'issues' => $issues];
                continue;
            }
            foreach ($warnings as $w) $out['warnings'][$w][] = $no;

            $staff = $existing->get($no);
            if (!$staff) {
                $out['new'][] = ['staff_no' => $no, 'name' => $name, 'dept' => trim((string) $row['dept']), 'college' => trim((string) $row['college']),
                                 'has_email' => (bool) $data['emails']];
                if ($this->commit) $this->markDictsLoginSent($this->create($no, $data, $by), $data);
                continue;
            }

            $changes = $this->diff($staff, $data);
            if ($changes) {
                $out['update'][] = ['staff_no' => $no, 'name' => $name, 'changes' => $changes];
                if ($this->commit && $this->updateExisting) $this->update($staff, $data, $by);
            } else {
                $out['same']++;
            }
            if ($this->commit) $this->markDictsLoginSent($staff, $data);
        }

        $out['disengaged'] = $this->disengagements(array_keys($seen), $by);
        $out['added_lookups'] = $this->lk['_added'];
        $out['counts'] = [
            'rows'       => count($serving),
            'new'        => count($out['new']),
            'update'     => count($out['update']),
            'same'       => $out['same'],
            'problems'   => count($out['problems']),
            'disengaged' => count(array_filter($out['disengaged'], fn ($d) => $d['action'] === 'apply')),
            'no_email'   => count(array_filter($out['new'], fn ($n) => !$n['has_email'])),
        ];
        return $out;
    }

    // ── Reading ─────────────────────────────────────────────────────────

    private function servingRows(): array
    {
        $names = $this->book->sheetNames();
        $sheet = collect($names)->first(fn ($n) => stripos($n, 'NOMINAL') !== false) ?? $names[0];
        $rows  = $this->book->rows($sheet);

        $headerAt = null;
        foreach (array_slice($rows, 0, 15, true) as $i => $r) {
            $joined = strtoupper(implode('|', array_map('strval', $r)));
            if (str_contains($joined, 'STAFF') && str_contains($joined, 'NAME')) { $headerAt = $i; break; }
        }
        if ($headerAt === null) throw new RuntimeException("Could not find the header row (STAFF ID NO., NAMES OF STAFF …) on the '{$sheet}' sheet.");

        $map = $this->mapColumns($rows[$headerAt]);
        foreach (['staff_no', 'names'] as $need) {
            if (!isset($map[$need])) throw new RuntimeException("The '{$sheet}' sheet has no " . self::COLUMNS[$need][0] . ' column.');
        }

        $out = [];
        foreach (array_slice($rows, $headerAt + 1, null, true) as $i => $r) {
            $rec = ['_line' => $i + 1];
            foreach ($map as $key => $col) $rec[$key] = $r[$col] ?? null;
            foreach (array_keys(self::COLUMNS) as $key) $rec[$key] ??= null;
            if (trim((string) $rec['staff_no']) === '' && trim((string) $rec['names']) === '') continue;
            if (trim((string) $rec['staff_no']) === '') {
                $rec['staff_no'] = '';
            }
            $out[] = $rec;
        }
        return $out;
    }

    private function mapColumns(array $header): array
    {
        $map = [];
        $h = array_map(fn ($v) => strtoupper(preg_replace('/\s+/', ' ', trim((string) $v))), $header);
        // Most specific first, so "SALARY LEVEL/STEP" is not taken as "SALARY LEVEL".
        $keys = array_keys(self::COLUMNS);
        usort($keys, fn ($a, $b) => max(array_map('strlen', self::COLUMNS[$b])) <=> max(array_map('strlen', self::COLUMNS[$a])));
        foreach ($keys as $key) {
            foreach ($h as $col => $text) {
                if (in_array($col, $map, true) || $text === '') continue;
                foreach (self::COLUMNS[$key] as $needle) {
                    if (str_contains($text, $needle)) { $map[$key] = $col; continue 3; }
                }
            }
        }
        return $map;
    }

    // ── Normalising one row ─────────────────────────────────────────────

    private function normalise(array $r): array
    {
        $issues = $warnings = [];
        $no = strtoupper(trim((string) $r['staff_no']));
        if ($no === '') $issues[] = 'No staff number';
        elseif (strlen($no) > 10) $issues[] = "Staff number '{$no}' is longer than 10 characters";

        [$surname, $firstname, $othernames] = self::splitName((string) $r['names']);
        if (!$surname || !$firstname) $issues[] = 'Name is not in "Surname, Firstname Othernames" form';

        $dob = XlsxReader::toDate($r['dob']);
        if (!$dob) $issues[] = 'Date of birth missing or not a date';

        $gender = $this->lookup('gender', match (strtoupper(trim((string) $r['gender']))) { 'M', 'MALE' => 'male', 'F', 'FEMALE' => 'female', default => '' });
        if (!$gender) $issues[] = 'Gender missing (M or F)';

        $title = $this->title((string) $r['title']);
        if (!$title) $issues[] = 'Title missing';

        $data = ['staff' => [], 'employment' => [], 'emails' => [], 'phones' => []];
        if ($issues) return [$data, $issues, $warnings];

        $stateId = $this->lookup('state', $r['state']);
        $lgaId   = $stateId ? $this->lga($stateId, (string) $r['lga']) : null;
        if (trim((string) $r['state']) !== '' && !$stateId) $warnings[] = 'State not recognised';
        elseif (trim((string) $r['lga']) !== '' && $stateId && !$lgaId) $warnings[] = 'LGA not recognised for the state';

        $marital = $this->lookup('marital', $r['marital']);
        if (!$marital) $warnings[] = 'Marital status missing';

        $zone = trim((string) $r['zone']);
        $data['staff'] = [
            'staff_title_id' => $title, 'surname' => $surname, 'firstname' => $firstname, 'othernames' => $othernames,
            'dob' => $dob, 'gender_id' => $gender, 'state_id' => $stateId, 'lga_id' => $lgaId,
            'senatorial_district_id' => $this->lookup('senatorial', $r['senatorial']),
            'geo_zone_id' => $this->lookup('zone', $zone), 'marital_status_id' => $marital,
            'nationality' => stripos($zone, 'NON') === 0 ? 'Non-Nigerian' : 'Nigerian',
        ];

        // Division from College/Institute, department from Dept./Unit of deployment.
        $deptId = $this->org->department($r['dept']);
        $divId  = $this->org->division($r['college']) ?? ($deptId ? $this->org->divisionOfDepartment($deptId) : null);
        if (!$deptId) $warnings[] = 'Department not matched';
        if (!$divId)  $warnings[] = 'College / Division not matched';

        [$structureId, $stepId, $level] = $this->salary($r);
        if ($structureId && !$stepId && !$this->isConsolidated($r)) $warnings[] = 'Salary level/step not matched';

        $data['employment'] = [
            'staff_category_id' => $this->category((string) $r['category'], $structureId, $level),
            'ippis_no' => self::blank($r['ippis']),
            'division_id' => $divId, 'department_id' => $deptId,
            'highest_degree' => $this->degree((string) $r['degree']),
            'date_public_first_appointment' => XlsxReader::toDate($r['first_public']),
            'date_funaab_first_appointment' => XlsxReader::toDate($r['first_funaab']),
            'date_confirmation' => XlsxReader::toDate($r['confirmation']),
            'designation_title' => self::blank($r['designation']),
            'date_present_appointment' => XlsxReader::toDate($r['present_appt']),
            'salary_structure_id' => $structureId, 'salary_step_id' => $stepId,
        ];
        if (!$data['employment']['staff_category_id']) return [$data, ['Staff category not recognised (STAFF STATUS column)'], $warnings];

        $data['emails'] = self::emails((string) $r['email']);
        $data['phones'] = self::phones((string) $r['phone']);
        if (!$data['emails']) $warnings[] = 'No valid e-mail address (cannot be sent a login mail)';

        return [$data, [], $warnings];
    }

    public static function splitName(string $names): array
    {
        $names = trim(preg_replace('/\s+/', ' ', $names));
        if ($names === '') return [null, null, null];
        if (str_contains($names, ',')) {
            [$sur, $rest] = array_map('trim', explode(',', $names, 2));
        } else {
            $parts = explode(' ', $names, 2);
            [$sur, $rest] = [$parts[0], $parts[1] ?? ''];
        }
        $rest  = explode(' ', trim($rest), 2);
        return [self::tidy($sur) ?: null, self::tidy($rest[0] ?? '') ?: null, self::tidy($rest[1] ?? '') ?: null];
    }

    private static function tidy(string $s): string
    {
        $s = trim($s, " .,");
        return $s === mb_strtoupper($s) && mb_strlen($s) > 3 ? mb_convert_case(mb_strtolower($s), MB_CASE_TITLE) : $s;
    }

    public static function emails(string $raw): array
    {
        $out = [];
        foreach (preg_split('/[\s,;\/]+/', strtolower($raw)) as $e) {
            $e = trim($e, " .<>");
            if ($e !== '' && filter_var($e, FILTER_VALIDATE_EMAIL) && strlen($e) <= 255) $out[] = $e;
        }
        return array_values(array_unique($out));
    }

    public static function phones(string $raw): array
    {
        $out = [];
        foreach (preg_split('/[,;\/]+|\s{2,}/', $raw) as $p) {
            $digits = preg_replace('/\D/', '', $p);
            if (strlen($digits) === 10 && in_array($digits[0], ['7', '8', '9'], true)) $digits = '0' . $digits;
            if (str_starts_with($digits, '234') && strlen($digits) === 13) $digits = '0' . substr($digits, 3);
            if (strlen($digits) >= 7) $out[] = $digits;
        }
        return array_values(array_unique($out));
    }

    // ── Lookups ─────────────────────────────────────────────────────────

    private function loadLookups(): void
    {
        $idx = fn ($rows, $col) => collect($rows)->mapWithKeys(fn ($r) => [self::key($r->$col) => $r->id])->all();
        $this->lk = [
            'gender'     => $idx(DB::table('genders')->get(), 'gender_name'),
            'state'      => $idx(DB::table('states')->get(), 'state_name'),
            'marital'    => $idx(DB::table('marital_statuses')->get(), 'status'),
            'zone'       => $idx(DB::table('geo_pol_zones')->get(), 'zone'),
            'senatorial' => $idx(DB::table('senatorial_districts')->orderBy('id')->get()->unique('senatorial_district_name'), 'senatorial_district_name'),
            'title'      => $idx(DB::table('staff_titles')->get(), 'title'),
            'structure'  => $idx(DB::table('salary_structures')->get(), 'salary_structure_name'),
            'category'   => $idx(DB::table('staff_categories')->get(), 'category'),
            'degree'     => [],
            'lga'        => [], 'level' => [], 'step' => [], '_added' => [],
        ];
        foreach (DB::table('lgas')->get() as $l) $this->lk['lga'][$l->state_id][self::key($l->lga_name)] = $l->id;
        foreach (DB::table('salary_levels')->get() as $l) $this->lk['level'][$l->salary_structure_id][(int) $l->level] = $l->id;
        foreach (DB::table('salary_steps')->get() as $s) $this->lk['step'][$s->salary_level_id][(int) $s->step] = $s->id;
        foreach (DB::table('degrees')->get() as $d) {
            $this->lk['degree'][self::key($d->degree_name)] = $d->id;
            if (preg_match('/\(([^)]+)\)/', $d->degree_name, $m)) $this->lk['degree'][self::key($m[1])] ??= $d->id;
        }
        $this->lk['senatorial'] = array_filter($this->lk['senatorial']);
    }

    /** Common roll spellings => the LGA name used in the system (both as keys). */
    private const LGA_ALIASES = [
        'sagamu' => 'shagamu', 'ogbomosonorth' => 'ogbomoshonorth', 'ogbomososouth' => 'ogbomoshosouth', 'ogbomonorth' => 'ogbomoshonorth',
        'atigbo' => 'atisbo', 'kabbabunnu' => 'kabbabunu', 'ayedire' => 'aiyedire', 'oorelope' => 'orelope', 'okenne' => 'okene',
        'ileshawest' => 'ilesawest', 'ileshaeast' => 'ilesaeast', 'atakumosawest' => 'atakunmosawest', 'atakumosaeast' => 'atakunmosaeast',
        'ogorimangongo' => 'ogorimagongo', 'zangokataf' => 'zangonkataf', 'adoodo' => 'adoodoota', 'obafemiowede' => 'obafemiowode',
        'vandeikaya' => 'vandeikya', 'afijo' => 'afijio', 'ibaparaeast' => 'ibarapaeast', 'tambuwa' => 'tambuwal', 'ileoluji' => 'ileolujiokeigbo',
        'urwie' => 'uvwie', 'ezinihittembaise' => 'ezinihitte', 'bassia' => 'bassa', 'ilajeeseodo' => 'ilaje', 'odeirele' => 'irele',
    ];

    private function lga(int $stateId, string $raw): ?int
    {
        $k = self::key($raw);
        if ($k === '') return null;
        $list = $this->lk['lga'][$stateId] ?? [];
        if (isset($list[$k])) return $list[$k];
        if (isset(self::LGA_ALIASES[$k], $list[self::LGA_ALIASES[$k]])) return $list[self::LGA_ALIASES[$k]];
        // A unique prefix match, e.g. "Ado-Odo" => "Ado-Odo/Ota".
        $hits = array_filter($list, fn ($id, $name) => strlen($k) >= 4 && (str_starts_with($name, $k) || str_starts_with($k, $name)), ARRAY_FILTER_USE_BOTH);
        return count($hits) === 1 ? reset($hits) : null;
    }

    private function lookup(string $type, mixed $value): ?int
    {
        $k = self::key($value);
        return $k === '' ? null : ($this->lk[$type][$k] ?? null);
    }

    /** Titles and degrees not yet in the system are added (on apply). */
    private function title(string $raw): int|string|null
    {
        $raw = trim(preg_replace('/\s+/', ' ', $raw));
        if ($raw === '') return null;
        return $this->lookupOrAdd('title', 'staff_titles', 'title', $raw, fn () => ['title' => $raw]);
    }

    private function degree(string $raw): int|string|null
    {
        $raw = trim($raw);
        if ($raw === '') return null;
        $name = match (self::key($raw)) {
            'phd' => 'Ph.D', 'bachelorsdegree' => "Bachelor's Degree", 'mastersdegree' => "Master's Degree",
            'pgd' => 'Postgraduate Diploma (PGD)', 'pgde' => 'Postgraduate Diploma in Education (PGDE)', default => $raw,
        };
        if ($id = $this->lk['degree'][self::key($name)] ?? $this->lk['degree'][self::key($raw)] ?? null) return $id;
        return $this->lookupOrAdd('degree', 'degrees', 'degree_name', $name, fn () => ['degree_name' => $name, 'remark' => 'Added from nominal roll']);
    }

    private function lookupOrAdd(string $type, string $table, string $col, string $value, callable $row): int|string
    {
        $k = self::key($value);
        if (isset($this->lk[$type][$k])) return $this->lk[$type][$k];
        if (!$this->commit) {
            $this->lk['_added'][$type][$k] = $value;
            return $this->lk[$type][$k] = "new:{$value}";
        }
        $values = $row() + ['created_at' => now(), 'updated_at' => now()];
        if (\Schema::hasColumn($table, 'uuid')) $values['uuid'] = (string) Str::uuid();
        $this->lk['_added'][$type][$k] = $value;
        return $this->lk[$type][$k] = DB::table($table)->insertGetId($values);
    }

    private function salary(array $r): array
    {
        $structureName = trim((string) $r['structure']);
        $levelStep     = trim((string) $r['level_step']);
        $level = $step = null;
        if (preg_match('/^(CON[A-Z]+)\s*0*(\d+)\s*(?:\/|STEP|STP)\s*0*(\d+)/i', $levelStep, $m)) {
            $structureName = $structureName ?: $m[1];
            [$level, $step] = [(int) $m[2], (int) $m[3]];
        } elseif (is_numeric($r['level'])) {
            $level = (int) $r['level'];
        }
        $structureId = $this->lookup('structure', $structureName);
        $levelId = $structureId && $level ? ($this->lk['level'][$structureId][$level] ?? null) : null;
        $stepId  = $levelId && $step ? ($this->lk['step'][$levelId][$step] ?? null) : null;
        return [$structureId, $stepId, $level];
    }

    private function isConsolidated(array $r): bool
    {
        return stripos((string) $r['level_step'], 'CONSOLIDATED') !== false || stripos((string) $r['level'], 'CONSOLIDATED') !== false;
    }

    private function category(string $raw, ?int $structureId, ?int $level): ?int
    {
        $raw = strtoupper($raw);
        if (str_contains($raw, 'ACADEMIC')) return $this->lk['category'][self::key('Academic Staff')] ?? null;
        $junior = $level !== null && $level <= (int) config('aper.junior_max_contiss_level', 5);
        return $this->lk['category'][self::key($junior ? 'Junior Non-Teaching Staff' : 'Senior Non-Teaching Staff')]
            ?? $this->lk['category'][self::key('Non-Teaching Staff')] ?? null;
    }

    // ── Comparing and writing ───────────────────────────────────────────

    private function diff(Staff $staff, array $data): array
    {
        $changes = [];
        foreach ($data['staff'] as $f => $new) {
            if ($f === 'nationality' || $new === null) continue;
            $old = $f === 'dob' ? $staff->dob?->format('Y-m-d') : $staff->$f;
            if (!$this->differs($f, $old, $new)) continue;
            $changes[$f] = [$this->label($f, $old), $this->label($f, $new)];
        }
        $emp = $staff->employment;
        foreach ($data['employment'] as $f => $new) {
            if ($new === null) continue;
            $old = $emp?->$f;
            if ($old instanceof \DateTimeInterface) $old = $old->format('Y-m-d');
            if ($f === 'highest_degree' && $old) continue;      // keep the more specific degree Registry recorded
            if (!$this->differs($f, $old, $new)) continue;
            $changes[$f] = [$this->label($f, $old), $this->label($f, $new)];
        }
        $have = $staff->contacts->pluck('contact_value')->map(fn ($v) => strtolower(preg_replace('/\s+/', '', $v)))->all();
        $add  = array_values(array_filter([...$data['emails'], ...$data['phones']], fn ($v) => !in_array(strtolower($v), $have, true)));
        if ($add) $changes['contacts'] = ['', 'add ' . implode(', ', $add)];
        return $changes;
    }

    private function differs(string $field, mixed $old, mixed $new): bool
    {
        if ((string) $old === (string) $new) return false;
        if (in_array($field, ['surname', 'firstname', 'othernames', 'designation_title'], true)) {
            return mb_strtolower(trim((string) $old)) !== mb_strtolower(trim((string) $new));
        }
        if ($field === 'senatorial_district_id' && $old) {      // same district name, another LGA's row
            return $this->label($field, $old) !== $this->label($field, $new);
        }
        return true;
    }

    /** Only the fields that differ are written (names that differ only in capitals are kept). */
    private function changedOnly(Staff $staff, array $values, ?Employment $emp = null): array
    {
        $out = [];
        foreach ($values as $f => $v) {
            if ($v === null) continue;
            $old = $emp ? $emp->$f : ($f === 'dob' ? $staff->dob?->format('Y-m-d') : $staff->$f);
            if ($old instanceof \DateTimeInterface) $old = $old->format('Y-m-d');
            if ($f === 'highest_degree' && $old) continue;
            if ($this->differs($f, $old, $v)) $out[$f] = $v;
        }
        return $out;
    }

    private function create(string $no, array $data, ?int $by): Staff
    {
        $active = DB::table('staff_statuses')->where('status', 'Active')->value('id');
        $staff = Staff::create(['staff_no' => $no, 'staff_status_id' => $active, 'created_by' => $by] + $data['staff']);
        Employment::create(['staff_id' => $staff->id, 'created_by' => $by,
            'employment_type_id' => DB::table('employment_types')->where('employment_type', 'Full time Employment')->value('id'),
        ] + array_filter($data['employment'], fn ($v) => $v !== null));
        $this->addContacts($staff, $data);
        return $staff;
    }

    private function update(Staff $staff, array $data, ?int $by): void
    {
        $values = $this->changedOnly($staff, array_diff_key($data['staff'], ['nationality' => 1]));
        if ($values) $staff->update($values + ['updated_by' => $by]);
        $emp = array_filter($data['employment'], fn ($v) => $v !== null);
        if ($staff->employment) {
            $values = $this->changedOnly($staff, $emp, $staff->employment);
            if ($values) $staff->employment->update($values + ['updated_by' => $by]);
        } elseif (!empty($emp['staff_category_id'])) {
            Employment::create($emp + ['staff_id' => $staff->id, 'created_by' => $by]);
        }
        $this->addContacts($staff, $data);
    }

    private function addContacts(Staff $staff, array $data): void
    {
        $types = ['emails' => DB::table('contact_types')->where('contact_type_name', 'Email Address')->value('id'),
                  'phones' => DB::table('contact_types')->where('contact_type_name', 'Phone Number')->value('id')];
        $have = $staff->contacts()->pluck('contact_value')->map(fn ($v) => strtolower(preg_replace('/\s+/', '', $v)))->all();
        foreach ($types as $key => $typeId) {
            if (!$typeId) continue;
            foreach ($data[$key] as $value) {
                if (in_array(strtolower($value), $have, true)) continue;
                Contact::create(['staff_id' => $staff->id, 'contact_type_id' => $typeId, 'contact_value' => $value]);
                $have[] = strtolower($value);
            }
        }
    }

    /** DICTS staff were onboarded one by one; record that their login was already sent. */
    private function markDictsLoginSent(Staff $staff, array $data): void
    {
        $user = $staff->user;
        $div  = $data['employment']['division_id'] ?? null;
        if (!$user || $user->login_mail_sent_at || !$div || strtoupper($this->org->divisionShort[$div] ?? '') !== 'DICTS') return;
        $user->forceFill(['login_mail_sent_at' => $user->created_at ?? now(), 'login_mail_count' => max(1, (int) $user->login_mail_count)])->saveQuietly();
        DB::table('login_mail_logs')->insert(['staff_id' => $staff->id, 'user_id' => $user->id, 'email' => $user->email, 'status' => 'recorded',
            'note' => 'Onboarded (DICTS) before login mail tracking', 'created_at' => now(), 'updated_at' => now()]);
    }

    // ── Disengagement sheets ────────────────────────────────────────────

    private function disengagements(array $servingNos, ?int $by): array
    {
        $statuses = DB::table('staff_statuses')->pluck('id', 'status');
        $out = [];
        $done = [];
        foreach ($this->book->sheetNames() as $sheet) {
            if (stripos($sheet, 'DISENGAGEMENT') === false) continue;
            $rows = $this->book->rows($sheet);
            $h = null;
            foreach (array_slice($rows, 0, 10, true) as $i => $r) {
                if (str_contains(strtoupper(implode('|', array_map('strval', $r))), 'MODE OF DISENGAGEMENT')) { $h = $i; break; }
            }
            if ($h === null) continue;
            $cols = [];
            foreach ($rows[$h] as $c => $text) {
                $t = strtoupper((string) $text);
                if (!isset($cols['no']) && str_contains($t, 'STAFF') && !str_contains($t, 'NAME') && !str_contains($t, 'STATUS')) $cols['no'] = $c;
                if (!isset($cols['name']) && str_contains($t, 'NAME')) $cols['name'] = $c;
                if (!isset($cols['date']) && str_contains($t, 'DATE OF DISENGAGEMENT')) $cols['date'] = $c;
                if (!isset($cols['mode']) && str_contains($t, 'MODE OF DISENGAGEMENT')) $cols['mode'] = $c;
            }
            if (!isset($cols['no'], $cols['mode'])) continue;

            foreach (array_slice($rows, $h + 1) as $r) {
                $no = strtoupper(trim((string) ($r[$cols['no']] ?? '')));
                if ($no === '' || !preg_match('/^[A-Z]{1,4}\d+/', $no) || isset($done[$no])) continue;
                $done[$no] = true;
                $mode   = trim((string) ($r[$cols['mode']] ?? ''));
                $date   = XlsxReader::toDate($r[$cols['date'] ?? -1] ?? null);
                $status = self::statusForMode($mode);
                $entry  = ['sheet' => $sheet, 'staff_no' => $no, 'name' => trim((string) ($r[$cols['name'] ?? -1] ?? '')), 'mode' => $mode, 'date' => $date, 'status' => $status];

                $staff = Staff::where('staff_no', $no)->first();
                $onRoll = in_array($no, $servingNos, true);
                $entry['action'] = match (true) {
                    !$staff && !$onRoll              => 'not_in_system',
                    !$status                         => 'skip',
                    $onRoll                          => 'conflict',
                    !$date                           => 'no_date',
                    (int) $staff->staff_status_id === (int) ($statuses[$status] ?? 0) => 'same',
                    default                          => 'apply',
                };
                if ($entry['action'] === 'apply' && $this->commit) {
                    app(StaffRecordService::class)->addOffEmployment($staff, [
                        'staff_status_id' => $statuses[$status], 'effective_date' => $date,
                        'reason' => "{$mode} (from nominal roll, {$sheet})",
                    ]);
                }
                if ($entry['action'] !== 'not_in_system') $out[] = $entry;
            }
        }
        return $out;
    }

    public static function statusForMode(string $mode): ?string
    {
        $m = strtolower($mode);
        return match (true) {
            str_contains($m, 'deceas') || str_contains($m, 'death') || str_contains($m, 'died') => 'Deceased',
            str_contains($m, 'retire')                                                         => 'Retired',
            str_contains($m, 'terminat') || str_contains($m, 'dismiss')                        => 'Terminated',
            str_contains($m, 'withdraw') || str_contains($m, 'resign') || str_contains($m, 'transfer')
                || str_contains($m, 'abscond') || str_contains($m, 'abandon') || str_contains($m, 'disengag') => 'Withdrawn',
            default => null,   // sabbatical, contract, leave … — status unchanged
        };
    }

    // ── Helpers ─────────────────────────────────────────────────────────

    private function label(string $field, mixed $value): string
    {
        if ($value === null || $value === '') return '—';
        if (is_string($value) && str_starts_with($value, 'new:')) return substr($value, 4) . ' (new)';
        static $names = [];
        $table = match ($field) {
            'staff_title_id' => ['staff_titles', 'title'], 'gender_id' => ['genders', 'gender_name'], 'state_id' => ['states', 'state_name'],
            'lga_id' => ['lgas', 'lga_name'], 'senatorial_district_id' => ['senatorial_districts', 'senatorial_district_name'],
            'geo_zone_id' => ['geo_pol_zones', 'zone'], 'marital_status_id' => ['marital_statuses', 'status'],
            'staff_category_id' => ['staff_categories', 'category'], 'division_id' => ['divisions', 'division_name'],
            'department_id' => ['departments', 'department_name'], 'highest_degree' => ['degrees', 'degree_name'],
            'salary_structure_id' => ['salary_structures', 'salary_structure_name'], default => null,
        };
        if ($field === 'salary_step_id') {
            $s = DB::table('salary_steps')->join('salary_levels', 'salary_levels.id', '=', 'salary_steps.salary_level_id')
                ->where('salary_steps.id', $value)->first(['salary_levels.level', 'salary_steps.step']);
            return $s ? "Level {$s->level} Step {$s->step}" : (string) $value;
        }
        if (!$table) return (string) $value;
        return $names["{$field}.{$value}"] ??= (string) (DB::table($table[0])->where('id', $value)->value($table[1]) ?? $value);
    }

    private static function key(mixed $v): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower(trim((string) $v)));
    }

    private static function blank(mixed $v): ?string
    {
        $v = trim((string) $v);
        return $v === '' ? null : $v;
    }
}
