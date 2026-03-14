<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\WorkOrderResource\Pages;
use App\Filament\Concerns\UIStandards;
use App\Models\WorkOrder;
use Filament\Actions;
use Filament\Infolists;
use Filament\Schemas;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WorkOrderResource extends Resource
{
    protected static ?string $model = WorkOrder::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static string|\UnitEnum|null $navigationGroup = 'Work Orders';
    protected static ?string $navigationLabel = 'Work Orders';
    protected static ?int $navigationSort = 1;
    protected static bool $shouldRegisterNavigation = false;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('company_id', auth()->user()?->company_id);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Work Order Details')->schema([
                Infolists\Components\TextEntry::make('wo_number')
                    ->label('WO #')
                    ->icon('heroicon-o-hashtag')
                    ->copyable(),
                Infolists\Components\TextEntry::make('title')
                    ->icon('heroicon-o-document-text'),
                Infolists\Components\TextEntry::make('type.name')
                    ->label('Type')
                    ->badge(),
                Infolists\Components\TextEntry::make('priority')
                    ->badge()
                    ->color(fn(string $state): string => UIStandards::priorityColor($state)),
                Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn(string $state): string => UIStandards::statusColor($state)),
                Infolists\Components\TextEntry::make('client.name')
                    ->label('Client')
                    ->icon('heroicon-o-user')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('asset.name')
                    ->label('Asset')
                    ->icon('heroicon-o-cube')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('assignee.name')
                    ->label('Assigned To')
                    ->icon('heroicon-o-user-circle')
                    ->placeholder('Unassigned'),
            ])->columns(2),

            Schemas\Components\Section::make('Schedule')->schema([
                Infolists\Components\TextEntry::make('due_date')
                    ->date(UIStandards::DATE_FORMAT)
                    ->icon('heroicon-o-calendar')
                    ->placeholder(UIStandards::PLACEHOLDER_DATE),
                Infolists\Components\TextEntry::make('preferred_date')
                    ->date(UIStandards::DATE_FORMAT)
                    ->placeholder(UIStandards::PLACEHOLDER_DATE),
                Infolists\Components\TextEntry::make('preferred_time')
                    ->time()
                    ->placeholder(UIStandards::PLACEHOLDER_SHORT),
                Infolists\Components\TextEntry::make('started_at')
                    ->dateTime(UIStandards::DATETIME_FORMAT)
                    ->placeholder('Not started'),
                Infolists\Components\TextEntry::make('completed_at')
                    ->dateTime(UIStandards::DATETIME_FORMAT)
                    ->placeholder('Not completed'),
                Infolists\Components\TextEntry::make('created_at')
                    ->label('Created')
                    ->dateTime(UIStandards::DATETIME_FORMAT),
            ])->columns(3),

            Schemas\Components\Section::make('Description & Notes')->schema([
                Infolists\Components\TextEntry::make('description')
                    ->html()
                    ->columnSpanFull()
                    ->placeholder('No description provided.'),
                Infolists\Components\TextEntry::make('notes')
                    ->columnSpanFull()
                    ->placeholder('No notes.'),
            ])->collapsible(),
        ]);
    }

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

            // ── Testing & Inspection Plan (TIP) ──
            Schemas\Components\Section::make('🔍 Testing & Inspection')
                ->icon('heroicon-o-clipboard-document-check')
                ->description('For quality assurance inspection and testing work orders')
                ->schema([
                    Forms\Components\Toggle::make('is_inspection')
                        ->label('This is a Testing & Inspection work order')
                        ->reactive()
                        ->columnSpanFull(),

                    Schemas\Components\Fieldset::make('Inspection Details')
                        ->schema([
                            Forms\Components\Select::make('inspection_type')
                                ->label('Inspection Type')
                                ->options(WorkOrder::$inspectionTypes)
                                ->searchable(),
                            Forms\Components\Select::make('hold_point')
                                ->label('Hold Point')
                                ->options(WorkOrder::$holdPoints),
                            Forms\Components\TextInput::make('equipment_tested')
                                ->label('Equipment / Tag No.'),
                            Forms\Components\TextInput::make('method_statement_ref')
                                ->label('Method Statement Ref'),
                            Forms\Components\Textarea::make('acceptance_criteria')
                                ->label('Acceptance Criteria')
                                ->rows(2)
                                ->columnSpanFull(),
                            Forms\Components\Select::make('test_result')
                                ->label('Test Result')
                                ->options(WorkOrder::$testResults),
                        ])->columns(2)
                        ->visible(fn($get) => $get('is_inspection')),
                ])->collapsed(),

            // ── Commissioning (Energy Projects) ──
            Schemas\Components\Section::make('⚡ Commissioning')
                ->icon('heroicon-o-bolt')
                ->description('For commissioning phase work orders on energy projects')
                ->schema([
                    Forms\Components\Toggle::make('is_commissioning')
                        ->label('This is a Commissioning work order')
                        ->reactive()
                        ->columnSpanFull(),

                    Schemas\Components\Fieldset::make('Commissioning Details')
                        ->schema([
                            Forms\Components\Select::make('commissioning_phase')
                                ->label('Commissioning Phase')
                                ->options(WorkOrder::$commissioningPhases),
                            Forms\Components\TextInput::make('system_tag')
                                ->label('System / Subsystem Tag')
                                ->placeholder('e.g. ELEC-MV-02, SOLAR-INV-01'),
                        ])->columns(2)
                        ->visible(fn($get) => $get('is_commissioning')),
                ])->collapsed(),

            // ── Road Material Testing ──
            Schemas\Components\Section::make('🛣️ Road Material Testing')
                ->icon('heroicon-o-beaker')
                ->description('For CBR, compaction, asphalt core, and other road tests')
                ->schema([
                    Forms\Components\Toggle::make('is_road_test')
                        ->label('This is a Road Material Test')
                        ->reactive()
                        ->columnSpanFull(),

                    Schemas\Components\Fieldset::make('Test Details')
                        ->schema([
                            Forms\Components\Select::make('road_test_type')
                                ->label('Test Type')
                                ->options(WorkOrder::$roadTestTypes)
                                ->searchable(),
                            Forms\Components\TextInput::make('test_chainage')
                                ->label('Chainage')
                                ->placeholder('e.g. 15+200'),
                            Forms\Components\Select::make('test_layer')
                                ->label('Layer Tested')
                                ->options(\App\Models\DailySiteDiary::$roadLayers),
                            Forms\Components\TextInput::make('sample_reference')
                                ->label('Sample Reference'),
                            Forms\Components\TextInput::make('test_lab')
                                ->label('Testing Laboratory'),
                            Forms\Components\TextInput::make('test_value_achieved')
                                ->label('Value Achieved')
                                ->numeric(),
                            Forms\Components\TextInput::make('test_value_required')
                                ->label('Value Required (Spec)')
                                ->numeric(),
                            Forms\Components\TextInput::make('test_unit')
                                ->label('Unit')
                                ->placeholder('%, MPa, mm, kN'),
                        ])->columns(2)
                        ->visible(fn($get) => $get('is_road_test')),
                ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('wo_number')->label('WO #')->searchable()->sortable()->weight('bold')->color('primary'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(UIStandards::LIMIT_TITLE),
                Tables\Columns\TextColumn::make('type.name')->label('Type')->badge(),
                Tables\Columns\TextColumn::make('client.name')->label('Client')->searchable()->limit(UIStandards::LIMIT_NAME),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn(string $state): string => UIStandards::priorityColor($state)),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => UIStandards::statusColor($state)),
                Tables\Columns\TextColumn::make('assignee.name')->label('Assigned To')->limit(UIStandards::LIMIT_NAME),
                Tables\Columns\TextColumn::make('due_date')->date(UIStandards::DATE_FORMAT)->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime(UIStandards::DATETIME_FORMAT)->sortable(),
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
            'view' => Pages\ViewWorkOrder::route('/{record}'),
            'edit' => Pages\EditWorkOrder::route('/{record}/edit'),
        ];
    }
}
