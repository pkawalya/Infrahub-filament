<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages;

use App\Filament\App\Resources\CdeProjectResource;
use App\Models\CdeProject;
use App\Models\Module;
use App\Models\ProjectInvitation;
use App\Models\User;
use App\Services\EmailService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewCdeProject extends ViewRecord
{
    protected static string $resource = CdeProjectResource::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Overview';
    protected string $view = 'filament.app.pages.view-cde-project';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('invitePeople')
                ->label('Invite People')
                ->icon('heroicon-o-user-plus')
                ->color('primary')
                ->modalHeading('Invite People to ' . $this->record->name)
                ->modalDescription('Invite team members by email. One person can be invited to multiple projects.')
                ->modalIcon('heroicon-o-user-plus')
                ->form([
                    Forms\Components\Repeater::make('invites')
                        ->label('People to Invite')
                        ->schema([
                            Forms\Components\TextInput::make('email')
                                ->email()
                                ->required()
                                ->placeholder('name@company.com'),
                            Forms\Components\TextInput::make('name')
                                ->placeholder('Full name (optional)'),
                            Forms\Components\Select::make('role')
                                ->options(ProjectInvitation::$roles)
                                ->default('member')
                                ->required(),
                        ])
                        ->columns(3)
                        ->defaultItems(1)
                        ->addActionLabel('+ Add another person')
                        ->minItems(1)
                        ->maxItems(10),
                ])
                ->action(function (array $data) {
                    $project = $this->record;
                    $sent = 0;
                    $skipped = 0;

                    foreach ($data['invites'] as $invite) {
                        $email = strtolower(trim($invite['email']));

                        // Skip if user is already a project member
                        $existingUser = User::where('email', $email)
                            ->where('company_id', $project->company_id)
                            ->first();

                        if ($existingUser && $project->members()->where('user_id', $existingUser->id)->exists()) {
                            $skipped++;
                            continue;
                        }

                        // If user already exists in the company, add them directly
                        if ($existingUser) {
                            $project->members()->syncWithoutDetaching([
                                $existingUser->id => [
                                    'role' => $invite['role'],
                                    'invited_by' => auth()->id(),
                                ],
                            ]);
                            $sent++;
                            continue;
                        }

                        // Create invitation for external/new user
                        $invitation = ProjectInvitation::createInvitation(
                            companyId: $project->company_id,
                            projectId: $project->id,
                            email: $email,
                            role: $invite['role'],
                            name: $invite['name'] ?? null,
                            invitedBy: auth()->id(),
                        );

                        // Try sending the invitation email
                        try {
                            $emailService = app(EmailService::class);
                            $emailService->sendTo(
                                $email,
                                'project-invitation',
                                [
                                    'recipient_name' => $invite['name'] ?? $email,
                                    'project_name' => $project->name,
                                    'inviter_name' => auth()->user()->name,
                                    'company_name' => $project->company->name ?? 'InfraHub',
                                    'role' => ProjectInvitation::$roles[$invite['role']] ?? $invite['role'],
                                    'accept_url' => $invitation->getAcceptUrl(),
                                    'expires_at' => $invitation->expires_at->format('M d, Y'),
                                ],
                                $project->company_id,
                            );
                        } catch (\Throwable $e) {
                            \Illuminate\Support\Facades\Log::warning("ProjectInvitation email failed for {$email}: {$e->getMessage()}");
                        }

                        $sent++;
                    }

                    if ($sent > 0) {
                        Notification::make()
                            ->success()
                            ->title("{$sent} invitation(s) sent!")
                            ->body($skipped > 0 ? "{$skipped} skipped (already members)" : null)
                            ->send();
                    } else {
                        Notification::make()
                            ->warning()
                            ->title('No new invitations')
                            ->body('All emails are already project members.')
                            ->send();
                    }
                })
                ->modalSubmitActionLabel('Send Invitations'),

            Actions\Action::make('viewTeam')
                ->label('Team')
                ->icon('heroicon-o-users')
                ->color('gray')
                ->modalHeading('Project Team — ' . $this->record->name)
                ->modalContent(function () {
                    $project = $this->record;
                    $members = $project->members()->with('company')->get();
                    $pendingInvites = $project->invitations()
                        ->where('status', 'pending')
                        ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                        ->get();

                    return view('filament.app.pages.partials.project-team', [
                        'members' => $members,
                        'pendingInvites' => $pendingInvites,
                        'manager' => $project->manager,
                    ]);
                })
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),

            Actions\EditAction::make(),
        ];
    }

    public function getStats(): array
    {
        $pid = $this->record->id;
        $now = now();
        $weekStart = $now->copy()->startOfWeek();

        // Single query for all task stats
        $taskStats = \DB::selectOne("
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status NOT IN ('done','cancelled') THEN 1 ELSE 0 END) as open,
                SUM(CASE WHEN status NOT IN ('done','cancelled') AND due_date < ? THEN 1 ELSE 0 END) as overdue
            FROM tasks WHERE cde_project_id = ?
        ", [$now, $pid]);

        // Single query for document/folder/rfi stats
        $docStats = \DB::selectOne("
            SELECT
                (SELECT COUNT(*) FROM cde_documents WHERE cde_project_id = ?) as documents,
                (SELECT COUNT(*) FROM cde_documents WHERE cde_project_id = ? AND created_at >= ?) as docs_this_week,
                (SELECT COUNT(*) FROM cde_folders WHERE cde_project_id = ?) as folders,
                (SELECT COUNT(*) FROM rfis WHERE cde_project_id = ?) as rfis
        ", [$pid, $pid, $weekStart, $pid, $pid]);

        // Single query for incident stats
        $incidentStats = \DB::selectOne("
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status NOT IN ('closed','resolved') THEN 1 ELSE 0 END) as open
            FROM safety_incidents WHERE cde_project_id = ?
        ", [$pid]);

        return [
            'tasks_total' => (int) $taskStats->total,
            'tasks_open' => (int) $taskStats->open,
            'tasks_overdue' => (int) $taskStats->overdue,
            'documents' => (int) $docStats->documents,
            'docs_this_week' => (int) $docStats->docs_this_week,
            'folders' => (int) $docStats->folders,
            'rfis' => (int) $docStats->rfis,
            'incidents' => (int) $incidentStats->total,
            'incidents_open' => (int) $incidentStats->open,
        ];
    }

    public function getRecentTasks(): \Illuminate\Support\Collection
    {
        return $this->record->tasks()
            ->with('assignee')
            ->whereNotIn('status', ['done', 'cancelled'])
            ->orderByRaw("CASE WHEN due_date < NOW() THEN 0 ELSE 1 END")
            ->orderBy('due_date')
            ->limit(5)
            ->get();
    }

    public function getRecentDocuments(): \Illuminate\Support\Collection
    {
        return $this->record->documents()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function getEnabledModulesList(): array
    {
        $enabled = $this->record->getEnabledModules();
        $modules = Module::$availableModules;

        return collect($enabled)
            ->map(fn($code) => $modules[$code] ?? null)
            ->filter()
            ->values()
            ->toArray();
    }
}
