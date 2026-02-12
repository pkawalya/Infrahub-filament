<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\Milestone;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class PlanningProgressPage extends BaseModulePage implements HasTable
{
    use InteractsWithTable;

    protected static string $moduleCode = 'planning_progress';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Planning';
    protected static ?string $title = 'Planning & Progress';
    protected string $view = 'filament.app.pages.modules.planning-progress';

    public function getStats(): array
    {
        $r = $this->record;
        $totalTasks = $r->tasks()->count();
        $done = $r->tasks()->where('status', 'done')->count();
        $progress = $totalTasks > 0 ? round(($done / $totalTasks) * 100) : 0;
        $totalMilestones = $r->milestones()->count();
        $completedMilestones = $r->milestones()->where('status', 'completed')->count();
        $overdueMilestones = $r->milestones()
            ->where('status', '!=', 'completed')
            ->where('target_date', '<', now())
            ->count();

        return [
            [
                'label' => 'Overall Progress',
                'value' => $progress . '%',
                'sub' => $done . '/' . $totalTasks . ' tasks done',
                'sub_type' => $progress >= 75 ? 'success' : ($progress >= 40 ? 'warning' : 'neutral'),
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>'
            ],
            [
                'label' => 'Milestones',
                'value' => $totalMilestones,
                'sub' => $completedMilestones . ' completed',
                'sub_type' => 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2563eb" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5" /></svg>',
                'icon_bg' => '#eff6ff'
            ],
            [
                'label' => 'Overdue',
                'value' => $overdueMilestones,
                'sub' => $overdueMilestones > 0 ? 'Need attention' : 'On track',
                'sub_type' => $overdueMilestones > 0 ? 'danger' : 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="' . ($overdueMilestones > 0 ? '#dc2626' : '#059669') . '" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => $overdueMilestones > 0 ? '#fef2f2' : '#ecfdf5'
            ],
            [
                'label' => 'Schedule Health',
                'value' => $overdueMilestones === 0 ? 'On Track' : ($overdueMilestones <= 2 ? 'At Risk' : 'Delayed'),
                'sub' => $overdueMilestones === 0 ? 'All milestones on time' : $overdueMilestones . ' milestone(s) overdue',
                'sub_type' => $overdueMilestones === 0 ? 'success' : 'danger',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="' . ($overdueMilestones === 0 ? '#059669' : '#dc2626') . '" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => $overdueMilestones === 0 ? '#ecfdf5' : '#fef2f2'
            ],
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Milestone::query()->where('cde_project_id', $this->record->id))
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->limit(50),
                Tables\Columns\TextColumn::make('priority')->badge()
                    ->color(fn(string $state) => match ($state) {
                        'critical' => 'danger',
                        'high' => 'warning',
                        'medium' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) {
                        'completed' => 'success',
                        'in_progress' => 'info',
                        'delayed' => 'danger',
                        'cancelled' => 'gray',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('target_date')->date()->sortable()
                    ->color(fn($record) => $record->status !== 'completed' && $record->target_date?->isPast() ? 'danger' : null),
                Tables\Columns\TextColumn::make('actual_date')->date()->placeholder('—'),
                Tables\Columns\TextColumn::make('description')->limit(50)->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('target_date', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Milestone::$statuses),
                Tables\Filters\SelectFilter::make('priority')->options(Milestone::$priorities),
            ])
            ->emptyStateHeading('No Milestones')
            ->emptyStateDescription('No milestones have been created for this project yet.')
            ->emptyStateIcon('heroicon-o-flag');
    }
}
