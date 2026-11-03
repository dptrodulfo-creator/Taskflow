<form
    method="POST"
    action="{{ $task
        ? route('tasks.update', $task)
        : route('tasks.store') }}"
>

    @csrf

    @if($task)
        @method('PUT')
    @endif

    <div class="field">

        <label for="title">Task Title *</label>

        <input
            id="title"
            name="title"
            type="text"
            value="{{ old('title', $task?->title) }}"
            maxlength="150"
            required
            placeholder="e.g. Finish project documentation"
        >

        @error('title')
            <span class="errors">{{ $message }}</span>
        @enderror

    </div>

    <div class="field">

        <label for="description">Description</label>

        <textarea
            id="description"
            name="description"
            maxlength="3000"
            placeholder="Add notes or details about this task..."
        >{{ old('description', $task?->description) }}</textarea>

        @error('description')
            <span class="errors">{{ $message }}</span>
        @enderror

    </div>

    <div class="form-grid">

        <div class="field">

            <label for="priority">Priority</label>

            <select id="priority" name="priority">

                @foreach(['low', 'medium', 'high'] as $priority)

                    <option
                        value="{{ $priority }}"
                        @selected(old('priority', $task?->priority ?? 'medium') === $priority)
                    >
                        {{ ucfirst($priority) }}
                    </option>

                @endforeach

            </select>

            @error('priority')
                <span class="errors">{{ $message }}</span>
            @enderror

        </div>

        <div class="field">

            <label for="status">Status</label>

            <select id="status" name="status">

                <option value="todo"
                    @selected(old('status', $task?->status ?? 'todo') === 'todo')>
                    To Do
                </option>

                <option value="in_progress"
                    @selected(old('status', $task?->status ?? 'todo') === 'in_progress')>
                    In Progress
                </option>

                <option value="completed"
                    @selected(old('status', $task?->status ?? 'todo') === 'completed')>
                    Completed
                </option>

            </select>

            @error('status')
                <span class="errors">{{ $message }}</span>
            @enderror

        </div>

        <div class="field">

            <label for="due_date">Due Date</label>

            <input
                id="due_date"
                type="date"
                name="due_date"
                value="{{ old('due_date', $task?->due_date?->format('Y-m-d')) }}"
            >

            @error('due_date')
                <span class="errors">{{ $message }}</span>
            @enderror

        </div>

    </div>

    <div class="form-actions">

        <button type="submit" class="btn">
            {{ $buttonText }}
        </button>

        <a href="{{ route('tasks.index') }}" class="btn btn-light">
            Cancel
        </a>

    </div>

</form>