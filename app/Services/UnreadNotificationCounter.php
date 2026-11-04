<?php

namespace App\Services;

use App\Models\User;

class UnreadNotificationCounter
{
    public function for(User $user): int
    {
        $readAt = $user->notification_center_read_at;
        $unreadCount = 0;
        $today = today();

        if ($user->notify_upcoming_deadlines) {
            $upcomingTasks = $user->tasks()
                ->whereNotNull('due_date')
                ->whereDate('due_date', '>=', $today)
                ->whereDate('due_date', '<=', $today->copy()->addDays(7))
                ->where('status', '!=', 'completed');

            if ($readAt !== null) {
                $upcomingTasks->where(function ($query) use ($readAt): void {
                    $query->whereDate('due_date', '>', $readAt->copy()->startOfDay()->addDays(7)->toDateString())
                        ->orWhere('updated_at', '>', $readAt);
                });
            }

            $unreadCount += $upcomingTasks->count();
        }

        if ($user->notify_overdue_tasks) {
            $overdueTasks = $user->tasks()
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', $today)
                ->where('status', '!=', 'completed');

            if ($readAt !== null) {
                $overdueTasks->where(function ($query) use ($readAt): void {
                    $query->whereDate('due_date', '>=', $readAt->toDateString())
                        ->orWhere('updated_at', '>', $readAt);
                });
            }

            $unreadCount += $overdueTasks->count();
        }

        if ($user->notify_task_completions) {
            $completionActivities = $user->taskActivities()
                ->where('action', 'completed');

            if ($readAt !== null) {
                $completionActivities->where('created_at', '>', $readAt);
            }

            $unreadCount += $completionActivities->count();
        }

        return $unreadCount;
    }
}
