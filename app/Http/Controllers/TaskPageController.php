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
        ]);

        $tasks = $request->user()
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
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('task.index', compact('tasks'));
    }

    public function create(): View
    {
        return view('task.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->user()->tasks()->create($request->validate($this->rules()));

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
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
