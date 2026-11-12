<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

#[Signature('user:reset-password {email : The email address of the account to reset}')]
#[Description('Reset a user password using private terminal prompts')]
class ResetUserPassword extends Command
{
    public function handle(): int
    {
        $email = Str::lower(trim((string) $this->argument('email')));
        $emailValidator = Validator::make(['email' => $email], ['email' => ['required', 'email']]);

        if ($emailValidator->fails()) {
            $this->error('Enter a valid account email address.');

            return self::FAILURE;
        }

        $user = User::query()->where('email', $email)->first();

        if ($user === null) {
            $this->error('No user account was found for that email address.');

            return self::FAILURE;
        }

        if (! $this->input->isInteractive()) {
            $this->error('This command requires an interactive terminal.');

            return self::FAILURE;
        }

        $password = $this->secret('New password (minimum 12 characters)');
        $confirmation = $this->secret('Confirm new password');
        $passwordValidator = Validator::make(
            ['password' => $password, 'password_confirmation' => $confirmation],
            ['password' => ['required', 'string', 'min:12', 'confirmed']],
        );

        if ($passwordValidator->fails()) {
            $this->error('The password must be at least 12 characters and both entries must match.');

            return self::FAILURE;
        }

        $user->forceFill([
            'password' => $password,
            'remember_token' => Str::random(60),
        ])->save();

        if (config('session.driver') === 'database') {
            DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', $user->getKey())
                ->delete();
        }

        $this->info('Password reset successfully. You can now sign in with the new password.');

        return self::SUCCESS;
    }
}
