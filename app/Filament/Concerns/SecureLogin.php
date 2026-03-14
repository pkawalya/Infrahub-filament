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

            // ── Password Expiry Check ─────────────────────
            // If password is older than max_age_days, force a change
            $maxAgeDays = config('security.password.max_age_days', 90);
            if ($maxAgeDays > 0 && $user) {
                $passwordAge = $user->password_changed_at
                    ? now()->diffInDays($user->password_changed_at)
                    : ($user->created_at ? now()->diffInDays($user->created_at) : 999);

                if ($passwordAge >= $maxAgeDays && !$user->must_change_password) {
                    $user->updateQuietly(['must_change_password' => true]);
                } else {
                    // Warn if approaching expiry
                    $warnDays = config('security.password.warn_before_expiry_days', 14);
                    $daysRemaining = $maxAgeDays - $passwordAge;
                    if ($daysRemaining > 0 && $daysRemaining <= $warnDays) {
                        \Filament\Notifications\Notification::make()
                            ->title('Password Expiring Soon')
                            ->body("Your password will expire in {$daysRemaining} day(s). Please change it soon to avoid interruption.")
                            ->warning()
                            ->icon('heroicon-o-clock')
                            ->persistent()
                            ->send();
                    }
                }
            }

            return $response;
        } catch (ValidationException $e) {
            // Failed authentication — rate limit stays incremented
            throw $e;
        }
    }
}
