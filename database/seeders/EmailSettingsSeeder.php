<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class EmailSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'mail_mailer' => 'smtp',
            'mail_host' => 'mail.infrahub.click',
            'mail_port' => '465',
            'mail_username' => 'inotify@infrahub.click',
            'mail_password' => 'Hub@256!!@@',
            'mail_encryption' => 'smtps',
            'mail_from_address' => 'inotify@infrahub.click',
            'mail_from_name' => 'InfraHub',
        ];

        foreach ($settings as $key => $value) {
            Setting::setValue($key, $value, 'email');
        }

        $this->command->info('✅ Default email settings seeded (InfraHub SMTP).');
    }
}
