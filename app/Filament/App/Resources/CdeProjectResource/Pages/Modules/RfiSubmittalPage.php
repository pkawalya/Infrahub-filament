<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\CdeActivityLog;
use App\Models\Rfi;
use App\Models\Submittal;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class RfiSubmittalPage extends BaseModulePage implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static string $moduleCode = 'cde';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationLabel = 'RFIs & Submittals';
    protected static ?string $title = 'RFIs & Submittals';
    protected string $view = 'filament.app.pages.modules.rfi-submittals';

    public string $activeTab = 'rfis';

    /* ────────────── helpers ────────────── */

    private function pid(): int
    {
        return $this->record->id;
    }
    private function cid(): int
    {
        return $this->record->company_id;
    }

    private function teamOptions(): array
    {
        return User::where('company_id', $this->cid())
            ->pluck('name', 'id')->toArray();
    }

    private function nextRfiNumber(): string
    {
        $count = Rfi::where('cde_project_id', $this->pid())->count();
        return 'RFI-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    private function nextSubmittalNumber(): string
    {
        $count = Submittal::where('cde_project_id', $this->pid())->count();
        return 'SUB-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
    }

    /* ────────────── stat cards ────────────── */

    public function getStats(): array
    {
        $p = $this->record;
        $totalRfis = $p->rfis()->count();
        $openRfis = $p->rfis()->whereIn('status', ['open', 'under_review'])->count();
        $overdueRfis = $p->rfis()->whereIn('status', ['open', 'under_review'])
            ->where('due_date', '<', now())->count();
        $totalSubs = $p->submittals()->count();
        $pendingSubs = $p->submittals()->whereIn('status', ['pending', 'under_review'])->count();

        // Avg RFI response time (days)
        $avgResponse = $p->rfis()->whereNotNull('answered_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, answered_at)) as avg_days')
            ->value('avg_days');

        return [
            [
                'label' => 'Total RFIs',
                'value' => $totalRfis,
                'sub' => $openRfis . ' open',
                'sub_type' => $openRfis > 0 ? 'warning' : 'success',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" /></svg>',
            ],
            [
                'label' => 'Overdue RFIs',
                'value' => $overdueRfis,
                'sub' => $overdueRfis > 0 ? 'Needs attention' : 'All on track',
                'sub_type' => $overdueRfis > 0 ? 'danger' : 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="' . ($overdueRfis > 0 ? '#ef4444' : '#10b981') . '" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => $overdueRfis > 0 ? '#fef2f2' : '#ecfdf5',
            ],
            [
                'label' => 'Avg Response',
                'value' => $avgResponse ? round($avgResponse, 1) . ' days' : '—',
                'sub' => 'RFI turnaround',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#6366f1" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>',
                'icon_bg' => '#eef2ff',
            ],
            [
                'label' => 'Submittals',
                'value' => $totalSubs,
                'sub' => $pendingSubs . ' pending review',
                'sub_type' => $pendingSubs > 3 ? 'warning' : 'neutral',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#0ea5e9" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>',
                'icon_bg' => '#f0f9ff',
            ],
        ];
    }

    /* ────────────── RFI form schema ────────────── */

    private function rfiFormSchema(bool $isCreate = false): array
    {
        return [
            Section::make('RFI Details')->schema([
                Forms\Components\TextInput::make('rfi_number')
                    ->label('RFI #')
                    ->default($isCreate ? $this->nextRfiNumber() : null)
                    ->required(),
                Forms\Components\TextInput::make('subject')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('question')
                    ->label('Question / Description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('priority')
                    ->options(Rfi::$priorities)
                    ->default('medium')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options(Rfi::$statuses)
                    ->default('open')
                    ->required(),
                Forms\Components\Select::make('assigned_to')
                    ->label('Assign To')
                    ->options($this->teamOptions())
                    ->searchable(),
                Forms\Components\DatePicker::make('due_date')
                    ->label('Required Response Date'),
                Forms\Components\Select::make('cost_impact')
                    ->options(['yes' => 'Yes', 'no' => 'No', 'unknown' => 'Unknown'])
                    ->default('unknown'),
                Forms\Components\Select::make('schedule_impact')
                    ->options(['yes' => 'Yes', 'no' => 'No', 'unknown' => 'Unknown'])
                    ->default('unknown'),
            ])->columns(2),
        ];
    }

    /* ────────────── Submittal form schema ────────────── */

    private function submittalFormSchema(bool $isCreate = false): array
    {
        return [
            Section::make('Submittal Details')->schema([
                Forms\Components\TextInput::make('submittal_number')
                    ->label('Submittal #')
                    ->default($isCreate ? $this->nextSubmittalNumber() : null)
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('type')
                    ->options(Submittal::$types)
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options(Submittal::$statuses)
                    ->default('pending')
                    ->required(),
                Forms\Components\Select::make('reviewer_id')
                    ->label('Reviewer')
                    ->options($this->teamOptions())
                    ->searchable(),
                Forms\Components\DatePicker::make('due_date')
                    ->label('Required Response Date'),
                Forms\Components\TextInput::make('current_revision')
                    ->label('Revision')
                    ->default('0'),
                Forms\Components\RichEditor::make('description')
                    ->columnSpanFull(),
            ])->columns(2),
        ];
    }

    /* ────────────── header actions (Create RFI + Create Submittal) ────────────── */

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createRfi')
                ->label('New RFI')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->form($this->rfiFormSchema(true))
                ->action(function (array $data) {
                    $rfi = Rfi::create([
                        ...$data,
                        'company_id' => $this->cid(),
                        'cde_project_id' => $this->pid(),
                        'raised_by' => auth()->id(),
                    ]);

                    CdeActivityLog::record($rfi, 'created', "RFI {$rfi->rfi_number} created");

                    Notification::make()->title('RFI created')->success()->send();
                }),

            Action::make('createSubmittal')
                ->label('New Submittal')
                ->icon('heroicon-o-document-plus')
                ->color('info')
                ->form($this->submittalFormSchema(true))
                ->action(function (array $data) {
                    $sub = Submittal::create([
                        ...$data,
                        'company_id' => $this->cid(),
                        'cde_project_id' => $this->pid(),
                        'submitted_by' => auth()->id(),
                    ]);

                    CdeActivityLog::record($sub, 'created', "Submittal {$sub->submittal_number} created");

                    Notification::make()->title('Submittal created')->success()->send();
                }),
        ];
    }

    /* ────────────── table ────────────── */

    public function table(Table $table): Table
    {
        // Dynamic query based on active tab
        $query = $this->activeTab === 'submittals'
            ? Submittal::query()->where('cde_project_id', $this->pid())
            : Rfi::query()->where('cde_project_id', $this->pid());

        if ($this->activeTab === 'submittals') {
            return $this->submittalTable($table->query($query));
        }

        return $this->rfiTable($table->query($query));
    }

    /* ── RFI table ─────────────────────────────────── */

    private function rfiTable(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('rfi_number')
                    ->label('RFI #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->copyable(),

                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn($record) => $record->subject),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'urgent' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        'low' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'open' => 'warning',
                        'under_review' => 'info',
                        'answered' => 'success',
                        'closed' => 'gray',
                        'draft' => 'gray',
                        'void' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Assigned To')
                    ->placeholder('Unassigned'),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due')
                    ->date()
                    ->color(fn($record) => $record->due_date && $record->due_date->isPast()
                        && !in_array($record->status, ['answered', 'closed']) ? 'danger' : null)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Raised')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Rfi::$statuses),
                Tables\Filters\SelectFilter::make('priority')
                    ->options(Rfi::$priorities),
                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue Only')
                    ->query(fn($query) => $query
                        ->whereIn('status', ['open', 'under_review'])
                        ->where('due_date', '<', now())),
            ])
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\Action::make('view_rfi')
                        ->label('View')
                        ->icon('heroicon-o-eye')
                        ->modalHeading(fn($record) => $record->rfi_number . ' — ' . $record->subject)
                        ->modalContent(fn($record) => view('filament.app.pages.modules.partials.rfi-detail', ['rfi' => $record]))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),

                    \Filament\Actions\Action::make('answer_rfi')
                        ->label('Answer')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->visible(fn($record) => in_array($record->status, ['open', 'under_review']))
                        ->schema([
                            Forms\Components\RichEditor::make('answer')
                                ->label('Response')
                                ->required(),
                        ])
                        ->action(function (array $data, Rfi $record): void {
                            $record->update([
                                'answer' => $data['answer'],
                                'status' => 'answered',
                                'answered_at' => now(),
                            ]);
                            CdeActivityLog::record($record, 'status_changed', "RFI {$record->rfi_number} answered");
                            Notification::make()->title('RFI answered')->success()->send();
                        }),

                    \Filament\Actions\Action::make('edit_rfi')
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square')
                        ->schema($this->rfiFormSchema())
                        ->fillForm(fn(Rfi $record) => $record->toArray())
                        ->action(function (array $data, Rfi $record): void {
                            $record->update($data);
                            CdeActivityLog::record($record, 'updated', "RFI {$record->rfi_number} updated");
                            Notification::make()->title('RFI updated')->success()->send();
                        }),

                    \Filament\Actions\Action::make('close_rfi')
                        ->label('Close')
                        ->icon('heroicon-o-x-circle')
                        ->color('gray')
                        ->visible(fn($record) => $record->status === 'answered')
                        ->requiresConfirmation()
                        ->action(function (Rfi $record): void {
                            $record->update(['status' => 'closed']);
                            CdeActivityLog::record($record, 'status_changed', "RFI {$record->rfi_number} closed");
                            Notification::make()->title('RFI closed')->success()->send();
                        }),
                ]),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('bulk_close')
                        ->label('Close Selected')
                        ->icon('heroicon-o-x-circle')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(fn($r) => $r->update(['status' => 'closed']));
                            Notification::make()->title($records->count() . ' RFIs closed')->success()->send();
                        }),
                ]),
            ])
            ->emptyStateHeading('No RFIs yet')
            ->emptyStateDescription('Create your first Request for Information to get started.')
            ->emptyStateIcon('heroicon-o-question-mark-circle');
    }

    /* ── Submittal table ───────────────────────────── */

    private function submittalTable(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('submittal_number')
                    ->label('Submittal #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->copyable(),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn($record) => $record->title),

                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(fn(?string $state) => Submittal::$types[$state] ?? $state)
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('current_revision')
                    ->label('Rev')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'pending' => 'warning',
                        'under_review' => 'info',
                        'approved' => 'success',
                        'approved_as_noted' => 'success',
                        'revise_resubmit' => 'danger',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('reviewer.name')
                    ->label('Reviewer')
                    ->placeholder('Unassigned'),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due')
                    ->date()
                    ->color(fn($record) => $record->due_date && $record->due_date->isPast()
                        && in_array($record->status, ['pending', 'under_review']) ? 'danger' : null)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Submittal::$statuses),
                Tables\Filters\SelectFilter::make('type')
                    ->options(Submittal::$types),
            ])
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\Action::make('review_submittal')
                        ->label('Review')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->color('info')
                        ->visible(fn($record) => in_array($record->status, ['pending', 'under_review']))
                        ->schema([
                            Forms\Components\Select::make('status')
                                ->label('Decision')
                                ->options([
                                    'approved' => 'Approved',
                                    'approved_as_noted' => 'Approved as Noted',
                                    'revise_resubmit' => 'Revise & Resubmit',
                                    'rejected' => 'Rejected',
                                ])
                                ->required(),
                            Forms\Components\RichEditor::make('review_comments')
                                ->label('Review Comments'),
                        ])
                        ->action(function (array $data, Submittal $record): void {
                            $record->update([
                                'status' => $data['status'],
                                'review_comments' => $data['review_comments'] ?? null,
                                'reviewed_at' => now(),
                                'reviewer_id' => auth()->id(),
                            ]);
                            $statusLabel = Submittal::$statuses[$data['status']] ?? $data['status'];
                            CdeActivityLog::record($record, $data['status'], "Submittal {$record->submittal_number}: {$statusLabel}");
                            Notification::make()->title("Submittal {$statusLabel}")->success()->send();
                        }),

                    \Filament\Actions\Action::make('edit_submittal')
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square')
                        ->schema($this->submittalFormSchema())
                        ->fillForm(fn(Submittal $record) => $record->toArray())
                        ->action(function (array $data, Submittal $record): void {
                            $record->update($data);
                            CdeActivityLog::record($record, 'updated', "Submittal {$record->submittal_number} updated");
                            Notification::make()->title('Submittal updated')->success()->send();
                        }),

                    \Filament\Actions\Action::make('resubmit')
                        ->label('Resubmit')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->visible(fn($record) => $record->status === 'revise_resubmit')
                        ->requiresConfirmation()
                        ->modalDescription('This will increment the revision and reset the status to Pending.')
                        ->action(function (Submittal $record): void {
                            $rev = intval($record->current_revision) + 1;
                            $record->update([
                                'current_revision' => (string) $rev,
                                'status' => 'pending',
                                'reviewed_at' => null,
                                'review_comments' => null,
                            ]);
                            CdeActivityLog::record($record, 'submitted', "Submittal {$record->submittal_number} Rev {$rev} resubmitted");
                            Notification::make()->title('Submittal resubmitted as Rev ' . $rev)->success()->send();
                        }),
                ]),
            ])
            ->emptyStateHeading('No submittals yet')
            ->emptyStateDescription('Create your first submittal to track shop drawings, product data, and more.')
            ->emptyStateIcon('heroicon-o-document-plus');
    }
}
