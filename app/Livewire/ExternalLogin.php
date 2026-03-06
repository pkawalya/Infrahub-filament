<?php

namespace App\Livewire;

use App\Models\ExternalAccess;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;

class ExternalLogin extends Component
{
    public $token;
    public $password;
    public $error;

    public function mount($token)
    {
        $this->token = $token;

        if (Session::get('external_authenticated_' . $token)) {
            return redirect()->route('external.dashboard', $token);
        }

        $externalAccess = ExternalAccess::where('access_token', $token)
            ->where('is_active', true)
            ->first();

        if (!$externalAccess) {
            abort(404, 'External access not found');
        }

        // Check expiry if the model has an expires_at field
        if (isset($externalAccess->expires_at) && $externalAccess->expires_at && $externalAccess->expires_at->isPast()) {
            abort(403, 'This access link has expired.');
        }
    }

    public function authenticate()
    {
        $this->login();
    }

    public function login()
    {
        $this->error = null;

        // Rate limiting: 5 attempts per minute per token
        $rateLimitKey = 'external-login:' . $this->token;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $this->error = "Too many login attempts. Please try again in {$seconds} seconds.";
            return;
        }
        RateLimiter::hit($rateLimitKey, 60);

        $externalAccess = ExternalAccess::where('access_token', $this->token)
            ->where('is_active', true)
            ->first();

        if (!$externalAccess) {
            $this->error = 'Invalid credentials';
            return;
        }

        // Use Hash::check for proper bcrypt comparison
        // Falls back to plain comparison for legacy passwords, then rehashes
        $passwordValid = false;
        if (str_starts_with($externalAccess->password ?? '', '$2y$') || str_starts_with($externalAccess->password ?? '', '$2a$')) {
            // Already hashed
            $passwordValid = Hash::check($this->password, $externalAccess->password);
        } else {
            // Legacy plaintext — compare then rehash for future security
            $passwordValid = ($externalAccess->password === $this->password);
            if ($passwordValid) {
                $externalAccess->update(['password' => Hash::make($this->password)]);
            }
        }

        if (!$passwordValid) {
            $this->error = 'Invalid credentials';
            return;
        }

        // Clear rate limiter on successful login
        RateLimiter::clear($rateLimitKey);

        $externalAccess->updateLastAccessed();

        // Regenerate session to prevent session fixation
        request()->session()->regenerate();

        Session::put([
            'external_project_id' => $externalAccess->project_id,
            'external_authenticated' => true
        ]);
        Session::put('external_authenticated_' . $this->token, true);

        return redirect()->route('external.dashboard', $this->token);
    }

    public function render()
    {
        return view('livewire.external-login')
            ->layout('layouts.external');
    }
}