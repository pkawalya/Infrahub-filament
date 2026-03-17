<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Support\Facades\Log;

/**
 * Service for creating and sending user invitations.
 *
 * Usage:
 *   // When creating a user from Admin panel
 *   app(InvitationService::class)->sendInvitation($user);
 *
 *   // When creating a company with admin user
 *   app(InvitationService::class)->sendInvitation($user, plainPassword: $password);
 *
 *   // Resend an invitation
 *   app(InvitationService::class)->resendInvitation($user);
 */
class InvitationService
{
    public function __construct(
        protected EmailService $emailService,
    ) {
    }

    /**
     * Create and send an invitation email to a user.
     */
    public function sendInvitation(
        User $user,
        ?string $plainPassword = null,
        ?int $invitedBy = null,
        int $expiryDays = 7
    ): ?UserInvitation {
        try {
            // Create the invitation record
            $invitation = UserInvitation::createForUser($user, $invitedBy, $expiryDays);

            // Determine login URL based on user type
            $loginUrl = $this->getLoginUrl($user);

            // Build email variables
            $variables = [
                'invitation_url' => $invitation->getAcceptUrl(),
                'login_url' => $loginUrl,
                'user_role' => User::$userTypes[$user->user_type] ?? $user->user_type,
                'inviter_name' => $this->getInviterName($invitedBy),
                'expiry_days' => $expiryDays,
                'expiry_date' => $invitation->expires_at->format('d M Y'),
            ];

            // Include password if provided (for newly created users)
            if ($plainPassword) {
                $variables['user_password'] = $plainPassword;
                $variables['has_password'] = true;
            }

            // Send the invitation email synchronously (critical, don't queue)
            $this->emailService->send(
                'user-invitation',
                $user,
                $variables,
                $user->company_id,
                sync: true,
            );

            Log::info("InvitationService: Sent invitation to {$user->email}", [
                'invitation_id' => $invitation->id,
                'user_id' => $user->id,
            ]);

            return $invitation;

        } catch (\Throwable $e) {
            Log::error("InvitationService: Failed to send invitation to {$user->email}", [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            return null;
        }
    }

    /**
     * Resend an invitation for an existing user.
     */
    public function resendInvitation(User $user, ?int $invitedBy = null): ?UserInvitation
    {
        return $this->sendInvitation($user, null, $invitedBy);
    }

    /**
     * Get the appropriate login URL based on user type.
     */
    protected function getLoginUrl(User $user): string
    {
        return match ($user->user_type) {
            'super_admin' => url('/admin/login'),
            'client' => url('/client/login'),
            default => url('/app/login'),
        };
    }

    /**
     * Get the inviter's name.
     */
    protected function getInviterName(?int $invitedBy): string
    {
        if ($invitedBy) {
            return User::find($invitedBy)?->name ?? 'An administrator';
        }

        return auth()->user()?->name ?? 'An administrator';
    }
}
