<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource;
use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkOrder;
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

class TaskWorkflowPage extends BaseModulePage implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static string $moduleCode = 'task_workflow';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Tasks';
    protected static ?string $title = 'Task & Workflow Management';
    protected string $view = 'filament.app.pages.modules.task-workflow';

    public function getStats(): array
    {
        $pid = $this->record->id;
        $total = Task::where('cde_project_id', $pid)->count();
        $todo = Task::where('cde_project_id', $pid)->where('status', 'to_do')->count();
        $inProgress = Task::where('cde_project_id', $pid)->where('status', 'in_progress')->count();
        $done = Task::where('cde_project_id', $pid)->where('status', 'done')->count();
        $blocked = Task::where('cde_project_id', $pid)->where('status', 'blocked')->count();
        $avgProgress = $total > 0 ? round(Task::where('cde_project_id', $pid)->avg('progress_percent') ?? 0) : 0;

        return [
            [
                'label' => 'Total Tasks',
                'value' => $total,
                'sub' => $done . ' completed',
                'sub_type' => 'success',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" /></svg>'
            ],
            [
                'label' => 'To Do',
                'value' => $todo,
                'sub' => 'Pending start',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#6b7280" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#f9fafb'
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
                'label' => 'Avg. Progress',
                'value' => $avgProgress . '%',
                'sub' => 'Project-wide',
                'sub_type' => $avgProgress >= 70 ? 'success' : ($avgProgress >= 40 ? 'warning' : 'neutral'),
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>',
                'icon_bg' => '#ecfdf5'
            ],
        ];
    }

    protected function getHeaderActions(): array
    {
        $companyId = $this->record->company_id;
        $projectId = $this->record->id;

        return [
            Action::make('createTask')
                ->label('New Task')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->modalWidth('3xl')
                ->schema([
                    Section::make('Task Details')->schema([
                        Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                        Forms\Components\Select::make('assigned_to')->label('Assign To')
                            ->options(User::where('company_id', $companyId)->where('is_active', true)->pluck('name', 'id'))->searchable(),
                        Forms\Components\Select::make('work_order_id')->label('Link to Work Order')
                            ->options(WorkOrder::where('cde_project_id', $projectId)->pluck('title', 'id'))->searchable()->nullable(),
                        Forms\Components\Select::make('parent_id')->label('Parent Task')
                            ->options(Task::where('cde_project_id', $projectId)->pluck('title', 'id'))->searchable()->nullable(),
                        Forms\Components\Select::make('priority')->options([
                            'low' => 'Low',
                            'medium' => 'Medium',
                            'high' => 'High',
                            'urgent' => 'Urgent',
                        ])->required()->default('medium'),
                        Forms\Components\Select::make('status')->options(Task::$statuses)->required()->default('to_do'),
                        Forms\Components\DatePicker::make('due_date'),
                        Forms\Components\TextInput::make('estimated_hours')->numeric()->suffix('hrs'),
                    ])->columns(2),
                    Section::make('Description')->schema([
                        Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                    ])->collapsed(),
                ])
                ->action(function (array $data) use ($companyId, $projectId): void {
                    $data['company_id'] = $companyId;
                    $data['cde_project_id'] = $projectId;
                    $data['created_by'] = auth()->id();
                    Task::create($data);
                    Notification::make()->title('Task created successfully')->success()->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        $projectId = $this->record->id;

        return $table
            ->query(
                Task::query()
                    ->where('cde_project_id', $projectId)
                    ->with(['assignee', 'workOrder', 'parent'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50)
                    ->description(fn(Task $record) => $record->parent ? '↳ Sub-task of: ' . $record->parent->title : null),
                Tables\Columns\TextColumn::make('assignee.name')->label('Assigned To')->placeholder('Unassigned'),
                Tables\Columns\TextColumn::make('priority')->badge()
                    ->color(fn(string $state) => match ($state) { 'urgent' => 'danger', 'high' => 'warning', 'medium' => 'info', default => 'gray'}),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) { 'done' => 'success', 'in_progress' => 'info', 'review' => 'primary', 'blocked' => 'danger', default => 'gray'}),
                Tables\Columns\TextColumn::make('progress_percent')->label('Progress')->suffix('%')->sortable(),
                Tables\Columns\TextColumn::make('estimated_hours')->label('Est. Hrs')->placeholder('-'),
                Tables\Columns\TextColumn::make('actual_hours')->label('Actual Hrs')->placeholder('-'),
                Tables\Columns\TextColumn::make('due_date')->date()->sortable()
                    ->color(fn($record) => $record->due_date?->isPast() && !in_array($record->status, ['done']) ? 'danger' : null),
                Tables\Columns\TextColumn::make('workOrder.wo_number')->label('WO #')->placeholder('-')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Task::$statuses),
                Tables\Filters\SelectFilter::make('priority')->options([
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                    'urgent' => 'Urgent',
                ]),
                Tables\Filters\SelectFilter::make('assigned_to')->label('Assigned To')
                    ->options(fn() => User::where('company_id', $this->record->company_id)->where('is_active', true)->pluck('name', 'id')),
            ])
            ->actions([
                \Filament\Actions\Action::make('updateProgress')
                    ->label('Progress')
                    ->icon('heroicon-o-chart-bar')
                    ->color('info')
                    ->schema([
                        Forms\Components\Select::make('status')->options(Task::$statuses)->required(),
                        Forms\Components\TextInput::make('progress_percent')->label('Progress (%)')->numeric()->minValue(0)->maxValue(100)->required()->suffix('%'),
                        Forms\Components\TextInput::make('actual_hours')->label('Hours Worked')->numeric()->suffix('hrs'),
                    ])
                    ->fillForm(fn(Task $record) => ['status' => $record->status, 'progress_percent' => $record->progress_percent, 'actual_hours' => $record->actual_hours])
                    ->action(function (array $data, Task $record): void {
                        $record->update($data);
                        Notification::make()->title('Task progress updated')->success()->send();
                    }),

                \Filament\Actions\Action::make('edit')
                    ->icon('heroicon-o-pencil')
                    ->modalWidth('3xl')
                    ->schema([
                        Section::make('Task Details')->schema([
                            Forms\Components\TextInput::make('title')->required()->columnSpanFull(),
                            Forms\Components\Select::make('assigned_to')->label('Assign To')
                                ->options(fn() => User::where('company_id', $this->record->company_id)->where('is_active', true)->pluck('name', 'id'))->searchable(),
                            Forms\Components\Select::make('priority')->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'])->required(),
                            Forms\Components\Select::make('status')->options(Task::$statuses)->required(),
                            Forms\Components\DatePicker::make('due_date'),
                            Forms\Components\TextInput::make('estimated_hours')->numeric()->suffix('hrs'),
                            Forms\Components\TextInput::make('actual_hours')->numeric()->suffix('hrs'),
                            Forms\Components\TextInput::make('progress_percent')->numeric()->suffix('%'),
                        ])->columns(2),
                        Section::make('Description')->schema([
                            Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                        ])->collapsed(),
                    ])
                    ->fillForm(fn(Task $record) => $record->toArray())
                    ->action(function (array $data, Task $record): void {
                        $record->update($data);
                        Notification::make()->title('Task updated')->success()->send();
                    }),

                \Filament\Actions\Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn(Task $record) => $record->delete()),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Tasks')
            ->emptyStateDescription('Create tasks to track project work.')
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }
}
