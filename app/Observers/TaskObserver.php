<?php

namespace App\Observers;

use App\Models\Task;
use App\Models\TaskActivity;

class TaskObserver
{
    public function created(Task $task): void
    {
        $this->record($task, 'created');
    }

    public function updated(Task $task): void
    {
        if ($task->wasChanged(['title', 'description', 'priority', 'status', 'due_date'])) {
            $this->record($task, $task->wasChanged('status') && $task->status === 'completed'
                ? 'completed'
                : 'updated');
        }
    }

    public function deleted(Task $task): void
    {
        $this->record($task, 'deleted', taskId: null);
    }

    private function record(Task $task, string $action, ?int $taskId = null): void
    {
        TaskActivity::create([
            'user_id' => $task->user_id,
            'task_id' => $taskId ?? ($action === 'deleted' ? null : $task->getKey()),
            'task_title' => $task->title,
            'action' => $action,
        ]);
    }
}
