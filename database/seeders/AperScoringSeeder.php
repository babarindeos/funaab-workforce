<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * APER Part C scoring guides and minimum promotion requirements, exactly
 * as printed on the official FUNAAB APER forms (Senior Non-Teaching and
 * Junior CONTISS 01-05).
 *
 * source:
 *   manual                – typed in by the Departmental Assessment Panel
 *   hod_total             – sum of the HOD's 1–5 grades (Senior 9 × 5 = 45, Junior 6 × 5 = 30)
 *   hod_organisational_x2 – HOD's "Organisational Ability" grade × 2 (out of 10)
 *
 * Minimum requirement is keyed by the staff member's CURRENT level
 * ("CONTISS 08 – 45" = minimum score to be promoted FROM level 08).
 */
class AperScoringSeeder extends Seeder
{
    private array $junior = [
        ['code' => 'qualifications',     'metric' => 'Qualifications',                    'max' => 10, 'source' => 'manual'],
        ['code' => 'conduct',            'metric' => 'Conduct',                           'max' => 10, 'source' => 'manual'],
        ['code' => 'initiative',         'metric' => 'Initiative',                        'max' => 15, 'source' => 'manual'],
        ['code' => 'punctuality',        'metric' => 'Punctuality & Regularity at Work',  'max' => 10, 'source' => 'manual'],
        ['code' => 'self_improvement',   'metric' => 'Self-improvement effort',           'max' => 5,  'source' => 'manual'],
        ['code' => 'human_relations',    'metric' => 'Human Relations',                   'max' => 5,  'source' => 'manual'],
        ['code' => 'reliability',        'metric' => 'Degree of Reliability',             'max' => 10, 'source' => 'hod_organisational_x2'],
        ['code' => 'commendation',       'metric' => 'Commendation',                      'max' => 5,  'source' => 'manual'],
        ['code' => 'hod_assessment',     'metric' => "HOD's Assessment",                  'max' => 30, 'source' => 'hod_total'],
    ];

    private array $senior = [
        ['code' => 'academic_qualifications',     'metric' => 'Basic Academic Qualifications',                  'max' => 10, 'source' => 'manual'],
        ['code' => 'professional_qualifications', 'metric' => 'Professional Qualifications (where applicable)', 'max' => 5,  'source' => 'manual'],
        ['code' => 'cognate_experience',          'metric' => 'Years of Cognate Experience',                    'max' => 15, 'source' => 'manual'],
        ['code' => 'hod_assessment',              'metric' => "Head of Department's Assessment",                'max' => 45, 'source' => 'hod_total'],
        ['code' => 'other_contributions',         'metric' => 'Other Contributions (to University or Country)', 'max' => 10, 'source' => 'manual'],
        ['code' => 'managerial_ability',          'metric' => 'Managerial Ability',                             'max' => 10, 'source' => 'hod_organisational_x2'],
        ['code' => 'commendations',               'metric' => 'Commendations',                                  'max' => 5,  'source' => 'manual'],
    ];

    private array $juniorMin = ['01' => 45, '02' => 50, '03' => 55, '04' => 60, '05' => 65];

    private array $seniorMin = ['07' => 40, '08' => 45, '09' => 50, '10' => 52, '11' => 55,
                                '12' => 57, '13' => 60, '14' => 65, '15' => 70];

    public function run(): void
    {
        $this->syncGuides('aper_form_junior_scoring_guides', $this->junior);
        $this->syncGuides('aper_form_senior_scoring_guides', $this->senior);
        $this->syncMinimums('aper_form_junior_promotion_min_reqs', $this->juniorMin);
        $this->syncMinimums('aper_form_senior_promotion_min_reqs', $this->seniorMin);
    }

    private function syncGuides(string $table, array $defs): void
    {
        $existing = DB::table($table)->orderBy('id')->get();

        foreach ($defs as $i => $def) {
            $row = $existing->firstWhere('code', $def['code'])
                ?? $existing->first(fn ($r) => empty($r->code) && $this->sameMetric($r->metric, $def['metric']));

            $values = [
                'code'       => $def['code'],
                'metric'     => $def['metric'],
                'max_score'  => $def['max'],
                'source'     => $def['source'],
                'sort_order' => $i + 1,
                'updated_at' => now(),
            ];

            if ($row) {
                DB::table($table)->where('id', $row->id)->update($values);
                $existing = $existing->reject(fn ($r) => $r->id === $row->id);
            } else {
                DB::table($table)->insert($values + ['uuid' => (string) Str::uuid(), 'created_at' => now()]);
            }
        }

        // Rows that are not on the official form are left in place (they may
        // already hold scores) but pushed to the end so they are easy to spot.
        foreach ($existing as $extra) {
            if (empty($extra->code)) {
                DB::table($table)->where('id', $extra->id)->update(['sort_order' => 999]);
            }
        }
    }

    private function syncMinimums(string $table, array $mins): void
    {
        $structureId = DB::table('salary_structures')->where('salary_structure_name', 'CONTISS')->value('id');
        if (!$structureId) {
            $this->command?->warn("CONTISS salary structure missing – run SalaryScaleSeeder first.");
            return;
        }

        $keepIds = [];
        foreach ($mins as $level => $pct) {
            $levelId = DB::table('salary_levels')->where('salary_structure_id', $structureId)
                ->where(fn ($q) => $q->where('level', $level)->orWhere('level', (string) (int) $level))
                ->value('id');
            if (!$levelId) {
                $this->command?->line("  - CONTISS {$level} does not exist; minimum ({$pct}) skipped.");
                continue;
            }
            $id = DB::table($table)->where('salary_level_id', $levelId)->value('id');
            if ($id) {
                DB::table($table)->where('id', $id)->update(['percentage' => $pct, 'updated_at' => now()]);
            } else {
                $id = DB::table($table)->insertGetId([
                    'uuid' => (string) Str::uuid(), 'salary_level_id' => $levelId,
                    'percentage' => $pct, 'created_at' => now(), 'updated_at' => now(),
                ]);
            }
            $keepIds[] = $id;
        }

        // The table must mirror the official form exactly.
        DB::table($table)->whereNotIn('id', $keepIds)->delete();
    }

    private function sameMetric(?string $a, string $b): bool
    {
        $norm = fn ($s) => strtolower(preg_replace('/[^a-z]/i', '', explode('(', (string) $s)[0]));
        $a = $norm($a);
        $b = $norm($b);
        return $a !== '' && (str_starts_with($a, substr($b, 0, 8)) || str_starts_with($b, substr($a, 0, 8)));
    }
}
