<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

#[Signature('user:verify-email {email : The email address of the user account to verify}')]
#[Description('Verify a user email for a local development account')]
class VerifyUserEmail extends Command
{
    public function handle(): int
    {
        if (! app()->environment('local')) {
            $this->error('This recovery command is available only in the local environment.');

            return self::FAILURE;
        }

        if (! $this->input->isInteractive()) {
            $this->error('This command requires an interactive terminal.');

            return self::FAILURE;
        }

        $email = Str::lower(trim((string) $this->argument('email')));
        $emailValidator = Validator::make(['email' => $email], ['email' => ['required', 'email']]);

        if ($emailValidator->fails()) {
            $this->error('Enter a valid account email address.');

            return self::FAILURE;
        }

        $user = User::query()
            ->where('email', $email)
            ->where('is_admin', false)
            ->first();

        if ($user === null) {
            $this->error('No regular user account was found for that email address.');

            return self::FAILURE;
        }

        if ($user->hasVerifiedEmail()) {
            $this->info('This account email is already verified.');

            return self::SUCCESS;
        }

        if (! $this->confirm("Verify the email address {$user->email} for this account?")) {
            $this->info('No changes were made.');

            return self::SUCCESS;
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        $this->info('Email verified. You can now open the user dashboard.');

        return self::SUCCESS;
    }
}
