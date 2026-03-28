<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskDueSoonNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Task $task) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $daysLeft = now()->diffInDays($this->task->due_date, false);
        $when = $daysLeft <= 0 ? 'today' : "in {$daysLeft} day(s)";

        return (new MailMessage)
            ->subject("⏰ Task Due {$when}: {$this->task->title}")
            ->greeting("Hi {$notifiable->name},")
            ->line("A task assigned to you is due {$when}.")
            ->line("**Task:** {$this->task->title}")
            ->line("**Project:** " . ($this->task->project?->name ?? '—'))
            ->line("**Due:** " . $this->task->due_date?->format('M j, Y'))
            ->line("**Status:** " . ucfirst(str_replace('_', ' ', $this->task->status)))
            ->action('View Task', url("/app/tasks/{$this->task->id}"))
            ->line('Please update the task status when complete.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'     => 'task_due_soon',
            'task_id'  => $this->task->id,
            'title'    => $this->task->title,
            'due_date' => $this->task->due_date?->toDateString(),
            'project'  => $this->task->project?->name,
            'message'  => "Task \"{$this->task->title}\" is due " . $this->task->due_date?->diffForHumans(),
        ];
    }
}
