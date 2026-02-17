<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\AssetResource\Pages;
use App\Models\Asset;
use App\Support\CurrencyHelper;
use Filament\Actions;
use Filament\Infolists;
use Filament\Schemas;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-server-stack';
    protected static string|\UnitEnum|null $navigationGroup = 'Company';
    protected static ?int $navigationSort = 2;

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Asset Details')->schema([
                Infolists\Components\TextEntry::make('asset_id')
                    ->label('Asset ID')
                    ->icon('heroicon-o-hashtag')
                    ->copyable(),
                Infolists\Components\TextEntry::make('name')
                    ->icon('heroicon-o-cube'),
                Infolists\Components\TextEntry::make('category')
                    ->badge(),
                Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success', 'maintenance' => 'warning',
                        'inactive' => 'gray', 'retired' => 'danger', default => 'gray',
                    }),
                Infolists\Components\TextEntry::make('condition')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'excellent' => 'success', 'good' => 'info',
                        'fair' => 'warning', 'poor' => 'danger', default => 'gray',
                    })
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('client.name')
                    ->label('Client')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('location')
                    ->icon('heroicon-o-map-pin')
                    ->placeholder('—'),
                Infolists\Components\ImageEntry::make('image'),
            ])->columns(2),

            Schemas\Components\Section::make('Specifications')->schema([
                Infolists\Components\TextEntry::make('brand')->placeholder('—'),
                Infolists\Components\TextEntry::make('model_number')
                    ->label('Model')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('serial_number')
                    ->label('Serial #')
                    ->copyable()
                    ->placeholder('—'),
            ])->columns(3)->collapsible(),

            Schemas\Components\Section::make('Purchase & Warranty')->schema([
                Infolists\Components\TextEntry::make('purchase_date')
                    ->date()
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('purchase_cost')
                    ->formatStateUsing(CurrencyHelper::formatter())
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('warranty_expires_at')
                    ->label('Warranty Expires')
                    ->date()
                    ->placeholder('—'),
            ])->columns(3)->collapsible(),

            Schemas\Components\Section::make('Notes')->schema([
                Infolists\Components\TextEntry::make('notes')
                    ->columnSpanFull()
                    ->placeholder('No notes.'),
                Infolists\Components\TextEntry::make('created_at')
                    ->label('Added')
                    ->dateTime(),
            ])->collapsible(),
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Asset Details')->schema([
                Forms\Components\TextInput::make('asset_id')->label('Asset ID'),
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\Select::make('category')
                    ->options(['hvac' => 'HVAC', 'electrical' => 'Electrical', 'plumbing' => 'Plumbing', 'mechanical' => 'Mechanical', 'it_equipment' => 'IT Equipment', 'vehicle' => 'Vehicle', 'other' => 'Other'])
                    ->searchable(),
                Forms\Components\TextInput::make('brand'),
                Forms\Components\TextInput::make('model_number'),
                Forms\Components\TextInput::make('serial_number'),
                Forms\Components\TextInput::make('location'),
                Forms\Components\Select::make('client_id')->relationship('client', 'name')->searchable()->preload(),
                Forms\Components\Select::make('status')->options(Asset::$statuses)->default('active'),
                Forms\Components\Select::make('condition')
                    ->options(['excellent' => 'Excellent', 'good' => 'Good', 'fair' => 'Fair', 'poor' => 'Poor']),
                Forms\Components\DatePicker::make('purchase_date'),
                Forms\Components\TextInput::make('purchase_cost')->numeric()->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix()),
                Forms\Components\DatePicker::make('warranty_expires_at')->label('Warranty Expires'),
                Forms\Components\FileUpload::make('image')->image()->directory('assets/images'),
                Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset_id')->label('ID')->searchable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category')->badge(),
                Tables\Columns\TextColumn::make('client.name')->label('Client'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'active' => 'success', 'maintenance' => 'warning',
                        'inactive' => 'gray', 'retired' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('location'),
                Tables\Columns\TextColumn::make('warranty_expires_at')->date()->label('Warranty'),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Asset::$statuses),
                Tables\Filters\SelectFilter::make('client_id')->relationship('client', 'name')->label('Client'),
            ])
            ->actions([Actions\ViewAction::make(), Actions\EditAction::make()])
            ->bulkActions([Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}
