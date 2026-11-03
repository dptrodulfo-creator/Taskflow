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

    <form method="GET" action="{{ route('tasks.index') }}" class="filters">

        <div class="field search">
            <label>Search Tasks</label>

            <input
                id="task-search"
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search by task title..."
            >
        </div>

        <div class="field">
            <label>Status</label>

            <select name="status">
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
            <label>Priority</label>

            <select name="priority">
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

        <button class="btn" style="margin-bottom:16px">
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
                                <span class="pill {{ $task->status }}">
                                    {{ str_replace('_', ' ', $task->status) }}
                                </span>
                            </td>

                            <td>
                                {{ $task->due_date?->format('M d, Y') ?? '—' }}
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