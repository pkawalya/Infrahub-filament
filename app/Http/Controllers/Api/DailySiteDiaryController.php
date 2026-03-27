<?php

namespace App\Http\Controllers\Api;

use App\Models\DailySiteDiary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DailySiteDiaryController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $cid = $request->user()->company_id;

        $diaries = DailySiteDiary::query()
            ->where('company_id', $cid)
            ->when($request->project_id, fn($q, $id) => $q->where('cde_project_id', $id))
            ->when($request->status, fn($q, $s) => $s === 'pending' ? $q->whereNull('approved_by') : $q->whereNotNull('approved_by'))
            ->with(['project:id,name', 'preparedBy:id,name', 'approvedBy:id,name'])
            ->orderBy('diary_date', 'desc')
            ->paginate($request->per_page ?? 20);

        return $this->paginated($diaries);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'cde_project_id' => 'required|exists:cde_projects,id',
            'diary_date' => 'required|date',
            'weather' => 'nullable|string|max:50',
            'temperature' => 'nullable|numeric',
            'workforce_own' => 'nullable|integer|min:0',
            'workforce_sub' => 'nullable|integer|min:0',
            'equipment_on_site' => 'nullable|integer|min:0',
            'work_performed' => 'nullable|string',
            'issues_delays' => 'nullable|string',
            'safety_notes' => 'nullable|string',
            'visitor_log' => 'nullable|string',
        ]);

        $data['company_id']  = $request->user()->company_id;
        $data['prepared_by'] = $request->user()->id;
        $diary = DailySiteDiary::create($data);

        return $this->success($diary->load('project:id,name'), 'Site diary created', 201);
    }

    public function show(Request $request, DailySiteDiary $diary): JsonResponse
    {
        abort_if($diary->company_id !== $request->user()->company_id, 403);
        return $this->success($diary->load(['project:id,name', 'preparedBy:id,name', 'approvedBy:id,name']));
    }

    public function update(Request $request, DailySiteDiary $diary): JsonResponse
    {
        abort_if($diary->company_id !== $request->user()->company_id, 403);
        if ($diary->approved_by) {
            return $this->error('Cannot edit an approved diary', 422);
        }

        $data = $request->validate([
            'weather' => 'nullable|string|max:50',
            'temperature' => 'nullable|numeric',
            'workforce_own' => 'nullable|integer|min:0',
            'workforce_sub' => 'nullable|integer|min:0',
            'equipment_on_site' => 'nullable|integer|min:0',
            'work_performed' => 'nullable|string',
            'issues_delays' => 'nullable|string',
            'safety_notes' => 'nullable|string',
        ]);

        $diary->update($data);

        return $this->success($diary->fresh());
    }

    public function approve(Request $request, DailySiteDiary $diary): JsonResponse
    {
        if ($diary->approved_by) {
            return $this->error('Already approved', 422);
        }

        $diary->update([
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        return $this->success($diary->fresh()->load('approvedBy:id,name'), 'Diary approved');
    }
}
