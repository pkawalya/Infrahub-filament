<?php

namespace App\Http\Controllers\Api;

use App\Models\CdeProject;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends BaseApiController
{
    public function index(Request $request, CdeProject $project): JsonResponse
    {
        $this->authorizeProject($request, $project);

        $query = $project->tasks()->with(['assignee:id,name']);

        if ($request->filled('status'))
            $query->where('status', $request->status);
        if ($request->filled('priority'))
            $query->where('priority', $request->priority);
        if ($request->filled('assigned_to'))
            $query->where('assigned_to', $request->assigned_to);
        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        return $this->paginated(
            $query->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
                ->paginate($request->per_page ?? 20)
        );
    }

    public function store(Request $request, CdeProject $project): JsonResponse
    {
        $this->authorizeProject($request, $project);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'status' => 'nullable|string|in:todo,in_progress,review,done,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'progress' => 'nullable|integer|between:0,100',
        ]);

        $task = Task::create([
            ...$data,
            'company_id' => $request->user()->company_id,
            'cde_project_id' => $project->id,
            'created_by' => $request->user()->id,
            'status' => $data['status'] ?? 'todo',
            'priority' => $data['priority'] ?? 'medium',
            'progress' => $data['progress'] ?? 0,
        ]);

        return $this->success($task, 'Task created', 201);
    }

    public function show(Request $request, CdeProject $project, Task $task): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeTask($project, $task);

        return $this->success($task->load(['assignee:id,name']));
    }

    public function update(Request $request, CdeProject $project, Task $task): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeTask($project, $task);

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'status' => 'nullable|string|in:todo,in_progress,review,done,cancelled',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'actual_hours' => 'nullable|numeric|min:0',
            'progress' => 'nullable|integer|between:0,100',
        ]);

        $task->update($data);

        return $this->success($task->fresh(), 'Task updated');
    }

    public function destroy(Request $request, CdeProject $project, Task $task): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeTask($project, $task);

        $task->delete();

        return $this->success(message: 'Task deleted');
    }

    /**
     * PATCH /api/v1/projects/{project}/tasks/{task}/progress
     * Quick-update progress percentage.
     */
    public function updateProgress(Request $request, CdeProject $project, Task $task): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeTask($project, $task);

        $data = $request->validate([
            'progress' => 'required|integer|between:0,100',
        ]);

        $updates = ['progress' => $data['progress']];

        // Auto-transition status based on progress
        if ($data['progress'] === 100 && $task->status !== 'done') {
            $updates['status'] = 'done';
        } elseif ($data['progress'] > 0 && $task->status === 'todo') {
            $updates['status'] = 'in_progress';
        }

        $task->update($updates);

        return $this->success($task->fresh(), 'Progress updated');
    }

    private function authorizeProject(Request $request, CdeProject $project): void
    {
        if ($project->company_id !== $request->user()->company_id) {
            abort(403, 'Access denied');
        }
    }

    private function authorizeTask(CdeProject $project, Task $task): void
    {
        if ($task->cde_project_id !== $project->id) {
            abort(404, 'Task not found in this project');
        }
    }
}
