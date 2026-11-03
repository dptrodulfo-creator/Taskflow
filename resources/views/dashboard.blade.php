@extends('layouts.taskflow')

@section('title', 'Dashboard')

@section('content')
    <div class="topbar">
        <div>
            <p class="eyebrow">Workspace / Overview</p>
            <h1>Good day, {{ auth()->user()->name }} <span aria-hidden="true">👋</span></h1>
            <p class="sub">Here’s a quick look at what you’re working on.</p>
        </div>

        <a href="{{ route('tasks.create') }}" class="btn">
            <svg aria-hidden="true" viewBox="0 0 20 20" fill="none">
                <path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
            Add New Task
        </a>
    </div>

    <section class="cards" aria-label="Task overview">
        <article class="card">
            <div class="stat-top">
                <span class="stat-label">Total Tasks</span>
                <span class="stat-icon stat-icon-total" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M8 6h12M8 12h12M8 18h12M4 6h.01M4 12h.01M4 18h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $stats['total'] }}</div>
            <p class="stat-note">Across your workspace</p>
        </article>

        <article class="card">
            <div class="stat-top">
                <span class="stat-label">Pending Tasks</span>
                <span class="stat-icon stat-icon-pending" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M12 8v4l2.5 1.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $stats['pending'] }}</div>
            <p class="stat-note">Ready when you are</p>
        </article>

        <article class="card">
            <div class="stat-top">
                <span class="stat-label">In Progress</span>
                <span class="stat-icon stat-icon-progress" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M4 16.5 9 11l3.5 3.5L20 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15.5 7H20v4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $stats['in_progress'] }}</div>
            <p class="stat-note">Currently on your plate</p>
        </article>

        <article class="card">
            <div class="stat-top">
                <span class="stat-label">Completed</span>
                <span class="stat-icon stat-icon-completed" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="m5 12.5 4.5 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </div>
            <div class="stat-value">{{ $stats['completed'] }}</div>
            <p class="stat-note">Great work, keep it up</p>
        </article>
    </section>

    <section class="panel progress-panel" aria-label="Overall progress">
        <div class="progress-copy">
            <div>
                <h2>Overall progress</h2>
                <p class="sub">{{ $stats['completed'] }} of {{ $stats['total'] }} tasks completed</p>
            </div>
            <strong>{{ $progress }}%</strong>
        </div>
        <div class="progress" role="progressbar" aria-label="Tasks completed" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
            <span style="width: {{ $progress }}%"></span>
        </div>
    </section>

    <section class="panel recent-panel">
        <div class="panel-head">
            <div>
                <h2>Recent Tasks</h2>
                <p class="sub">Your latest updates, all in one place.</p>
            </div>
            <a class="text-link" href="{{ route('tasks.index') }}">
                View all tasks
                <svg aria-hidden="true" viewBox="0 0 20 20" fill="none">
                    <path d="M4 10h12m-5-5 5 5-5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>

        @if($recentTasks->isEmpty())
            <div class="empty">
                <span class="empty-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M7 4h10a2 2 0 0 1 2 2v14H5V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                        <path d="M9 9h6m-6 4h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                    </svg>
                </span>
                <h3>No tasks yet</h3>
                <p>Start with one small task and build momentum.</p>
                <a href="{{ route('tasks.create') }}" class="btn">Create your first task</a>
            </div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">Task</th>
                            <th scope="col">Priority</th>
                            <th scope="col">Status</th>
                            <th scope="col">Due date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTasks as $task)
                            <tr>
                                <td class="task-title">{{ $task->title }}</td>
                                <td>
                                    <span class="priority {{ $task->priority }}">
                                        <span class="priority-dot" aria-hidden="true"></span>
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="pill {{ $task->status }}">
                                        <span class="status-dot" aria-hidden="true"></span>
                                        {{ ucwords(str_replace('_', ' ', $task->status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($task->due_date)
                                        <span class="due-date {{ $task->status !== 'completed' && $task->due_date->lt(today()) ? 'overdue' : '' }}">
                                            {{ $task->due_date->format('M d, Y') }}
                                        </span>
                                    @else
                                        <span class="muted">No due date</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
