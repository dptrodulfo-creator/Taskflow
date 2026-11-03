<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $taskCounts = $request->user()
            ->tasks()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $stats = [
            'total' => (int) $taskCounts->sum(),
            'pending' => (int) $taskCounts->get('todo', 0),
            'in_progress' => (int) $taskCounts->get('in_progress', 0),
            'completed' => (int) $taskCounts->get('completed', 0),
        ];

        $recentTasks = $request->user()
            ->tasks()
            ->latest()
            ->limit(5)
            ->get(['title', 'status', 'priority', 'due_date', 'created_at']);

        $progress = $stats['total'] === 0
            ? 0
            : (int) round(($stats['completed'] / $stats['total']) * 100);

        return view('dashboard', compact('progress', 'recentTasks', 'stats'));
    }
}
