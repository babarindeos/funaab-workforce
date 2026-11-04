<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\OrganisationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrganisationSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_loads_structure_once_and_keeps_registry_edits(): void
    {
        $this->seed(DatabaseSeeder::class);

        // An existing DICTS (as on the live system) is reused, not duplicated.
        $type = DB::table('division_types')->where('division_type_name', 'Directorate')->value('id');
        $dicts = DB::table('divisions')->insertGetId(['uuid' => (string) Str::uuid(), 'division_type_id' => $type,
            'division_name' => 'Directorate of ICT Services', 'short_name' => 'DICTS', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('departments')->insert(['uuid' => (string) Str::uuid(), 'division_id' => $dicts,
            'department_name' => 'Directorate of Information Communication Services', 'short_name' => 'DICTS', 'created_at' => now(), 'updated_at' => now()]);

        $this->seed(OrganisationSeeder::class);

        $this->assertSame(1, DB::table('divisions')->where('short_name', 'DICTS')->count());
        $this->assertSame(1, DB::table('departments')->where('short_name', 'DICTS')->count());
        $this->assertSame(11, DB::table('divisions')->where('division_type_id', DB::table('division_types')->where('division_type_name', 'College')->value('id'))->count());

        $coleng = DB::table('divisions')->where('short_name', 'COLENG')->value('id');
        $this->assertSame($coleng, DB::table('departments')->where('short_name', 'MCE')->value('division_id'));
        $this->assertTrue(DB::table('departments')->where('department_name', "Dean's Office, COLENG")->exists());
        $this->assertTrue(DB::table('deployment_posts')->where('deployment_post', 'Desk Officer')->exists());

        // A name Registry corrects survives a re-run; nothing is duplicated.
        DB::table('departments')->where('short_name', 'MCE')->update(['department_name' => 'Mechanical Engineering (renamed)']);
        $counts = [DB::table('divisions')->count(), DB::table('departments')->count(), DB::table('deployment_posts')->count()];
        $this->seed(OrganisationSeeder::class);
        $this->assertSame($counts, [DB::table('divisions')->count(), DB::table('departments')->count(), DB::table('deployment_posts')->count()]);
        $this->assertSame('Mechanical Engineering (renamed)', DB::table('departments')->where('short_name', 'MCE')->value('department_name'));
    }
}
