<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Support\CurrencyHelper;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class InventoryPage extends BaseModulePage implements HasTable
{
    use InteractsWithTable;

    protected static string $moduleCode = 'inventory';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Inventory';
    protected static ?string $title = 'Inventory & Procurement';
    protected string $view = 'filament.app.pages.modules.inventory';

    public function getStats(): array
    {
        $companyId = $this->record->company_id;
        $totalPOs = PurchaseOrder::where('company_id', $companyId)->count();
        $pendingPOs = PurchaseOrder::where('company_id', $companyId)
            ->whereIn('status', ['submitted', 'approved', 'ordered'])
            ->count();
        $totalValue = PurchaseOrder::where('company_id', $companyId)->sum('total_amount');

        return [
            [
                'label' => 'Purchase Orders',
                'value' => $totalPOs,
                'sub' => $pendingPOs . ' pending',
                'sub_type' => $pendingPOs > 0 ? 'warning' : 'success',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" /></svg>'
            ],
            [
                'label' => 'Total Spend',
                'value' => CurrencyHelper::format($totalValue, 0),
                'sub' => 'All purchase orders',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#ecfdf5'
            ],
            [
                'label' => 'Awaiting Delivery',
                'value' => PurchaseOrder::where('company_id', $companyId)
                    ->whereIn('status', ['ordered', 'partially_received'])
                    ->count(),
                'sub' => 'In transit',
                'sub_type' => 'info',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2563eb" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.079-.481 1.09-1.101.005-.163.009-.364.009-.527V8.284c0-2.152-1.477-4.028-3.567-4.478A36.127 36.127 0 0012 3c-1.21 0-2.403.084-3.567.206-2.09.45-3.567 2.326-3.567 4.478V15.75c0 .63.504 1.126 1.126 1.126H5.25" /></svg>',
                'icon_bg' => '#eff6ff'
            ],
        ];
    }

    protected function getPurchaseOrderFormSchema(): array
    {
        $companyId = $this->record->company_id;

        return [
            Section::make('Order Details')->schema([
                Forms\Components\TextInput::make('po_number')
                    ->label('PO Number')
                    ->required()
                    ->default(fn() => 'PO-' . str_pad((string) (PurchaseOrder::where('company_id', $companyId)->count() + 1), 5, '0', STR_PAD_LEFT)),
                Forms\Components\Select::make('supplier_id')
                    ->label('Supplier')
                    ->options(Supplier::where('company_id', $companyId)->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options(PurchaseOrder::$statuses)
                    ->required()
                    ->default('draft'),
                Forms\Components\DatePicker::make('order_date')
                    ->required()
                    ->default(now()),
                Forms\Components\DatePicker::make('expected_date')
                    ->label('Expected Delivery'),
                Forms\Components\DatePicker::make('received_date')
                    ->label('Received Date'),
            ])->columns(3),

            Section::make('Amounts')->schema([
                Forms\Components\TextInput::make('subtotal')
                    ->numeric()
                    ->prefix('$')
                    ->default(0),
                Forms\Components\TextInput::make('tax_amount')
                    ->numeric()
                    ->prefix('$')
                    ->default(0)
                    ->label('Tax'),
                Forms\Components\TextInput::make('shipping_cost')
                    ->numeric()
                    ->prefix('$')
                    ->default(0)
                    ->label('Shipping'),
                Forms\Components\TextInput::make('total_amount')
                    ->numeric()
                    ->prefix('$')
                    ->default(0)
                    ->required()
                    ->label('Total'),
            ])->columns(4),

            Section::make('Notes')->schema([
                Forms\Components\Textarea::make('notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ])->collapsed(),
        ];
    }

    public function table(Table $table): Table
    {
        $companyId = $this->record->company_id;

        return $table
            ->query(PurchaseOrder::query()->where('company_id', $companyId))
            ->columns([
                Tables\Columns\TextColumn::make('po_number')->label('PO #')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')->searchable()->label('Supplier'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) {
                        'received' => 'success',
                        'ordered' => 'info',
                        'approved' => 'primary',
                        'submitted' => 'warning',
                        'partially_received' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_amount')
                    ->formatStateUsing(CurrencyHelper::formatter())
                    ->sortable()
                    ->label('Total'),
                Tables\Columns\TextColumn::make('order_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('expected_date')->date()->label('Expected')
                    ->color(fn($record) => $record->expected_date?->isPast() && !in_array($record->status, ['received', 'cancelled']) ? 'danger' : null),
                Tables\Columns\TextColumn::make('creator.name')->label('Created By'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(PurchaseOrder::$statuses),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New Purchase Order')
                    ->icon('heroicon-o-plus')
                    ->schema($this->getPurchaseOrderFormSchema())
                    ->mutateDataUsing(function (array $data) use ($companyId): array {
                        $data['company_id'] = $companyId;
                        $data['created_by'] = auth()->id();
                        return $data;
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->schema($this->getPurchaseOrderFormSchema()),
                EditAction::make()
                    ->schema($this->getPurchaseOrderFormSchema()),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Purchase Orders')
            ->emptyStateDescription('No purchase orders have been created yet.')
            ->emptyStateIcon('heroicon-o-shopping-cart');
    }
}
