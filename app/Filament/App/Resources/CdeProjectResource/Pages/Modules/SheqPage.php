<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\InspectionTemplate;
use App\Models\SafetyIncident;
use App\Models\SafetyInspection;
use App\Models\SnagItem;
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

class SheqPage extends BaseModulePage implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static string $moduleCode = 'sheq';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'SHEQ';
    protected static ?string $title = 'Safety, Health, Environment & Quality';
    protected string $view = 'filament.app.pages.modules.sheq';

    public string $activeTab = 'incidents';

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
        return User::where('company_id', $this->cid())->where('is_active', true)->pluck('name', 'id')->toArray();
    }

    public static array $types = [
        'injury' => 'Injury',
        'near_miss' => 'Near Miss',
        'property_damage' => 'Property Damage',
        'environmental' => 'Environmental',
        'fire' => 'Fire',
        'chemical' => 'Chemical Spill',
        'fall' => 'Fall',
        'electrical' => 'Electrical',
        'other' => 'Other',
    ];
    public static array $severities = [
        'minor' => 'Minor',
        'moderate' => 'Moderate',
        'major' => 'Major',
        'critical' => 'Critical',
        'fatal' => 'Fatal',
    ];

    public function getStats(): array
    {
        $pid = $this->pid();
        $base = SafetyIncident::where('cde_project_id', $pid);
        $total = (clone $base)->count();
        $open = (clone $base)->whereIn('status', ['reported', 'investigating'])->count();
        $critical = (clone $base)->whereIn('severity', ['critical', 'fatal'])->whereNotIn('status', ['closed', 'resolved'])->count();
        $thisMonth = (clone $base)->whereMonth('incident_date', now()->month)->whereYear('incident_date', now()->year)->count();
        $resolved = (clone $base)->where('status', 'resolved')->count();

        return [
            [
                'label' => 'Total Incidents',
                'value' => $total,
                'sub' => $open . ' open',
                'sub_type' => $open > 0 ? 'warning' : 'success',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>'
            ],
            [
                'label' => 'Critical / Fatal',
                'value' => $critical,
                'sub' => $critical > 0 ? 'Immediate attention' : 'None active',
                'sub_type' => $critical > 0 ? 'danger' : 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ef4444" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>',
                'icon_bg' => '#fef2f2'
            ],
            [
                'label' => 'This Month',
                'value' => $thisMonth,
                'sub' => now()->format('F Y'),
                'sub_type' => 'info',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#3b82f6" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>',
                'icon_bg' => '#eff6ff'
            ],
            [
                'label' => 'Resolved',
                'value' => $resolved,
                'sub' => $total > 0 ? round(($resolved / $total) * 100) . '% resolution' : 'No incidents',
                'sub_type' => 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>',
                'icon_bg' => '#ecfdf5'
            ],
        ];
    }

    private function incidentFormSchema(bool $isCreate = false): array
    {
        return [
            Section::make('Incident Details')->schema([
                Forms\Components\TextInput::make('incident_number')->label('Incident #')
                    ->default(fn() => $isCreate ? 'INC-' . str_pad((string) (SafetyIncident::where('cde_project_id', $this->pid())->count() + 1), 5, '0', STR_PAD_LEFT) : null)
                    ->required()->maxLength(50),
                Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\Select::make('type')->options(self::$types)->required()->searchable(),
                Forms\Components\Select::make('severity')->options(self::$severities)->required()->default('minor'),
                Forms\Components\Select::make('status')->options(SafetyIncident::$statuses)->required()->default($isCreate ? 'reported' : null),
                Forms\Components\DateTimePicker::make('incident_date')->required()->default(now()),
                Forms\Components\TextInput::make('location')->maxLength(255)->columnSpanFull(),
                Forms\Components\Select::make('reported_by')->label('Reported By')
                    ->options(fn() => $this->teamOptions())->searchable()->default(auth()->id()),
                Forms\Components\Select::make('investigated_by')->label('Investigator')
                    ->options(fn() => $this->teamOptions())->searchable()->nullable(),
            ])->columns(2),
            Section::make('Description')->schema([
                Forms\Components\RichEditor::make('description')->label('Incident Description')
                    ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList'])->columnSpanFull(),
            ]),
            Section::make('Investigation & Resolution')->schema([
                Forms\Components\Textarea::make('root_cause')->label('Root Cause Analysis')->rows(3)->columnSpanFull(),
                Forms\Components\Textarea::make('corrective_action')->label('Corrective Actions')->rows(3)->columnSpanFull(),
            ])->collapsed($isCreate),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reportIncident')
                ->label('Report Incident')->icon('heroicon-o-plus-circle')->color('danger')
                ->modalWidth('4xl')
                ->schema($this->incidentFormSchema(isCreate: true))
                ->action(function (array $data): void {
                    $data['company_id'] = $this->cid();
                    $data['cde_project_id'] = $this->pid();
                    SafetyIncident::create($data);
                    Notification::make()->title('Incident reported')->success()->send();
                }),

            Action::make('scheduleInspection')
                ->label('Schedule Inspection')->icon('heroicon-o-clipboard-document-check')->color('info')
                ->modalWidth('3xl')
                ->schema([
                    Section::make('Inspection Details')->schema([
                        Forms\Components\TextInput::make('inspection_number')->label('Inspection #')
                            ->default(fn() => 'INSP-' . str_pad((string) (SafetyInspection::where('cde_project_id', $this->pid())->count() + 1), 4, '0', STR_PAD_LEFT))
                            ->required()->maxLength(50),
                        Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                        Forms\Components\Select::make('type')->options(InspectionTemplate::$types)->searchable(),
                        Forms\Components\Select::make('inspection_template_id')->label('Template')
                            ->options(fn() => InspectionTemplate::where('company_id', $this->cid())->where('is_active', true)->pluck('name', 'id')->toArray())
                            ->searchable()->nullable(),
                        Forms\Components\DateTimePicker::make('scheduled_date')->required()->default(now()),
                        Forms\Components\Select::make('inspector_id')->label('Inspector')
                            ->options(fn() => $this->teamOptions())->searchable()->default(auth()->id()),
                        Forms\Components\TextInput::make('location')->maxLength(255),
                        Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
                    ])->columns(2),
                ])
                ->action(function (array $data): void {
                    $data['company_id'] = $this->cid();
                    $data['cde_project_id'] = $this->pid();
                    $data['status'] = 'scheduled';
                    SafetyInspection::create($data);
                    Notification::make()->title('Inspection scheduled')->success()->send();
                }),

            Action::make('reportSnag')
                ->label('Report Snag')->icon('heroicon-o-bug-ant')->color('warning')
                ->modalWidth('3xl')
                ->schema([
                    Section::make('Snag / Defect Details')->schema([
                        Forms\Components\TextInput::make('snag_number')->label('Snag #')
                            ->default(fn() => 'SNG-' . str_pad((string) (SnagItem::where('cde_project_id', $this->pid())->count() + 1), 4, '0', STR_PAD_LEFT))
                            ->required()->maxLength(50),
                        Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                        Forms\Components\Select::make('category')->options(SnagItem::$categories)->searchable(),
                        Forms\Components\Select::make('severity')->options(SnagItem::$severities)->required()->default('minor'),
                        Forms\Components\TextInput::make('location')->maxLength(255),
                        Forms\Components\TextInput::make('trade')->label('Trade / Subcontractor')->maxLength(100),
                        Forms\Components\Select::make('assigned_to')->label('Assign To')
                            ->options(fn() => $this->teamOptions())->searchable()->nullable(),
                        Forms\Components\DatePicker::make('due_date')->label('Due Date'),
                        Forms\Components\RichEditor::make('description')->label('Description')
                            ->toolbarButtons(['bold', 'italic', 'bulletList'])->columnSpanFull(),
                    ])->columns(2),
                ])
                ->action(function (array $data): void {
                    $data['company_id'] = $this->cid();
                    $data['cde_project_id'] = $this->pid();
                    $data['reported_by'] = auth()->id();
                    $data['status'] = 'open';
                    SnagItem::create($data);
                    Notification::make()->title('Snag reported')->success()->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        if ($this->activeTab === 'inspections') {
            return $this->inspectionTable($table->query(
                SafetyInspection::query()->where('cde_project_id', $this->pid())->with(['inspector', 'template'])
            ));
        }
        if ($this->activeTab === 'snags') {
            return $this->snagTable($table->query(
                SnagItem::query()->where('cde_project_id', $this->pid())->with(['reporter', 'assignee'])
            ));
        }
        return $this->incidentTable($table->query(
            SafetyIncident::query()->where('cde_project_id', $this->pid())->with(['reporter', 'investigator'])
        ));
    }

    private function incidentTable(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('incident_number')->label('Incident #')->searchable()->sortable()->weight('bold')
                    ->icon('heroicon-o-exclamation-triangle')->copyable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40)->tooltip(fn(SafetyIncident $record) => $record->title),
                Tables\Columns\TextColumn::make('type')->badge()->color('info')
                    ->formatStateUsing(fn($state) => self::$types[$state] ?? $state),
                Tables\Columns\TextColumn::make('severity')->badge()
                    ->color(fn(string $state) => match ($state) { 'fatal' => 'danger', 'critical' => 'danger', 'major' => 'warning', 'moderate' => 'info', default => 'gray'})->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) { 'closed' => 'gray', 'resolved' => 'success', 'investigating' => 'warning', 'reported' => 'info', default => 'gray'})->sortable(),
                Tables\Columns\TextColumn::make('incident_date')->dateTime('M d, Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('location')->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('reporter.name')->label('Reported By')->toggleable(),
                Tables\Columns\TextColumn::make('investigator.name')->label('Investigator')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('incident_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(SafetyIncident::$statuses)->multiple(),
                Tables\Filters\SelectFilter::make('severity')->options(self::$severities)->multiple(),
                Tables\Filters\SelectFilter::make('type')->options(self::$types)->multiple(),
                Tables\Filters\Filter::make('critical_only')->label('Critical/Fatal Only')
                    ->query(fn($q) => $q->whereIn('severity', ['critical', 'fatal']))->toggle(),
                Tables\Filters\Filter::make('open_only')->label('Open Only')
                    ->query(fn($q) => $q->whereIn('status', ['reported', 'investigating']))->toggle(),
            ])
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\Action::make('viewDetail')
                        ->label('View')->icon('heroicon-o-eye')->color('gray')->modalWidth('3xl')
                        ->modalHeading(fn(SafetyIncident $record) => $record->incident_number . ' — ' . $record->title)
                        ->schema(fn(SafetyIncident $record) => [
                            Forms\Components\Placeholder::make('severity_display')->label('Severity')
                                ->content(fn() => (self::$severities[$record->severity] ?? $record->severity)),
                            Forms\Components\Placeholder::make('type_display')->label('Type')
                                ->content(fn() => (self::$types[$record->type] ?? $record->type)),
                            Forms\Components\Placeholder::make('status_display')->label('Status')
                                ->content(SafetyIncident::$statuses[$record->status] ?? $record->status),
                            Forms\Components\Placeholder::make('date_display')->label('Occurred')
                                ->content($record->incident_date?->format('M d, Y H:i') ?? '—'),
                            Forms\Components\Placeholder::make('location_display')->label('Location')
                                ->content($record->location ?: '—'),
                            Forms\Components\Placeholder::make('reported_by')->label('Reported By')
                                ->content($record->reporter?->name ?? '—'),
                            Forms\Components\Placeholder::make('desc')->label('Description')
                                ->content(fn() => new \Illuminate\Support\HtmlString($record->description ?: '<em>No description</em>'))
                                ->columnSpanFull(),
                            Forms\Components\Placeholder::make('root_cause_display')->label('Root Cause')
                                ->content($record->root_cause ?: '— Not yet determined')->columnSpanFull(),
                            Forms\Components\Placeholder::make('corrective_display')->label('Corrective Actions')
                                ->content($record->corrective_action ?: '— None recorded')->columnSpanFull(),
                            Forms\Components\Placeholder::make('preventive_display')->label('Preventive Actions')
                                ->content($record->preventive_action ?: '— None recorded')->columnSpanFull(),
                        ])
                        ->modalSubmitAction(false)->modalCancelActionLabel('Close'),

                    \Filament\Actions\Action::make('investigate')
                        ->label('Investigate')->icon('heroicon-o-magnifying-glass')->color('warning')
                        ->visible(fn(SafetyIncident $record) => $record->status === 'reported')
                        ->schema([
                            Forms\Components\Select::make('investigated_by')->label('Investigator')
                                ->options(fn() => $this->teamOptions())->searchable()->required()->default(auth()->id()),
                            Forms\Components\Textarea::make('root_cause')->label('Root Cause (Initial)')->rows(3),
                        ])
                        ->action(function (array $data, SafetyIncident $record): void {
                            $record->update(array_merge($data, ['status' => 'investigating']));
                            Notification::make()->title('Investigation started')->success()->send();
                        }),

                    \Filament\Actions\Action::make('resolve')
                        ->label('Resolve')->icon('heroicon-o-check-circle')->color('success')
                        ->visible(fn(SafetyIncident $record) => !in_array($record->status, ['resolved', 'closed']))
                        ->schema([
                            Forms\Components\Textarea::make('root_cause')->label('Root Cause')->rows(3)->required(),
                            Forms\Components\Textarea::make('corrective_action')->label('Corrective Actions')->rows(3)->required(),
                        ])
                        ->fillForm(fn(SafetyIncident $record) => ['root_cause' => $record->root_cause, 'corrective_action' => $record->corrective_action])
                        ->action(function (array $data, SafetyIncident $record): void {
                            $record->update(array_merge($data, ['status' => 'resolved']));
                            Notification::make()->title('Incident resolved')->success()->send();
                        }),

                    \Filament\Actions\Action::make('close')
                        ->label('Close')->icon('heroicon-o-lock-closed')->color('gray')
                        ->visible(fn(SafetyIncident $record) => $record->status === 'resolved')
                        ->requiresConfirmation()->modalDescription('Mark as closed.')
                        ->action(function (SafetyIncident $record): void {
                            $record->update(['status' => 'closed']);
                            Notification::make()->title('Incident closed')->success()->send();
                        }),

                    \Filament\Actions\Action::make('preventiveAction')
                        ->label('Preventive Action')->icon('heroicon-o-shield-check')->color('info')
                        ->visible(fn(SafetyIncident $record) => in_array($record->status, ['resolved', 'closed']))
                        ->schema([
                            Forms\Components\Textarea::make('preventive_action')
                                ->label('Preventive Actions / Lessons Learned')->rows(4)->required(),
                        ])
                        ->fillForm(fn(SafetyIncident $record) => ['preventive_action' => $record->preventive_action])
                        ->action(function (array $data, SafetyIncident $record): void {
                            $record->update($data);
                            Notification::make()->title('Preventive actions recorded')->success()->send();
                        }),

                    \Filament\Actions\Action::make('edit')
                        ->icon('heroicon-o-pencil')->modalWidth('4xl')
                        ->schema($this->incidentFormSchema())
                        ->fillForm(fn(SafetyIncident $record) => $record->toArray())
                        ->action(function (array $data, SafetyIncident $record): void {
                            $record->update($data);
                            Notification::make()->title('Incident updated')->success()->send();
                        }),

                    \Filament\Actions\Action::make('delete')
                        ->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                        ->action(fn(SafetyIncident $record) => $record->delete()),
                ]),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('bulkStatus')->label('Update Status')->icon('heroicon-o-arrow-path')
                        ->schema([Forms\Components\Select::make('status')->options(SafetyIncident::$statuses)->required()])
                        ->action(function (array $data, $records): void {
                            foreach ($records as $r) $r->update($data);
                            Notification::make()->title($records->count() . ' incidents updated')->success()->send();
                        })->deselectRecordsAfterCompletion(),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Safety Incidents')
            ->emptyStateDescription('Report safety incidents to track workplace safety.')
            ->emptyStateIcon('heroicon-o-shield-check')
            ->striped()->paginated([10, 25, 50]);
    }

    /* ── Inspection Table ───────────────────────────── */
    private function inspectionTable(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('inspection_number')->label('#')->searchable()->sortable()->weight('bold')->copyable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40)->tooltip(fn(SafetyInspection $r) => $r->title),
                Tables\Columns\TextColumn::make('type')->badge()->color('info')
                    ->formatStateUsing(fn($state) => InspectionTemplate::$types[$state] ?? $state),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $s) => match ($s) { 'completed' => 'success', 'in_progress' => 'warning', default => 'info'})->sortable(),
                Tables\Columns\TextColumn::make('score')->label('Score')->placeholder('—')->sortable()
                    ->color(fn($state) => $state >= 80 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),
                Tables\Columns\TextColumn::make('scheduled_date')->dateTime('M d, Y')->sortable(),
                Tables\Columns\TextColumn::make('inspector.name')->label('Inspector')->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('location')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('scheduled_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(SafetyInspection::$statuses),
            ])
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\Action::make('complete')
                        ->label('Complete')->icon('heroicon-o-check-circle')->color('success')
                        ->visible(fn(SafetyInspection $r) => $r->status !== 'completed')
                        ->schema([
                            Forms\Components\TextInput::make('score')->label('Score (0-100)')->numeric()->minValue(0)->maxValue(100)->required(),
                            Forms\Components\Textarea::make('notes')->label('Findings / Notes')->rows(4),
                        ])
                        ->fillForm(fn(SafetyInspection $r) => ['score' => $r->score, 'notes' => $r->notes])
                        ->action(function (array $data, SafetyInspection $record): void {
                            $record->update(array_merge($data, ['status' => 'completed', 'completed_date' => now()]));
                            Notification::make()->title('Inspection completed — Score: ' . $data['score'])->success()->send();
                        }),

                    \Filament\Actions\Action::make('editInspection')
                        ->label('Edit')->icon('heroicon-o-pencil')->modalWidth('3xl')
                        ->schema([
                            Section::make('Inspection Details')->schema([
                                Forms\Components\TextInput::make('inspection_number')->label('#')->required(),
                                Forms\Components\TextInput::make('title')->required()->columnSpanFull(),
                                Forms\Components\Select::make('type')->options(InspectionTemplate::$types)->searchable(),
                                Forms\Components\Select::make('status')->options(SafetyInspection::$statuses)->required(),
                                Forms\Components\DateTimePicker::make('scheduled_date')->required(),
                                Forms\Components\Select::make('inspector_id')->label('Inspector')
                                    ->options(fn() => $this->teamOptions())->searchable(),
                                Forms\Components\TextInput::make('location'),
                                Forms\Components\TextInput::make('score')->numeric()->minValue(0)->maxValue(100),
                                Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
                            ])->columns(2),
                        ])
                        ->fillForm(fn(SafetyInspection $r) => $r->toArray())
                        ->action(function (array $data, SafetyInspection $record): void {
                            $record->update($data);
                            Notification::make()->title('Inspection updated')->success()->send();
                        }),

                    \Filament\Actions\Action::make('deleteInspection')
                        ->label('Delete')->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                        ->action(fn(SafetyInspection $r) => $r->delete()),
                ]),
            ])
            ->emptyStateHeading('No Inspections')
            ->emptyStateDescription('Use "Schedule Inspection" above to create one.')
            ->emptyStateIcon('heroicon-o-clipboard-document-check')
            ->striped()->paginated([10, 25, 50]);
    }

    /* ── Snag / Defect Table ────────────────────────── */
    private function snagTable(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('snag_number')->label('Snag #')->searchable()->sortable()->weight('bold')->copyable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40)->tooltip(fn(SnagItem $r) => $r->title),
                Tables\Columns\TextColumn::make('category')->badge()->color('info')
                    ->formatStateUsing(fn($state) => SnagItem::$categories[$state] ?? $state),
                Tables\Columns\TextColumn::make('severity')->badge()
                    ->color(fn(string $s) => match ($s) { 'critical' => 'danger', 'major' => 'warning', default => 'gray'})->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $s) => match ($s) { 'open' => 'danger', 'in_progress' => 'info', 'resolved' => 'success', 'verified' => 'success', 'closed' => 'gray', default => 'gray'})->sortable(),
                Tables\Columns\TextColumn::make('assignee.name')->label('Assigned To')->placeholder('—'),
                Tables\Columns\TextColumn::make('due_date')->date('M d, Y')->sortable()
                    ->color(fn(SnagItem $r) => $r->due_date?->isPast() && !in_array($r->status, ['resolved', 'verified', 'closed']) ? 'danger' : null)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('trade')->label('Trade')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(SnagItem::$statuses)->multiple(),
                Tables\Filters\SelectFilter::make('severity')->options(SnagItem::$severities),
                Tables\Filters\SelectFilter::make('category')->options(SnagItem::$categories),
                Tables\Filters\Filter::make('overdue')->label('Overdue Only')
                    ->query(fn($q) => $q->where('due_date', '<', now())->whereNotIn('status', ['resolved', 'verified', 'closed']))->toggle(),
            ])
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\Action::make('resolveSnag')
                        ->label('Resolve')->icon('heroicon-o-check-circle')->color('success')
                        ->visible(fn(SnagItem $r) => in_array($r->status, ['open', 'in_progress']))
                        ->requiresConfirmation()
                        ->action(function (SnagItem $record): void {
                            $record->update(['status' => 'resolved', 'resolved_at' => now()]);
                            Notification::make()->title('Snag resolved')->success()->send();
                        }),

                    \Filament\Actions\Action::make('verifySnag')
                        ->label('Verify')->icon('heroicon-o-check-badge')->color('success')
                        ->visible(fn(SnagItem $r) => $r->status === 'resolved')
                        ->requiresConfirmation()
                        ->action(function (SnagItem $record): void {
                            $record->update(['status' => 'verified']);
                            Notification::make()->title('Snag verified')->success()->send();
                        }),

                    \Filament\Actions\Action::make('editSnag')
                        ->label('Edit')->icon('heroicon-o-pencil')->modalWidth('3xl')
                        ->schema([
                            Section::make('Snag / Defect Details')->schema([
                                Forms\Components\TextInput::make('snag_number')->label('Snag #')->required(),
                                Forms\Components\TextInput::make('title')->required()->columnSpanFull(),
                                Forms\Components\Select::make('category')->options(SnagItem::$categories)->searchable(),
                                Forms\Components\Select::make('severity')->options(SnagItem::$severities)->required(),
                                Forms\Components\Select::make('status')->options(SnagItem::$statuses)->required(),
                                Forms\Components\TextInput::make('location'),
                                Forms\Components\TextInput::make('trade')->label('Trade / Subcontractor'),
                                Forms\Components\Select::make('assigned_to')->label('Assign To')
                                    ->options(fn() => $this->teamOptions())->searchable()->nullable(),
                                Forms\Components\DatePicker::make('due_date'),
                                Forms\Components\RichEditor::make('description')
                                    ->toolbarButtons(['bold', 'italic', 'bulletList'])->columnSpanFull(),
                            ])->columns(2),
                        ])
                        ->fillForm(fn(SnagItem $r) => $r->toArray())
                        ->action(function (array $data, SnagItem $record): void {
                            $record->update($data);
                            Notification::make()->title('Snag updated')->success()->send();
                        }),

                    \Filament\Actions\Action::make('deleteSnag')
                        ->label('Delete')->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                        ->action(fn(SnagItem $r) => $r->delete()),
                ]),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('bulkResolve')->label('Resolve')->icon('heroicon-o-check-circle')->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            foreach ($records as $r) $r->update(['status' => 'resolved', 'resolved_at' => now()]);
                            Notification::make()->title($records->count() . ' snags resolved')->success()->send();
                        })->deselectRecordsAfterCompletion(),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Snag Items')
            ->emptyStateDescription('Use "Report Snag" above to log defects.')
            ->emptyStateIcon('heroicon-o-bug-ant')
            ->striped()->paginated([10, 25, 50]);
    }
}
