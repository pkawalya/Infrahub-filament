<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Branded 2FA sign-in code notification.
 *
 * Replaces Filament's default plain-text email with a branded
 * template that uses the InfraHub email layout, includes the logo,
 * and renders the OTP code in a large, easy-to-copy format.
 */
class VerifyEmailAuthenticationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $code,
        public int $codeExpiryMinutes,
    ) {
    }

    /**
     * @return array<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Sign-In Code — ' . config('app.name', 'InfraHub'))
            ->view('emails.auth.sign-in-code', [
                'code' => $this->code,
                'expiryMinutes' => $this->codeExpiryMinutes,
                'userName' => $notifiable->name ?? 'there',
            ]);
    }
}
