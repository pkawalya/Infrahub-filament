<?php

namespace App\Notifications;

use App\Models\WorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkOrderDueSoonNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public WorkOrder $wo) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $daysLeft = now()->diffInDays($this->wo->due_date, false);
        $when = $daysLeft <= 0 ? 'today' : "in {$daysLeft} day(s)";

        return (new MailMessage)
            ->subject("🔧 Work Order Due {$when}: [{$this->wo->wo_number}] {$this->wo->title}")
            ->greeting("Hi {$notifiable->name},")
            ->line("A work order assigned to you is due {$when}.")
            ->line("**WO #:** {$this->wo->wo_number}")
            ->line("**Title:** {$this->wo->title}")
            ->line("**Priority:** " . ucfirst($this->wo->priority ?? '—'))
            ->line("**Due:** " . ($this->wo->due_date ? \Carbon\Carbon::parse($this->wo->due_date)->format('M j, Y') : '—'))
            ->action('View Work Order', url("/app/work-orders/{$this->wo->id}"))
            ->line('Please update the work order when complete.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'     => 'work_order_due_soon',
            'wo_id'    => $this->wo->id,
            'wo_number'=> $this->wo->wo_number,
            'title'    => $this->wo->title,
            'due_date' => $this->wo->due_date ? \Carbon\Carbon::parse($this->wo->due_date)->toDateString() : null,
            'message'  => "Work Order {$this->wo->wo_number} is due " . ($this->wo->due_date ? \Carbon\Carbon::parse($this->wo->due_date)->diffForHumans() : '—'),
        ];
    }
}
