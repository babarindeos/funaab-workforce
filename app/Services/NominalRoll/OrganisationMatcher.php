<?php

namespace App\Services\NominalRoll;

use Illuminate\Support\Facades\DB;

/**
 * Matches the College/Institute and Dept./Unit spellings used in the nominal
 * roll to divisions and departments, using the roll spellings kept in
 * database/seeders/data/organisation.json plus whatever Registry has added.
 */
class OrganisationMatcher
{
    /** normalised spelling => department id */
    private array $dept = [];
    /** normalised spelling => division id */
    private array $div = [];
    /** department id => division id */
    private array $deptDivision = [];
    /** division id => short name */
    public array $divisionShort = [];

    public function __construct()
    {
        $deptByShort = $deptByName = $divByShort = $divByName = [];
        foreach (DB::table('departments')->get(['id', 'division_id', 'short_name', 'department_name']) as $d) {
            $deptByShort[self::norm($d->short_name)] = $d->id;
            $deptByName[self::norm($d->department_name)] = $d->id;
            $this->deptDivision[$d->id] = $d->division_id;
        }
        foreach (DB::table('divisions')->get(['id', 'short_name', 'division_name']) as $v) {
            $divByShort[self::norm($v->short_name)] = $v->id;
            $divByName[self::norm($v->division_name)] = $v->id;
            $this->divisionShort[$v->id] = $v->short_name;
        }

        // What Registry has in the database first …
        $this->dept = $deptByName + $deptByShort;
        $this->div  = $divByName + $divByShort;

        // … then the nominal-roll spellings for the records seeded from the roll.
        $file = database_path('seeders/data/organisation.json');
        $org  = is_file($file) ? json_decode(file_get_contents($file), true) : ['divisions' => []];
        foreach ($org['divisions'] as $division) {
            $divId = $divByShort[self::norm($division['short'])] ?? $divByName[self::norm($division['name'])] ?? null;
            if ($divId) {
                foreach ([$division['short'], $division['name'], ...$division['aliases']] as $a) {
                    $this->div[self::norm($a)] ??= $divId;
                }
            }
            foreach ($division['departments'] as $d) {
                $id = $deptByShort[self::norm($d['short'])] ?? $deptByName[self::norm($d['name'])] ?? null;
                if (!$id) continue;
                foreach ([$d['short'], $d['name'], ...$d['aliases']] as $a) {
                    $this->dept[self::norm($a)] ??= $id;
                }
            }
        }
    }

    public function division(?string $raw): ?int
    {
        return $raw === null || trim($raw) === '' ? null : ($this->div[self::norm($raw)] ?? null);
    }

    public function department(?string $raw): ?int
    {
        if ($raw === null || trim($raw) === '') return null;
        $k = self::norm($raw);
        if (isset($this->dept[$k])) return $this->dept[$k];

        if (preg_match("/^dean'?s(?: office)?,? *(col[a-z]+)\)?$/", $k, $m)) {
            return $this->dept[self::norm(strtoupper($m[1]) . '-DO')] ?? $this->dept[self::norm("dean's office, {$m[1]}")] ?? null;
        }
        if (preg_match('/^(.+?) \((col[a-z]+|bursary|inhurd)\)$/', $k, $m)) {
            return $this->dept[self::norm($m[1])] ?? null;
        }
        return null;
    }

    public function divisionOfDepartment(int $deptId): ?int
    {
        return $this->deptDivision[$deptId] ?? null;
    }

    public static function norm(?string $s): string
    {
        $s = mb_strtolower(trim((string) $s));
        $s = str_replace(['’', '‘'], "'", $s);
        $s = preg_replace('/\s+/', ' ', $s);
        if (substr_count($s, ')') > substr_count($s, '(')) $s = trim(rtrim($s, ')'));
        return $s;
    }
}
