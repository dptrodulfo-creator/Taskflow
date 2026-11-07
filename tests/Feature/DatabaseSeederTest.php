<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_seeding_does_not_create_a_predictable_demo_account(): void
    {
        config([
            'admin.email' => null,
            'admin.password' => null,
        ]);

        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }

    public function test_default_seeding_creates_the_configured_admin_account(): void
    {
        config([
            'admin.email' => 'admin@example.test',
            'admin.password' => 'long-enough-admin-password',
        ]);

        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.test',
            'is_admin' => true,
        ]);
    }
}
