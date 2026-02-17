<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\ExternalLogin;
use App\Livewire\ExternalDashboard;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\Auth\GoogleController;

Route::get('/', function () {
    return view('welcome');
});

// Company Onboarding
Route::get('/get-started', [OnboardingController::class, 'show'])->name('onboarding');
Route::post('/get-started', [OnboardingController::class, 'store'])->name('onboarding.store');
Route::get('/get-started/success', [OnboardingController::class, 'success'])->name('onboarding.success');

// Google Authentication Routes
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// External Dashboard Routes
Route::prefix('external')->name('external.')->group(function () {
    Route::get('/{token}', ExternalLogin::class)->name('login');
    Route::get('/{token}/dashboard', ExternalDashboard::class)->name('dashboard');
});

// Document Download/Preview Routes (requires authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/documents/{version}/download', [\App\Http\Controllers\DocumentController::class, 'download'])
        ->name('documents.download');
    Route::get('/documents/{version}/preview', [\App\Http\Controllers\DocumentController::class, 'preview'])
        ->name('documents.preview');
});
