<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();

        // ── Global Security ────────────────────────────────
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->append(\App\Http\Middleware\AuditSensitiveActions::class);

        // ── API-specific ───────────────────────────────────
        $middleware->api(prepend: [
            \App\Http\Middleware\EnforceTenantIsolation::class,
        ]);

        // ── Aliases ────────────────────────────────────────
        $middleware->alias([
            'module' => \App\Http\Middleware\CheckModulePermission::class,
        ]);
    })
    ->withEvents(discover: [__DIR__ . '/../app/Listeners'])
    ->withExceptions(function (Exceptions $exceptions) {
        // Convert 404 on panel routes to 403 — "not authorized" instead of "not found"
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            $path = $request->path();

            // If someone hits a panel route that doesn't exist (e.g. /app/register after removal),
            // treat it as "not authorized" rather than exposing the 404 default.
            if (preg_match('#^(app|admin|client)(/|$)#', $path)) {
                abort(403, 'You are not authorized to access this resource.');
            }
        });
    })->create();

