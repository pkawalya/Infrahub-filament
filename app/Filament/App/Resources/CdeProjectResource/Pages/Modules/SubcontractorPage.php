<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Filament\App\Concerns\ExportsTableCsv;
use App\Models\Company;
use App\Models\SubcontractorPackage;
use App\Models\Subcontractor;
use App\Support\CurrencyHelper;
use Filament\Actions;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class SubcontractorPage extends BaseModulePage implements HasTable
{
    use InteractsWithTable, ExportsTableCsv;

    protected static string $moduleCode = 'subcontractors';
    protected static ?string $title = 'Subcontractors';
    protected string $view = 'filament.app.pages.modules.subcontractors';
    protected static ?string $navigationLabel = 'Subcontractors';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

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
        $packages = SubcontractorPackage::where('cde_project_id', $pid)->get();

        $activeCount = $packages->whereIn('status', ['awarded', 'in_progress'])->count();
        $totalValue = $packages->sum('contract_value');
        $totalPaid = $packages->sum('paid_to_date');
        $avgProgress = $packages->whereIn('status', ['awarded', 'in_progress'])->avg('progress_percent') ?? 0;

        return [
            [
                'label' => 'Active Packages',
                'value' => $activeCount,
                'sub' => $packages->count() . ' total work packages',
                'sub_type' => 'neutral',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-user-group class="w-5 h-5 text-blue-600 dark:text-blue-400" />'),
                'icon_bg' => '#dbeafe',
                'primary' => true
            ],
            [
                'label' => 'Contract Value',
                'value' => CurrencyHelper::format($totalValue, 0),
                'sub' => CurrencyHelper::format($totalPaid, 0) . ' paid to date',
                'sub_type' => 'neutral',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-currency-dollar class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />'),
                'icon_bg' => '#d1fae5',
                'primary' => false
            ],
            [
                'label' => 'Avg Progress',
                'value' => round($avgProgress) . '%',
                'sub' => 'Across active packages',
                'sub_type' => $avgProgress >= 50 ? 'success' : 'neutral',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-chart-bar class="w-5 h-5 text-amber-600 dark:text-amber-400" />'),
                'icon_bg' => '#fef3c7',
                'primary' => false
            ],
        ];
    }

    public function getHeaderActions(): array
    {
        $pid = $this->pid();
        $cid = $this->cid();

        return [
            Actions\Action::make('addPackage')
                ->label('Add Work Package')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->form([
                    Forms\Components\Select::make('subcontractor_id')
                        ->label('Subcontractor')
                        ->relationship('subcontractor', 'name', fn($q) => $q->where('status', 'active'))
                        ->searchable()
                        ->preload(false)
                        ->required()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')->required()->maxLength(255),
                            Forms\Components\TextInput::make('contact_person')->maxLength(255),
                            Forms\Components\TextInput::make('email')->email()->maxLength(255),
                            Forms\Components\TextInput::make('phone')->maxLength(50),
                            Forms\Components\TextInput::make('specialty')->maxLength(255),
                            Forms\Components\TextInput::make('registration_number')->label('Reg. Number')->maxLength(100),
                        ])
                        ->createOptionUsing(function (array $data) use ($cid): int {
                            return Subcontractor::create(array_merge($data, [
                                'company_id' => $cid,
                                'status'     => 'active',
                            ]))->id;
                        }),
                    Forms\Components\TextInput::make('title')->required()->maxLength(255),
                    Forms\Components\TextInput::make('scope_of_work')->maxLength(500),
                    Forms\Components\TextInput::make('contract_value')->numeric()
                        ->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix()),
                    Forms\Components\DatePicker::make('start_date'),
                    Forms\Components\DatePicker::make('end_date'),
                    Forms\Components\Select::make('status')
                        ->options(SubcontractorPackage::$statuses)->default('draft'),
                ])
                ->action(function (array $data) use ($pid, $cid) {
                    SubcontractorPackage::create(array_merge($data, [
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
            ->query(SubcontractorPackage::query()->where('cde_project_id', $pid)->with('subcontractor'))
            ->columns([
                Tables\Columns\TextColumn::make('subcontractor.name')->label('Subcontractor')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('title')->label('Package')->limit(30)->searchable(),
                Tables\Columns\TextColumn::make('contract_value')->formatStateUsing(CurrencyHelper::formatter(0))->placeholder('—'),
                Tables\Columns\TextColumn::make('paid_to_date')->formatStateUsing(CurrencyHelper::formatter(0))->placeholder('0'),
                Tables\Columns\TextColumn::make('progress_percent')->label('Progress')
                    ->formatStateUsing(fn(int $state) => $state . '%')
                    ->color(fn(int $state) => $state >= 80 ? 'success' : ($state >= 40 ? 'info' : 'warning')),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn(string $st) => match ($st) {
                        'draft' => 'gray', 'awarded' => 'info', 'in_progress' => 'warning',
                        'completed' => 'success', 'terminated' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('end_date')->date('M d')->placeholder('—')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Actions\EditAction::make()
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options(SubcontractorPackage::$statuses)->required(),
                        Forms\Components\TextInput::make('progress_percent')
                            ->numeric()->minValue(0)->maxValue(100)->suffix('%'),
                        Forms\Components\TextInput::make('paid_to_date')->numeric()
                            ->prefix(fn() => CurrencyHelper::prefix()),
                        Forms\Components\Textarea::make('notes')->rows(2),
                    ]),
                Actions\DeleteAction::make(),
            ]);
    }
}
