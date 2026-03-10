<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
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
        $agent = substr(request()->userAgent() ?? '', 0, 200);

        Log::channel('security')->info('LOGIN_SUCCESS', [
            'user_id' => $user->id,
            'email' => $user->email,
            'company_id' => $user->company_id,
            'ip' => $ip,
            'user_agent' => $agent,
        ]);

        // Detect new IP for this user
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

        Log::channel('security')->warning('LOGIN_FAILED', [
            'email' => $email,
            'ip' => $ip,
            'user_agent' => substr(request()->userAgent() ?? '', 0, 200),
        ]);

        // Track consecutive failures
        $key = "login_fails:{$email}";
        $fails = Cache::increment($key);
        Cache::put($key, $fails, now()->addHours(1));

        // Alert on brute force attempts
        if ($fails >= 5) {
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
        Log::channel('security')->critical('ACCOUNT_LOCKOUT', [
            'email' => $event->request->input('email', 'unknown'),
            'ip' => $event->request->ip(),
            'user_agent' => substr($event->request->userAgent() ?? '', 0, 200),
        ]);
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
