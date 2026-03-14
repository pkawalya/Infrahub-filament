<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseApiController
{
    /**
     * POST /api/v1/auth/login
     * Authenticate and receive a bearer token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'string|max:255',
        ]);

        $ip = $request->ip();
        $email = strtolower($request->email);

        // ── Dual rate limiting: per-email AND per-IP ────────
        $emailKey = 'api-login:' . $email;
        $ipKey = 'api-login-ip:' . $ip;
        $maxAttempts = config('security.login.max_attempts', 5);

        if (RateLimiter::tooManyAttempts($emailKey, $maxAttempts)) {
            $this->logLoginActivity($email, null, $ip, $request->userAgent(), 'locked', 'rate_limited');
            $seconds = RateLimiter::availableIn($emailKey);
            return $this->error("Too many login attempts. Try again in {$seconds} seconds.", 429);
        }
        if (RateLimiter::tooManyAttempts($ipKey, $maxAttempts * 3)) {
            $this->logLoginActivity($email, null, $ip, $request->userAgent(), 'blocked', 'ip_rate_limited');
            return $this->error('Too many requests from this IP address.', 429);
        }

        RateLimiter::hit($emailKey, 60);
        RateLimiter::hit($ipKey, 300);

        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            $this->logLoginActivity($email, $user?->id, $ip, $request->userAgent(), 'failed', 'wrong_password');
            Log::channel('security')->warning('LOGIN_FAILED', ['email' => $email, 'ip' => $ip]);
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            $this->logLoginActivity($email, $user->id, $ip, $request->userAgent(), 'failed', 'disabled');
            return $this->error('Account is deactivated. Contact your administrator.', 403);
        }

        // SaaS: Verify the user's company is active
        if ($user->company && !$user->company->is_active) {
            $this->logLoginActivity($email, $user->id, $ip, $request->userAgent(), 'failed', 'company_inactive');
            return $this->error('Your company account is inactive. Contact support.', 403);
        }

        // ── Success ─────────────────────────────────────────
        RateLimiter::clear($emailKey);
        $this->logLoginActivity($email, $user->id, $ip, $request->userAgent(), 'success');

        $deviceName = $request->device_name ?? ($request->userAgent() ?: 'api-token');

        // Token with configurable expiry (default: 30 days)
        $expiryHours = config('security.api.token_expiry_hours', 720);
        $token = $user->createToken($deviceName, ['*'], now()->addHours($expiryHours))->plainTextToken;

        $user->update(['last_login_at' => now()]);

        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $expiryHours * 3600,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'company_id' => $user->company_id,
                'user_type' => $user->user_type,
                'avatar' => $user->avatar,
                'roles' => $user->getRoleNames(),
            ],
        ], 'Login successful');
    }

    /**
     * POST /api/v1/auth/register
     * Register a new user (usually disabled in production).
     */
    public function register(Request $request): JsonResponse
    {
        // SECURITY: Public registration is disabled in production SaaS.
        // Users must be invited by their company admin via the Filament panel.
        // To re-enable, set ALLOW_API_REGISTRATION=true in .env
        if (!config('app.allow_api_registration', false)) {
            return $this->error('Public registration is disabled. Contact your company admin for an invitation.', 403);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'string', 'confirmed', Password::min(10)->mixedCase()->numbers()->symbols()],
            'company_id' => 'required|exists:companies,id',
        ]);

        // Verify the company is active and accepting registrations
        $company = \App\Models\Company::find($data['company_id']);
        if (!$company || !$company->is_active) {
            return $this->error('This company is not accepting registrations.', 403);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'company_id' => $data['company_id'],
            'user_type' => 'member',
            'is_active' => true,
        ]);

        $expiryHours = config('security.api.token_expiry_hours', 720);
        $token = $user->createToken('api-token', ['*'], now()->addHours($expiryHours))->plainTextToken;

        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], 'Registration successful', 201);
    }

    /**
     * POST /api/v1/auth/logout
     * Revoke the current token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(message: 'Logged out successfully');
    }

    /**
     * GET /api/v1/auth/me
     * Get the authenticated user's profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('company');

        return $this->success([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'company' => $user->company ? [
                'id' => $user->company->id,
                'name' => $user->company->name,
            ] : null,
            'user_type' => $user->user_type,
            'job_title' => $user->job_title,
            'department' => $user->department,
            'phone' => $user->phone,
            'avatar' => $user->avatar,
            'timezone' => $user->timezone,
            'roles' => $user->getRoleNames(),
            'last_login_at' => $user->last_login_at?->toISOString(),
        ]);
    }

    /**
     * POST /api/v1/auth/tokens
     * Create a named API token.
     */
    public function createToken(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'abilities' => 'array',
            'abilities.*' => 'string',
        ]);

        // Enforce max tokens per user
        $maxTokens = config('security.api.max_tokens_per_user', 5);
        $existingTokenCount = $request->user()->tokens()->count();
        if ($existingTokenCount >= $maxTokens) {
            return $this->error("Maximum of {$maxTokens} API tokens allowed. Revoke an existing token first.", 422);
        }

        // Restrict abilities to safe operations (never allow wildcard from user input)
        $allowedAbilities = ['read', 'write', 'projects:read', 'projects:write', 'documents:read', 'documents:write', 'tasks:read', 'tasks:write'];
        $requestedAbilities = $request->abilities ?? ['read'];
        $safeAbilities = array_intersect($requestedAbilities, $allowedAbilities);
        if (empty($safeAbilities)) {
            $safeAbilities = ['read'];
        }

        $expiryHours = config('security.api.token_expiry_hours', 720);
        $token = $request->user()->createToken(
            $request->name,
            $safeAbilities,
            now()->addHours($expiryHours),
        );

        return $this->success([
            'token' => $token->plainTextToken,
            'name' => $token->accessToken->name,
            'abilities' => $token->accessToken->abilities,
            'expires_at' => $token->accessToken->expires_at?->toISOString(),
        ], 'Token created', 201);
    }

    /**
     * DELETE /api/v1/auth/tokens/{tokenId}
     * Revoke a specific token.
     */
    public function revokeToken(Request $request, string $tokenId): JsonResponse
    {
        $deleted = $request->user()->tokens()
            ->where('id', $tokenId)
            ->delete();

        if (!$deleted) {
            return $this->error('Token not found', 404);
        }

        return $this->success(message: 'Token revoked');
    }

    /**
     * Record login activity to database.
     */
    protected function logLoginActivity(
        string $email,
        ?int $userId,
        ?string $ip,
        ?string $userAgent,
        string $status,
        ?string $failureReason = null
    ): void {
        try {
            if (Schema::hasTable('login_activities')) {
                DB::table('login_activities')->insert([
                    'user_id' => $userId,
                    'email' => $email,
                    'ip_address' => $ip ?? '0.0.0.0',
                    'user_agent' => substr($userAgent ?? '', 0, 500),
                    'status' => $status,
                    'failure_reason' => $failureReason,
                    'created_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            // Don't break login flow for audit logging failures
            Log::warning('Failed to log login activity: ' . $e->getMessage());
        }
    }
}
