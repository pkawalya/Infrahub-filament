<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\Task;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class TaskWorkflowPage extends BaseModulePage implements HasTable
{
    use InteractsWithTable;

    protected static string $moduleCode = 'task_workflow';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Tasks';
    protected static ?string $title = 'Task & Workflow';
    protected string $view = 'filament.app.pages.modules.task-workflow';

    public function getStats(): array
    {
        $r = $this->record;
        $total = $r->tasks()->count();
        $open = $r->tasks()->whereNotIn('status', ['done', 'cancelled'])->count();
        $overdue = $r->tasks()->where('due_date', '<', now())->whereNotIn('status', ['done', 'cancelled'])->count();
        $done = $total - $open;

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
                'label' => 'In Progress',
                'value' => $r->tasks()->where('status', 'in_progress')->count(),
                'sub' => 'Active now',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2563eb" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" /></svg>',
                'icon_bg' => '#eff6ff'
            ],
            [
                'label' => 'Overdue',
                'value' => $overdue,
                'sub' => $overdue > 0 ? 'Need attention' : 'None',
                'sub_type' => $overdue > 0 ? 'danger' : 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#dc2626" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#fef2f2'
            ],
        ];
    }

    protected function getTaskFormSchema(): array
    {
        $companyId = $this->record->company_id;
        return [
            Section::make('Task Details')->schema([
                Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\Select::make('priority')->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'])->required()->default('medium'),
                Forms\Components\Select::make('status')->options(Task::$statuses)->required()->default('to_do'),
                Forms\Components\Select::make('assigned_to')->label('Assign To')
                    ->options(User::where('company_id', $companyId)->where('is_active', true)->pluck('name', 'id'))->searchable(),
                Forms\Components\DatePicker::make('due_date'),
                Forms\Components\TextInput::make('estimated_hours')->numeric()->suffix('hrs'),
                Forms\Components\TextInput::make('actual_hours')->numeric()->suffix('hrs'),
                Forms\Components\TextInput::make('progress_percent')->numeric()->suffix('%')->default(0)->minValue(0)->maxValue(100)->label('Progress'),
                Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
            ])->columns(2),
        ];
    }

    public function table(Table $table): Table
    {
        $projectId = $this->record->id;
        $companyId = $this->record->company_id;

        return $table
            ->query(Task::query()->where('cde_project_id', $projectId))
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('priority')->badge()->color(fn(string $state) => match ($state) { 'urgent' => 'danger', 'high' => 'warning', 'medium' => 'info', default => 'gray'}),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn(string $state) => match ($state) { 'done' => 'success', 'in_progress' => 'info', 'review' => 'warning', 'blocked' => 'danger', default => 'gray'}),
                Tables\Columns\TextColumn::make('assignee.name')->label('Assigned To')->placeholder('Unassigned'),
                Tables\Columns\TextColumn::make('due_date')->date()->color(fn($record) => $record->due_date?->isPast() && !in_array($record->status, ['done', 'cancelled']) ? 'danger' : null),
                Tables\Columns\TextColumn::make('progress_percent')->label('Progress')->suffix('%'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Task::$statuses),
                Tables\Filters\SelectFilter::make('priority')->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent']),
            ])
            ->headerActions([
                Action::make('create')->label('New Task')->icon('heroicon-o-plus')
                    ->schema($this->getTaskFormSchema())
                    ->action(function (array $data) use ($projectId, $companyId): void {
                        $data['cde_project_id'] = $projectId;
                        $data['company_id'] = $companyId;
                        $data['created_by'] = auth()->id();
                        Task::create($data);
                        Notification::make()->title('Task created')->success()->send();
                    }),
            ])
            ->recordActions([
                Action::make('view')->icon('heroicon-o-eye')->color('gray')
                    ->schema($this->getTaskFormSchema())
                    ->fillForm(fn(Task $record) => $record->toArray())
                    ->modalSubmitAction(false),
                Action::make('edit')->icon('heroicon-o-pencil')
                    ->schema($this->getTaskFormSchema())
                    ->fillForm(fn(Task $record) => $record->toArray())
                    ->action(function (array $data, Task $record): void {
                        $record->update($data);
                        Notification::make()->title('Task updated')->success()->send();
                    }),
                Action::make('mark_done')->icon('heroicon-o-check-circle')->color('success')->label('Done')->requiresConfirmation()
                    ->visible(fn(Task $record) => !in_array($record->status, ['done', 'cancelled']))
                    ->action(fn(Task $record) => $record->update(['status' => 'done', 'progress_percent' => 100])),
                Action::make('delete')->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                    ->action(fn(Task $record) => $record->delete()),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->emptyStateHeading('No Tasks')
            ->emptyStateDescription('No tasks have been created for this project yet.')
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }
}
