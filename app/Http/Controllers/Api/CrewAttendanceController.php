<?php

namespace App\Http\Controllers\Api;

use App\Models\CrewAttendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CrewAttendanceController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $attendance = CrewAttendance::query()
            ->when($request->project_id, fn($q, $id) => $q->where('cde_project_id', $id))
            ->when($request->date, fn($q, $d) => $q->whereDate('attendance_date', $d))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->with(['worker:id,name', 'project:id,name'])
            ->orderBy('attendance_date', 'desc')
            ->paginate($request->per_page ?? 30);

        return $this->paginated($attendance);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'cde_project_id' => 'required|exists:cde_projects,id',
            'attendance_date' => 'required|date',
            'status' => 'required|string|in:present,absent,late,half_day,leave',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'overtime_hours' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $data['recorded_by'] = $request->user()->id;
        $record = CrewAttendance::create($data);

        return $this->success($record->load('worker:id,name'), 'Attendance recorded', 201);
    }

    public function show(CrewAttendance $attendance): JsonResponse
    {
        return $this->success($attendance->load(['worker:id,name', 'project:id,name']));
    }

    public function today(Request $request): JsonResponse
    {
        $attendance = CrewAttendance::query()
            ->whereDate('attendance_date', now())
            ->when($request->project_id, fn($q, $id) => $q->where('cde_project_id', $id))
            ->with('worker:id,name')
            ->get();

        $summary = [
            'date' => now()->toDateString(),
            'total' => $attendance->count(),
            'present' => $attendance->where('status', 'present')->count(),
            'absent' => $attendance->where('status', 'absent')->count(),
            'late' => $attendance->where('status', 'late')->count(),
            'records' => $attendance,
        ];

        return $this->success($summary);
    }
}
