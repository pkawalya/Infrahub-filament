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
            'mail_host' => 'smtp.gmail.com',
            'mail_port' => '587',
            'mail_username' => 'appcellon@gmail.com',
            'mail_password' => 'elch ocax mkfk jssf',
            'mail_encryption' => 'auto',
            'mail_from_address' => 'appcellon@gmail.com',
            'mail_from_name' => 'InfraHub',
        ];

        foreach ($settings as $key => $value) {
            Setting::setValue($key, $value, 'email');
        }

        $this->command->info('✅ Default email settings seeded (Gmail SMTP).');
    }
}
