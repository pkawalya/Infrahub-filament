<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Logs sensitive HTTP requests for security auditing.
 * Tracks: login attempts, data exports, admin actions, financial operations.
 */
class AuditSensitiveActions
{
    /**
     * URI patterns to audit (regex).
     */
    protected array $auditPatterns = [
        '#/auth/(login|logout|register)#',  // Authentication events
        '#/admin/#',                         // All admin panel actions
        '#export|download|print#i',          // Data exports
        '#/api/v1/#',                        // All API requests
        '#delete|destroy#i',                 // Destructive actions
    ];

    /**
     * Sensitive methods to always log.
     */
    protected array $auditMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only audit write operations + matching patterns
        if (!$this->shouldAudit($request)) {
            return $response;
        }

        $this->logAction($request, $response);

        return $response;
    }

    protected function shouldAudit(Request $request): bool
    {
        // Always audit write methods
        if (in_array($request->method(), $this->auditMethods)) {
            return true;
        }

        // Audit GET requests matching sensitive patterns
        foreach ($this->auditPatterns as $pattern) {
            if (preg_match($pattern, $request->path())) {
                return true;
            }
        }

        return false;
    }

    protected function logAction(Request $request, Response $response): void
    {
        $user = $request->user();

        $entry = [
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'company_id' => $user?->company_id,
            'method' => $request->method(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 200),
            'status' => $response->getStatusCode(),
            'timestamp' => now()->toISOString(),
        ];

        // Don't log request body for security (passwords, tokens, etc.)
        // But log query params for GETs (filtering)
        if ($request->method() === 'GET') {
            $entry['query'] = substr($request->getQueryString() ?? '', 0, 500);
        }

        // Flag suspicious activity
        if ($response->getStatusCode() === 403) {
            Log::channel('security')->warning('ACCESS_DENIED', $entry);
        } elseif ($response->getStatusCode() === 401) {
            Log::channel('security')->warning('UNAUTHENTICATED', $entry);
        } elseif ($response->getStatusCode() >= 500) {
            Log::channel('security')->error('SERVER_ERROR', $entry);
        } else {
            Log::channel('security')->info('AUDIT', $entry);
        }
    }
}
