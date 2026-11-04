@extends('layouts.taskflow')

@section('title', 'Notifications')

@section('content')

<div class="topbar">
    <div>
        <p class="eyebrow">Stay in the loop</p>
        <h1>Notification Center</h1>
        <p class="sub">Upcoming deadlines, overdue tasks, and recent completions.</p>
    </div>

    <a href="{{ route('tasks.index') }}" class="btn">
        View tasks
    </a>
</div>

<div class="notification-sections">
    <section class="panel notification-section">
        <div class="panel-head">
            <div>
                <h2>Upcoming deadlines</h2>
                <p class="sub">Tasks due within the next 7 days.</p>
            </div>
            <span class="notification-section-count" aria-label="{{ $upcomingTasks->count() }} upcoming deadlines">{{ $upcomingTasks->count() }}</span>
        </div>

        @if($upcomingTasks->isEmpty())
            <p class="notification-empty">{{ auth()->user()->notify_upcoming_deadlines ? 'You’re all clear. No deadlines in the next 7 days.' : 'Upcoming deadline reminders are turned off in Settings.' }}</p>
        @else
            <div class="notification-list">
                @foreach($upcomingTasks as $task)
                    <article class="notification-item">
                        <span class="notification-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <rect x="4" y="6" width="16" height="14" rx="2" stroke="currentColor" stroke-width="1.7"/>
                                <path d="M8 4v4m8-4v4M4 10h16" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <span class="notification-copy">
                            <strong>{{ $task->title }}</strong>
                            <span>Due {{ $task->due_date->format('M d, Y') }}</span>
                        </span>
                        <a class="notification-link" href="{{ route('tasks.edit', $task) }}">View task</a>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="panel notification-section overdue">
        <div class="panel-head">
            <div>
                <h2>Overdue tasks</h2>
                <p class="sub">Uncompleted tasks past their due date.</p>
            </div>
            <span class="notification-section-count" aria-label="{{ $overdueTasks->count() }} overdue tasks">{{ $overdueTasks->count() }}</span>
        </div>

        @if($overdueTasks->isEmpty())
            <p class="notification-empty">{{ auth()->user()->notify_overdue_tasks ? 'No overdue tasks. Nice work!' : 'Overdue task alerts are turned off in Settings.' }}</p>
        @else
            <div class="notification-list">
                @foreach($overdueTasks as $task)
                    <article class="notification-item">
                        <span class="notification-icon overdue" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.7"/>
                                <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="notification-copy">
                            <strong>{{ $task->title }}</strong>
                            <span>Was due {{ $task->due_date->format('M d, Y') }}</span>
                        </span>
                        <a class="notification-link" href="{{ route('tasks.edit', $task) }}">View task</a>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="panel notification-section">
        <div class="panel-head">
            <div>
                <h2>Completed tasks</h2>
                <p class="sub">Your latest task completion updates.</p>
            </div>
            <span class="notification-section-count" aria-label="{{ $completionNotifications->count() }} recent completions">{{ $completionNotifications->count() }}</span>
        </div>

        @if($completionNotifications->isEmpty())
            <p class="notification-empty">{{ auth()->user()->notify_task_completions ? 'Completed tasks will show up here.' : 'Task completion updates are turned off in Settings.' }}</p>
        @else
            <div class="notification-list">
                @foreach($completionNotifications as $notification)
                    <article class="notification-item">
                        <span class="notification-icon completed" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="m5 12.5 4.5 4.5L19 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="notification-copy">
                            <strong>{{ $notification->task_title }} completed</strong>
                            <span>{{ $notification->created_at->diffForHumans() }}</span>
                        </span>
                        @if($notification->task_id)
                            <a class="notification-link" href="{{ route('tasks.edit', $notification->task_id) }}">View task</a>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</div>

@endsection
