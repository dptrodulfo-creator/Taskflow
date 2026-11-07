@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
    <header class="mb-7">
        <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Insights</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Platform reports</h1>
        <p class="mt-2 text-slate-600">Account growth and task completion, including the last six months.</p>
    </header>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Report summary">
        @foreach ([
            ['All users', $stats['users']],
            ['New users this month', $stats['new_users']],
            ['All tasks', $stats['tasks']],
            ['New tasks this month', $stats['new_tasks']],
        ] as [$label, $value])
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($value) }}</p>
            </article>
        @endforeach
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm xl:col-span-2">
            <h2 class="text-lg font-bold text-slate-900">Monthly activity</h2>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                    <thead class="text-xs uppercase tracking-wide text-slate-500"><tr><th class="py-3 pr-4">Month</th><th class="px-4 py-3">New users</th><th class="px-4 py-3">Tasks created</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($monthlyReports as $month)
                            <tr><th class="whitespace-nowrap py-3 pr-4 font-semibold text-slate-800">{{ $month['label'] }}</th><td class="px-4 py-3 text-slate-600">{{ number_format($month['users']) }}</td><td class="px-4 py-3 text-slate-600">{{ number_format($month['tasks']) }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900">Task completion</h2>
            <p class="mt-4 text-4xl font-bold text-emerald-700">{{ $stats['completion_rate'] }}%</p>
            <p class="mt-1 text-sm text-slate-500">of all tasks are complete</p>
            <dl class="mt-6 divide-y divide-slate-100">
                <div class="flex justify-between gap-3 py-3"><dt class="text-sm text-slate-600">Pending</dt><dd class="font-semibold">{{ number_format($taskStatusCounts['todo']) }}</dd></div>
                <div class="flex justify-between gap-3 py-3"><dt class="text-sm text-slate-600">In progress</dt><dd class="font-semibold">{{ number_format($taskStatusCounts['in_progress']) }}</dd></div>
                <div class="flex justify-between gap-3 py-3"><dt class="text-sm text-slate-600">Completed</dt><dd class="font-semibold">{{ number_format($taskStatusCounts['completed']) }}</dd></div>
            </dl>
        </div>
    </section>
@endsection
