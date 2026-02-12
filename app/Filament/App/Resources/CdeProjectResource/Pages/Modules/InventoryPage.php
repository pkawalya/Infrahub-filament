<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource;
use App\Models\CdeProject;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Support\CurrencyHelper;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class InventoryPage extends ManageRelatedRecords
{
    protected static string $resource = CdeProjectResource::class;
    protected static string $relationship = 'purchaseOrders';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Inventory';
    protected static ?string $title = 'Inventory & Procurement';

    public static function canAccess(array $parameters = []): bool
    {
        $record = $parameters['record'] ?? null;
        if ($record instanceof CdeProject) {
            return $record->hasModule('inventory');
        }
        return true;
    }

    public function form(Schema $schema): Schema
    {
        $companyId = $this->getOwnerRecord()->company_id;
        $projectId = $this->getOwnerRecord()->id;
        return $schema->components([
            Section::make('Purchase Order Details')->schema([
                Forms\Components\TextInput::make('po_number')->label('PO Number')
                    ->default(fn() => 'PO-' . str_pad((string) (PurchaseOrder::where('cde_project_id', $projectId)->count() + 1), 4, '0', STR_PAD_LEFT))
                    ->required()->maxLength(50),
                Forms\Components\Select::make('supplier_id')->label('Supplier')
                    ->options(Supplier::where('company_id', $companyId)->where('is_active', true)->pluck('name', 'id'))->searchable(),
                Forms\Components\Select::make('status')->options(PurchaseOrder::$statuses)->required()->default('draft'),
                Forms\Components\DatePicker::make('order_date')->required()->default(now()),
                Forms\Components\DatePicker::make('expected_date'),
                Forms\Components\DatePicker::make('received_date'),
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
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('po_number')->label('PO #')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')->label('Supplier')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn(string $state) => match ($state) {
                    'approved' => 'success', 'ordered' => 'info', 'draft' => 'gray',
                    'received' => 'primary', 'cancelled' => 'danger', default => 'warning',
                }),
                Tables\Columns\TextColumn::make('total_amount')->formatStateUsing(CurrencyHelper::formatter())->label('Total'),
                Tables\Columns\TextColumn::make('order_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('expected_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(PurchaseOrder::$statuses),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['company_id'] = $this->getOwnerRecord()->company_id;
                        $data['created_by'] = auth()->id();
                        return $data;
                    }),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title;
    }

    public function getBreadcrumbs(): array
    {
        return [
            CdeProjectResource::getUrl() => 'Projects',
            CdeProjectResource::getUrl('view', ['record' => $this->getRecord()]) => $this->getRecord()->name,
            'Inventory',
        ];
    }
}
