@extends('layouts.admin')

@section('title', 'Task monitoring')

@section('content')
    <header class="mb-7">
        <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Platform activity</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Task monitoring</h1>
        <p class="mt-2 text-slate-600">Search tasks across all accounts and review their current status.</p>
    </header>

    <form method="GET" class="mb-5 grid gap-3 sm:grid-cols-[minmax(0,1fr)_12rem_auto]">
        <label class="sr-only" for="search">Search tasks or owners</label>
        <input id="search" type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search tasks or owners" class="rounded-xl border-slate-300 bg-white shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
        <label class="sr-only" for="status">Filter by status</label>
        <select id="status" name="status" class="rounded-xl border-slate-300 bg-white shadow-sm focus:border-emerald-600 focus:ring-emerald-600">
            <option value="">All statuses</option>
            <option value="todo" @selected(($filters['status'] ?? '') === 'todo')>Pending</option>
            <option value="in_progress" @selected(($filters['status'] ?? '') === 'in_progress')>In progress</option>
            <option value="completed" @selected(($filters['status'] ?? '') === 'completed')>Completed</option>
        </select>
        <button class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">Apply</button>
    </form>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                    <tr><th class="px-5 py-3">Task</th><th class="px-5 py-3">Owner</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Priority</th><th class="px-5 py-3">Due date</th><th class="px-5 py-3">Created</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($tasks as $task)
                        <tr>
                            <td class="max-w-sm px-5 py-4 font-semibold text-slate-900">{{ $task->title }}</td>
                            <td class="whitespace-nowrap px-5 py-4"><p class="font-medium text-slate-800">{{ $task->user->name }}</p><p class="text-xs text-slate-500">{{ $task->user->email }}</p></td>
                            <td class="whitespace-nowrap px-5 py-4 capitalize text-slate-600">{{ str_replace('_', ' ', $task->status) }}</td>
                            <td class="whitespace-nowrap px-5 py-4 capitalize text-slate-600">{{ $task->priority }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $task->due_date?->format('M j, Y') ?? '—' }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $task->created_at->format('M j, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-500">No tasks matched your filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($tasks->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $tasks->links() }}</div>
        @endif
    </div>
@endsection
