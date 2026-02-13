<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource;
use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\SafetyIncident;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class SheqPage extends BaseModulePage implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static string $moduleCode = 'sheq';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'SHEQ';
    protected static ?string $title = 'Safety, Health, Environment & Quality';
    protected string $view = 'filament.app.pages.modules.sheq';

    public function getStats(): array
    {
        $pid = $this->record->id;
        $total = SafetyIncident::where('cde_project_id', $pid)->count();
        $open = SafetyIncident::where('cde_project_id', $pid)->whereNotIn('status', ['closed', 'resolved'])->count();
        $critical = SafetyIncident::where('cde_project_id', $pid)->whereIn('type', ['fatality', 'lost_time'])->count();
        $thisMonth = SafetyIncident::where('cde_project_id', $pid)->whereMonth('incident_date', now()->month)->count();

        return [
            [
                'label' => 'Total Incidents',
                'value' => $total,
                'sub' => $open . ' open',
                'sub_type' => $open > 0 ? 'danger' : 'success',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>'
            ],
            [
                'label' => 'Critical / LTI',
                'value' => $critical,
                'sub' => $critical > 0 ? 'Requires attention' : 'None reported',
                'sub_type' => $critical > 0 ? 'danger' : 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ef4444" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>',
                'icon_bg' => '#fef2f2'
            ],
            [
                'label' => 'This Month',
                'value' => $thisMonth,
                'sub' => now()->format('F Y'),
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#d97706" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>',
                'icon_bg' => '#fffbeb'
            ],
        ];
    }

    protected function getHeaderActions(): array
    {
        $companyId = $this->record->company_id;
        $projectId = $this->record->id;

        return [
            Action::make('reportIncident')
                ->label('Report Incident')
                ->icon('heroicon-o-plus-circle')
                ->color('danger')
                ->modalWidth('3xl')
                ->schema([
                    Section::make('Incident Details')->schema([
                        Forms\Components\TextInput::make('incident_number')
                            ->default(fn() => 'INC-' . str_pad((string) (SafetyIncident::where('cde_project_id', $projectId)->count() + 1), 4, '0', STR_PAD_LEFT))
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
                        Forms\Components\DateTimePicker::make('incident_date')->required()->default(now()),
                        Forms\Components\TextInput::make('location')->maxLength(255)->placeholder('e.g. Block A, Level 3'),
                        Forms\Components\Select::make('reported_by')
                            ->options(User::where('company_id', $companyId)->where('is_active', true)->pluck('name', 'id'))->searchable()->default(auth()->id()),
                    ])->columns(2),
                    Section::make('Description & Investigation')->schema([
                        Forms\Components\Textarea::make('description')->label('What happened?')->rows(4)->required()->columnSpanFull(),
                        Forms\Components\Textarea::make('root_cause')->label('Root Cause (if known)')->rows(3)->columnSpanFull(),
                        Forms\Components\Textarea::make('corrective_action')->label('Corrective Actions Taken')->rows(3)->columnSpanFull(),
                    ]),
                ])
                ->action(function (array $data) use ($companyId, $projectId): void {
                    $data['company_id'] = $companyId;
                    $data['cde_project_id'] = $projectId;
                    $data['status'] = 'reported';
                    SafetyIncident::create($data);
                    Notification::make()->title('Incident reported')->success()->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        $projectId = $this->record->id;

        return $table
            ->query(SafetyIncident::query()->where('cde_project_id', $projectId)->with(['reporter', 'investigator']))
            ->columns([
                Tables\Columns\TextColumn::make('incident_number')->label('Inc #')->searchable()->sortable()->weight('bold')
                    ->icon('heroicon-o-exclamation-triangle'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(45),
                Tables\Columns\TextColumn::make('type')->badge()
                    ->color(fn(string $state) => match ($state) { 'fatality' => 'danger', 'lost_time' => 'danger', 'medical' => 'warning', 'first_aid' => 'info', 'near_miss' => 'gray', default => 'gray'}),
                Tables\Columns\TextColumn::make('severity')->badge()
                    ->color(fn(string $state) => match ($state) { 'critical' => 'danger', 'high' => 'warning', 'medium' => 'info', default => 'gray'}),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) { 'closed' => 'success', 'resolved' => 'success', 'investigating' => 'info', default => 'danger'}),
                Tables\Columns\TextColumn::make('incident_date')->dateTime('M d, Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('location')->placeholder('—')->toggleable(),
                Tables\Columns\TextColumn::make('reporter.name')->label('Reported By')->toggleable(),
            ])
            ->defaultSort('incident_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(SafetyIncident::$statuses),
                Tables\Filters\SelectFilter::make('severity')->options([
                    'low' => 'Low',
                    'medium' => 'Medium',
                    'high' => 'High',
                    'critical' => 'Critical',
                ]),
                Tables\Filters\SelectFilter::make('type')->options([
                    'near_miss' => 'Near Miss',
                    'first_aid' => 'First Aid',
                    'medical' => 'Medical',
                    'lost_time' => 'LTI',
                    'environmental' => 'Environmental',
                    'property_damage' => 'Property Damage',
                ]),
            ])
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalWidth('3xl')
                    ->schema([
                        Section::make('Incident')->schema([
                            Forms\Components\TextInput::make('incident_number')->disabled(),
                            Forms\Components\TextInput::make('title')->disabled()->columnSpanFull(),
                            Forms\Components\TextInput::make('type')->disabled(),
                            Forms\Components\TextInput::make('severity')->disabled(),
                            Forms\Components\TextInput::make('status')->disabled(),
                            Forms\Components\TextInput::make('location')->disabled(),
                            Forms\Components\TextInput::make('incident_date')->disabled(),
                        ])->columns(2),
                        Section::make('Details')->schema([
                            Forms\Components\Textarea::make('description')->disabled()->rows(3)->columnSpanFull(),
                            Forms\Components\Textarea::make('root_cause')->disabled()->rows(2)->columnSpanFull(),
                            Forms\Components\Textarea::make('corrective_action')->disabled()->rows(2)->columnSpanFull(),
                        ]),
                    ])
                    ->fillForm(fn(SafetyIncident $record) => $record->toArray())
                    ->modalSubmitAction(false)->modalCancelActionLabel('Close'),

                \Filament\Actions\Action::make('investigate')
                    ->label('Investigate')
                    ->icon('heroicon-o-magnifying-glass')
                    ->color('warning')
                    ->visible(fn(SafetyIncident $record) => !in_array($record->status, ['closed', 'resolved']))
                    ->schema([
                        Forms\Components\Select::make('status')->options(SafetyIncident::$statuses)->required(),
                        Forms\Components\Select::make('investigated_by')
                            ->options(fn() => User::where('company_id', $this->record->company_id)->where('is_active', true)->pluck('name', 'id'))->searchable(),
                        Forms\Components\Textarea::make('root_cause')->label('Root Cause')->rows(3),
                        Forms\Components\Textarea::make('corrective_action')->label('Corrective Actions')->rows(3),
                    ])
                    ->fillForm(fn(SafetyIncident $record) => $record->toArray())
                    ->action(function (array $data, SafetyIncident $record): void {
                        $record->update($data);
                        Notification::make()->title('Investigation updated')->success()->send();
                    }),

                \Filament\Actions\Action::make('closeIncident')
                    ->label('Close')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(SafetyIncident $record) => !in_array($record->status, ['closed', 'resolved']))
                    ->requiresConfirmation()
                    ->action(function (SafetyIncident $record): void {
                        $record->update(['status' => 'closed']);
                        Notification::make()->title('Incident closed')->success()->send();
                    }),

                \Filament\Actions\Action::make('edit')
                    ->icon('heroicon-o-pencil')
                    ->modalWidth('3xl')
                    ->schema([
                        Section::make('Incident Details')->schema([
                            Forms\Components\TextInput::make('incident_number')->required(),
                            Forms\Components\TextInput::make('title')->required()->columnSpanFull(),
                            Forms\Components\Select::make('type')->options([
                                'near_miss' => 'Near Miss',
                                'first_aid' => 'First Aid',
                                'medical' => 'Medical',
                                'lost_time' => 'LTI',
                                'fatality' => 'Fatality',
                                'environmental' => 'Environmental',
                                'property_damage' => 'Property Damage',
                            ])->required(),
                            Forms\Components\Select::make('severity')->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                                'critical' => 'Critical',
                            ])->required(),
                            Forms\Components\DateTimePicker::make('incident_date')->required(),
                            Forms\Components\TextInput::make('location'),
                        ])->columns(2),
                        Section::make('Investigation')->schema([
                            Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull(),
                            Forms\Components\Textarea::make('root_cause')->rows(2)->columnSpanFull(),
                            Forms\Components\Textarea::make('corrective_action')->rows(2)->columnSpanFull(),
                        ]),
                    ])
                    ->fillForm(fn(SafetyIncident $record) => $record->toArray())
                    ->action(function (array $data, SafetyIncident $record): void {
                        $record->update($data);
                        Notification::make()->title('Incident updated')->success()->send();
                    }),

                \Filament\Actions\Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn(SafetyIncident $record) => $record->delete()),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Safety Incidents')
            ->emptyStateDescription('No incidents reported — keep up the safe work!')
            ->emptyStateIcon('heroicon-o-shield-check');
    }
}
