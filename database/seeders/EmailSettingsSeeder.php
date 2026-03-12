<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class EmailSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'mail_mailer' => env('MAIL_MAILER', 'smtp'),
            'mail_host' => env('MAIL_HOST', 'mail.infrahub.click'),
            'mail_port' => env('MAIL_PORT', '465'),
            'mail_username' => env('MAIL_USERNAME', 'inotify@infrahub.click'),
            'mail_password' => env('MAIL_PASSWORD', ''),
            'mail_encryption' => env('MAIL_SCHEME', 'smtps'),
            'mail_from_address' => env('MAIL_FROM_ADDRESS', 'inotify@infrahub.click'),
            'mail_from_name' => env('APP_NAME', 'InfraHub'),
        ];

        foreach ($settings as $key => $value) {
            Setting::setValue($key, $value, 'email');
        }

        $this->command->info('✅ Default email settings seeded (InfraHub SMTP).');
    }
}
