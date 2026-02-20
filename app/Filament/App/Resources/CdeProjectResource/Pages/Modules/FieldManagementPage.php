<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\DailySiteLog;
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

class FieldManagementPage extends BaseModulePage implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static string $moduleCode = 'field_management';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Field Mgmt';
    protected static ?string $title = 'Field Management — Daily Site Logs';
    protected string $view = 'filament.app.pages.modules.field-management';

    private function pid(): int
    {
        return $this->record->id;
    }
    private function cid(): int
    {
        return $this->record->company_id;
    }

    private function teamOptions(): array
    {
        return User::where('company_id', $this->cid())->where('is_active', true)->pluck('name', 'id')->toArray();
    }

    public function getStats(): array
    {
        $pid = $this->pid();
        $base = DailySiteLog::where('cde_project_id', $pid);
        $total = (clone $base)->count();
        $thisWeek = (clone $base)->whereBetween('log_date', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $pending = (clone $base)->where('status', 'draft')->count();
        $avgWorkers = $total > 0 ? round((clone $base)->avg('workers_on_site') ?? 0) : 0;
        $weatherDelays = (clone $base)->whereIn('weather', ['rainy', 'stormy'])->count();

        return [
            [
                'label' => 'Total Logs',
                'value' => $total,
                'sub' => $pending . ' pending review',
                'sub_type' => $pending > 0 ? 'warning' : 'success',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>'
            ],
            [
                'label' => 'This Week',
                'value' => $thisWeek,
                'sub' => now()->startOfWeek()->format('M d') . ' – ' . now()->endOfWeek()->format('M d'),
                'sub_type' => 'info',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#3b82f6" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>',
                'icon_bg' => '#eff6ff'
            ],
            [
                'label' => 'Avg Workers/Day',
                'value' => $avgWorkers,
                'sub' => 'On site',
                'sub_type' => 'neutral',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#6366f1" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>',
                'icon_bg' => '#eef2ff'
            ],
            [
                'label' => 'Weather Delays',
                'value' => $weatherDelays,
                'sub' => 'Rainy/stormy days',
                'sub_type' => $weatherDelays > 3 ? 'danger' : ($weatherDelays > 0 ? 'warning' : 'success'),
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#d97706" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 002.25 15z" /></svg>',
                'icon_bg' => '#fffbeb'
            ],
        ];
    }

    private function logFormSchema(bool $isCreate = false): array
    {
        return [
            Section::make('Log Details')->schema([
                Forms\Components\DatePicker::make('log_date')->required()->default(now()),
                Forms\Components\Select::make('status')->options(DailySiteLog::$statuses)->required()->default('draft'),
                Forms\Components\Select::make('weather')->options([
                    'sunny' => '☀️ Sunny',
                    'partly_cloudy' => '⛅ Partly Cloudy',
                    'cloudy' => '☁️ Cloudy',
                    'rainy' => '🌧️ Rainy',
                    'stormy' => '⛈️ Stormy',
                    'snowy' => '❄️ Snowy',
                    'windy' => '💨 Windy',
                    'foggy' => '🌫️ Foggy',
                ])->searchable(),
                Forms\Components\TextInput::make('temperature_high')->label('High °C')->numeric()->suffix('°C'),
                Forms\Components\TextInput::make('temperature_low')->label('Low °C')->numeric()->suffix('°C'),
                Forms\Components\TextInput::make('workers_on_site')->label('Workers On Site')->numeric()->default(0),
                Forms\Components\TextInput::make('visitors_on_site')->label('Visitors')->numeric()->default(0),
                Forms\Components\Select::make('approved_by')->label('Approved By')
                    ->options(fn() => $this->teamOptions())->searchable()->nullable(),
            ])->columns(2),
            Section::make('Work Performed')->schema([
                Forms\Components\Textarea::make('work_performed')->label('Work Performed Today')->rows(4)->columnSpanFull(),
                Forms\Components\Textarea::make('materials_received')->label('Materials Received')->rows(2)->columnSpanFull(),
                Forms\Components\Textarea::make('equipment_used')->label('Equipment Used')->rows(2)->columnSpanFull(),
            ]),
            Section::make('Issues & Notes')->schema([
                Forms\Components\Textarea::make('delays')->label('Delays / Issues')->rows(2)->columnSpanFull(),
                Forms\Components\Textarea::make('safety_incidents')->label('Safety Incidents')->rows(2)->columnSpanFull(),
                Forms\Components\Textarea::make('notes')->label('Additional Notes')->rows(2)->columnSpanFull(),
            ])->collapsed(!$isCreate),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createLog')
                ->label('New Daily Log')->icon('heroicon-o-plus-circle')->color('primary')
                ->modalWidth('4xl')
                ->schema($this->logFormSchema(isCreate: true))
                ->action(function (array $data): void {
                    $data['company_id'] = $this->cid();
                    $data['cde_project_id'] = $this->pid();
                    $data['created_by'] = auth()->id();
                    DailySiteLog::create($data);
                    Notification::make()->title('Daily site log created')->success()->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(DailySiteLog::query()->where('cde_project_id', $this->pid())->with(['creator', 'approver']))
            ->columns([
                Tables\Columns\TextColumn::make('log_date')->date('D, M d Y')->sortable()->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('weather')->formatStateUsing(fn(string $state) => match ($state) {
                    'sunny' => '☀️ Sunny', 'partly_cloudy' => '⛅ Partly', 'cloudy' => '☁️ Cloudy', 'rainy' => '🌧️ Rainy',
                    'stormy' => '⛈️ Storm', 'snowy' => '❄️ Snow', 'windy' => '💨 Wind', 'foggy' => '🌫️ Fog', default => $state ?? '—'
                }),
                Tables\Columns\TextColumn::make('temperature_display')->label('Temp')
                    ->state(fn($record) => $record->temperature_high || $record->temperature_low ? ($record->temperature_low ?? '–') . '–' . ($record->temperature_high ?? '–') . '°C' : '—'),
                Tables\Columns\TextColumn::make('workers_on_site')->label('Workers')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) { 'approved' => 'success', 'submitted' => 'info', 'draft' => 'gray', default => 'warning'})->sortable(),
                Tables\Columns\TextColumn::make('work_performed')->limit(50)->toggleable()->placeholder('—'),
                Tables\Columns\TextColumn::make('delays')->limit(30)->toggleable(isToggledHiddenByDefault: true)->placeholder('None'),
                Tables\Columns\TextColumn::make('creator.name')->label('Logged By')->toggleable(),
                Tables\Columns\TextColumn::make('approver.name')->label('Approved By')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('log_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(DailySiteLog::$statuses),
                Tables\Filters\SelectFilter::make('weather')->options([
                    'sunny' => 'Sunny',
                    'partly_cloudy' => 'Partly Cloudy',
                    'cloudy' => 'Cloudy',
                    'rainy' => 'Rainy',
                    'stormy' => 'Stormy',
                ]),
                Tables\Filters\Filter::make('this_week')->label('This Week Only')
                    ->query(fn($q) => $q->whereBetween('log_date', [now()->startOfWeek(), now()->endOfWeek()]))->toggle(),
            ])
            ->recordActions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\Action::make('view')
                        ->icon('heroicon-o-eye')->color('gray')->modalWidth('4xl')
                        ->modalHeading(fn(DailySiteLog $record) => 'Site Log — ' . $record->log_date->format('D, M d Y'))
                        ->schema([
                            Section::make('Overview')->schema([
                                Forms\Components\TextInput::make('weather_display')->label('Weather')->disabled(),
                                Forms\Components\TextInput::make('temp_display')->label('Temperature')->disabled(),
                                Forms\Components\TextInput::make('workers_on_site')->label('Workers')->disabled(),
                                Forms\Components\TextInput::make('visitors_on_site')->label('Visitors')->disabled(),
                                Forms\Components\TextInput::make('status_display')->label('Status')->disabled(),
                                Forms\Components\TextInput::make('logged_by')->label('Logged By')->disabled(),
                            ])->columns(3),
                            Section::make('Work Performed')->schema([Forms\Components\Textarea::make('work_performed')->disabled()->rows(4)->columnSpanFull()])->collapsed(false),
                            Section::make('Materials & Equipment')->schema([
                                Forms\Components\Textarea::make('materials_received')->disabled()->rows(2)->columnSpanFull(),
                                Forms\Components\Textarea::make('equipment_used')->disabled()->rows(2)->columnSpanFull(),
                            ])->collapsed(empty('')),
                            Section::make('Issues')->schema([
                                Forms\Components\Textarea::make('delays')->disabled()->rows(2)->columnSpanFull(),
                                Forms\Components\Textarea::make('safety_incidents')->label('Safety Incidents')->disabled()->rows(2)->columnSpanFull(),
                                Forms\Components\Textarea::make('notes')->disabled()->rows(2)->columnSpanFull(),
                            ])->collapsed(true),
                        ])
                        ->fillForm(fn(DailySiteLog $record) => [
                            'weather_display' => match ($record->weather) { 'sunny' => '☀️ Sunny', 'partly_cloudy' => '⛅ Partly Cloudy', 'cloudy' => '☁️ Cloudy', 'rainy' => '🌧️ Rainy', 'stormy' => '⛈️ Stormy', default => $record->weather ?? '—'},
                            'temp_display' => ($record->temperature_low ?? '–') . ' – ' . ($record->temperature_high ?? '–') . ' °C',
                            'workers_on_site' => $record->workers_on_site,
                            'visitors_on_site' => $record->visitors_on_site,
                            'status_display' => DailySiteLog::$statuses[$record->status] ?? $record->status,
                            'logged_by' => $record->creator?->name ?? '—',
                            'work_performed' => $record->work_performed ?? '',
                            'materials_received' => $record->materials_received ?? '',
                            'equipment_used' => $record->equipment_used ?? '',
                            'delays' => $record->delays ?? '',
                            'safety_incidents' => $record->safety_incidents ?? '',
                            'notes' => $record->notes ?? '',
                        ])
                        ->modalSubmitAction(false)->modalCancelActionLabel('Close'),

                    \Filament\Actions\Action::make('submit')
                        ->label('Submit for Review')->icon('heroicon-o-paper-airplane')->color('info')
                        ->visible(fn(DailySiteLog $record) => $record->status === 'draft')
                        ->requiresConfirmation()->modalDescription('Submit this log for management review.')
                        ->action(function (DailySiteLog $record): void {
                            $record->update(['status' => 'submitted']);
                            Notification::make()->title('Log submitted for review')->success()->send();
                        }),

                    \Filament\Actions\Action::make('approve')
                        ->icon('heroicon-o-check-circle')->color('success')
                        ->visible(fn(DailySiteLog $record) => in_array($record->status, ['draft', 'submitted']))
                        ->requiresConfirmation()->modalHeading('Approve Daily Log')
                        ->action(function (DailySiteLog $record): void {
                            $record->update(['status' => 'approved', 'approved_by' => auth()->id()]);
                            Notification::make()->title('Log approved')->success()->send();
                        }),

                    \Filament\Actions\Action::make('edit')
                        ->icon('heroicon-o-pencil')->modalWidth('4xl')
                        ->schema($this->logFormSchema())
                        ->fillForm(fn(DailySiteLog $record) => $record->toArray())
                        ->action(function (array $data, DailySiteLog $record): void {
                            $record->update($data);
                            Notification::make()->title('Log updated')->success()->send();
                        }),

                    \Filament\Actions\Action::make('duplicate')
                        ->icon('heroicon-o-document-duplicate')->color('gray')
                        ->requiresConfirmation()->modalDescription('Creates a copy for today.')
                        ->action(function (DailySiteLog $record): void {
                            $new = $record->replicate();
                            $new->log_date = now();
                            $new->status = 'draft';
                            $new->approved_by = null;
                            $new->created_by = auth()->id();
                            $new->save();
                            Notification::make()->title('Log duplicated for today')->success()->send();
                        }),

                    \Filament\Actions\Action::make('delete')
                        ->icon('heroicon-o-trash')->color('danger')->requiresConfirmation()
                        ->action(fn(DailySiteLog $record) => $record->delete()),
                ]),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('bulkApprove')->label('Approve')
                        ->icon('heroicon-o-check-circle')->color('success')->requiresConfirmation()
                        ->action(function ($records): void {
                            foreach ($records as $record)
                                $record->update(['status' => 'approved', 'approved_by' => auth()->id()]);
                            Notification::make()->title($records->count() . ' logs approved')->success()->send();
                        })->deselectRecordsAfterCompletion(),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Daily Site Logs')
            ->emptyStateDescription('Start logging daily site activities.')
            ->emptyStateIcon('heroicon-o-map-pin')
            ->striped()->paginated([10, 25, 50]);
    }
}
