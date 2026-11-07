@extends('layouts.admin')

@section('title', 'Admin overview')

@section('content')
    <header class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Administration</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Overview</h1>
        <p class="mt-2 text-slate-600">Monitor TaskFlow accounts, tasks, and recent activity.</p>
    </header>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Platform statistics">
        @foreach ([
            ['Total users', $stats['users']],
            ['Admin accounts', $stats['admins']],
            ['Total tasks', $stats['tasks']],
            ['Overdue tasks', $stats['overdue']],
        ] as [$label, $value])
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
                <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($value) }}</p>
            </article>
        @endforeach
    </section>

    <section class="mt-8 grid gap-6 xl:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5 flex items-center justify-between gap-4">
                <h2 class="text-lg font-bold text-slate-900">Recent tasks</h2>
                <a href="{{ route('admin.tasks.index') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">Monitor all</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentTasks as $task)
                    <div class="flex flex-wrap items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-slate-800">{{ $task->title }}</p>
                            <p class="mt-1 text-sm text-slate-500">{{ $task->user->name }} · {{ $task->user->email }}</p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold capitalize text-slate-700">{{ str_replace('_', ' ', $task->status) }}</span>
                    </div>
                @empty
                    <p class="py-4 text-sm text-slate-500">No tasks have been created yet.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5 flex items-center justify-between gap-4">
                <h2 class="text-lg font-bold text-slate-900">Recent activity</h2>
                <a href="{{ route('admin.activities.index') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">View logs</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($recentActivities as $activity)
                    <div class="py-3 first:pt-0 last:pb-0">
                        <p class="font-medium text-slate-800">
                            <span class="font-semibold">{{ $activity->user->name }}</span>
                            {{ ucfirst($activity->action) }} task
                            <span class="font-semibold">{{ $activity->task_title }}</span>
                        </p>
                        <p class="mt-1 text-xs text-slate-500">{{ $activity->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                @empty
                    <p class="py-4 text-sm text-slate-500">No activity has been recorded yet.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900">Task status</h2>
        <dl class="mt-4 grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl bg-slate-50 p-4"><dt class="text-sm text-slate-500">Pending</dt><dd class="mt-1 text-2xl font-bold">{{ number_format($stats['pending']) }}</dd></div>
            <div class="rounded-xl bg-slate-50 p-4"><dt class="text-sm text-slate-500">In progress</dt><dd class="mt-1 text-2xl font-bold">{{ number_format($stats['in_progress']) }}</dd></div>
            <div class="rounded-xl bg-slate-50 p-4"><dt class="text-sm text-slate-500">Completed</dt><dd class="mt-1 text-2xl font-bold">{{ number_format($stats['completed']) }}</dd></div>
        </dl>
        <a href="{{ route('admin.reports.index') }}" class="mt-5 inline-flex text-sm font-semibold text-emerald-700 hover:text-emerald-900">Open platform reports →</a>
    </section>
@endsection
