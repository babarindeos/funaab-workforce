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
            DocumentCategorySeeder::class,
        ]);

        // University structure from the nominal roll. Feature tests build their
        // own small structure, so they skip this (OrganisationSeederTest covers it).
        if (!app()->runningUnitTests()) {
            $this->call(OrganisationSeeder::class);
        }
    }
}
