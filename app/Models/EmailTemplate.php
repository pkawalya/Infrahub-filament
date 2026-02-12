<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class EmailTemplate extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'slug',
        'subject',
        'body',
        'available_variables',
        'category',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'available_variables' => 'array',
        'is_active' => 'boolean',
    ];

    // -- Relationships --

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // -- Scopes --

    public function scopeGlobal(Builder $query): Builder
    {
        return $query->whereNull('company_id');
    }

    public function scopeForCompany(Builder $query, int $companyId): Builder
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // -- Helpers --

    public function isGlobal(): bool
    {
        return is_null($this->company_id);
    }

    /**
     * Render the template body by replacing {{variable}} placeholders.
     */
    public function render(array $data = []): string
    {
        $body = $this->body;

        foreach ($data as $key => $value) {
            $body = str_replace('{{' . $key . '}}', (string) $value, $body);
        }

        return $body;
    }

    /**
     * Render the subject line by replacing {{variable}} placeholders.
     */
    public function renderSubject(array $data = []): string
    {
        $subject = $this->subject;

        foreach ($data as $key => $value) {
            $subject = str_replace('{{' . $key . '}}', (string) $value, $subject);
        }

        return $subject;
    }

    /**
     * Resolve the best template: company override first, then global fallback.
     */
    public static function resolve(string $slug, ?int $companyId = null): ?self
    {
        if ($companyId) {
            $template = static::where('slug', $slug)
                ->where('company_id', $companyId)
                ->active()
                ->first();

            if ($template) {
                return $template;
            }
        }

        return static::where('slug', $slug)
            ->global()
            ->active()
            ->first();
    }

    /**
     * Standard template categories.
     */
    public static function categories(): array
    {
        return [
            'general' => 'General',
            'authentication' => 'Authentication',
            'project' => 'Project',
            'task' => 'Task & Workflow',
            'notification' => 'Notification',
            'reporting' => 'Reporting',
            'billing' => 'Billing & Subscription',
        ];
    }

    /**
     * Common variables available for templates.
     */
    public static function commonVariables(): array
    {
        return [
            'user_name' => 'Recipient\'s full name',
            'user_email' => 'Recipient\'s email address',
            'company_name' => 'Company name',
            'app_name' => 'Application name',
            'app_url' => 'Application URL',
            'current_date' => 'Current date',
            'current_year' => 'Current year',
        ];
    }

    /**
     * Category-specific variables.
     */
    public static function variablesForCategory(string $category): array
    {
        $common = static::commonVariables();

        $specific = match ($category) {
            'authentication' => [
                'reset_link' => 'Password reset link',
                'verification_link' => 'Email verification link',
                'login_url' => 'Login page URL',
            ],
            'project' => [
                'project_name' => 'Project name',
                'project_code' => 'Project code',
                'project_status' => 'Project status',
                'project_url' => 'Link to the project',
                'assigned_by' => 'Name of person who assigned',
                'role_in_project' => 'User\'s role in the project',
            ],
            'task' => [
                'task_title' => 'Task title',
                'task_description' => 'Task description',
                'task_status' => 'Task status',
                'task_priority' => 'Task priority',
                'task_due_date' => 'Task due date',
                'task_url' => 'Link to the task',
                'assigned_by' => 'Assigned by',
            ],
            'notification' => [
                'notification_title' => 'Notification title',
                'notification_body' => 'Notification body',
                'action_url' => 'Action URL',
            ],
            'reporting' => [
                'report_name' => 'Report name',
                'report_period' => 'Report period',
                'report_url' => 'Link to report',
            ],
            'billing' => [
                'plan_name' => 'Subscription plan name',
                'amount' => 'Amount',
                'billing_cycle' => 'Billing cycle',
                'expiry_date' => 'Expiry date',
                'invoice_url' => 'Invoice URL',
            ],
            default => [],
        };

        return array_merge($common, $specific);
    }
}
