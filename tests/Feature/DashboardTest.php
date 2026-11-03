<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_user_scoped_task_stats_and_recent_tasks(): void
    {
        $user = User::factory()->create();
        $user->tasks()->create([
            'title' => 'Prepare launch plan',
            'status' => 'in_progress',
            'priority' => 'high',
            'due_date' => '2026-11-08',
            'created_at' => now(),
        ]);
        $user->tasks()->create([
            'title' => 'Review design',
            'status' => 'todo',
            'priority' => 'medium',
            'created_at' => now()->subDay(),
        ]);
        $user->tasks()->create([
            'title' => 'Send final report',
            'status' => 'completed',
            'priority' => 'low',
            'created_at' => now()->subDays(2),
        ]);
        User::factory()->create()->tasks()->create([
            'title' => 'Another user private task',
            'status' => 'todo',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertViewHas('stats', [
                'total' => 3,
                'pending' => 1,
                'in_progress' => 1,
                'completed' => 1,
            ])
            ->assertViewHas('progress', 33)
            ->assertSee('Prepare launch plan')
            ->assertSee('In Progress')
            ->assertSee('Nov 08, 2026')
            ->assertSee('Pending Tasks')
            ->assertSee('Add New Task')
            ->assertSee(route('tasks.create'))
            ->assertDontSee('Another user private task');
    }

    public function test_dashboard_shows_an_empty_state_when_the_user_has_no_tasks(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk()
            ->assertViewHas('stats', [
                'total' => 0,
                'pending' => 0,
                'in_progress' => 0,
                'completed' => 0,
            ])
            ->assertViewHas('progress', 0)
            ->assertSee('No tasks yet')
            ->assertSee('Create your first task');
    }

    public function test_dashboard_redirects_guests_to_login(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }
}
