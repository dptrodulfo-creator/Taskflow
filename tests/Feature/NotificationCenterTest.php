<?php

namespace Tests\Feature;

use App\Models\TaskActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('unread notifications');

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

    public function test_navigation_shows_unread_notification_count_until_the_center_is_visited(): void
    {
        $this->travelTo('2026-11-03 12:00:00');

        $user = User::factory()->create([
            'notification_center_read_at' => now()->subDay(),
        ]);
        $user->tasks()->createMany([
            ['title' => 'Upcoming reminder', 'due_date' => '2026-11-04'],
            ['title' => 'Overdue reminder', 'due_date' => '2026-11-02'],
        ]);
        DB::table('tasks')->where('user_id', $user->id)->update([
            'created_at' => now()->subHour(),
            'updated_at' => now()->subHour(),
        ]);
        TaskActivity::create([
            'user_id' => $user->id,
            'task_id' => null,
            'task_title' => 'Recently completed task',
            'action' => 'completed',
        ]);
        DB::table('task_activities')->where('user_id', $user->id)->update([
            'created_at' => now()->subHour(),
            'updated_at' => now()->subHour(),
        ]);
        $otherUser = User::factory()->create();
        $otherUser->tasks()->create([
            'title' => 'Another user reminder',
            'due_date' => '2026-11-04',
        ]);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('aria-label="3 unread notifications"', false);

        $this->get(route('notifications.index'))
            ->assertOk()
            ->assertDontSee('aria-label="3 unread notifications"', false);

        $this->assertNotNull($user->fresh()->notification_center_read_at);
    }

    public function test_sidebar_and_global_search_controls_are_rendered_in_the_app_layout(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('aria-label="Close navigation menu"', false)
            ->assertSee('aria-label="Collapse sidebar"', false)
            ->assertSee('data-sidebar-toggle', false)
            ->assertSee('aria-keyshortcuts="Control+K Meta+K"', false)
            ->assertSee('id="global-search-dialog"', false)
            ->assertSee('name="search"', false)
            ->assertSee('data-tooltip="Notifications"', false);
    }
}
