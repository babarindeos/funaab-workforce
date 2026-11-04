<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class StaffRecordTabsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private int $staffId;
    private int $home;
    private int $deptB;
    private int $deptC;
    private int $post;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::forceCreate(['fileno' => 'ADM1', 'surname' => 'R', 'firstname' => 'O', 'email' => 'r@funaab.test', 'password' => bcrypt('x'), 'role' => 'admin']);

        $div = DB::table('divisions')->insertGetId($this->row(['division_type_id' => DB::table('division_types')->value('id'), 'division_name' => 'Registry', 'short_name' => 'REG']));
        $this->home  = DB::table('departments')->insertGetId($this->row(['division_id' => $div, 'department_name' => 'Establishment', 'short_name' => 'EST']));
        $this->deptB = DB::table('departments')->insertGetId($this->row(['division_id' => $div, 'department_name' => 'Pensions', 'short_name' => 'PEN']));
        $this->deptC = DB::table('departments')->insertGetId($this->row(['division_id' => $div, 'department_name' => 'Council Affairs', 'short_name' => 'CA']));
        $this->post  = DB::table('deployment_posts')->insertGetId(['deployment_post' => 'Desk Officer', 'created_at' => now(), 'updated_at' => now()]);

        $this->staffId = DB::table('staff')->insertGetId($this->row([
            'staff_no' => 'SP50', 'staff_title_id' => DB::table('staff_titles')->value('id'), 'surname' => 'Adeyemi', 'firstname' => 'Tola',
            'dob' => '1970-01-01', 'gender_id' => DB::table('genders')->value('id'), 'lga_id' => DB::table('lgas')->value('id'),
            'marital_status_id' => DB::table('marital_statuses')->value('id'),
            'staff_status_id' => DB::table('staff_statuses')->where('status', 'Active')->value('id'),
        ]));
        DB::table('employments')->insert(['staff_id' => $this->staffId, 'department_id' => $this->home, 'division_id' => $div,
            'staff_category_id' => DB::table('staff_categories')->value('id'), 'created_at' => now(), 'updated_at' => now()]);
    }

    private function row(array $v): array
    {
        return $v + ['uuid' => (string) Str::uuid(), 'created_at' => now(), 'updated_at' => now()];
    }

    private function url(string $path = ''): string
    {
        return "/admin/staff/{$this->staffId}{$path}";
    }

    public function test_next_of_kin_history_keeps_previous_and_new_one_is_current(): void
    {
        $this->actingAs($this->admin)->post($this->url('/next-of-kin'), ['surname' => 'X'])->assertSessionHasErrorsIn('kin', 'relationship');

        $this->actingAs($this->admin)->post($this->url('/next-of-kin'), ['relationship' => 'Spouse', 'surname' => 'Adeyemi', 'firstname' => 'Bola', 'phone' => '0801'])
            ->assertRedirect($this->url() . '#nextofkin');
        $this->actingAs($this->admin)->post($this->url('/next-of-kin'), ['relationship' => 'Child', 'surname' => 'Adeyemi', 'firstname' => 'Kemi']);

        $kin = DB::table('next_of_kin')->where('staff_id', $this->staffId)->orderBy('id')->get();
        $this->assertCount(2, $kin);
        $this->assertSame(0, (int) $kin[0]->is_current);
        $this->assertNotNull($kin[0]->replaced_at);
        $this->assertSame(1, (int) $kin[1]->is_current);

        $this->actingAs($this->admin)->get($this->url())->assertOk()->assertSee('Current')->assertSee('Previous')->assertSee('Spouse');

        // Make the first one current again, edit it, delete the current one → the other becomes current.
        $this->actingAs($this->admin)->patch($this->url("/next-of-kin/{$kin[0]->id}/current"))->assertRedirect();
        $this->assertSame(1, (int) DB::table('next_of_kin')->where('id', $kin[0]->id)->value('is_current'));
        $this->assertSame(0, (int) DB::table('next_of_kin')->where('id', $kin[1]->id)->value('is_current'));

        $this->actingAs($this->admin)->put($this->url("/next-of-kin/{$kin[0]->id}"), ['relationship' => 'Spouse', 'surname' => 'Adeyemi', 'firstname' => 'Bolanle'])->assertRedirect();
        $this->assertDatabaseHas('next_of_kin', ['id' => $kin[0]->id, 'firstname' => 'Bolanle', 'phone' => null]);

        $this->actingAs($this->admin)->delete($this->url("/next-of-kin/{$kin[0]->id}"))->assertRedirect();
        $this->assertSame(1, (int) DB::table('next_of_kin')->where('id', $kin[1]->id)->value('is_current'));
    }

    public function test_contacts_use_contact_types(): void
    {
        $phone = DB::table('contact_types')->where('contact_type_name', 'Phone Number')->value('id');
        $email = DB::table('contact_types')->where('contact_type_name', 'Email Address')->value('id');

        $this->actingAs($this->admin)->post($this->url('/contacts'), ['contact_type_id' => $email, 'contact_value' => 'not-an-email'])
            ->assertSessionHasErrorsIn('contact', 'contact_value');
        $this->actingAs($this->admin)->post($this->url('/contacts'), ['contact_type_id' => $phone, 'contact_value' => '0803 111 2222'])->assertRedirect();
        $this->actingAs($this->admin)->post($this->url('/contacts'), ['contact_type_id' => $phone, 'contact_value' => '0805 333 4444'])->assertRedirect();
        $this->assertSame(2, DB::table('contacts')->where('staff_id', $this->staffId)->count());

        $c = DB::table('contacts')->where('staff_id', $this->staffId)->first();
        $this->actingAs($this->admin)->put($this->url("/contacts/{$c->id}"), ['contact_type_id' => $email, 'contact_value' => 'tola@funaab.edu.ng'])->assertRedirect();
        $this->actingAs($this->admin)->get($this->url())->assertSee('tola@funaab.edu.ng')->assertSee('Email Address');
        $this->actingAs($this->admin)->delete($this->url("/contacts/{$c->id}"))->assertRedirect();
        $this->assertSame(1, DB::table('contacts')->where('staff_id', $this->staffId)->count());
    }

    public function test_new_deployment_ends_the_ongoing_one_and_keeps_home_department(): void
    {
        $this->actingAs($this->admin)->post($this->url('/deployments'), ['department_id' => $this->deptB, 'deployment_post_id' => $this->post])
            ->assertSessionHasErrorsIn('deployment', 'start_date');

        $this->actingAs($this->admin)->post($this->url('/deployments'), [
            'department_id' => $this->deptB, 'deployment_post_id' => $this->post, 'start_date' => '2025-01-01',
        ])->assertRedirect($this->url() . '#deployments');

        $first = DB::table('deployments')->where('staff_id', $this->staffId)->first();
        $this->assertSame($this->home, (int) $first->from_department_id);      // defaults to home department
        $this->assertNull($first->end_date);

        $this->actingAs($this->admin)->post($this->url('/deployments'), [
            'department_id' => $this->deptC, 'deployment_post_id' => $this->post, 'start_date' => '2026-03-01',
        ])->assertRedirect();

        $this->assertSame('2026-02-28', DB::table('deployments')->where('id', $first->id)->value('end_date'));
        $second = DB::table('deployments')->where('staff_id', $this->staffId)->orderByDesc('id')->first();
        $this->assertSame($this->deptB, (int) $second->from_department_id);    // from = where they were
        $this->assertNull($second->end_date);
        $this->assertSame($this->home, (int) DB::table('employments')->where('staff_id', $this->staffId)->value('department_id'));

        // From and To must differ.
        $this->actingAs($this->admin)->post($this->url('/deployments'), [
            'from_department_id' => $this->deptC, 'department_id' => $this->deptC, 'deployment_post_id' => $this->post, 'start_date' => '2026-06-01',
        ])->assertSessionHasErrorsIn('deployment', 'department_id');

        $this->actingAs($this->admin)->get($this->url())->assertOk()->assertSee('Ongoing')->assertSee('Council Affairs');

        $this->actingAs($this->admin)->put($this->url("/deployments/{$second->id}"), [
            'from_department_id' => $this->deptB, 'department_id' => $this->deptC, 'deployment_post_id' => $this->post,
            'start_date' => '2026-03-01', 'end_date' => '2026-09-30',
        ])->assertRedirect();
        $this->assertSame('2026-09-30', DB::table('deployments')->where('id', $second->id)->value('end_date'));

        $this->actingAs($this->admin)->delete($this->url("/deployments/{$second->id}"))->assertRedirect();
        $this->assertDatabaseMissing('deployments', ['id' => $second->id]);
    }

    public function test_off_employment_changes_status_and_deleting_restores_it(): void
    {
        $active  = DB::table('staff_statuses')->where('status', 'Active')->value('id');
        $retired = DB::table('staff_statuses')->where('status', 'Retired')->value('id');
        $dead    = DB::table('staff_statuses')->where('status', 'Deceased')->value('id');

        $this->actingAs($this->admin)->post($this->url('/off-employments'), ['staff_status_id' => $active, 'effective_date' => '2026-01-01', 'reason' => 'x'])
            ->assertSessionHasErrorsIn('offemp', 'staff_status_id');

        $this->actingAs($this->admin)->post($this->url('/off-employments'), [
            'staff_status_id' => $retired, 'effective_date' => '2026-01-01', 'reason' => 'Mandatory retirement', 'document_reference' => 'REG/RT/1',
        ])->assertRedirect($this->url() . '#offemployments');
        $this->assertSame($retired, (int) DB::table('staff')->where('id', $this->staffId)->value('staff_status_id'));

        $off = DB::table('off_employments')->where('staff_id', $this->staffId)->first();
        $this->assertSame($active, (int) $off->previous_staff_status_id);

        // Editing the latest record keeps the status in step.
        $this->actingAs($this->admin)->put($this->url("/off-employments/{$off->id}"), ['staff_status_id' => $dead, 'effective_date' => '2026-01-01', 'reason' => 'Died in service'])->assertRedirect();
        $this->assertSame($dead, (int) DB::table('staff')->where('id', $this->staffId)->value('staff_status_id'));

        // Dashboard no longer counts them as serving.
        $this->actingAs($this->admin)->get('/admin/dashboard')->assertViewHas('totalStaff', 0);

        // Deleting (entered in error) restores the previous status.
        $this->actingAs($this->admin)->delete($this->url("/off-employments/{$off->id}"))->assertRedirect();
        $this->assertSame($active, (int) DB::table('staff')->where('id', $this->staffId)->value('staff_status_id'));
    }

    public function test_staff_list_has_no_dead_sections_bar_and_shows_active_count(): void
    {
        $this->actingAs($this->admin)->get('/admin/staff')->assertOk()
            ->assertDontSee('SECTIONS:')->assertViewHas('totalActive', 1);
    }

    public function test_records_of_another_staff_member_cannot_be_changed_through_this_record(): void
    {
        $other = DB::table('staff')->insertGetId($this->row([
            'staff_no' => 'SP51', 'staff_title_id' => DB::table('staff_titles')->value('id'), 'surname' => 'O', 'firstname' => 'O',
            'dob' => '1970-01-01', 'gender_id' => DB::table('genders')->value('id'), 'lga_id' => DB::table('lgas')->value('id'),
            'marital_status_id' => DB::table('marital_statuses')->value('id'), 'staff_status_id' => DB::table('staff_statuses')->value('id'),
        ]));
        $c = DB::table('contacts')->insertGetId(['staff_id' => $other, 'contact_type_id' => DB::table('contact_types')->value('id'), 'contact_value' => '1', 'created_at' => now(), 'updated_at' => now()]);
        $this->actingAs($this->admin)->delete($this->url("/contacts/{$c}"))->assertNotFound();
        $this->assertDatabaseHas('contacts', ['id' => $c]);
    }
}
