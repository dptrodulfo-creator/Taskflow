<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_requires_an_authenticated_session(): void
    {
        $this->getJson('/api/tasks')->assertUnauthorized();
    }

    public function test_user_can_list_only_their_own_tasks(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $user->tasks()->create(['title' => 'My task']);
        $otherUser->tasks()->create(['title' => 'Private task']);

        $this->actingAs($user)
            ->getJson('/api/tasks')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'My task');
    }

    public function test_user_can_create_a_task(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/tasks', [
                'title' => 'Prepare release',
                'description' => 'Review the launch checklist.',
                'priority' => 'high',
                'status' => 'in_progress',
                'due_date' => '2026-12-01',
                'user_id' => $otherUser->id,
            ])
            ->assertCreated()
            ->assertJsonPath('data.title', 'Prepare release')
            ->assertJsonPath('data.due_date', '2026-12-01');

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Prepare release',
            'priority' => 'high',
            'status' => 'in_progress',
        ]);
        $this->assertDatabaseMissing('tasks', [
            'user_id' => $otherUser->id,
            'title' => 'Prepare release',
        ]);
        $this->assertDatabaseHas('task_activities', [
            'user_id' => $user->id,
            'task_title' => 'Prepare release',
            'action' => 'created',
        ]);
    }

    public function test_invalid_task_data_returns_validation_errors(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson('/api/tasks', [
                'priority' => 'urgent',
                'due_date' => 'next week',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'priority', 'due_date'])
            ->assertJsonPath('errors.title.0', 'The title field is required.')
            ->assertJsonPath('errors.priority.0', 'The selected priority is invalid.')
            ->assertJsonPath('errors.due_date.0', 'The due date field must match the format Y-m-d.');
    }

    public function test_user_can_retrieve_their_task(): void
    {
        $user = User::factory()->create();
        $task = $user->tasks()->create(['title' => 'Review proposal']);

        $this->actingAs($user)
            ->getJson("/api/tasks/{$task->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $task->id)
            ->assertJsonPath('data.title', 'Review proposal');
    }

    public function test_user_can_update_their_task(): void
    {
        $user = User::factory()->create();
        $task = $user->tasks()->create(['title' => 'Draft plan']);

        $this->actingAs($user)
            ->patchJson("/api/tasks/{$task->id}", [
                'title' => 'Finalize plan',
                'status' => 'completed',
            ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Finalize plan')
            ->assertJsonPath('data.status', 'completed');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'user_id' => $user->id,
            'title' => 'Finalize plan',
            'status' => 'completed',
        ]);
        $this->assertDatabaseHas('task_activities', [
            'user_id' => $user->id,
            'task_title' => 'Finalize plan',
            'action' => 'completed',
        ]);
    }

    public function test_user_can_delete_their_task(): void
    {
        $user = User::factory()->create();
        $task = $user->tasks()->create(['title' => 'Obsolete task']);

        $this->actingAs($user)
            ->deleteJson("/api/tasks/{$task->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_cannot_access_another_users_task(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $task = $owner->tasks()->create(['title' => 'Owner private task']);

        $this->actingAs($user);

        $this->getJson("/api/tasks/{$task->id}")->assertNotFound();
        $this->putJson("/api/tasks/{$task->id}", ['title' => 'Changed'])->assertNotFound();
        $this->deleteJson("/api/tasks/{$task->id}")->assertNotFound();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'user_id' => $owner->id,
            'title' => 'Owner private task',
        ]);
    }
}
