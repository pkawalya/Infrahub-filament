<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource;
use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
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

class InventoryPage extends BaseModulePage implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static string $moduleCode = 'inventory';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Inventory';
    protected static ?string $title = 'Inventory & Procurement';
    protected string $view = 'filament.app.pages.modules.inventory';

    public function getStats(): array
    {
        $pid = $this->record->id;
        $total = PurchaseOrder::where('cde_project_id', $pid)->count();
        $totalValue = PurchaseOrder::where('cde_project_id', $pid)->sum('total_amount');
        $pending = PurchaseOrder::where('cde_project_id', $pid)->whereIn('status', ['draft', 'submitted'])->count();
        $ordered = PurchaseOrder::where('cde_project_id', $pid)->whereIn('status', ['approved', 'ordered'])->count();

        return [
            [
                'label' => 'Purchase Orders',
                'value' => $total,
                'sub' => $ordered . ' in progress',
                'sub_type' => 'info',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>'
            ],
            [
                'label' => 'Total Value',
                'value' => CurrencyHelper::format($totalValue, 0),
                'sub' => 'All orders',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#ecfdf5'
            ],
            [
                'label' => 'Pending Approval',
                'value' => $pending,
                'sub' => $pending > 0 ? 'Awaiting action' : 'All clear',
                'sub_type' => $pending > 0 ? 'warning' : 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#d97706" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>',
                'icon_bg' => '#fffbeb'
            ],
        ];
    }

    protected function getHeaderActions(): array
    {
        $companyId = $this->record->company_id;
        $projectId = $this->record->id;

        return [
            Action::make('createPO')
                ->label('New Purchase Order')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->modalWidth('3xl')
                ->schema([
                    Section::make('Purchase Order Details')->schema([
                        Forms\Components\TextInput::make('po_number')->label('PO Number')
                            ->default(fn() => 'PO-' . str_pad((string) (PurchaseOrder::where('cde_project_id', $projectId)->count() + 1), 4, '0', STR_PAD_LEFT))
                            ->required()->maxLength(50),
                        Forms\Components\Select::make('supplier_id')->label('Supplier')
                            ->options(Supplier::where('company_id', $companyId)->where('is_active', true)->pluck('name', 'id'))->searchable()->required(),
                        Forms\Components\Select::make('warehouse_id')->label('Warehouse')
                            ->options(Warehouse::where('company_id', $companyId)->pluck('name', 'id'))->searchable(),
                        Forms\Components\Select::make('status')->options(PurchaseOrder::$statuses)->required()->default('draft'),
                        Forms\Components\DatePicker::make('order_date')->required()->default(now()),
                        Forms\Components\DatePicker::make('expected_date')->label('Expected Delivery'),
                    ])->columns(2),
                    Section::make('Financial Details')->schema([
                        Forms\Components\TextInput::make('subtotal')->numeric()->prefix('$')->default(0),
                        Forms\Components\TextInput::make('tax_amount')->numeric()->prefix('$')->default(0)->label('Tax'),
                        Forms\Components\TextInput::make('shipping_cost')->numeric()->prefix('$')->default(0)->label('Shipping'),
                        Forms\Components\TextInput::make('total_amount')->numeric()->prefix('$')->default(0)->label('Total'),
                    ])->columns(2),
                    Section::make('Notes')->schema([
                        Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
                    ])->collapsed(),
                ])
                ->action(function (array $data) use ($companyId, $projectId): void {
                    $data['company_id'] = $companyId;
                    $data['cde_project_id'] = $projectId;
                    $data['created_by'] = auth()->id();
                    PurchaseOrder::create($data);
                    Notification::make()->title('Purchase Order created')->success()->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        $projectId = $this->record->id;

        return $table
            ->query(PurchaseOrder::query()->where('cde_project_id', $projectId)->with(['supplier', 'warehouse', 'creator']))
            ->columns([
                Tables\Columns\TextColumn::make('po_number')->label('PO #')->searchable()->sortable()->weight('bold')
                    ->icon('heroicon-o-shopping-bag'),
                Tables\Columns\TextColumn::make('supplier.name')->label('Supplier')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) {
                        'approved' => 'success', 'ordered' => 'info', 'received' => 'primary',
                        'draft' => 'gray', 'cancelled' => 'danger', default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('total_amount')->formatStateUsing(CurrencyHelper::formatter())->label('Total'),
                Tables\Columns\TextColumn::make('order_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('expected_date')->date()->sortable()->label('Expected')
                    ->color(fn($record) => $record->expected_date?->isPast() && !in_array($record->status, ['received', 'cancelled']) ? 'danger' : null),
                Tables\Columns\TextColumn::make('warehouse.name')->label('Warehouse')->toggleable(),
                Tables\Columns\TextColumn::make('creator.name')->label('Created By')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(PurchaseOrder::$statuses),
            ])
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalWidth('3xl')
                    ->schema([
                        Section::make('PO Details')->schema([
                            Forms\Components\TextInput::make('po_number')->disabled(),
                            Forms\Components\TextInput::make('status')->disabled(),
                            Forms\Components\TextInput::make('order_date')->disabled(),
                            Forms\Components\TextInput::make('expected_date')->disabled(),
                            Forms\Components\TextInput::make('subtotal')->disabled()->prefix('$'),
                            Forms\Components\TextInput::make('total_amount')->disabled()->prefix('$'),
                            Forms\Components\Textarea::make('notes')->disabled()->rows(2)->columnSpanFull(),
                        ])->columns(2),
                    ])
                    ->fillForm(fn(PurchaseOrder $record) => $record->toArray())
                    ->modalSubmitAction(false)->modalCancelActionLabel('Close'),

                \Filament\Actions\Action::make('updateStatus')
                    ->label('Status')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->schema([
                        Forms\Components\Select::make('status')->options(PurchaseOrder::$statuses)->required(),
                        Forms\Components\DatePicker::make('received_date')->label('Received Date')->visible(fn($get) => $get('status') === 'received'),
                    ])
                    ->fillForm(fn(PurchaseOrder $record) => ['status' => $record->status])
                    ->action(function (array $data, PurchaseOrder $record): void {
                        $updates = ['status' => $data['status']];
                        if ($data['status'] === 'approved')
                            $updates['approved_by'] = auth()->id();
                        if (!empty($data['received_date']))
                            $updates['received_date'] = $data['received_date'];
                        $record->update($updates);
                        Notification::make()->title('PO status updated')->success()->send();
                    }),

                \Filament\Actions\Action::make('edit')
                    ->icon('heroicon-o-pencil')
                    ->modalWidth('3xl')
                    ->schema([
                        Section::make('PO Details')->schema([
                            Forms\Components\TextInput::make('po_number')->required(),
                            Forms\Components\Select::make('supplier_id')->label('Supplier')
                                ->options(fn() => Supplier::where('company_id', $this->record->company_id)->where('is_active', true)->pluck('name', 'id'))->searchable(),
                            Forms\Components\Select::make('status')->options(PurchaseOrder::$statuses)->required(),
                            Forms\Components\DatePicker::make('order_date')->required(),
                            Forms\Components\DatePicker::make('expected_date'),
                            Forms\Components\DatePicker::make('received_date'),
                        ])->columns(2),
                        Section::make('Financials')->schema([
                            Forms\Components\TextInput::make('subtotal')->numeric()->prefix('$'),
                            Forms\Components\TextInput::make('tax_amount')->numeric()->prefix('$'),
                            Forms\Components\TextInput::make('shipping_cost')->numeric()->prefix('$'),
                            Forms\Components\TextInput::make('total_amount')->numeric()->prefix('$'),
                        ])->columns(2),
                        Section::make('Notes')->schema([
                            Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
                        ])->collapsed(),
                    ])
                    ->fillForm(fn(PurchaseOrder $record) => $record->toArray())
                    ->action(function (array $data, PurchaseOrder $record): void {
                        $record->update($data);
                        Notification::make()->title('Purchase Order updated')->success()->send();
                    }),

                \Filament\Actions\Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn(PurchaseOrder $record) => $record->delete()),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Purchase Orders')
            ->emptyStateDescription('Create purchase orders for project materials.')
            ->emptyStateIcon('heroicon-o-cube');
    }
}
