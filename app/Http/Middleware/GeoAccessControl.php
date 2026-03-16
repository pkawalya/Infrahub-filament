<?php

namespace App\Http\Middleware;

use App\Models\BlockedIp;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Geo-restriction and IP blocking middleware.
 *
 * 1. Checks if the visitor's IP is in the blocklist (config + database)
 * 2. Optionally checks visitor's country via ip-api.com free lookup
 *
 * Enable via .env:
 *   GEO_RESTRICTION_ENABLED=true
 *   GEO_ALLOWED_COUNTRIES=UG,KE,TZ,RW,GB,US
 *   IP_BLOCKING_ENABLED=true
 *   BLOCKED_IPS=1.2.3.4,10.0.0.0/8
 *   WHITELISTED_IPS=127.0.0.1,your.office.ip
 */
class GeoAccessControl
{
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // ── 1. IP Blocking (fast, check first) ─────────────────
        if (BlockedIp::isBlocked($ip)) {
            Log::channel('security')->warning('BLOCKED_IP_ACCESS', [
                'ip' => $ip,
                'path' => $request->path(),
                'user_agent' => substr($request->userAgent() ?? '', 0, 200),
            ]);

            // Record hit in DB if there's a matching record
            $this->recordBlockHit($ip);

            abort(403, 'Access denied.');
        }

        // ── 2. Geo Restriction ─────────────────────────────────
        $geoConfig = config('security.geo_access');

        if (!($geoConfig['enabled'] ?? false)) {
            return $next($request);
        }

        // Skip geo check for excluded paths
        foreach ($geoConfig['excluded_paths'] ?? [] as $excluded) {
            if (str_starts_with($request->path(), $excluded)) {
                return $next($request);
            }
        }

        // Skip for localhost / private IPs
        if ($this->isPrivateIp($ip)) {
            return $next($request);
        }

        $allowedCountries = $geoConfig['allowed_countries'] ?? [];
        if (empty($allowedCountries)) {
            return $next($request);  // No country restriction configured
        }

        // Look up the country for this IP (cached)
        $country = $this->getCountryForIp($ip, $geoConfig['cache_minutes'] ?? 1440);

        if ($country === null) {
            // If lookup failed, allow access (fail-open to avoid blocking legit users)
            return $next($request);
        }

        if (!in_array(strtoupper($country), array_map('strtoupper', $allowedCountries))) {
            Log::channel('security')->warning('GEO_BLOCKED', [
                'ip' => $ip,
                'country' => $country,
                'path' => $request->path(),
            ]);

            abort(403, $geoConfig['block_message'] ?? 'Access not available in your region.');
        }

        return $next($request);
    }

    /**
     * Look up the country code for an IP via ip-api.com (free, 45 req/min).
     * Results are cached to minimize API calls.
     */
    protected function getCountryForIp(string $ip, int $cacheMinutes): ?string
    {
        $cacheKey = 'geo_country:' . md5($ip);

        return Cache::remember($cacheKey, $cacheMinutes * 60, function () use ($ip) {
            try {
                $ctx = stream_context_create([
                    'http' => ['timeout' => 3, 'ignore_errors' => true],
                ]);

                $response = @file_get_contents(
                    "http://ip-api.com/json/{$ip}?fields=status,countryCode",
                    false,
                    $ctx
                );

                if (!$response)
                    return null;

                $data = json_decode($response, true);

                if (($data['status'] ?? '') === 'success') {
                    return $data['countryCode'] ?? null;
                }

                return null;
            } catch (\Throwable $e) {
                Log::warning('Geo lookup failed for IP: ' . $ip . ' — ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Check if an IP is private/local (skip geo for these).
     */
    protected function isPrivateIp(string $ip): bool
    {
        return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    /**
     * Update the hit counter for a blocked IP in the database.
     */
    protected function recordBlockHit(string $ip): void
    {
        try {
            $record = BlockedIp::where('ip_address', $ip)->where('is_active', true)->first();
            if ($record) {
                $record->recordHit();
            }
        } catch (\Throwable $e) {
            // Don't break the request if DB is down
        }
    }
}
