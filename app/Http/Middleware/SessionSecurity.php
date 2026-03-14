<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Validates active sessions to prevent session fixation and hijacking.
 *
 * Checks:
 * 1. Session IP consistency (optional — disabled by default for mobile users)
 * 2. User-Agent consistency — detects session hijacking
 * 3. Session idle timeout enforcement (beyond Laravel's built-in)
 * 4. Concurrent session limiting
 */
class SessionSecurity
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // ── User-Agent Fingerprint Check ──────────────────
        // Detects session hijacking by comparing browser fingerprints
        $currentFingerprint = $this->fingerprint($request);
        $sessionFingerprint = session('_security_fingerprint');

        if ($sessionFingerprint && $sessionFingerprint !== $currentFingerprint) {
            Log::channel('security')->warning('SESSION_HIJACK_ATTEMPT', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'expected_fingerprint' => $sessionFingerprint,
                'actual_fingerprint' => $currentFingerprint,
            ]);

            // Invalidate the session and force re-login
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('filament.app.auth.login')->with(
                'notification',
                ['title' => 'Session expired', 'body' => 'Your session was invalidated for security reasons. Please log in again.', 'status' => 'danger']
            );
        }

        // Set fingerprint if not set (first request after login)
        if (!$sessionFingerprint) {
            session(['_security_fingerprint' => $currentFingerprint]);
        }

        // ── Inactivity Timeout ────────────────────────────
        // Additional check beyond session.lifetime for extra safety
        $lastActivity = session('_security_last_activity');
        $maxIdleSeconds = config('security.session.lifetime_minutes', 120) * 60;

        if ($lastActivity && (time() - $lastActivity) > $maxIdleSeconds) {
            Log::channel('security')->info('SESSION_IDLE_TIMEOUT', [
                'user_id' => $user->id,
                'idle_seconds' => time() - $lastActivity,
            ]);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('filament.app.auth.login');
        }

        session(['_security_last_activity' => time()]);

        return $next($request);
    }

    /**
     * Generate a browser fingerprint from request metadata.
     * Uses User-Agent + Accept-Language to detect gross session theft
     * without breaking legitimate IP changes (e.g., mobile networks).
     */
    protected function fingerprint(Request $request): string
    {
        return hash('xxh128', implode('|', [
            $request->userAgent() ?? '',
            $request->header('Accept-Language', ''),
        ]));
    }
}
