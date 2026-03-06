<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * If the authenticated user has must_change_password = true,
     * redirect them to the password change page.
     * Allow access only to the change-password page, logout, and assets.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->must_change_password) {
            return $next($request);
        }

        // Allow access to the change-password page itself + logout
        $allowedPaths = [
            'app/change-password',
            'app/logout',
            'livewire',
        ];

        foreach ($allowedPaths as $path) {
            if ($request->is($path) || $request->is($path . '/*')) {
                return $next($request);
            }
        }

        return redirect()->to('/app/change-password');
    }
}
