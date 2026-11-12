<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminAccountSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_root_redirects_to_the_login_screen(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
        $this->get('/admin/')->assertRedirect(route('admin.login'));
    }

    public function test_admin_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertOk()->assertSee('Administrator sign in');
    }

    public function test_admin_can_sign_in_and_reaches_the_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);
        $this->travelTo('2026-11-06 12:00:00');

        $response = $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $this->assertSame('2026-11-06 12:00:00', $admin->refresh()->last_login_at->toDateTimeString());
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_can_sign_in_when_email_has_different_casing_and_whitespace(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);

        $response = $this->post(route('admin.login.store'), [
            'email' => '  ADMIN@EXAMPLE.COM  ',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_regular_user_cannot_sign_in_through_the_admin_login(): void
    {
        $user = User::factory()->create();

        $response = $this->from(route('admin.login'))->post(route('admin.login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('admin.login'))->assertSessionHasErrors('email');
    }

    public function test_guest_is_redirected_to_the_admin_login_for_protected_pages(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_authenticated_user_without_admin_access_is_forbidden(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_admin_logout_invalidates_the_authenticated_session(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post(route('admin.logout'));

        $this->assertGuest();
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_seeder_creates_an_admin_with_a_hashed_password(): void
    {
        config([
            'admin.name' => 'Seeded Admin',
            'admin.email' => ' SEEDED-ADMIN@EXAMPLE.COM ',
            'admin.password' => 'a-strong-test-password',
        ]);

        $this->seed(AdminAccountSeeder::class);

        $admin = User::query()->where('email', 'seeded-admin@example.com')->firstOrFail();

        $this->assertTrue($admin->is_admin);
        $this->assertTrue(Hash::check('a-strong-test-password', $admin->password));
    }

    public function test_admin_seeder_explains_missing_credentials_in_non_interactive_runs(): void
    {
        config([
            'admin.email' => null,
            'admin.password' => null,
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Admin credentials are not configured.');

        $this->artisan('db:seed', [
            '--class' => 'Database\\Seeders\\AdminAccountSeeder',
            '--no-interaction' => true,
        ]);
    }
}
