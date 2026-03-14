<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enforces SaaS subscription validity for company users.
 *
 * Checks:
 *  1. Company subscription is active (not expired/suspended)
 *  2. Company is not in a lapsed trial
 *
 * Super admins bypass all checks.
 * Allows access to the upgrade page so users can fix their subscription.
 */
class CheckSubscription
{
    /**
     * Routes that are always accessible even when subscription is expired.
     * This lets users reach the upgrade page to reactivate.
     */
    protected array $allowedRoutes = [
        'filament.app.pages.settings.upgrade',
        'filament.app.auth.logout',
        'filament.app.auth.login',
        'filament.app.auth.password-reset.*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // No user, or super admin — skip
        if (!$user || $user->isSuperAdmin()) {
            return $next($request);
        }

        $company = $user->company;

        // Users without a company (shouldn't happen but guard) — skip
        if (!$company) {
            return $next($request);
        }

        // Allow access to always-permitted routes
        $routeName = $request->route()?->getName() ?? '';
        foreach ($this->allowedRoutes as $pattern) {
            if (Str::is($pattern, $routeName)) {
                return $next($request);
            }
        }

        // ── Check 1: Company must be active (not suspended) ───────
        if (!$company->is_active) {
            return $this->redirectWithMessage(
                $request,
                'Your company account has been suspended. Please contact support.',
                'danger'
            );
        }

        // ── Check 2: Subscription must be valid ──────────────────
        if (!$company->isSubscriptionActive() && !$company->isInTrial()) {
            return $this->redirectWithMessage(
                $request,
                'Your subscription has expired. Please upgrade your plan to continue.',
                'warning'
            );
        }

        return $next($request);
    }

    /**
     * Redirect to the upgrade page with a flash notification.
     */
    protected function redirectWithMessage(Request $request, string $message, string $type): Response
    {
        // For API/AJAX requests, return JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'subscription_required' => true,
            ], 403);
        }

        // For web requests, use Filament notification system via session flash
        session()->flash('notification', [
            'title' => 'Subscription Required',
            'body' => $message,
            'status' => $type,
        ]);

        return redirect()->route('filament.app.pages.settings.upgrade');
    }
}
