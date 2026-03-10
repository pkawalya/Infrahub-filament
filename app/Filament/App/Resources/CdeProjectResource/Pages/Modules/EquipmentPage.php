<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Filament\App\Concerns\ExportsTableCsv;
use App\Models\Company;
use App\Models\EquipmentAllocation;
use App\Models\EquipmentFuelLog;
use App\Support\CurrencyHelper;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class EquipmentPage extends BaseModulePage implements HasTable
{
    use InteractsWithTable, ExportsTableCsv;

    protected static string $moduleCode = 'equipment';
    protected static ?string $title = 'Plant & Equipment';
    protected static string $view = 'filament.app.pages.modules.equipment';
    protected static ?string $navigationLabel = 'Equipment';
    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected function pid(): int
    {
        return $this->record->id;
    }
    protected function cid(): int
    {
        return $this->record->company_id;
    }

    public function getStats(): array
    {
        $pid = $this->pid();
        $cid = $this->cid();

        $allocations = EquipmentAllocation::where('cde_project_id', $pid)->get();
        $activeCount = $allocations->where('status', 'active')->count();
        $totalDailyRate = $allocations->where('status', 'active')->sum('daily_rate');

        $fuelThisMonth = EquipmentFuelLog::where('cde_project_id', $pid)
            ->whereMonth('log_date', now()->month)
            ->whereYear('log_date', now()->year)
            ->sum('total_cost');

        $fuelLitersMonth = EquipmentFuelLog::where('cde_project_id', $pid)
            ->whereMonth('log_date', now()->month)
            ->whereYear('log_date', now()->year)
            ->sum('liters');

        return [
            [
                'label' => 'Active Equipment',
                'value' => $activeCount,
                'sub' => $allocations->count() . ' total allocations',
                'sub_type' => 'neutral',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-truck class="w-5 h-5 text-violet-600 dark:text-violet-400" />'),
                'icon_bg' => '#ede9fe',
                'primary' => true
            ],
            [
                'label' => 'Daily Rate Total',
                'value' => CurrencyHelper::format($totalDailyRate, 0),
                'sub' => 'For active allocations',
                'sub_type' => 'neutral',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-currency-dollar class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />'),
                'icon_bg' => '#d1fae5',
                'primary' => false
            ],
            [
                'label' => 'Fuel Cost (MTD)',
                'value' => CurrencyHelper::format($fuelThisMonth, 0),
                'sub' => number_format($fuelLitersMonth, 0) . ' liters consumed',
                'sub_type' => 'neutral',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-fire class="w-5 h-5 text-orange-600 dark:text-orange-400" />'),
                'icon_bg' => '#ffedd5',
                'primary' => false
            ],
        ];
    }

    public function getHeaderActions(): array
    {
        $pid = $this->pid();
        $cid = $this->cid();

        return [
            Actions\Action::make('allocateEquipment')
                ->label('Allocate Equipment')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->form([
                    Forms\Components\Select::make('asset_id')
                        ->label('Equipment')
                        ->relationship('asset', 'name', fn($q) => $q->where('company_id', $cid))
                        ->searchable()->preload()->required(),
                    Forms\Components\Select::make('operator_id')
                        ->label('Operator')
                        ->relationship('operator', 'name', fn($q) => $q->where('company_id', $cid))
                        ->searchable()->preload(),
                    Forms\Components\DatePicker::make('start_date')->default(now())->required(),
                    Forms\Components\DatePicker::make('end_date'),
                    Forms\Components\TextInput::make('daily_rate')->numeric()
                        ->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix()),
                    Forms\Components\Textarea::make('notes')->rows(2),
                ])
                ->action(function (array $data) use ($pid, $cid) {
                    EquipmentAllocation::create(array_merge($data, [
                        'company_id' => $cid,
                        'cde_project_id' => $pid,
                        'status' => 'active',
                        'created_by' => auth()->id(),
                    ]));
                }),

            Actions\Action::make('logFuel')
                ->label('Log Fuel')
                ->icon('heroicon-o-fire')
                ->color('warning')
                ->form([
                    Forms\Components\Select::make('asset_id')
                        ->label('Equipment')
                        ->relationship('asset', 'name', fn($q) => $q->where('company_id', $cid))
                        ->searchable()->preload()->required(),
                    Forms\Components\DatePicker::make('log_date')->default(now())->required(),
                    Forms\Components\TextInput::make('liters')->numeric()->suffix('L')->required(),
                    Forms\Components\TextInput::make('cost_per_liter')->numeric()
                        ->prefix(fn() => CurrencyHelper::prefix()),
                    Forms\Components\TextInput::make('total_cost')->numeric()
                        ->prefix(fn() => CurrencyHelper::prefix()),
                    Forms\Components\TextInput::make('meter_reading')->numeric()->suffix('hrs/km'),
                    Forms\Components\TextInput::make('filled_by'),
                ])
                ->action(function (array $data) use ($pid, $cid) {
                    EquipmentFuelLog::create(array_merge($data, [
                        'company_id' => $cid,
                        'cde_project_id' => $pid,
                        'created_by' => auth()->id(),
                    ]));
                }),
        ];
    }

    public function table(Table $table): Table
    {
        $pid = $this->pid();

        return $table
            ->query(EquipmentAllocation::query()->where('cde_project_id', $pid)->with(['asset', 'operator']))
            ->columns([
                Tables\Columns\TextColumn::make('asset.name')->label('Equipment')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('operator.name')->label('Operator')->placeholder('—'),
                Tables\Columns\TextColumn::make('start_date')->date('M d')->sortable(),
                Tables\Columns\TextColumn::make('end_date')->date('M d')->placeholder('Ongoing'),
                Tables\Columns\TextColumn::make('daily_rate')->formatStateUsing(CurrencyHelper::formatter())->placeholder('—'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $st) => match ($st) { 'active' => 'success', 'completed' => 'info', default => 'gray'}),
            ])
            ->defaultSort('start_date', 'desc')
            ->recordActions([
                Actions\EditAction::make()
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options(['active' => 'Active', 'completed' => 'Completed', 'cancelled' => 'Cancelled'])->required(),
                        Forms\Components\DatePicker::make('end_date'),
                        Forms\Components\Textarea::make('notes')->rows(2),
                    ]),
                Actions\DeleteAction::make(),
            ]);
    }
}
