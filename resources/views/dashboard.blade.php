@extends('layouts.taskflow')

@section('title', 'Dashboard')

@section('content')
    @php
        $taskChartValues = $taskChart->pluck('count')->all();
        $maxChartValue = max(1, max($taskChartValues ?: [1]));
        $chartPoints = [];
        foreach ($taskChart as $index => $point) {
            $x = 18 + ($index * 42);
            $y = 120 - (($point['count'] / $maxChartValue) * 82);
            $chartPoints[] = [$x, $y];
        }
        $chartPolyline = implode(' ', array_map(fn (array $point) => $point[0] . ',' . $point[1], $chartPoints));
    @endphp

    <header class="top-toolbar">
        <label class="search-box" aria-label="Search tasks">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="1.8"/>
                <path d="M16 16L21 21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
            <input type="text" value="" placeholder="Search tasks, projects, or press Ctrl + K..." />
        </label>

        <div class="toolbar-actions">
            <div class="status-pill">
                <span>57%</span>
                <svg viewBox="0 0 16 16" fill="none" width="14" height="14" aria-hidden="true">
                    <path d="M4 6.5L8 10.5L12 6.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <button class="toolbar-icon" type="button" aria-label="Notifications">
                <svg viewBox="0 0 24 24" fill="none" width="16" height="16" aria-hidden="true">
                    <path d="M8 17H16L15 15.5V11.5C15 9.6 13.7 8 12 8C10.3 8 9 9.6 9 11.5V15.5L8 17Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                    <path d="M10.5 19C11 19.8 11.5 20 12 20C12.5 20 13 19.8 13.5 19" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                </svg>
            </button>
            <button class="toolbar-share" type="button">
                <svg viewBox="0 0 24 24" fill="none" width="16" height="16" aria-hidden="true">
                    <path d="M15 8L9 12L15 16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M5 12H19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <span>Share</span>
            </button>
            <div class="profile-mini">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="meta">
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>Student · Developer</span>
                </div>
            </div>
        </div>
    </header>

    <div class="dashboard-layout">
        <div class="main-column">
            <div class="welcome-row">
                <div class="welcome-copy">
                    <p class="eyebrow">Good morning,</p>
                    <h1>Welcome back, {{ auth()->user()->name }}!</h1>
                    <p>Stay focused, keep building your goals. You got this!</p>
                </div>

                <button class="primary-btn" type="button">+ Add new task</button>
            </div>

            <div class="stats-grid">
                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">Total tasks</span>
                        <span class="stat-icon blue" aria-hidden="true">☰</span>
                    </div>
                    <div class="stat-value">{{ $stats['total'] }}</div>
                    <div class="stat-meta"><span class="trend-up">↑</span> 12% from last month</div>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">Pending</span>
                        <span class="stat-icon yellow" aria-hidden="true">◔</span>
                    </div>
                    <div class="stat-value">{{ $stats['pending'] }}</div>
                    <div class="stat-meta"><span class="trend-down">↓</span> 8% from last week</div>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">In Progress</span>
                        <span class="stat-icon green" aria-hidden="true">✓</span>
                    </div>
                    <div class="stat-value">{{ $stats['in_progress'] }}</div>
                    <div class="stat-meta"><span class="trend-up">↑</span> 25% completion rate</div>
                </article>

                <article class="stat-card">
                    <div class="stat-top">
                        <span class="stat-label">Completed</span>
                        <span class="stat-icon purple" aria-hidden="true">✦</span>
                    </div>
                    <div class="stat-value">{{ $stats['completed'] }}</div>
                    <div class="stat-meta"><span class="trend-up">↑</span> 7% increase</div>
                </article>
            </div>

            <div class="analytics-row">
                <section class="panel">
                    <div class="panel-head">
                        <h3>Task progress overview</h3>
                        <span class="mini-link">This month</span>
                    </div>

                    <div class="donut-wrap">
                        <div class="donut-chart" style="background: conic-gradient(var(--primary) 0 {{ $progress }}%, #e3ebf3 {{ $progress }}% 100%);">
                            <div class="donut-value">
                                <strong>{{ $progress }}%</strong>
                                <span>Completed</span>
                            </div>
                        </div>

                        <ul class="legend-list">
                            <li class="legend-row">
                                <span class="label"><span class="legend-dot blue"></span>Completed</span>
                                <strong>{{ $stats['completed'] }}</strong>
                            </li>
                            <li class="legend-row">
                                <span class="label"><span class="legend-dot green"></span>In progress</span>
                                <strong>{{ $stats['in_progress'] }}</strong>
                            </li>
                            <li class="legend-row">
                                <span class="label"><span class="legend-dot gold"></span>Pending</span>
                                <strong>{{ $stats['pending'] }}</strong>
                            </li>
                            <li class="legend-row">
                                <span class="label"><span class="legend-dot red"></span>Overdue</span>
                                <strong>{{ $stats['overdue'] }}</strong>
                            </li>
                        </ul>
                    </div>
                </section>

                <section class="panel chart-panel">
                    <div class="panel-head">
                        <h3>Daily Task Trend</h3>
                        <span class="mini-link">7 days</span>
                    </div>

                    <svg class="mini-chart" viewBox="0 0 300 170" aria-label="Daily task trend chart" role="img">
                        <g stroke="#edf1f5" stroke-width="1">
                            <line x1="18" y1="20" x2="282" y2="20"/>
                            <line x1="18" y1="60" x2="282" y2="60"/>
                            <line x1="18" y1="100" x2="282" y2="100"/>
                            <line x1="18" y1="140" x2="282" y2="140"/>
                        </g>
                        <polyline fill="none" stroke="#236cf5" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" points="{{ $chartPolyline }}"/>
                        @foreach ($chartPoints as $point)
                            <circle cx="{{ $point[0] }}" cy="{{ $point[1] }}" r="4" fill="#236cf5"/>
                        @endforeach
                        <g fill="#7d8ca1" font-size="10" font-family="DM Sans, sans-serif">
                            @foreach ($taskChart as $index => $month)
                                <text x="{{ 18 + ($index * 42) }}" y="156" text-anchor="middle">{{ $month['label'] }}</text>
                            @endforeach
                        </g>
                    </svg>

                    <div class="chart-legend"><span class="dot"></span> 12 tasks</div>
                </section>
            </div>

            <div class="lower-grid">
                <section class="panel task-list-panel">
                    <div class="panel-head">
                        <h3>Recent Tasks</h3>
                        <a href="{{ route('tasks.index') }}" class="mini-link">View all</a>
                    </div>

                    <div class="task-tabs">
                        <button class="task-tab active" type="button">All</button>
                        <button class="task-tab" type="button">In progress</button>
                        <button class="task-tab" type="button">Pending</button>
                        <button class="task-tab" type="button">Completed</button>
                    </div>

                    @if ($recentTasks->isEmpty())
                        <p class="muted">No tasks yet</p>
                        <p class="muted">Create your first task</p>
                    @else
                        <ul class="task-list">
                            @foreach ($recentTasks as $task)
                                <li class="task-row {{ $task->status === 'completed' ? 'is-done' : '' }}">
                                    <span class="task-check" aria-hidden="true">{{ $task->status === 'completed' ? '✓' : '' }}</span>
                                    <span class="task-title">{{ $task->title }}</span>
                                    <span class="priority-label {{ $task->priority }}"><span class="dot"></span>{{ ucfirst($task->priority) }}</span>
                                    <span class="task-date">{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No date' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                <section class="panel calendar-panel">
                    <div class="calendar-header">
                        <h3>{{ $calendarMonth->format('F Y') }}</h3>
                        <div class="calendar-navs">
                            <button type="button" aria-label="Previous month">‹</button>
                            <button type="button" aria-label="Next month">›</button>
                        </div>
                    </div>

                    <div class="calendar-grid" role="grid" aria-label="{{ $calendarMonth->format('F Y') }} calendar">
                        @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                            <div class="calendar-weekday" role="columnheader">{{ $day }}</div>
                        @endforeach

                        @foreach ($calendarWeeks as $week)
                            @foreach ($week as $day)
                                <div class="calendar-day {{ $day['isCurrentMonth'] ? '' : 'outside-month' }} {{ $day['isToday'] ? 'today' : '' }} {{ $day['taskCount'] > 0 ? 'has-tasks' : '' }}" role="gridcell">
                                    {{ $day['date']->day }}
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </section>
            </div>
        </div>

        <aside class="right-rail">
            <section class="panel">
                <div class="panel-head">
                    <h3>Quick Actions</h3>
                </div>

                <div class="quick-action-list">
                    <button class="quick-action blue" type="button">
                        <span class="quick-action-icon">＋</span>
                        <span class="quick-action-copy">
                            <strong>Create New Task</strong>
                            <span>Add a new task quickly</span>
                        </span>
                        <span class="arrow">→</span>
                    </button>

                    <button class="quick-action purple" type="button">
                        <span class="quick-action-icon">◫</span>
                        <span class="quick-action-copy">
                            <strong>View Calendar</strong>
                            <span>Check your schedule</span>
                        </span>
                        <span class="arrow">→</span>
                    </button>

                    <button class="quick-action green" type="button">
                        <span class="quick-action-icon">▣</span>
                        <span class="quick-action-copy">
                            <strong>Generate Report</strong>
                            <span>View productivity stats</span>
                        </span>
                        <span class="arrow">→</span>
                    </button>
                </div>
            </section>

            <section class="panel">
                <div class="panel-head">
                    <h3>Upcoming deadlines</h3>
                    <a href="{{ route('tasks.index') }}" class="mini-link">View all</a>
                </div>

                <ul class="deadline-list">
                    @foreach ($upcomingTasks as $task)
                        <li class="deadline-item">
                            <span class="deadline-marker" aria-hidden="true"></span>
                            <div class="deadline-meta">
                                <strong>{{ $task->title }}</strong>
                                <span>{{ $task->due_date ? $task->due_date->format('D, M d') : 'No date' }}</span>
                            </div>
                            <span class="deadline-tag {{ $task->priority }}">{{ ucfirst($task->priority) }}</span>
                        </li>
                    @endforeach
                    @if ($upcomingTasks->isEmpty())
                        <li class="deadline-item">
                            <span class="deadline-marker" aria-hidden="true"></span>
                            <div class="deadline-meta">
                                <strong>No upcoming tasks</strong>
                                <span>You're all caught up.</span>
                            </div>
                            <span class="deadline-tag low">Low</span>
                        </li>
                    @endif
                </ul>
            </section>

            <div class="motivation-card">
                <span class="rocket" aria-hidden="true">🚀</span>
                <h4>Keep going!</h4>
                <p>You’re 54% there this month.</p>
                <div class="progress-line"><span></span></div>
            </div>

            <section class="panel">
                <div class="panel-head">
                    <h3>Recent activity</h3>
                </div>

                @if ($activities->isEmpty())
                    <p class="muted">No recent activity yet.</p>
                @else
                    <ul class="deadline-list">
                        @foreach ($activities as $activity)
                            <li class="deadline-item">
                                <span class="deadline-marker" aria-hidden="true"></span>
                                <div class="deadline-meta">
                                    <strong>{{ $activity->task_title }}</strong>
                                    <span>{{ ucfirst($activity->action) }}</span>
                                </div>
                                <span class="deadline-tag low">{{ $activity->created_at->diffForHumans() }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </aside>
    </div>
@endsection
