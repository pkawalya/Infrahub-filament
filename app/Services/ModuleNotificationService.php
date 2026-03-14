<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Filament\Notifications\Notification;

/**
 * Central notification dispatcher for all module events.
 *
 * Sends both:
 * 1. In-app Filament database notifications
 * 2. Email via EmailTemplate (if template exists and is active)
 *
 * Usage:
 *   app(ModuleNotificationService::class)->notify(
 *       slug: 'task-assigned',
 *       recipients: [$user],
 *       variables: ['task_title' => 'Review blueprints', 'project_name' => 'Tower A'],
 *       actionUrl: '/app/tasks/42',
 *       companyId: $user->company_id
 *   );
 */
class ModuleNotificationService
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Send notification to one or more users via all channels.
     *
     * @param string      $slug       Template slug (e.g., 'task-assigned')
     * @param User[]      $recipients Array of User models to notify
     * @param array       $variables  Template variables for rendering
     * @param string|null $actionUrl  URL for "View" button in notification
     * @param int|null    $companyId  Company context for template resolution
     */
    public function notify(
        string $slug,
        array $recipients,
        array $variables = [],
        ?string $actionUrl = null,
        ?int $companyId = null,
    ): void {
        if (empty($recipients)) {
            return;
        }

        // Resolve template for title/body rendering
        $template = EmailTemplate::resolve($slug, $companyId);
        $title = $template?->renderSubject($variables) ?? $this->fallbackTitle($slug, $variables);
        $body = $template ? strip_tags(substr($template->render($variables), 0, 300)) : ($variables['body'] ?? '');

        foreach ($recipients as $user) {
            if (!$user instanceof User)
                continue;

            // ── In-app notification (Filament database) ────
            try {
                $notification = Notification::make()
                    ->title($title)
                    ->body($body)
                    ->icon($this->iconForSlug($slug))
                    ->iconColor($this->colorForSlug($slug));

                if ($actionUrl) {
                    $notification->actions([
                        \Filament\Notifications\Actions\Action::make('view')
                            ->label('View')
                            ->url($actionUrl),
                    ]);
                }

                $notification->sendToDatabase($user);
            } catch (\Throwable $e) {
                Log::warning("ModuleNotification: in-app failed for {$user->email}: {$e->getMessage()}");
            }

            // ── Email notification ─────────────────────────
            try {
                if ($template && $template->is_active) {
                    $this->emailService->send($slug, $user, $variables, $companyId ?? $user->company_id);
                }
            } catch (\Throwable $e) {
                Log::warning("ModuleNotification: email failed for {$user->email}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Notify a single user (convenience method).
     */
    public function notifyUser(
        string $slug,
        User $user,
        array $variables = [],
        ?string $actionUrl = null,
    ): void {
        $this->notify($slug, [$user], $variables, $actionUrl, $user->company_id);
    }

    /**
     * Notify all company admins for a company.
     */
    public function notifyCompanyAdmins(
        string $slug,
        int $companyId,
        array $variables = [],
        ?string $actionUrl = null,
    ): void {
        $admins = User::where('company_id', $companyId)
            ->where('user_type', 'company_admin')
            ->where('is_active', true)
            ->get()
            ->all();

        $this->notify($slug, $admins, $variables, $actionUrl, $companyId);
    }

    /**
     * Notify project team members.
     */
    public function notifyProjectTeam(
        string $slug,
        int $projectId,
        array $variables = [],
        ?string $actionUrl = null,
        ?int $excludeUserId = null,
    ): void {
        $project = \App\Models\CdeProject::with('members')->find($projectId);
        if (!$project)
            return;

        $members = $project->members
            ->where('is_active', true)
            ->when($excludeUserId, fn($c) => $c->where('id', '!=', $excludeUserId))
            ->all();

        $variables['project_name'] = $variables['project_name'] ?? $project->name;
        $variables['project_code'] = $variables['project_code'] ?? $project->project_code;

        $this->notify($slug, $members, $variables, $actionUrl, $project->company_id);
    }

    /**
     * Fallback title when no template exists.
     */
    protected function fallbackTitle(string $slug, array $variables): string
    {
        $title = str_replace(['-', '_'], ' ', $slug);
        return ucwords($title);
    }

    /**
     * Icon mapping per notification category.
     */
    protected function iconForSlug(string $slug): string
    {
        return match (true) {
            str_contains($slug, 'task') => 'heroicon-o-clipboard-document-list',
            str_contains($slug, 'document') => 'heroicon-o-document-text',
            str_contains($slug, 'project') => 'heroicon-o-building-office',
            str_contains($slug, 'change-order') => 'heroicon-o-document-plus',
            str_contains($slug, 'drawing') => 'heroicon-o-map',
            str_contains($slug, 'payment') || str_contains($slug, 'invoice') => 'heroicon-o-banknotes',
            str_contains($slug, 'safety') => 'heroicon-o-shield-check',
            str_contains($slug, 'diary') || str_contains($slug, 'field') => 'heroicon-o-book-open',
            str_contains($slug, 'equipment') => 'heroicon-o-truck',
            str_contains($slug, 'attendance') || str_contains($slug, 'crew') => 'heroicon-o-user-group',
            str_contains($slug, 'rfi') => 'heroicon-o-question-mark-circle',
            str_contains($slug, 'submittal') => 'heroicon-o-paper-airplane',
            str_contains($slug, 'overdue') || str_contains($slug, 'alert') => 'heroicon-o-exclamation-triangle',
            // ── Inventory ──
            str_contains($slug, 'purchase-order') || str_contains($slug, 'po-') => 'heroicon-o-shopping-cart',
            str_contains($slug, 'grn') || str_contains($slug, 'goods-received') => 'heroicon-o-inbox-arrow-down',
            str_contains($slug, 'requisition') => 'heroicon-o-clipboard-document-list',
            str_contains($slug, 'issuance') || str_contains($slug, 'issued') => 'heroicon-o-clipboard-document-check',
            str_contains($slug, 'transfer') || str_contains($slug, 'stock-transfer') => 'heroicon-o-arrows-right-left',
            str_contains($slug, 'adjustment') || str_contains($slug, 'stock-adj') => 'heroicon-o-scale',
            str_contains($slug, 'asset') => 'heroicon-o-tag',
            str_contains($slug, 'low-stock') => 'heroicon-o-exclamation-triangle',
            default => 'heroicon-o-bell',
        };
    }

    /**
     * Color mapping per notification category.
     */
    protected function colorForSlug(string $slug): string
    {
        return match (true) {
            str_contains($slug, 'approved') || str_contains($slug, 'completed') => 'success',
            str_contains($slug, 'rejected') || str_contains($slug, 'overdue') || str_contains($slug, 'alert') || str_contains($slug, 'low-stock') => 'danger',
            str_contains($slug, 'submitted') || str_contains($slug, 'review') || str_contains($slug, 'pending') => 'warning',
            str_contains($slug, 'assigned') || str_contains($slug, 'created') || str_contains($slug, 'issued') || str_contains($slug, 'received') => 'info',
            default => 'primary',
        };
    }
}
