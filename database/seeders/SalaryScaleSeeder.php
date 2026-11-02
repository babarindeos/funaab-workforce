<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Salary structures, levels and steps (CONTISS, CONUASS, CONHESS, CONMESS).
 * Source: database/seeders/data/salary_scales.json. Only ADDS what is
 * missing; existing rows are never changed or removed.
 */
class SalaryScaleSeeder extends Seeder
{
    public function run(): void
    {
        $scales = json_decode(file_get_contents(__DIR__ . '/data/salary_scales.json'), true)['scales'];

        foreach ($scales as $structureName => $levels) {
            $structureId = DB::table('salary_structures')->where('salary_structure_name', $structureName)->value('id')
                ?? DB::table('salary_structures')->insertGetId($this->row(['salary_structure_name' => $structureName]));

            foreach ($levels as $level => $maxStep) {
                $levelId = DB::table('salary_levels')
                    ->where('salary_structure_id', $structureId)
                    ->where(fn ($q) => $q->where('level', $level)->orWhere('level', (string) (int) $level))
                    ->value('id')
                    ?? DB::table('salary_levels')->insertGetId($this->row([
                        'salary_structure_id' => $structureId,
                        'level' => $level,
                    ]));

                for ($s = 1; $s <= $maxStep; $s++) {
                    $step = str_pad((string) $s, 2, '0', STR_PAD_LEFT);
                    $exists = DB::table('salary_steps')->where('salary_level_id', $levelId)
                        ->where(fn ($q) => $q->where('step', $step)->orWhere('step', (string) $s))
                        ->exists();
                    if (!$exists) {
                        DB::table('salary_steps')->insert($this->row(['salary_level_id' => $levelId, 'step' => $step]));
                    }
                }
            }
        }
    }

    private function row(array $values): array
    {
        return $values + ['uuid' => (string) Str::uuid(), 'created_at' => now(), 'updated_at' => now()];
    }
}
