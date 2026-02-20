<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource;
use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\Asset;
use App\Models\Client;
use App\Models\User;
use App\Models\WorkOrder;
use App\Models\WorkOrderItem;
use App\Models\WorkOrderTask;
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

class CoreFsmPage extends BaseModulePage implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static string $moduleCode = 'core';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Core FSM';
    protected static ?string $title = 'Work Order Management';
    protected string $view = 'filament.app.pages.modules.core-fsm';

    /* ───────────────── STAT CARDS ───────────────── */

    public function getStats(): array
    {
        $pid = $this->record->id;
        $base = WorkOrder::where('cde_project_id', $pid);

        $total = (clone $base)->count();
        $open = (clone $base)->whereNotIn('status', ['completed', 'cancelled'])->count();
        $urgent = (clone $base)->where('priority', 'urgent')->whereNotIn('status', ['completed', 'cancelled'])->count();
        $overdue = (clone $base)->whereNotIn('status', ['completed', 'cancelled'])->whereNotNull('due_date')->where('due_date', '<', now())->count();
        $inProg = (clone $base)->where('status', 'in_progress')->count();
        $compMonth = (clone $base)->where('status', 'completed')->whereMonth('completed_at', now()->month)->whereYear('completed_at', now()->year)->count();
        $totalCost = (clone $base)->withSum('items', 'amount')->get()->sum('items_sum_amount') ?? 0;

        return [
            [
                'label' => 'Total Work Orders',
                'value' => $total,
                'sub' => $open . ' open · ' . $inProg . ' in progress',
                'sub_type' => $open > 0 ? 'info' : 'success',
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
                'value' => $compMonth,
                'sub' => now()->format('F Y'),
                'sub_type' => 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#ecfdf5'
            ],
            [
                'label' => 'Total Cost',
                'value' => CurrencyHelper::format($totalCost, 0),
                'sub' => 'Parts & services',
                'sub_type' => 'neutral',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#6366f1" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#eef2ff'
            ],
        ];
    }

    /* ───────────────── HELPERS ───────────────── */

    private function companyId(): int
    {
        return $this->record->company_id;
    }

    private function projectId(): int
    {
        return $this->record->id;
    }

    private function nextWoNumber(): string
    {
        $count = WorkOrder::where('cde_project_id', $this->projectId())->count() + 1;
        return 'WO-' . str_pad((string) $count, 5, '0', STR_PAD_LEFT);
    }

    private function teamOptions(): array
    {
        return User::where('company_id', $this->companyId())
            ->where('is_active', true)
            ->pluck('name', 'id')
            ->toArray();
    }

    private function clientOptions(): array
    {
        return Client::where('company_id', $this->companyId())
            ->where('is_active', true)
            ->pluck('name', 'id')
            ->toArray();
    }

    private function typeOptions(): array
    {
        return WorkOrderType::where('company_id', $this->companyId())
            ->where('is_active', true)
            ->pluck('name', 'id')
            ->toArray();
    }

    private function assetOptions(): array
    {
        return Asset::where('company_id', $this->companyId())
            ->pluck('name', 'id')
            ->toArray();
    }

    /* ───────────────── FORM SCHEMAS ───────────────── */

    private function woFormSchema(bool $isCreate = false): array
    {
        return [
            Section::make('Work Order Details')->schema([
                Forms\Components\TextInput::make('wo_number')
                    ->label('WO Number')
                    ->default(fn() => $isCreate ? $this->nextWoNumber() : null)
                    ->required()
                    ->maxLength(50)
                    ->unique(table: 'work_orders', column: 'wo_number', ignorable: fn($record) => $record),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Select::make('work_order_type_id')
                    ->label('Type')
                    ->options(fn() => $this->typeOptions())
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')->required()->maxLength(100),
                        Forms\Components\ColorPicker::make('color'),
                    ])
                    ->createOptionUsing(fn(array $data) => WorkOrderType::create(array_merge($data, [
                        'company_id' => $this->companyId(),
                        'is_active' => true,
                    ]))->id),
                Forms\Components\Select::make('client_id')
                    ->label('Client')
                    ->options(fn() => $this->clientOptions())
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')->required()->maxLength(255),
                        Forms\Components\TextInput::make('email')->email()->maxLength(255),
                        Forms\Components\TextInput::make('phone')->tel()->maxLength(50),
                        Forms\Components\TextInput::make('company_name')->label('Company Name')->maxLength(255),
                    ])
                    ->createOptionUsing(fn(array $data) => Client::create(array_merge($data, [
                        'company_id' => $this->companyId(),
                        'is_active' => true,
                    ]))->id),
                Forms\Components\Select::make('asset_id')
                    ->label('Asset / Location')
                    ->options(fn() => $this->assetOptions())
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')->required()->maxLength(255),
                        Forms\Components\Select::make('category')->options([
                            'building' => 'Building',
                            'equipment' => 'Equipment',
                            'vehicle' => 'Vehicle',
                            'facility' => 'Facility',
                            'site' => 'Site / Location',
                            'other' => 'Other',
                        ])->required()->searchable()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('custom_category')
                                    ->label('New Category Name')
                                    ->required()
                                    ->maxLength(100),
                            ])
                            ->createOptionUsing(fn(array $data) => $data['custom_category']),
                        Forms\Components\TextInput::make('location')->maxLength(255),
                        Forms\Components\TextInput::make('serial_number')->label('Serial / ID')->maxLength(100),
                    ])
                    ->createOptionUsing(fn(array $data) => Asset::create(array_merge($data, [
                        'company_id' => $this->companyId(),
                        'status' => 'active',
                        'condition' => 'good',
                    ]))->id),
                Forms\Components\Select::make('priority')
                    ->options(WorkOrder::$priorities)
                    ->required()
                    ->default('medium'),
                Forms\Components\Select::make('status')
                    ->options(WorkOrder::$statuses)
                    ->required()
                    ->default($isCreate ? 'pending' : null),
                Forms\Components\Select::make('assigned_to')
                    ->label('Assign To')
                    ->options(fn() => $this->teamOptions())
                    ->searchable()
                    ->nullable(),
                Forms\Components\DatePicker::make('due_date'),
                Forms\Components\DatePicker::make('preferred_date')
                    ->label('Preferred Date'),
                Forms\Components\TimePicker::make('preferred_time')
                    ->label('Preferred Time'),
                Forms\Components\TextInput::make('preferred_notes')
                    ->label('Scheduling Notes')
                    ->maxLength(255),
            ])->columns(2),

            Section::make('Description & Notes')->schema([
                Forms\Components\RichEditor::make('description')
                    ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link'])
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ])->collapsed(!$isCreate),
        ];
    }

    /* ───────────────── HEADER ACTIONS ───────────────── */

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createWorkOrder')
                ->label('New Work Order')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->modalWidth('4xl')
                ->schema($this->woFormSchema(isCreate: true))
                ->action(function (array $data): void {
                    $data['company_id'] = $this->companyId();
                    $data['cde_project_id'] = $this->projectId();
                    $data['created_by'] = auth()->id();
                    WorkOrder::create($data);
                    Notification::make()->title('Work Order created successfully')->success()->send();
                }),
        ];
    }

    /* ───────────────── TABLE ───────────────── */

    public function table(Table $table): Table
    {
        return $table
            ->query(
                WorkOrder::query()
                    ->where('cde_project_id', $this->projectId())
                    ->with(['assignee', 'client', 'type', 'tasks', 'items'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('wo_number')
                    ->label('WO #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->copyable()
                    ->copyMessage('WO number copied'),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn(WorkOrder $record) => $record->title),

                Tables\Columns\TextColumn::make('type.name')
                    ->label('Type')
                    ->badge()
                    ->color('info')
                    ->toggleable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'urgent' => 'danger', 'high' => 'warning', 'medium' => 'info', default => 'gray'
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'completed' => 'success', 'in_progress' => 'info', 'approved' => 'primary',
                        'on_hold' => 'warning', 'cancelled' => 'danger', default => 'gray'
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Assigned To')
                    ->placeholder('Unassigned')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tasks_progress')
                    ->label('Tasks')
                    ->state(function (WorkOrder $record) {
                        $total = $record->tasks->count();
                        if ($total === 0)
                            return '—';
                        $done = $record->tasks->where('is_completed', true)->count();
                        return $done . '/' . $total;
                    })
                    ->color(function (WorkOrder $record) {
                        $total = $record->tasks->count();
                        if ($total === 0)
                            return null;
                        $done = $record->tasks->where('is_completed', true)->count();
                        return $done === $total ? 'success' : 'warning';
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('items_cost')
                    ->label('Cost')
                    ->state(fn(WorkOrder $record) => $record->items->sum('amount'))
                    ->money('USD')
                    ->sortable(query: fn($query, $direction) => $query->withSum('items', 'amount')->orderBy('items_sum_amount', $direction))
                    ->toggleable(),

                Tables\Columns\TextColumn::make('client.name')
                    ->label('Client')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable()
                    ->color(fn($record) => $record->due_date?->isPast() && !in_array($record->status, ['completed', 'cancelled']) ? 'danger' : null)
                    ->description(fn($record) => $record->due_date?->isPast() && !in_array($record->status, ['completed', 'cancelled']) ? 'Overdue' : null),

                Tables\Columns\TextColumn::make('started_at')
                    ->label('Started')
                    ->dateTime('M d, H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime('M d, H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(WorkOrder::$statuses)
                    ->multiple(),
                Tables\Filters\SelectFilter::make('priority')
                    ->options(WorkOrder::$priorities)
                    ->multiple(),
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->label('Assigned To')
                    ->options(fn() => $this->teamOptions()),
                Tables\Filters\SelectFilter::make('work_order_type_id')
                    ->label('Type')
                    ->options(fn() => $this->typeOptions()),
                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue Only')
                    ->query(fn($query) => $query->whereNotIn('status', ['completed', 'cancelled'])->whereNotNull('due_date')->where('due_date', '<', now()))
                    ->toggle(),
                Tables\Filters\Filter::make('unassigned')
                    ->label('Unassigned Only')
                    ->query(fn($query) => $query->whereNull('assigned_to'))
                    ->toggle(),
            ])
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                    // ── View Detail ──
                    \Filament\Actions\Action::make('view')
                        ->icon('heroicon-o-eye')
                        ->color('gray')
                        ->modalWidth('5xl')
                        ->modalHeading(fn(WorkOrder $record) => $record->wo_number . ' — ' . $record->title)
                        ->schema(fn(WorkOrder $record) => $this->viewModalSchema($record))
                        ->fillForm(fn(WorkOrder $record) => $this->viewModalData($record))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),

                    // ── Quick Status ──
                    \Filament\Actions\Action::make('updateStatus')
                        ->label('Status')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->schema([
                            Forms\Components\Select::make('status')
                                ->options(WorkOrder::$statuses)
                                ->required(),
                            Forms\Components\Textarea::make('status_note')
                                ->label('Status Note')
                                ->rows(2)
                                ->placeholder('Optional note about this status change'),
                        ])
                        ->fillForm(fn(WorkOrder $record) => ['status' => $record->status])
                        ->action(function (array $data, WorkOrder $record): void {
                            $updates = ['status' => $data['status']];
                            if ($data['status'] === 'in_progress' && !$record->started_at) {
                                $updates['started_at'] = now();
                            }
                            if ($data['status'] === 'completed' && !$record->completed_at) {
                                $updates['completed_at'] = now();
                            }
                            if (!empty($data['status_note'])) {
                                $updates['notes'] = ($record->notes ? $record->notes . "\n" : '')
                                    . '[' . now()->format('M d H:i') . ' — ' . auth()->user()->name . '] '
                                    . $data['status_note'];
                            }
                            $record->update($updates);
                            Notification::make()
                                ->title('Status → ' . WorkOrder::$statuses[$data['status']])
                                ->success()->send();
                        }),

                    // ── Add Task ──
                    \Filament\Actions\Action::make('addTask')
                        ->label('Add Task')
                        ->icon('heroicon-o-plus-circle')
                        ->color('info')
                        ->modalHeading(fn(WorkOrder $record) => 'Add Task — ' . $record->wo_number)
                        ->schema([
                            Forms\Components\TextInput::make('title')->required()->maxLength(255),
                            Forms\Components\Textarea::make('description')->rows(2),
                            Forms\Components\Toggle::make('is_completed')->label('Mark as Done'),
                        ])
                        ->action(function (array $data, WorkOrder $record): void {
                            $record->tasks()->create([
                                'title' => $data['title'],
                                'description' => $data['description'] ?? null,
                                'is_completed' => $data['is_completed'] ?? false,
                                'completed_by' => ($data['is_completed'] ?? false) ? auth()->id() : null,
                                'completed_at' => ($data['is_completed'] ?? false) ? now() : null,
                                'sort_order' => ($record->tasks()->max('sort_order') ?? 0) + 1,
                            ]);
                            Notification::make()->title('Task added — ' . $data['title'])->success()->send();
                        }),

                    // ── Toggle Task Status ──
                    \Filament\Actions\Action::make('toggleTask')
                        ->label('Toggle Task')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->color('info')
                        ->modalHeading(fn(WorkOrder $record) => 'Toggle Tasks — ' . $record->wo_number)
                        ->schema([
                            Forms\Components\CheckboxList::make('task_ids')
                                ->label('Select tasks to mark as complete/incomplete')
                                ->options(fn(WorkOrder $record) => $record->tasks()->orderBy('sort_order')->get()
                                    ->mapWithKeys(fn($t) => [$t->id => ($t->is_completed ? '✅ ' : '⬜ ') . $t->title]))
                                ->required()->searchable()->columns(1),
                            Forms\Components\Toggle::make('mark_complete')->label('Mark selected as complete?')->default(true),
                        ])
                        ->action(function (array $data, WorkOrder $record): void {
                            $complete = $data['mark_complete'] ?? true;
                            \App\Models\WorkOrderTask::whereIn('id', $data['task_ids'])->where('work_order_id', $record->id)
                                ->update([
                                    'is_completed' => $complete,
                                    'completed_by' => $complete ? auth()->id() : null,
                                    'completed_at' => $complete ? now() : null,
                                ]);
                            $total = $record->tasks()->count();
                            $done = $record->tasks()->where('is_completed', true)->count();
                            Notification::make()->title("Tasks: {$done}/{$total} complete")->success()->send();
                        }),

                    // ── Delete Tasks ──
                    \Filament\Actions\Action::make('deleteTasks')
                        ->label('Delete Tasks')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->modalHeading(fn(WorkOrder $record) => 'Delete Tasks — ' . $record->wo_number)
                        ->schema([
                            Forms\Components\CheckboxList::make('task_ids')
                                ->label('Select tasks to remove')
                                ->options(fn(WorkOrder $record) => $record->tasks()->orderBy('sort_order')->get()
                                    ->mapWithKeys(fn($t) => [$t->id => ($t->is_completed ? '✅ ' : '⬜ ') . $t->title]))
                                ->required()->searchable()->columns(1),
                        ])
                        ->action(function (array $data, WorkOrder $record): void {
                            $count = count($data['task_ids']);
                            \App\Models\WorkOrderTask::whereIn('id', $data['task_ids'])->where('work_order_id', $record->id)->delete();
                            Notification::make()->title("{$count} tasks deleted")->danger()->send();
                        }),

                    // ── Add Item (Part/Service) ──
                    \Filament\Actions\Action::make('addItem')
                        ->label('Add Item')
                        ->icon('heroicon-o-shopping-cart')
                        ->color('success')
                        ->modalHeading(fn(WorkOrder $record) => 'Add Part/Service — ' . $record->wo_number)
                        ->schema([
                            Forms\Components\Select::make('type')
                                ->options(['part' => 'Part', 'service' => 'Service', 'labour' => 'Labour', 'other' => 'Other'])
                                ->required()->default('part'),
                            Forms\Components\TextInput::make('description')->required()->maxLength(255),
                            Forms\Components\TextInput::make('quantity')->numeric()->required()->default(1)->minValue(0.01),
                            Forms\Components\TextInput::make('unit_price')->label('Unit Price')->numeric()->prefix('$')->required()->default(0),
                        ])
                        ->action(function (array $data, WorkOrder $record): void {
                            $data['amount'] = round(($data['quantity'] ?? 0) * ($data['unit_price'] ?? 0), 2);
                            $record->items()->create($data);
                            $total = $record->items()->sum('amount');
                            Notification::make()->title('Item added — $' . number_format($data['amount'], 2) . ' (Total: ' . CurrencyHelper::format($total) . ')')->success()->send();
                        }),

                    // ── Edit Item ──
                    \Filament\Actions\Action::make('editItem')
                        ->label('Edit Item')
                        ->icon('heroicon-o-pencil-square')
                        ->color('success')
                        ->modalHeading(fn(WorkOrder $record) => 'Edit Item — ' . $record->wo_number)
                        ->schema([
                            Forms\Components\Select::make('item_id')->label('Select Item')
                                ->options(fn(WorkOrder $record) => $record->items->mapWithKeys(fn($i) =>
                                    [$i->id => ucfirst($i->type) . ': ' . $i->description . ' ($' . number_format((float) $i->amount, 2) . ')']))
                                ->required()->searchable()->live()
                                ->afterStateUpdated(function ($state, Forms\Set $set) {
                                    if ($item = \App\Models\WorkOrderItem::find($state)) {
                                        $set('type', $item->type);
                                        $set('description', $item->description);
                                        $set('quantity', $item->quantity);
                                        $set('unit_price', $item->unit_price);
                                    }
                                }),
                            Forms\Components\Select::make('type')
                                ->options(['part' => 'Part', 'service' => 'Service', 'labour' => 'Labour', 'other' => 'Other'])
                                ->required(),
                            Forms\Components\TextInput::make('description')->required()->maxLength(255),
                            Forms\Components\TextInput::make('quantity')->numeric()->required()->minValue(0.01),
                            Forms\Components\TextInput::make('unit_price')->label('Unit Price')->numeric()->prefix('$')->required(),
                        ])
                        ->action(function (array $data, WorkOrder $record): void {
                            $item = \App\Models\WorkOrderItem::where('id', $data['item_id'])->where('work_order_id', $record->id)->first();
                            if (!$item)
                                return;
                            unset($data['item_id']);
                            $data['amount'] = round(($data['quantity'] ?? 0) * ($data['unit_price'] ?? 0), 2);
                            $item->update($data);
                            Notification::make()->title('Item updated — ' . $data['description'])->success()->send();
                        }),

                    // ── Delete Items ──
                    \Filament\Actions\Action::make('deleteItems')
                        ->label('Delete Items')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->modalHeading(fn(WorkOrder $record) => 'Delete Items — ' . $record->wo_number)
                        ->schema([
                            Forms\Components\CheckboxList::make('item_ids')
                                ->label('Select items to remove')
                                ->options(fn(WorkOrder $record) => $record->items->mapWithKeys(fn($i) =>
                                    [$i->id => ucfirst($i->type) . ': ' . $i->description . ' ($' . number_format((float) $i->amount, 2) . ')']))
                                ->required()->searchable()->columns(1),
                        ])
                        ->action(function (array $data, WorkOrder $record): void {
                            $count = count($data['item_ids']);
                            \App\Models\WorkOrderItem::whereIn('id', $data['item_ids'])->where('work_order_id', $record->id)->delete();
                            $total = $record->items()->sum('amount');
                            Notification::make()
                                ->title("{$count} items deleted — Total: " . CurrencyHelper::format($total))
                                ->danger()->send();
                        }),

                    // ── Edit ──
                    \Filament\Actions\Action::make('edit')
                        ->icon('heroicon-o-pencil')
                        ->modalWidth('4xl')
                        ->schema($this->woFormSchema())
                        ->fillForm(fn(WorkOrder $record) => $record->toArray())
                        ->action(function (array $data, WorkOrder $record): void {
                            $record->update($data);
                            Notification::make()->title('Work Order updated')->success()->send();
                        }),

                    // ── Assign ──
                    \Filament\Actions\Action::make('assign')
                        ->icon('heroicon-o-user-plus')
                        ->color('info')
                        ->schema([
                            Forms\Components\Select::make('assigned_to')
                                ->label('Assign To')
                                ->options(fn() => $this->teamOptions())
                                ->searchable()
                                ->required(),
                        ])
                        ->fillForm(fn(WorkOrder $record) => ['assigned_to' => $record->assigned_to])
                        ->action(function (array $data, WorkOrder $record): void {
                            $record->update($data);
                            $name = User::find($data['assigned_to'])?->name ?? 'Unknown';
                            Notification::make()->title("Assigned to {$name}")->success()->send();
                        }),

                    // ── Duplicate ──
                    \Filament\Actions\Action::make('duplicate')
                        ->icon('heroicon-o-document-duplicate')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading('Duplicate Work Order')
                        ->modalDescription('This will create a copy with status set to Pending.')
                        ->action(function (WorkOrder $record): void {
                            $new = $record->replicate(['wo_number', 'started_at', 'completed_at']);
                            $new->wo_number = $this->nextWoNumber();
                            $new->status = 'pending';
                            $new->created_by = auth()->id();
                            $new->save();

                            // Copy tasks
                            foreach ($record->tasks as $task) {
                                $new->tasks()->create([
                                    'title' => $task->title,
                                    'description' => $task->description,
                                    'is_completed' => false,
                                    'sort_order' => $task->sort_order,
                                ]);
                            }

                            Notification::make()
                                ->title('Duplicated as ' . $new->wo_number)
                                ->success()->send();
                        }),

                    // ── Delete ──
                    \Filament\Actions\Action::make('delete')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn(WorkOrder $record) => $record->delete()),
                ]),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    // Bulk Status Update
                    \Filament\Actions\BulkAction::make('bulkStatus')
                        ->label('Update Status')
                        ->icon('heroicon-o-arrow-path')
                        ->schema([
                            Forms\Components\Select::make('status')
                                ->options(WorkOrder::$statuses)
                                ->required(),
                        ])
                        ->action(function (array $data, $records): void {
                            $updates = ['status' => $data['status']];
                            if ($data['status'] === 'completed') {
                                $updates['completed_at'] = now();
                            }
                            if ($data['status'] === 'in_progress') {
                                $updates['started_at'] = now();
                            }
                            foreach ($records as $r) {
                                $r->update($updates);
                            }
                            Notification::make()->title($records->count() . ' work orders updated')->success()->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // Bulk Assign
                    \Filament\Actions\BulkAction::make('bulkAssign')
                        ->label('Assign To')
                        ->icon('heroicon-o-user-plus')
                        ->schema([
                            Forms\Components\Select::make('assigned_to')
                                ->label('Assign To')
                                ->options(fn() => $this->teamOptions())
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function (array $data, $records): void {
                            foreach ($records as $r) {
                                $r->update(['assigned_to' => $data['assigned_to']]);
                            }
                            $name = User::find($data['assigned_to'])?->name ?? 'Unknown';
                            Notification::make()->title($records->count() . " WOs assigned to {$name}")->success()->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    // Bulk Priority
                    \Filament\Actions\BulkAction::make('bulkPriority')
                        ->label('Set Priority')
                        ->icon('heroicon-o-flag')
                        ->schema([
                            Forms\Components\Select::make('priority')
                                ->options(WorkOrder::$priorities)
                                ->required(),
                        ])
                        ->action(function (array $data, $records): void {
                            foreach ($records as $r) {
                                $r->update(['priority' => $data['priority']]);
                            }
                            Notification::make()->title($records->count() . ' WOs priority updated')->success()->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Work Orders')
            ->emptyStateDescription('Create your first work order for this project.')
            ->emptyStateIcon('heroicon-o-wrench-screwdriver')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    /* ───────────────── VIEW MODAL HELPERS ───────────────── */

    private function viewModalSchema(WorkOrder $record): array
    {
        $tasks = $record->tasks()->orderBy('sort_order')->get();
        $items = $record->items;
        $totalCost = $items->sum('amount');

        return [
            Section::make('Details')->schema([
                Forms\Components\TextInput::make('wo_number')->label('WO Number')->disabled(),
                Forms\Components\TextInput::make('type_name')->label('Type')->disabled(),
                Forms\Components\TextInput::make('priority_label')->label('Priority')->disabled(),
                Forms\Components\TextInput::make('status_label')->label('Status')->disabled(),
                Forms\Components\TextInput::make('assignee_name')->label('Assigned To')->disabled(),
                Forms\Components\TextInput::make('client_name')->label('Client')->disabled(),
                Forms\Components\TextInput::make('due_date_display')->label('Due Date')->disabled(),
                Forms\Components\TextInput::make('created_by_name')->label('Created By')->disabled(),
            ])->columns(2),

            Section::make('Timeline')->schema([
                Forms\Components\TextInput::make('created_display')->label('Created')->disabled(),
                Forms\Components\TextInput::make('started_display')->label('Started')->disabled(),
                Forms\Components\TextInput::make('completed_display')->label('Completed')->disabled(),
                Forms\Components\TextInput::make('duration_display')->label('Duration')->disabled(),
            ])->columns(4)->collapsed(!$record->started_at),

            Section::make('Description')->schema([
                Forms\Components\Textarea::make('description')->disabled()->rows(3)->columnSpanFull(),
            ])->collapsed(empty($record->description)),

            Section::make("Tasks ({$tasks->where('is_completed', true)->count()}/{$tasks->count()})")->schema([
                Forms\Components\Placeholder::make('tasks_list')
                    ->content(function () use ($tasks) {
                        if ($tasks->isEmpty())
                            return 'No tasks defined.';
                        return $tasks->map(
                            fn($t) =>
                            ($t->is_completed ? '✅' : '⬜') . ' ' . $t->title
                            . ($t->description ? ' — ' . $t->description : '')
                        )->join("\n");
                    }),
            ])->collapsed($tasks->isEmpty()),

            Section::make("Items & Costs (" . CurrencyHelper::format($totalCost) . ")")->schema([
                Forms\Components\Placeholder::make('items_list')
                    ->content(function () use ($items, $totalCost) {
                        if ($items->isEmpty())
                            return 'No items added.';
                        $lines = $items->map(
                            fn($i) =>
                            ucfirst($i->type) . ': ' . $i->description
                            . ' — ' . $i->quantity . ' × $' . number_format($i->unit_price, 2)
                            . ' = $' . number_format($i->amount, 2)
                        );
                        $lines->push('─────────────────────────');
                        $lines->push('Total: $' . number_format($totalCost, 2));
                        return $lines->join("\n");
                    }),
            ])->collapsed($items->isEmpty()),

            Section::make('Notes')->schema([
                Forms\Components\Textarea::make('notes')->disabled()->rows(4)->columnSpanFull(),
            ])->collapsed(empty($record->notes)),
        ];
    }

    private function viewModalData(WorkOrder $record): array
    {
        $record->load(['assignee', 'client', 'type', 'creator']);

        $duration = null;
        if ($record->started_at && $record->completed_at) {
            $duration = $record->started_at->diffForHumans($record->completed_at, true);
        } elseif ($record->started_at) {
            $duration = $record->started_at->diffForHumans(now(), true) . ' (ongoing)';
        }

        return [
            'wo_number' => $record->wo_number,
            'type_name' => $record->type?->name ?? '—',
            'priority_label' => WorkOrder::$priorities[$record->priority] ?? $record->priority,
            'status_label' => WorkOrder::$statuses[$record->status] ?? $record->status,
            'assignee_name' => $record->assignee?->name ?? 'Unassigned',
            'client_name' => $record->client?->name ?? '—',
            'due_date_display' => $record->due_date?->format('M d, Y') ?? '—',
            'created_by_name' => $record->creator?->name ?? '—',
            'created_display' => $record->created_at?->format('M d, Y H:i'),
            'started_display' => $record->started_at?->format('M d, Y H:i') ?? '—',
            'completed_display' => $record->completed_at?->format('M d, Y H:i') ?? '—',
            'duration_display' => $duration ?? '—',
            'description' => $record->description ?? '',
            'notes' => $record->notes ?? '',
        ];
    }
}
