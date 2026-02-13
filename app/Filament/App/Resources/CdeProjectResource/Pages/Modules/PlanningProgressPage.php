<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource;
use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\Milestone;
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

class PlanningProgressPage extends BaseModulePage implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static string $moduleCode = 'planning_progress';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Planning';
    protected static ?string $title = 'Planning & Progress Tracking';
    protected string $view = 'filament.app.pages.modules.planning-progress';

    public function getStats(): array
    {
        $pid = $this->record->id;
        $total = Milestone::where('cde_project_id', $pid)->count();
        $completed = Milestone::where('cde_project_id', $pid)->where('status', 'completed')->count();
        $delayed = Milestone::where('cde_project_id', $pid)->where('status', 'delayed')->count();
        $overdue = Milestone::where('cde_project_id', $pid)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->whereNotNull('target_date')
            ->where('target_date', '<', now())->count();
        $progress = $total > 0 ? round(($completed / $total) * 100) : 0;

        return [
            [
                'label' => 'Overall Progress',
                'value' => $progress . '%',
                'sub' => $completed . '/' . $total . ' milestones',
                'sub_type' => $progress >= 70 ? 'success' : ($progress >= 40 ? 'warning' : 'neutral'),
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>'
            ],
            [
                'label' => 'Delayed',
                'value' => $delayed,
                'sub' => $overdue . ' overdue',
                'sub_type' => $delayed > 0 ? 'danger' : 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ef4444" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>',
                'icon_bg' => '#fef2f2'
            ],
            [
                'label' => 'Completed',
                'value' => $completed,
                'sub' => 'On target',
                'sub_type' => 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#ecfdf5'
            ],
        ];
    }

    protected function getHeaderActions(): array
    {
        $companyId = $this->record->company_id;
        $projectId = $this->record->id;

        return [
            Action::make('createMilestone')
                ->label('New Milestone')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->modalWidth('2xl')
                ->schema([
                    Section::make('Milestone Details')->schema([
                        Forms\Components\TextInput::make('name')->required()->maxLength(255)->columnSpanFull(),
                        Forms\Components\Select::make('status')->options(Milestone::$statuses)->required()->default('pending'),
                        Forms\Components\Select::make('priority')->options(Milestone::$priorities)->required()->default('medium'),
                        Forms\Components\DatePicker::make('target_date')->label('Target Date')->required(),
                        Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                    ])->columns(2),
                ])
                ->action(function (array $data) use ($companyId, $projectId): void {
                    $data['company_id'] = $companyId;
                    $data['cde_project_id'] = $projectId;
                    Milestone::create($data);
                    Notification::make()->title('Milestone created')->success()->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        $projectId = $this->record->id;

        return $table
            ->query(Milestone::query()->where('cde_project_id', $projectId))
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->limit(50)->weight('bold')
                    ->icon('heroicon-o-flag'),
                Tables\Columns\TextColumn::make('priority')->badge()
                    ->color(fn(string $state) => match ($state) { 'critical' => 'danger', 'high' => 'warning', 'medium' => 'info', default => 'gray'}),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) { 'completed' => 'success', 'in_progress' => 'info', 'delayed' => 'danger', 'cancelled' => 'danger', default => 'gray'}),
                Tables\Columns\TextColumn::make('target_date')->date()->sortable()->label('Target')
                    ->color(fn($record) => $record->isOverdue() ? 'danger' : null),
                Tables\Columns\TextColumn::make('actual_date')->date()->label('Actual')->placeholder('—'),
                Tables\Columns\TextColumn::make('description')->limit(40)->toggleable(),
            ])
            ->defaultSort('target_date', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Milestone::$statuses),
                Tables\Filters\SelectFilter::make('priority')->options(Milestone::$priorities),
            ])
            ->actions([
                \Filament\Actions\Action::make('markComplete')
                    ->label('Complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Milestone $record) => !in_array($record->status, ['completed', 'cancelled']))
                    ->requiresConfirmation()
                    ->schema([
                        Forms\Components\DatePicker::make('actual_date')->label('Actual Completion Date')->required()->default(now()),
                    ])
                    ->action(function (array $data, Milestone $record): void {
                        $record->update(['status' => 'completed', 'actual_date' => $data['actual_date']]);
                        Notification::make()->title('Milestone completed!')->success()->send();
                    }),

                \Filament\Actions\Action::make('markDelayed')
                    ->label('Delay')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->visible(fn(Milestone $record) => !in_array($record->status, ['completed', 'cancelled', 'delayed']))
                    ->schema([
                        Forms\Components\DatePicker::make('target_date')->label('New Target Date')->required(),
                        Forms\Components\Textarea::make('description')->label('Reason for Delay')->rows(2),
                    ])
                    ->fillForm(fn(Milestone $record) => ['target_date' => $record->target_date])
                    ->action(function (array $data, Milestone $record): void {
                        $desc = $record->description ? $record->description . "\n[Delayed " . now()->format('M d') . '] ' . ($data['description'] ?? '') : ($data['description'] ?? '');
                        $record->update(['status' => 'delayed', 'target_date' => $data['target_date'], 'description' => $desc]);
                        Notification::make()->title('Milestone marked as delayed')->warning()->send();
                    }),

                \Filament\Actions\Action::make('edit')
                    ->icon('heroicon-o-pencil')
                    ->schema([
                        Forms\Components\TextInput::make('name')->required()->columnSpanFull(),
                        Forms\Components\Select::make('status')->options(Milestone::$statuses)->required(),
                        Forms\Components\Select::make('priority')->options(Milestone::$priorities)->required(),
                        Forms\Components\DatePicker::make('target_date')->required(),
                        Forms\Components\DatePicker::make('actual_date'),
                        Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                    ])
                    ->fillForm(fn(Milestone $record) => $record->toArray())
                    ->action(function (array $data, Milestone $record): void {
                        $record->update($data);
                        Notification::make()->title('Milestone updated')->success()->send();
                    }),

                \Filament\Actions\Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn(Milestone $record) => $record->delete()),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Milestones')
            ->emptyStateDescription('Create milestones to track project progress.')
            ->emptyStateIcon('heroicon-o-calendar-days');
    }
}
