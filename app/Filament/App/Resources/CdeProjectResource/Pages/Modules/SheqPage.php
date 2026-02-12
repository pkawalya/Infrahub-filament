<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\SafetyIncident;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class SheqPage extends BaseModulePage implements HasTable
{
    use InteractsWithTable;

    protected static string $moduleCode = 'sheq';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'SHEQ';
    protected static ?string $title = 'SHEQ Management';
    protected string $view = 'filament.app.pages.modules.sheq';

    public function getStats(): array
    {
        $r = $this->record;
        $total = $r->safetyIncidents()->count();
        $open = $r->safetyIncidents()->whereNotIn('status', ['closed', 'resolved'])->count();
        $critical = $r->safetyIncidents()->where('severity', 'critical')->count();

        return [
            [
                'label' => 'Total Incidents',
                'value' => $total,
                'sub' => $open . ' open',
                'sub_type' => $open > 0 ? 'danger' : 'success',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>'
            ],
            [
                'label' => 'Critical',
                'value' => $critical,
                'sub' => $critical > 0 ? 'Needs attention' : 'None',
                'sub_type' => $critical > 0 ? 'danger' : 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#dc2626" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>',
                'icon_bg' => '#fef2f2'
            ],
            [
                'label' => 'Resolved',
                'value' => $r->safetyIncidents()->whereIn('status', ['closed', 'resolved'])->count(),
                'sub' => 'Handled',
                'sub_type' => 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#059669" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#ecfdf5'
            ],
        ];
    }

    protected function getIncidentForm(): array
    {
        $companyId = $this->record->company_id;

        return [
            Schemas\Components\Section::make('Incident Details')->schema([
                Forms\Components\TextInput::make('incident_number')
                    ->label('Incident #')
                    ->required()
                    ->default(fn() => 'INC-' . str_pad((string) (SafetyIncident::where('cde_project_id', $this->record->id)->count() + 1), 4, '0', STR_PAD_LEFT)),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->options([
                        'near_miss' => 'Near Miss',
                        'first_aid' => 'First Aid',
                        'injury' => 'Injury',
                        'property_damage' => 'Property Damage',
                        'environmental' => 'Environmental',
                        'fire' => 'Fire',
                        'other' => 'Other',
                    ])
                    ->required(),
                Forms\Components\Select::make('severity')
                    ->options([
                        'minor' => 'Minor',
                        'moderate' => 'Moderate',
                        'major' => 'Major',
                        'critical' => 'Critical',
                    ])
                    ->required()
                    ->default('minor'),
                Forms\Components\Select::make('status')
                    ->options(SafetyIncident::$statuses)
                    ->required()
                    ->default('reported'),
                Forms\Components\DateTimePicker::make('incident_date')
                    ->required()
                    ->default(now()),
                Forms\Components\TextInput::make('location')
                    ->placeholder('e.g. Block A, Floor 3')
                    ->columnSpanFull(),
            ])->columns(2),

            Schemas\Components\Section::make('Details & Resolution')->schema([
                Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->label('Description')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('root_cause')
                    ->rows(2)
                    ->label('Root Cause'),
                Forms\Components\Textarea::make('corrective_action')
                    ->rows(2)
                    ->label('Corrective Action'),
                Forms\Components\Select::make('investigated_by')
                    ->label('Investigated By')
                    ->options(User::where('company_id', $companyId)->where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),
            ])->columns(2),
        ];
    }

    public function table(Table $table): Table
    {
        $projectId = $this->record->id;
        $companyId = $this->record->company_id;

        return $table
            ->query(SafetyIncident::query()->where('cde_project_id', $projectId))
            ->columns([
                Tables\Columns\TextColumn::make('incident_number')->label('Incident #')->searchable(),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('severity')->badge()
                    ->color(fn(string $state) => match ($state) { 'critical' => 'danger', 'major' => 'warning', 'minor' => 'info', default => 'gray'}),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) { 'resolved' => 'success', 'closed' => 'gray', 'investigating' => 'warning', 'reported' => 'info', default => 'gray'}),
                Tables\Columns\TextColumn::make('incident_date')->dateTime(),
            ])
            ->defaultSort('incident_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(SafetyIncident::$statuses),
                Tables\Filters\SelectFilter::make('severity')->options([
                    'minor' => 'Minor',
                    'moderate' => 'Moderate',
                    'major' => 'Major',
                    'critical' => 'Critical',
                ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Report Incident')
                    ->icon('heroicon-o-plus')
                    ->form($this->getIncidentForm())
                    ->mutateFormDataUsing(function (array $data) use ($projectId, $companyId): array {
                        $data['cde_project_id'] = $projectId;
                        $data['company_id'] = $companyId;
                        $data['reported_by'] = auth()->id();
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->form($this->getIncidentForm()),
                Tables\Actions\EditAction::make()
                    ->form($this->getIncidentForm()),
                Tables\Actions\Action::make('resolve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(SafetyIncident $record) => !in_array($record->status, ['resolved', 'closed']))
                    ->action(fn(SafetyIncident $record) => $record->update(['status' => 'resolved'])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('No Safety Incidents')
            ->emptyStateDescription('No safety incidents have been reported for this project.')
            ->emptyStateIcon('heroicon-o-shield-check');
    }
}
