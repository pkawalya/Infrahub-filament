<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ExternalLogin;
use App\Livewire\ExternalDashboard;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProjectInvitationController;
use App\Http\Controllers\MobileController;

Route::get('/', function () {
    return view('welcome');
});

// ── Health Check Endpoint ─────────────────────────────────
Route::get('/health', function () {
    $dbOk = false;
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $dbOk = true;
    } catch (\Throwable) {}

    $cacheOk = false;
    try {
        \Illuminate\Support\Facades\Cache::put('_health', 1, 10);
        $cacheOk = (bool) \Illuminate\Support\Facades\Cache::get('_health');
    } catch (\Throwable) {}

    $status = ($dbOk && $cacheOk) ? 'ok' : 'degraded';
    $code   = ($dbOk && $cacheOk) ? 200 : 503;

    return response()->json([
        'status'    => $status,
        'db'        => $dbOk ? 'connected' : 'error',
        'cache'     => $cacheOk ? 'ok' : 'error',
        'timestamp' => now()->toISOString(),
        'version'   => config('app.version', '1.0.0'),
    ], $code);
})->middleware('throttle:60,1')->name('health');

// Email Invitation Acceptance
Route::get('/invitation/accept/{token}', [InvitationController::class, 'accept'])->name('invitation.accept');
Route::post('/invitation/accept/{token}', [InvitationController::class, 'confirm'])->middleware('throttle:5,1')->name('invitation.confirm');

// Project Invitation Acceptance
Route::get('/project-invite/{token}', [ProjectInvitationController::class, 'accept'])->name('project-invitation.accept');
Route::post('/project-invite/{token}', [ProjectInvitationController::class, 'confirm'])->middleware('throttle:5,1')->name('project-invitation.confirm');
Route::post('/project-invite/{token}/register', [ProjectInvitationController::class, 'register'])->middleware('throttle:5,1')->name('project-invitation.register');

Route::get('/offline', function () {
    return response()->file(public_path('offline.html'));
});

Route::get('/docs', function () {
    return view('docs');
})->name('docs');

// Company Onboarding
Route::get('/get-started', [OnboardingController::class, 'show'])->name('onboarding');
Route::post('/get-started', [OnboardingController::class, 'store'])->middleware('throttle:5,1')->name('onboarding.store');
Route::get('/get-started/success', [OnboardingController::class, 'success'])->name('onboarding.success');

// Schedule a Call
Route::get('/schedule-call', [\App\Http\Controllers\AppointmentController::class, 'create'])->name('schedule-call');
Route::post('/schedule-call', [\App\Http\Controllers\AppointmentController::class, 'store'])->middleware('throttle:5,1')->name('schedule-call.store');

// Google Authentication Routes
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// External Dashboard Routes
Route::prefix('external')->name('external.')->middleware('throttle:10,1')->group(function () {
    Route::get('/{token}', ExternalLogin::class)->name('login');
    Route::get('/{token}/dashboard', ExternalDashboard::class)->name('dashboard');
});

// Document Download/Preview Routes (requires authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/documents/{version}/download', [\App\Http\Controllers\DocumentController::class, 'download'])
        ->name('documents.download');
    Route::get('/documents/{version}/preview', [\App\Http\Controllers\DocumentController::class, 'preview'])
        ->name('documents.preview');

    // Financial Print Routes
    Route::get('/print/invoice/{invoice}', [\App\Http\Controllers\FinancialPrintController::class, 'printInvoice'])
        ->name('print.invoice');
    Route::get('/print/receipt/{payment}', [\App\Http\Controllers\FinancialPrintController::class, 'printReceipt'])
        ->name('print.receipt');
    Route::get('/print/quotation/{quotation}', [\App\Http\Controllers\FinancialPrintController::class, 'printQuotation'])
        ->name('print.quotation');
});

// ── Mobile PWA ─────────────────────────────────────────────
Route::prefix('mobile')->group(function () {
    Route::get('/login', [MobileController::class, 'login'])->name('mobile.login');
    Route::get('/', [MobileController::class, 'home'])->name('mobile.home');
    Route::get('/projects', [MobileController::class, 'projects'])->name('mobile.projects');
    Route::get('/projects/{id}', [MobileController::class, 'projectShow'])->name('mobile.projects.show');
    Route::get('/tasks', [MobileController::class, 'tasks'])->name('mobile.tasks');
    Route::get('/forms', [MobileController::class, 'forms'])->name('mobile.forms');
    Route::get('/profile', [MobileController::class, 'profile'])->name('mobile.profile');
    Route::get('/notifications', [MobileController::class, 'notifications'])->name('mobile.notifications');
});
