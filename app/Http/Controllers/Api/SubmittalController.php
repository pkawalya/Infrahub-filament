<?php

namespace App\Http\Controllers\Api;

use App\Models\CdeActivityLog;
use App\Models\CdeProject;
use App\Models\Submittal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubmittalController extends BaseApiController
{
    public function index(Request $request, CdeProject $project): JsonResponse
    {
        $this->authorizeProject($request, $project);

        $query = $project->submittals()->with(['submitter:id,name', 'reviewer:id,name']);

        if ($request->filled('status'))
            $query->where('status', $request->status);
        if ($request->filled('type'))
            $query->where('type', $request->type);
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('submittal_number', 'like', "%{$request->search}%");
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
            'submittal_number' => 'nullable|string|max:50',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:' . implode(',', array_keys(Submittal::$types)),
            'reviewer_id' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        $count = $project->submittals()->count();
        $sub = Submittal::create([
            ...$data,
            'submittal_number' => $data['submittal_number'] ?? 'SUB-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT),
            'company_id' => $request->user()->company_id,
            'cde_project_id' => $project->id,
            'submitted_by' => $request->user()->id,
            'status' => 'pending',
            'current_revision' => '0',
        ]);

        return $this->success($sub, 'Submittal created', 201);
    }

    public function show(Request $request, CdeProject $project, Submittal $submittal): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeSubmittal($project, $submittal);

        return $this->success($submittal->load(['submitter:id,name', 'reviewer:id,name']));
    }

    public function update(Request $request, CdeProject $project, Submittal $submittal): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeSubmittal($project, $submittal);

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'type' => 'sometimes|string|in:' . implode(',', array_keys(Submittal::$types)),
            'reviewer_id' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        $submittal->update($data);

        return $this->success($submittal->fresh(), 'Submittal updated');
    }

    public function destroy(Request $request, CdeProject $project, Submittal $submittal): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeSubmittal($project, $submittal);

        $submittal->delete();

        return $this->success(message: 'Submittal deleted');
    }

    /**
     * POST /api/v1/projects/{project}/submittals/{submittal}/review
     */
    public function review(Request $request, CdeProject $project, Submittal $submittal): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeSubmittal($project, $submittal);

        if (!in_array($submittal->status, ['pending', 'under_review'])) {
            return $this->error("Cannot review a submittal with status '{$submittal->status}'", 422);
        }

        $data = $request->validate([
            'decision' => 'required|string|in:approved,approved_as_noted,revise_resubmit,rejected',
            'review_comments' => 'nullable|string|max:2000',
        ]);

        $submittal->update([
            'status' => $data['decision'],
            'review_comments' => $data['review_comments'] ?? null,
            'reviewed_at' => now(),
            'reviewer_id' => $request->user()->id,
        ]);

        $label = Submittal::$statuses[$data['decision']] ?? $data['decision'];
        CdeActivityLog::record($submittal, $data['decision'], "Submittal {$submittal->submittal_number}: {$label} via API");

        return $this->success($submittal->fresh(), "Submittal {$label}");
    }

    /**
     * POST /api/v1/projects/{project}/submittals/{submittal}/resubmit
     */
    public function resubmit(Request $request, CdeProject $project, Submittal $submittal): JsonResponse
    {
        $this->authorizeProject($request, $project);
        $this->authorizeSubmittal($project, $submittal);

        if ($submittal->status !== 'revise_resubmit') {
            return $this->error('Only submittals marked for revision can be resubmitted', 422);
        }

        $rev = intval($submittal->current_revision) + 1;
        $submittal->update([
            'current_revision' => (string) $rev,
            'status' => 'pending',
            'reviewed_at' => null,
            'review_comments' => null,
        ]);

        CdeActivityLog::record($submittal, 'submitted', "Submittal {$submittal->submittal_number} Rev {$rev} resubmitted via API");

        return $this->success($submittal->fresh(), "Submittal resubmitted as Rev {$rev}");
    }

    private function authorizeProject(Request $request, CdeProject $project): void
    {
        if ($project->company_id !== $request->user()->company_id) {
            abort(403, 'Access denied');
        }
    }

    private function authorizeSubmittal(CdeProject $project, Submittal $submittal): void
    {
        if ($submittal->cde_project_id !== $project->id) {
            abort(404, 'Submittal not found in this project');
        }
    }
}
