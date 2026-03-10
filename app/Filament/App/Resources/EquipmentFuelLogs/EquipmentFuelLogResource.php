<?php

namespace App\Filament\App\Resources\EquipmentFuelLogs;

use App\Filament\App\Resources\EquipmentFuelLogs\Pages\ManageEquipmentFuelLogs;
use App\Models\Asset;
use App\Models\EquipmentFuelLog;
use App\Support\CurrencyHelper;
use App\Support\StoragePath;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EquipmentFuelLogResource extends Resource
{
    protected static ?string $model = EquipmentFuelLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-fire';
    protected static string|\UnitEnum|null $navigationGroup = 'Company';
    protected static ?string $navigationLabel = 'Fuel Logs';
    protected static ?string $modelLabel = 'Fuel Log';
    protected static ?string $pluralModelLabel = 'Fuel Logs';
    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('company_id', auth()->user()?->company_id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Equipment & Date')
                    ->icon('heroicon-o-fire')
                    ->schema([
                        Forms\Components\Select::make('asset_id')
                            ->label('Equipment / Asset')
                            ->relationship('asset', 'name')
                            ->getOptionLabelFromRecordUsing(fn(Asset $record) => "{$record->asset_tag} — {$record->display_name}")
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('cde_project_id')
                            ->label('Project (if on-site)')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\DatePicker::make('log_date')
                            ->label('Date')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextInput::make('meter_reading')
                            ->label('Meter Reading')
                            ->numeric()
                            ->suffix('hrs / km')
                            ->helperText('Odometer or hour-meter at time of fueling'),
                    ])->columns(2),

                Section::make('Fuel Details')
                    ->icon('heroicon-o-beaker')
                    ->schema([
                        Forms\Components\TextInput::make('liters')
                            ->label('Quantity')
                            ->required()
                            ->numeric()
                            ->suffix('liters')
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $cpl = $get('cost_per_liter');
                                if ($state && $cpl) {
                                    $set('total_cost', round((float) $state * (float) $cpl, 2));
                                }
                            }),
                        Forms\Components\TextInput::make('cost_per_liter')
                            ->label('Unit Cost')
                            ->numeric()
                            ->prefix(fn() => CurrencyHelper::prefix())
                            ->suffix(fn() => CurrencyHelper::suffix())
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $liters = $get('liters');
                                if ($state && $liters) {
                                    $set('total_cost', round((float) $state * (float) $liters, 2));
                                }
                            }),
                        Forms\Components\TextInput::make('total_cost')
                            ->label('Total Cost')
                            ->numeric()
                            ->prefix(fn() => CurrencyHelper::prefix())
                            ->suffix(fn() => CurrencyHelper::suffix())
                            ->helperText('Auto-calculated from qty × unit cost'),
                    ])->columns(3),

                Section::make('Additional Info')
                    ->schema([
                        Forms\Components\TextInput::make('filled_by')
                            ->label('Filled By')
                            ->placeholder('Driver / operator name'),
                        Forms\Components\TextInput::make('supplier')
                            ->label('Fuel Supplier')
                            ->placeholder('e.g. Shell, Total'),
                        Forms\Components\FileUpload::make('receipt_path')
                            ->label('Receipt Photo')
                            ->image()
                            ->directory('fuel-receipts')
                            ->maxSize(5120),
                        Forms\Components\Textarea::make('notes')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(2)->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('log_date')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('asset.display_name')
                    ->label('Equipment')
                    ->searchable(['name', 'asset_tag'])
                    ->description(fn(EquipmentFuelLog $r) => $r->asset?->asset_tag)
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project')
                    ->placeholder('— Yard —')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('liters')
                    ->label('Liters')
                    ->suffix(' L')
                    ->sortable()
                    ->color('info'),
                Tables\Columns\TextColumn::make('cost_per_liter')
                    ->label('Rate')
                    ->formatStateUsing(CurrencyHelper::formatter())
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_cost')
                    ->label('Cost')
                    ->formatStateUsing(CurrencyHelper::formatter())
                    ->placeholder('—')
                    ->sortable()
                    ->color('danger')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('meter_reading')
                    ->label('Meter')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('filled_by')
                    ->label('By')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('supplier')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('log_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('asset_id')
                    ->label('Equipment')
                    ->relationship('asset', 'name'),
                Tables\Filters\SelectFilter::make('cde_project_id')
                    ->label('Project')
                    ->relationship('project', 'name'),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEquipmentFuelLogs::route('/'),
        ];
    }
}
