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

    public function test_task_list_can_be_filtered_to_tasks_due_after_a_date(): void
    {
        $user = User::factory()->create();
        $user->tasks()->create([
            'title' => 'Task due before cutoff',
            'due_date' => '2026-11-14',
        ]);
        $user->tasks()->create([
            'title' => 'Task due on cutoff',
            'due_date' => '2026-11-15',
        ]);
        $user->tasks()->create([
            'title' => 'Task due after cutoff',
            'due_date' => '2026-11-16',
        ]);

        $response = $this->actingAs($user)->get(route('tasks.index', ['due_after' => '2026-11-15']));

        $response->assertOk()
            ->assertSee('id="due-after-filter"', false)
            ->assertViewHas('tasks', fn ($tasks): bool => $tasks->pluck('title')->all() === ['Task due after cutoff']);
    }

    public function test_task_list_combines_search_status_priority_and_due_date_filters(): void
    {
        $user = User::factory()->create();
        $user->tasks()->create([
            'title' => 'Prepare release checklist',
            'status' => 'in_progress',
            'priority' => 'high',
            'due_date' => '2026-11-15',
        ]);
        $user->tasks()->create([
            'title' => 'Prepare release notes',
            'status' => 'todo',
            'priority' => 'high',
            'due_date' => '2026-11-15',
        ]);
        $user->tasks()->create([
            'title' => 'Prepare release checklist',
            'status' => 'in_progress',
            'priority' => 'low',
            'due_date' => '2026-11-15',
        ]);

        $response = $this->actingAs($user)->get(route('tasks.index', [
            'search' => 'checklist',
            'status' => 'in_progress',
            'priority' => 'high',
            'due_date' => '2026-11-15',
        ]));

        $response->assertOk()
            ->assertViewHas('tasks', fn ($tasks): bool => $tasks->pluck('title')->all() === [
                'Prepare release checklist',
            ]);
    }

    public function test_task_list_can_sort_by_due_date_priority_and_status(): void
    {
        $user = User::factory()->create();
        $user->tasks()->create([
            'title' => 'Later low priority',
            'priority' => 'low',
            'status' => 'todo',
            'due_date' => '2026-11-20',
        ]);
        $user->tasks()->create([
            'title' => 'Soon high priority',
            'priority' => 'high',
            'status' => 'completed',
            'due_date' => '2026-11-10',
        ]);
        $user->tasks()->create([
            'title' => 'Middle medium priority',
            'priority' => 'medium',
            'status' => 'in_progress',
            'due_date' => '2026-11-15',
        ]);

        $this->actingAs($user)
            ->get(route('tasks.index', ['sort' => 'due_date', 'direction' => 'asc']))
            ->assertOk()
            ->assertViewHas('tasks', fn ($tasks): bool => $tasks->pluck('title')->all() === [
                'Soon high priority',
                'Middle medium priority',
                'Later low priority',
            ]);

        $this->get(route('tasks.index', ['sort' => 'priority', 'direction' => 'asc']))
            ->assertOk()
            ->assertViewHas('tasks', fn ($tasks): bool => $tasks->pluck('title')->all() === [
                'Soon high priority',
                'Later low priority',
                'Middle medium priority',
            ]);

        $this->get(route('tasks.index', ['sort' => 'status', 'direction' => 'asc']))
            ->assertOk()
            ->assertViewHas('tasks', fn ($tasks): bool => $tasks->pluck('title')->all() === [
                'Soon high priority',
                'Middle medium priority',
                'Later low priority',
            ]);
    }

    public function test_task_list_respects_the_requested_sort_direction_for_statuses(): void
    {
        $user = User::factory()->create();
        $user->tasks()->create([
            'title' => 'Later low priority',
            'priority' => 'low',
            'status' => 'todo',
            'due_date' => '2026-11-20',
        ]);
        $user->tasks()->create([
            'title' => 'Soon high priority',
            'priority' => 'high',
            'status' => 'completed',
            'due_date' => '2026-11-10',
        ]);
        $user->tasks()->create([
            'title' => 'Middle medium priority',
            'priority' => 'medium',
            'status' => 'in_progress',
            'due_date' => '2026-11-15',
        ]);

        $this->actingAs($user)
            ->get(route('tasks.index', ['sort' => 'status', 'direction' => 'desc']))
            ->assertOk()
            ->assertViewHas('tasks', fn ($tasks): bool => $tasks->pluck('title')->all() === [
                'Later low priority',
                'Middle medium priority',
                'Soon high priority',
            ]);
    }

    public function test_task_status_can_be_cycled_through_the_three_states(): void
    {
        $user = User::factory()->create();
        $task = $user->tasks()->create(['title' => 'Cycle this task', 'status' => 'todo']);

        $this->actingAs($user)
            ->post(route('tasks.cycle-status', $task))
            ->assertRedirect()
            ->assertSessionHas('success', 'Task status updated to In Progress.');
        $this->assertSame('in_progress', $task->refresh()->status);

        $this->post(route('tasks.cycle-status', $task))
            ->assertRedirect()
            ->assertSessionHas('success', 'Task status updated to Done.');
        $this->assertSame('completed', $task->refresh()->status);

        $this->post(route('tasks.cycle-status', $task))
            ->assertRedirect()
            ->assertSessionHas('success', 'Task status updated to Todo.');
        $this->assertSame('todo', $task->refresh()->status);

        $this->assertDatabaseHas('task_activities', [
            'user_id' => $user->id,
            'task_id' => $task->id,
            'action' => 'completed',
        ]);
    }

    public function test_user_cannot_cycle_another_users_task_status(): void
    {
        $user = User::factory()->create();
        $task = User::factory()->create()->tasks()->create(['title' => 'Private task']);

        $this->actingAs($user)
            ->post(route('tasks.cycle-status', $task))
            ->assertNotFound();

        $this->assertSame('todo', $task->refresh()->status);
    }

    public function test_task_list_displays_overdue_badges_and_delete_confirmation(): void
    {
        $this->travelTo('2026-11-03 12:00:00');

        $user = User::factory()->create();
        $user->tasks()->create([
            'title' => 'Past due task',
            'due_date' => '2026-11-02',
        ]);
        $user->tasks()->create([
            'title' => 'Completed past due task',
            'due_date' => '2026-11-01',
            'status' => 'completed',
        ]);
        $user->tasks()->create([
            'title' => 'Due today task',
            'due_date' => '2026-11-03',
        ]);

        $response = $this->actingAs($user)
            ->withSession(['success' => 'Task deleted successfully.'])
            ->get(route('tasks.index'));

        $response->assertOk()
            ->assertSee('Overdue')
            ->assertSee('data-confirm-delete', false)
            ->assertSee('id="delete-confirmation"', false)
            ->assertSee('Dismiss notification', false);
        $this->assertSame(1, substr_count($response->getContent(), 'class="overdue-badge"'));
    }

    public function test_task_list_rejects_an_unapproved_sort_column(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('tasks.index', [
            'sort' => 'title desc; DROP TABLE tasks',
        ]));

        $response->assertRedirect()
            ->assertSessionHasErrors('sort');
    }
}
