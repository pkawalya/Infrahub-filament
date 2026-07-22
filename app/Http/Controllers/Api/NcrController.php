<?php

namespace App\Http\Controllers\Api;

use App\Models\CdeDocument;
use App\Models\CdeProject;
use App\Models\Ncr;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NcrController extends BaseApiController
{
    public function index(Request $request, CdeProject $project): JsonResponse
    {
        $this->authorizeProject($request, $project);

        $query = $project->ncrs()->with(['reporter:id,name', 'assignee:id,name', 'document:id,document_number,title']);

        if ($request->filled('status'))
            $query->where('status', $request->status);
        if ($request->filled('severity'))
            $query->where('severity', $request->severity);
        if ($request->filled('type'))
            $query->where('type', $request->type);
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('ncr_number', 'like', "%{$request->search}%");
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
            'ncr_number' => 'nullable|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string|in:' . implode(',', array_keys(Ncr::$types)),
            'severity' => 'nullable|string|in:' . implode(',', array_keys(Ncr::$severities)),
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'cde_document_id' => 'nullable|exists:cde_documents,id',
        ]);

        $count = $project->ncrs()->count();
        $ncr = Ncr::create([
            ...$data,
            'ncr_number' => $data['ncr_number'] ?? 'NCR-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT),
            'company_id' => $request->user()->company_id,
            'cde_project_id' => $project->id,
            'reported_by' => $request->user()->id,
            'type' => $data['type'] ?? 'product',
            'severity' => $data['severity'] ?? 'minor',
            'status' => 'open',
        ]);

        return $this->success($ncr, 'NCR created', 201);
    }

    public function show(Request $request, CdeProject $project, Ncr $ncr): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeNcr($project, $ncr);

        return $this->success($ncr->load(['reporter:id,name', 'assignee:id,name', 'verifier:id,name', 'document:id,document_number,title']));
    }

    public function update(Request $request, CdeProject $project, Ncr $ncr): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeNcr($project, $ncr);

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|string|in:' . implode(',', array_keys(Ncr::$types)),
            'severity' => 'sometimes|string|in:' . implode(',', array_keys(Ncr::$severities)),
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'cde_document_id' => 'nullable|exists:cde_documents,id',
            'root_cause' => 'nullable|string',
            'corrective_action' => 'nullable|string',
            'preventive_action' => 'nullable|string',
        ]);

        $ncr->update($data);

        return $this->success($ncr->fresh(), 'NCR updated');
    }

    public function destroy(Request $request, CdeProject $project, Ncr $ncr): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeNcr($project, $ncr);

        $ncr->delete();

        return $this->success(message: 'NCR deleted');
    }

    public function investigate(Request $request, CdeProject $project, Ncr $ncr): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeNcr($project, $ncr);

        $data = $request->validate([
            'root_cause' => 'required|string',
        ]);

        $ncr->transitionTo('investigating');
        $ncr->update(['root_cause' => $data['root_cause']]);

        return $this->success($ncr->fresh(), 'NCR investigation started');
    }

    public function proposeCorrectiveAction(Request $request, CdeProject $project, Ncr $ncr): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeNcr($project, $ncr);

        $data = $request->validate([
            'corrective_action' => 'required|string',
            'preventive_action' => 'nullable|string',
        ]);

        $ncr->transitionTo('corrective_action');
        $ncr->update([
            'corrective_action' => $data['corrective_action'],
            'preventive_action' => $data['preventive_action'] ?? null,
        ]);

        return $this->success($ncr->fresh(), 'Corrective action proposed');
    }

    public function verify(Request $request, CdeProject $project, Ncr $ncr): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeNcr($project, $ncr);

        $data = $request->validate([
            'verification_notes' => 'required|string',
        ]);

        $ncr->transitionTo('verified');
        $ncr->update([
            'verification_notes' => $data['verification_notes'],
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return $this->success($ncr->fresh(), 'NCR verified');
    }

    public function closeNcr(Request $request, CdeProject $project, Ncr $ncr): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeNcr($project, $ncr);

        $data = $request->validate([
            'closure_notes' => 'nullable|string',
        ]);

        $ncr->transitionTo('closed');
        $ncr->update([
            'closure_notes' => $data['closure_notes'] ?? null,
            'closed_at' => now(),
        ]);

        // If linked to a document that is under review, auto-approve it
        if ($ncr->cde_document_id && $ncr->document && $ncr->document->status === 'under_review') {
            $ncr->document->transitionTo('approved');
        }

        return $this->success($ncr->fresh(), 'NCR closed');
    }

    private function authorizeProject(Request $request, CdeProject $project): void
    {
        if ($project->company_id !== $request->user()->company_id) {
            abort(403, 'Access denied');
        }
    }

    private function authorizeNcr(CdeProject $project, Ncr $ncr): void
    {
        if ($ncr->cde_project_id !== $project->id) {
            abort(404, 'NCR not found in this project');
        }
    }
}
