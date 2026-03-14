<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class AuthSecurityListener
{
    /**
     * Handle login events.
     */
    public function onLogin(Login $event): void
    {
        /** @var User $user */
        $user = $event->user;
        $ip = request()->ip();
        $agent = substr(request()->userAgent() ?? '', 0, 500);

        // ── Record to DB ──────────────────────────────────
        $this->recordActivity([
            'user_id' => $user->id,
            'email' => $user->email,
            'ip_address' => $ip,
            'user_agent' => $agent,
            'status' => 'success',
            'metadata' => json_encode([
                'company_id' => $user->company_id,
                'user_type' => $user->user_type,
            ]),
        ]);

        // ── Record to log ─────────────────────────────────
        Log::channel('security')->info('LOGIN_SUCCESS', [
            'user_id' => $user->id,
            'email' => $user->email,
            'company_id' => $user->company_id,
            'ip' => $ip,
            'user_agent' => substr($agent, 0, 200),
        ]);

        // ── Update last_login_at ──────────────────────────
        $user->updateQuietly(['last_login_at' => now()]);

        // ── Detect new IP for this user ───────────────────
        $knownIps = Cache::get("user_ips:{$user->id}", []);
        if (!in_array($ip, $knownIps)) {
            if (!empty($knownIps)) {
                Log::channel('security')->warning('NEW_IP_LOGIN', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'new_ip' => $ip,
                    'known_ips' => $knownIps,
                ]);
            }
            $knownIps[] = $ip;
            Cache::put("user_ips:{$user->id}", array_slice($knownIps, -10), now()->addDays(90));
        }

        // Clear failed attempts on success
        Cache::forget("login_fails:{$user->email}");
    }

    /**
     * Handle failed login attempts.
     */
    public function onFailed(Failed $event): void
    {
        $email = $event->credentials['email'] ?? 'unknown';
        $ip = request()->ip();
        $agent = substr(request()->userAgent() ?? '', 0, 500);

        // Determine the reason for failure
        $reason = 'wrong_password';
        $user = User::withoutGlobalScopes()->where('email', $email)->first();
        if (!$user) {
            $reason = 'user_not_found';
        } elseif (!$user->is_active) {
            $reason = 'user_disabled';
        } elseif ($user->company_id && $user->company && !$user->company->is_active) {
            $reason = 'company_suspended';
        }

        // ── Record to DB ──────────────────────────────────
        $this->recordActivity([
            'user_id' => $user?->id,
            'email' => $email,
            'ip_address' => $ip,
            'user_agent' => $agent,
            'status' => 'failed',
            'failure_reason' => $reason,
            'metadata' => json_encode([
                'company_id' => $user?->company_id,
            ]),
        ]);

        // ── Record to log ─────────────────────────────────
        Log::channel('security')->warning('LOGIN_FAILED', [
            'email' => $email,
            'ip' => $ip,
            'reason' => $reason,
            'user_agent' => substr($agent, 0, 200),
        ]);

        // Track consecutive failures
        $key = "login_fails:{$email}";
        $fails = Cache::increment($key);
        Cache::put($key, $fails, now()->addHours(1));

        $maxAttempts = config('security.login.max_attempts', 5);
        if ($fails >= $maxAttempts) {
            Log::channel('security')->critical('BRUTE_FORCE_DETECTED', [
                'email' => $email,
                'ip' => $ip,
                'attempts' => $fails,
            ]);
        }
    }

    /**
     * Handle logout events.
     */
    public function onLogout(Logout $event): void
    {
        if ($event->user) {
            /** @var User $logoutUser */
            $logoutUser = $event->user;

            $this->recordActivity([
                'user_id' => $logoutUser->id,
                'email' => $logoutUser->email,
                'ip_address' => request()->ip(),
                'user_agent' => substr(request()->userAgent() ?? '', 0, 500),
                'status' => 'logout',
            ]);

            Log::channel('security')->info('LOGOUT', [
                'user_id' => $logoutUser->id,
                'email' => $logoutUser->email,
                'ip' => request()->ip(),
            ]);
        }
    }

    /**
     * Handle lockout events (too many attempts).
     */
    public function onLockout(Lockout $event): void
    {
        $email = $event->request->input('email', 'unknown');
        $ip = $event->request->ip();

        $this->recordActivity([
            'email' => $email,
            'ip_address' => $ip,
            'user_agent' => substr($event->request->userAgent() ?? '', 0, 500),
            'status' => 'locked',
            'failure_reason' => 'rate_limited',
        ]);

        Log::channel('security')->critical('ACCOUNT_LOCKOUT', [
            'email' => $email,
            'ip' => $ip,
            'user_agent' => substr($event->request->userAgent() ?? '', 0, 200),
        ]);
    }

    /**
     * Record an activity to the login_activities table.
     */
    protected function recordActivity(array $data): void
    {
        try {
            if (Schema::hasTable('login_activities')) {
                DB::table('login_activities')->insert(array_merge($data, [
                    'created_at' => now(),
                ]));
            }
        } catch (\Throwable $e) {
            // Silently fail — never break login flow for audit logging
            Log::error('Failed to record login activity: ' . $e->getMessage());
        }
    }

    /**
     * Register the listeners.
     */
    public function subscribe($events): array
    {
        return [
            Login::class => 'onLogin',
            Failed::class => 'onFailed',
            Logout::class => 'onLogout',
            Lockout::class => 'onLockout',
        ];
    }
}
