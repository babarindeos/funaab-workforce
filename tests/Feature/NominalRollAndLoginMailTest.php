<?php

namespace Tests\Feature;

use App\Mail\StaffLoginMail;
use App\Models\Staff;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\OrganisationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Tests\TestCase;

class NominalRollAndLoginMailTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);
        $this->seed(OrganisationSeeder::class);
        $this->admin = User::forceCreate(['fileno' => 'ADM1', 'surname' => 'Registry', 'firstname' => 'Officer',
            'email' => 'registry@funaab.test', 'password' => bcrypt('x'), 'role' => 'admin']);

        // A DICTS member already onboarded (has an account) …
        User::forceCreate(['fileno' => 'TS103', 'surname' => 'Testthree', 'firstname' => 'Femi',
            'email' => 'testthree@example.test', 'password' => bcrypt('secret123'), 'role' => 'staff']);

        // … and a serving staff member who the roll says has retired.
        DB::table('staff')->insert([
            'uuid' => (string) Str::uuid(), 'staff_no' => 'TS900', 'staff_title_id' => DB::table('staff_titles')->value('id'),
            'surname' => 'Leaver', 'firstname' => 'Old', 'dob' => '1960-01-01', 'gender_id' => DB::table('genders')->value('id'),
            'staff_status_id' => DB::table('staff_statuses')->where('status', 'Active')->value('id'), 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function upload(): \App\Models\NominalRollImport
    {
        $file = new UploadedFile(base_path('tests/fixtures/nominal_roll_sample.xlsx'), 'NOMINAL ROLL.xlsx', null, null, true);
        $r = $this->actingAs($this->admin)->post('/admin/nominal-roll', ['file' => $file]); $r->assertSessionHasNoErrors(); $r->assertRedirect();
        return \App\Models\NominalRollImport::latest('id')->firstOrFail();
    }

    public function test_preview_changes_nothing_and_apply_imports_without_sending_mail(): void
    {
        Mail::fake();
        $import = $this->upload();
        $c = $import->summary['counts'];

        $this->assertSame(['rows' => 6, 'new' => 4, 'update' => 0, 'same' => 0, 'problems' => 2, 'disengaged' => 1, 'no_email' => 1], $c);
        $this->assertSame(1, Staff::count());                       // preview wrote nothing
        $this->actingAs($this->admin)->get('/admin/nominal-roll')->assertOk()->assertSee('NOMINAL ROLL.xlsx')->assertSee('Preview only');
        $this->actingAs($this->admin)->get("/admin/nominal-roll/{$import->uuid}")->assertOk()
            ->assertSee('Date of birth missing')->assertSee('Listed twice')->assertSee('Engr.')->assertSee('Will be recorded');

        $this->actingAs($this->admin)->post("/admin/nominal-roll/{$import->uuid}/apply", ['update_existing' => 1])->assertRedirect();

        $one = Staff::where('staff_no', 'TS101')->with(['employment.department', 'employment.division', 'contacts', 'title'])->first();
        $this->assertSame('Mechanical Engineering', $one->employment->department->department_name);
        $this->assertSame('COLENG', $one->employment->division->short_name);
        $this->assertSame('Lecturer I', $one->employment->designation_name);
        $this->assertSame('Engr.', $one->title->title);
        $this->assertSame(3, $one->contacts->count());              // 2 e-mails + 1 phone
        $this->assertSame('Academic Staff', $one->employment->staffCategory->category);

        $two = Staff::where('staff_no', 'TS102')->with('employment.department', 'contacts')->first();
        $this->assertSame('Testtwo', $two->surname);
        $this->assertSame("Dean's Office, COLENG", $two->employment->department->department_name);
        $this->assertSame('Registry', $two->employment->division->short_name);
        $this->assertNotNull($two->lga_id);                          // "Sagamu" => Shagamu
        $this->assertTrue($two->contacts->contains('contact_value', '08031112233'));
        $this->assertSame('Senior Non-Teaching Staff', $two->employment->staffCategory->category);

        // Retired per the disengagement sheet, with history.
        $leaver = Staff::where('staff_no', 'TS900')->first();
        $this->assertSame('Retired', $leaver->staffStatus->status);
        $this->assertSame(1, $leaver->offEmployments()->count());

        // DICTS member already onboarded is marked as sent; nobody was mailed.
        $this->assertNotNull(User::where('fileno', 'TS103')->value('login_mail_sent_at'));
        Mail::assertNothingSent();
        $this->assertSame(0, User::where('role', 'staff')->where('fileno', '!=', 'TS103')->count());

        // Running the same roll again changes nothing.
        $again = $this->upload();
        $this->assertSame(0, $again->summary['counts']['new']);
        $this->assertSame(0, $again->summary['counts']['update']);
        $this->assertSame(0, $again->summary['counts']['disengaged']);
    }

    public function test_bulk_send_only_mails_staff_never_sent_and_links_set_a_password(): void
    {
        Mail::fake();
        $import = $this->upload();
        $this->actingAs($this->admin)->post("/admin/nominal-roll/{$import->uuid}/apply", ['update_existing' => 1]);

        $page = $this->actingAs($this->admin)->get('/admin/login-mails')->assertOk();
        $counts = $page->viewData('counts');
        $this->assertSame(2, $counts['not_sent']);     // TS101, TS102 (TS103 DICTS already sent)
        $this->assertSame(1, $counts['sent']);
        $this->assertSame(1, $counts['no_email']);     // TS105

        $res = $this->actingAs($this->admin)->postJson('/admin/login-mails/bulk', [])->assertOk()->json();
        $this->assertSame(2, $res['sent']);
        $this->assertSame(0, $res['remaining']);
        Mail::assertSent(StaffLoginMail::class, 2);
        Mail::assertNotSent(StaffLoginMail::class, fn ($m) => $m->user->fileno === 'TS103');

        // Second click: nobody left.
        $this->assertSame(0, $this->actingAs($this->admin)->postJson('/admin/login-mails/bulk', [])->json('sent'));

        $user = User::where('fileno', 'TS101')->first();
        $this->assertSame('testone@example.test', $user->email);   // first address on the roll
        $this->assertSame(1, $user->login_mail_count);
        $this->assertSame($this->admin->id, $user->login_mail_sent_by);

        // Resend to one person counts again.
        $staff = Staff::where('staff_no', 'TS101')->first();
        $this->actingAs($this->admin)->post("/admin/login-mails/staff/{$staff->id}")->assertRedirect();
        $this->assertSame(2, $user->fresh()->login_mail_count);

        // The mailed link sets a password; then they sign in with Staff No. or e-mail.
        auth()->logout();
        $token = Password::broker('invites')->createToken($user);
        $this->get(route('password.invite', ['token' => $token, 'email' => $user->email]))->assertOk()->assertSee('Choose your password');
        $this->post('/reset-password', ['token' => $token, 'email' => $user->email, 'broker' => 'invites',
            'password' => 'NewPass123', 'password_confirmation' => 'NewPass123'])->assertRedirect(route('welcome'));

        $this->post('/', ['email' => 'ts101', 'password' => 'NewPass123'])->assertRedirect(route('staff.dashboard.index'));
        $this->assertNotNull($user->fresh()->last_login_at);
        $this->actingAs($this->admin)->get('/admin/staff/' . $staff->id)->assertOk()->assertSee('Signed in');
        $this->actingAs($this->admin)->get('/admin/login-mails?status=signed_in')->assertOk()->assertSee('TS101');
        auth()->logout();
        auth()->logout();
        $this->post('/', ['email' => 'testone@example.test', 'password' => 'NewPass123'])->assertRedirect(route('staff.dashboard.index'));
    }

    public function test_e_mail_already_used_by_another_account_is_reported(): void
    {
        Mail::fake();
        $import = $this->upload();
        $this->actingAs($this->admin)->post("/admin/nominal-roll/{$import->uuid}/apply");
        User::forceCreate(['fileno' => 'OTHER1', 'surname' => 'X', 'firstname' => 'Y', 'email' => 'testtwo@example.test', 'password' => 'x', 'role' => 'staff']);

        $staff = Staff::where('staff_no', 'TS102')->first();
        $this->actingAs($this->admin)->post("/admin/login-mails/staff/{$staff->id}")->assertSessionHas('error');
        Mail::assertNothingSent();
        $this->assertDatabaseHas('login_mail_logs', ['staff_id' => $staff->id, 'status' => 'failed']);
    }
}
