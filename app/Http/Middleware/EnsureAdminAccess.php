<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restrict access to routes that should only be accessible
 * by Super Admins and Company Admins (admin-tier users).
 */
class EnsureAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || (! $user->isSuperAdmin() && ! $user->isCompanyAdmin())) {
            abort(403, 'This area is restricted to administrators only.');
        }

        return $next($request);
    }
}
