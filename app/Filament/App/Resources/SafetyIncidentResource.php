<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\SafetyIncidentResource\Pages;
use App\Models\SafetyIncident;
use Filament\Actions;
use Filament\Infolists;
use Filament\Schemas;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SafetyIncidentResource extends Resource
{
    protected static ?string $model = SafetyIncident::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static string|\UnitEnum|null $navigationGroup = 'SHEQ';
    protected static ?int $navigationSort = 1;
    protected static bool $shouldRegisterNavigation = false;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('company_id', auth()->user()?->company_id);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Incident Details')->schema([
                Infolists\Components\TextEntry::make('incident_number')
                    ->label('Incident #')
                    ->icon('heroicon-o-hashtag')
                    ->copyable(),
                Infolists\Components\TextEntry::make('title')
                    ->icon('heroicon-o-exclamation-triangle'),
                Infolists\Components\TextEntry::make('project.name')
                    ->label('Project')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('type')
                    ->badge(),
                Infolists\Components\TextEntry::make('severity')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'critical' => 'danger', 'high' => 'warning',
                        'medium' => 'info', default => 'gray',
                    }),
                Infolists\Components\TextEntry::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'closed' => 'success', 'resolved' => 'info',
                        'investigating' => 'warning', default => 'gray',
                    }),
                Infolists\Components\TextEntry::make('location')
                    ->icon('heroicon-o-map-pin')
                    ->placeholder('—'),
                Infolists\Components\TextEntry::make('incident_date')
                    ->dateTime()
                    ->icon('heroicon-o-calendar'),
            ])->columns(2),

            Schemas\Components\Section::make('Description')->schema([
                Infolists\Components\TextEntry::make('description')
                    ->html()
                    ->columnSpanFull()
                    ->placeholder('No description provided.'),
            ])->collapsible(),

            Schemas\Components\Section::make('Root Cause Analysis')->schema([
                Infolists\Components\TextEntry::make('root_cause')
                    ->label('Root Cause')
                    ->html()
                    ->columnSpanFull()
                    ->placeholder('Root cause analysis not yet completed.'),
            ])->collapsible(),

            Schemas\Components\Section::make('Corrective Actions')->schema([
                Infolists\Components\TextEntry::make('corrective_action')
                    ->html()
                    ->columnSpanFull()
                    ->placeholder('No corrective actions documented.'),
            ])->collapsible(),
        ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Incident Details')->schema([
                Forms\Components\TextInput::make('incident_number')
                    ->default(fn() => 'INC-' . str_pad(SafetyIncident::withoutGlobalScopes()->count() + 1, 4, '0', STR_PAD_LEFT))
                    ->disabled()->dehydrated(),
                Forms\Components\TextInput::make('title')->required(),
                Forms\Components\Select::make('cde_project_id')
                    ->relationship('project', 'name')->searchable()->preload()->label('Project'),
                Forms\Components\Select::make('type')
                    ->options(['near_miss' => 'Near Miss', 'first_aid' => 'First Aid', 'medical' => 'Medical Treatment', 'lost_time' => 'Lost Time', 'fatality' => 'Fatality']),
                Forms\Components\Select::make('severity')
                    ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'critical' => 'Critical'])
                    ->default('low'),
                Forms\Components\Select::make('status')
                    ->options(SafetyIncident::$statuses)->default('reported'),
                Forms\Components\TextInput::make('location'),
                Forms\Components\DateTimePicker::make('incident_date')->default(now()),
                Forms\Components\RichEditor::make('description')->columnSpanFull(),
                Forms\Components\RichEditor::make('root_cause')->label('Root Cause Analysis')->columnSpanFull(),
                Forms\Components\RichEditor::make('corrective_action')->columnSpanFull(),
            ])->columns(2),

            // ── Permit to Work (PTW) — toggle to show ──
            Schemas\Components\Section::make('🔒 Permit to Work (PTW)')
                ->icon('heroicon-o-shield-check')
                ->description('Issue a safety permit for high-risk activities')
                ->schema([
                    Forms\Components\Toggle::make('is_ptw')
                        ->label('This is a Permit to Work')
                        ->reactive()
                        ->columnSpanFull(),

                    Schemas\Components\Fieldset::make('PTW Details')
                        ->schema([
                            Forms\Components\TextInput::make('ptw_number')
                                ->label('PTW Number')
                                ->placeholder('PTW-2026-001'),
                            Forms\Components\Select::make('ptw_type')
                                ->label('Permit Type')
                                ->options(SafetyIncident::$ptwTypes)
                                ->searchable(),
                            Forms\Components\TextInput::make('isolation_method')
                                ->label('Isolation Method')
                                ->placeholder('e.g. LOTO, circuit breaker'),
                            Forms\Components\Textarea::make('isolation_points')
                                ->label('Isolation Points')
                                ->rows(2)
                                ->placeholder('List all isolation points'),
                            Forms\Components\Select::make('ptw_issuer_id')
                                ->label('Permit Issuer')
                                ->relationship('ptwIssuer', 'name')
                                ->searchable()
                                ->preload(),
                            Forms\Components\Select::make('ptw_receiver_id')
                                ->label('Permit Receiver')
                                ->relationship('ptwReceiver', 'name')
                                ->searchable()
                                ->preload(),
                            Forms\Components\DateTimePicker::make('ptw_valid_from')
                                ->label('Valid From'),
                            Forms\Components\DateTimePicker::make('ptw_valid_until')
                                ->label('Valid Until'),
                            Forms\Components\Select::make('ptw_status')
                                ->label('Permit Status')
                                ->options(SafetyIncident::$ptwStatuses)
                                ->default('active'),
                            Forms\Components\Textarea::make('ptw_conditions')
                                ->label('Special Conditions')
                                ->rows(2)
                                ->columnSpanFull(),
                            Forms\Components\Textarea::make('ppe_requirements')
                                ->label('PPE Requirements')
                                ->rows(2)
                                ->columnSpanFull()
                                ->placeholder('Hard hat, safety goggles, arc flash suit, insulated gloves...'),
                        ])->columns(2)
                        ->visible(fn($get) => $get('is_ptw')),
                ])->collapsed(),

            // ── Traffic Management (Road Projects) — toggle to show ──
            Schemas\Components\Section::make('🚧 Traffic / Road Work Zone')
                ->icon('heroicon-o-exclamation-triangle')
                ->description('For incidents involving traffic or road work zones')
                ->schema([
                    Forms\Components\Toggle::make('is_traffic_incident')
                        ->label('This is a traffic / road work zone incident')
                        ->reactive()
                        ->columnSpanFull(),

                    Schemas\Components\Fieldset::make('Traffic Details')
                        ->schema([
                            Forms\Components\Select::make('traffic_control_type')
                                ->label('Traffic Control Type')
                                ->options(SafetyIncident::$trafficControlTypes)
                                ->searchable(),
                            Forms\Components\TextInput::make('incident_chainage')
                                ->label('Chainage')
                                ->placeholder('e.g. 15+200'),
                            Forms\Components\Toggle::make('third_party_involved')
                                ->label('Third Party (Public) Involved'),
                            Forms\Components\Toggle::make('road_closure_required')
                                ->label('Road Closure Required')
                                ->reactive(),
                            Forms\Components\TextInput::make('closure_duration_hours')
                                ->label('Closure Duration')
                                ->numeric()
                                ->suffix('hours')
                                ->visible(fn($get) => $get('road_closure_required')),
                        ])->columns(2)
                        ->visible(fn($get) => $get('is_traffic_incident')),
                ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('incident_number')->label('#')->searchable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(35),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('severity')->badge()
                    ->color(fn(string $state) => match ($state) {
                        'critical' => 'danger', 'high' => 'warning',
                        'medium' => 'info', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) {
                        'closed' => 'success', 'resolved' => 'info',
                        'investigating' => 'warning', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('project.name')->label('Project'),
                Tables\Columns\TextColumn::make('incident_date')->dateTime(),
            ])
            ->defaultSort('incident_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(SafetyIncident::$statuses),
                Tables\Filters\SelectFilter::make('severity')
                    ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'critical' => 'Critical']),
            ])
            ->actions([Actions\ViewAction::make(), Actions\EditAction::make()])
            ->bulkActions([Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSafetyIncidents::route('/'),
            'create' => Pages\CreateSafetyIncident::route('/create'),
            'edit' => Pages\EditSafetyIncident::route('/{record}/edit'),
        ];
    }
}
