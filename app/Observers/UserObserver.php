<?php

namespace App\Observers;

use App\Models\User;
use App\Services\EmailService;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * When a new user is created, send them a welcome email
     * with their login credentials and a link to the platform.
     */
    public function created(User $user): void
    {
        // Only send if the user was created by an admin (not self-registration)
        // Self-registered users go through email verification instead.
        if (!auth()->check()) {
            return;
        }

        // The plain password is temporarily set on the model by the
        // CreateUser / CreateCompanyUser pages before saving.
        $plainPassword = $user->plainPassword;

        if (!$plainPassword) {
            return;
        }

        // Mark user to change password on first login
        $user->updateQuietly(['must_change_password' => true]);

        $loginUrl = url('/app/login');

        try {
            $emailService = app(EmailService::class);

            $emailService->send('welcome-new-user', $user, [
                'login_url' => $loginUrl,
                'user_password' => $plainPassword,
                'user_role' => User::$userTypes[$user->user_type] ?? $user->user_type,
                'creator_name' => auth()->user()->name,
            ], $user->company_id);

        } catch (\Throwable $e) {
            Log::warning('Failed to send welcome email to ' . $user->email . ': ' . $e->getMessage());
        }
    }
}
