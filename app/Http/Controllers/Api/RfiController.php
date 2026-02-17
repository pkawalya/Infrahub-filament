<?php

namespace App\Http\Controllers\Api;

use App\Models\CdeActivityLog;
use App\Models\CdeProject;
use App\Models\Rfi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RfiController extends BaseApiController
{
    public function index(Request $request, CdeProject $project): JsonResponse
    {
        $this->authorizeProject($request, $project);

        $query = $project->rfis()->with(['raisedBy:id,name', 'assignee:id,name']);

        if ($request->filled('status'))
            $query->where('status', $request->status);
        if ($request->filled('priority'))
            $query->where('priority', $request->priority);
        if ($request->boolean('overdue')) {
            $query->whereIn('status', ['open', 'under_review'])->where('due_date', '<', now());
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', "%{$request->search}%")
                    ->orWhere('rfi_number', 'like', "%{$request->search}%");
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
            'rfi_number' => 'nullable|string|max:50',
            'subject' => 'required|string|max:255',
            'question' => 'required|string',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'cost_impact' => 'nullable|string|in:yes,no,unknown',
            'schedule_impact' => 'nullable|string|in:yes,no,unknown',
        ]);

        $count = $project->rfis()->count();
        $rfi = Rfi::create([
            ...$data,
            'rfi_number' => $data['rfi_number'] ?? 'RFI-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT),
            'company_id' => $request->user()->company_id,
            'cde_project_id' => $project->id,
            'raised_by' => $request->user()->id,
            'status' => 'open',
            'priority' => $data['priority'] ?? 'medium',
        ]);

        return $this->success($rfi, 'RFI created', 201);
    }

    public function show(Request $request, CdeProject $project, Rfi $rfi): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeRfi($project, $rfi);

        return $this->success($rfi->load(['raisedBy:id,name', 'assignee:id,name']));
    }

    public function update(Request $request, CdeProject $project, Rfi $rfi): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeRfi($project, $rfi);

        $data = $request->validate([
            'subject' => 'sometimes|string|max:255',
            'question' => 'sometimes|string',
            'priority' => 'nullable|string|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'cost_impact' => 'nullable|string|in:yes,no,unknown',
            'schedule_impact' => 'nullable|string|in:yes,no,unknown',
        ]);

        $rfi->update($data);

        return $this->success($rfi->fresh(), 'RFI updated');
    }

    public function destroy(Request $request, CdeProject $project, Rfi $rfi): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeRfi($project, $rfi);

        $rfi->delete();

        return $this->success(message: 'RFI deleted');
    }

    /**
     * POST /api/v1/projects/{project}/rfis/{rfi}/answer
     */
    public function answer(Request $request, CdeProject $project, Rfi $rfi): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeRfi($project, $rfi);

        if (!in_array($rfi->status, ['open', 'under_review'])) {
            return $this->error("RFI is already {$rfi->status}", 422);
        }

        $data = $request->validate([
            'answer' => 'required|string',
        ]);

        $rfi->update([
            'answer' => $data['answer'],
            'status' => 'answered',
            'answered_at' => now(),
        ]);

        CdeActivityLog::record($rfi, 'status_changed', "RFI {$rfi->rfi_number} answered via API");

        return $this->success($rfi->fresh(), 'RFI answered');
    }

    /**
     * POST /api/v1/projects/{project}/rfis/{rfi}/close
     */
    public function close(Request $request, CdeProject $project, Rfi $rfi): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeRfi($project, $rfi);

        if ($rfi->status !== 'answered') {
            return $this->error('Only answered RFIs can be closed', 422);
        }

        $rfi->update(['status' => 'closed']);

        CdeActivityLog::record($rfi, 'status_changed', "RFI {$rfi->rfi_number} closed via API");

        return $this->success($rfi->fresh(), 'RFI closed');
    }

    private function authorizeProject(Request $request, CdeProject $project): void
    {
        if ($project->company_id !== $request->user()->company_id) {
            abort(403, 'Access denied');
        }
    }

    private function authorizeRfi(CdeProject $project, Rfi $rfi): void
    {
        if ($rfi->cde_project_id !== $project->id) {
            abort(404, 'RFI not found in this project');
        }
    }
}
