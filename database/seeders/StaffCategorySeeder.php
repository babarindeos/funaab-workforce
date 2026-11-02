<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Staff categories the APER module depends on.
 *   Academic Staff
 *   Non-Teaching Staff
 *     ├─ Senior Non-Teaching Staff  (CONTISS 06 and above)
 *     └─ Junior Non-Teaching Staff  (CONTISS 01 – 05)
 */
class StaffCategorySeeder extends Seeder
{
    public function run(): void
    {
        $academic    = $this->ensure('Academic Staff', null);
        $nonTeaching = $this->ensure('Non-Teaching Staff', null);
        $this->ensure('Senior Non-Teaching Staff', $nonTeaching);
        $this->ensure('Junior Non-Teaching Staff', $nonTeaching);
    }

    private function ensure(string $name, ?int $parent): int
    {
        $id = DB::table('staff_categories')->where('category', $name)->value('id');
        if ($id) {
            return $id;
        }
        return DB::table('staff_categories')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'parent_staff_category' => $parent,
            'category' => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
