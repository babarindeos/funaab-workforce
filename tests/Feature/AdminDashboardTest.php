<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private int $deptId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::forceCreate(['fileno' => 'ADM1', 'surname' => 'Registry', 'firstname' => 'Officer',
            'email' => 'registry@funaab.test', 'password' => bcrypt('x'), 'role' => 'admin']);

        $divId = DB::table('divisions')->insertGetId($this->row(['division_type_id' => DB::table('division_types')->value('id'), 'division_name' => 'Registry', 'short_name' => 'REG']));
        $this->deptId = DB::table('departments')->insertGetId($this->row(['division_id' => $divId, 'department_name' => 'Establishment Unit', 'short_name' => 'EST']));
    }

    private function row(array $v): array
    {
        return $v + ['uuid' => (string) Str::uuid(), 'created_at' => now(), 'updated_at' => now()];
    }

    private function staff(string $no, int $level, string $status = 'Active', string $empType = 'Full time Employment'): int
    {
        $id = DB::table('staff')->insertGetId($this->row([
            'staff_no' => $no, 'staff_title_id' => DB::table('staff_titles')->value('id'),
            'surname' => "Sur{$no}", 'firstname' => 'First', 'dob' => '1980-01-01',
            'gender_id' => DB::table('genders')->value('id'), 'lga_id' => DB::table('lgas')->value('id'),
            'marital_status_id' => DB::table('marital_statuses')->value('id'),
            'staff_status_id' => DB::table('staff_statuses')->where('status', $status)->value('id'),
        ]));
        $contiss = DB::table('salary_structures')->where('salary_structure_name', 'CONTISS')->value('id');
        $lvl     = DB::table('salary_levels')->where('salary_structure_id', $contiss)->where('level', sprintf('%02d', $level))->value('id');
        DB::table('employments')->insert([
            'staff_id' => $id, 'department_id' => $this->deptId,
            'staff_category_id' => DB::table('staff_categories')->where('category', $level <= 5 ? 'Junior Non-Teaching Staff' : 'Senior Non-Teaching Staff')->value('id'),
            'employment_type_id' => DB::table('employment_types')->where('employment_type', $empType)->value('id'),
            'salary_structure_id' => $contiss, 'salary_step_id' => DB::table('salary_steps')->where('salary_level_id', $lvl)->value('id'),
            'created_at' => now(), 'updated_at' => now(),
        ]);
        return $id;
    }

    public function test_dashboard_shows_live_figures_and_no_demo_data(): void
    {
        $a = $this->staff('SP1', 8);
        $this->staff('SP2', 8, 'Active', 'Contract Employment');
        $this->staff('JP3', 3);
        $this->staff('SP4', 9, 'Retired');   // not serving

        DB::table('leaves')->insert(['staff_id' => $a, 'leave_type_id' => DB::table('leave_types')->insertGetId(['leave_type' => 'Annual', 'created_at' => now(), 'updated_at' => now()]),
            'start_date' => now()->subDay(), 'end_date' => now()->addDays(3), 'resumption_date' => now()->addDays(4),
            'approval_status' => 'approved', 'leave_year' => now()->year, 'created_at' => now(), 'updated_at' => now()]);

        $this->actingAs($this->admin)->get('/admin/dashboard')
            ->assertOk()
            ->assertViewHas('totalStaff', 3)
            ->assertViewHas('onLeave', 1)
            ->assertViewHas('resumingSoon', 1)
            ->assertViewHas('totalDepartments', 1)
            ->assertViewHas('fullTime', 2)
            ->assertViewHas('otherTime', 1)
            ->assertSee('Senior Non-Teaching Staff')
            ->assertSee('66.7%')                       // 2 of 3 serving staff are Senior
            ->assertSee('SurSP1')                      // recent staff + activity
            ->assertSee('On Leave')
            ->assertDontSee('Adewale')->assertDontSee('1,247')->assertDontSee('Okonkwo');   // old demo content
    }

    public function test_aper_overview_counts_stages_and_flags_missing_assessors(): void
    {
        $j1 = $this->staff('JP1', 3);
        $j2 = $this->staff('JP2', 4);
        $s1 = $this->staff('SP1', 8);
        $this->staff('SP2', 9);

        $formId = DB::table('aper_forms')->insertGetId($this->row(['name' => 'APER 2026', 'start_date' => now()->subDay(), 'end_date' => now()->addMonth(),
            'review_period_start' => '2025-10-01', 'review_period_end' => '2026-09-30']));
        $formUuid = DB::table('aper_forms')->where('id', $formId)->value('uuid');

        DB::table('aper_submissions')->insert($this->row(['aper_form_id' => $formId, 'staff_id' => $j1, 'form_type' => 'junior', 'status' => 'submitted']));
        DB::table('aper_submissions')->insert($this->row(['aper_form_id' => $formId, 'staff_id' => $j2, 'form_type' => 'junior', 'status' => 'draft']));
        DB::table('aper_submissions')->insert($this->row(['aper_form_id' => $formId, 'staff_id' => $s1, 'form_type' => 'senior', 'status' => 'completed',
                'recommended_for_promotion' => 1, 'total_score' => 72, 'min_required' => 45, 'meets_minimum' => 1, 'punctuality_ok' => 1]));

        $res = $this->actingAs($this->admin)->get('/admin/aper/overview')->assertOk()->assertSee('APER 2026')->assertSee('No Supervisor, HOD, Chairman DAPC');
        $data = $res->viewData('data');
        $this->assertSame(4, $data['eligibleTotal']);
        $this->assertSame(1, $data['stageCounts']['not_started']);
        $this->assertSame(1, $data['stageCounts']['draft']);
        $this->assertSame(1, $data['stageCounts']['with_supervisor']);
        $this->assertSame(1, $data['stageCounts']['completed']);
        $this->assertSame(1, $data['results']['meets']);

        $this->actingAs($this->admin)->get("/admin/aper/overview/staff?form={$formUuid}&stage=with_supervisor")
            ->assertOk()->assertSee('SurJP1')->assertDontSee('SurJP2');
        $this->actingAs($this->admin)->get("/admin/aper/overview/staff?form={$formUuid}&result=meets")
            ->assertOk()->assertSee('SurSP1')->assertSee('72%');
    }
}
