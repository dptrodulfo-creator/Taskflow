@extends('layouts.admin')

@section('title', 'Activity logs')

@section('content')
    <header class="mb-7">
        <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Audit trail</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-900">Activity logs</h1>
        <p class="mt-2 text-slate-600">Recent task changes made across all user accounts.</p>
    </header>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="divide-y divide-slate-100">
            @forelse ($activities as $activity)
                <article class="flex flex-wrap items-start justify-between gap-3 px-5 py-4">
                    <div>
                        <p class="font-medium text-slate-800">
                            <span class="font-semibold">{{ $activity->user->name }}</span>
                            {{ ucfirst($activity->action) }} task
                            <span class="font-semibold">{{ $activity->task_title }}</span>
                        </p>
                        <p class="mt-1 text-sm text-slate-500">{{ $activity->user->email }}</p>
                    </div>
                    <time class="whitespace-nowrap text-sm text-slate-500" datetime="{{ $activity->created_at->toIso8601String() }}">
                        {{ $activity->created_at->format('M j, Y g:i A') }}
                    </time>
                </article>
            @empty
                <p class="px-5 py-10 text-center text-slate-500">No activity has been recorded yet.</p>
            @endforelse
        </div>
        @if ($activities->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">{{ $activities->links() }}</div>
        @endif
    </div>
@endsection
