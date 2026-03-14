<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

/**
 * Infrastructure health check endpoint.
 * Returns status of database, cache, queue, and storage.
 *
 * GET /api/health
 */
class HealthController
{
    public function __invoke(): JsonResponse
    {
        $checks = [];
        $healthy = true;

        // ── Database ──────────────────────────────────────
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $checks['database'] = [
                'status' => 'ok',
                'response_ms' => round((microtime(true) - $start) * 1000, 1),
            ];
        } catch (\Throwable $e) {
            $checks['database'] = ['status' => 'error', 'message' => 'Connection failed'];
            $healthy = false;
        }

        // ── Cache ─────────────────────────────────────────
        try {
            $key = '_health_check_' . uniqid();
            Cache::put($key, 'ok', 10);
            $val = Cache::get($key);
            Cache::forget($key);
            $checks['cache'] = [
                'status' => $val === 'ok' ? 'ok' : 'error',
                'driver' => config('cache.default'),
            ];
            if ($val !== 'ok')
                $healthy = false;
        } catch (\Throwable $e) {
            $checks['cache'] = ['status' => 'error', 'driver' => config('cache.default')];
            $healthy = false;
        }

        // ── Queue ─────────────────────────────────────────
        try {
            $checks['queue'] = [
                'status' => 'ok',
                'connection' => config('queue.default'),
                'pending_jobs' => DB::table('jobs')->count(),
                'failed_jobs' => DB::table('failed_jobs')->count(),
            ];
        } catch (\Throwable $e) {
            $checks['queue'] = [
                'status' => 'degraded',
                'connection' => config('queue.default'),
            ];
        }

        // ── Storage ───────────────────────────────────────
        try {
            $disk = Storage::disk('local');
            $testFile = '_health_check.tmp';
            $disk->put($testFile, 'ok');
            $content = $disk->get($testFile);
            $disk->delete($testFile);
            $checks['storage'] = [
                'status' => $content === 'ok' ? 'ok' : 'error',
            ];
            if ($content !== 'ok')
                $healthy = false;
        } catch (\Throwable $e) {
            $checks['storage'] = ['status' => 'error'];
            $healthy = false;
        }

        // ── System ────────────────────────────────────────
        $checks['system'] = [
            'php' => PHP_VERSION,
            'laravel' => app()->version(),
            'app_version' => config('app.version', '1.0.0'),
            'api_version' => config('app.api_version', 'v1'),
            'environment' => app()->environment(),
            'debug' => config('app.debug'),
            'uptime_seconds' => (int) (microtime(true) - LARAVEL_START),
        ];

        return response()->json([
            'status' => $healthy ? 'healthy' : 'unhealthy',
            'version' => config('app.version', '1.0.0'),
            'api_version' => config('app.api_version', 'v1'),
            'timestamp' => now()->toISOString(),
            'checks' => $checks,
        ], $healthy ? 200 : 503);
    }
}
