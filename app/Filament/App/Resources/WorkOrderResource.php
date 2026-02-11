<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\WorkOrderResource\Pages;
use App\Models\WorkOrder;
use Filament\Actions;
use Filament\Schemas;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WorkOrderResource extends Resource
{
    protected static ?string $model = WorkOrder::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static string|\UnitEnum|null $navigationGroup = 'Work Orders';
    protected static ?int $navigationSort = 1;
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Work Order Details')->schema([
                Forms\Components\TextInput::make('wo_number')
                    ->label('WO #')
                    ->default(fn() => 'WO-' . str_pad(WorkOrder::withoutGlobalScopes()->count() + 1, 5, '0', STR_PAD_LEFT))
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('title')->required()->maxLength(255),
                Forms\Components\Select::make('work_order_type_id')
                    ->relationship('type', 'name')
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\ColorPicker::make('color'),
                    ]),
                Forms\Components\Select::make('priority')
                    ->options(WorkOrder::$priorities)
                    ->default('medium')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options(WorkOrder::$statuses)
                    ->default('pending')
                    ->required(),
                Forms\Components\Select::make('client_id')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('asset_id')
                    ->relationship('asset', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('assigned_to')
                    ->relationship('assignee', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\DatePicker::make('due_date'),
                Forms\Components\DatePicker::make('preferred_date'),
                Forms\Components\TimePicker::make('preferred_time'),
                Forms\Components\RichEditor::make('description')->columnSpanFull(),
                Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('wo_number')->label('WO #')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('type.name')->label('Type')->badge(),
                Tables\Columns\TextColumn::make('client.name')->label('Client')->searchable(),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'urgent' => 'danger', 'high' => 'warning',
                        'medium' => 'info', 'low' => 'gray', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'completed' => 'success', 'in_progress' => 'info',
                        'on_hold' => 'warning', 'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('assignee.name')->label('Assigned To'),
                Tables\Columns\TextColumn::make('due_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(WorkOrder::$statuses),
                Tables\Filters\SelectFilter::make('priority')->options(WorkOrder::$priorities),
                Tables\Filters\SelectFilter::make('work_order_type_id')
                    ->relationship('type', 'name')->label('Type'),
                Tables\Filters\SelectFilter::make('assigned_to')
                    ->relationship('assignee', 'name')->label('Assignee'),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkOrders::route('/'),
            'create' => Pages\CreateWorkOrder::route('/create'),
            'edit' => Pages\EditWorkOrder::route('/{record}/edit'),
        ];
    }
}
