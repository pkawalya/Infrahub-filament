<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\DailySiteDiaryResource\Pages;
use App\Models\Company;
use App\Models\DailySiteDiary;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DailySiteDiaryResource extends Resource
{
    protected static ?string $model = DailySiteDiary::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static string|\UnitEnum|null $navigationGroup = 'Projects';
    protected static ?string $navigationLabel = 'Daily Site Diary';
    protected static ?string $modelLabel = 'Site Diary';
    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('company_id', auth()->user()?->company_id);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->whereNull('approved_by')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Date & Project')
                ->icon('heroicon-o-calendar-days')
                ->schema([
                    Forms\Components\Select::make('cde_project_id')
                        ->label('Project')
                        ->relationship('project', 'name', fn($q) => $q->where('company_id', auth()->user()?->company_id))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive(),
                    Forms\Components\DatePicker::make('diary_date')
                        ->label('Date')
                        ->required()
                        ->default(now()),
                    Forms\Components\Select::make('weather')
                        ->options(fn() => Company::options('weather_types'))
                        ->searchable(),
                    Forms\Components\TextInput::make('temperature')
                        ->numeric()
                        ->suffix('°C'),
                ])->columns(4),

            Section::make('Workforce')
                ->icon('heroicon-o-users')
                ->schema([
                    Forms\Components\TextInput::make('workers_on_site')
                        ->label('Own Workers')
                        ->numeric()
                        ->default(0)
                        ->required(),
                    Forms\Components\TextInput::make('subcontractor_workers')
                        ->label('Subcontractor Workers')
                        ->numeric()
                        ->default(0),
                ])->columns(2),

            Section::make('Equipment on Site')
                ->icon('heroicon-o-truck')
                ->schema([
                    Forms\Components\TextInput::make('equipment_on_site')
                        ->label('Equipment Count')
                        ->numeric()
                        ->default(0),
                ])->columns(2),

            Section::make('Work Summary')
                ->icon('heroicon-o-clipboard-document-list')
                ->schema([
                    Forms\Components\Textarea::make('work_performed')
                        ->label('Work Performed Today')
                        ->rows(4)
                        ->columnSpanFull()
                        ->placeholder('Describe the work activities completed today...'),
                    Forms\Components\Textarea::make('work_planned_tomorrow')
                        ->label('Work Planned for Tomorrow')
                        ->rows(3)
                        ->columnSpanFull()
                        ->placeholder('Activities planned for the next working day...'),
                ]),

            // ── Environmental Monitoring (Energy Projects) ──
            Section::make('🌡️ Environmental Monitoring')
                ->icon('heroicon-o-beaker')
                ->description('Environmental readings for energy/EIA compliance')
                ->visible(fn($get) => static::projectTypeIs($get('cde_project_id'), 'energy'))
                ->schema([
                    Forms\Components\TextInput::make('humidity_percent')
                        ->label('Humidity')
                        ->numeric()
                        ->suffix('%'),
                    Forms\Components\TextInput::make('wind_speed_kmh')
                        ->label('Wind Speed')
                        ->numeric()
                        ->suffix('km/h'),
                    Forms\Components\Select::make('wind_direction')
                        ->label('Wind Direction')
                        ->options(['N' => 'N', 'NE' => 'NE', 'E' => 'E', 'SE' => 'SE', 'S' => 'S', 'SW' => 'SW', 'W' => 'W', 'NW' => 'NW']),
                    Forms\Components\TextInput::make('noise_level_db')
                        ->label('Noise Level')
                        ->numeric()
                        ->suffix('dB(A)'),
                    Forms\Components\TextInput::make('dust_level_pm10')
                        ->label('Dust (PM10)')
                        ->numeric()
                        ->suffix('µg/m³'),
                    Forms\Components\TextInput::make('water_ph')
                        ->label('Water pH')
                        ->numeric(),
                    Forms\Components\TextInput::make('solar_irradiance')
                        ->label('Solar Irradiance')
                        ->numeric()
                        ->suffix('W/m²'),
                    Forms\Components\Textarea::make('environmental_notes')
                        ->label('Environmental Notes')
                        ->rows(2)
                        ->columnSpanFull(),
                ])->columns(4)->collapsed(),

            // ── Road Layer Works (Road Projects) ──
            Section::make('🛣️ Road Layer Works')
                ->icon('heroicon-o-map')
                ->description('Chainage, layer placement & compaction tracking')
                ->visible(fn($get) => static::projectTypeIs($get('cde_project_id'), 'road'))
                ->schema([
                    Forms\Components\TextInput::make('chainage_from')
                        ->label('Chainage From')
                        ->placeholder('e.g. 12+450'),
                    Forms\Components\TextInput::make('chainage_to')
                        ->label('Chainage To')
                        ->placeholder('e.g. 12+850'),
                    Forms\Components\Select::make('road_layer')
                        ->label('Layer')
                        ->options(\App\Models\DailySiteDiary::$roadLayers)
                        ->searchable(),
                    Forms\Components\TextInput::make('layer_thickness_mm')
                        ->label('Layer Thickness')
                        ->numeric()
                        ->suffix('mm'),
                    Forms\Components\TextInput::make('compaction_achieved')
                        ->label('Compaction Achieved')
                        ->numeric()
                        ->suffix('% MDD'),
                    Forms\Components\TextInput::make('compaction_required')
                        ->label('Compaction Required')
                        ->numeric()
                        ->suffix('% MDD'),
                    Forms\Components\TextInput::make('moisture_content')
                        ->label('Moisture Content')
                        ->numeric()
                        ->suffix('%'),
                    Forms\Components\TextInput::make('truck_loads')
                        ->label('Truck Loads')
                        ->numeric(),
                    Forms\Components\TextInput::make('material_source')
                        ->label('Material Source / Quarry')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('survey_data')
                        ->label('Survey Data / Levels')
                        ->rows(2)
                        ->placeholder('Level readings, alignment checks'),
                    Forms\Components\Textarea::make('traffic_management_notes')
                        ->label('Traffic Management')
                        ->rows(2)
                        ->placeholder('Diversions, flagmen, lane closures'),
                ])->columns(4)->collapsed(),

            Section::make('Issues, Safety & Quality')
                ->icon('heroicon-o-exclamation-triangle')
                ->schema([
                    Forms\Components\Textarea::make('delays')
                        ->label('Delays / Issues')
                        ->rows(2)
                        ->placeholder('Any delays, stoppages, or issues encountered'),
                    Forms\Components\Textarea::make('safety_observations')
                        ->label('Safety Observations')
                        ->rows(2)
                        ->placeholder('Near-misses, hazards observed, PPE compliance'),
                    Forms\Components\Textarea::make('quality_observations')
                        ->label('Quality Observations')
                        ->rows(2)
                        ->placeholder('Quality checks, NCRs, rework needed'),
                    Forms\Components\Textarea::make('visitor_log')
                        ->label('Visitors')
                        ->rows(2)
                        ->placeholder('Name (Company) - Time in/out'),
                    Forms\Components\Textarea::make('deliveries')
                        ->label('Deliveries Received')
                        ->rows(2)
                        ->placeholder('Materials delivered today'),
                ])->columns(2)->collapsed(),

            Section::make('Site Photos')
                ->icon('heroicon-o-camera')
                ->schema([
                    Forms\Components\FileUpload::make('photos')
                        ->label('Photos')
                        ->image()
                        ->multiple()
                        ->directory('site-diary-photos')
                        ->maxSize(10240)
                        ->maxFiles(10)
                        ->columnSpanFull(),
                ])->collapsed(),
        ]);
    }

    /**
     * Check if the selected project is of a specific type.
     */
    protected static function projectTypeIs(?int $projectId, string $type): bool
    {
        if (!$projectId)
            return false;
        return \App\Models\CdeProject::where('id', $projectId)->value('project_type') === $type;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('diary_date')
                    ->label('Date')
                    ->date('D, M d Y')
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('weather')
                    ->label('Weather')
                    ->formatStateUsing(fn(?string $state) => Company::options('weather_types')[$state] ?? $state ?? '—'),
                Tables\Columns\TextColumn::make('total_workforce')
                    ->label('Workers')
                    ->state(fn(DailySiteDiary $r) => $r->total_workforce)
                    ->description(fn(DailySiteDiary $r) => $r->workers_on_site . ' own + ' . $r->subcontractor_workers . ' sub')
                    ->color('info'),
                Tables\Columns\TextColumn::make('equipment_on_site')
                    ->label('Equipment')
                    ->placeholder('0'),
                Tables\Columns\TextColumn::make('work_performed')
                    ->label('Summary')
                    ->limit(50)
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('approved')
                    ->label('Approved')
                    ->state(fn(DailySiteDiary $r) => $r->isApproved())
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),
                Tables\Columns\TextColumn::make('preparer.name')
                    ->label('By')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('diary_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('cde_project_id')
                    ->label('Project')
                    ->relationship('project', 'name', fn($q) => $q->where('company_id', auth()->user()?->company_id)),
                Tables\Filters\Filter::make('unapproved')
                    ->label('Pending Approval')
                    ->query(fn($q) => $q->whereNull('approved_by'))
                    ->toggle(),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\Action::make('approve')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(DailySiteDiary $r) => !$r->isApproved())
                    ->action(fn(DailySiteDiary $r) => $r->update([
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ])),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ])
            ->persistFiltersInSession()
            ->persistSearchInSession();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDailySiteDiaries::route('/'),
            'create' => Pages\CreateDailySiteDiary::route('/create'),
            'edit' => Pages\EditDailySiteDiary::route('/{record}/edit'),
        ];
    }
}
