<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
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

        // Rate limiting: 5 attempts per minute per email
        $rateLimitKey = 'api-login:' . strtolower($request->email);
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return $this->error("Too many login attempts. Try again in {$seconds} seconds.", 429);
        }
        RateLimiter::hit($rateLimitKey, 60);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            return $this->error('Account is deactivated. Contact your administrator.', 403);
        }

        // SaaS: Verify the user's company is active
        if ($user->company && !$user->company->is_active) {
            return $this->error('Your company account is inactive. Contact support.', 403);
        }

        RateLimiter::clear($rateLimitKey);

        $deviceName = $request->device_name ?? ($request->userAgent() ?: 'api-token');

        $token = $user->createToken($deviceName, ['*'])->plainTextToken;

        $user->update(['last_login_at' => now()]);

        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'company_id' => $user->company_id,
                'user_type' => $user->user_type,
                'avatar' => $user->avatar,
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
            'password' => 'required|string|min:8|confirmed',
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

        $token = $user->createToken('api-token')->plainTextToken;

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

        // Restrict abilities to safe operations (never allow wildcard from user input)
        $allowedAbilities = ['read', 'write', 'projects:read', 'projects:write', 'documents:read', 'documents:write', 'tasks:read', 'tasks:write'];
        $requestedAbilities = $request->abilities ?? ['read'];
        $safeAbilities = array_intersect($requestedAbilities, $allowedAbilities);
        if (empty($safeAbilities)) {
            $safeAbilities = ['read'];
        }

        $token = $request->user()->createToken(
            $request->name,
            $safeAbilities,
        );

        return $this->success([
            'token' => $token->plainTextToken,
            'name' => $token->accessToken->name,
            'abilities' => $token->accessToken->abilities,
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
}
