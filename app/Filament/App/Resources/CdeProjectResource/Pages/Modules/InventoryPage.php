<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
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
    protected static ?string $title = 'Inventory & Purchase Orders';
    protected string $view = 'filament.app.pages.modules.inventory';

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
        $base = PurchaseOrder::where('cde_project_id', $pid);
        $total = (clone $base)->count();
        $pending = (clone $base)->whereIn('status', ['draft', 'submitted'])->count();
        $ordered = (clone $base)->whereIn('status', ['approved', 'ordered', 'partially_received'])->count();
        $totalVal = (clone $base)->sum('total_amount');

        return [
            [
                'label' => 'Total POs',
                'value' => $total,
                'sub' => $pending . ' pending',
                'sub_type' => $pending > 0 ? 'warning' : 'success',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>'
            ],
            [
                'label' => 'Active Orders',
                'value' => $ordered,
                'sub' => 'In procurement',
                'sub_type' => 'info',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#3b82f6" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>',
                'icon_bg' => '#eff6ff'
            ],
            [
                'label' => 'Total Value',
                'value' => CurrencyHelper::format($totalVal, 0),
                'sub' => 'All purchase orders',
                'sub_type' => 'neutral',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#6366f1" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#eef2ff'
            ],
        ];
    }

    private function poFormSchema(bool $isCreate = false): array
    {
        $cid = $this->cid();
        return [
            Section::make('Purchase Order Details')->schema([
                Forms\Components\TextInput::make('po_number')->label('PO Number')
                    ->default(fn() => $isCreate ? 'PO-' . str_pad((string) (PurchaseOrder::where('cde_project_id', $this->pid())->count() + 1), 5, '0', STR_PAD_LEFT) : null)
                    ->required()->maxLength(50),
                Forms\Components\Select::make('supplier_id')->label('Supplier')
                    ->options(fn() => Supplier::where('company_id', $cid)->where('is_active', true)->pluck('name', 'id'))
                    ->searchable()->preload()->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')->required()->maxLength(255),
                        Forms\Components\TextInput::make('email')->email()->maxLength(255),
                        Forms\Components\TextInput::make('phone')->tel()->maxLength(50),
                        Forms\Components\TextInput::make('contact_person')->maxLength(255),
                    ])
                    ->createOptionUsing(fn(array $data) => Supplier::create(array_merge($data, ['company_id' => $cid, 'is_active' => true]))->id),
                Forms\Components\Select::make('status')->options(PurchaseOrder::$statuses)->required()->default('draft'),
                Forms\Components\DatePicker::make('order_date')->default(now()),
                Forms\Components\DatePicker::make('expected_date')->label('Expected Delivery'),
                Forms\Components\DatePicker::make('received_date')->label('Received Date')->visible(!$isCreate),
            ])->columns(2),
            Section::make('Amounts')->schema([
                Forms\Components\TextInput::make('subtotal')->numeric()->prefix('$')->default(0),
                Forms\Components\TextInput::make('tax_amount')->label('Tax')->numeric()->prefix('$')->default(0),
                Forms\Components\TextInput::make('shipping_cost')->label('Shipping')->numeric()->prefix('$')->default(0),
                Forms\Components\TextInput::make('total_amount')->label('Total')->numeric()->prefix('$')->default(0),
            ])->columns(4),
            Section::make('Notes')->schema([
                Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
            ])->collapsed(!$isCreate),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createPO')
                ->label('New Purchase Order')->icon('heroicon-o-plus-circle')->color('primary')
                ->modalWidth('4xl')
                ->schema($this->poFormSchema(isCreate: true))
                ->action(function (array $data): void {
                    $data['company_id'] = $this->cid();
                    $data['cde_project_id'] = $this->pid();
                    $data['created_by'] = auth()->id();
                    PurchaseOrder::create($data);
                    Notification::make()->title('Purchase Order created')->success()->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(PurchaseOrder::query()->where('cde_project_id', $this->pid())->with(['supplier', 'creator', 'items']))
            ->columns([
                Tables\Columns\TextColumn::make('po_number')->label('PO #')->searchable()->sortable()->weight('bold')
                    ->icon('heroicon-o-shopping-cart')->copyable(),
                Tables\Columns\TextColumn::make('supplier.name')->label('Supplier')->searchable()->sortable()->placeholder('—'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) { 'received' => 'success', 'ordered' => 'info', 'approved' => 'primary', 'partially_received' => 'warning', 'cancelled' => 'danger', default => 'gray'})->sortable(),
                Tables\Columns\TextColumn::make('order_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('expected_date')->label('Expected')->date()->sortable()
                    ->color(fn($record) => $record->expected_date?->isPast() && !in_array($record->status, ['received', 'cancelled']) ? 'danger' : null),
                Tables\Columns\TextColumn::make('items_count')->label('Items')->counts('items')->sortable(),
                Tables\Columns\TextColumn::make('total_amount')->label('Total')->money('USD')->sortable(),
                Tables\Columns\TextColumn::make('creator.name')->label('Created By')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M d, Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(PurchaseOrder::$statuses)->multiple(),
                Tables\Filters\SelectFilter::make('supplier_id')->label('Supplier')
                    ->options(fn() => Supplier::where('company_id', $this->cid())->pluck('name', 'id')),
            ])
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                \Filament\Actions\Action::make('manageItems')
                    ->label('Items')->icon('heroicon-o-list-bullet')->color('info')->modalWidth('5xl')
                    ->modalHeading(fn(PurchaseOrder $record) => 'Line Items — ' . $record->po_number)
                    ->schema([
                        Forms\Components\Repeater::make('items')->schema([
                            Forms\Components\Select::make('product_id')->label('Product')
                                ->options(fn() => Product::where('company_id', $this->cid())->where('is_active', true)->pluck('name', 'id'))
                                ->searchable()->nullable()->columnSpan(2),
                            Forms\Components\TextInput::make('notes')->label('Description')->columnSpan(2),
                            Forms\Components\TextInput::make('quantity_ordered')->label('Qty Ordered')->numeric()->required()->default(1)->columnSpan(1),
                            Forms\Components\TextInput::make('quantity_received')->label('Qty Received')->numeric()->default(0)->columnSpan(1),
                            Forms\Components\TextInput::make('unit_price')->label('Unit Price')->numeric()->prefix('$')->required()->default(0)->columnSpan(1),
                            Forms\Components\TextInput::make('total_price')->label('Total')->numeric()->prefix('$')->disabled()->dehydrated()->columnSpan(1),
                        ])->columns(8)->addActionLabel('Add Item')->defaultItems(0)->reorderable()
                            ->itemLabel(fn(array $state): ?string => ($state['notes'] ?? 'Item') . ' — $' . number_format($state['total_price'] ?? 0, 2)),
                    ])
                    ->fillForm(fn(PurchaseOrder $record) => [
                        'items' => $record->items->map(fn($i) => [
                            'product_id' => $i->product_id,
                            'notes' => $i->notes,
                            'quantity_ordered' => $i->quantity_ordered,
                            'quantity_received' => $i->quantity_received,
                            'unit_price' => $i->unit_price,
                            'total_price' => $i->total_price,
                        ])->toArray(),
                    ])
                    ->action(function (array $data, PurchaseOrder $record): void {
                        $record->items()->delete();
                        $subtotal = 0;
                        foreach ($data['items'] ?? [] as $item) {
                            $item['total_price'] = round(($item['quantity_ordered'] ?? 0) * ($item['unit_price'] ?? 0), 2);
                            $subtotal += $item['total_price'];
                            $record->items()->create($item);
                        }
                        $record->update(['subtotal' => $subtotal, 'total_amount' => $subtotal + ($record->tax_amount ?? 0) + ($record->shipping_cost ?? 0)]);
                        Notification::make()->title('Items updated — Subtotal: ' . CurrencyHelper::format($subtotal))->success()->send();
                    }),

                \Filament\Actions\Action::make('updateStatus')
                    ->label('Status')->icon('heroicon-o-arrow-path')->color('warning')
                    ->schema([Forms\Components\Select::make('status')->options(PurchaseOrder::$statuses)->required()])
                    ->fillForm(fn(PurchaseOrder $record) => ['status' => $record->status])
                    ->action(function (array $data, PurchaseOrder $record): void {
                        $updates = ['status' => $data['status']];
                        if ($data['status'] === 'received')
                            $updates['received_date'] = now();
                        $record->update($updates);
                        Notification::make()->title('Status → ' . PurchaseOrder::$statuses[$data['status']])->success()->send();
                    }),

                \Filament\Actions\Action::make('edit')
                    ->icon('heroicon-o-pencil')->modalWidth('4xl')
                    ->schema($this->poFormSchema())
                    ->fillForm(fn(PurchaseOrder $record) => $record->toArray())
                    ->action(function (array $data, PurchaseOrder $record): void {
                        $record->update($data);
                        Notification::make()->title('PO updated')->success()->send();
                    }),

                \Filament\Actions\Action::make('delete')
                    ->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                    ->action(fn(PurchaseOrder $record) => $record->delete()),
                ]),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('bulkStatus')->label('Update Status')->icon('heroicon-o-arrow-path')
                        ->schema([Forms\Components\Select::make('status')->options(PurchaseOrder::$statuses)->required()])
                        ->action(function (array $data, $records): void {
                            foreach ($records as $r)
                                $record->update(['status' => $data['status']]);
                            Notification::make()->title($records->count() . ' POs updated')->success()->send();
                        })->deselectRecordsAfterCompletion(),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Purchase Orders')
            ->emptyStateDescription('Create purchase orders to track inventory procurement.')
            ->emptyStateIcon('heroicon-o-cube')
            ->striped()->paginated([10, 25, 50]);
    }
}
