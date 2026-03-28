<?php

namespace App\Observers;

use App\Models\User;
use App\Services\EmailService;
use App\Services\InvitationService;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * When a new user is created, send them a welcome email
     * with their login credentials and an invitation email.
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

        $loginUrl = match ($user->user_type) {
            'super_admin' => url('/admin/login'),
            'client' => url('/client/login'),
            default => url('/app/login'),
        };

        try {
            $emailService = app(EmailService::class);

            // 1. Send the welcome email with credentials
            $emailService->send('welcome-new-user', $user, [
                'login_url' => $loginUrl,
                'user_password' => $plainPassword,
                'user_role' => User::$userTypes[$user->user_type] ?? $user->user_type,
                'creator_name' => auth()->user()->name,
            ], $user->company_id);

            // 2. Send the invitation email with accept link
            $invitationService = app(InvitationService::class);
            $invitationService->sendInvitation(
                $user,
                $plainPassword,
                auth()->id()
            );

        } catch (\Throwable $e) {
            Log::warning('Failed to send welcome/invitation email to ' . $user->email . ': ' . $e->getMessage());
        }

        // Bust cached user count so plan limits reflect the new user immediately
        $user->company?->bustLimitCaches();
    }

    public function deleted(User $user): void
    {
        $user->company?->bustLimitCaches();
    }

    public function restored(User $user): void
    {
        $user->company?->bustLimitCaches();
    }
}
