@extends('layouts.taskflow')

@section('title', 'Dashboard')

@section('content')

<div class="topbar">
    <div>
        <h1>Good day, {{ auth()->user()->name }} 👋</h1>
        <p class="sub">Here's an overview of your productivity.</p>
    </div>

    <a href="{{ route('tasks.create') }}" class="btn">
        + Create Task
    </a>
</div>

<div class="cards">

    <div class="card">
        <div class="stat-label">Total Tasks</div>
        <div class="stat-value">{{ $stats['total'] }}</div>
    </div>

    <div class="card">
        <div class="stat-label">Completed</div>
        <div class="stat-value">{{ $stats['completed'] }}</div>
    </div>

    <div class="card">
        <div class="stat-label">In Progress</div>
        <div class="stat-value">{{ $stats['in_progress'] }}</div>
    </div>

    <div class="card">
        <div class="stat-label">Overdue</div>
        <div class="stat-value" style="color:var(--red)">
            {{ $stats['overdue'] }}
        </div>
    </div>

</div>

<section class="panel">

    <div class="panel-head">
        <h2>Overall Progress</h2>
        <strong>{{ $progress }}%</strong>
    </div>

    <div class="progress">
        <span style="width:{{ $progress }}%"></span>
    </div>

    <p class="sub">
        {{ $stats['completed'] }} of
        {{ $stats['total'] }} tasks completed.
    </p>

</section>

<section class="panel">

    <div class="panel-head">
        <h2>Recently Added Tasks</h2>

        <a class="muted" href="{{ route('tasks.index') }}">
            View All →
        </a>
    </div>

    @if($recentTasks->isEmpty())

        <div class="empty">
            No tasks yet. Create your first task!
        </div>

    @else

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Due Date</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($recentTasks as $task)
                        <tr>
                            <td class="task-title">
                                {{ $task->title }}
                            </td>

                            <td>
                                <span class="pill {{ $task->priority }}">
                                    {{ $task->priority }}
                                </span>
                            </td>

                            <td>
                                <span class="pill {{ $task->status }}">
                                    {{ str_replace('_', ' ', $task->status) }}
                                </span>
                            </td>

                            <td>
                                {{ $task->due_date?->format('M d, Y') ?? '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @endif

</section>

@endsection