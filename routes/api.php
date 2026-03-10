<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\RfiController;
use App\Http\Controllers\Api\SubmittalController;
use App\Http\Controllers\Api\WorkOrderController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\DailySiteDiaryController;
use App\Http\Controllers\Api\CrewAttendanceController;
use App\Http\Controllers\Api\EquipmentController;
use App\Http\Controllers\Api\SafetyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| InfraHub REST API v1
|--------------------------------------------------------------------------
| Authentication: Bearer token via Laravel Sanctum
| Base URL: /api/v1
| Rate Limit: 60 requests/minute (authenticated)
|
| Permission Middleware:
| - module:projects.view   → requires 'projects.view' permission
| - module:tasks.create    → requires 'tasks.create' permission
| - Super admins and company admins bypass all permission checks
|
*/

// ── Public Auth Routes ─────────────────────────────────────
Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:3,1');
});

// ── Authenticated Routes ───────────────────────────────────
Route::prefix('v1')
    ->middleware(['auth:sanctum', 'throttle:60,1'])
    ->group(function () {

        // ─ Auth & Profile ─────────────────────────────────
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/tokens', [AuthController::class, 'createToken']);
        Route::delete('/auth/tokens/{tokenId}', [AuthController::class, 'revokeToken']);

        // ─ Projects ───────────────────────────────────────
        Route::middleware('module:projects.view')->group(function () {
            Route::get('projects', [ProjectController::class, 'index']);
            Route::get('projects/{project}', [ProjectController::class, 'show']);
            Route::get('projects/{project}/stats', [ProjectController::class, 'stats']);
        });
        Route::middleware('module:projects.create')->post('projects', [ProjectController::class, 'store']);
        Route::middleware('module:projects.update')->put('projects/{project}', [ProjectController::class, 'update']);
        Route::middleware('module:projects.delete')->delete('projects/{project}', [ProjectController::class, 'destroy']);

        // ─ Documents (scoped to project) ──────────────────
        Route::prefix('projects/{project}')->group(function () {
            Route::middleware('module:documents.view')->group(function () {
                Route::get('documents', [DocumentController::class, 'index']);
                Route::get('documents/{document}', [DocumentController::class, 'show']);
            });
            Route::middleware('module:documents.create')->post('documents', [DocumentController::class, 'store']);
            Route::middleware('module:documents.update')->put('documents/{document}', [DocumentController::class, 'update']);
            Route::middleware('module:documents.delete')->delete('documents/{document}', [DocumentController::class, 'destroy']);
            Route::middleware('module:documents.approve')->group(function () {
                Route::post('documents/{document}/submit-for-review', [DocumentController::class, 'submitForReview']);
                Route::post('documents/{document}/approve', [DocumentController::class, 'approve']);
                Route::post('documents/{document}/reject', [DocumentController::class, 'reject']);
            });

            // ─ RFIs ───────────────────────────────────────
            Route::middleware('module:documents.view')->group(function () {
                Route::get('rfis', [RfiController::class, 'index']);
                Route::get('rfis/{rfi}', [RfiController::class, 'show']);
            });
            Route::middleware('module:documents.create')->post('rfis', [RfiController::class, 'store']);
            Route::middleware('module:documents.update')->group(function () {
                Route::put('rfis/{rfi}', [RfiController::class, 'update']);
                Route::post('rfis/{rfi}/answer', [RfiController::class, 'answer']);
                Route::post('rfis/{rfi}/close', [RfiController::class, 'close']);
            });

            // ─ Submittals ─────────────────────────────────
            Route::middleware('module:documents.view')->group(function () {
                Route::get('submittals', [SubmittalController::class, 'index']);
                Route::get('submittals/{submittal}', [SubmittalController::class, 'show']);
            });
            Route::middleware('module:documents.create')->post('submittals', [SubmittalController::class, 'store']);
            Route::middleware('module:documents.approve')->group(function () {
                Route::post('submittals/{submittal}/review', [SubmittalController::class, 'review']);
                Route::post('submittals/{submittal}/resubmit', [SubmittalController::class, 'resubmit']);
            });

            // ─ Work Orders ────────────────────────────────
            Route::middleware('module:work_orders.view')->group(function () {
                Route::get('work-orders', [WorkOrderController::class, 'index']);
                Route::get('work-orders/{workOrder}', [WorkOrderController::class, 'show']);
            });
            Route::middleware('module:work_orders.create')->post('work-orders', [WorkOrderController::class, 'store']);
            Route::middleware('module:work_orders.update')->put('work-orders/{workOrder}', [WorkOrderController::class, 'update']);
            Route::middleware('module:work_orders.delete')->delete('work-orders/{workOrder}', [WorkOrderController::class, 'destroy']);

            // ─ Tasks ──────────────────────────────────────
            Route::middleware('module:tasks.view')->group(function () {
                Route::get('tasks', [TaskController::class, 'index']);
                Route::get('tasks/{task}', [TaskController::class, 'show']);
            });
            Route::middleware('module:tasks.create')->post('tasks', [TaskController::class, 'store']);
            Route::middleware('module:tasks.update')->group(function () {
                Route::put('tasks/{task}', [TaskController::class, 'update']);
                Route::patch('tasks/{task}/progress', [TaskController::class, 'updateProgress']);
            });
            Route::middleware('module:tasks.delete')->delete('tasks/{task}', [TaskController::class, 'destroy']);
        });

        // ─ Daily Site Diaries ─────────────────────────────
        Route::middleware('module:field_logs.view')->group(function () {
            Route::get('site-diaries', [DailySiteDiaryController::class, 'index']);
            Route::get('site-diaries/{diary}', [DailySiteDiaryController::class, 'show']);
        });
        Route::middleware('module:field_logs.create')->post('site-diaries', [DailySiteDiaryController::class, 'store']);
        Route::middleware('module:field_logs.update')->put('site-diaries/{diary}', [DailySiteDiaryController::class, 'update']);
        Route::middleware('module:field_logs.approve')->post('site-diaries/{diary}/approve', [DailySiteDiaryController::class, 'approve']);

        // ─ Crew Attendance ────────────────────────────────
        Route::middleware('module:crew.view')->group(function () {
            Route::get('attendance', [CrewAttendanceController::class, 'index']);
            Route::get('attendance/today', [CrewAttendanceController::class, 'today']);
            Route::get('attendance/{attendance}', [CrewAttendanceController::class, 'show']);
        });
        Route::middleware('module:crew.create')->post('attendance', [CrewAttendanceController::class, 'store']);

        // ─ Equipment ──────────────────────────────────────
        Route::middleware('module:equipment.view')->group(function () {
            Route::get('equipment/allocations', [EquipmentController::class, 'allocations']);
            Route::get('equipment/fuel-logs', [EquipmentController::class, 'fuelLogs']);
        });
        Route::middleware('module:equipment.create')->group(function () {
            Route::post('equipment/allocations', [EquipmentController::class, 'storeAllocation']);
            Route::post('equipment/fuel-logs', [EquipmentController::class, 'storeFuelLog']);
        });

        // ─ Safety Incidents ───────────────────────────────
        Route::middleware('module:safety.view')->group(function () {
            Route::get('safety-incidents', [SafetyController::class, 'index']);
            Route::get('safety-incidents/{incident}', [SafetyController::class, 'show']);
        });
        Route::middleware('module:safety.create')->post('safety-incidents', [SafetyController::class, 'store']);
        Route::middleware('module:safety.update')->put('safety-incidents/{incident}', [SafetyController::class, 'update']);
    });
