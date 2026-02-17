<?php

namespace App\Http\Controllers\Api;

use App\Models\CdeActivityLog;
use App\Models\CdeDocument;
use App\Models\CdeProject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends BaseApiController
{
    /**
     * GET /api/v1/projects/{project}/documents
     */
    public function index(Request $request, CdeProject $project): JsonResponse
    {
        $this->authorizeProject($request, $project);

        $query = $project->documents()->with(['folder:id,name', 'uploadedBy:id,name']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('discipline')) {
            $query->where('discipline', $request->discipline);
        }
        if ($request->filled('folder_id')) {
            $query->where('cde_folder_id', $request->folder_id);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('document_number', 'like', "%{$request->search}%");
            });
        }

        $docs = $query->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
            ->paginate($request->per_page ?? 20);

        return $this->paginated($docs);
    }

    /**
     * POST /api/v1/projects/{project}/documents
     */
    public function store(Request $request, CdeProject $project): JsonResponse
    {
        $this->authorizeProject($request, $project);

        $data = $request->validate([
            'document_number' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discipline' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:50',
            'status' => 'nullable|string|in:' . implode(',', array_keys(CdeDocument::$statuses ?? ['draft' => 'Draft'])),
            'revision' => 'nullable|string|max:10',
            'cde_folder_id' => 'nullable|exists:cde_folders,id',
        ]);

        $doc = CdeDocument::create([
            ...$data,
            'company_id' => $request->user()->company_id,
            'cde_project_id' => $project->id,
            'uploaded_by' => $request->user()->id,
            'status' => $data['status'] ?? 'wip',
        ]);

        return $this->success($doc, 'Document created', 201);
    }

    /**
     * GET /api/v1/projects/{project}/documents/{document}
     */
    public function show(Request $request, CdeProject $project, CdeDocument $document): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeDocument($project, $document);

        return $this->success(
            $document->load(['folder:id,name', 'uploadedBy:id,name'])
        );
    }

    /**
     * PUT /api/v1/projects/{project}/documents/{document}
     */
    public function update(Request $request, CdeProject $project, CdeDocument $document): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeDocument($project, $document);

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'discipline' => 'nullable|string|max:50',
            'type' => 'nullable|string|max:50',
            'revision' => 'nullable|string|max:10',
            'cde_folder_id' => 'nullable|exists:cde_folders,id',
        ]);

        $document->update($data);

        return $this->success($document->fresh(), 'Document updated');
    }

    /**
     * DELETE /api/v1/projects/{project}/documents/{document}
     */
    public function destroy(Request $request, CdeProject $project, CdeDocument $document): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeDocument($project, $document);

        $document->delete();

        return $this->success(message: 'Document deleted');
    }

    /* ═══════════════════════════════════════════════════════════
     *  Document Workflow Actions
     * ═══════════════════════════════════════════════════════════ */

    /**
     * POST /api/v1/projects/{project}/documents/{document}/submit-for-review
     *
     * Transition: WIP / Revision → Under Review
     */
    public function submitForReview(Request $request, CdeProject $project, CdeDocument $document): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeDocument($project, $document);

        $allowedFrom = ['wip', 'draft', 'revision'];
        if (!in_array($document->status, $allowedFrom)) {
            return $this->error(
                "Cannot submit document with status '{$document->status}'. Allowed statuses: " . implode(', ', $allowedFrom),
                422
            );
        }

        $request->validate([
            'reviewer_notes' => 'nullable|string|max:1000',
        ]);

        $oldStatus = $document->status;
        $document->update(['status' => 'under_review']);

        CdeActivityLog::record(
            $document,
            'submitted',
            "Document {$document->document_number} submitted for review",
            ['status' => ['from' => $oldStatus, 'to' => 'under_review']],
        );

        return $this->success($document->fresh(), 'Document submitted for review');
    }

    /**
     * POST /api/v1/projects/{project}/documents/{document}/approve
     *
     * Transition: Under Review → Approved (Published)
     */
    public function approve(Request $request, CdeProject $project, CdeDocument $document): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeDocument($project, $document);

        if ($document->status !== 'under_review') {
            return $this->error(
                "Only documents under review can be approved. Current status: '{$document->status}'",
                422
            );
        }

        $request->validate([
            'comments' => 'nullable|string|max:2000',
        ]);

        $document->update(['status' => 'approved']);

        CdeActivityLog::record(
            $document,
            'approved',
            "Document {$document->document_number} approved" . ($request->comments ? ": {$request->comments}" : ''),
            ['status' => ['from' => 'under_review', 'to' => 'approved']],
        );

        return $this->success($document->fresh(), 'Document approved');
    }

    /**
     * POST /api/v1/projects/{project}/documents/{document}/reject
     *
     * Transition: Under Review → Revision (needs rework)
     */
    public function reject(Request $request, CdeProject $project, CdeDocument $document): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeDocument($project, $document);

        if ($document->status !== 'under_review') {
            return $this->error(
                "Only documents under review can be rejected. Current status: '{$document->status}'",
                422
            );
        }

        $request->validate([
            'reason' => 'required|string|max:2000',
        ]);

        $document->update(['status' => 'revision']);

        CdeActivityLog::record(
            $document,
            'rejected',
            "Document {$document->document_number} rejected: {$request->reason}",
            ['status' => ['from' => 'under_review', 'to' => 'revision'], 'reason' => $request->reason],
        );

        return $this->success($document->fresh(), 'Document rejected and sent back for revision');
    }

    /* ─── Authorization helpers ─────────────────────────────── */

    private function authorizeProject(Request $request, CdeProject $project): void
    {
        if ($project->company_id !== $request->user()->company_id) {
            abort(403, 'You do not have access to this project.');
        }
    }

    private function authorizeDocument(CdeProject $project, CdeDocument $document): void
    {
        if ($document->cde_project_id !== $project->id) {
            abort(404, 'Document not found in this project.');
        }
    }
}
