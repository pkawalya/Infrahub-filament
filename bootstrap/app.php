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
        //
    })->create();

