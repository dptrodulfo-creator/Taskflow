<?php

namespace Tests\Feature;

use App\Models\TaskActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_center_shows_upcoming_overdue_and_completion_notifications_for_the_user(): void
    {
        $this->travelTo('2026-11-03 12:00:00');

        $user = User::factory()->create();
        $user->tasks()->createMany([
            ['title' => 'Due today', 'due_date' => '2026-11-03'],
            ['title' => 'Due in seven days', 'due_date' => '2026-11-10'],
            ['title' => 'Due outside reminder window', 'due_date' => '2026-11-11'],
            ['title' => 'Past due', 'due_date' => '2026-11-02'],
            ['title' => 'Completed upcoming task', 'due_date' => '2026-11-04', 'status' => 'completed'],
            ['title' => 'Completed overdue task', 'due_date' => '2026-11-01', 'status' => 'completed'],
            ['title' => 'Finish weekly report', 'status' => 'todo'],
        ]);
        $completionTask = $user->tasks()->where('title', 'Finish weekly report')->firstOrFail();
        $otherUser = User::factory()->create();
        $otherUser->tasks()->create([
            'title' => 'Another user private deadline',
            'due_date' => '2026-11-04',
        ]);
        TaskActivity::create([
            'user_id' => $otherUser->id,
            'task_id' => null,
            'task_title' => 'Another user private completion',
            'action' => 'completed',
        ]);

        $this->actingAs($user)->put(route('tasks.update', $completionTask), [
            'title' => 'Finish weekly report',
            'priority' => 'medium',
            'status' => 'completed',
        ])->assertRedirect(route('tasks.index'));

        $response = $this->get(route('notifications.index'));

        $response->assertOk()
            ->assertViewHas('upcomingTasks');
        $this->assertSame([
            'Due today',
            'Due in seven days',
        ], $response->viewData('upcomingTasks')->pluck('title')->all());
        $response
            ->assertViewHas('overdueTasks', fn ($tasks): bool => $tasks->pluck('title')->all() === [
                'Past due',
            ])
            ->assertViewHas('completionNotifications', fn ($notifications): bool => $notifications->pluck('task_title')->all() === [
                'Finish weekly report',
            ])
            ->assertSee('Upcoming deadlines')
            ->assertSee('Overdue tasks')
            ->assertSee('Finish weekly report completed')
            ->assertDontSee('Due outside reminder window')
            ->assertDontSee('Completed upcoming task')
            ->assertDontSee('Completed overdue task')
            ->assertDontSee('Another user private');
    }

    public function test_guest_is_redirected_to_login_from_the_notification_center(): void
    {
        $response = $this->get(route('notifications.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_notification_preferences_hide_their_matching_notification_categories(): void
    {
        $this->travelTo('2026-11-03 12:00:00');

        $user = User::factory()->create([
            'notify_upcoming_deadlines' => false,
            'notify_overdue_tasks' => false,
            'notify_task_completions' => false,
        ]);
        $user->tasks()->createMany([
            ['title' => 'Upcoming reminder task', 'due_date' => '2026-11-04'],
            ['title' => 'Overdue alert task', 'due_date' => '2026-11-02'],
            ['title' => 'Completed notification task', 'status' => 'completed'],
        ]);

        $this->actingAs($user)->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Upcoming deadline reminders are turned off in Settings.')
            ->assertSee('Overdue task alerts are turned off in Settings.')
            ->assertSee('Task completion updates are turned off in Settings.')
            ->assertDontSee('Upcoming reminder task')
            ->assertDontSee('Overdue alert task')
            ->assertDontSee('Completed notification task completed');
    }

    public function test_notification_center_shows_empty_states_when_the_user_has_no_notifications(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->get(route('notifications.index'));

        $response->assertOk()
            ->assertSee('No deadlines in the next 7 days.')
            ->assertSee('No overdue tasks.')
            ->assertSee('Completed tasks will show up here.');
    }
}
