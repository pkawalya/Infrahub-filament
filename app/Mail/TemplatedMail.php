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
        // Resolve company branding for the email
        $companyId = $this->template->company_id;
        $company = $companyId ? \App\Models\Company::find($companyId) : null;

        // Also try to find company from variables
        if (!$company && isset($this->variables['company_id'])) {
            $company = \App\Models\Company::find($this->variables['company_id']);
        }

        $branding = $company ? $company->getBranding() : [];

        return new \Illuminate\Mail\Mailables\Content(
            view: 'emails.templated',
            with: [
                'body' => $this->renderedBody,
                'templateName' => $this->template->name,
                'companyName' => $branding['name'] ?? $this->variables['company_name'] ?? config('app.name'),
                'companyLogoUrl' => $branding['logo_url'] ?? null,
                'companyPrimaryColor' => $branding['primary_color'] ?? '#4f46e5',
                'companyEmail' => $branding['email'] ?? null,
                'companyWebsite' => $branding['website'] ?? null,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
