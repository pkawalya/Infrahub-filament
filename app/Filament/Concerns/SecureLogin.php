<?php

namespace App\Filament\Concerns;

use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Shared login security for all Filament panels.
 * Adds rate limiting (5 attempts per minute per email + 15 per IP)
 * and blocks inactive users with a clear message.
 */
trait SecureLogin
{
    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();
        $email = strtolower($data['email'] ?? '');
        $ip = request()->ip();

        // ── Rate Limiting: per-email ──────────────────────
        $emailKey = 'filament-login:' . $email;
        if (RateLimiter::tooManyAttempts($emailKey, 5)) {
            $seconds = RateLimiter::availableIn($emailKey);
            throw ValidationException::withMessages([
                'data.email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        // ── Rate Limiting: per-IP ─────────────────────────
        $ipKey = 'filament-login-ip:' . $ip;
        if (RateLimiter::tooManyAttempts($ipKey, 15)) {
            $seconds = RateLimiter::availableIn($ipKey);
            throw ValidationException::withMessages([
                'data.email' => "Too many login attempts from your network. Please try again in {$seconds} seconds.",
            ]);
        }

        // ── Block inactive users ──────────────────────────
        $user = User::where('email', $email)->first();

        if ($user && !$user->is_active) {
            throw ValidationException::withMessages([
                'data.email' => 'Your account has been deactivated. Please contact your administrator to restore access.',
            ]);
        }

        // ── Block users with inactive companies ───────────
        if ($user && $user->company_id && $user->company && !$user->company->is_active) {
            throw ValidationException::withMessages([
                'data.email' => 'Your company account is inactive. Please contact support.',
            ]);
        }

        // Count the attempt
        RateLimiter::hit($emailKey, 60);
        RateLimiter::hit($ipKey, 120);

        try {
            $response = parent::authenticate();

            // Success — clear rate limiters
            RateLimiter::clear($emailKey);

            return $response;
        } catch (ValidationException $e) {
            // Failed authentication — rate limit stays incremented
            throw $e;
        }
    }
}
