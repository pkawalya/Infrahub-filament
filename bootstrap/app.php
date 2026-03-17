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
        $middleware->append(\App\Http\Middleware\GeoAccessControl::class);
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->append(\App\Http\Middleware\AuditSensitiveActions::class);
        $middleware->append(\App\Http\Middleware\ValidateFileUpload::class);

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

        // Convert raw SQL constraint violations into user-friendly messages
        $exceptions->renderable(function (\Illuminate\Database\QueryException $e, $request) {
            if ($e->getCode() === '23000') {
                // Extract column name from "Column 'xyz' cannot be null" or similar
                $message = 'A required field is missing. Please check the form and try again.';
                if (preg_match("/Column '(\w+)' cannot be null/", $e->getMessage(), $m)) {
                    $field = str_replace('_', ' ', $m[1]);
                    $message = "A required field is missing: {$field}. Please check the form and try again.";
                }

                if ($request->expectsJson() || $request->hasHeader('X-Livewire')) {
                    // For Livewire/AJAX requests, flash a notification
                    \Filament\Notifications\Notification::make()
                        ->danger()
                        ->title('Validation Error')
                        ->body($message)
                        ->send();

                    return back();
                }

                abort(422, $message);
            }
        });
    })->create();

