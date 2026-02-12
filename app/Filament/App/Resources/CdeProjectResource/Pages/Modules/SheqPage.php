<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource;
use App\Models\CdeProject;
use App\Models\SafetyIncident;
use App\Models\User;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class SheqPage extends ManageRelatedRecords
{
    protected static string $resource = CdeProjectResource::class;
    protected static string $relationship = 'safetyIncidents';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'SHEQ';
    protected static ?string $title = 'SHEQ Management';

    public static function canAccess(array $parameters = []): bool
    {
        $record = $parameters['record'] ?? null;
        if ($record instanceof CdeProject) {
            return $record->hasModule('sheq');
        }
        return true;
    }

    public function form(Schema $schema): Schema
    {
        $companyId = $this->getOwnerRecord()->company_id;
        return $schema->components([
            Section::make('Incident Details')->schema([
                Forms\Components\TextInput::make('incident_number')->label('Incident #')
                    ->default(fn() => 'INC-' . str_pad((string) (SafetyIncident::where('cde_project_id', $this->getOwnerRecord()->id)->count() + 1), 4, '0', STR_PAD_LEFT))
                    ->required()->maxLength(50),
                Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\Select::make('type')->options([
                    'near_miss' => 'Near Miss',
                    'first_aid' => 'First Aid',
                    'medical' => 'Medical Treatment',
                    'lost_time' => 'Lost Time Injury',
                    'fatality' => 'Fatality',
                    'environmental' => 'Environmental',
                    'property_damage' => 'Property Damage',
                ])->required(),
                Forms\Components\Select::make('severity')->options([
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                    'critical' => 'Critical',
                ])->required()->default('low'),
                Forms\Components\Select::make('status')->options([
                    'reported' => 'Reported',
                    'investigating' => 'Investigating',
                    'corrective_action' => 'Corrective Action',
                    'closed' => 'Closed',
                    'resolved' => 'Resolved',
                ])->required()->default('reported'),
                Forms\Components\DateTimePicker::make('incident_date')->required()->default(now()),
                Forms\Components\TextInput::make('location')->maxLength(255),
                Forms\Components\Select::make('reported_by')->label('Reported By')
                    ->options(User::where('company_id', $companyId)->where('is_active', true)->pluck('name', 'id'))->searchable()->default(auth()->id()),
            ])->columns(2),
            Section::make('Description & Actions')->schema([
                Forms\Components\Textarea::make('description')->rows(4)->columnSpanFull(),
                Forms\Components\Textarea::make('root_cause')->label('Root Cause')->rows(3)->columnSpanFull(),
                Forms\Components\Textarea::make('corrective_actions')->label('Corrective Actions')->rows(3)->columnSpanFull(),
                Forms\Components\Textarea::make('preventive_actions')->label('Preventive Actions')->rows(3)->columnSpanFull(),
            ])->collapsed(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('incident_number')->label('Inc #')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(45),
                Tables\Columns\TextColumn::make('type')->badge()->color('info'),
                Tables\Columns\TextColumn::make('severity')->badge()->color(fn(string $state) => match ($state) {
                    'critical' => 'danger', 'high' => 'warning', 'medium' => 'info', default => 'gray',
                }),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn(string $state) => match ($state) {
                    'closed' => 'success', 'resolved' => 'success', 'investigating' => 'info',
                    'corrective_action' => 'warning', 'reported' => 'danger', default => 'gray',
                }),
                Tables\Columns\TextColumn::make('incident_date')->dateTime('M d, Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('location')->toggleable(),
            ])
            ->defaultSort('incident_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'reported' => 'Reported',
                    'investigating' => 'Investigating',
                    'corrective_action' => 'Corrective Action',
                    'closed' => 'Closed',
                    'resolved' => 'Resolved',
                ]),
                Tables\Filters\SelectFilter::make('severity')->options([
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                    'critical' => 'Critical',
                ]),
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function getTitle(): string|Htmlable
    {
        return static::$title;
    }

    public function getBreadcrumbs(): array
    {
        return [
            CdeProjectResource::getUrl() => 'Projects',
            CdeProjectResource::getUrl('view', ['record' => $this->getRecord()]) => $this->getRecord()->name,
            'SHEQ',
        ];
    }
}
