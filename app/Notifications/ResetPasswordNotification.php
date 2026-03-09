<?php

namespace App\Notifications;

use App\Mail\TemplatedMail;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * Custom password reset notification that uses the branded
 * EmailTemplate system instead of Laravel's default plain-text mail.
 *
 * Falls back to Filament's default notification if no template exists.
 */
class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $token,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): TemplatedMail|\Illuminate\Notifications\Messages\MailMessage
    {
        $resetUrl = $this->buildResetUrl($notifiable);

        // Try to use our branded email template
        $companyId = $notifiable->company_id ?? null;
        $template = EmailTemplate::resolve('password-reset', $companyId);

        if ($template) {
            $variables = [
                'user_name' => $notifiable->name,
                'user_email' => $notifiable->email,
                'company_name' => $notifiable->company?->name ?? config('app.name'),
                'company_id' => $companyId,
                'reset_url' => $resetUrl,
                'expire_minutes' => config('auth.passwords.users.expire', 60),
            ];

            return new TemplatedMail($template, $variables);
        }

        // Fallback: standard Laravel notification mail
        Log::info('ResetPasswordNotification: No branded template found, using default.');

        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Reset Your Password — ' . config('app.name'))
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $resetUrl)
            ->line('This password reset link will expire in ' . config('auth.passwords.users.expire', 60) . ' minutes.')
            ->line('If you did not request a password reset, no further action is required.');
    }

    /**
     * Build the password reset URL for the correct panel.
     */
    protected function buildResetUrl(object $notifiable): string
    {
        // Determine which panel the user should use
        $panelId = ($notifiable->user_type === 'super_admin') ? 'admin' : 'app';

        try {
            $panel = \Filament\Facades\Filament::getPanel($panelId);
            return $panel->getPasswordResetUrl($this->token, $notifiable);
        } catch (\Exception $e) {
            // Fallback — build URL manually
            $path = $panelId === 'admin' ? 'admin' : 'app';
            return url("/{$path}/password-reset/reset?token={$this->token}&email=" . urlencode($notifiable->email));
        }
    }
}
