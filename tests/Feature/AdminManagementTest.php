<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_grant_administrator_access_to_another_user(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($admin)->patch(route('admin.users.role.update', $user), [
            'is_admin' => '1',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_admin' => true,
        ]);
        $response->assertRedirect();
    }

    public function test_sole_administrator_cannot_remove_their_own_access(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)
            ->from(route('admin.admins.index'))
            ->patch(route('admin.users.role.update', $admin), ['is_admin' => '0']);

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'is_admin' => true]);
        $response->assertRedirect(route('admin.admins.index'))->assertSessionHasErrors('is_admin');
    }

    public function test_admin_can_remove_another_administrators_access_when_more_remain(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $anotherAdmin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)
            ->patch(route('admin.users.role.update', $anotherAdmin), ['is_admin' => '0']);

        $this->assertDatabaseHas('users', ['id' => $anotherAdmin->id, 'is_admin' => false]);
        $response->assertRedirect();
    }

    public function test_role_change_race_cannot_remove_the_remaining_administrator(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $lastAdmin = User::factory()->create(['is_admin' => true]);
        DB::table('users')->where('id', $admin->id)->update(['is_admin' => false]);

        $response = $this->actingAs($admin)
            ->from(route('admin.admins.index'))
            ->patch(route('admin.users.role.update', $lastAdmin), ['is_admin' => '0']);

        $this->assertDatabaseHas('users', ['id' => $lastAdmin->id, 'is_admin' => true]);
        $response->assertRedirect(route('admin.admins.index'))->assertSessionHasErrors('is_admin');
    }

    public function test_regular_user_cannot_change_account_roles(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create();

        $response = $this->actingAs($user)->patch(route('admin.users.role.update', $target), [
            'is_admin' => '1',
        ]);

        $this->assertDatabaseHas('users', ['id' => $target->id, 'is_admin' => false]);
        $response->assertForbidden();
    }

    public function test_invalid_role_value_does_not_change_account_access(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($admin)
            ->from(route('admin.users.index'))
            ->patch(route('admin.users.role.update', $user), ['is_admin' => 'superadmin']);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_admin' => false]);
        $response->assertRedirect(route('admin.users.index'))->assertSessionHasErrors('is_admin');
    }
}
