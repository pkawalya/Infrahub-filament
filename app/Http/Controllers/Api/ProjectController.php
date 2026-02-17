<?php

namespace App\Http\Controllers\Api;

use App\Models\CdeProject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends BaseApiController
{
    /**
     * GET /api/v1/projects
     */
    public function index(Request $request): JsonResponse
    {
        $query = CdeProject::where('company_id', $request->user()->company_id)
            ->with(['client:id,name', 'manager:id,name']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%");
            });
        }

        $projects = $query->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
            ->paginate($request->per_page ?? 15);

        return $this->paginated($projects);
    }

    /**
     * POST /api/v1/projects
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'client_id' => 'nullable|exists:clients,id',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'nullable|string|in:' . implode(',', array_keys(CdeProject::$statuses)),
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
        ]);

        $project = CdeProject::create([
            ...$data,
            'company_id' => $request->user()->company_id,
        ]);

        return $this->success($project->load(['client:id,name', 'manager:id,name']), 'Project created', 201);
    }

    /**
     * GET /api/v1/projects/{project}
     */
    public function show(Request $request, CdeProject $project): JsonResponse
    {
        $this->authorizeProject($request, $project);

        return $this->success(
            $project->load(['client:id,name', 'manager:id,name'])
        );
    }

    /**
     * PUT /api/v1/projects/{project}
     */
    public function update(Request $request, CdeProject $project): JsonResponse
    {
        $this->authorizeProject($request, $project);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'client_id' => 'nullable|exists:clients,id',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'nullable|string|in:' . implode(',', array_keys(CdeProject::$statuses)),
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'budget' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
        ]);

        $project->update($data);

        return $this->success($project->fresh()->load(['client:id,name', 'manager:id,name']), 'Project updated');
    }

    /**
     * DELETE /api/v1/projects/{project}
     */
    public function destroy(Request $request, CdeProject $project): JsonResponse
    {
        $this->authorizeProject($request, $project);

        $project->delete();

        return $this->success(message: 'Project deleted');
    }

    /**
     * GET /api/v1/projects/{project}/stats
     */
    public function stats(Request $request, CdeProject $project): JsonResponse
    {
        $this->authorizeProject($request, $project);

        return $this->success([
            'tasks' => [
                'total' => $project->tasks()->count(),
                'done' => $project->tasks()->where('status', 'done')->count(),
                'overdue' => $project->tasks()->whereNotIn('status', ['done', 'cancelled'])
                    ->where('due_date', '<', now())->count(),
            ],
            'documents' => $project->documents()->count(),
            'rfis' => [
                'total' => $project->rfis()->count(),
                'open' => $project->rfis()->whereIn('status', ['open', 'under_review'])->count(),
            ],
            'submittals' => [
                'total' => $project->submittals()->count(),
                'pending' => $project->submittals()->whereIn('status', ['pending', 'under_review'])->count(),
            ],
            'work_orders' => $project->workOrders()->count(),
            'contracts' => [
                'total' => $project->contracts()->count(),
                'value' => (float) $project->contracts()->sum('original_value'),
            ],
            'safety_incidents' => $project->safetyIncidents()->count(),
        ]);
    }

    private function authorizeProject(Request $request, CdeProject $project): void
    {
        if ($project->company_id !== $request->user()->company_id) {
            abort(403, 'You do not have access to this project.');
        }
    }
}
