<?php

namespace App\Http\Controllers\Api;

use App\Models\CdeProject;
use App\Models\DocumentSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentSubmissionController extends BaseApiController
{
    public function index(Request $request, CdeProject $project): JsonResponse
    {
        $this->authorizeProject($request, $project);

        $query = DocumentSubmission::where('cde_project_id', $project->id)
            ->with(['submitter:id,name', 'reviewer:id,name', 'document:id,document_number,title']);

        if ($request->filled('status'))
            $query->where('status', $request->status);
        if ($request->filled('stage'))
            $query->where('stage', $request->stage);

        return $this->paginated(
            $query->orderByRaw("FIELD(status, 'rejected','overdue','pending','submitted','approved','waived')")
                ->orderBy('due_date')
                ->paginate($request->per_page ?? 20)
        );
    }

    public function store(Request $request, CdeProject $project): JsonResponse
    {
        $this->authorizeProject($request, $project);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discipline' => 'nullable|string',
            'stage' => 'required|string',
            'due_date' => 'nullable|date',
            'cde_document_id' => 'nullable|exists:cde_documents,id',
        ]);

        $submission = DocumentSubmission::create([
            ...$data,
            'company_id' => $request->user()->company_id,
            'cde_project_id' => $project->id,
            'status' => 'pending',
        ]);

        return $this->success($submission, 'Document submission created', 201);
    }

    public function show(Request $request, CdeProject $project, DocumentSubmission $submission): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeSubmission($project, $submission);

        return $this->success($submission->load(['submitter:id,name', 'reviewer:id,name', 'document:id,document_number,title']));
    }

    public function update(Request $request, CdeProject $project, DocumentSubmission $submission): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeSubmission($project, $submission);

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'discipline' => 'nullable|string',
            'stage' => 'sometimes|string',
            'due_date' => 'nullable|date',
            'cde_document_id' => 'nullable|exists:cde_documents,id',
        ]);

        $submission->update($data);

        return $this->success($submission->fresh(), 'Document submission updated');
    }

    public function destroy(Request $request, CdeProject $project, DocumentSubmission $submission): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeSubmission($project, $submission);

        $submission->delete();

        return $this->success(message: 'Document submission deleted');
    }

    public function submit(Request $request, CdeProject $project, DocumentSubmission $submission): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeSubmission($project, $submission);

        if (!in_array($submission->status, ['pending', 'rejected'])) {
            return $this->error("Cannot submit a document with status '{$submission->status}'", 422);
        }

        $data = $request->validate([
            'file_path' => 'nullable|string',
            'review_notes' => 'nullable|string|max:1000',
        ]);

        $submission->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'submitted_by' => $request->user()->id,
            'file_path' => $data['file_path'] ?? $submission->file_path,
            'review_notes' => $data['review_notes'] ?? null,
        ]);

        return $this->success($submission->fresh(), 'Document submitted');
    }

    public function approve(Request $request, CdeProject $project, DocumentSubmission $submission): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeSubmission($project, $submission);

        if ($submission->status !== 'submitted') {
            return $this->error("Only submitted documents can be approved", 422);
        }

        $submission->update([
            'status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => $request->user()->id,
        ]);

        // Auto-approve the linked document if under review
        if ($submission->cde_document_id && $submission->document && $submission->document->status === 'under_review') {
            $submission->document->transitionTo('approved');
        }

        return $this->success($submission->fresh(), 'Document submission approved');
    }

    public function reject(Request $request, CdeProject $project, DocumentSubmission $submission): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeSubmission($project, $submission);

        if ($submission->status !== 'submitted') {
            return $this->error("Only submitted documents can be rejected", 422);
        }

        $data = $request->validate([
            'rejection_reason' => 'required|string|max:2000',
        ]);

        $submission->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
            'reviewed_by' => $request->user()->id,
            'rejection_reason' => $data['rejection_reason'],
        ]);

        // Set linked document back to revision if under review
        if ($submission->cde_document_id && $submission->document && $submission->document->status === 'under_review') {
            $submission->document->transitionTo('revision');
        }

        return $this->success($submission->fresh(), 'Document submission rejected');
    }

    private function authorizeProject(Request $request, CdeProject $project): void
    {
        if ($project->company_id !== $request->user()->company_id) {
            abort(403, 'Access denied');
        }
    }

    private function authorizeSubmission(CdeProject $project, DocumentSubmission $submission): void
    {
        if ($submission->cde_project_id !== $project->id) {
            abort(404, 'Document submission not found in this project');
        }
    }
}
