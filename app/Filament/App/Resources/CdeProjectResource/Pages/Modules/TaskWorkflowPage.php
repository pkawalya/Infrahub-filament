<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\CdeProject;
use App\Models\Milestone;
use App\Models\Task;
use App\Models\TaskDependency;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

use App\Filament\App\Concerns\ExportsTableCsv;

class TaskWorkflowPage extends BaseModulePage implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms, ExportsTableCsv;

    /**
     * This unified Schedule page merges task_workflow + core (Work Orders) + planning_progress.
     */
    protected static string $moduleCode = 'task_workflow';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Schedule';
    protected static ?string $title = 'Project Schedule';
    protected string $view = 'filament.app.pages.modules.task-workflow';

    /** Active sub-tab: schedule | work_orders | milestones */
    public string $activeTab = 'schedule';

    // ─── Modal / Form State (Livewire-driven) ───────────────────
    public bool $showTaskModal = false;
    public bool $showMilestoneModal = false;
    public bool $showEditModal = false;
    public bool $showProgressModal = false;
    public ?int $editingTaskId = null;

    public array $editTask = [
        'title' => '',
        'status' => 'to_do',
        'priority' => 'medium',
        'start_date' => '',
        'due_date' => '',
        'assigned_to' => null,
        'description' => '',
    ];

    public array $progressTask = [
        'status' => 'to_do',
        'progress_percent' => 0,
        'actual_hours' => null,
    ];

    public array $newTask = [
        'title' => '',
        'type' => 'task',
        'priority' => 'medium',
        'status' => 'to_do',
        'parent_id' => null,
        'start_date' => '',
        'duration_days' => 1,
        'due_date' => '',
        'assigned_to' => null,
        'resource_names' => '',
        'description' => '',
    ];

    public array $newMilestone = [
        'title' => '',
        'start_date' => '',
        'parent_id' => null,
        'priority' => 'medium',
    ];

    /**
     * Accept access if ANY of the 3 merged module codes is enabled.
     */
    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $enabled = $this->record->getEnabledModules();
        $accepted = ['task_workflow', 'core', 'planning_progress'];
        if (!array_intersect($accepted, $enabled)) {
            abort(403, 'Schedule module is not enabled for this project.');
        }
    }

    public static function canAccess(array $parameters = []): bool
    {
        $record = $parameters['record'] ?? null;
        if ($record instanceof CdeProject) {
            $enabled = $record->getEnabledModules();
            return (bool) array_intersect(['task_workflow', 'core', 'planning_progress'], $enabled);
        }
        return true;
    }

    private function pid(): int
    {
        return $this->record->id;
    }
    private function cid(): int
    {
        return $this->record->company_id;
    }

    public function teamOptions(): array
    {
        return User::where('company_id', $this->cid())
            ->where('is_active', true)->pluck('name', 'id')->toArray();
    }

    public function taskOptions(): array
    {
        return Task::where('cde_project_id', $this->pid())->pluck('title', 'id')->toArray();
    }

    // ─── Stats (optimized single query) ──────────────────────────

    public function getStats(): array
    {
        $pid = $this->pid();

        $stats = \DB::selectOne("
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status = 'to_do' THEN 1 ELSE 0 END) as todo,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = 'done' THEN 1 ELSE 0 END) as done,
                SUM(CASE WHEN status = 'blocked' THEN 1 ELSE 0 END) as blocked,
                SUM(CASE WHEN status NOT IN ('done','cancelled') AND due_date IS NOT NULL AND due_date < NOW() THEN 1 ELSE 0 END) as overdue,
                ROUND(COALESCE(AVG(progress_percent), 0)) as avg_progress,
                SUM(CASE WHEN is_milestone = 1 THEN 1 ELSE 0 END) as milestones,
                COALESCE(SUM(estimated_hours), 0) as total_est_hours,
                COALESCE(SUM(actual_hours), 0) as total_act_hours
            FROM tasks WHERE cde_project_id = ? AND deleted_at IS NULL
        ", [$pid]);

        $total = (int) $stats->total;
        $todo = (int) $stats->todo;
        $inProgress = (int) $stats->in_progress;
        $done = (int) $stats->done;
        $blocked = (int) $stats->blocked;
        $overdue = (int) $stats->overdue;
        $avgProg = (int) $stats->avg_progress;
        $totalEstHours = (float) $stats->total_est_hours;
        $totalActHours = (float) $stats->total_act_hours;

        return [
            [
                'label' => 'Total Tasks',
                'value' => $total,
                'sub' => $done . ' completed · ' . (int) $stats->milestones . ' milestones',
                'sub_type' => 'success',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" /></svg>'
            ],
            [
                'label' => 'In Progress',
                'value' => $inProgress,
                'sub' => $blocked > 0 ? $blocked . ' blocked' : 'Active',
                'sub_type' => $blocked > 0 ? 'danger' : 'info',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2563eb" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" /></svg>',
                'icon_bg' => '#eff6ff'
            ],
            [
                'label' => 'Overdue',
                'value' => $overdue,
                'sub' => 'Past due date',
                'sub_type' => $overdue > 0 ? 'danger' : 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#d97706" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#fffbeb'
            ],
            [
                'label' => 'Effort',
                'value' => round($totalActHours) . 'h / ' . round($totalEstHours) . 'h',
                'sub' => $avgProg . '% avg progress',
                'sub_type' => $avgProg >= 70 ? 'success' : ($avgProg >= 40 ? 'warning' : 'neutral'),
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>',
                'icon_bg' => '#ecfdf5'
            ],
        ];
    }

    // ─── Gantt Data for JS ────────────────────────────────────────

    public function getGanttData(): array
    {
        return Task::getGanttData($this->pid());
    }

    // ─── Task Form Schema ─────────────────────────────────────────

    private function taskFormSchema(bool $isCreate = false): array
    {
        return [
            Section::make('Task Details')->schema([
                Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\Select::make('type')->options([
                    'task' => '📋 Task',
                    'milestone' => '◆ Milestone',
                    'summary' => '📁 Summary',
                    'bug' => '🐛 Bug',
                    'feature' => '⭐ Feature',
                    'inspection' => '🔍 Inspection',
                    'review' => '📝 Review',
                    'other' => 'Other',
                ])->default('task')
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set) {
                        if ($state === 'milestone') {
                            $set('is_milestone', true);
                            $set('duration_days', 0);
                        } else {
                            $set('is_milestone', false);
                        }
                    }),
                Forms\Components\Select::make('priority')->options(Task::$priorities)->required()->default('medium'),
                Forms\Components\Select::make('status')->options(Task::$statuses)->required()->default($isCreate ? 'to_do' : null),
                Forms\Components\Select::make('parent_id')->label('Parent Task / Phase')
                    ->options(fn() => $this->taskOptions())
                    ->searchable()->nullable()
                    ->helperText('Select a parent to create a WBS hierarchy'),
                Forms\Components\Hidden::make('is_milestone')->default(false),
            ])->columns(2),

            Section::make('Schedule')->schema([
                Forms\Components\DatePicker::make('start_date')->label('Start')
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        $duration = $get('duration_days');
                        if ($state && $duration) {
                            $finish = Task::calculateFinishDate($state, (int) $duration);
                            $set('due_date', $finish->format('Y-m-d'));
                        }
                    }),
                Forms\Components\TextInput::make('duration_days')->label('Duration (days)')
                    ->numeric()->minValue(0)->default(1)
                    ->reactive()
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        $start = $get('start_date');
                        if ($start && $state) {
                            $finish = Task::calculateFinishDate($start, (int) $state);
                            $set('due_date', $finish->format('Y-m-d'));
                        }
                    }),
                Forms\Components\DatePicker::make('due_date')->label('Finish'),
                Forms\Components\Select::make('constraint_type')->label('Constraint')
                    ->options(Task::$constraintTypes)->nullable()
                    ->helperText('ASAP is default'),
                Forms\Components\DatePicker::make('constraint_date')
                    ->visible(fn(Get $get) => in_array($get('constraint_type'), ['mso', 'mfo', 'snet', 'snlt', 'fnet', 'fnlt'])),
            ])->columns(3),

            Section::make('Resources & Effort')->schema([
                Forms\Components\Select::make('assigned_to')->label('Primary Assignee')
                    ->options(fn() => $this->teamOptions())
                    ->searchable()->nullable(),
                Forms\Components\TextInput::make('resource_names')
                    ->label('Resource Names')
                    ->placeholder('e.g. John, Excavator, Crane')
                    ->maxLength(500),
                Forms\Components\TextInput::make('estimated_hours')->label('Work (hrs)')
                    ->numeric()->suffix('hrs'),
                Forms\Components\TextInput::make('actual_hours')->label('Actual (hrs)')
                    ->numeric()->suffix('hrs')->visible(!$isCreate),
                Forms\Components\TextInput::make('progress_percent')->label('% Complete')
                    ->numeric()->minValue(0)->maxValue(100)->suffix('%')->default(0),
                Forms\Components\TextInput::make('resource_units')->label('Units %')
                    ->numeric()->default(100)->suffix('%')
                    ->helperText('100% = full time'),
            ])->columns(3),

            Section::make('Cost')->schema([
                Forms\Components\TextInput::make('fixed_cost')->label('Fixed Cost')
                    ->numeric()->prefix('$')->default(0),
                Forms\Components\TextInput::make('cost_rate')->label('Cost Rate ($/hr)')
                    ->numeric()->prefix('$'),
            ])->columns(2)->collapsed(),

            Section::make('Dependencies (Predecessors)')->schema([
                Forms\Components\Repeater::make('predecessor_list')
                    ->schema([
                        Forms\Components\Select::make('depends_on_id')->label('Predecessor Task')
                            ->options(fn() => $this->taskOptions())
                            ->searchable()->required(),
                        Forms\Components\Select::make('dependency_type')->label('Type')
                            ->options(TaskDependency::$types)
                            ->default('finish_to_start')->required(),
                        Forms\Components\TextInput::make('lag_days')->label('Lag (days)')
                            ->numeric()->default(0)
                            ->helperText('Negative = lead'),
                    ])
                    ->columns(3)
                    ->defaultItems(0)
                    ->addActionLabel('+ Add Predecessor')
                    ->collapsible()
                    ->columnSpanFull(),
            ])->collapsed(),

            Section::make('Description & Notes')->schema([
                Forms\Components\RichEditor::make('description')
                    ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link'])
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('notes')->label('Notes')->rows(2)->columnSpanFull(),
            ])->collapsed(!$isCreate),

            Section::make('Attachments')->schema([
                Forms\Components\FileUpload::make('attachments')
                    ->label('Files')->multiple()->maxFiles(10)->maxSize(10240)
                    ->directory('task-attachments')
                    ->acceptedFileTypes([
                        'application/pdf',
                        'image/*',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-project',
                        'text/csv',
                        'text/plain',
                    ])
                    ->columnSpanFull()
                    ->helperText('Upload documents, images, or spreadsheets (max 10 files, 10MB each)'),
            ])->collapsed()->collapsible(),
        ];
    }

    // ─── Livewire Action Methods (called via wire:click) ────────

    protected function getHeaderActions(): array
    {
        return []; // Actions are handled by custom modals in the Blade template
    }

    public function openTaskModal(): void
    {
        $this->newTask = [
            'title' => '',
            'type' => 'task',
            'priority' => 'medium',
            'status' => 'to_do',
            'parent_id' => null,
            'start_date' => now()->format('Y-m-d'),
            'duration_days' => 1,
            'due_date' => now()->addDay()->format('Y-m-d'),
            'assigned_to' => null,
            'resource_names' => '',
            'description' => '',
        ];
        $this->showTaskModal = true;
    }

    public function submitNewTask(): void
    {
        $this->validate([
            'newTask.title' => 'required|string|max:255',
            'newTask.start_date' => 'required|date',
        ]);

        $data = $this->newTask;
        $data['company_id'] = $this->cid();
        $data['cde_project_id'] = $this->pid();
        $data['created_by'] = auth()->id();

        if ($data['type'] === 'milestone') {
            $data['is_milestone'] = true;
            $data['duration_days'] = 0;
        }

        $this->autoFillDates($data);
        Task::create($data);
        Task::regenerateWbs($this->pid());

        $this->showTaskModal = false;
        Notification::make()->title('Task created: ' . $data['title'])->success()->send();
        $this->dispatch('gantt-refresh');
    }

    public function openMilestoneModal(): void
    {
        $this->newMilestone = [
            'title' => '',
            'start_date' => now()->format('Y-m-d'),
            'parent_id' => null,
            'priority' => 'medium',
        ];
        $this->showMilestoneModal = true;
    }

    public function submitMilestone(): void
    {
        $this->validate([
            'newMilestone.title' => 'required|string|max:255',
            'newMilestone.start_date' => 'required|date',
        ]);

        $data = $this->newMilestone;
        $data['company_id'] = $this->cid();
        $data['cde_project_id'] = $this->pid();
        $data['created_by'] = auth()->id();
        $data['type'] = 'milestone';
        $data['is_milestone'] = true;
        $data['duration_days'] = 0;
        $data['due_date'] = $data['start_date'];
        $data['status'] = 'to_do';
        Task::create($data);
        Task::regenerateWbs($this->pid());

        $this->showMilestoneModal = false;
        Notification::make()->title('Milestone added ◆')->success()->send();
        $this->dispatch('gantt-refresh');
    }

    public function openEditTaskModal(int $taskId): void
    {
        $task = Task::find($taskId);
        if (!$task)
            return;

        $this->editingTaskId = $taskId;
        $this->editTask = [
            'title' => $task->title,
            'status' => $task->status,
            'priority' => $task->priority,
            'start_date' => $task->start_date?->format('Y-m-d'),
            'due_date' => $task->due_date?->format('Y-m-d'),
            'assigned_to' => $task->assigned_to,
            'description' => $task->description ?? '',
        ];
        $this->showEditModal = true;
    }

    public function submitEditTask(): void
    {
        $this->validate([
            'editTask.title' => 'required|string|max:255',
            'editTask.start_date' => 'nullable|date',
            'editTask.due_date' => 'nullable|date',
        ]);

        $task = Task::find($this->editingTaskId);
        if (!$task)
            return;

        $task->update($this->editTask);
        $this->showEditModal = false;
        Notification::make()->title('Task updated')->success()->send();
        $this->dispatch('gantt-refresh');
    }

    public function openProgressModal(int $taskId): void
    {
        $task = Task::find($taskId);
        if (!$task)
            return;

        $this->editingTaskId = $taskId;
        $this->progressTask = [
            'status' => $task->status,
            'progress_percent' => $task->progress_percent ?? 0,
            'actual_hours' => $task->actual_hours,
        ];
        $this->showProgressModal = true;
    }

    public function submitProgress(): void
    {
        $task = Task::find($this->editingTaskId);
        if (!$task)
            return;

        $data = $this->progressTask;
        if ($data['status'] === 'done') {
            $data['progress_percent'] = 100;
            $data['completed_at'] = now();
            $data['actual_finish'] = now()->format('Y-m-d');
        }
        if ($data['status'] === 'in_progress' && !$task->actual_start) {
            $data['actual_start'] = now()->format('Y-m-d');
        }

        $task->update($data);

        if ($task->parent_id) {
            $parent = Task::find($task->parent_id);
            $parent?->rollUpFromChildren();
        }

        $this->showProgressModal = false;
        Notification::make()->title('Progress updated')->success()->send();
        $this->dispatch('gantt-refresh');
    }

    public function doSaveBaseline(): void
    {
        $tasks = Task::where('cde_project_id', $this->pid())->get();
        foreach ($tasks as $task) {
            $task->saveBaseline();
        }
        $this->record->update(['baseline_saved_at' => now()]);
        Notification::make()
            ->title('Baseline saved for ' . $tasks->count() . ' tasks')
            ->body('Snapshot taken at ' . now()->format('M d, Y H:i'))
            ->success()->send();
        $this->dispatch('gantt-refresh');
    }

    public function doRebuildWbs(): void
    {
        Task::regenerateWbs($this->pid());
        Notification::make()->title('WBS codes regenerated')->success()->send();
        $this->dispatch('gantt-refresh');
    }

    public function updateTaskField(int $taskId, string $field, $value): void
    {
        $task = Task::where('cde_project_id', $this->pid())->find($taskId);
        if (!$task)
            return;

        $allowedFields = ['title', 'duration_days', 'start_date', 'progress'];
        if (!in_array($field, $allowedFields))
            return;

        if ($field === 'duration_days') {
            $value = max(0, (int) $value);
            $task->duration_days = $value;
            if ($value > 0 && $task->start_date) {
                $task->due_date = Task::calculateFinishDate($task->start_date, $value)->format('Y-m-d');
            }
        } elseif ($field === 'start_date') {
            $task->start_date = $value;
            if ($task->duration_days > 0) {
                $task->due_date = Task::calculateFinishDate($task->start_date, $task->duration_days)->format('Y-m-d');
            }
        } elseif ($field === 'progress') {
            $value = min(100, max(0, (int) $value));
            $task->progress_percent = $value; // Changed from $task->progress = $value;
            if ($value === 100)
                $task->status = 'done';
            elseif ($value > 0)
                $task->status = 'in_progress';
            elseif ($task->status === 'done' && $value < 100)
                $task->status = 'in_progress';
        } else {
            $task->{$field} = $value;
        }

        $task->save();

        if ($field === 'duration_days' || $field === 'start_date') {
            $task->forwardPass();
        }

        $this->dispatch('gantt-refresh');
    }

    public function indentTask(int $taskId): void
    {
        $task = Task::where('cde_project_id', $this->pid())->find($taskId);
        if (!$task)
            return;

        // Find the task immediately preceding this one in the same WBS sibling list
        $previousSibling = Task::where('cde_project_id', $this->pid())
            ->where('parent_id', $task->parent_id)
            ->where('sort_order', '<', $task->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($previousSibling) {
            $task->parent_id = $previousSibling->id;
            $task->save();
            Task::regenerateWbs($this->pid());
            $this->dispatch('gantt-refresh');
        } else {
            Notification::make()->title('Cannot indent')->body('No previous sibling task to become a parent.')->warning()->send();
        }
    }

    public function outdentTask(int $taskId): void
    {
        $task = Task::where('cde_project_id', $this->pid())->find($taskId);
        // Cannot outdent if it's already at top level
        if (!$task || !$task->parent_id)
            return;

        $parent = Task::find($task->parent_id);
        if ($parent) {
            $task->parent_id = $parent->parent_id;
            $task->save();
            Task::regenerateWbs($this->pid());
            $this->dispatch('gantt-refresh');
        }
    }

    // ─── Table View (Filament Table) ──────────────────────────────

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Task::query()->where('cde_project_id', $this->pid())
                    ->with(['creator', 'assignee', 'parent', 'predecessorLinks'])
                    ->orderBy('sort_order')
                    ->orderBy('wbs_code')
                    ->orderBy('id')
            )
            ->columns([
                Tables\Columns\TextColumn::make('wbs_code')->label('WBS')->sortable()->toggleable()
                    ->fontFamily('mono')->size('xs')->width('60px'),
                Tables\Columns\IconColumn::make('is_milestone')->label('')->boolean()->toggleable()
                    ->trueIcon('heroicon-s-star')->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')->falseColor('gray')
                    ->width('30px'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(45)->toggleable()
                    ->description(fn(Task $r) => $r->parent ? '↳ ' . $r->parent->title : null)
                    ->weight(fn(Task $r) => $r->is_summary ? 'bold' : null),
                Tables\Columns\TextColumn::make('duration_days')->label('Duration')->toggleable()
                    ->suffix('d')->placeholder('—')->sortable()->width('70px')
                    ->state(fn(Task $r) => $r->is_milestone ? '0' : ($r->duration_days ?? '—')),
                Tables\Columns\TextColumn::make('start_date')->label('Start')->date('M d')->sortable()->width('80px')->toggleable(),
                Tables\Columns\TextColumn::make('due_date')->label('Finish')->date('M d')->sortable()->width('80px')->toggleable()
                    ->color(fn($record) => $record->due_date?->isPast() && !in_array($record->status, ['done', 'cancelled']) ? 'danger' : null),
                Tables\Columns\TextColumn::make('status')->badge()->toggleable()
                    ->color(fn(string $state) => match ($state) { 'done' => 'success', 'in_progress' => 'info', 'review' => 'primary', 'blocked' => 'danger', default => 'gray'})->sortable(),
                Tables\Columns\TextColumn::make('progress_percent')->label('Timeline')->toggleable()
                    ->html()
                    ->sortable()
                    ->formatStateUsing(function ($state, Task $record) {
                        $progress = (int) ($state ?? 0);
                        $startDate = $record->start_date;
                        $dueDate = $record->due_date;
                        $status = $record->status ?? '';
                        $isDone = in_array($status, ['done', 'cancelled']);

                        // Calculate % time elapsed
                        $timeElapsed = 0;
                        if ($startDate && $dueDate) {
                            $start = \Carbon\Carbon::parse($startDate);
                            $end = \Carbon\Carbon::parse($dueDate);
                            $totalDays = max(1, $start->diffInDays($end));
                            $daysSpent = max(0, $start->diffInDays(now()));
                            $timeElapsed = min(100, round(($daysSpent / $totalDays) * 100));
                        }

                        $isOverdue = $dueDate && \Carbon\Carbon::parse($dueDate)->isPast() && !$isDone;
                        $variance = $progress - $timeElapsed;

                        // Colors
                        $barColor = $isDone ? '#10b981' : ($progress > 0 ? '#22c55e' : '#d1d5db');
                        $bgColor = $isOverdue ? '#fed7aa' : '#e5e7eb';

                        // Time marker
                        $marker = '';
                        if ($startDate && $dueDate && !$isDone && $timeElapsed > 0 && $timeElapsed < 100) {
                            $marker = '<div style="position:absolute;top:-2px;bottom:-2px;left:' . $timeElapsed . '%;width:2px;background:#ef4444;z-index:2;box-shadow:0 0 3px rgba(239,68,68,0.5);"></div>';
                        }

                        // Variance label
                        $varLabel = '';
                        if ($startDate && $dueDate && !$isDone) {
                            if ($variance < 0) {
                                $varLabel = '<span style="font-size:9px;font-weight:600;color:#dc2626;">▼' . abs($variance) . '%</span>';
                            } elseif ($variance > 0) {
                                $varLabel = '<span style="font-size:9px;font-weight:600;color:#10b981;">▲' . $variance . '%</span>';
                            } else {
                                $varLabel = '<span style="font-size:9px;font-weight:600;color:#6b7280;">On track</span>';
                            }
                        }

                        $pctColor = $isDone ? '#10b981' : ($isOverdue ? '#dc2626' : '#374151');

                        $tooltip = $progress . '% done';
                        if ($startDate && $dueDate) {
                            $tooltip .= ' · ' . $timeElapsed . '% time used';
                            if (!$isDone) {
                                $tooltip .= ' · ' . ($variance >= 0 ? $variance . '% ahead' : abs($variance) . '% behind');
                            }
                        }

                        return new \Illuminate\Support\HtmlString(
                            '<div title="' . e($tooltip) . '" style="min-width:120px;max-width:160px;">' .
                            '<div style="position:relative;height:14px;border-radius:7px;overflow:hidden;background:' . $bgColor . ';">' .
                            '<div style="position:absolute;top:0;left:0;bottom:0;width:' . $progress . '%;background:' . $barColor . ';border-radius:7px 0 0 7px;transition:width 0.3s;"></div>' .
                            $marker .
                            '</div>' .
                            '<div style="display:flex;justify-content:space-between;align-items:center;margin-top:2px;">' .
                            '<span style="font-size:10px;font-weight:700;color:' . $pctColor . ';">' . $progress . '%</span>' .
                            $varLabel .
                            '</div>' .
                            '</div>'
                        );
                    }),
                Tables\Columns\TextColumn::make('priority')->badge()->toggleable()
                    ->color(fn(string $state) => match ($state) { 'urgent' => 'danger', 'high' => 'warning', 'medium' => 'info', default => 'gray'})->sortable(),
                Tables\Columns\TextColumn::make('assignee.name')->label('Resource')->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('estimated_hours')->label('Work')->suffix('h')->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('actual_hours')->label('Actual')->suffix('h')->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('fixed_cost')->label('Cost')->money('USD')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Task::$statuses)->multiple(),
                Tables\Filters\SelectFilter::make('priority')->options(Task::$priorities)->multiple(),
                Tables\Filters\Filter::make('milestones')->label('Milestones')
                    ->query(fn($q) => $q->where('is_milestone', true))->toggle(),
                Tables\Filters\Filter::make('summary')->label('Summary Tasks')
                    ->query(fn($q) => $q->where('is_summary', true))->toggle(),
                Tables\Filters\Filter::make('overdue')->label('Overdue')
                    ->query(fn($q) => $q->whereNotIn('status', ['done', 'cancelled'])->whereNotNull('due_date')->where('due_date', '<', now()))->toggle(),
                Tables\Filters\Filter::make('has_baseline')->label('Has Baseline')
                    ->query(fn($q) => $q->whereNotNull('baseline_start'))->toggle(),
            ])
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\Action::make('updateProgress')
                        ->label('Progress')->icon('heroicon-o-chart-bar')->color('info')
                        ->schema([
                            Forms\Components\Select::make('status')->options(Task::$statuses)->required(),
                            Forms\Components\TextInput::make('progress_percent')->label('% Complete')
                                ->numeric()->minValue(0)->maxValue(100)->required()->suffix('%'),
                            Forms\Components\TextInput::make('actual_hours')->label('Hours Worked')
                                ->numeric()->suffix('hrs'),
                            Forms\Components\DatePicker::make('actual_start')->label('Actual Start'),
                            Forms\Components\DatePicker::make('actual_finish')->label('Actual Finish')
                                ->visible(fn(Get $get) => $get('status') === 'done'),
                        ])
                        ->fillForm(fn(Task $record) => [
                            'status' => $record->status,
                            'progress_percent' => $record->progress_percent,
                            'actual_hours' => $record->actual_hours,
                            'actual_start' => $record->actual_start,
                            'actual_finish' => $record->actual_finish,
                        ])
                        ->action(function (array $data, Task $record): void {
                            if ($data['status'] === 'done') {
                                $data['progress_percent'] = 100;
                                $data['completed_at'] = now();
                                $data['actual_finish'] = $data['actual_finish'] ?? now()->format('Y-m-d');
                            }
                            if ($data['status'] === 'in_progress' && !$record->actual_start) {
                                $data['actual_start'] = now()->format('Y-m-d');
                            }
                            $record->update($data);

                            // Roll up to parent if exists
                            if ($record->parent_id) {
                                $parent = Task::find($record->parent_id);
                                $parent?->rollUpFromChildren();
                            }

                            Notification::make()->title('Progress updated')->success()->send();
                            $this->dispatch('gantt-refresh');
                        }),

                    \Filament\Actions\Action::make('assign')
                        ->label('Assign')->icon('heroicon-o-user-plus')->color('info')
                        ->schema([
                            Forms\Components\Select::make('assigned_to')->label('Assign To')
                                ->options($this->teamOptions())->searchable()->required(),
                            Forms\Components\TextInput::make('resource_names')->label('Resource Names')
                                ->placeholder('Additional resources'),
                            Forms\Components\TextInput::make('resource_units')->label('Units %')
                                ->numeric()->default(100)->suffix('%'),
                        ])
                        ->fillForm(fn(Task $record) => [
                            'assigned_to' => $record->assigned_to,
                            'resource_names' => $record->resource_names,
                            'resource_units' => $record->resource_units,
                        ])
                        ->action(function (array $data, Task $record): void {
                            $record->update($data);
                            $name = User::find($data['assigned_to'])?->name ?? 'Unknown';
                            Notification::make()->title('Assigned to ' . $name)->success()->send();
                            $this->dispatch('gantt-refresh');
                        }),

                    \Filament\Actions\Action::make('addDependency')
                        ->label('Predecessors')->icon('heroicon-o-link')->color('warning')
                        ->schema([
                            Forms\Components\Repeater::make('predecessors')
                                ->schema([
                                    Forms\Components\Select::make('depends_on_id')->label('Predecessor')
                                        ->options(fn() => $this->taskOptions())
                                        ->searchable()->required(),
                                    Forms\Components\Select::make('dependency_type')->label('Type')
                                        ->options(TaskDependency::$types)
                                        ->default('finish_to_start')->required(),
                                    Forms\Components\TextInput::make('lag_days')->label('Lag')
                                        ->numeric()->default(0)->suffix('days'),
                                ])
                                ->columns(3)
                                ->defaultItems(1)
                                ->addActionLabel('+ Add'),
                        ])
                        ->fillForm(fn(Task $record) => [
                            'predecessors' => $record->predecessorLinks->map(fn($d) => [
                                'depends_on_id' => $d->depends_on_id,
                                'dependency_type' => $d->dependency_type,
                                'lag_days' => $d->lag_days,
                            ])->toArray(),
                        ])
                        ->action(function (array $data, Task $record): void {
                            // Replace all dependencies
                            $record->predecessorLinks()->delete();
                            foreach ($data['predecessors'] ?? [] as $pred) {
                                if (!empty($pred['depends_on_id'])) {
                                    TaskDependency::create([
                                        'task_id' => $record->id,
                                        'depends_on_id' => $pred['depends_on_id'],
                                        'dependency_type' => $pred['dependency_type'] ?? 'finish_to_start',
                                        'lag_days' => $pred['lag_days'] ?? 0,
                                    ]);
                                }
                            }
                            Notification::make()->title('Dependencies updated')->success()->send();
                            $this->dispatch('gantt-refresh');
                        }),

                    \Filament\Actions\Action::make('indent')
                        ->label('Indent →')->icon('heroicon-o-arrow-right')->color('gray')
                        ->requiresConfirmation()
                        ->action(function (Task $record): void {
                            // Find the task above this one and make it the parent
                            $above = Task::where('cde_project_id', $this->pid())
                                ->where('sort_order', '<', $record->sort_order)
                                ->where('id', '!=', $record->id)
                                ->orderByDesc('sort_order')->first();
                            if ($above) {
                                $record->update(['parent_id' => $above->id]);
                                $above->update(['is_summary' => true]);
                                Task::regenerateWbs($this->pid());
                                Notification::make()->title('Task indented under ' . $above->title)->success()->send();
                                $this->dispatch('gantt-refresh');
                            }
                        }),

                    \Filament\Actions\Action::make('outdent')
                        ->label('← Outdent')->icon('heroicon-o-arrow-left')->color('gray')
                        ->visible(fn(Task $record) => $record->parent_id !== null)
                        ->action(function (Task $record): void {
                            $oldParentId = $record->parent_id;
                            $parent = Task::find($oldParentId);
                            $record->update(['parent_id' => $parent?->parent_id]);

                            // Check if old parent still has children
                            if ($oldParentId) {
                                $remaining = Task::where('parent_id', $oldParentId)->count();
                                if ($remaining === 0) {
                                    Task::where('id', $oldParentId)->update(['is_summary' => false]);
                                }
                            }

                            Task::regenerateWbs($this->pid());
                            Notification::make()->title('Task outdented')->success()->send();
                            $this->dispatch('gantt-refresh');
                        }),

                    \Filament\Actions\Action::make('quickComplete')
                        ->label('Complete ✓')->icon('heroicon-o-check-circle')->color('success')
                        ->visible(fn(Task $record) => !in_array($record->status, ['done', 'cancelled']))
                        ->requiresConfirmation()
                        ->action(function (Task $record): void {
                            $record->update([
                                'status' => 'done',
                                'progress_percent' => 100,
                                'completed_at' => now(),
                                'actual_finish' => now()->format('Y-m-d'),
                            ]);
                            if ($record->parent_id) {
                                Task::find($record->parent_id)?->rollUpFromChildren();
                            }
                            Notification::make()->title('Task completed ✓')->success()->send();
                            $this->dispatch('gantt-refresh');
                        }),

                    \Filament\Actions\Action::make('edit')
                        ->icon('heroicon-o-pencil')->modalWidth('4xl')
                        ->schema($this->taskFormSchema())
                        ->fillForm(fn(Task $record) => array_merge($record->toArray(), [
                            'predecessor_list' => $record->predecessorLinks->map(fn($d) => [
                                'depends_on_id' => $d->depends_on_id,
                                'dependency_type' => $d->dependency_type,
                                'lag_days' => $d->lag_days,
                            ])->toArray(),
                        ]))
                        ->action(function (array $data, Task $record): void {
                            $predecessors = $data['predecessor_list'] ?? [];
                            unset($data['predecessor_list']);

                            if ($data['status'] === 'done' && $record->status !== 'done') {
                                $data['completed_at'] = now();
                                $data['progress_percent'] = 100;
                                $data['actual_finish'] = $data['actual_finish'] ?? now()->format('Y-m-d');
                            }

                            // Auto-fill missing dates
                            $this->autoFillDates($data);

                            $record->update($data);

                            // Update dependencies
                            $record->predecessorLinks()->delete();
                            foreach ($predecessors as $pred) {
                                if (!empty($pred['depends_on_id'])) {
                                    TaskDependency::create([
                                        'task_id' => $record->id,
                                        'depends_on_id' => $pred['depends_on_id'],
                                        'dependency_type' => $pred['dependency_type'] ?? 'finish_to_start',
                                        'lag_days' => $pred['lag_days'] ?? 0,
                                    ]);
                                }
                            }

                            Task::regenerateWbs($this->pid());
                            if ($record->parent_id) {
                                Task::find($record->parent_id)?->rollUpFromChildren();
                            }
                            Notification::make()->title('Task updated')->success()->send();
                            $this->dispatch('gantt-refresh');
                        }),

                    \Filament\Actions\Action::make('duplicate')
                        ->icon('heroicon-o-document-duplicate')->color('gray')
                        ->requiresConfirmation()
                        ->action(function (Task $record): void {
                            $new = $record->replicate(['completed_at', 'actual_start', 'actual_finish', 'baseline_start', 'baseline_finish', 'baseline_duration', 'baseline_cost', 'baseline_work']);
                            $new->status = 'to_do';
                            $new->progress_percent = 0;
                            $new->actual_hours = null;
                            $new->created_by = auth()->id();
                            $new->save();
                            Task::regenerateWbs($this->pid());
                            Notification::make()->title('Task duplicated')->success()->send();
                            $this->dispatch('gantt-refresh');
                        }),

                    \Filament\Actions\Action::make('delete')
                        ->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                        ->action(function (Task $record): void {
                            $parentId = $record->parent_id;
                            $record->delete();
                            if ($parentId) {
                                $remaining = Task::where('parent_id', $parentId)->count();
                                if ($remaining === 0) {
                                    Task::where('id', $parentId)->update(['is_summary' => false]);
                                }
                                Task::find($parentId)?->rollUpFromChildren();
                            }
                            Task::regenerateWbs($this->pid());
                            $this->dispatch('gantt-refresh');
                        }),
                ]),
            ])
            ->toolbarActions([
                $this->exportCsvAction('tasks', fn() => Task::query()->where('cde_project_id', $this->pid())->with(['creator', 'assignee', 'parent']), [
                    'wbs_code' => 'WBS',
                    'title' => 'Task Name',
                    'type' => 'Type',
                    'priority' => 'Priority',
                    'status' => 'Status',
                    'progress_percent' => '% Complete',
                    'duration_days' => 'Duration',
                    'start_date' => 'Start',
                    'due_date' => 'Finish',
                    'estimated_hours' => 'Work (hrs)',
                    'actual_hours' => 'Actual (hrs)',
                    'fixed_cost' => 'Fixed Cost',
                    'assignee.name' => 'Resource',
                    'baseline_start' => 'Baseline Start',
                    'baseline_finish' => 'Baseline Finish',
                    'created_at' => 'Created',
                ]),
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('bulkStatus')->label('Update Status')->icon('heroicon-o-arrow-path')
                        ->schema([Forms\Components\Select::make('status')->options(Task::$statuses)->required()])
                        ->action(function (array $data, $records): void {
                            foreach ($records as $r) {
                                $u = ['status' => $data['status']];
                                if ($data['status'] === 'done') {
                                    $u['completed_at'] = now();
                                    $u['progress_percent'] = 100;
                                    $u['actual_finish'] = now()->format('Y-m-d');
                                }
                                $r->update($u);
                            }
                            Notification::make()->title($records->count() . ' tasks updated')->success()->send();
                        })->deselectRecordsAfterCompletion(),
                    \Filament\Actions\BulkAction::make('bulkPriority')->label('Set Priority')->icon('heroicon-o-flag')
                        ->schema([Forms\Components\Select::make('priority')->options(Task::$priorities)->required()])
                        ->action(function (array $data, $records): void {
                            foreach ($records as $r)
                                $r->update(['priority' => $data['priority']]);
                            Notification::make()->title($records->count() . ' tasks updated')->success()->send();
                        })->deselectRecordsAfterCompletion(),
                    \Filament\Actions\BulkAction::make('bulkSaveBaseline')->label('Save Baseline')->icon('heroicon-o-camera')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            foreach ($records as $r)
                                $r->saveBaseline();
                            Notification::make()->title('Baseline saved for ' . $records->count() . ' tasks')->success()->send();
                        })->deselectRecordsAfterCompletion(),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Tasks')
            ->emptyStateDescription('Create tasks, milestones, and phases to build your project schedule — just like MS Project.')
            ->emptyStateIcon('heroicon-o-clipboard-document-check')
            ->striped()
            ->paginated([25, 50, 100]);
    }

    // ─── Work Orders Data ─────────────────────────────────────────

    public function getWorkOrdersData(): array
    {
        $pid = $this->pid();

        $workOrders = WorkOrder::where('cde_project_id', $pid)
            ->with(['assignee:id,name', 'client:id,name', 'type:id,name', 'items'])
            ->orderByRaw("FIELD(status, 'in_progress', 'approved', 'pending', 'on_hold', 'completed', 'cancelled')")
            ->get();

        return $workOrders->map(fn(WorkOrder $wo) => [
            'id' => $wo->id,
            'wo_number' => $wo->wo_number,
            'title' => $wo->title,
            'description' => $wo->description,
            'status' => $wo->status,
            'priority' => $wo->priority,
            'assignee' => $wo->assignee?->name,
            'client' => $wo->client?->name,
            'type' => $wo->type?->name,
            'due_date' => $wo->due_date?->format('Y-m-d'),
            'started_at' => $wo->started_at?->format('Y-m-d'),
            'completed_at' => $wo->completed_at?->format('Y-m-d'),
            'items_count' => $wo->items->count(),
            'items_cost' => $wo->items->sum('amount'),
            'tasks_count' => $wo->tasks->count(),
            'notes' => $wo->notes,
            'is_overdue' => $wo->due_date && $wo->due_date->isPast() && !in_array($wo->status, ['completed', 'cancelled']),
            'days_until_due' => $wo->due_date ? (int) now()->diffInDays($wo->due_date, false) : null,
        ])->toArray();
    }

    // ─── Milestones Data ──────────────────────────────────────────

    public function getMilestonesData(): array
    {
        $pid = $this->pid();

        $milestones = Milestone::where('cde_project_id', $pid)
            ->orderBy('target_date')
            ->get();

        return $milestones->map(fn(Milestone $m) => [
            'id' => $m->id,
            'name' => $m->name,
            'status' => $m->status,
            'priority' => $m->priority,
            'target_date' => $m->target_date?->format('Y-m-d'),
            'actual_date' => $m->actual_date?->format('Y-m-d'),
            'description' => $m->description,
            'is_overdue' => $m->isOverdue(),
            'days_remaining' => $m->target_date ? (int) now()->diffInDays($m->target_date, false) : null,
        ])->toArray();
    }

    // ─── Combined Stats (all 3 merged modules) ───────────────────

    public function getScheduleStats(): array
    {
        $pid = $this->pid();

        $stats = \DB::selectOne("
            SELECT
                (SELECT COUNT(*) FROM tasks WHERE cde_project_id = ? AND deleted_at IS NULL) as tasks_total,
                (SELECT COUNT(*) FROM tasks WHERE cde_project_id = ? AND deleted_at IS NULL AND status NOT IN ('done','cancelled')) as tasks_open,
                (SELECT ROUND(COALESCE(AVG(progress_percent), 0)) FROM tasks WHERE cde_project_id = ? AND deleted_at IS NULL) as tasks_progress,
                (SELECT COUNT(*) FROM work_orders WHERE cde_project_id = ? AND deleted_at IS NULL) as wo_total,
                (SELECT COUNT(*) FROM work_orders WHERE cde_project_id = ? AND deleted_at IS NULL AND status NOT IN ('completed','cancelled')) as wo_open,
                (SELECT COUNT(*) FROM milestones WHERE cde_project_id = ?) as ms_total,
                (SELECT COUNT(*) FROM milestones WHERE cde_project_id = ? AND status = 'completed') as ms_completed,
                (SELECT COUNT(*) FROM milestones WHERE cde_project_id = ? AND status NOT IN ('completed','cancelled') AND target_date < NOW()) as ms_overdue
        ", [$pid, $pid, $pid, $pid, $pid, $pid, $pid, $pid]);

        return [
            'tasks_total' => (int) $stats->tasks_total,
            'tasks_open' => (int) $stats->tasks_open,
            'tasks_progress' => (int) $stats->tasks_progress,
            'wo_total' => (int) $stats->wo_total,
            'wo_open' => (int) $stats->wo_open,
            'ms_total' => (int) $stats->ms_total,
            'ms_completed' => (int) $stats->ms_completed,
            'ms_overdue' => (int) $stats->ms_overdue,
        ];
    }

    // ─── Tab switching (called from Livewire) ────────────────────

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // ─── Auto-fill missing dates helper ──────────────────────────

    private function autoFillDates(array &$data): void
    {
        $start = $data['start_date'] ?? null;
        $due = $data['due_date'] ?? null;
        $duration = (int) ($data['duration_days'] ?? 0);

        if ($start && $due && !$duration) {
            // Both dates set but no duration — calculate it
            $task = new Task($data);
            $data['duration_days'] = $task->calculateDuration();
        } elseif ($start && !$due && $duration > 0) {
            // Start + duration but no due date — compute finish
            $data['due_date'] = Task::calculateFinishDate($start, $duration)->format('Y-m-d');
        } elseif (!$start && $due && $duration > 0) {
            // Due date + duration but no start — compute start (subtract working days)
            $date = \Carbon\Carbon::parse($due);
            $remaining = max(1, $duration) - 1;
            while ($remaining > 0) {
                $date->subDay();
                if (!$date->isWeekend()) {
                    $remaining--;
                }
            }
            $data['start_date'] = $date->format('Y-m-d');
        } elseif (!$start && $due) {
            // Only due date — default to 5 working days before
            $date = \Carbon\Carbon::parse($due);
            $remaining = 4;
            while ($remaining > 0) {
                $date->subDay();
                if (!$date->isWeekend()) {
                    $remaining--;
                }
            }
            $data['start_date'] = $date->format('Y-m-d');
            $data['duration_days'] = $data['duration_days'] ?: 5;
        } elseif ($start && !$due) {
            // Only start date — default 5 working days ahead
            $data['due_date'] = Task::calculateFinishDate($start, $duration ?: 5)->format('Y-m-d');
            $data['duration_days'] = $data['duration_days'] ?: 5;
        } elseif (!$start && !$due) {
            // No dates at all — default to today + 5 days
            $data['start_date'] = now()->format('Y-m-d');
            $data['due_date'] = Task::calculateFinishDate(now(), $duration ?: 5)->format('Y-m-d');
            $data['duration_days'] = $data['duration_days'] ?: 5;
        }
    }
}
