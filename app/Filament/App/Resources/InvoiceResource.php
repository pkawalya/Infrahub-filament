<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Actions;
use Filament\Schemas;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-currency-dollar';
    protected static string|\UnitEnum|null $navigationGroup = 'Work Orders';
    protected static ?int $navigationSort = 3;
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Invoice Details')->schema([
                Forms\Components\TextInput::make('invoice_number')
                    ->default(fn() => 'INV-' . str_pad(Invoice::withoutGlobalScopes()->count() + 1, 5, '0', STR_PAD_LEFT))
                    ->disabled()->dehydrated(),
                Forms\Components\Select::make('client_id')
                    ->relationship('client', 'name')->searchable()->preload()->required(),
                Forms\Components\Select::make('work_order_id')
                    ->relationship('workOrder', 'wo_number')->searchable()->preload(),
                Forms\Components\Select::make('status')
                    ->options(Invoice::$statuses)->default('draft')->required(),
                Forms\Components\DatePicker::make('issue_date')->default(now()),
                Forms\Components\DatePicker::make('due_date'),
            ])->columns(2),

            Schemas\Components\Section::make('Amounts')->schema([
                Forms\Components\TextInput::make('subtotal')->numeric()->prefix('$')->default(0),
                Forms\Components\TextInput::make('tax_rate')->numeric()->suffix('%')->default(0),
                Forms\Components\TextInput::make('tax_amount')->numeric()->prefix('$')->default(0),
                Forms\Components\TextInput::make('discount_amount')->numeric()->prefix('$')->default(0),
                Forms\Components\TextInput::make('total_amount')->numeric()->prefix('$')->default(0),
                Forms\Components\TextInput::make('amount_paid')->numeric()->prefix('$')->default(0),
            ])->columns(3),

            Schemas\Components\Section::make('Notes')->schema([
                Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
            ])->collapsible()->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')->label('Invoice #')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('client.name')->searchable(),
                Tables\Columns\TextColumn::make('workOrder.wo_number')->label('Work Order'),
                Tables\Columns\TextColumn::make('total_amount')->money('USD')->sortable(),
                Tables\Columns\TextColumn::make('amount_paid')->money('USD'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'paid' => 'success', 'sent' => 'info', 'partially_paid' => 'warning',
                        'overdue' => 'danger', 'cancelled' => 'gray', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('issue_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('due_date')->date()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Invoice::$statuses),
            ])
            ->actions([Actions\ViewAction::make(), Actions\EditAction::make()])
            ->bulkActions([Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
