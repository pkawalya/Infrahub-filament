<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * 2FA sign-in code notification.
 *
 * Sent SYNCHRONOUSLY (not queued) because the user is actively
 * waiting at the login screen. Queuing adds latency and failure
 * risk if the queue worker is down or misconfigured.
 */
class VerifyEmailAuthenticationNotification extends Notification
{
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
        Log::info('Sending 2FA code to: ' . ($notifiable->email ?? 'unknown'));

        return (new MailMessage)
            ->subject('Your Sign-In Code — ' . config('app.name', 'App'))
            ->view('emails.auth.sign-in-code', [
                'code' => $this->code,
                'expiryMinutes' => $this->codeExpiryMinutes,
                'userName' => $notifiable->name ?? 'there',
            ]);
    }
}
