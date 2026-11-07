<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TaskPageController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', Rule::in(['todo', 'in_progress', 'completed'])],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'due_date' => ['nullable', 'date_format:Y-m-d'],
            'due_after' => ['nullable', 'date_format:Y-m-d'],
            'sort' => ['nullable', Rule::in(['created_at', 'due_date', 'priority', 'status'])],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
        ]);

        $sort = $filters['sort'] ?? 'created_at';
        $direction = $filters['direction'] ?? ($sort === 'created_at' ? 'desc' : 'asc');
        $sortColumns = [
            'created_at' => 'created_at',
            'due_date' => 'due_date',
            'priority' => 'priority',
            'status' => 'status',
        ];

        $tasksQuery = $request->user()
            ->tasks()
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where('title', 'like', "%{$search}%");
            })
            ->when($filters['status'] ?? null, function ($query, string $status): void {
                $query->where('status', $status);
            })
            ->when($filters['priority'] ?? null, function ($query, string $priority): void {
                $query->where('priority', $priority);
            })
            ->when($filters['due_date'] ?? null, function ($query, string $dueDate): void {
                $query->whereDate('due_date', $dueDate);
            })
            ->when($filters['due_after'] ?? null, function ($query, string $dueAfter): void {
                $query->whereDate('due_date', '>', $dueAfter);
            })
            ->orderBy($sortColumns[$sort], $direction);

        if ($sort !== 'created_at') {
            $tasksQuery->orderBy('created_at', 'desc');
        }

        $tasks = $tasksQuery->paginate(10)->withQueryString();

        return view('task.index', compact('direction', 'sort', 'tasks'));
    }

    public function create(): View
    {
        return view('task.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules() + [
            'return_to' => ['nullable', Rule::in(['dashboard'])],
        ]);
        $returnTo = $validated['return_to'] ?? 'tasks.index';
        unset($validated['return_to']);

        $request->user()->tasks()->create($validated);

        return redirect()->route($returnTo)->with('success', 'Task created successfully.');
    }

    public function edit(Request $request, Task $task): View
    {
        $task = $request->user()->tasks()->findOrFail($task->getKey());

        return view('task.edit', compact('task'));
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $task = $request->user()->tasks()->findOrFail($task->getKey());
        $task->update($request->validate($this->rules()));

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Request $request, Task $task): RedirectResponse
    {
        $request->user()->tasks()->findOrFail($task->getKey())->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function cycleStatus(Request $request, Task $task): RedirectResponse
    {
        $task = $request->user()->tasks()->findOrFail($task->getKey());
        $statuses = ['todo', 'in_progress', 'completed'];
        $currentIndex = array_search($task->status, $statuses, true);

        abort_if($currentIndex === false, 409, 'This task has an unsupported status.');

        $task->update([
            'status' => $statuses[($currentIndex + 1) % count($statuses)],
        ]);

        $statusLabel = match ($task->status) {
            'todo' => 'Todo',
            'in_progress' => 'In Progress',
            'completed' => 'Done',
        };

        return back()->with('success', 'Task status updated to '.$statusLabel.'.');
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:3000'],
            'priority' => ['required', 'string', Rule::in(['low', 'medium', 'high'])],
            'status' => ['required', 'string', Rule::in(['todo', 'in_progress', 'completed'])],
            'due_date' => ['nullable', 'date_format:Y-m-d'],
        ];
    }
}
