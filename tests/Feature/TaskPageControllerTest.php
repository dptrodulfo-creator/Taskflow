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
}
