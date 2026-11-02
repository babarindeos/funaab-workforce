<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * All seeders are idempotent: they only add what is missing (and bring
     * APER scoring in line with the official forms), so it is safe to run
     * `php artisan db:seed` on the live database.
     */
    public function run(): void
    {
        $this->call([
            ReferenceDataSeeder::class,
            StaffCategorySeeder::class,
            SalaryScaleSeeder::class,
            AperScoringSeeder::class,
        ]);
    }
}
