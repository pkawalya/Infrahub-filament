<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CrewAttendance;
use App\Models\DailySiteDiary;
use App\Models\SafetyIncident;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Handles sync of offline-collected data from field workers.
 * Accepts individual records and creates them in the database.
 */
class OfflineSyncController extends Controller
{
    /**
     * Return list of workers for offline attendance dropdown cache.
     */
    public function workers(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([], 401);
        }

        $workers = User::where('company_id', $user->company_id)
            ->where('is_active', true)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($workers);
    }

    /**
     * Map of URL resource slugs to Eloquent model classes.
     * Only whitelisted resources can be synced offline.
     */
    private const RESOURCE_MAP = [
        'tasks' => \App\Models\Task::class,
        'work-orders' => \App\Models\WorkOrder::class,
        'daily-site-diaries' => \App\Models\DailySiteDiary::class,
        'crew-attendances' => \App\Models\CrewAttendance::class,
        'safety-incidents' => \App\Models\SafetyIncident::class,
        'invoices' => \App\Models\Invoice::class,
        'assets' => \App\Models\Asset::class,
        'clients' => \App\Models\Client::class,
        'subcontractors' => \App\Models\Subcontractor::class,
        'tenders' => \App\Models\Tender::class,
        'drawings' => \App\Models\Drawing::class,
        'payment-certificates' => \App\Models\PaymentCertificate::class,
        'cde-projects' => \App\Models\CdeProject::class,
        'change-orders' => \App\Models\ChangeOrder::class,
        'snag-items' => \App\Models\SnagItem::class,
    ];

    /**
     * Generic sync endpoint — handles ANY whitelisted resource type.
     *
     * Accepts: { resource: 'tasks', action: 'create'|'update', record_id: null|int, data: {...} }
     */
    public function syncGeneric(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $resource = $request->input('resource');
        $action = $request->input('action', 'create');
        $recordId = $request->input('record_id');
        $data = $request->input('data', []);

        // Validate resource type is whitelisted
        if (!isset(self::RESOURCE_MAP[$resource])) {
            return response()->json([
                'success' => false,
                'message' => "Unknown resource type: {$resource}",
                'allowed' => array_keys(self::RESOURCE_MAP),
            ], 422);
        }

        $modelClass = self::RESOURCE_MAP[$resource];

        try {
            // Get fillable fields from the model to filter out junk
            $model = new $modelClass;
            $fillable = $model->getFillable();

            // Filter data to only include fillable fields
            $cleanData = [];
            foreach ($data as $key => $value) {
                if (in_array($key, $fillable) && $value !== null && $value !== '') {
                    $cleanData[$key] = $value;
                }
            }

            // Auto-inject company_id if the model has it
            if (in_array('company_id', $fillable)) {
                $cleanData['company_id'] = $user->company_id;
            }

            // Auto-inject user references based on common patterns
            $userFieldMap = [
                'created_by' => $user->id,
                'reported_by' => $user->id,
                'prepared_by' => $user->id,
                'assigned_to' => null, // Don't override
            ];
            foreach ($userFieldMap as $field => $value) {
                if ($value && in_array($field, $fillable) && !isset($cleanData[$field])) {
                    $cleanData[$field] = $value;
                }
            }

            if ($action === 'update' && $recordId) {
                // Update existing record (scoped to company)
                $record = $modelClass::where('id', $recordId);

                // Scope to company if applicable
                if (in_array('company_id', $fillable)) {
                    $record->where('company_id', $user->company_id);
                }

                $record = $record->first();

                if (!$record) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Record not found or access denied',
                    ], 404);
                }

                $record->update($cleanData);

                Log::info("Offline sync: {$resource} updated", [
                    'id' => $record->id,
                    'user' => $user->id,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => ucfirst(str_replace('-', ' ', $resource)) . ' updated',
                    'id' => $record->id,
                ]);
            } else {
                // Create new record
                $record = $modelClass::create($cleanData);

                Log::info("Offline sync: {$resource} created", [
                    'id' => $record->id,
                    'user' => $user->id,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => ucfirst(str_replace('-', ' ', $resource)) . ' created',
                    'id' => $record->id,
                ], 201);
            }
        } catch (\Throwable $e) {
            Log::error("Offline sync failed: {$resource}", [
                'error' => $e->getMessage(),
                'user' => $user->id,
                'data' => $cleanData ?? [],
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync a single site diary record from offline storage.
     */
    public function syncSiteDiary(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $validator = Validator::make($request->all(), [
            'cde_project_id' => 'required|integer|exists:cde_projects,id',
            'diary_date' => 'required|date',
            'weather' => 'nullable|string|max:50',
            'temperature' => 'nullable|numeric',
            'workers_on_site' => 'nullable|integer|min:0',
            'subcontractor_workers' => 'nullable|integer|min:0',
            'equipment_on_site' => 'nullable|integer|min:0',
            'work_performed' => 'nullable|string',
            'work_planned_tomorrow' => 'nullable|string',
            'delays' => 'nullable|string',
            'safety_observations' => 'nullable|string',
            'quality_observations' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $validator->validated();
            $data['company_id'] = $user->company_id;
            $data['prepared_by'] = $user->id;

            // Prevent duplicate: check if same project + date + preparer exists
            $existing = DailySiteDiary::where('company_id', $user->company_id)
                ->where('cde_project_id', $data['cde_project_id'])
                ->where('diary_date', $data['diary_date'])
                ->where('prepared_by', $user->id)
                ->first();

            if ($existing) {
                // Update instead of duplicate
                $existing->update($data);
                return response()->json([
                    'success' => true,
                    'message' => 'Site diary updated (merged with existing)',
                    'id' => $existing->id,
                ]);
            }

            $record = DailySiteDiary::create($data);

            Log::info('Offline sync: Site diary created', [
                'id' => $record->id,
                'user' => $user->id,
                'project' => $data['cde_project_id'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Site diary synced successfully',
                'id' => $record->id,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Offline sync failed: site diary', [
                'error' => $e->getMessage(),
                'user' => $user->id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync a single attendance record from offline storage.
     */
    public function syncAttendance(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'cde_project_id' => 'nullable|integer|exists:cde_projects,id',
            'attendance_date' => 'required|date',
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i',
            'hours_worked' => 'nullable|numeric|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'status' => 'required|string|max:20',
            'site_location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $validator->validated();
            $data['company_id'] = $user->company_id;

            // Prevent duplicate: same worker + date
            $existing = CrewAttendance::where('company_id', $user->company_id)
                ->where('user_id', $data['user_id'])
                ->where('attendance_date', $data['attendance_date'])
                ->first();

            if ($existing) {
                $existing->update($data);
                return response()->json([
                    'success' => true,
                    'message' => 'Attendance updated (merged with existing)',
                    'id' => $existing->id,
                ]);
            }

            $record = CrewAttendance::create($data);

            Log::info('Offline sync: Attendance created', [
                'id' => $record->id,
                'user' => $user->id,
                'worker' => $data['user_id'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Attendance synced successfully',
                'id' => $record->id,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Offline sync failed: attendance', [
                'error' => $e->getMessage(),
                'user' => $user->id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync a single safety incident record from offline storage.
     */
    public function syncSafetyIncident(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $validator = Validator::make($request->all(), [
            'cde_project_id' => 'nullable|integer|exists:cde_projects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'nullable|string|max:50',
            'severity' => 'nullable|string|max:20',
            'status' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'incident_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $data = $validator->validated();
            $data['company_id'] = $user->company_id;
            $data['reported_by'] = $user->id;
            $data['status'] = $data['status'] ?? 'reported';

            // Generate incident number
            $count = SafetyIncident::where('company_id', $user->company_id)->count() + 1;
            $data['incident_number'] = 'INC-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $record = SafetyIncident::create($data);

            Log::info('Offline sync: Safety incident created', [
                'id' => $record->id,
                'user' => $user->id,
                'incident' => $data['incident_number'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Safety incident synced successfully',
                'id' => $record->id,
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Offline sync failed: safety incident', [
                'error' => $e->getMessage(),
                'user' => $user->id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
