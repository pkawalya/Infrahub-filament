<?php

namespace App\Filament\App\Resources\EquipmentAllocations;

use App\Filament\App\Resources\EquipmentAllocations\Pages\ManageEquipmentAllocations;
use App\Models\Asset;
use App\Models\CdeProject;
use App\Models\EquipmentAllocation;
use App\Models\User;
use App\Support\CurrencyHelper;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;

class EquipmentAllocationResource extends Resource
{
    protected static ?string $model = EquipmentAllocation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';
    protected static string|\UnitEnum|null $navigationGroup = 'Company';
    protected static ?string $navigationLabel = 'Plant Allocations';
    protected static ?string $modelLabel = 'Allocation';
    protected static ?string $pluralModelLabel = 'Plant Allocations';
    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('company_id', auth()->user()?->company_id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Equipment & Project')
                    ->icon('heroicon-o-truck')
                    ->schema([
                        Forms\Components\Select::make('asset_id')
                            ->label('Equipment / Asset')
                            ->relationship('asset', 'name')
                            ->getOptionLabelFromRecordUsing(fn(Asset $record) => "{$record->asset_tag} — {$record->display_name}")
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                                Forms\Components\TextInput::make('asset_tag')->label('Asset Tag')->maxLength(100),
                                Forms\Components\TextInput::make('serial_number')->label('Serial Number')->maxLength(100),
                                Forms\Components\Select::make('status')
                                    ->options(['available' => 'Available', 'in_use' => 'In Use', 'maintenance' => 'Maintenance'])
                                    ->default('available'),
                            ])
                            ->createOptionUsing(fn(array $data) => Asset::create(array_merge($data, [
                                'company_id' => auth()->user()->company_id,
                                'condition'  => 'good',
                            ]))->id),
                        Forms\Components\Select::make('cde_project_id')
                            ->label('Assigned to Project')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\Select::make('operator_id')
                            ->label('Operator')
                            ->relationship('operator', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => '🟢 Active',
                                'completed' => '✅ Completed',
                                'cancelled' => '❌ Cancelled',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),

                Section::make('Schedule & Rates')
                    ->icon('heroicon-o-calendar')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('end_date')
                            ->nullable()
                            ->after('start_date'),
                        Forms\Components\TextInput::make('daily_rate')
                            ->label('Daily Rate')
                            ->numeric()
                            ->prefix(fn() => CurrencyHelper::prefix())
                            ->suffix(fn() => CurrencyHelper::suffix())
                            ->helperText('Internal cross-charge rate per day'),
                    ])->columns(3),

                Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset.display_name')
                    ->label('Equipment')
                    ->searchable(['name', 'asset_tag'])
                    ->description(fn(EquipmentAllocation $r) => $r->asset?->asset_tag)
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project')
                    ->placeholder('— Yard / Unassigned —')
                    ->icon('heroicon-o-rectangle-stack')
                    ->searchable(),
                Tables\Columns\TextColumn::make('operator.name')
                    ->label('Operator')
                    ->placeholder('—')
                    ->icon('heroicon-o-user'),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('End')
                    ->date('M d, Y')
                    ->placeholder('Ongoing')
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->label('Days')
                    ->state(function (EquipmentAllocation $record): string {
                        $end = $record->end_date ?? now();
                        return $record->start_date->diffInDays($end) . 'd';
                    }),
                Tables\Columns\TextColumn::make('daily_rate')
                    ->label('Rate/Day')
                    ->formatStateUsing(CurrencyHelper::formatter())
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('total_cost')
                    ->label('Total Cost')
                    ->state(function (EquipmentAllocation $record): ?float {
                        if (!$record->daily_rate)
                            return null;
                        $end = $record->end_date ?? now();
                        $days = max(1, $record->start_date->diffInDays($end));
                        return $record->daily_rate * $days;
                    })
                    ->formatStateUsing(CurrencyHelper::formatter())
                    ->placeholder('—')
                    ->color('success'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'active' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('start_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('cde_project_id')
                    ->label('Project')
                    ->relationship('project', 'name'),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\Action::make('complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(EquipmentAllocation $record) => $record->status === 'active')
                    ->action(function (EquipmentAllocation $record) {
                        $record->update([
                            'status' => 'completed',
                            'end_date' => $record->end_date ?? now(),
                        ]);
                    }),
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
            'index' => ManageEquipmentAllocations::route('/'),
        ];
    }
}
