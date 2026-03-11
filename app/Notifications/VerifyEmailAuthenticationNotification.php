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
 * Includes login context (IP, device) for security awareness.
 */
class VerifyEmailAuthenticationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(
        public string $code,
        public int $codeExpiryMinutes,
    ) {
        //
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

        // Capture request context (may be null in queue, so use defaults)
        $ip = request()?->ip() ?? 'Unknown';
        $userAgent = request()?->userAgent() ?? '';

        // Parse user agent into a readable device string
        $device = $this->parseUserAgent($userAgent);

        return (new MailMessage)
            ->subject('Sign-In Verification — ' . config('app.name', 'App'))
            ->view('emails.auth.sign-in-code', [
                'code' => $this->code,
                'expiryMinutes' => $this->codeExpiryMinutes,
                'userName' => $notifiable->name ?? 'there',
                'ipAddress' => $ip,
                'userAgent' => $device,
                'loginTime' => now()->format('F j, Y, H:i'),
            ]);
    }

    /**
     * Parse a user-agent string into a human-readable device description.
     */
    private function parseUserAgent(string $ua): string
    {
        if (empty($ua)) {
            return '';
        }

        $browser = 'Unknown Browser';
        $os = 'Unknown OS';

        // Detect browser
        if (str_contains($ua, 'Chrome') && !str_contains($ua, 'Edg')) {
            $browser = 'Google Chrome';
        } elseif (str_contains($ua, 'Firefox')) {
            $browser = 'Firefox';
        } elseif (str_contains($ua, 'Safari') && !str_contains($ua, 'Chrome')) {
            $browser = 'Safari';
        } elseif (str_contains($ua, 'Edg')) {
            $browser = 'Microsoft Edge';
        }

        // Detect OS
        if (str_contains($ua, 'Windows')) {
            $os = 'Windows';
        } elseif (str_contains($ua, 'Mac OS')) {
            $os = 'macOS';
        } elseif (str_contains($ua, 'Linux')) {
            $os = 'Linux';
        } elseif (str_contains($ua, 'Android')) {
            $os = 'Android';
        } elseif (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) {
            $os = 'iOS';
        }

        // Detect device type
        $type = 'Desktop';
        if (str_contains($ua, 'Mobile') || str_contains($ua, 'Android')) {
            $type = 'Mobile';
        } elseif (str_contains($ua, 'iPad') || str_contains($ua, 'Tablet')) {
            $type = 'Tablet';
        }

        return "{$type}, {$browser}, {$os}";
    }

    /**
     * Log when the notification fails permanently.
     */
    public function failed(\Throwable $e): void
    {
        Log::error('2FA email failed: ' . $e->getMessage());
    }
}
