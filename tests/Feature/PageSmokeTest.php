<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/** Every parameter-less admin page and the public auth pages render without a server error. */
class PageSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_pages_render(): void
    {
        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);
        $admin = User::forceCreate(['fileno' => 'ADM1', 'surname' => 'Admin', 'firstname' => 'A', 'email' => 'admin@funaab.test', 'password' => bcrypt('x'), 'role' => 'admin']);

        $failures = [];
        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();
            if (!in_array('GET', $route->methods(), true) || !str_starts_with($uri, 'admin/') || str_contains($uri, '{')) continue;
            $status = $this->actingAs($admin)->get('/' . $uri)->getStatusCode();
            if ($status >= 500) $failures[] = "{$uri} → {$status}";
        }
        $this->assertSame([], $failures);

        $identityId = DB::table('identities')->insertGetId(['fileno' => 'SP1', 'title' => 'Mr.', 'names' => 'Test, Person', 'created_at' => now(), 'updated_at' => now()]);
        $this->actingAs($admin)->get("/admin/identity/{$identityId}/edit")->assertOk();
        $this->actingAs($admin)->put("/admin/identity/{$identityId}", ['names' => 'Test, Changed', 'remark' => 'typo'])->assertRedirect();
        $this->assertDatabaseHas('identity_update_histories', ['identity_id' => $identityId, 'old' => 'Test, Person', 'new' => 'Test, Changed']);
    }

    public function test_public_pages_render_and_reset_link_does_not_reveal_accounts(): void
    {
        $this->withoutVite();
        foreach (['/', '/admin', '/identity', '/sign-up', '/forgot-password'] as $uri) {
            $this->get($uri)->assertOk();
        }
        $this->post('/forgot-password', ['login' => 'nobody@nowhere.test'])->assertSessionHas('success');
    }

    public function test_staff_cannot_open_admin_area(): void
    {
        $this->withoutVite();
        $staff = User::forceCreate(['fileno' => 'SP9', 'surname' => 'S', 'firstname' => 'S', 'email' => 's@funaab.test', 'password' => bcrypt('x'), 'role' => 'staff']);
        $this->actingAs($staff)->get('/admin/dashboard')->assertForbidden();
    }
}
