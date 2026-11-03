<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', 'in:todo,in_progress,completed'],
            'month' => ['nullable', 'date_format:Y-m'],
        ]);

        $user = $request->user();
        $taskCounts = $user
            ->tasks()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $stats = [
            'total' => (int) $taskCounts->sum(),
            'pending' => (int) $taskCounts->get('todo', 0),
            'in_progress' => (int) $taskCounts->get('in_progress', 0),
            'completed' => (int) $taskCounts->get('completed', 0),
            'overdue' => $user->tasks()
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', today())
                ->where('status', '!=', 'completed')
                ->count(),
        ];

        $recentTasks = $user
            ->tasks()
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where('title', 'like', "%{$search}%");
            })
            ->when($filters['status'] ?? null, function ($query, string $status): void {
                $query->where('status', $status);
            })
            ->latest()
            ->limit(5)
            ->get(['id', 'title', 'status', 'priority', 'due_date', 'created_at']);

        $progress = $stats['total'] === 0
            ? 0
            : (int) round(($stats['completed'] / $stats['total']) * 100);

        $calendarMonth = isset($filters['month'])
            ? Carbon::createFromFormat('!Y-m', $filters['month'])
            : Carbon::now()->startOfMonth();
        $calendarStart = $calendarMonth->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $calendarMonth->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);
        $calendarTaskCounts = $user->tasks()
            ->whereBetween('due_date', [$calendarStart, $calendarEnd->copy()->endOfDay()])
            ->selectRaw('due_date, COUNT(*) as aggregate')
            ->groupBy('due_date')
            ->get()
            ->mapWithKeys(fn (Task $task): array => [
                $task->due_date->toDateString() => (int) $task->aggregate,
            ]);
        $dueTasks = $user->tasks()
            ->whereBetween('due_date', [
                $calendarMonth->copy()->startOfMonth(),
                $calendarMonth->copy()->endOfMonth()->endOfDay(),
            ])
            ->orderBy('due_date')
            ->limit(3)
            ->get(['id', 'title', 'status', 'due_date']);
        $upcomingTasks = $user->tasks()
            ->whereNotNull('due_date')
            ->whereDate('due_date', '>=', today())
            ->where('status', '!=', 'completed')
            ->orderBy('due_date')
            ->limit(5)
            ->get(['id', 'title', 'status', 'priority', 'due_date']);

        $calendarWeeks = [];
        $week = [];
        for ($day = $calendarStart->copy(); $day->lte($calendarEnd); $day->addDay()) {
            $dateKey = $day->format('Y-m-d');
            $week[] = [
                'date' => $day->copy(),
                'isCurrentMonth' => $day->month === $calendarMonth->month,
                'isToday' => $day->isToday(),
                'taskCount' => $calendarTaskCounts->get($dateKey, 0),
            ];

            if (count($week) === 7) {
                $calendarWeeks[] = $week;
                $week = [];
            }
        }

        $chartStart = Carbon::now()->startOfMonth()->subMonths(5);
        $monthlyTaskCounts = $user->tasks()
            ->whereBetween('created_at', [$chartStart, Carbon::now()->endOfMonth()])
            ->get(['created_at'])
            ->countBy(fn (Task $task): string => $task->created_at->format('Y-m'));
        $chartMaximum = max(1, (int) $monthlyTaskCounts->max());
        $taskChart = collect(range(5, 0))
            ->map(function (int $monthsAgo) use ($monthlyTaskCounts, $chartMaximum): array {
                $month = Carbon::now()->startOfMonth()->subMonths($monthsAgo);
                $count = (int) $monthlyTaskCounts->get($month->format('Y-m'), 0);

                return [
                    'label' => $month->format('M'),
                    'count' => $count,
                    'height' => $count === 0 ? 0 : max(8, (int) round(($count / $chartMaximum) * 100)),
                ];
            });

        $activities = $user->taskActivities()->latest()->limit(5)->get();

        return view('dashboard', compact(
            'activities',
            'calendarMonth',
            'calendarWeeks',
            'dueTasks',
            'progress',
            'recentTasks',
            'stats',
            'taskChart',
            'upcomingTasks',
        ));
    }
}
