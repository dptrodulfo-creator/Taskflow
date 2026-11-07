<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

class AdminAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = config('admin.email');
        $password = config('admin.password');

        if (blank($email) || blank($password)) {
            if ($this->command === null || $this->command->option('no-interaction')) {
                throw new RuntimeException(
                    'Admin credentials are not configured. Set ADMIN_EMAIL and ADMIN_PASSWORD in .env, or run this seeder in an interactive terminal to enter them securely.',
                );
            }

            $email = filled($email) ? $email : $this->command->ask('Admin email');
            $password = filled($password) ? $password : $this->command->secret('Admin password (minimum 12 characters)');
        }

        $admin = Validator::make([
            'name' => config('admin.name'),
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:12'],
        ])->validate();

        $user = User::query()->firstOrNew(['email' => $admin['email']]);
        $user->forceFill([
            'name' => $admin['name'],
            'password' => $admin['password'],
            'is_admin' => true,
        ])->save();
    }
}
