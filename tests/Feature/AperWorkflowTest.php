<?php

namespace Tests\Feature;

use App\Models\AperSubmission;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * End-to-end APER workflow (Junior and Senior Non-Teaching) against the
 * rules on the official forms.
 */
class AperWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private int $deptId;
    private int $otherDeptId;
    private string $formUuid;
    private int $formId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);

        $divTypeId = DB::table('division_types')->value('id');
        $divId = DB::table('divisions')->insertGetId($this->row(['division_type_id' => $divTypeId, 'division_name' => 'Registry', 'short_name' => 'REG']));
        $this->deptId      = DB::table('departments')->insertGetId($this->row(['division_id' => $divId, 'department_name' => 'Establishment', 'short_name' => 'EST']));
        $this->otherDeptId = DB::table('departments')->insertGetId($this->row(['division_id' => $divId, 'department_name' => 'Pensions', 'short_name' => 'PEN']));

        $this->formUuid = (string) Str::uuid();
        $this->formId = DB::table('aper_forms')->insertGetId([
            'uuid' => $this->formUuid, 'name' => 'APER 2026',
            'review_period_start' => '2025-10-01', 'review_period_end' => '2026-09-30',
            'start_date' => now()->subDay()->toDateString(), 'end_date' => now()->addMonth()->toDateString(),
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function row(array $v): array
    {
        return $v + ['uuid' => (string) Str::uuid(), 'created_at' => now(), 'updated_at' => now()];
    }

    /** Creates a user + staff + employment on CONTISS $level, returns [User, staffId, staffUuid]. */
    private function person(string $fileno, int $level, ?int $deptId = null): array
    {
        $deptId ??= $this->deptId;
        $user = User::forceCreate([
            'fileno' => $fileno, 'surname' => "Sur{$fileno}", 'firstname' => 'First',
            'email' => strtolower($fileno) . '@funaab.test', 'password' => bcrypt('secret'), 'role' => 'staff',
        ]);
        DB::table('profiles')->insert(['user_id' => $user->id, 'designation' => 'x', 'phone' => '0800', 'avatar' => 'a.png', 'created_at' => now(), 'updated_at' => now()]);

        $uuid    = (string) Str::uuid();
        $staffId = DB::table('staff')->insertGetId([
            'uuid' => $uuid, 'staff_no' => $fileno, 'staff_title_id' => DB::table('staff_titles')->value('id'),
            'surname' => "Sur{$fileno}", 'firstname' => 'First', 'dob' => '1980-01-01',
            'gender_id' => DB::table('genders')->value('id'), 'lga_id' => DB::table('lgas')->value('id'),
            'marital_status_id' => DB::table('marital_statuses')->value('id'),
            'staff_status_id' => DB::table('staff_statuses')->value('id'),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $contiss = DB::table('salary_structures')->where('salary_structure_name', 'CONTISS')->value('id');
        $levelId = DB::table('salary_levels')->where('salary_structure_id', $contiss)->where('level', sprintf('%02d', $level))->value('id');
        $stepId  = DB::table('salary_steps')->where('salary_level_id', $levelId)->where('step', '02')->value('id');
        $cat     = DB::table('staff_categories')->where('category', $level <= 5 ? 'Junior Non-Teaching Staff' : 'Senior Non-Teaching Staff')->value('id');

        DB::table('employments')->insert([
            'staff_id' => $staffId, 'staff_category_id' => $cat, 'department_id' => $deptId,
            'division_id' => DB::table('departments')->where('id', $deptId)->value('division_id'),
            'date_funaab_first_appointment' => '2010-01-01', 'salary_structure_id' => $contiss, 'salary_step_id' => $stepId,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return [$user, $staffId, $uuid];
    }

    private function assign(string $table, int $staffId, ?int $deptId = null, ?string $category = null): void
    {
        $v = ['department_id' => $deptId ?? $this->deptId, 'staff_id' => $staffId, 'is_current' => 1];
        if ($table !== 'aper_supervisors') {
            $v['staff_category_id'] = DB::table('staff_categories')->where('category', $category ?? 'Non-Teaching Staff')->value('id');
        }
        DB::table($table)->insert($this->row($v));
    }

    private function sub(int $staffId): AperSubmission
    {
        return AperSubmission::where('aper_form_id', $this->formId)->where('staff_id', $staffId)->firstOrFail();
    }

    private function hodGrades(int $grade, array $fields): array
    {
        return array_fill_keys($fields, $grade);
    }

    // ─────────────────────────────────────────────────────────────

    public function test_junior_form_full_workflow(): void
    {
        [$staffU, $staffId, $staffUuid] = $this->person('JP100', 4);
        [$supU, $supId]   = $this->person('SP200', 9);
        [$hodU, $hodId]   = $this->person('SP300', 12);
        [$chU, $chId]     = $this->person('SP400', 14);
        $this->assign('aper_supervisors', $supId);
        $this->assign('aper_hods', $hodId);
        $this->assign('aper_chairman_dapcs', $chId);

        $f = $this->formUuid;

        // Staff opens the form: CONTISS 04 → Junior.
        $this->actingAs($staffU)->get("/staff/aper/{$f}")->assertOk()->assertSee('Junior Non-Teaching');

        // Part A + Part B, then Submit.
        $this->actingAs($staffU)->post("/staff/aper/{$f}/junior/part-a", [
            'children' => '2 (5, 8)', 'telephone_no' => '0803', 'appointment_confirmed' => 1, 'confirmation_date' => '2012-01-01',
            'qualifications' => [['institution' => 'Abeokuta Grammar School', 'from_date' => '1990', 'to_date' => '1996', 'qualification_obtained' => 'SSCE', 'subjects_grades' => 'English C4, Maths B3']],
        ])->assertRedirect();
        $this->assertDatabaseHas('aper_form_junior_qualifications', ['staff_id' => $staffId, 'subjects_grades' => 'English C4, Maths B3']);

        $this->actingAs($staffU)->post("/staff/aper/{$f}/junior/part-b", ['duties' => ['Filing', 'Dispatch']])->assertRedirect();
        $this->assertSame('draft', $this->sub($staffId)->status);
        $this->actingAs($staffU)->get("/staff/aper/{$f}/junior/part-b")->assertOk()->assertSee('Submit APER Form');
        $this->actingAs($staffU)->get("/staff/aper/{$f}/junior/part-c")->assertOk();
        $this->actingAs($staffU)->get('/staff/aper')->assertOk()->assertSee('Draft');

        // HOD cannot act before the Supervisor.
        $this->actingAs($hodU)->get("/staff/hod/{$f}/assess/{$staffUuid}")->assertForbidden();

        $this->actingAs($staffU)->post("/staff/aper/{$f}/submit")->assertRedirect(route('staff.aper.index'));
        $this->assertSame('submitted', $this->sub($staffId)->status);

        // Locked after submission.
        $this->actingAs($staffU)->post("/staff/aper/{$f}/junior/part-b", ['duties' => ['Changed']])->assertSessionHas('error');
        $this->assertDatabaseMissing('aper_form_junior_duties', ['staff_id' => $staffId, 'duty' => 'Changed']);

        // Supervisor (items 15–16).
        $this->actingAs($supU)->get('/staff/supervisor')->assertOk();
        $this->actingAs($supU)->get("/staff/supervisor/{$f}/staff")->assertOk()->assertSee('SurJP100');
        $this->actingAs($supU)->get("/staff/supervisor/{$f}/assess/{$staffUuid}")->assertOk()->assertSee('11–20');
        $this->actingAs($supU)->get("/staff/supervisor/{$f}/submission/{$staffUuid}")->assertOk();
        $this->actingAs($supU)->get("/staff/supervisor/{$f}/submission/{$staffUuid}/aper_junior_part_a")->assertOk();
        $this->actingAs($supU)->get("/staff/supervisor/{$f}/submission/{$staffUuid}/aper_junior_part_b")->assertOk();
        $this->actingAs($supU)->post("/staff/supervisor/{$f}/assess/{$staffUuid}", [
            'with_medical_certificate' => '2', 'without_medical_certificate' => '0',
            'frequency' => 'Many (11–20 Units)', 'sanctions' => [],
        ])->assertRedirect();
        $this->assertSame('supervisor_done', $this->sub($staffId)->status);
        $this->assertSame($supId, $this->sub($staffId)->supervisor_staff_id);

        $this->actingAs($staffU)->get("/staff/aper/{$f}/junior/supervisor-evaluation")->assertOk();
        $this->actingAs($hodU)->get('/staff/hod')->assertOk();
        $this->actingAs($hodU)->get("/staff/hod/{$f}/staff")->assertOk()->assertSee('SurJP100');
        $this->actingAs($hodU)->get("/staff/hod/{$f}/assess/{$staffUuid}")->assertOk()->assertSee('Particulars supplied by the Supervisor');
        $this->actingAs($hodU)->get("/staff/hod/{$f}/submission/junior/{$staffUuid}")->assertOk();

        // HOD: punctuality 3/5 (60%) + promotion → refused by the 70% rule.
        $fields = ['punctuality', 'initiative', 'interpersonal', 'responsibility', 'organisational_ability', 'work_quality'];
        $promoLevel = DB::table('salary_levels')->where('level', '05')->value('id');
        $this->actingAs($hodU)->post("/staff/hod/{$f}/assess/{$staffUuid}", $this->hodGrades(3, $fields) + [
            'general_remarks' => 'ok', 'promotion_salary_level_id' => $promoLevel,
        ])->assertSessionHasErrors('promotion_salary_level_id');

        // HOD: all grades 5, recommends promotion.
        $this->actingAs($hodU)->post("/staff/hod/{$f}/assess/{$staffUuid}", $this->hodGrades(5, $fields) + [
            'general_remarks' => 'Excellent', 'promotion_salary_level_id' => $promoLevel,
        ])->assertRedirect();
        $s = $this->sub($staffId);
        $this->assertSame('hod_done', $s->status);
        $this->assertTrue($s->recommended_for_promotion);
        $this->assertTrue($s->punctuality_ok);

        // Chairman cannot score before the staff comment.
        $this->actingAs($chU)->get("/staff/chairman/{$f}/assess/{$staffUuid}")->assertForbidden();

        $this->actingAs($staffU)->get("/staff/aper/{$f}/junior/part-c")->assertOk()->assertSee('I certify');

        // Staff comment (item 20).
        $this->actingAs($staffU)->post("/staff/aper/{$f}/junior/part-c/comment", ['comment' => 'Thank you'])->assertRedirect();
        $this->assertSame('staff_commented', $this->sub($staffId)->status);

        // Panel: HOD's Assessment (30) and Reliability (org 5×2=10) are calculated.
        $this->actingAs($chU)->get('/staff/chairman')->assertOk();
        $this->actingAs($chU)->get("/staff/chairman/{$f}/staff")->assertOk()->assertSee('SurJP100');
        $this->actingAs($chU)->get("/staff/chairman/{$f}/assess/{$staffUuid}")->assertOk()->assertSee('Calculated');
        $guides = DB::table('aper_form_junior_scoring_guides')->get()->keyBy('code');
        $manual = ['qualifications' => 8, 'conduct' => 8, 'initiative' => 12, 'punctuality' => 9, 'self_improvement' => 4, 'human_relations' => 4, 'commendation' => 3];
        $scores = [];
        foreach ($manual as $code => $v) $scores[$guides[$code]->id] = $v;
        $scores[$guides['hod_assessment']->id] = 1; // ignored – derived on the server

        $this->actingAs($chU)->post("/staff/chairman/{$f}/assess/{$staffUuid}", [
            'scores' => $scores, 'recommendation' => 'endorse', 'endorse' => 'Agreed',
        ])->assertRedirect();

        $s = $this->sub($staffId);
        $this->assertSame('completed', $s->status);
        // 8+8+12+9+4+4+3 manual + 30 HOD + 10 reliability = 88
        $this->assertEquals(88.0, $s->total_score);
        $this->assertEquals(60.0, $s->min_required);   // CONTISS 04 → 60%
        $this->assertTrue($s->meets_minimum);
        $this->assertDatabaseHas('aper_form_junior_scorings', ['staff_id' => $staffId, 'metric_id' => $guides['hod_assessment']->id, 'score' => 30]);
    }

    public function test_senior_form_goes_straight_to_hod_and_skips_panel_without_promotion(): void
    {
        [$staffU, $staffId, $staffUuid] = $this->person('SP500', 8);
        [$hodU, $hodId] = $this->person('SP600', 13);
        $this->assign('aper_hods', $hodId);
        $f = $this->formUuid;

        $this->actingAs($staffU)->get("/staff/aper/{$f}")->assertRedirect(route('staff.aper.senior.part_a1', $f));

        $this->actingAs($staffU)->get("/staff/aper/{$f}/senior/part-a1")->assertOk();
        $this->actingAs($staffU)->post("/staff/aper/{$f}/senior/part-a1", ['children' => 'None', 'telephone_no' => '0803'])->assertRedirect();
        $this->actingAs($staffU)->get("/staff/aper/{$f}/senior/part-a2")->assertOk()->assertSee('NEW THIS PERIOD');
        $this->actingAs($staffU)->get("/staff/aper/{$f}/senior/part-a3")->assertOk()->assertDontSee('Practicals Involved');
        $this->actingAs($staffU)->post("/staff/aper/{$f}/senior/part-a2", [
            'qualifications' => [['degree' => 'M.Sc.', 'class' => '', 'institution' => 'FUNAAB', 'date_obtained' => '2026', 'is_additional' => 1]],
        ])->assertRedirect();
        $this->assertDatabaseHas('aper_form_senior_qualifications', ['staff_id' => $staffId, 'degree' => 'M.Sc.', 'is_additional' => 1]);

        // Cannot submit without item 13.
        $this->actingAs($staffU)->post("/staff/aper/{$f}/submit")->assertSessionHas('error');
        $this->actingAs($staffU)->post("/staff/aper/{$f}/senior/part-a3", ['present_jobs' => ['Payroll']])->assertRedirect();
        $this->actingAs($staffU)->post("/staff/aper/{$f}/submit")->assertRedirect();
        $this->assertSame('submitted', $this->sub($staffId)->status);

        $fields = ['punctuality', 'initiative', 'expression', 'interpersonal', 'responsibility', 'competence', 'organisational_ability', 'work_quality', 'special_responsibility'];

        // Item 21 is mandatory.
        $this->actingAs($hodU)->post("/staff/hod/{$f}/assess/senior/{$staffUuid}", $this->hodGrades(4, $fields) + [
            'report_agreement' => 1, 'general_remarks' => 'Good',
        ])->assertSessionHasErrors('no_salary_increment');

        $this->actingAs($hodU)->post("/staff/hod/{$f}/assess/senior/{$staffUuid}", $this->hodGrades(4, $fields) + [
            'report_agreement' => 1, 'general_remarks' => 'Good', 'increment_steps' => 1,
        ])->assertRedirect();
        $this->assertSame('hod_done', $this->sub($staffId)->status);
        $this->actingAs($staffU)->get("/staff/aper/{$f}/senior/part-c")->assertOk()->assertSee('I certify');
        $this->actingAs($hodU)->get("/staff/hod/{$f}/assess/senior/{$staffUuid}")->assertOk();
        $this->actingAs($hodU)->get("/staff/hod/{$f}/submission/senior/{$staffUuid}")->assertOk();
        $this->assertDatabaseHas('aper_form_senior_hod_recommends', ['staff_id' => $staffId, 'increment_steps' => 1]);

        // No promotion recommended → the staff comment completes the form.
        $this->actingAs($staffU)->post("/staff/aper/{$f}/senior/part-c/comment", ['comment' => 'Noted'])->assertRedirect();
        $this->assertSame('completed', $this->sub($staffId)->status);

        // A second comment is refused.
        $this->actingAs($staffU)->post("/staff/aper/{$f}/senior/part-c/comment", ['comment' => 'Again'])->assertSessionHas('error');
    }

    public function test_assessors_are_limited_to_their_department_and_cannot_assess_themselves(): void
    {
        [$staffU, $staffId, $staffUuid] = $this->person('JP700', 3, $this->otherDeptId);
        [$supU, $supId, $supUuid] = $this->person('SP800', 9);
        $this->assign('aper_supervisors', $supId); // supervisor of the OTHER department

        DB::table('aper_submissions')->insert($this->row([
            'aper_form_id' => $this->formId, 'staff_id' => $staffId, 'form_type' => 'junior', 'status' => 'submitted',
        ]));

        $this->actingAs($supU)->get("/staff/supervisor/{$this->formUuid}/assess/{$staffUuid}")->assertForbidden();
        $this->actingAs($supU)->get("/staff/supervisor/{$this->formUuid}/submission/{$staffUuid}")->assertForbidden();
        $this->actingAs($supU)->get("/staff/supervisor/{$this->formUuid}/assess/{$supUuid}")->assertForbidden();
        $this->actingAs($supU)->get("/staff/supervisor/{$this->formUuid}/staff")->assertOk()->assertDontSee('SurJP700');
    }

    public function test_lookup_in_use_cannot_be_deleted_and_aper_form_with_submissions_is_protected(): void
    {
        [$staffU, $staffId] = $this->person('JP900', 2);
        $admin = User::forceCreate(['fileno' => 'ADM1', 'surname' => 'Admin', 'firstname' => 'A', 'email' => 'admin@funaab.test', 'password' => bcrypt('x'), 'role' => 'admin']);

        $this->actingAs($admin)->get('/admin/aper')->assertOk()->assertSee('PERIOD OF REPORT');
        $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
        $titleId = DB::table('staff')->where('id', $staffId)->value('staff_title_id');
        $this->actingAs($admin)->from('/admin/staff_titles')->delete("/admin/staff_titles/{$titleId}")->assertSessionHas('error');
        $this->assertDatabaseHas('staff', ['id' => $staffId]);

        DB::table('aper_submissions')->insert($this->row(['aper_form_id' => $this->formId, 'staff_id' => $staffId, 'form_type' => 'junior', 'status' => 'draft']));
        $this->actingAs($admin)->delete("/admin/aper/{$this->formUuid}")->assertSessionHas('error');
        $this->assertDatabaseHas('aper_forms', ['id' => $this->formId]);
    }

    public function test_login_without_staff_record_gets_a_friendly_page(): void
    {
        $user = User::forceCreate(['fileno' => 'SP3254', 'surname' => 'No', 'firstname' => 'Record', 'email' => 'x@funaab.test', 'password' => bcrypt('x'), 'role' => 'staff']);
        DB::table('profiles')->insert(['user_id' => $user->id, 'designation' => 'x', 'phone' => '1', 'avatar' => 'a', 'created_at' => now(), 'updated_at' => now()]);

        $this->actingAs($user)->get('/staff/aper')->assertStatus(403)->assertSee('could not be found');
    }
}
