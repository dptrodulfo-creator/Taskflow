<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class VerifyUserEmailCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_verifies_only_the_confirmed_regular_user_account_in_local_environment(): void
    {
        $this->app['env'] = 'local';
        $user = User::factory()->unverified()->create(['email' => 'user@example.com']);
        Event::fake();

        $this->artisan('user:verify-email', ['email' => '  USER@EXAMPLE.COM  '])
            ->expectsConfirmation('Verify the email address user@example.com for this account?', 'yes')
            ->expectsOutputToContain('Email verified.')
            ->assertExitCode(0);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        Event::assertDispatched(Verified::class, fn (Verified $event): bool => $event->user->is($user));
    }

    public function test_it_does_not_verify_an_admin_account(): void
    {
        $this->app['env'] = 'local';
        $admin = User::factory()->unverified()->create([
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);

        $this->artisan('user:verify-email', ['email' => $admin->email])
            ->assertExitCode(1);

        $this->assertFalse($admin->fresh()->hasVerifiedEmail());
    }

    public function test_it_does_not_change_the_account_when_confirmation_is_declined(): void
    {
        $this->app['env'] = 'local';
        $user = User::factory()->unverified()->create();

        $this->artisan('user:verify-email', ['email' => $user->email])
            ->expectsConfirmation("Verify the email address {$user->email} for this account?", 'no')
            ->expectsOutputToContain('No changes were made.')
            ->assertExitCode(0);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_it_refuses_to_run_outside_the_local_environment(): void
    {
        $user = User::factory()->unverified()->create();
        $this->app['env'] = 'production';

        $this->artisan('user:verify-email', ['email' => $user->email])
            ->assertExitCode(1);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
