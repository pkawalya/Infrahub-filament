<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Generic Mailable that renders content from an EmailTemplate record.
 *
 * Usage:
 *   $template = EmailTemplate::resolve('project-assigned', $companyId);
 *   Mail::to($user)->send(new TemplatedMail($template, [
 *       'user_name' => $user->name,
 *       'project_name' => $project->name,
 *   ]));
 */
class TemplatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $renderedBody;
    public string $renderedSubject;

    public function __construct(
        public EmailTemplate $template,
        public array $variables = [],
    ) {
        // Merge common variables
        $this->variables = array_merge([
            'app_name' => config('app.name', 'InfraHub'),
            'app_url' => config('app.url'),
            'current_date' => now()->format('d M Y'),
            'current_year' => now()->format('Y'),
        ], $this->variables);

        $this->renderedSubject = $this->template->renderSubject($this->variables);
        $this->renderedBody = $this->template->render($this->variables);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->renderedSubject,
        );
    }

    public function content()
    {
        // Use a generic email layout with the rendered HTML body
        return new \Illuminate\Mail\Mailables\Content(
            view: 'emails.templated',
            with: [
                'body' => $this->renderedBody,
                'templateName' => $this->template->name,
                'companyName' => $this->variables['company_name'] ?? config('app.name'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
