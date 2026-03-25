<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * If the authenticated user has must_change_password = true,
     * redirect them to the appropriate panel's password change page.
     * Allow access only to the change-password page, logout, and assets.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->must_change_password) {
            return $next($request);
        }

        // Detect which panel we're in based on the URL prefix
        $panel = 'app';
        if ($request->is('admin/*') || $request->is('admin')) {
            $panel = 'admin';
        } elseif ($request->is('client/*') || $request->is('client')) {
            $panel = 'client';
        }

        // Allow access to the change-password page itself, logout, and Livewire
        $allowedPaths = [
            "{$panel}/change-password",
            "{$panel}/logout",
            'livewire',
            'filament',  // Filament assets
        ];

        foreach ($allowedPaths as $path) {
            if ($request->is($path) || $request->is($path . '/*')) {
                return $next($request);
            }
        }

        // Log the forced redirect (first time only per session)
        $sessionKey = '_password_change_logged';
        if (!session($sessionKey)) {
            Log::channel('security')->info('FORCE_PASSWORD_CHANGE_REDIRECT', [
                'user_id' => $user->id,
                'email' => $user->email,
                'panel' => $panel,
                'reason' => $this->detectReason($user),
            ]);
            session([$sessionKey => true]);
        }

        return redirect()->away(url("/{$panel}/change-password"));
    }

    /**
     * Detect why the password change was forced.
     */
    protected function detectReason($user): string
    {
        $maxAgeDays = config('security.password.max_age_days', 90);

        if ($maxAgeDays > 0 && $user->password_changed_at) {
            $age = now()->diffInDays($user->password_changed_at);
            if ($age >= $maxAgeDays) {
                return "expired ({$age} days old)";
            }
        }

        if (!$user->password_changed_at) {
            return 'new_account';
        }

        return 'admin_forced';
    }
}
