<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_global_counts_and_recent_activity(): void
    {
        $this->travelTo('2026-11-06 12:00:00');
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['name' => 'Task Owner', 'email' => 'owner@example.com']);
        $user->tasks()->create([
            'title' => 'Prepare release',
            'status' => 'in_progress',
            'due_date' => '2026-11-05',
        ]);
        $user->tasks()->create([
            'title' => 'Finish release',
            'status' => 'completed',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee('Task Owner')
            ->assertSee('Prepare release')
            ->assertSee('Recent activity')
            ->assertSee('Overdue tasks')
            ->assertSee('Task status');
        $this->assertDatabaseCount('users', 2);
        $response->assertViewHas('stats', [
            'users' => 2,
            'admins' => 1,
            'tasks' => 2,
            'pending' => 0,
            'in_progress' => 1,
            'completed' => 1,
            'overdue' => 1,
        ]);
    }

    public function test_admin_reports_summarize_all_users_and_tasks(): void
    {
        $this->travelTo('2026-11-06 12:00:00');
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['created_at' => now()->subMonth()]);
        $user->tasks()->create(['title' => 'Completed', 'status' => 'completed']);
        $user->tasks()->create(['title' => 'In progress', 'status' => 'in_progress']);

        $response = $this->actingAs($admin)->get(route('admin.reports.index'));

        $response->assertOk()
            ->assertSee('Platform reports')
            ->assertViewHas('stats', [
                'users' => 2,
                'new_users' => 1,
                'tasks' => 2,
                'new_tasks' => 2,
                'completion_rate' => 50,
            ])
            ->assertViewHas('taskStatusCounts', [
                'todo' => 0,
                'in_progress' => 1,
                'completed' => 1,
            ]);
    }

    public function test_admin_can_review_all_users_tasks_and_task_activity(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $user = User::factory()->create(['name' => 'Monitored User', 'email' => 'monitor@example.com']);
        $user->tasks()->create(['title' => 'Visible task', 'status' => 'todo']);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Monitored User')
            ->assertSee('Make admin');

        $this->actingAs($admin)
            ->get(route('admin.tasks.index', ['search' => 'Visible']))
            ->assertOk()
            ->assertSee('Visible task')
            ->assertSee('monitor@example.com');

        $this->actingAs($admin)
            ->get(route('admin.activities.index'))
            ->assertOk()
            ->assertSee('Visible task')
            ->assertSee('Monitored User');
    }
}
