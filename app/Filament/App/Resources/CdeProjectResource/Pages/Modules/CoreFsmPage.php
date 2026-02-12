<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\Client;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderType;
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

class CoreFsmPage extends BaseModulePage implements HasTable
{
    use InteractsWithTable;

    protected static string $moduleCode = 'core';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Core FSM';
    protected static ?string $title = 'Core FSM';
    protected string $view = 'filament.app.pages.modules.core-fsm';

    public function getStats(): array
    {
        $companyId = $this->record->company_id;
        $total = WorkOrder::where('company_id', $companyId)->count();
        $open = WorkOrder::where('company_id', $companyId)
            ->whereNotIn('status', ['completed', 'cancelled'])->count();
        $urgent = WorkOrder::where('company_id', $companyId)
            ->where('priority', 'urgent')
            ->whereNotIn('status', ['completed', 'cancelled'])->count();
        $completedMonth = WorkOrder::where('company_id', $companyId)
            ->where('status', 'completed')
            ->whereMonth('completed_at', now()->month)->count();

        return [
            [
                'label' => 'Work Orders',
                'value' => $total,
                'sub' => $open . ' open',
                'sub_type' => $open > 0 ? 'warning' : 'success',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.84-3.08a2.003 2.003 0 01-1.08-1.78V7.88c0-.77.44-1.47 1.13-1.8L11.6 2.82a2.003 2.003 0 011.8 0l6.07 3.26c.69.33 1.13 1.03 1.13 1.8v2.43c0 .76-.44 1.46-1.13 1.79l-5.84 3.08a2.003 2.003 0 01-1.8 0z" /></svg>'
            ],
            [
                'label' => 'Urgent',
                'value' => $urgent,
                'sub' => $urgent > 0 ? 'Need attention' : 'All clear',
                'sub_type' => $urgent > 0 ? 'danger' : 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#dc2626" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>',
                'icon_bg' => '#fef2f2'
            ],
            [
                'label' => 'In Progress',
                'value' => WorkOrder::where('company_id', $companyId)->where('status', 'in_progress')->count(),
                'sub' => 'Active now',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2563eb" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" /></svg>',
                'icon_bg' => '#eff6ff'
            ],
            [
                'label' => 'Completed',
                'value' => $completedMonth,
                'sub' => 'This month',
                'sub_type' => 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#ecfdf5'
            ],
        ];
    }

    protected function getWorkOrderFormSchema(): array
    {
        $companyId = $this->record->company_id;
        return [
            Section::make('Work Order Details')->schema([
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
                Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
            ])->columns(2),
        ];
    }

    public function table(Table $table): Table
    {
        $companyId = $this->record->company_id;

        return $table
            ->query(WorkOrder::query()->where('company_id', $companyId))
            ->columns([
                Tables\Columns\TextColumn::make('wo_number')->label('WO #')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(45),
                Tables\Columns\TextColumn::make('priority')->badge()
                    ->color(fn(string $state) => match ($state) { 'urgent' => 'danger', 'high' => 'warning', 'medium' => 'info', default => 'gray'}),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) { 'completed' => 'success', 'in_progress' => 'info', 'approved' => 'primary', 'on_hold' => 'warning', 'cancelled' => 'danger', default => 'gray'}),
                Tables\Columns\TextColumn::make('assignee.name')->label('Assigned To')->placeholder('Unassigned'),
                Tables\Columns\TextColumn::make('client.name')->label('Client')->toggleable(),
                Tables\Columns\TextColumn::make('due_date')->date()->sortable()
                    ->color(fn($record) => $record->due_date?->isPast() && !in_array($record->status, ['completed', 'cancelled']) ? 'danger' : null),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(WorkOrder::$statuses),
                Tables\Filters\SelectFilter::make('priority')->options(WorkOrder::$priorities),
            ])
            ->headerActions([
                Action::make('create')
                    ->label('New Work Order')
                    ->icon('heroicon-o-plus')
                    ->schema($this->getWorkOrderFormSchema())
                    ->action(function (array $data) use ($companyId): void {
                        $data['company_id'] = $companyId;
                        $data['created_by'] = auth()->id();
                        $data['wo_number'] = 'WO-' . str_pad((string) (WorkOrder::where('company_id', $companyId)->count() + 1), 5, '0', STR_PAD_LEFT);
                        WorkOrder::create($data);
                        Notification::make()->title('Work Order created')->success()->send();
                    }),
            ])
            ->recordActions([
                Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->schema($this->getWorkOrderFormSchema())
                    ->fillForm(fn(WorkOrder $record) => $record->toArray())
                    ->modalSubmitAction(false),
                Action::make('edit')
                    ->icon('heroicon-o-pencil')
                    ->schema($this->getWorkOrderFormSchema())
                    ->fillForm(fn(WorkOrder $record) => $record->toArray())
                    ->action(function (array $data, WorkOrder $record): void {
                        $record->update($data);
                        Notification::make()->title('Work Order updated')->success()->send();
                    }),
                Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn(WorkOrder $record) => $record->delete()),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->emptyStateHeading('No Work Orders')
            ->emptyStateDescription('No work orders have been created yet.')
            ->emptyStateIcon('heroicon-o-wrench-screwdriver');
    }
}
