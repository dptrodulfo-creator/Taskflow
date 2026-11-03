@extends('layouts.taskflow')

@section('title', 'Dashboard')

@section('content')
    <header class="dashboard-header">
        <div>
            <p class="eyebrow">{{ now()->format('l, F j, Y') }}</p>
            <h1>Welcome back, {{ auth()->user()->name }}</h1>
            <p class="sub">Here’s what’s happening with your tasks today.</p>
        </div>

        <div class="header-actions">
            <details class="quick-add" @if($errors->has('title') || $errors->has('due_date')) open @endif>
                <summary class="btn">
                    <svg aria-hidden="true" viewBox="0 0 20 20" fill="none">
                        <path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    Add new task
                </summary>
                <form class="quick-add-panel" method="POST" action="{{ route('tasks.store') }}">
                    @csrf
                    <h2>Quick add</h2>
                    <label class="sr-only" for="quick-task-title">Task title</label>
                    <input id="quick-task-title" name="title" type="text" value="{{ old('title') }}" maxlength="150" required placeholder="What needs to get done?">
                    <div class="quick-add-fields">
                        <label>
                            <span>Due date</span>
                            <input name="due_date" type="date" value="{{ old('due_date') }}">
                        </label>
                        <label>
                            <span>Priority</span>
                            <select name="priority">
                                <option value="medium" @selected(old('priority', 'medium') === 'medium')>Medium</option>
                                <option value="high" @selected(old('priority') === 'high')>High</option>
                                <option value="low" @selected(old('priority') === 'low')>Low</option>
                            </select>
                        </label>
                    </div>
                    <input type="hidden" name="status" value="todo">
                    <input type="hidden" name="return_to" value="dashboard">
                    @error('title')
                        <span class="errors">{{ $message }}</span>
                    @enderror
                    @error('due_date')
                        <span class="errors">{{ $message }}</span>
                    @enderror
                    <button class="btn" type="submit">Add task</button>
                </form>
            </details>

            <div class="header-avatar" aria-label="{{ auth()->user()->name }}">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
        </div>
    </header>

    <section class="cards dashboard-stats" aria-label="Task overview">
        <article class="card">
            <div class="stat-top">
                <span class="stat-label">Total tasks</span>
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
                <span class="stat-label">Pending</span>
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
                <span class="stat-label">In progress</span>
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
            <p class="stat-note">One step at a time</p>
        </article>

        <article class="card overdue-card">
            <div class="stat-top">
                <span class="stat-label">Overdue</span>
                <span class="stat-icon stat-icon-overdue" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </span>
            </div>
            <div class="stat-value overdue-value">{{ $stats['overdue'] }}</div>
            <p class="stat-note">Past due and unfinished</p>
        </article>
    </section>

    <section class="panel overall-progress">
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

    <div class="dashboard-grid">
        <section class="panel chart-panel" id="task-report">
            <div class="panel-head">
                <div>
                    <h2>Task progress overview</h2>
                    <p class="sub">Tasks added over the last six months</p>
                </div>
                <span class="chart-legend"><span aria-hidden="true"></span> Tasks added</span>
            </div>

            <div class="chart" role="img" aria-label="Monthly tasks added over the last six months">
                <div class="chart-grid" aria-hidden="true">
                    <span></span><span></span><span></span><span></span>
                </div>
                <div class="chart-bars">
                    @foreach($taskChart as $month)
                        <div class="chart-column">
                            <span class="chart-count">{{ $month['count'] ?: '' }}</span>
                            <div class="chart-bar-track">
                                <span class="chart-bar" style="height: {{ $month['height'] }}%"></span>
                            </div>
                            <span class="chart-month">{{ $month['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="panel calendar-panel" id="calendar">
            <div class="panel-head">
                <div>
                    <h2>Due dates</h2>
                    <p class="sub">Your monthly schedule</p>
                </div>
                <div class="calendar-controls">
                    <a href="{{ route('dashboard', array_merge(request()->except('month'), ['month' => $calendarMonth->copy()->subMonth()->format('Y-m')])) }}" aria-label="Previous month">‹</a>
                    <a href="{{ route('dashboard', array_merge(request()->except('month'), ['month' => $calendarMonth->copy()->addMonth()->format('Y-m')])) }}" aria-label="Next month">›</a>
                </div>
            </div>

            <p class="calendar-month">{{ $calendarMonth->format('F Y') }}</p>
            <div class="calendar-grid" role="grid" aria-label="{{ $calendarMonth->format('F Y') }}">
                @foreach(['M', 'T', 'W', 'T', 'F', 'S', 'S'] as $weekday)
                    <span class="calendar-weekday" role="columnheader">{{ $weekday }}</span>
                @endforeach

                @foreach($calendarWeeks as $week)
                    @foreach($week as $day)
                        <div class="calendar-day {{ $day['isCurrentMonth'] ? '' : 'outside-month' }} {{ $day['isToday'] ? 'today' : '' }} {{ $day['taskCount'] > 0 ? 'has-tasks' : '' }}" role="gridcell" aria-label="{{ $day['date']->format('F j') }}{{ $day['taskCount'] > 0 ? ', '.$day['taskCount'].' tasks due' : '' }}">
                            <span>{{ $day['date']->day }}</span>
                            @if($day['taskCount'] > 0)
                                <span class="calendar-task-count">{{ $day['taskCount'] }}</span>
                            @endif
                        </div>
                    @endforeach
                @endforeach
            </div>

            @if($dueTasks->isNotEmpty())
                <div class="calendar-upcoming">
                    <p class="calendar-upcoming-title">Tasks this month</p>
                    @foreach($dueTasks->take(3) as $task)
                        <a href="{{ route('tasks.index', ['due_date' => $task->due_date->format('Y-m-d')]) }}">
                            <span class="calendar-task-date">{{ $task->due_date->format('M j') }}</span>
                            <span class="calendar-task-title">{{ $task->title }}</span>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="calendar-empty">No tasks due this month.</p>
            @endif
        </section>
    </div>

    <section class="panel upcoming-panel" aria-labelledby="upcoming-title">
        <div class="panel-head">
            <div>
                <h2 id="upcoming-title">Upcoming deadlines</h2>
                <p class="sub">Your next five unfinished tasks due.</p>
            </div>
            <a class="text-link" href="{{ route('tasks.index', ['sort' => 'due_date', 'direction' => 'asc']) }}">View all tasks <span aria-hidden="true">→</span></a>
        </div>

        @if($upcomingTasks->isEmpty())
            <p class="upcoming-empty">No upcoming deadlines. You’re all caught up.</p>
        @else
            <ul class="upcoming-list">
                @foreach($upcomingTasks as $task)
                    <li>
                        <span class="upcoming-date">{{ $task->due_date->format('M d') }}</span>
                        <a class="upcoming-title" href="{{ route('tasks.edit', $task) }}">{{ $task->title }}</a>
                        <span class="priority {{ $task->priority }}">
                            <span class="priority-dot" aria-hidden="true"></span>
                            {{ ucfirst($task->priority) }}
                        </span>
                        <form method="POST" action="{{ route('tasks.cycle-status', $task) }}" class="status-cycle-form">
                            @csrf
                            <button
                                class="pill status-cycle {{ $task->status }}"
                                type="submit"
                                aria-label="Change status for {{ $task->title }}. Current status: {{ $task->status }}."
                                title="Click to cycle status"
                            >
                                {{ $task->status === 'todo' ? 'Todo' : 'In Progress' }}
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>

    <div class="dashboard-bottom">
        <section class="panel recent-panel">
            <div class="panel-head">
                <div>
                    <h2>Recent tasks</h2>
                    <p class="sub">Find and pick up where you left off.</p>
                </div>
                <a class="text-link" href="{{ route('tasks.index') }}">All tasks <span aria-hidden="true">→</span></a>
            </div>

            <form method="GET" action="{{ route('dashboard') }}" class="dashboard-filters">
                <input type="hidden" name="month" value="{{ $calendarMonth->format('Y-m') }}">
                <label class="dashboard-search">
                    <svg aria-hidden="true" viewBox="0 0 20 20" fill="none">
                        <circle cx="8.75" cy="8.75" r="5.75" stroke="currentColor" stroke-width="1.5"/>
                        <path d="m13 13 4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <span class="sr-only">Search tasks</span>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Search tasks...">
                </label>
                <label>
                    <span class="sr-only">Filter by status</span>
                    <select name="status">
                        <option value="">All statuses</option>
                        <option value="todo" @selected(request('status') === 'todo')>Pending</option>
                        <option value="in_progress" @selected(request('status') === 'in_progress')>In progress</option>
                        <option value="completed" @selected(request('status') === 'completed')>Completed</option>
                    </select>
                </label>
                <button class="btn btn-filter" type="submit">Filter</button>
            </form>

            @if($recentTasks->isEmpty())
                <div class="empty">
                    <span class="empty-icon" aria-hidden="true">✓</span>
                    @if($stats['total'] === 0)
                        <h3>No tasks yet</h3>
                        <p>Create a task to get your dashboard moving.</p>
                        <a href="{{ route('tasks.create') }}" class="btn">Create your first task</a>
                    @else
                        <h3>No matching tasks</h3>
                        <p>Try another search or status filter.</p>
                    @endif
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
                                    <td>
                                        <a class="task-title" href="{{ route('tasks.edit', $task) }}">{{ $task->title }}</a>
                                    </td>
                                    <td>
                                        <span class="priority {{ $task->priority }}">
                                            <span class="priority-dot" aria-hidden="true"></span>
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('tasks.cycle-status', $task) }}" class="status-cycle-form">
                                            @csrf
                                            <button
                                                class="pill status-cycle {{ $task->status }}"
                                                type="submit"
                                                aria-label="Change status for {{ $task->title }}. Current status: {{ $task->status }}."
                                                title="Click to cycle status"
                                            >
                                                {{ $task->status === 'todo' ? 'Todo' : ($task->status === 'completed' ? 'Done' : 'In Progress') }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        @if($task->due_date)
                                            <span class="due-date {{ $task->status !== 'completed' && $task->due_date->lt(today()) ? 'overdue' : '' }}">
                                                {{ $task->due_date->format('M d, Y') }}
                                            </span>
                                            @if($task->status !== 'completed' && $task->due_date->lt(today()))
                                                <span class="overdue-badge">Overdue</span>
                                            @endif
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

        <section class="panel activity-panel" id="recent-activity">
            <div class="panel-head">
                <div>
                    <h2>Recent activity</h2>
                    <p class="sub">The latest changes to your tasks.</p>
                </div>
            </div>

            @if($activities->isEmpty())
                <div class="activity-empty">
                    <p>Your task updates will appear here.</p>
                </div>
            @else
                <ul class="activity-list">
                    @foreach($activities as $activity)
                        <li>
                            <span class="activity-icon activity-{{ $activity->action }}" aria-hidden="true">
                                @if($activity->action === 'deleted')
                                    ×
                                @elseif($activity->action === 'completed')
                                    ✓
                                @elseif($activity->action === 'created')
                                    +
                                @else
                                    ↻
                                @endif
                            </span>
                            <span class="activity-copy">
                                <span>
                                    @if($activity->action === 'created')
                                        Created
                                    @elseif($activity->action === 'completed')
                                        Completed
                                    @elseif($activity->action === 'deleted')
                                        Deleted
                                    @else
                                        Updated
                                    @endif
                                    <strong>{{ $activity->task_title }}</strong>
                                </span>
                                <time datetime="{{ $activity->created_at->toIso8601String() }}">{{ $activity->created_at->diffForHumans() }}</time>
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endsection
