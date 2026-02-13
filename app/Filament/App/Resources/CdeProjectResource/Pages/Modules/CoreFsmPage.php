<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource;
use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\Client;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderTask;
use App\Models\WorkOrderItem;
use App\Models\WorkOrderType;
use App\Support\CurrencyHelper;
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
use Illuminate\Contracts\Support\Htmlable;

class CoreFsmPage extends BaseModulePage implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static string $moduleCode = 'core';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Core FSM';
    protected static ?string $title = 'Work Order Management';
    protected string $view = 'filament.app.pages.modules.core-fsm';

    public function getStats(): array
    {
        $projectId = $this->record->id;
        $total = WorkOrder::where('cde_project_id', $projectId)->count();
        $open = WorkOrder::where('cde_project_id', $projectId)
            ->whereNotIn('status', ['completed', 'cancelled'])->count();
        $urgent = WorkOrder::where('cde_project_id', $projectId)
            ->where('priority', 'urgent')
            ->whereNotIn('status', ['completed', 'cancelled'])->count();
        $overdue = WorkOrder::where('cde_project_id', $projectId)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', now())->count();
        $completedMonth = WorkOrder::where('cde_project_id', $projectId)
            ->where('status', 'completed')
            ->whereMonth('completed_at', now()->month)->count();

        return [
            [
                'label' => 'Total Work Orders',
                'value' => $total,
                'sub' => $open . ' open',
                'sub_type' => $open > 0 ? 'warning' : 'success',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085" /></svg>'
            ],
            [
                'label' => 'Urgent',
                'value' => $urgent,
                'sub' => $urgent > 0 ? 'Needs attention' : 'All clear',
                'sub_type' => $urgent > 0 ? 'danger' : 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ef4444" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>',
                'icon_bg' => '#fef2f2'
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
                'label' => 'Completed This Month',
                'value' => $completedMonth,
                'sub' => now()->format('F Y'),
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
            Action::make('createWorkOrder')
                ->label('New Work Order')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->modalWidth('3xl')
                ->schema([
                    Section::make('Work Order Details')->schema([
                        Forms\Components\TextInput::make('wo_number')->label('WO Number')
                            ->default(fn() => 'WO-' . str_pad((string) (WorkOrder::where('cde_project_id', $projectId)->count() + 1), 5, '0', STR_PAD_LEFT))
                            ->required()->maxLength(50),
                        Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                        Forms\Components\Select::make('work_order_type_id')->label('Type')
                            ->options(WorkOrderType::where('company_id', $companyId)->pluck('name', 'id'))->searchable(),
                        Forms\Components\Select::make('client_id')->label('Client')
                            ->options(Client::where('company_id', $companyId)->where('is_active', true)->pluck('name', 'id'))->searchable(),
                        Forms\Components\Select::make('priority')->options(WorkOrder::$priorities)->required()->default('medium'),
                        Forms\Components\Select::make('status')->options(WorkOrder::$statuses)->required()->default('pending'),
                        Forms\Components\Select::make('assigned_to')->label('Assign To')
                            ->options(User::where('company_id', $companyId)->where('is_active', true)->pluck('name', 'id'))->searchable(),
                        Forms\Components\DatePicker::make('due_date'),
                    ])->columns(2),
                    Section::make('Description & Notes')->schema([
                        Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
                    ])->collapsed(),
                ])
                ->action(function (array $data) use ($companyId, $projectId): void {
                    $data['company_id'] = $companyId;
                    $data['cde_project_id'] = $projectId;
                    $data['created_by'] = auth()->id();
                    WorkOrder::create($data);
                    Notification::make()->title('Work Order created successfully')->success()->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        $projectId = $this->record->id;

        return $table
            ->query(
                WorkOrder::query()
                    ->where('cde_project_id', $projectId)
                    ->with(['assignee', 'client', 'type', 'tasks'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('wo_number')->label('WO #')->searchable()->sortable()->weight('bold')
                    ->icon('heroicon-o-wrench-screwdriver'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(45),
                Tables\Columns\TextColumn::make('type.name')->label('Type')->badge()->color('info')->toggleable(),
                Tables\Columns\TextColumn::make('priority')->badge()
                    ->color(fn(string $state) => match ($state) { 'urgent' => 'danger', 'high' => 'warning', 'medium' => 'info', default => 'gray'}),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) { 'completed' => 'success', 'in_progress' => 'info', 'approved' => 'primary', 'on_hold' => 'warning', 'cancelled' => 'danger', default => 'gray'}),
                Tables\Columns\TextColumn::make('assignee.name')->label('Assigned To')->placeholder('Unassigned'),
                Tables\Columns\TextColumn::make('client.name')->label('Client')->toggleable(),
                Tables\Columns\TextColumn::make('due_date')->date()->sortable()
                    ->color(fn($record) => $record->due_date?->isPast() && !in_array($record->status, ['completed', 'cancelled']) ? 'danger' : null),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M d, Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(WorkOrder::$statuses),
                Tables\Filters\SelectFilter::make('priority')->options(WorkOrder::$priorities),
                Tables\Filters\SelectFilter::make('assigned_to')->label('Assigned To')
                    ->options(fn() => User::where('company_id', $this->record->company_id)->where('is_active', true)->pluck('name', 'id')),
            ])
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalWidth('3xl')
                    ->schema([
                        Section::make('Work Order Details')->schema([
                            Forms\Components\TextInput::make('wo_number')->disabled(),
                            Forms\Components\TextInput::make('title')->disabled()->columnSpanFull(),
                            Forms\Components\TextInput::make('priority')->disabled(),
                            Forms\Components\TextInput::make('status')->disabled(),
                            Forms\Components\TextInput::make('due_date')->disabled(),
                            Forms\Components\Textarea::make('description')->disabled()->rows(3)->columnSpanFull(),
                            Forms\Components\Textarea::make('notes')->disabled()->rows(2)->columnSpanFull(),
                        ])->columns(2),
                    ])
                    ->fillForm(fn(WorkOrder $record) => $record->toArray())
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),

                \Filament\Actions\Action::make('updateStatus')
                    ->label('Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->schema([
                        Forms\Components\Select::make('status')->options(WorkOrder::$statuses)->required(),
                        Forms\Components\Textarea::make('notes')->label('Status Note')->rows(2),
                    ])
                    ->fillForm(fn(WorkOrder $record) => ['status' => $record->status])
                    ->action(function (array $data, WorkOrder $record): void {
                        $updates = ['status' => $data['status']];
                        if ($data['status'] === 'in_progress' && !$record->started_at) {
                            $updates['started_at'] = now();
                        }
                        if ($data['status'] === 'completed') {
                            $updates['completed_at'] = now();
                        }
                        if (!empty($data['notes'])) {
                            $updates['notes'] = ($record->notes ? $record->notes . "\n" : '') . '[' . now()->format('M d H:i') . '] ' . $data['notes'];
                        }
                        $record->update($updates);
                        Notification::make()->title('Status updated to ' . WorkOrder::$statuses[$data['status']])->success()->send();
                    }),

                \Filament\Actions\Action::make('assign')
                    ->icon('heroicon-o-user-plus')
                    ->color('info')
                    ->schema([
                        Forms\Components\Select::make('assigned_to')
                            ->label('Assign To')
                            ->options(fn() => User::where('company_id', $this->record->company_id)->where('is_active', true)->pluck('name', 'id'))
                            ->searchable()->required(),
                    ])
                    ->fillForm(fn(WorkOrder $record) => ['assigned_to' => $record->assigned_to])
                    ->action(function (array $data, WorkOrder $record): void {
                        $record->update($data);
                        Notification::make()->title('Work Order reassigned')->success()->send();
                    }),

                \Filament\Actions\Action::make('edit')
                    ->icon('heroicon-o-pencil')
                    ->modalWidth('3xl')
                    ->schema([
                        Section::make('Details')->schema([
                            Forms\Components\TextInput::make('wo_number')->required(),
                            Forms\Components\TextInput::make('title')->required()->columnSpanFull(),
                            Forms\Components\Select::make('work_order_type_id')->label('Type')
                                ->options(fn() => WorkOrderType::where('company_id', $this->record->company_id)->pluck('name', 'id'))->searchable(),
                            Forms\Components\Select::make('client_id')->label('Client')
                                ->options(fn() => Client::where('company_id', $this->record->company_id)->where('is_active', true)->pluck('name', 'id'))->searchable(),
                            Forms\Components\Select::make('priority')->options(WorkOrder::$priorities)->required(),
                            Forms\Components\Select::make('status')->options(WorkOrder::$statuses)->required(),
                            Forms\Components\Select::make('assigned_to')->label('Assign To')
                                ->options(fn() => User::where('company_id', $this->record->company_id)->where('is_active', true)->pluck('name', 'id'))->searchable(),
                            Forms\Components\DatePicker::make('due_date'),
                        ])->columns(2),
                        Section::make('Description')->schema([
                            Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                            Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
                        ])->collapsed(),
                    ])
                    ->fillForm(fn(WorkOrder $record) => $record->toArray())
                    ->action(function (array $data, WorkOrder $record): void {
                        $record->update($data);
                        Notification::make()->title('Work Order updated')->success()->send();
                    }),

                \Filament\Actions\Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn(WorkOrder $record) => $record->delete()),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Work Orders')
            ->emptyStateDescription('Create your first work order for this project.')
            ->emptyStateIcon('heroicon-o-wrench-screwdriver');
    }
}
