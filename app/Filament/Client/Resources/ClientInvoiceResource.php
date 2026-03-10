<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\ClientInvoiceResource\Pages;
use App\Models\Invoice;
use App\Support\CurrencyHelper;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientInvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Invoices';
    protected static ?string $modelLabel = 'Invoice';
    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return false;
    }
    public static function canEdit($record): bool
    {
        return false;
    }
    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('company_id', auth()->user()?->company_id)
            ->whereIn('status', ['sent', 'partially_paid', 'paid', 'overdue']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')->searchable()->weight('bold')->color('primary'),
                Tables\Columns\TextColumn::make('project.name')->label('Project')->limit(20),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn(string $s) => match ($s) {
                    'sent' => 'info', 'partially_paid' => 'warning', 'paid' => 'success', 'overdue' => 'danger', default => 'gray'
                }),
                Tables\Columns\TextColumn::make('total_amount')->formatStateUsing(CurrencyHelper::formatter())->weight('bold'),
                Tables\Columns\TextColumn::make('amount_paid')->formatStateUsing(CurrencyHelper::formatter()),
                Tables\Columns\TextColumn::make('issue_date')->date('M d, Y'),
                Tables\Columns\TextColumn::make('due_date')->date('M d, Y')
                    ->color(fn($record) => $record->due_date?->isPast() && $record->status !== 'paid' ? 'danger' : null),
            ])
            ->defaultSort('issue_date', 'desc')
            ->actions([Actions\ViewAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientInvoices::route('/'),
        ];
    }
}
