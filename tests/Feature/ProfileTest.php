<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_profile_information_can_be_updated_with_a_username_and_profile_picture(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => 'Updated User',
            'username' => 'updated_user',
            'email' => $user->email,
            'avatar' => UploadedFile::fake()->createWithContent(
                'profile.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/l2cAAAAASUVORK5CYII='),
            ),
        ]);

        $response->assertRedirect('/profile')
            ->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertSame('updated_user', $user->username);
        $this->assertNotNull($user->profile_photo_path);
        Storage::disk('public')->assertExists($user->profile_photo_path);
    }

    public function test_replacing_a_profile_picture_removes_the_previous_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('profile-photos/previous.png', 'previous picture');
        $user = User::factory()->create(['profile_photo_path' => 'profile-photos/previous.png']);

        $this->actingAs($user)->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'username' => '',
            'avatar' => UploadedFile::fake()->createWithContent(
                'profile.png',
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/l2cAAAAASUVORK5CYII='),
            ),
        ])->assertRedirect('/profile');

        Storage::disk('public')->assertMissing('profile-photos/previous.png');
        Storage::disk('public')->assertExists($user->refresh()->profile_photo_path);
    }

    public function test_non_image_profile_picture_is_rejected(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)->from('/profile')->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'username' => '',
            'avatar' => UploadedFile::fake()->createWithContent('avatar.svg', '<svg></svg>'),
        ])->assertRedirect('/profile')
            ->assertSessionHasErrors('avatar');

        $this->assertNull($user->refresh()->profile_photo_path);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_username_must_be_unique_and_valid(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create(['username' => 'taken_name']);

        $response = $this->actingAs($user)->from('/profile')->patch('/profile', [
            'name' => $user->name,
            'username' => $otherUser->username,
            'email' => $user->email,
        ]);

        $response->assertRedirect('/profile')
            ->assertSessionHasErrors('username');
        $this->assertNull($user->refresh()->username);
    }

    public function test_account_preferences_are_saved_and_applied_to_settings(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch(route('profile.preferences.update'), [
            'theme' => 'dark',
            'locale' => 'fil',
            'timezone' => 'Asia/Manila',
            'notify_upcoming_deadlines' => '0',
            'notify_overdue_tasks' => '1',
            'notify_task_completions' => '0',
        ])->assertRedirect('/profile')
            ->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertSame('dark', $user->theme);
        $this->assertSame('fil', $user->locale);
        $this->assertSame('Asia/Manila', $user->timezone);
        $this->assertFalse($user->notify_upcoming_deadlines);
        $this->assertTrue($user->notify_overdue_tasks);
        $this->assertFalse($user->notify_task_completions);

        $this->get('/profile')
            ->assertOk()
            ->assertSee('lang="fil"', false)
            ->assertSee('data-theme="dark"', false)
            ->assertSee('Mga Setting');
    }

    public function test_account_preferences_reject_unsupported_timezone_and_theme(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->from('/profile')->patch(route('profile.preferences.update'), [
            'theme' => 'neon',
            'locale' => 'en',
            'timezone' => 'Mars/Olympus',
            'notify_upcoming_deadlines' => '1',
            'notify_overdue_tasks' => '1',
            'notify_task_completions' => '1',
        ]);

        $response->assertRedirect('/profile')
            ->assertSessionHasErrors(['theme', 'timezone']);
        $this->assertSame('light', $user->refresh()->theme);
    }

    public function test_profile_page_shows_account_creation_login_and_security_controls(): void
    {
        $user = User::factory()->create(['last_login_at' => now()]);

        $this->actingAs($user)->get('/profile')
            ->assertOk()
            ->assertSee('Account created')
            ->assertSee('Last sign in')
            ->assertSee('Active sessions')
            ->assertSee('data-password-strength-input', false)
            ->assertSee('data-password-toggle', false)
            ->assertSee('Sign out everywhere')
            ->assertSee('data-delete-account-dialog', false);
    }

    public function test_profile_page_lists_only_the_users_active_sessions(): void
    {
        config(['session.driver' => 'database']);

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        DB::table('sessions')->insert([
            [
                'id' => 'user-active-session',
                'user_id' => $user->id,
                'ip_address' => '192.0.2.1',
                'user_agent' => 'TaskFlow Browser',
                'payload' => '',
                'last_activity' => now()->timestamp,
            ],
            [
                'id' => 'other-user-session',
                'user_id' => $otherUser->id,
                'ip_address' => '192.0.2.2',
                'user_agent' => 'Private Browser',
                'payload' => '',
                'last_activity' => now()->timestamp,
            ],
            [
                'id' => 'expired-session',
                'user_id' => $user->id,
                'ip_address' => '192.0.2.3',
                'user_agent' => 'Expired Browser',
                'payload' => '',
                'last_activity' => now()->subMinutes(config('session.lifetime') + 1)->timestamp,
            ],
        ]);

        $this->actingAs($user)->get('/profile')
            ->assertOk()
            ->assertSee('TaskFlow Browser')
            ->assertDontSee('Private Browser')
            ->assertDontSee('Expired Browser');
    }

    public function test_user_can_revoke_another_owned_session_but_not_another_users_session(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        DB::table('sessions')->insert([
            [
                'id' => 'user-session-to-revoke',
                'user_id' => $user->id,
                'payload' => '',
                'last_activity' => now()->timestamp,
            ],
            [
                'id' => 'private-session',
                'user_id' => $otherUser->id,
                'payload' => '',
                'last_activity' => now()->timestamp,
            ],
        ]);

        $this->actingAs($user)
            ->delete(route('profile.sessions.destroy', 'user-session-to-revoke'))
            ->assertRedirect('/profile')
            ->assertSessionHas('status', 'session-revoked');
        $this->assertDatabaseMissing('sessions', ['id' => 'user-session-to-revoke']);

        $this->delete(route('profile.sessions.destroy', 'private-session'))
            ->assertNotFound();
        $this->assertDatabaseHas('sessions', ['id' => 'private-session', 'user_id' => $otherUser->id]);
    }

    public function test_user_can_sign_out_from_all_devices_after_confirming_password(): void
    {
        $user = User::factory()->create(['remember_token' => 'old-remember-token']);
        $otherUser = User::factory()->create();
        DB::table('sessions')->insert([
            [
                'id' => 'first-user-session',
                'user_id' => $user->id,
                'payload' => '',
                'last_activity' => now()->timestamp,
            ],
            [
                'id' => 'second-user-session',
                'user_id' => $user->id,
                'payload' => '',
                'last_activity' => now()->timestamp,
            ],
            [
                'id' => 'unrelated-session',
                'user_id' => $otherUser->id,
                'payload' => '',
                'last_activity' => now()->timestamp,
            ],
        ]);

        $response = $this->actingAs($user)->delete(route('profile.sessions.destroy-all'), [
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        $this->assertGuest();
        $this->assertDatabaseMissing('sessions', ['id' => 'first-user-session']);
        $this->assertDatabaseMissing('sessions', ['id' => 'second-user-session']);
        $this->assertDatabaseHas('sessions', ['id' => 'unrelated-session', 'user_id' => $otherUser->id]);
        $this->assertNotSame('old-remember-token', $user->refresh()->remember_token);
    }

    public function test_signing_out_from_all_devices_requires_the_current_password(): void
    {
        $user = User::factory()->create();
        DB::table('sessions')->insert([
            'id' => 'still-active-session',
            'user_id' => $user->id,
            'payload' => '',
            'last_activity' => now()->timestamp,
        ]);

        $this->actingAs($user)->from('/profile')->delete(route('profile.sessions.destroy-all'), [
            'password' => 'incorrect',
        ])->assertRedirect('/profile')
            ->assertSessionHasErrorsIn('logoutAllDevices', 'password');

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('sessions', ['id' => 'still-active-session']);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();
        DB::table('sessions')->insert([
            'id' => 'deleted-account-session',
            'user_id' => $user->id,
            'payload' => '',
            'last_activity' => now()->timestamp,
        ]);

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
        $this->assertDatabaseMissing('sessions', ['id' => 'deleted-account-session']);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
