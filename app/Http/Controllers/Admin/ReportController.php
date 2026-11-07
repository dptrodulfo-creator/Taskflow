<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use LogicException;

class ReportController extends Controller
{
    public function index(): View
    {
        $taskStatusCounts = Task::query()
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');
        $monthStart = now()->startOfMonth();
        $reportStart = $monthStart->copy()->subMonths(5);
        $reportEnd = now()->endOfMonth();
        $monthExpression = match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'mysql', 'mariadb' => "DATE_FORMAT(created_at, '%Y-%m')",
            'pgsql' => "to_char(created_at, 'YYYY-MM')",
            default => throw new LogicException('Monthly reporting is not supported for this database driver.'),
        };
        $taskCountsByMonth = Task::query()
            ->whereBetween('created_at', [$reportStart, $reportEnd])
            ->selectRaw("$monthExpression as month, COUNT(*) as aggregate")
            ->groupBy('month')
            ->pluck('aggregate', 'month');
        $userCountsByMonth = User::query()
            ->whereBetween('created_at', [$reportStart, $reportEnd])
            ->selectRaw("$monthExpression as month, COUNT(*) as aggregate")
            ->groupBy('month')
            ->pluck('aggregate', 'month');
        $monthlyReports = collect(range(5, 0))
            ->map(function (int $monthsAgo) use ($taskCountsByMonth, $userCountsByMonth): array {
                $month = now()->startOfMonth()->subMonths($monthsAgo);
                $monthKey = $month->format('Y-m');

                return [
                    'label' => $month->format('M Y'),
                    'tasks' => (int) $taskCountsByMonth->get($monthKey, 0),
                    'users' => (int) $userCountsByMonth->get($monthKey, 0),
                ];
            });
        $totalTasks = (int) $taskStatusCounts->sum();
        $completedTasks = (int) $taskStatusCounts->get('completed', 0);
        $stats = [
            'users' => User::query()->count(),
            'new_users' => User::query()->where('created_at', '>=', $monthStart)->count(),
            'tasks' => $totalTasks,
            'new_tasks' => Task::query()->where('created_at', '>=', $monthStart)->count(),
            'completion_rate' => $totalTasks === 0 ? 0 : (int) round(($completedTasks / $totalTasks) * 100),
        ];
        $taskStatusCounts = [
            'todo' => (int) $taskStatusCounts->get('todo', 0),
            'in_progress' => (int) $taskStatusCounts->get('in_progress', 0),
            'completed' => $completedTasks,
        ];

        return view('admin.reports.index', compact('monthlyReports', 'stats', 'taskStatusCounts'));
    }
}
