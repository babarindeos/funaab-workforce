<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Lookup data exported from the live database (2 Nov 2026).
 * Idempotent: only rows that are missing (by natural key) are added.
 */
class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(__DIR__ . '/data/reference.json'), true);

        // Geography keeps its original ids on an empty table, so that
        // states → zones, LGAs → states and districts → LGAs line up.
        $this->seedWithIds('geo_pol_zones', $data['geo_pol_zones'], ['zone']);
        $this->seedWithIds('states', $data['states'], ['state_name']);
        $this->seedWithIds('lgas', $data['lgas'], ['state_id', 'lga_name']);
        $this->seedByKey('senatorial_districts', $data['senatorial_districts'], ['lga_id', 'senatorial_district_name']);

        $this->seedWithIds('genders', $data['genders'], ['gender_name']);
        $this->seedByKey('marital_statuses', $data['marital_statuses'], ['status']);
        $this->seedByKey('staff_titles', $data['staff_titles'], ['title']);
        $this->seedByKey('staff_statuses', $data['staff_statuses'], ['status']);
        $this->seedByKey('employment_types', $data['employment_types'], ['employment_type']);
        $this->seedByKey('degrees', $data['degrees'], ['degree_name']);
        $this->seedByKey('degree_classes', $data['degree_classes'], ['degree_class_name']);
        $this->seedByKey('division_types', $data['division_types'], ['division_type_name']);
        $this->seedByKey('contact_types', $data['contact_types'], ['contact_type_name']);
        $this->seedByKey('identity_update_fields', $data['identity_update_fields'], ['field']);
    }

    private function seedWithIds(string $table, array $rows, array $keys): void
    {
        if (DB::table($table)->exists()) {
            $this->seedByKey($table, array_map(fn ($r) => array_diff_key($r, ['id' => 1]), $rows), $keys);
            return;
        }
        foreach (array_chunk($rows, 200) as $chunk) {
            DB::table($table)->insert(array_map(fn ($r) => $this->stamp($table, $r), $chunk));
        }
    }

    private function seedByKey(string $table, array $rows, array $keys): void
    {
        foreach ($rows as $row) {
            $q = DB::table($table);
            foreach ($keys as $k) {
                $q->where($k, $row[$k]);
            }
            if (!$q->exists()) {
                DB::table($table)->insert($this->stamp($table, $row));
            }
        }
    }

    private function stamp(string $table, array $row): array
    {
        static $hasUuid = [];
        $hasUuid[$table] ??= Schema::hasColumn($table, 'uuid');
        if ($hasUuid[$table] && empty($row['uuid'])) {
            $row['uuid'] = (string) Str::uuid();
        }
        return $row + ['created_at' => now(), 'updated_at' => now()];
    }
}
