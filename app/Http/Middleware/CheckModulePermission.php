<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Check module-level permissions for API requests.
 * Usage in routes: ->middleware('module:projects.view')
 */
class CheckModulePermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        // Super admins and company admins always pass
        if ($user->isSuperAdmin() || $user->isCompanyAdmin()) {
            return $next($request);
        }

        if (!$user->hasModulePermission($permission)) {
            return response()->json([
                'success' => false,
                'message' => "You don't have permission: {$permission}",
            ], 403);
        }

        return $next($request);
    }
}
