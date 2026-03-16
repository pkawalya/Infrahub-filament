<?php

return [

    /*
    |--------------------------------------------------------------------------
    | InfraHub Security Configuration
    |--------------------------------------------------------------------------
    | Centralized security settings for auth, API, sessions, passwords.
    |
    */

    // ── Password Policy ────────────────────────────────────
    'password' => [
        'min_length' => 10,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_symbols' => true,
        'prevent_reuse' => 5,               // Remember last N passwords
        'max_age_days' => 90,               // Force reset after N days (0 = disabled)
        'warn_before_expiry_days' => 14,    // Show "expiring soon" warnings N days before
    ],

    // ── API Tokens ─────────────────────────────────────────
    'api' => [
        'token_expiry_hours' => 24 * 30, // 30 days (was: never)
        'max_tokens_per_user' => 5,
        'rate_limit_per_minute' => 60,
        'rate_limit_auth' => 5,     // login/register per minute
    ],

    // ── Session Security ───────────────────────────────────
    'session' => [
        'lifetime_minutes' => 120,     // Auto logout after inactivity
        'encrypt' => true,             // Encrypt session data
        'same_site' => 'lax',          // Cookie SameSite policy
    ],

    // ── Login Protection ───────────────────────────────────
    'login' => [
        'max_attempts' => 5,            // Before lockout
        'lockout_minutes' => 15,        // How long to lock out
        'track_ip_changes' => true,     // Alert on new IP logins
        'enforce_2fa' => true,          // Require email 2FA
    ],

    // ── File Upload Security ───────────────────────────────
    'uploads' => [
        'max_file_size_mb' => 50,       // Maximum upload size
        'allowed_extensions' => [
            'pdf',
            'doc',
            'docx',
            'xls',
            'xlsx',
            'csv',
            'png',
            'jpg',
            'jpeg',
            'gif',
            'webp',
            'svg',
            'dwg',
            'dxf',
            'dwf',       // CAD files
            'ifc',
            'rvt',              // BIM files
            'zip',
            'rar',
            '7z',        // Archives
        ],
        'blocked_extensions' => [
            'exe',
            'bat',
            'cmd',
            'sh',
            'php',
            'phar',
            'js',
            'vbs',
            'ps1',
            'com',
            'scr',
        ],
        'scan_for_malware' => false,    // Enable if ClamAV available
    ],

    // ── Audit Retention ────────────────────────────────────
    'audit' => [
        'log_retention_days' => 90,
        'log_login_attempts' => true,
        'log_api_requests' => true,
        'log_data_exports' => true,
        'log_admin_actions' => true,
    ],

    // ── Content Security ───────────────────────────────────
    'headers' => [
        'hsts_max_age' => 31536000,     // 1 year
        'frame_options' => 'SAMEORIGIN',
        'content_type_options' => 'nosniff',
        'referrer_policy' => 'strict-origin-when-cross-origin',
    ],

    // ── Geo Access Control ────────────────────────────────
    // Restrict access to specific countries (ISO 3166-1 alpha-2 codes).
    // Uses ip-api.com free tier for lookups (45 req/min, no key needed).
    // Set GEO_RESTRICTION_ENABLED=true in .env to activate.
    'geo_access' => [
        'enabled' => env('GEO_RESTRICTION_ENABLED', false),

        // Allowed country codes (uppercase ISO 3166-1 alpha-2)
        // Empty = allow all countries (no restriction)
        'allowed_countries' => array_filter(explode(',', env(
            'GEO_ALLOWED_COUNTRIES',
            'UG,KE,TZ,RW,SS,NG,GH,ZA,GB,US,AE'
        ))),

        // Paths to EXCLUDE from geo checks (always accessible)
        'excluded_paths' => [
            'api/health',
            'up',
        ],

        // Cache country lookups for N minutes (saves API calls)
        'cache_minutes' => (int) env('GEO_CACHE_MINUTES', 1440), // 24 hours

        // What to show blocked visitors
        'block_message' => 'Access to this service is not available in your region.',
    ],

    // ── IP Blocking ───────────────────────────────────────
    // Block specific IPs or CIDR ranges.
    // Can be managed via config, .env, or admin panel (database).
    'ip_blocking' => [
        'enabled' => env('IP_BLOCKING_ENABLED', true),

        // Static blocklist (from .env, comma-separated)
        // Example: BLOCKED_IPS="1.2.3.4,10.0.0.0/8,192.168.1.100"
        'blocked_ips' => array_filter(explode(',', env('BLOCKED_IPS', ''))),

        // Also check database table 'blocked_ips' for dynamic blocks
        'use_database' => true,

        // Whitelisted IPs that can never be blocked
        'whitelisted_ips' => array_filter(explode(',', env('WHITELISTED_IPS', '127.0.0.1'))),
    ],

];
