<?php

namespace Tests\Feature;

use App\Models\StaffDocument;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class CircleAndDocumentsTest extends TestCase
{
    use RefreshDatabase;

    private int $divA;
    private int $divB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);
        Storage::fake('local');

        $type = DB::table('division_types')->value('id');
        $this->divA = DB::table('divisions')->insertGetId($this->row(['division_type_id' => $type, 'division_name' => 'DICTS', 'short_name' => 'DICTS']));
        $this->divB = DB::table('divisions')->insertGetId($this->row(['division_type_id' => $type, 'division_name' => 'Bursary', 'short_name' => 'BUR']));
    }

    private function row(array $v): array
    {
        return $v + ['uuid' => (string) Str::uuid(), 'created_at' => now(), 'updated_at' => now()];
    }

    private function person(string $no, int $divisionId, string $status = 'Active', bool $withLogin = true): array
    {
        $deptId = DB::table('departments')->insertGetId($this->row(['division_id' => $divisionId, 'department_name' => "Dept {$no}", 'short_name' => $no]));
        $staffId = DB::table('staff')->insertGetId($this->row([
            'staff_no' => $no, 'staff_title_id' => DB::table('staff_titles')->where('title', 'Mr.')->value('id'),
            'surname' => "Sur{$no}", 'firstname' => 'First', 'dob' => '1980-01-01',
            'gender_id' => DB::table('genders')->value('id'), 'lga_id' => DB::table('lgas')->value('id'),
            'marital_status_id' => DB::table('marital_statuses')->value('id'),
            'staff_status_id' => DB::table('staff_statuses')->where('status', $status)->value('id'),
        ]));
        DB::table('employments')->insert([
            'staff_id' => $staffId, 'staff_category_id' => DB::table('staff_categories')->value('id'),
            'division_id' => $divisionId, 'department_id' => $deptId, 'created_at' => now(), 'updated_at' => now(),
        ]);

        $user = null;
        if ($withLogin) {
            $user = User::forceCreate(['fileno' => $no, 'surname' => "Sur{$no}", 'firstname' => 'First',
                'email' => strtolower($no) . '@funaab.test', 'password' => bcrypt('x'), 'role' => 'staff']);
            DB::table('profiles')->insert(['user_id' => $user->id, 'designation' => 'x', 'phone' => '08031234567', 'avatar' => '', 'created_at' => now(), 'updated_at' => now()]);
        }
        return [$user, $staffId];
    }

    private function admin(): User
    {
        return User::forceCreate(['fileno' => 'ADM1', 'surname' => 'Registry', 'firstname' => 'Officer',
            'email' => 'registry@funaab.test', 'password' => bcrypt('x'), 'role' => 'admin']);
    }

    // ── Circle ───────────────────────────────────────────────────────

    public function test_circle_shows_only_serving_colleagues_in_my_division_with_email_not_phone(): void
    {
        [$me]  = $this->person('SP1', $this->divA);
        $this->person('SP2', $this->divA);
        $this->person('SP3', $this->divA, 'Retired');
        $this->person('SP4', $this->divB);
        $this->person('SP5', $this->divA, 'Active', false); // no portal account yet

        $this->actingAs($me)->get('/staff/circle')
            ->assertOk()
            ->assertSee('SurSP1')->assertSee('SurSP2')->assertSee('SurSP5')
            ->assertSee('sp2@funaab.test')
            ->assertDontSee('SurSP3')            // retired
            ->assertDontSee('SurSP4')            // other division
            ->assertDontSee('08031234567');      // phone never shown
    }

    // ── Documents ────────────────────────────────────────────────────

    public function test_staff_upload_waits_for_registry_and_can_be_rejected_then_approved(): void
    {
        [$me, $myId] = $this->person('SP10', $this->divA);
        $typeId = DB::table('document_types')->where('name', 'Promotion Letter')->value('id');

        $this->actingAs($me)->get('/staff/documents')->assertOk();
        $this->actingAs($me)->get('/staff/documents/upload')->assertOk()->assertSee('Promotion Letter');

        // Wrong file type refused.
        $this->actingAs($me)->post('/staff/documents', [
            'document_type_id' => $typeId, 'file' => UploadedFile::fake()->create('virus.exe', 10),
        ])->assertSessionHasErrors('file');

        $this->actingAs($me)->post('/staff/documents', [
            'document_type_id' => $typeId, 'title' => 'Promotion 2024',
            'file' => UploadedFile::fake()->create('promo.pdf', 200, 'application/pdf'),
        ])->assertRedirect('/staff/documents');

        $doc = StaffDocument::where('staff_id', $myId)->firstOrFail();
        $this->assertSame('pending', $doc->status);
        $this->assertSame('staff', $doc->source);
        Storage::disk('local')->assertExists($doc->file_path);
        $this->assertStringStartsWith('staff_documents/', $doc->file_path);   // private disk, not public/

        // Registry sees it in "awaiting approval" and rejects with a reason.
        $admin = $this->admin();
        $this->actingAs($admin)->get('/admin/documents')->assertOk()->assertSee('SurSP10');
        $this->actingAs($admin)->patch("/admin/documents/{$doc->uuid}/reject", [])->assertSessionHasErrors('rejection_reason');
        $this->actingAs($admin)->patch("/admin/documents/{$doc->uuid}/reject", ['rejection_reason' => 'Page 2 missing'])->assertRedirect();
        $this->assertSame('rejected', $doc->fresh()->status);
        $this->actingAs($me)->get('/staff/documents')->assertSee('Page 2 missing');

        // Staff withdraws the rejected copy and uploads a corrected one; Registry approves.
        $this->actingAs($me)->delete("/staff/documents/{$doc->uuid}")->assertRedirect();
        $this->assertDatabaseMissing('staff_documents', ['id' => $doc->id]);
        Storage::disk('local')->assertMissing($doc->file_path);

        $this->actingAs($me)->post('/staff/documents', [
            'document_type_id' => $typeId, 'file' => UploadedFile::fake()->create('promo2.pdf', 200, 'application/pdf'),
        ]);
        $doc2 = StaffDocument::where('staff_id', $myId)->firstOrFail();
        $this->actingAs($admin)->patch("/admin/documents/{$doc2->uuid}/approve")->assertRedirect();
        $this->assertSame('approved', $doc2->fresh()->status);

        // Official document cannot be withdrawn by the staff member.
        $this->actingAs($me)->delete("/staff/documents/{$doc2->uuid}")->assertSessionHas('error');
        $this->assertDatabaseHas('staff_documents', ['id' => $doc2->id]);
    }

    public function test_registry_upload_is_official_and_staff_cannot_open_other_peoples_documents(): void
    {
        [$me, $myId]       = $this->person('SP20', $this->divA);
        [$other, $otherId] = $this->person('SP21', $this->divA);
        $admin  = $this->admin();
        $typeId = DB::table('document_types')->where('name', 'Letter of Offer of Appointment')->value('id');

        $this->actingAs($admin)->get('/admin/documents/upload?staff_no=SP20')->assertOk()->assertSee('SP20');
        $this->actingAs($admin)->post('/admin/documents', [
            'staff_no' => 'SP20', 'document_type_id' => $typeId, 'document_date' => '2010-01-01',
            'file' => UploadedFile::fake()->image('offer.jpg'),
        ])->assertRedirect();

        $doc = StaffDocument::where('staff_id', $myId)->firstOrFail();
        $this->assertSame('approved', $doc->status);
        $this->assertSame('registry', $doc->source);

        $this->actingAs($admin)->get("/admin/documents/staff/{$myId}")->assertOk()->assertSee('Letter of Offer of Appointment');
        $this->actingAs($me)->get("/staff/documents/{$doc->uuid}/view")->assertOk();
        $this->actingAs($me)->get("/staff/documents/{$doc->uuid}/download")->assertOk()->assertDownload();
        $this->actingAs($other)->get("/staff/documents/{$doc->uuid}/view")->assertForbidden();
        $this->actingAs($me)->delete("/staff/documents/{$doc->uuid}")->assertSessionHas('error');

        // Registry can remove it.
        $this->actingAs($admin)->delete("/admin/documents/{$doc->uuid}")->assertRedirect();
        $this->assertDatabaseMissing('staff_documents', ['id' => $doc->id]);
    }

    public function test_registry_manages_categories_and_types_and_in_use_ones_are_protected(): void
    {
        [$me, $myId] = $this->person('SP30', $this->divA);
        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin/document-setup')->assertOk()->assertSee('Appointment Records');

        $this->actingAs($admin)->post('/admin/document-setup/categories', ['name' => 'Pension Records'])->assertRedirect();
        $cat = DB::table('document_categories')->where('name', 'Pension Records')->first();
        $this->actingAs($admin)->post('/admin/document-setup/types', ['document_category_id' => $cat->id, 'name' => 'Pension Form'])->assertRedirect();
        $type = DB::table('document_types')->where('name', 'Pension Form')->first();

        // Duplicate type name in the same category is refused.
        $this->actingAs($admin)->post('/admin/document-setup/types', ['document_category_id' => $cat->id, 'name' => 'Pension Form'])->assertSessionHasErrors('name');

        // Category with types cannot be deleted.
        $this->actingAs($admin)->delete("/admin/document-setup/categories/{$cat->uuid}")->assertSessionHas('error');

        // Type in use cannot be deleted, but can be renamed.
        DB::table('staff_documents')->insert($this->row([
            'staff_id' => $myId, 'document_type_id' => $type->id, 'file_path' => 'staff_documents/x.pdf',
            'original_name' => 'x.pdf', 'mime_type' => 'application/pdf', 'file_size' => 1, 'source' => 'registry', 'status' => 'approved',
        ]));
        $this->actingAs($admin)->delete("/admin/document-setup/types/{$type->uuid}")->assertSessionHas('error');
        $this->actingAs($admin)->put("/admin/document-setup/types/{$type->uuid}", ['document_category_id' => $cat->id, 'name' => 'Pension Application Form'])->assertRedirect();
        $this->assertDatabaseHas('document_types', ['id' => $type->id, 'name' => 'Pension Application Form']);

        // Admin staff record page links to the documents.
        $this->actingAs($admin)->get("/admin/staff/{$myId}")->assertOk()->assertSee('Documents');
    }
}
