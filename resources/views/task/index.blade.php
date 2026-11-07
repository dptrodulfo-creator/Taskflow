@extends('layouts.taskflow')

@section('title', 'My Tasks')

@section('content')

<div class="topbar">
    <div>
        <h1>My Tasks</h1>
        <p class="sub">Organize your work and keep moving forward.</p>
    </div>

    <a href="{{ route('tasks.create') }}" class="btn">
        + Add Task
    </a>
</div>

<section class="panel">

    @if (session('success'))
        <div id="delete-confirmation" class="task-notification" role="status" x-data="{ visible: true }" x-show="visible">
            <span>{{ session('success') }}</span>
            <button type="button" aria-label="Dismiss notification" @click="visible = false">Dismiss notification</button>
        </div>
    @endif

    <form method="GET" action="{{ route('tasks.index') }}" class="filters">

        <div class="field search">
            <label for="task-search">Search Tasks</label>

            <input
                id="task-search"
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search by task title..."
            >
        </div>

        <div class="field">
            <label for="task-status-filter">Status</label>

            <select id="task-status-filter" name="status">
                <option value="">All Statuses</option>

                <option value="todo" @selected(request('status') === 'todo')>
                    To Do
                </option>

                <option value="in_progress" @selected(request('status') === 'in_progress')>
                    In Progress
                </option>

                <option value="completed" @selected(request('status') === 'completed')>
                    Completed
                </option>
            </select>
        </div>

        <div class="field">
            <label for="task-priority-filter">Priority</label>

            <select id="task-priority-filter" name="priority">
                <option value="">All Priorities</option>

                @foreach(['low', 'medium', 'high'] as $priority)
                    <option value="{{ $priority }}"
                        @selected(request('priority') === $priority)>
                        {{ ucfirst($priority) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label for="due-date-filter">Due date</label>
            <input
                id="due-date-filter"
                type="date"
                name="due_date"
                value="{{ request('due_date') }}"
            >
        </div>
        <div class="field">
            <label for="due-after-filter">Due after</label>
            <input
                id="due-after-filter"
                type="date"
                name="due_after"
                value="{{ request('due_after') }}"
            >
        </div>

        <div class="field">
            <label for="task-sort-filter">Sort by</label>
            <select id="task-sort-filter" name="sort">
                <option value="created_at" @selected($sort === 'created_at')>Recently added</option>
                <option value="due_date" @selected($sort === 'due_date')>Due date</option>
                <option value="priority" @selected($sort === 'priority')>Priority</option>
                <option value="status" @selected($sort === 'status')>Status</option>
            </select>
        </div>

        <div class="field">
            <label for="task-direction-filter">Order</label>
            <select id="task-direction-filter" name="direction">
                <option value="asc" @selected($direction === 'asc')>Ascending</option>
                <option value="desc" @selected($direction === 'desc')>Descending</option>
            </select>
        </div>

        <button class="btn">
            Filter
        </button>

    </form>

    @if($tasks->isEmpty())

        <div class="empty">
            No tasks found. Add a new task to get started.
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
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($tasks as $task)

                        <tr>

                            <td>
                                <div class="task-title">
                                    {{ $task->title }}
                                </div>

                                <div class="muted" style="font-size:12px;margin-top:4px">
                                    {{ \Illuminate\Support\Str::limit($task->description, 65) }}
                                </div>
                            </td>

                            <td>
                                <span class="pill {{ $task->priority }}">
                                    {{ $task->priority }}
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
                                    —
                                @endif
                            </td>

                            <td>
                                <div class="actions">

                                    <a href="{{ route('tasks.edit', $task) }}"
                                       class="btn btn-light">
                                        Edit
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('tasks.destroy', $task) }}"
                                        class="delete-task-form"
                                        data-confirm-delete
                                        data-task-title="{{ $task->title }}"
                                        onsubmit="return confirm('Are you sure you want to delete this task?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-danger">
                                            Delete
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        <div style="margin-top:18px">
            {{ $tasks->links() }}
        </div>

    @endif

</section>

@endsection