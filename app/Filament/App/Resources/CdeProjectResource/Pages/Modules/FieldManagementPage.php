<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource;
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
    protected static ?string $title = 'Field Management';
    protected string $view = 'filament.app.pages.modules.field-management';

    public function getStats(): array
    {
        $pid = $this->record->id;
        $total = DailySiteLog::where('cde_project_id', $pid)->count();
        $thisWeek = DailySiteLog::where('cde_project_id', $pid)->where('log_date', '>=', now()->startOfWeek())->count();
        $avgWorkers = (int) DailySiteLog::where('cde_project_id', $pid)->avg('workers_on_site');
        $pending = DailySiteLog::where('cde_project_id', $pid)->where('status', 'draft')->count();

        return [
            [
                'label' => 'Total Logs',
                'value' => $total,
                'sub' => $thisWeek . ' this week',
                'sub_type' => 'info',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>'
            ],
            [
                'label' => 'Avg. Workers/Day',
                'value' => $avgWorkers,
                'sub' => 'On site',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2563eb" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>',
                'icon_bg' => '#eff6ff'
            ],
            [
                'label' => 'Pending Approval',
                'value' => $pending,
                'sub' => $pending > 0 ? 'Needs review' : 'All clear',
                'sub_type' => $pending > 0 ? 'warning' : 'success',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#d97706" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>',
                'icon_bg' => '#fffbeb'
            ],
        ];
    }

    protected function getHeaderActions(): array
    {
        $companyId = $this->record->company_id;
        $projectId = $this->record->id;

        return [
            Action::make('createLog')
                ->label('New Daily Log')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->modalWidth('3xl')
                ->schema([
                    Section::make('Site Conditions')->schema([
                        Forms\Components\DatePicker::make('log_date')->required()->default(now()),
                        Forms\Components\Select::make('weather')->options(DailySiteLog::$weatherOptions)->required()->default('sunny'),
                        Forms\Components\TextInput::make('temperature_high')->label('High Temp')->numeric()->suffix('°C'),
                        Forms\Components\TextInput::make('temperature_low')->label('Low Temp')->numeric()->suffix('°C'),
                        Forms\Components\TextInput::make('workers_on_site')->label('Workers on Site')->numeric()->required()->default(0),
                        Forms\Components\TextInput::make('visitors_on_site')->label('Visitors on Site')->numeric()->default(0),
                    ])->columns(3),
                    Section::make('Work Activities')->schema([
                        Forms\Components\Textarea::make('work_performed')->label('Work Performed')->rows(4)->required()->columnSpanFull(),
                        Forms\Components\Textarea::make('materials_received')->label('Materials Received/Used')->rows(3)->columnSpanFull(),
                        Forms\Components\Textarea::make('equipment_used')->label('Equipment Used')->rows(2)->columnSpanFull(),
                    ]),
                    Section::make('Issues & Notes')->schema([
                        Forms\Components\Textarea::make('delays')->label('Delays / Issues Encountered')->rows(3)->columnSpanFull(),
                        Forms\Components\Textarea::make('safety_incidents')->label('Safety Observations')->rows(2)->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')->label('Additional Notes')->rows(2)->columnSpanFull(),
                    ])->collapsed(),
                ])
                ->action(function (array $data) use ($companyId, $projectId): void {
                    $data['company_id'] = $companyId;
                    $data['cde_project_id'] = $projectId;
                    $data['created_by'] = auth()->id();
                    $data['status'] = 'draft';
                    DailySiteLog::create($data);
                    Notification::make()->title('Daily Site Log created')->success()->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        $projectId = $this->record->id;

        return $table
            ->query(DailySiteLog::query()->where('cde_project_id', $projectId)->with(['creator', 'approver']))
            ->columns([
                Tables\Columns\TextColumn::make('log_date')->date('D, M d Y')->sortable()->label('Date')->weight('bold'),
                Tables\Columns\TextColumn::make('weather')->badge()
                    ->color(fn(string $state) => match ($state) { 'sunny' => 'success', 'partly_cloudy' => 'info', 'cloudy' => 'gray', 'rainy' => 'info', 'stormy' => 'danger', default => 'gray'}),
                Tables\Columns\TextColumn::make('temperature_high')->suffix('°C')->label('High'),
                Tables\Columns\TextColumn::make('workers_on_site')->label('Workers'),
                Tables\Columns\TextColumn::make('work_performed')->limit(60)->label('Activities'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) { 'approved' => 'success', 'submitted' => 'info', 'rejected' => 'danger', default => 'gray'}),
                Tables\Columns\TextColumn::make('creator.name')->label('Logged By')->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('H:i')->label('Time')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('log_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(DailySiteLog::$statuses),
                Tables\Filters\SelectFilter::make('weather')->options(DailySiteLog::$weatherOptions),
            ])
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalWidth('3xl')
                    ->schema([
                        Section::make('Site Conditions')->schema([
                            Forms\Components\DatePicker::make('log_date')->disabled(),
                            Forms\Components\TextInput::make('weather')->disabled(),
                            Forms\Components\TextInput::make('temperature_high')->suffix('°C')->disabled(),
                            Forms\Components\TextInput::make('temperature_low')->suffix('°C')->disabled(),
                            Forms\Components\TextInput::make('workers_on_site')->disabled(),
                            Forms\Components\TextInput::make('visitors_on_site')->disabled(),
                        ])->columns(3),
                        Section::make('Work Activities')->schema([
                            Forms\Components\Textarea::make('work_performed')->disabled()->rows(4)->columnSpanFull(),
                            Forms\Components\Textarea::make('materials_received')->disabled()->rows(2)->columnSpanFull(),
                            Forms\Components\Textarea::make('equipment_used')->disabled()->rows(2)->columnSpanFull(),
                        ]),
                        Section::make('Issues')->schema([
                            Forms\Components\Textarea::make('delays')->disabled()->rows(2)->columnSpanFull(),
                            Forms\Components\Textarea::make('safety_incidents')->disabled()->rows(2)->columnSpanFull(),
                            Forms\Components\Textarea::make('notes')->disabled()->rows(2)->columnSpanFull(),
                        ]),
                    ])
                    ->fillForm(fn(DailySiteLog $record) => $record->toArray())
                    ->modalSubmitAction(false)->modalCancelActionLabel('Close'),

                \Filament\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(DailySiteLog $record) => in_array($record->status, ['draft', 'submitted']))
                    ->requiresConfirmation()
                    ->action(function (DailySiteLog $record): void {
                        $record->update(['status' => 'approved', 'approved_by' => auth()->id()]);
                        Notification::make()->title('Daily log approved')->success()->send();
                    }),

                \Filament\Actions\Action::make('edit')
                    ->icon('heroicon-o-pencil')
                    ->visible(fn(DailySiteLog $record) => $record->status !== 'approved')
                    ->modalWidth('3xl')
                    ->schema([
                        Section::make('Site Conditions')->schema([
                            Forms\Components\DatePicker::make('log_date')->required(),
                            Forms\Components\Select::make('weather')->options(DailySiteLog::$weatherOptions)->required(),
                            Forms\Components\TextInput::make('temperature_high')->numeric()->suffix('°C'),
                            Forms\Components\TextInput::make('temperature_low')->numeric()->suffix('°C'),
                            Forms\Components\TextInput::make('workers_on_site')->numeric()->required(),
                            Forms\Components\TextInput::make('visitors_on_site')->numeric(),
                        ])->columns(3),
                        Section::make('Work Activities')->schema([
                            Forms\Components\Textarea::make('work_performed')->rows(4)->required()->columnSpanFull(),
                            Forms\Components\Textarea::make('materials_received')->rows(3)->columnSpanFull(),
                            Forms\Components\Textarea::make('equipment_used')->rows(2)->columnSpanFull(),
                        ]),
                        Section::make('Issues & Notes')->schema([
                            Forms\Components\Textarea::make('delays')->rows(3)->columnSpanFull(),
                            Forms\Components\Textarea::make('safety_incidents')->rows(2)->columnSpanFull(),
                            Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
                        ])->collapsed(),
                    ])
                    ->fillForm(fn(DailySiteLog $record) => $record->toArray())
                    ->action(function (array $data, DailySiteLog $record): void {
                        $record->update($data);
                        Notification::make()->title('Daily log updated')->success()->send();
                    }),

                \Filament\Actions\Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->visible(fn(DailySiteLog $record) => $record->status !== 'approved')
                    ->requiresConfirmation()
                    ->action(fn(DailySiteLog $record) => $record->delete()),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Daily Site Logs')
            ->emptyStateDescription('Start recording daily site activities.')
            ->emptyStateIcon('heroicon-o-map-pin');
    }
}
