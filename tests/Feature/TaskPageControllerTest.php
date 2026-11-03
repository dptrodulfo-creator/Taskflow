<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskPageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_creation_page_renders_for_authenticated_users(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->get(route('tasks.create'));

        $response->assertOk()
            ->assertSee('Create a Task')
            ->assertSee('Task Title');
    }

    public function test_task_creation_saves_the_task_to_the_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'Prepare release notes',
            'description' => 'Summarize the changes.',
            'priority' => 'high',
            'status' => 'todo',
            'due_date' => '2026-11-12',
        ]);

        $response->assertRedirect(route('tasks.index'))
            ->assertSessionHas('success', 'Task created successfully.');

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Prepare release notes',
            'status' => 'todo',
        ]);

        $this->assertDatabaseHas('task_activities', [
            'user_id' => $user->id,
            'task_title' => 'Prepare release notes',
            'action' => 'created',
        ]);
    }

    public function test_user_cannot_edit_another_users_task(): void
    {
        $user = User::factory()->create();
        $otherUserTask = User::factory()->create()->tasks()->create([
            'title' => 'Private task',
        ]);

        $response = $this->actingAs($user)->get(route('tasks.edit', $otherUserTask));

        $response->assertNotFound();
    }

    public function test_quick_add_creates_a_task_and_returns_to_the_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'Call the supplier',
            'priority' => 'high',
            'status' => 'todo',
            'due_date' => '2026-11-15',
            'return_to' => 'dashboard',
        ]);

        $response->assertRedirect(route('dashboard'))
            ->assertSessionHas('success', 'Task created successfully.');

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Call the supplier',
        ]);
        $this->assertSame(
            '2026-11-15',
            $user->tasks()->where('title', 'Call the supplier')->firstOrFail()->due_date->toDateString(),
        );
        $this->assertDatabaseHas('task_activities', [
            'user_id' => $user->id,
            'task_title' => 'Call the supplier',
            'action' => 'created',
        ]);
    }

    public function test_task_status_changes_and_deletions_are_recorded_in_activity(): void
    {
        $user = User::factory()->create();
        $task = $user->tasks()->create(['title' => 'Prepare presentation']);

        $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => 'Prepare presentation',
            'priority' => 'medium',
            'status' => 'completed',
        ])->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('task_activities', [
            'user_id' => $user->id,
            'task_title' => 'Prepare presentation',
            'action' => 'completed',
        ]);

        $this->delete(route('tasks.destroy', $task))->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('task_activities', [
            'user_id' => $user->id,
            'task_id' => null,
            'task_title' => 'Prepare presentation',
            'action' => 'deleted',
        ]);
    }

    public function test_task_list_can_be_filtered_to_a_due_date(): void
    {
        $user = User::factory()->create();
        $user->tasks()->create([
            'title' => 'Task due today',
            'due_date' => '2026-11-15',
        ]);
        $user->tasks()->create([
            'title' => 'Task due later',
            'due_date' => '2026-11-16',
        ]);

        $response = $this->actingAs($user)->get(route('tasks.index', ['due_date' => '2026-11-15']));

        $response->assertOk()
            ->assertViewHas('tasks', fn ($tasks): bool => $tasks->pluck('title')->all() === ['Task due today']);
    }
}
