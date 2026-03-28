<?php

namespace App\Notifications;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionLimitWarningNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Company $company,
        public array   $warnings
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("⚠️ InfraHub: Subscription Limit Warning — {$this->company->name}")
            ->greeting("Hi {$notifiable->name},")
            ->line("Your company **{$this->company->name}** is approaching one or more plan limits:");

        foreach ($this->warnings as $warning) {
            $mail->line("• {$warning}");
        }

        return $mail
            ->action('Upgrade Plan', url('/app/settings/upgrade'))
            ->line('Upgrade your plan or add add-ons to avoid service interruption.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => 'subscription_limit_warning',
            'company_id' => $this->company->id,
            'warnings'   => $this->warnings,
            'message'    => 'Your subscription has ' . count($this->warnings) . ' limit warning(s). Check your plan.',
        ];
    }
}
