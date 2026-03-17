<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class BlockedIp extends Model
{
    protected $fillable = [
        'ip_address',
        'cidr_range',
        'reason',
        'blocked_by',
        'expires_at',
        'is_active',
        'hit_count',
        'last_blocked_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
        'last_blocked_at' => 'datetime',
        'hit_count' => 'integer',
    ];

    /**
     * Check if a given IP is blocked (config + database).
     */
    public static function isBlocked(string $ip): bool
    {
        $config = config('security.ip_blocking');

        if (!($config['enabled'] ?? true)) {
            return false;
        }

        // Whitelisted IPs always pass
        if (in_array($ip, $config['whitelisted_ips'] ?? [])) {
            return false;
        }

        // Check static blocklist from config/env
        foreach ($config['blocked_ips'] ?? [] as $blocked) {
            $blocked = trim($blocked);
            if (empty($blocked))
                continue;

            if (str_contains($blocked, '/')) {
                if (self::ipInCidr($ip, $blocked))
                    return true;
            } elseif ($ip === $blocked) {
                return true;
            }
        }

        // Check database blocklist (cached for 5 min)
        if ($config['use_database'] ?? true) {
            return self::isBlockedInDb($ip);
        }

        return false;
    }

    /**
     * Check if IP is blocked in database, with caching.
     */
    protected static function isBlockedInDb(string $ip): bool
    {
        try {
            if (!Schema::hasTable((new static())->getTable())) {
                return false;
            }
        } catch (\Throwable $e) {
            return false;
        }

        $cacheKey = 'blocked_ip:' . md5($ip);

        return Cache::remember($cacheKey, 300, function () use ($ip) {
            // Exact IP match
            $exactMatch = static::where('ip_address', $ip)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->exists();

            if ($exactMatch)
                return true;

            // CIDR range match
            $ranges = static::whereNotNull('cidr_range')
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                })
                ->pluck('cidr_range');

            foreach ($ranges as $cidr) {
                if (self::ipInCidr($ip, $cidr))
                    return true;
            }

            return false;
        });
    }

    /**
     * Check if an IP falls within a CIDR range.
     */
    public static function ipInCidr(string $ip, string $cidr): bool
    {
        if (!str_contains($cidr, '/'))
            return $ip === $cidr;

        [$subnet, $bits] = explode('/', $cidr, 2);
        $bits = (int) $bits;

        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);

        if ($ipLong === false || $subnetLong === false)
            return false;

        $mask = -1 << (32 - $bits);

        return ($ipLong & $mask) === ($subnetLong & $mask);
    }

    /**
     * Record a hit (blocked request) for tracking.
     */
    public function recordHit(): void
    {
        $this->increment('hit_count');
        $this->update(['last_blocked_at' => now()]);
    }

    /**
     * Clear the blocked IP cache for a specific IP.
     */
    public static function clearCache(string $ip): void
    {
        Cache::forget('blocked_ip:' . md5($ip));
    }

    /**
     * Clear all blocked IP caches.
     */
    public static function clearAllCaches(): void
    {
        // Flush all blocked_ip keys by just clearing all active records' caches
        static::where('is_active', true)->pluck('ip_address')->each(function ($ip) {
            Cache::forget('blocked_ip:' . md5($ip));
        });
    }

    /**
     * Scope: active blocks only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope: expired blocks.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }
}
