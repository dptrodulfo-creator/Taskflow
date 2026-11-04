<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationCenterController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $user->forceFill(['notification_center_read_at' => now()])->save();
        $today = today();

        $upcomingTasks = $user->notify_upcoming_deadlines
            ? $user->tasks()
                ->whereNotNull('due_date')
                ->whereDate('due_date', '>=', $today)
                ->whereDate('due_date', '<=', $today->copy()->addDays(7))
                ->where('status', '!=', 'completed')
                ->orderBy('due_date')
                ->get(['id', 'title', 'status', 'due_date'])
            : collect();

        $overdueTasks = $user->notify_overdue_tasks
            ? $user->tasks()
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', $today)
                ->where('status', '!=', 'completed')
                ->orderBy('due_date')
                ->get(['id', 'title', 'status', 'due_date'])
            : collect();

        $completionNotifications = $user->notify_task_completions
            ? $user->taskActivities()
                ->where('action', 'completed')
                ->latest()
                ->limit(10)
                ->get(['id', 'task_id', 'task_title', 'created_at'])
            : collect();

        return view('notifications.index', compact(
            'completionNotifications',
            'overdueTasks',
            'upcomingTasks',
        ));
    }
}
