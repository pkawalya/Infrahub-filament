<?php

namespace App\Http\Controllers\Api;

use App\Models\SafetyIncident;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SafetyController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $cid = $request->user()->company_id;

        $incidents = SafetyIncident::query()
            ->where('company_id', $cid)
            ->when($request->project_id, fn($q, $id) => $q->where('cde_project_id', $id))
            ->when($request->severity, fn($q, $s) => $q->where('severity', $s))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->with(['project:id,name', 'reportedBy:id,name'])
            ->orderBy('incident_date', 'desc')
            ->paginate($request->per_page ?? 20);

        return $this->paginated($incidents);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'cde_project_id' => 'required|exists:cde_projects,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'incident_date' => 'required|date',
            'severity' => 'required|string|in:low,medium,high,critical',
            'incident_type' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
            'injuries' => 'nullable|integer|min:0',
            'immediate_action' => 'nullable|string',
        ]);

        $data['company_id']  = $request->user()->company_id;
        $data['reported_by'] = $request->user()->id;
        $data['status']      = 'reported';
        $incident = SafetyIncident::create($data);

        return $this->success($incident, 'Safety incident reported', 201);
    }

    public function show(Request $request, SafetyIncident $incident): JsonResponse
    {
        abort_if($incident->company_id !== $request->user()->company_id, 403);
        return $this->success($incident->load(['project:id,name', 'reportedBy:id,name']));
    }

    public function update(Request $request, SafetyIncident $incident): JsonResponse
    {
        $data = $request->validate([
            'status' => 'nullable|string|in:reported,investigating,resolved,closed',
            'corrective_action' => 'nullable|string',
            'resolution_notes' => 'nullable|string',
        ]);

        $incident->update($data);

        return $this->success($incident->fresh());
    }
}
