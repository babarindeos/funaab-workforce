<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Colleges / divisions, their departments / units and a starter list of
 * deployment posts, taken from the July 2026 nominal roll
 * (database/seeders/data/organisation.json).
 *
 * Only adds what is missing. A record that already exists (matched by short
 * name, name or a nominal-roll spelling) is left exactly as Registry has it,
 * so names corrected in the admin screens are never overwritten.
 */
class OrganisationSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('seeders/data/organisation.json')), true);

        DB::transaction(function () use ($data) {
            $types = [];
            foreach ($data['division_types'] as $name) {
                $types[$name] = $this->findOrCreate('division_types', ['division_type_name' => $name], []);
            }

            foreach ($data['divisions'] as $div) {
                $divisionId = $this->findDivision($div) ?? $this->insert('divisions', [
                    'division_type_id' => $types[$div['type']],
                    'division_name'    => $div['name'],
                    'short_name'       => $div['short'],
                ]);

                foreach ($div['departments'] as $dept) {
                    if ($this->findDepartment($dept)) continue;
                    $this->insert('departments', [
                        'division_id'     => $divisionId,
                        'department_name' => $dept['name'],
                        'short_name'      => $dept['short'],
                    ]);
                }
            }

            foreach ($data['deployment_posts'] as $post) {
                $exists = DB::table('deployment_posts')->whereRaw('LOWER(deployment_post) = ?', [mb_strtolower($post)])->exists();
                if (!$exists) {
                    DB::table('deployment_posts')->insert(['deployment_post' => $post, 'created_at' => now(), 'updated_at' => now()]);
                }
            }
        });
    }

    private function findDivision(array $div): ?int
    {
        $names = array_map('mb_strtolower', [$div['name'], $div['short'], ...$div['aliases']]);
        $id = DB::table('divisions')->whereRaw('LOWER(short_name) = ?', [mb_strtolower($div['short'])])->value('id');
        return $id ?? DB::table('divisions')
            ->where(fn ($q) => $q->whereIn(DB::raw('LOWER(division_name)'), $names))
            ->value('id');
    }

    private function findDepartment(array $dept): ?int
    {
        $names = array_map('mb_strtolower', [$dept['name'], ...$dept['aliases']]);
        $id = DB::table('departments')->whereRaw('LOWER(short_name) = ?', [mb_strtolower($dept['short'])])->value('id');
        return $id ?? DB::table('departments')->whereIn(DB::raw('LOWER(department_name)'), $names)->value('id');
    }

    private function findOrCreate(string $table, array $match, array $extra): int
    {
        return DB::table($table)->where($match)->value('id') ?? $this->insert($table, $match + $extra);
    }

    private function insert(string $table, array $values): int
    {
        return DB::table($table)->insertGetId($values + [
            'uuid' => (string) Str::uuid(), 'created_at' => now(), 'updated_at' => now(),
        ]);
    }
}
