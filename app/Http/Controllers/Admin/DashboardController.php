<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskActivity;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $taskCounts = Task::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $stats = [
            'users' => User::query()->count(),
            'admins' => User::query()->where('is_admin', true)->count(),
            'tasks' => (int) $taskCounts->sum(),
            'pending' => (int) $taskCounts->get('todo', 0),
            'in_progress' => (int) $taskCounts->get('in_progress', 0),
            'completed' => (int) $taskCounts->get('completed', 0),
            'overdue' => Task::query()
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', today())
                ->where('status', '!=', 'completed')
                ->count(),
        ];
        $recentTasks = Task::query()
            ->with('user:id,name,email')
            ->latest()
            ->limit(8)
            ->get(['id', 'user_id', 'title', 'status', 'priority', 'due_date', 'created_at']);
        $recentActivities = TaskActivity::query()
            ->with('user:id,name,email')
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact('recentActivities', 'recentTasks', 'stats'));
    }
}
