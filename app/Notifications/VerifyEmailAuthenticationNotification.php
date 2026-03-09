<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

/**
 * 2FA sign-in code notification.
 *
 * Queued for speed (so the login page responds instantly).
 * If the queue worker is down, Laravel will still attempt to
 * process it once the worker comes back up.
 */
class VerifyEmailAuthenticationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Retry up to 3 times, timeout after 30 seconds.
     */
    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(
        public string $code,
        public int $codeExpiryMinutes,
    ) {
        // Process on the 'mail' queue if available, otherwise default
        $this->onQueue('mail');
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

    /**
     * Log when the notification fails permanently.
     */
    public function failed(\Throwable $e): void
    {
        Log::error('2FA email failed: ' . $e->getMessage());
    }
}
