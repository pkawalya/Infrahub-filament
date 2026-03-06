<?php

namespace App\Services;

use App\Mail\TemplatedMail;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Central service for sending emails using EmailTemplate records.
 *
 * Usage examples:
 *
 *   // Simple send — resolves template by slug, renders variables, sends
 *   app(EmailService::class)->send('project-assigned', $user, [
 *       'project_name' => $project->name,
 *       'assigned_by' => $assigner->name,
 *   ]);
 *
 *   // With company override (company templates take priority over global)
 *   app(EmailService::class)->send('invoice-sent', $user, $vars, $companyId);
 *
 *   // Send to raw email address
 *   app(EmailService::class)->sendTo('client@example.com', 'quotation-sent', $vars, $companyId);
 *
 *   // Preview rendered template (for testing in admin panel)
 *   $result = app(EmailService::class)->preview('task-assigned', $vars, $companyId);
 */
class EmailService
{
    /**
     * Send a templated email to a User model.
     *
     * Auto-populates user_name, user_email, company_name from the user.
     */
    public function send(string $slug, User $recipient, array $variables = [], ?int $companyId = null): bool
    {
        $companyId = $companyId ?? $recipient->company_id;

        $template = EmailTemplate::resolve($slug, $companyId);

        if (!$template) {
            Log::warning("EmailService: No template found for slug '{$slug}' (company_id: {$companyId})");
            return false;
        }

        // Auto-populate user variables
        $variables = array_merge([
            'user_name' => $recipient->name,
            'user_email' => $recipient->email,
            'company_name' => $recipient->company?->name ?? config('app.name'),
        ], $variables);

        return $this->dispatch($recipient->email, $template, $variables);
    }

    /**
     * Send a templated email to a raw email address.
     */
    public function sendTo(string $email, string $slug, array $variables = [], ?int $companyId = null): bool
    {
        $template = EmailTemplate::resolve($slug, $companyId);

        if (!$template) {
            Log::warning("EmailService: No template found for slug '{$slug}' (company_id: {$companyId})");
            return false;
        }

        return $this->dispatch($email, $template, $variables);
    }

    /**
     * Send to multiple users at once.
     */
    public function sendBulk(string $slug, iterable $recipients, array $commonVariables = [], ?int $companyId = null): int
    {
        $sent = 0;

        foreach ($recipients as $recipient) {
            if ($recipient instanceof User) {
                $success = $this->send($slug, $recipient, $commonVariables, $companyId);
            } else {
                // Assume it's an email string
                $success = $this->sendTo((string) $recipient, $slug, $commonVariables, $companyId);
            }

            if ($success) {
                $sent++;
            }
        }

        return $sent;
    }

    /**
     * Preview a rendered template without sending (for admin panel preview feature).
     */
    public function preview(string $slug, array $variables = [], ?int $companyId = null): ?array
    {
        $template = EmailTemplate::resolve($slug, $companyId);

        if (!$template) {
            return null;
        }

        // Merge common variables for preview
        $variables = array_merge([
            'app_name' => config('app.name', 'InfraHub'),
            'app_url' => config('app.url'),
            'current_date' => now()->format('d M Y'),
            'current_year' => now()->format('Y'),
            'user_name' => 'John Doe',
            'user_email' => 'john@example.com',
            'company_name' => 'Demo Company',
        ], $variables);

        return [
            'template_id' => $template->id,
            'template_name' => $template->name,
            'slug' => $template->slug,
            'subject' => $template->renderSubject($variables),
            'body' => $template->render($variables),
            'variables_used' => array_keys($variables),
        ];
    }

    /**
     * Get all available template slugs, optionally filtered by category.
     */
    public function availableTemplates(?string $category = null): array
    {
        $query = EmailTemplate::active();

        if ($category) {
            $query->where('category', $category);
        }

        return $query->pluck('name', 'slug')->toArray();
    }

    /**
     * Internal: dispatch the email.
     */
    protected function dispatch(string $email, EmailTemplate $template, array $variables): bool
    {
        try {
            $mailable = new TemplatedMail($template, $variables);
            Mail::to($email)->queue($mailable);

            Log::info("EmailService: Queued '{$template->slug}' to {$email}");
            return true;
        } catch (\Exception $e) {
            Log::error("EmailService: Failed to send '{$template->slug}' to {$email}", [
                'error' => $e->getMessage(),
                'template_id' => $template->id,
            ]);
            return false;
        }
    }
}
