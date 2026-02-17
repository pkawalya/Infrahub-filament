<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;

class MailConfigServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Only override if settings exist in DB
        try {
            $mailer = Setting::getValue('mail_mailer');

            if ($mailer) {
                config([
                    'mail.default' => $mailer,
                    'mail.mailers.smtp.host' => Setting::getValue('mail_host', config('mail.mailers.smtp.host')),
                    'mail.mailers.smtp.port' => (int) Setting::getValue('mail_port', config('mail.mailers.smtp.port')),
                    'mail.mailers.smtp.username' => Setting::getValue('mail_username', config('mail.mailers.smtp.username')),
                    'mail.mailers.smtp.password' => Setting::getValue('mail_password', config('mail.mailers.smtp.password')),
                    'mail.mailers.smtp.encryption' => Setting::getValue('mail_encryption', config('mail.mailers.smtp.encryption')),
                    'mail.from.address' => Setting::getValue('mail_from_address', config('mail.from.address')),
                    'mail.from.name' => Setting::getValue('mail_from_name', config('mail.from.name')),
                ]);
            }
        } catch (\Throwable $e) {
            // Silently fail if DB not ready (migrations, etc.)
        }
    }
}
