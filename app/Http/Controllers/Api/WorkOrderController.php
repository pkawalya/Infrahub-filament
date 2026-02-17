<?php

namespace App\Http\Controllers\Api;

use App\Models\CdeProject;
use App\Models\WorkOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkOrderController extends BaseApiController
{
    public function index(Request $request, CdeProject $project): JsonResponse
    {
        $this->authorizeProject($request, $project);

        $query = $project->workOrders()->with(['assignee:id,name', 'creator:id,name']);

        if ($request->filled('status'))
            $query->where('status', $request->status);
        if ($request->filled('priority'))
            $query->where('priority', $request->priority);
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('wo_number', 'like', "%{$request->search}%");
            });
        }

        return $this->paginated(
            $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 20)
        );
    }

    public function store(Request $request, CdeProject $project): JsonResponse
    {
        $this->authorizeProject($request, $project);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
        ]);

        $count = $project->workOrders()->count();
        $wo = WorkOrder::create([
            ...$data,
            'wo_number' => 'WO-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT),
            'company_id' => $request->user()->company_id,
            'cde_project_id' => $project->id,
            'created_by' => $request->user()->id,
            'status' => 'draft',
            'priority' => $data['priority'] ?? 'medium',
        ]);

        return $this->success($wo, 'Work order created', 201);
    }

    public function show(Request $request, CdeProject $project, WorkOrder $workOrder): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeWo($project, $workOrder);

        return $this->success($workOrder->load(['assignee:id,name', 'creator:id,name']));
    }

    public function update(Request $request, CdeProject $project, WorkOrder $workOrder): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeWo($project, $workOrder);

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0',
            'actual_hours' => 'nullable|numeric|min:0',
        ]);

        $workOrder->update($data);

        return $this->success($workOrder->fresh(), 'Work order updated');
    }

    public function destroy(Request $request, CdeProject $project, WorkOrder $workOrder): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeWo($project, $workOrder);

        $workOrder->delete();

        return $this->success(message: 'Work order deleted');
    }

    private function authorizeProject(Request $request, CdeProject $project): void
    {
        if ($project->company_id !== $request->user()->company_id) {
            abort(403, 'Access denied');
        }
    }

    private function authorizeWo(CdeProject $project, WorkOrder $wo): void
    {
        if ($wo->cde_project_id !== $project->id) {
            abort(404, 'Work order not found in this project');
        }
    }
}
