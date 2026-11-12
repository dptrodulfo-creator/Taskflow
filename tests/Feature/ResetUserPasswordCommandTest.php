<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ResetUserPasswordCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_resets_password_and_revokes_only_the_target_users_sessions(): void
    {
        config(['session.driver' => 'database']);
        $user = User::factory()->create(['remember_token' => 'old-remember-token']);
        $otherUser = User::factory()->create();
        DB::table('sessions')->insert([
            [
                'id' => 'target-user-session',
                'user_id' => $user->id,
                'payload' => '',
                'last_activity' => now()->timestamp,
            ],
            [
                'id' => 'other-user-session',
                'user_id' => $otherUser->id,
                'payload' => '',
                'last_activity' => now()->timestamp,
            ],
        ]);

        $this->artisan('user:reset-password', ['email' => '  '.strtoupper($user->email)])
            ->expectsQuestion('New password (minimum 12 characters)', 'new-secure-password-123')
            ->expectsQuestion('Confirm new password', 'new-secure-password-123')
            ->expectsOutputToContain('Password reset successfully.')
            ->assertExitCode(0);

        $this->assertTrue(Hash::check('new-secure-password-123', $user->fresh()->password));
        $this->assertNotSame('old-remember-token', $user->fresh()->remember_token);
        $this->assertDatabaseMissing('sessions', ['id' => 'target-user-session']);
        $this->assertDatabaseHas('sessions', ['id' => 'other-user-session', 'user_id' => $otherUser->id]);
    }

    public function test_it_does_not_change_the_account_when_password_confirmation_does_not_match(): void
    {
        config(['session.driver' => 'database']);
        $user = User::factory()->create(['remember_token' => 'old-remember-token']);
        DB::table('sessions')->insert([
            'id' => 'target-user-session',
            'user_id' => $user->id,
            'payload' => '',
            'last_activity' => now()->timestamp,
        ]);

        $this->artisan('user:reset-password', ['email' => $user->email])
            ->expectsQuestion('New password (minimum 12 characters)', 'new-secure-password-123')
            ->expectsQuestion('Confirm new password', 'different-password-123')
            ->expectsOutputToContain('both entries must match.')
            ->assertExitCode(1);

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
        $this->assertSame('old-remember-token', $user->fresh()->remember_token);
        $this->assertDatabaseHas('sessions', ['id' => 'target-user-session']);
    }

    public function test_it_rejects_passwords_shorter_than_twelve_characters(): void
    {
        $user = User::factory()->create();

        $this->artisan('user:reset-password', ['email' => $user->email])
            ->expectsQuestion('New password (minimum 12 characters)', 'too-short')
            ->expectsQuestion('Confirm new password', 'too-short')
            ->expectsOutputToContain('at least 12 characters')
            ->assertExitCode(1);

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_it_fails_when_the_account_does_not_exist(): void
    {
        $this->artisan('user:reset-password', ['email' => 'missing@example.com'])
            ->assertExitCode(1);
    }
}
