<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\InvoiceResource\Pages;
use App\Filament\Concerns\UIStandards;
use App\Models\Invoice;
use App\Support\CurrencyHelper;
use Filament\Actions;
use Filament\Infolists;
use Filament\Schemas;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static string|\UnitEnum|null $navigationGroup = 'Work Orders';
    protected static ?string $navigationLabel = 'Invoices';
    protected static ?int $navigationSort = 3;
    protected static bool $shouldRegisterNavigation = false;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('company_id', auth()->user()?->company_id);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Invoice Details')->schema([
                Infolists\Components\TextEntry::make('invoice_number')
                    ->label('Invoice #')
                    ->icon('heroicon-o-hashtag')
                    ->copyable(),
                Infolists\Components\TextEntry::make('client.name')
                    ->label('Client')
                    ->icon('heroicon-o-user'),
                Infolists\Components\TextEntry::make('workOrder.wo_number')
                    ->label('Work Order')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn(string $state): string => UIStandards::statusColor($state)),
                Infolists\Components\TextEntry::make('issue_date')
                    ->date(UIStandards::DATE_FORMAT)
                    ->icon('heroicon-o-calendar'),
                Infolists\Components\TextEntry::make('due_date')
                    ->date(UIStandards::DATE_FORMAT)
                    ->placeholder(UIStandards::PLACEHOLDER_DATE),
            ])->columns(2),

            Schemas\Components\Section::make('Amounts')->schema([
                Infolists\Components\TextEntry::make('subtotal')
                    ->formatStateUsing(CurrencyHelper::formatter()),
                Infolists\Components\TextEntry::make('tax_rate')
                    ->suffix('%')
                    ->placeholder('0'),
                Infolists\Components\TextEntry::make('tax_amount')
                    ->formatStateUsing(CurrencyHelper::formatter()),
                Infolists\Components\TextEntry::make('discount_amount')
                    ->label('Discount')
                    ->formatStateUsing(CurrencyHelper::formatter()),
                Infolists\Components\TextEntry::make('total_amount')
                    ->label('Total')
                    ->formatStateUsing(CurrencyHelper::formatter())
                    ->weight('bold'),
                Infolists\Components\TextEntry::make('amount_paid')
                    ->label('Paid')
                    ->formatStateUsing(CurrencyHelper::formatter()),
            ])->columns(3),

            Schemas\Components\Section::make('Notes')->schema([
                Infolists\Components\TextEntry::make('notes')
                    ->columnSpanFull()
                    ->placeholder('No notes.'),
                Infolists\Components\TextEntry::make('created_at')
                    ->label('Created')
                    ->dateTime(UIStandards::DATETIME_FORMAT),
            ])->collapsible(),
        ]);
    }

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

            Schemas\Components\Section::make('Line Items')->schema([
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\TextInput::make('description')->required()->columnSpan(3),
                        Forms\Components\TextInput::make('quantity')->numeric()->default(1)->minValue(1)->columnSpan(1),
                        Forms\Components\TextInput::make('unit')->placeholder('pcs, hrs…')->maxLength(20)->columnSpan(1),
                        Forms\Components\TextInput::make('unit_price')->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->default(0)->columnSpan(1),
                        Forms\Components\TextInput::make('amount')->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->default(0)->readOnly()->columnSpan(1),
                    ])
                    ->columns(7)
                    ->defaultItems(0)
                    ->addActionLabel('+ Add Line Item')
                    ->reorderable()
                    ->collapsible()
                    ->columnSpanFull(),
            ])->collapsible(),

            Schemas\Components\Section::make('Amounts')->schema([
                Forms\Components\TextInput::make('subtotal')->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->default(0),
                Forms\Components\TextInput::make('tax_rate')->numeric()->suffix('%')->default(0),
                Forms\Components\TextInput::make('tax_amount')->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->default(0),
                Forms\Components\TextInput::make('discount_amount')->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->default(0),
                Forms\Components\TextInput::make('total_amount')->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->default(0),
                Forms\Components\TextInput::make('amount_paid')->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())->default(0),
            ])->columns(3),

            Schemas\Components\Section::make('Notes & Terms')->schema([
                Forms\Components\Textarea::make('notes')->rows(3),
                Forms\Components\Textarea::make('terms_and_conditions')->label('Terms & Conditions')->rows(3),
            ])->columns(2)->collapsible()->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')->label('Invoice #')->searchable()->sortable()->weight('bold')->color('primary'),
                Tables\Columns\TextColumn::make('client.name')->searchable()->limit(UIStandards::LIMIT_NAME),
                Tables\Columns\TextColumn::make('workOrder.wo_number')->label('Work Order'),
                Tables\Columns\TextColumn::make('items_count')->label('Items')->counts('items')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('total_amount')->formatStateUsing(CurrencyHelper::formatter())->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('amount_paid')->formatStateUsing(CurrencyHelper::formatter()),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => UIStandards::statusColor($state)),
                Tables\Columns\TextColumn::make('issue_date')->date(UIStandards::DATE_FORMAT)->sortable(),
                Tables\Columns\TextColumn::make('due_date')->date(UIStandards::DATE_FORMAT)->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Invoice::$statuses),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\Action::make('print')
                    ->icon('heroicon-o-printer')->color('gray')
                    ->url(fn(Invoice $record) => route('print.invoice', $record), shouldOpenInNewTab: true),
            ])
            ->bulkActions([Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
