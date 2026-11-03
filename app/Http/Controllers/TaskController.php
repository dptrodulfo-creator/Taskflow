<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return TaskResource::collection($request->user()->tasks()->latest()->paginate(15));
    }

    public function store(Request $request): JsonResponse
    {
        $task = $request->user()->tasks()->create($request->validate($this->rules()));

        return (new TaskResource($task))->response()->setStatusCode(201);
    }

    public function show(Request $request, int $task): TaskResource
    {
        return new TaskResource($request->user()->tasks()->findOrFail($task));
    }

    public function update(Request $request, int $task): TaskResource
    {
        $ownedTask = $request->user()->tasks()->findOrFail($task);
        $ownedTask->update($request->validate($this->rules(updating: true)));

        return new TaskResource($ownedTask->refresh());
    }

    public function destroy(Request $request, int $task): Response
    {
        $request->user()->tasks()->findOrFail($task)->delete();

        return response()->noContent();
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rules(bool $updating = false): array
    {
        return [
            'title' => [$updating ? 'sometimes' : 'required', 'string', 'max:150'],
            'description' => ['sometimes', 'nullable', 'string', 'max:3000'],
            'priority' => ['sometimes', 'required', 'string', Rule::in(['low', 'medium', 'high'])],
            'status' => ['sometimes', 'required', 'string', Rule::in(['todo', 'in_progress', 'completed'])],
            'due_date' => ['sometimes', 'nullable', 'date_format:Y-m-d'],
        ];
    }
}
