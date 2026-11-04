<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class StaffQualificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registry_can_add_edit_and_delete_qualifications_and_tabs_show_real_values(): void
    {
        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);
        $admin = User::forceCreate(['fileno' => 'ADM1', 'surname' => 'R', 'firstname' => 'O', 'email' => 'r@funaab.test', 'password' => bcrypt('x'), 'role' => 'admin']);

        $staffId = DB::table('staff')->insertGetId([
            'uuid' => (string) Str::uuid(), 'staff_no' => 'SP99', 'staff_title_id' => DB::table('staff_titles')->value('id'),
            'surname' => 'Akinola', 'firstname' => 'Bisi', 'dob' => '1985-02-03',
            'gender_id' => DB::table('genders')->where('gender_name', 'Female')->value('id'),
            'lga_id' => DB::table('lgas')->value('id'),
            'marital_status_id' => DB::table('marital_statuses')->where('status', 'Married')->value('id'),
            'staff_status_id' => DB::table('staff_statuses')->where('status', 'Active')->value('id'),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('employments')->insert(['staff_id' => $staffId, 'staff_category_id' => DB::table('staff_categories')->where('category', 'Senior Non-Teaching Staff')->value('id'), 'created_at' => now(), 'updated_at' => now()]);

        // Correct column values now show on the tabs.
        $this->actingAs($admin)->get("/admin/staff/{$staffId}")->assertOk()
            ->assertSee('Female')->assertSee('Married')->assertSee('Active')->assertSee('Senior Non-Teaching Staff')
            ->assertSee('Add Qualification')->assertSee('Add Contact')->assertSee('Add Next of Kin')->assertSee('Add Deployment')->assertSee('Record Off-Employment');

        $bsc   = DB::table('degrees')->where('degree_name', 'Bachelor of Science')->value('id');
        $msc   = DB::table('degrees')->where('degree_name', 'Master of Science')->value('id');
        $upper = DB::table('degree_classes')->where('degree_class_name', 'Second Class Upper')->value('id');
        $this->assertNotNull($upper, 'degree classes are seeded');

        // Validation
        $this->actingAs($admin)->post("/admin/staff/{$staffId}/qualifications", ['institution' => 'UI'])
            ->assertSessionHasErrorsIn('qualification', 'degree_id');
        $this->actingAs($admin)->post("/admin/staff/{$staffId}/qualifications", ['degree_id' => $bsc, 'institution' => 'UI', 'start_date' => '2010-01-01', 'end_date' => '2009-01-01'])
            ->assertSessionHasErrorsIn('qualification', 'end_date');

        // Add
        $this->actingAs($admin)->post("/admin/staff/{$staffId}/qualifications", [
            'degree_id' => $bsc, 'institution' => 'University of Ibadan', 'start_date' => '2003-10-01', 'end_date' => '2007-07-30', 'degree_class_id' => $upper,
        ])->assertRedirect("/admin/staff/{$staffId}#qualifications");
        $q = DB::table('qualifications')->where('staff_id', $staffId)->first();
        $this->assertSame('University of Ibadan', $q->institution);

        $this->actingAs($admin)->get("/admin/staff/{$staffId}")->assertSee('University of Ibadan')->assertSee('Second Class Upper');

        // Edit
        $this->actingAs($admin)->put("/admin/staff/{$staffId}/qualifications/{$q->id}", [
            'degree_id' => $msc, 'institution' => 'FUNAAB', 'remark' => 'Distinction',
        ])->assertRedirect();
        $this->assertDatabaseHas('qualifications', ['id' => $q->id, 'degree_id' => $msc, 'institution' => 'FUNAAB', 'remark' => 'Distinction', 'degree_class_id' => null]);

        // A qualification of another staff member cannot be changed through this record.
        $otherId = DB::table('staff')->insertGetId(['uuid' => (string) Str::uuid(), 'staff_no' => 'SP98', 'staff_title_id' => DB::table('staff_titles')->value('id'),
            'surname' => 'O', 'firstname' => 'O', 'dob' => '1980-01-01', 'gender_id' => DB::table('genders')->value('id'), 'lga_id' => DB::table('lgas')->value('id'),
            'marital_status_id' => DB::table('marital_statuses')->value('id'), 'staff_status_id' => DB::table('staff_statuses')->value('id'), 'created_at' => now(), 'updated_at' => now()]);
        $this->actingAs($admin)->delete("/admin/staff/{$otherId}/qualifications/{$q->id}")->assertNotFound();

        // Delete
        $this->actingAs($admin)->delete("/admin/staff/{$staffId}/qualifications/{$q->id}")->assertRedirect();
        $this->assertDatabaseMissing('qualifications', ['id' => $q->id]);
    }
}
