<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\DailySiteLog;
use App\Models\DailySiteLogTask;
use App\Models\Task;
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

use App\Filament\App\Concerns\ExportsTableCsv;

class FieldManagementPage extends BaseModulePage implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms, ExportsTableCsv;

    protected static string $moduleCode = 'field_management';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Field Mgmt';
    protected static ?string $title = 'Field Management — Daily Site Logs';
    protected string $view = 'filament.app.pages.modules.field-management';

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

    /**
     * Get active tasks for this project (for linking in daily logs).
     */
    private function getActiveTaskOptions(): array
    {
        return Task::where('cde_project_id', $this->pid())
            ->whereNotIn('status', ['done', 'cancelled'])
            ->where('is_summary', false)
            ->orderBy('wbs_code')
            ->get()
            ->mapWithKeys(fn(Task $t) => [
                $t->id => ($t->wbs_code ? $t->wbs_code . ' — ' : '') . $t->title . ' (' . ($t->progress_percent ?? 0) . '%)',
            ])
            ->toArray();
    }

    /**
     * Get ALL tasks for this project (for referencing in views).
     */
    private function getAllTaskOptions(): array
    {
        return Task::where('cde_project_id', $this->pid())
            ->where('is_summary', false)
            ->orderBy('wbs_code')
            ->pluck('title', 'id')
            ->toArray();
    }

    public function getStats(): array
    {
        $pid = $this->pid();
        $base = DailySiteLog::where('cde_project_id', $pid);
        $total = (clone $base)->count();
        $thisWeek = (clone $base)->whereBetween('log_date', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $pending = (clone $base)->where('status', 'draft')->count();
        $avgWorkers = $total > 0 ? round((clone $base)->avg('workers_on_site') ?? 0) : 0;
        $tasksTracked = DailySiteLogTask::whereHas('siteLog', fn($q) => $q->where('cde_project_id', $pid))
            ->distinct('task_id')->count('task_id');

        return [
            [
                'label' => 'Total Logs',
                'value' => $total,
                'sub' => $pending . ' pending review',
                'sub_type' => $pending > 0 ? 'warning' : 'success',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>'
            ],
            [
                'label' => 'This Week',
                'value' => $thisWeek,
                'sub' => now()->startOfWeek()->format('M d') . ' – ' . now()->endOfWeek()->format('M d'),
                'sub_type' => 'info',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#3b82f6" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>',
                'icon_bg' => '#eff6ff'
            ],
            [
                'label' => 'Tasks Tracked',
                'value' => $tasksTracked,
                'sub' => 'Linked to daily logs',
                'sub_type' => 'info',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#6366f1" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>',
                'icon_bg' => '#eef2ff'
            ],
            [
                'label' => 'Avg Workers/Day',
                'value' => $avgWorkers,
                'sub' => 'On site',
                'sub_type' => 'neutral',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>',
                'icon_bg' => '#ecfdf5'
            ],
        ];
    }

    public function getWeeklySummary(): array
    {
        $pid = $this->pid();
        $weekLogs = DailySiteLog::where('cde_project_id', $pid)
            ->whereBetween('log_date', [now()->startOfWeek(), now()->endOfWeek()])
            ->with('taskEntries')
            ->get();

        $totalHours = $weekLogs->flatMap->taskEntries->sum('hours_worked');
        $totalWorkers = $weekLogs->sum('workers_on_site');
        $avgWorkers = $weekLogs->count() > 0 ? round($totalWorkers / $weekLogs->count()) : 0;
        $pending = $weekLogs->where('status', 'draft')->count();
        $approved = $weekLogs->where('status', 'approved')->count();

        $weatherCounts = $weekLogs->groupBy('weather')->map->count()->toArray();

        return [
            'total_logs' => $weekLogs->count(),
            'total_hours' => round($totalHours, 1),
            'avg_workers' => $avgWorkers,
            'pending' => $pending,
            'approved' => $approved,
            'weather' => $weatherCounts,
        ];
    }

    public function getRecentLogs(): \Illuminate\Database\Eloquent\Collection
    {
        return DailySiteLog::where('cde_project_id', $this->pid())
            ->with(['creator:id,name', 'taskEntries'])
            ->orderByDesc('log_date')
            ->limit(7)
            ->get();
    }

    private function logFormSchema(bool $isCreate = false): array
    {
        return [
            Section::make('Log Details')->schema([
                Forms\Components\DatePicker::make('log_date')->required()->default(now()),
                Forms\Components\Select::make('weather')->options([
                    'sunny' => 'Sunny',
                    'partly_cloudy' => 'Partly Cloudy',
                    'cloudy' => 'Cloudy',
                    'rainy' => 'Rainy',
                    'stormy' => 'Stormy',
                    'windy' => 'Windy',
                    'foggy' => 'Foggy',
                ])->searchable(),
                Forms\Components\TextInput::make('workers_on_site')->label('Workers')->numeric()->default(0),
                Forms\Components\TextInput::make('visitors_on_site')->label('Visitors')->numeric()->default(0),
                Forms\Components\TextInput::make('temperature_high')->label('High °C')->numeric()->suffix('°C'),
                Forms\Components\TextInput::make('temperature_low')->label('Low °C')->numeric()->suffix('°C'),
                Forms\Components\Select::make('status')->options(DailySiteLog::$statuses)->required()->default('draft'),
                Forms\Components\Select::make('approved_by')->label('Approved By')
                    ->options(fn() => $this->teamOptions())->searchable()->nullable(),
            ])->columns(4),
            Section::make('Task Progress')->description('Link scheduled tasks and report daily progress')->schema([
                Forms\Components\Repeater::make('task_entries')
                    ->label('')
                    ->schema([
                        Forms\Components\Select::make('task_id')
                            ->label('Task')
                            ->options(fn() => $this->getActiveTaskOptions())
                            ->searchable()->required()->columnSpan(2),
                        Forms\Components\Select::make('status_update')
                            ->label('Status')
                            ->options(DailySiteLogTask::$statusOptions),
                        Forms\Components\TextInput::make('progress_today')
                            ->label('+% Today')
                            ->numeric()->minValue(0)->maxValue(100)->default(0)->suffix('%'),
                        Forms\Components\TextInput::make('cumulative_progress')
                            ->label('Total %')
                            ->numeric()->minValue(0)->maxValue(100)->suffix('%')
                            ->placeholder('Auto'),
                        Forms\Components\TextInput::make('hours_worked')
                            ->label('Hours')
                            ->numeric()->minValue(0)->step(0.5)->default(0)->suffix('h'),
                        Forms\Components\TextInput::make('remarks')
                            ->label('Remarks')
                            ->placeholder('Note...'),
                    ])
                    ->columns(7)
                    ->defaultItems($isCreate ? 1 : 0)
                    ->addActionLabel('+ Add Task')
                    ->reorderable(false)
                    ->columnSpanFull(),
            ]),
            Section::make('Notes')->schema([
                Forms\Components\Textarea::make('work_performed')->label('Work Performed')->rows(2),
                Forms\Components\Textarea::make('delays')->label('Delays / Issues')->rows(2),
                Forms\Components\Textarea::make('materials_received')->label('Materials Received')->rows(2),
                Forms\Components\Textarea::make('safety_incidents')->label('Safety Incidents')->rows(2),
                Forms\Components\Textarea::make('equipment_used')->label('Equipment Used')->rows(2),
                Forms\Components\Textarea::make('notes')->label('Additional Notes')->rows(2),
            ])->columns(2)->collapsed(!$isCreate),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createLog')
                ->label('New Daily Log')->icon('heroicon-o-plus-circle')->color('primary')
                ->modalWidth('screen')
                ->schema($this->logFormSchema(isCreate: true))
                ->action(function (array $data): void {
                    $taskEntries = $data['task_entries'] ?? [];
                    unset($data['task_entries']);

                    $data['company_id'] = $this->cid();
                    $data['cde_project_id'] = $this->pid();
                    $data['created_by'] = auth()->id();
                    $log = DailySiteLog::create($data);

                    // Save task progress entries
                    foreach ($taskEntries as $entry) {
                        if (empty($entry['task_id']))
                            continue;
                        $task = Task::find($entry['task_id']);
                        $cumulative = $entry['cumulative_progress'] ?? (($task->progress_percent ?? 0) + ($entry['progress_today'] ?? 0));

                        $log->taskEntries()->create([
                            'task_id' => $entry['task_id'],
                            'progress_today' => $entry['progress_today'] ?? 0,
                            'cumulative_progress' => min(100, max(0, $cumulative)),
                            'hours_worked' => $entry['hours_worked'] ?? 0,
                            'workers_assigned' => $entry['workers_assigned'] ?? 0,
                            'status_update' => $entry['status_update'] ?? null,
                            'remarks' => $entry['remarks'] ?? null,
                        ]);
                    }

                    // If created as approved, sync progress immediately
                    if ($log->status === 'approved') {
                        $this->syncTaskProgress($log);
                    }

                    $taskCount = count(array_filter($taskEntries, fn($e) => !empty($e['task_id'])));
                    Notification::make()
                        ->title("Daily log created" . ($taskCount > 0 ? " with {$taskCount} task entries" : ''))
                        ->success()->send();
                }),
        ];
    }

    /**
     * Sync task progress from a log's task entries back to Task model.
     */
    private function syncTaskProgress(DailySiteLog $log): void
    {
        foreach ($log->taskEntries()->with('task')->get() as $entry) {
            $task = $entry->task;
            if (!$task)
                continue;

            $newProgress = min(100, max(0, $entry->cumulative_progress ?? $task->progress_percent ?? 0));
            $updates = ['progress_percent' => $newProgress];

            if ($newProgress > 0 && !$task->actual_start) {
                $updates['actual_start'] = $log->log_date;
            }
            if ($entry->status_update === 'in_progress' && $task->status === 'to_do') {
                $updates['status'] = 'in_progress';
            }
            if ($entry->status_update === 'completed' || $newProgress >= 100) {
                $updates['status'] = 'done';
                $updates['progress_percent'] = 100;
                $updates['completed_at'] = now();
                $updates['actual_finish'] = $log->log_date;
            }
            if ($entry->status_update === 'blocked') {
                $updates['status'] = 'blocked';
            }

            // Accumulate actual hours
            if ($entry->hours_worked > 0) {
                $updates['actual_hours'] = ($task->actual_hours ?? 0) + $entry->hours_worked;
            }

            $task->update($updates);

            // Roll up through the full parent chain: subtask → parent → grandparent → ...
            $ancestor = $task->parent_id ? Task::find($task->parent_id) : null;
            while ($ancestor) {
                if ($ancestor->is_summary) {
                    $ancestor->rollUpFromChildren();
                }
                $ancestor = $ancestor->parent_id ? Task::find($ancestor->parent_id) : null;
            }
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(DailySiteLog::query()->where('cde_project_id', $this->pid())->with(['creator', 'approver', 'taskEntries']))
            ->columns([
                Tables\Columns\TextColumn::make('log_date')->date('D, M d Y')->sortable()->searchable()->weight('bold')->toggleable(),
                Tables\Columns\TextColumn::make('weather')->toggleable()->formatStateUsing(fn(?string $state) => match ($state) {
                    'sunny' => 'Sunny', 'partly_cloudy' => 'Partly', 'cloudy' => 'Cloudy', 'rainy' => 'Rainy',
                    'stormy' => 'Storm', 'snowy' => 'Snow', 'windy' => 'Wind', 'foggy' => 'Fog', default => $state ?? '—'
                }),
                Tables\Columns\TextColumn::make('temperature_display')->label('Temp')->toggleable()
                    ->state(fn($record) => $record->temperature_high || $record->temperature_low ? ($record->temperature_low ?? '–') . '–' . ($record->temperature_high ?? '–') . '°C' : '—'),
                Tables\Columns\TextColumn::make('workers_on_site')->label('Workers')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('task_entries_count')->label('Tasks')->counts('taskEntries')->sortable()->toggleable()
                    ->badge()->color('primary'),
                Tables\Columns\TextColumn::make('status')->badge()->toggleable()
                    ->color(fn(string $state) => match ($state) { 'approved' => 'success', 'submitted' => 'info', 'draft' => 'gray', 'rejected' => 'danger', default => 'warning'})->sortable(),
                Tables\Columns\TextColumn::make('work_performed')->limit(50)->toggleable()->placeholder('—'),
                Tables\Columns\TextColumn::make('delays')->limit(30)->toggleable(isToggledHiddenByDefault: true)->placeholder('None'),
                Tables\Columns\TextColumn::make('creator.name')->label('Logged By')->toggleable(),
                Tables\Columns\TextColumn::make('approver.name')->label('Approved By')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('log_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(DailySiteLog::$statuses),
                Tables\Filters\SelectFilter::make('weather')->options([
                    'sunny' => 'Sunny',
                    'partly_cloudy' => 'Partly Cloudy',
                    'cloudy' => 'Cloudy',
                    'rainy' => 'Rainy',
                    'stormy' => 'Stormy',
                ]),
                Tables\Filters\Filter::make('this_week')->label('This Week Only')
                    ->query(fn($q) => $q->whereBetween('log_date', [now()->startOfWeek(), now()->endOfWeek()]))->toggle(),
            ])
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                    // ── View Log ──
                    \Filament\Actions\Action::make('view')
                        ->icon('heroicon-o-eye')->color('gray')->modalWidth('screen')
                        ->modalHeading(fn(DailySiteLog $record) => 'Site Log — ' . $record->log_date->format('D, M d Y'))
                        ->schema([
                            Section::make('Overview')->schema([
                                Forms\Components\TextInput::make('weather_display')->label('Weather')->disabled(),
                                Forms\Components\TextInput::make('temp_display')->label('Temperature')->disabled(),
                                Forms\Components\TextInput::make('workers_on_site')->label('Workers')->disabled(),
                                Forms\Components\TextInput::make('visitors_on_site')->label('Visitors')->disabled(),
                                Forms\Components\TextInput::make('status_display')->label('Status')->disabled(),
                                Forms\Components\TextInput::make('logged_by')->label('Logged By')->disabled(),
                            ])->columns(3),
                            Section::make('Work Performed')->schema([
                                Forms\Components\Textarea::make('work_performed')->disabled()->rows(4)->columnSpanFull(),
                            ])->collapsed(false),
                            Section::make('Task Progress Entries')->schema([
                                Forms\Components\Textarea::make('task_progress_summary')->label('Tasks Updated')
                                    ->disabled()->rows(6)->columnSpanFull(),
                            ])->collapsed(false),
                            Section::make('Materials & Equipment')->schema([
                                Forms\Components\Textarea::make('materials_received')->disabled()->rows(2)->columnSpanFull(),
                                Forms\Components\Textarea::make('equipment_used')->disabled()->rows(2)->columnSpanFull(),
                            ])->collapsed(),
                            Section::make('Issues')->schema([
                                Forms\Components\Textarea::make('delays')->disabled()->rows(2)->columnSpanFull(),
                                Forms\Components\Textarea::make('safety_incidents')->label('Safety Incidents')->disabled()->rows(2)->columnSpanFull(),
                                Forms\Components\Textarea::make('notes')->disabled()->rows(2)->columnSpanFull(),
                            ])->collapsed(true),
                        ])
                        ->fillForm(function (DailySiteLog $record) {
                            $taskSummary = $record->taskEntries()->with('task')->get()->map(function ($entry) {
                                $taskName = $entry->task?->title ?? 'Unknown';
                                $wbs = $entry->task?->wbs_code ? "[{$entry->task->wbs_code}] " : '';
                                return "{$wbs}{$taskName}: +{$entry->progress_today}% → {$entry->cumulative_progress}% | {$entry->hours_worked}h | {$entry->status_update} | {$entry->remarks}";
                            })->join("\n") ?: 'No tasks linked to this log';

                            return [
                                'weather_display' => DailySiteLog::$weatherOptions[$record->weather] ?? $record->weather ?? '—',
                                'temp_display' => ($record->temperature_low ?? '–') . ' – ' . ($record->temperature_high ?? '–') . ' °C',
                                'workers_on_site' => $record->workers_on_site,
                                'visitors_on_site' => $record->visitors_on_site,
                                'status_display' => DailySiteLog::$statuses[$record->status] ?? $record->status,
                                'logged_by' => $record->creator?->name ?? '—',
                                'work_performed' => $record->work_performed ?? '',
                                'task_progress_summary' => $taskSummary,
                                'materials_received' => $record->materials_received ?? '',
                                'equipment_used' => $record->equipment_used ?? '',
                                'delays' => $record->delays ?? '',
                                'safety_incidents' => $record->safety_incidents ?? '',
                                'notes' => $record->notes ?? '',
                            ];
                        })
                        ->modalSubmitAction(false)->modalCancelActionLabel('Close'),

                    // ── Log Task Progress ──
                    \Filament\Actions\Action::make('logTaskProgress')
                        ->label('Log Task Progress')->icon('heroicon-o-clipboard-document-check')->color('primary')
                        ->modalWidth('screen')->modalHeading(fn(DailySiteLog $record) => 'Task Progress — ' . $record->log_date->format('M d'))
                        ->schema([
                            Forms\Components\Repeater::make('task_entries')
                                ->label('Task Progress Entries')
                                ->schema([
                                    Forms\Components\Select::make('task_id')
                                        ->label('Task')
                                        ->options(fn() => $this->getActiveTaskOptions())
                                        ->searchable()->required()->columnSpan(2),
                                    Forms\Components\TextInput::make('progress_today')
                                        ->label('Progress Today (%)')
                                        ->numeric()->minValue(0)->maxValue(100)->default(0)->suffix('%'),
                                    Forms\Components\TextInput::make('cumulative_progress')
                                        ->label('Cumulative (%)')
                                        ->numeric()->minValue(0)->maxValue(100)->suffix('%'),
                                    Forms\Components\TextInput::make('hours_worked')
                                        ->label('Hours Worked')
                                        ->numeric()->minValue(0)->step(0.5)->default(0)->suffix('h'),
                                    Forms\Components\TextInput::make('workers_assigned')
                                        ->label('Workers')
                                        ->numeric()->minValue(0)->default(0),
                                    Forms\Components\Select::make('status_update')
                                        ->label('Status')
                                        ->options(DailySiteLogTask::$statusOptions),
                                    Forms\Components\TextInput::make('remarks')
                                        ->label('Remarks')
                                        ->placeholder('Brief note...'),
                                ])
                                ->columns(4)
                                ->defaultItems(1)
                                ->addActionLabel('+ Add Task')
                                ->reorderable(false),
                        ])
                        ->fillForm(function (DailySiteLog $record) {
                            $existing = $record->taskEntries()->get()->map(fn($e) => [
                                'task_id' => $e->task_id,
                                'progress_today' => $e->progress_today,
                                'cumulative_progress' => $e->cumulative_progress,
                                'hours_worked' => $e->hours_worked,
                                'workers_assigned' => $e->workers_assigned,
                                'status_update' => $e->status_update,
                                'remarks' => $e->remarks,
                            ])->toArray();
                            return ['task_entries' => count($existing) > 0 ? $existing : [['task_id' => null, 'progress_today' => 0, 'cumulative_progress' => null, 'hours_worked' => 0, 'workers_assigned' => 0, 'status_update' => null, 'remarks' => '']]];
                        })
                        ->action(function (array $data, DailySiteLog $record): void {
                            // Clear existing entries and recreate
                            $record->taskEntries()->delete();

                            foreach ($data['task_entries'] ?? [] as $entry) {
                                if (empty($entry['task_id']))
                                    continue;

                                $task = Task::find($entry['task_id']);
                                $cumulative = $entry['cumulative_progress'] ?? (($task->progress_percent ?? 0) + ($entry['progress_today'] ?? 0));

                                $record->taskEntries()->create([
                                    'task_id' => $entry['task_id'],
                                    'progress_today' => $entry['progress_today'] ?? 0,
                                    'cumulative_progress' => min(100, max(0, $cumulative)),
                                    'hours_worked' => $entry['hours_worked'] ?? 0,
                                    'workers_assigned' => $entry['workers_assigned'] ?? 0,
                                    'status_update' => $entry['status_update'] ?? null,
                                    'remarks' => $entry['remarks'] ?? null,
                                ]);
                            }

                            // Sync progress to tasks if log is approved
                            if ($record->status === 'approved') {
                                $this->syncTaskProgress($record);
                            }

                            Notification::make()
                                ->title('Task progress saved (' . count(array_filter($data['task_entries'] ?? [], fn($e) => !empty($e['task_id']))) . ' tasks)')
                                ->success()->send();
                        }),

                    // ── Submit for Review ──
                    \Filament\Actions\Action::make('submit')
                        ->label('Submit for Review')->icon('heroicon-o-paper-airplane')->color('info')
                        ->visible(fn(DailySiteLog $record) => $record->status === 'draft')
                        ->requiresConfirmation()->modalDescription('Submit this log for management review.')
                        ->action(function (DailySiteLog $record): void {
                            $record->update(['status' => 'submitted']);
                            Notification::make()->title('Log submitted for review')->success()->send();
                        }),

                    // ── Approve (syncs task progress) ──
                    \Filament\Actions\Action::make('approve')
                        ->icon('heroicon-o-check-circle')->color('success')
                        ->visible(fn(DailySiteLog $record) => in_array($record->status, ['draft', 'submitted']))
                        ->requiresConfirmation()->modalHeading('Approve Daily Log')
                        ->modalDescription('Approving will also sync task progress updates to the schedule.')
                        ->action(function (DailySiteLog $record): void {
                            $record->update(['status' => 'approved', 'approved_by' => auth()->id()]);
                            $this->syncTaskProgress($record);
                            Notification::make()->title('Log approved — task progress synced to schedule')->success()->send();
                        }),

                    // ── Reject ──
                    \Filament\Actions\Action::make('reject')
                        ->label('Reject')->icon('heroicon-o-x-circle')->color('danger')
                        ->visible(fn(DailySiteLog $record) => $record->status === 'submitted')
                        ->requiresConfirmation()
                        ->action(function (DailySiteLog $record): void {
                            $record->update(['status' => 'rejected']);
                            Notification::make()->title('Log rejected')->warning()->send();
                        }),

                    // ── Edit ──
                    \Filament\Actions\Action::make('edit')
                        ->icon('heroicon-o-pencil')->modalWidth('screen')
                        ->schema($this->logFormSchema())
                        ->fillForm(function (DailySiteLog $record) {
                            $formData = $record->toArray();
                            $formData['task_entries'] = $record->taskEntries()->get()->map(fn($e) => [
                                'task_id' => $e->task_id,
                                'progress_today' => $e->progress_today,
                                'cumulative_progress' => $e->cumulative_progress,
                                'hours_worked' => $e->hours_worked,
                                'workers_assigned' => $e->workers_assigned,
                                'status_update' => $e->status_update,
                                'remarks' => $e->remarks,
                            ])->toArray();
                            return $formData;
                        })
                        ->action(function (array $data, DailySiteLog $record): void {
                            $taskEntries = $data['task_entries'] ?? [];
                            unset($data['task_entries']);
                            $record->update($data);

                            // Recreate task entries
                            $record->taskEntries()->delete();
                            foreach ($taskEntries as $entry) {
                                if (empty($entry['task_id']))
                                    continue;
                                $task = Task::find($entry['task_id']);
                                $cumulative = $entry['cumulative_progress'] ?? (($task->progress_percent ?? 0) + ($entry['progress_today'] ?? 0));
                                $record->taskEntries()->create([
                                    'task_id' => $entry['task_id'],
                                    'progress_today' => $entry['progress_today'] ?? 0,
                                    'cumulative_progress' => min(100, max(0, $cumulative)),
                                    'hours_worked' => $entry['hours_worked'] ?? 0,
                                    'workers_assigned' => $entry['workers_assigned'] ?? 0,
                                    'status_update' => $entry['status_update'] ?? null,
                                    'remarks' => $entry['remarks'] ?? null,
                                ]);
                            }

                            Notification::make()->title('Log updated')->success()->send();
                        }),

                    // ── Duplicate ──
                    \Filament\Actions\Action::make('duplicate')
                        ->icon('heroicon-o-document-duplicate')->color('gray')
                        ->requiresConfirmation()->modalDescription('Creates a copy for today with same task entries.')
                        ->action(function (DailySiteLog $record): void {
                            $new = $record->replicate();
                            $new->log_date = now();
                            $new->status = 'draft';
                            $new->approved_by = null;
                            $new->created_by = auth()->id();
                            $new->save();

                            // Duplicate task entries too
                            foreach ($record->taskEntries as $entry) {
                                $new->taskEntries()->create([
                                    'task_id' => $entry->task_id,
                                    'progress_today' => 0,
                                    'cumulative_progress' => $entry->cumulative_progress,
                                    'hours_worked' => 0,
                                    'workers_assigned' => $entry->workers_assigned,
                                    'status_update' => $entry->status_update,
                                    'remarks' => null,
                                ]);
                            }

                            Notification::make()->title('Log duplicated for today with tasks')->success()->send();
                        }),

                    // ── Delete ──
                    \Filament\Actions\Action::make('delete')
                        ->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                        ->action(fn(DailySiteLog $record) => $record->delete()),
                ]),
            ])
            ->toolbarActions([
                $this->exportCsvAction('daily_logs', fn() => DailySiteLog::query()->where('cde_project_id', $this->pid())->with(['creator', 'approver']), [
                    'log_date' => 'Date',
                    'weather' => 'Weather',
                    'temperature_high' => 'Temp High',
                    'temperature_low' => 'Temp Low',
                    'workers_on_site' => 'Workers',
                    'visitors_on_site' => 'Visitors',
                    'status' => 'Status',
                    'work_performed' => 'Work Performed',
                    'materials_received' => 'Materials',
                    'equipment_used' => 'Equipment',
                    'delays' => 'Delays',
                    'safety_incidents' => 'Safety Incidents',
                    'notes' => 'Notes',
                    'creator.name' => 'Logged By',
                    'approver.name' => 'Approved By',
                ]),
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('bulkApprove')->label('Approve')
                        ->icon('heroicon-o-check-circle')->color('success')->requiresConfirmation()
                        ->action(function ($records): void {
                            foreach ($records as $record) {
                                $record->update(['status' => 'approved', 'approved_by' => auth()->id()]);
                                $this->syncTaskProgress($record);
                            }
                            Notification::make()->title($records->count() . ' logs approved — task progress synced')->success()->send();
                        })->deselectRecordsAfterCompletion(),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Daily Site Logs')
            ->emptyStateDescription('Start logging daily site activities and track task progress.')
            ->emptyStateIcon('heroicon-o-map-pin')
            ->striped()->paginated([10, 25, 50]);
    }
}
