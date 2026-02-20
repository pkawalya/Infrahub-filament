<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\Milestone;
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

class PlanningProgressPage extends BaseModulePage implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static string $moduleCode = 'planning_progress';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Planning';
    protected static ?string $title = 'Planning & Progress — Milestones';
    protected string $view = 'filament.app.pages.modules.planning-progress';

    private function pid(): int
    {
        return $this->record->id;
    }
    private function cid(): int
    {
        return $this->record->company_id;
    }

    public function getStats(): array
    {
        $pid = $this->pid();
        $base = Milestone::where('cde_project_id', $pid);
        $total = (clone $base)->count();
        $completed = (clone $base)->where('status', 'completed')->count();
        $delayed = (clone $base)->where('status', 'delayed')->count();
        $upcoming = (clone $base)->whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('target_date')->whereBetween('target_date', [now(), now()->addDays(30)])->count();

        return [
            [
                'label' => 'Total Milestones',
                'value' => $total,
                'sub' => $completed . ' completed',
                'sub_type' => 'success',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5" /></svg>'
            ],
            [
                'label' => 'Completed',
                'value' => $total > 0 ? round(($completed / $total) * 100) . '%' : '0%',
                'sub' => $completed . ' of ' . $total,
                'sub_type' => 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#ecfdf5'
            ],
            [
                'label' => 'Delayed',
                'value' => $delayed,
                'sub' => $delayed > 0 ? 'Behind schedule' : 'On track',
                'sub_type' => $delayed > 0 ? 'danger' : 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ef4444" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>',
                'icon_bg' => '#fef2f2'
            ],
            [
                'label' => 'Upcoming (30d)',
                'value' => $upcoming,
                'sub' => 'Next 30 days',
                'sub_type' => 'info',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#3b82f6" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" /></svg>',
                'icon_bg' => '#eff6ff'
            ],
        ];
    }

    private function milestoneFormSchema(bool $isCreate = false): array
    {
        return [
            Section::make('Milestone Details')->schema([
                Forms\Components\TextInput::make('name')->label('Milestone Name')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\Select::make('priority')->options(Milestone::$priorities)->required()->default('medium'),
                Forms\Components\Select::make('status')->options(Milestone::$statuses)->required()->default($isCreate ? 'pending' : null),
                Forms\Components\DatePicker::make('target_date')->label('Target Date')->required(),
                Forms\Components\DatePicker::make('actual_date')->label('Actual Date')->visible(!$isCreate),
            ])->columns(2),
            Section::make('Description')->schema([
                Forms\Components\RichEditor::make('description')
                    ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link'])->columnSpanFull(),
            ])->collapsed(!$isCreate),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createMilestone')
                ->label('New Milestone')->icon('heroicon-o-plus-circle')->color('primary')
                ->modalWidth('3xl')
                ->schema($this->milestoneFormSchema(isCreate: true))
                ->action(function (array $data): void {
                    $data['company_id'] = $this->cid();
                    $data['cde_project_id'] = $this->pid();
                    Milestone::create($data);
                    Notification::make()->title('Milestone created')->success()->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Milestone::query()->where('cde_project_id', $this->pid()))
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->weight('bold')->limit(50),
                Tables\Columns\TextColumn::make('priority')->badge()
                    ->color(fn(string $state) => match ($state) { 'critical' => 'danger', 'high' => 'warning', 'medium' => 'info', default => 'gray'})->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) { 'completed' => 'success', 'in_progress' => 'info', 'delayed' => 'danger', 'pending' => 'gray', 'cancelled' => 'gray', default => 'gray'})->sortable(),
                Tables\Columns\TextColumn::make('target_date')->label('Target')->date()->sortable()
                    ->color(fn($record) => $record->target_date?->isPast() && !in_array($record->status, ['completed', 'cancelled']) ? 'danger' : null)
                    ->description(fn($record) => $record->target_date?->isPast() && !in_array($record->status, ['completed', 'cancelled']) ? 'Overdue' : ($record->target_date?->diffForHumans() ?? '')),
                Tables\Columns\TextColumn::make('actual_date')->label('Actual')->date()->placeholder('—')->sortable(),
                Tables\Columns\TextColumn::make('variance')->label('Variance')
                    ->state(function (Milestone $record) {
                        if (!$record->target_date)
                            return '—';
                        $compare = $record->actual_date ?? now();
                        $diff = $record->target_date->diffInDays($compare, false);
                        if ($record->status === 'completed' && $record->actual_date) {
                            return $diff > 0 ? $diff . 'd late' : abs($diff) . 'd early';
                        }
                        if (in_array($record->status, ['completed', 'cancelled']))
                            return '—';
                        return $record->target_date->isPast() ? abs($diff) . 'd overdue' : $diff . 'd remaining';
                    })
                    ->color(function (Milestone $record) {
                        if (!$record->target_date || in_array($record->status, ['completed', 'cancelled']))
                            return null;
                        return $record->target_date->isPast() ? 'danger' : 'success';
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M d, Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('target_date', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Milestone::$statuses)->multiple(),
                Tables\Filters\SelectFilter::make('priority')->options(Milestone::$priorities)->multiple(),
                Tables\Filters\Filter::make('overdue')->label('Overdue Only')
                    ->query(fn($q) => $q->whereNotIn('status', ['completed', 'cancelled'])->whereNotNull('target_date')->where('target_date', '<', now()))->toggle(),
                Tables\Filters\Filter::make('upcoming_30')->label('Next 30 Days')
                    ->query(fn($q) => $q->whereNotIn('status', ['completed', 'cancelled'])->whereBetween('target_date', [now(), now()->addDays(30)]))->toggle(),
            ])
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\Action::make('viewDetail')
                        ->label('View')->icon('heroicon-o-eye')->color('gray')
                        ->modalWidth('lg')
                        ->modalHeading(fn(Milestone $record) => $record->name)
                        ->schema(fn(Milestone $record) => [
                            Forms\Components\Placeholder::make('status_display')->label('Status')
                                ->content(Milestone::$statuses[$record->status] ?? $record->status),
                            Forms\Components\Placeholder::make('priority_display')->label('Priority')
                                ->content(Milestone::$priorities[$record->priority] ?? $record->priority),
                            Forms\Components\Placeholder::make('target')->label('Target Date')
                                ->content($record->target_date?->format('M d, Y') ?? '—'),
                            Forms\Components\Placeholder::make('actual')->label('Actual Date')
                                ->content($record->actual_date?->format('M d, Y') ?? '—'),
                            Forms\Components\Placeholder::make('variance_info')->label('Variance')
                                ->content(function () use ($record) {
                                    if (!$record->target_date)
                                        return '—';
                                    $compare = $record->actual_date ?? now();
                                    $diff = $record->target_date->diffInDays($compare, false);
                                    if ($record->status === 'completed' && $record->actual_date) {
                                        return $diff > 0 ? $diff . ' days late' : abs($diff) . ' days early';
                                    }
                                    return $record->target_date->isPast() ? abs($diff) . ' days overdue' : $diff . ' days remaining';
                                })
                                ->columnSpanFull(),
                            Forms\Components\Placeholder::make('desc')->label('Description')
                                ->content(fn() => new \Illuminate\Support\HtmlString($record->description ?: '<em>No description</em>'))
                                ->columnSpanFull(),
                        ])
                        ->modalSubmitAction(false)->modalCancelActionLabel('Close'),

                    \Filament\Actions\Action::make('complete')
                        ->icon('heroicon-o-check-circle')->color('success')->label('Complete')
                        ->visible(fn(Milestone $record) => !in_array($record->status, ['completed', 'cancelled']))
                        ->schema([
                            Forms\Components\DatePicker::make('actual_date')->label('Completion Date')->default(now())->required(),
                        ])
                        ->action(function (array $data, Milestone $record): void {
                            $record->update(['status' => 'completed', 'actual_date' => $data['actual_date']]);
                            Notification::make()->title('Milestone completed!')->success()->send();
                        }),

                    \Filament\Actions\Action::make('reschedule')
                        ->label('Reschedule')->icon('heroicon-o-calendar')->color('warning')
                        ->visible(fn(Milestone $record) => !in_array($record->status, ['completed', 'cancelled']))
                        ->schema([
                            Forms\Components\DatePicker::make('new_target_date')->label('New Target Date')->required(),
                            Forms\Components\Textarea::make('reason')->label('Reason for Reschedule')->rows(2)->required(),
                        ])
                        ->fillForm(fn(Milestone $record) => ['new_target_date' => $record->target_date])
                        ->action(function (array $data, Milestone $record): void {
                            $oldDate = $record->target_date?->format('M d, Y') ?? '—';
                            $desc = $record->description ? $record->description . "\n" : '';
                            $desc .= '[Rescheduled ' . now()->format('M d') . ': ' . $oldDate . ' → ' . \Carbon\Carbon::parse($data['new_target_date'])->format('M d, Y') . '] ' . $data['reason'];
                            $record->update(['target_date' => $data['new_target_date'], 'description' => $desc]);
                            Notification::make()->title('Milestone rescheduled')->success()->send();
                        }),

                    \Filament\Actions\Action::make('updateStatus')
                        ->label('Status')->icon('heroicon-o-arrow-path')->color('warning')
                        ->schema([Forms\Components\Select::make('status')->options(Milestone::$statuses)->required()])
                        ->fillForm(fn(Milestone $record) => ['status' => $record->status])
                        ->action(function (array $data, Milestone $record): void {
                            $record->update($data);
                            Notification::make()->title('Status → ' . Milestone::$statuses[$data['status']])->success()->send();
                        }),

                    \Filament\Actions\Action::make('edit')
                        ->icon('heroicon-o-pencil')->modalWidth('3xl')
                        ->schema($this->milestoneFormSchema())
                        ->fillForm(fn(Milestone $record) => $record->toArray())
                        ->action(function (array $data, Milestone $record): void {
                            $record->update($data);
                            Notification::make()->title('Milestone updated')->success()->send();
                        }),

                    \Filament\Actions\Action::make('duplicate')
                        ->icon('heroicon-o-document-duplicate')->color('gray')
                        ->requiresConfirmation()
                        ->action(function (Milestone $record): void {
                            $new = $record->replicate(['actual_date']);
                            $new->status = 'pending';
                            $new->name = $record->name . ' (Copy)';
                            $new->save();
                            Notification::make()->title('Milestone duplicated')->success()->send();
                        }),

                    \Filament\Actions\Action::make('delete')
                        ->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                        ->action(fn(Milestone $record) => $record->delete()),
                ]),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('bulkStatus')->label('Update Status')->icon('heroicon-o-arrow-path')
                        ->schema([Forms\Components\Select::make('status')->options(Milestone::$statuses)->required()])
                        ->action(function (array $data, $records): void {
                            foreach ($records as $r)
                                $r->update($data);
                            Notification::make()->title($records->count() . ' milestones updated')->success()->send();
                        })->deselectRecordsAfterCompletion(),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Milestones')
            ->emptyStateDescription('Create milestones to track project progress and deadlines.')
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->striped()->paginated([10, 25, 50]);
    }
}
