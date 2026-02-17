<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\RfiController;
use App\Http\Controllers\Api\SubmittalController;
use App\Http\Controllers\Api\WorkOrderController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| InfraHub REST API v1
|--------------------------------------------------------------------------
| Authentication: Bearer token via Laravel Sanctum
| Base URL: /api/v1
| Rate Limit: 60 requests/minute (authenticated)
|
*/

// ── Public Auth Routes ─────────────────────────────────────
Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
});

// ── Authenticated Routes ───────────────────────────────────
Route::prefix('v1')
    ->middleware(['auth:sanctum'])
    ->group(function () {

        // Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/tokens', [AuthController::class, 'createToken']);
        Route::delete('/auth/tokens/{tokenId}', [AuthController::class, 'revokeToken']);

        // Projects
        Route::apiResource('projects', ProjectController::class);
        Route::get('projects/{project}/stats', [ProjectController::class, 'stats']);

        // Documents (scoped to project)
        Route::prefix('projects/{project}')->group(function () {
            Route::apiResource('documents', DocumentController::class);
            Route::post('documents/{document}/submit-for-review', [DocumentController::class, 'submitForReview']);
            Route::post('documents/{document}/approve', [DocumentController::class, 'approve']);
            Route::post('documents/{document}/reject', [DocumentController::class, 'reject']);

            // RFIs (scoped to project)
            Route::apiResource('rfis', RfiController::class);
            Route::post('rfis/{rfi}/answer', [RfiController::class, 'answer']);
            Route::post('rfis/{rfi}/close', [RfiController::class, 'close']);

            // Submittals (scoped to project)
            Route::apiResource('submittals', SubmittalController::class);
            Route::post('submittals/{submittal}/review', [SubmittalController::class, 'review']);
            Route::post('submittals/{submittal}/resubmit', [SubmittalController::class, 'resubmit']);

            // Work orders (scoped to project)
            Route::apiResource('work-orders', WorkOrderController::class);

            // Tasks (scoped to project)
            Route::apiResource('tasks', TaskController::class);
            Route::patch('tasks/{task}/progress', [TaskController::class, 'updateProgress']);
        });
    });
