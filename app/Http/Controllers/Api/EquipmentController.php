<?php

namespace App\Http\Controllers\Api;

use App\Models\EquipmentAllocation;
use App\Models\EquipmentFuelLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EquipmentController extends BaseApiController
{
    public function allocations(Request $request): JsonResponse
    {
        $items = EquipmentAllocation::query()
            ->when($request->project_id, fn($q, $id) => $q->where('cde_project_id', $id))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->with(['asset:id,name', 'operator:id,name', 'project:id,name'])
            ->orderBy('start_date', 'desc')
            ->paginate($request->per_page ?? 20);

        return $this->paginated($items);
    }

    public function storeAllocation(Request $request): JsonResponse
    {
        $data = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'cde_project_id' => 'required|exists:cde_projects,id',
            'operator_id' => 'nullable|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'daily_rate' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $data['status'] = 'active';
        $data['created_by'] = $request->user()->id;
        $alloc = EquipmentAllocation::create($data);

        return $this->success($alloc->load('asset:id,name'), 'Equipment allocated', 201);
    }

    public function fuelLogs(Request $request): JsonResponse
    {
        $logs = EquipmentFuelLog::query()
            ->when($request->project_id, fn($q, $id) => $q->where('cde_project_id', $id))
            ->when($request->asset_id, fn($q, $id) => $q->where('asset_id', $id))
            ->with(['asset:id,name'])
            ->orderBy('log_date', 'desc')
            ->paginate($request->per_page ?? 20);

        return $this->paginated($logs);
    }

    public function storeFuelLog(Request $request): JsonResponse
    {
        $data = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'cde_project_id' => 'required|exists:cde_projects,id',
            'log_date' => 'required|date',
            'liters' => 'required|numeric|min:0.1',
            'cost_per_liter' => 'nullable|numeric|min:0',
            'total_cost' => 'nullable|numeric|min:0',
            'meter_reading' => 'nullable|numeric',
            'filled_by' => 'nullable|string|max:255',
        ]);

        $data['created_by'] = $request->user()->id;
        if (!isset($data['total_cost']) && isset($data['cost_per_liter'])) {
            $data['total_cost'] = $data['liters'] * $data['cost_per_liter'];
        }

        $log = EquipmentFuelLog::create($data);

        return $this->success($log->load('asset:id,name'), 'Fuel log recorded', 201);
    }
}
