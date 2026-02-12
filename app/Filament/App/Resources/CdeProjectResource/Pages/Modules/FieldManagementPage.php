<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Models\DailySiteLog;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class FieldManagementPage extends BaseModulePage implements HasTable
{
    use InteractsWithTable;

    protected static string $moduleCode = 'field_management';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Field Mgmt';
    protected static ?string $title = 'Field Management';
    protected string $view = 'filament.app.pages.modules.field-management';

    public function getStats(): array
    {
        $r = $this->record;
        $total = $r->dailySiteLogs()->count();
        $today = $r->dailySiteLogs()->whereDate('log_date', today())->exists();
        $approved = $r->dailySiteLogs()->where('status', 'approved')->count();
        $workersToday = $r->dailySiteLogs()->whereDate('log_date', today())->sum('workers_on_site');

        return [
            [
                'label' => 'Daily Logs',
                'value' => $total,
                'sub' => $approved . ' approved',
                'sub_type' => 'success',
                'primary' => true,
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:1.125rem;height:1.125rem;color:white;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>'
            ],
            [
                'label' => "Today's Log",
                'value' => $today ? '✓' : '—',
                'sub' => $today ? 'Submitted' : 'Not yet',
                'sub_type' => $today ? 'success' : 'warning',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="' . ($today ? '#059669' : '#d97706') . '" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>',
                'icon_bg' => $today ? '#ecfdf5' : '#fffbeb'
            ],
            [
                'label' => 'Workers Today',
                'value' => $workersToday ?: 0,
                'sub' => 'On site',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2563eb" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>',
                'icon_bg' => '#eff6ff'
            ],
            [
                'label' => 'Pending Review',
                'value' => $r->dailySiteLogs()->where('status', 'submitted')->count(),
                'sub' => 'Awaiting approval',
                'sub_type' => 'warning',
                'icon_svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#d97706" style="width:1.125rem;height:1.125rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
                'icon_bg' => '#fffbeb'
            ],
        ];
    }

    protected function getDailySiteLogFormSchema(): array
    {
        return [
            Section::make('Log Details')->schema([
                Forms\Components\DatePicker::make('log_date')
                    ->required()
                    ->default(now())
                    ->label('Date'),
                Forms\Components\Select::make('weather')
                    ->options(DailySiteLog::$weatherOptions)
                    ->required(),
                Forms\Components\TextInput::make('temperature_high')
                    ->numeric()
                    ->suffix('°C')
                    ->label('High Temp'),
                Forms\Components\TextInput::make('temperature_low')
                    ->numeric()
                    ->suffix('°C')
                    ->label('Low Temp'),
                Forms\Components\TextInput::make('workers_on_site')
                    ->numeric()
                    ->required()
                    ->default(0)
                    ->label('Workers on Site'),
                Forms\Components\TextInput::make('visitors_on_site')
                    ->numeric()
                    ->default(0)
                    ->label('Visitors'),
                Forms\Components\Select::make('status')
                    ->options(DailySiteLog::$statuses)
                    ->required()
                    ->default('draft'),
            ])->columns(3),

            Section::make('Work Summary')->schema([
                Forms\Components\Textarea::make('work_performed')
                    ->rows(3)
                    ->label('Work Performed')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('materials_received')
                    ->rows(2)
                    ->label('Materials Received')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('equipment_used')
                    ->rows(2)
                    ->label('Equipment Used')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('delays')
                    ->rows(2)
                    ->placeholder('Describe any delays or issues...'),
                Forms\Components\Textarea::make('safety_incidents')
                    ->rows(2)
                    ->placeholder('Report any safety incidents...'),
                Forms\Components\Textarea::make('notes')
                    ->rows(2)
                    ->columnSpanFull(),
            ])->columns(2),
        ];
    }

    public function table(Table $table): Table
    {
        $projectId = $this->record->id;
        $companyId = $this->record->company_id;

        return $table
            ->query(DailySiteLog::query()->where('cde_project_id', $projectId))
            ->columns([
                Tables\Columns\TextColumn::make('log_date')->date()->sortable()->label('Date'),
                Tables\Columns\TextColumn::make('weather')->badge()
                    ->color(fn(string $state) => match ($state) {
                        'sunny', 'hot' => 'warning',
                        'rainy', 'stormy' => 'danger',
                        'cloudy', 'partly_cloudy' => 'gray',
                        default => 'info',
                    }),
                Tables\Columns\TextColumn::make('workers_on_site')->label('Workers')->sortable(),
                Tables\Columns\TextColumn::make('work_performed')->limit(60)->label('Work Summary'),
                Tables\Columns\TextColumn::make('delays')->limit(40)->placeholder('None'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $state) => match ($state) {
                        'approved' => 'success',
                        'submitted' => 'info',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('creator.name')->label('Logged By'),
            ])
            ->defaultSort('log_date', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->label('New Daily Log')
                    ->icon('heroicon-o-plus')
                    ->schema($this->getDailySiteLogFormSchema())
                    ->mutateDataUsing(function (array $data) use ($projectId, $companyId): array {
                        $data['cde_project_id'] = $projectId;
                        $data['company_id'] = $companyId;
                        $data['created_by'] = auth()->id();
                        return $data;
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->schema($this->getDailySiteLogFormSchema()),
                EditAction::make()
                    ->schema($this->getDailySiteLogFormSchema()),
                Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn(DailySiteLog $record) => $record->status === 'submitted')
                    ->action(fn(DailySiteLog $record) => $record->update([
                        'status' => 'approved',
                        'approved_by' => auth()->id(),
                    ])),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No Daily Logs')
            ->emptyStateDescription('No daily site logs have been recorded for this project yet.')
            ->emptyStateIcon('heroicon-o-document-text');
    }
}
