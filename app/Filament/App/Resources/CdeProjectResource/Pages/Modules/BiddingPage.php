<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages\Modules;

use App\Filament\App\Resources\CdeProjectResource\Pages\BaseModulePage;
use App\Filament\App\Concerns\ExportsTableCsv;
use App\Models\BidStage;
use App\Models\Tender;
use App\Models\TenderBid;
use App\Models\TenderStage;
use App\Support\CurrencyHelper;
use Filament\Actions;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Livewire\Attributes\Url;

class BiddingPage extends BaseModulePage implements HasTable
{
    use InteractsWithTable, ExportsTableCsv;

    protected static string $moduleCode = 'bidding';
    protected static ?string $title = 'Tenders & Bids';
    protected string $view = 'filament.app.pages.modules.bidding';
    protected static ?string $navigationLabel = 'Tenders & Bids';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-trophy';

    #[Url]
    public string $activeTab = 'tenders';

    protected function cid(): int
    {
        return $this->record->company_id;
    }

    // ─── Stats ──────────────────────────────────────────────────

    public function getStats(): array
    {
        $cid = $this->cid();
        $tenders = Tender::where('company_id', $cid)->get();
        $bids = TenderBid::where('company_id', $cid)->get();

        $activeTenders = $tenders->whereIn('status', ['identified', 'preparing', 'submitted', 'shortlisted'])->count();
        $totalValue = $tenders->sum('estimated_value');
        $wonCount = $tenders->where('status', 'awarded')->count();
        $winRate = $tenders->count() > 0
            ? round(($wonCount / $tenders->count()) * 100)
            : 0;

        return [
            [
                'label' => 'Active Tenders',
                'value' => $activeTenders,
                'sub' => $tenders->count() . ' total tenders',
                'sub_type' => 'neutral',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-trophy class="w-5 h-5 text-blue-600 dark:text-blue-400" />'),
                'icon_bg' => '#dbeafe',
                'primary' => true
            ],
            [
                'label' => 'Pipeline Value',
                'value' => CurrencyHelper::format($totalValue, 0),
                'sub' => $activeTenders . ' in pipeline',
                'sub_type' => 'neutral',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-banknotes class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />'),
                'icon_bg' => '#d1fae5',
                'primary' => false
            ],
            [
                'label' => 'Win Rate',
                'value' => $winRate . '%',
                'sub' => $wonCount . ' won of ' . $tenders->count(),
                'sub_type' => $winRate >= 50 ? 'success' : ($winRate >= 25 ? 'neutral' : 'danger'),
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-chart-bar class="w-5 h-5 text-amber-600 dark:text-amber-400" />'),
                'icon_bg' => '#fef3c7',
                'primary' => false
            ],
            [
                'label' => 'Total Bids',
                'value' => $bids->count(),
                'sub' => $bids->whereNotNull('evaluated_at')->count() . ' evaluated',
                'sub_type' => 'neutral',
                'icon_svg' => \Illuminate\Support\Facades\Blade::render('<x-heroicon-o-document-text class="w-5 h-5 text-violet-600 dark:text-violet-400" />'),
                'icon_bg' => '#ede9fe',
                'primary' => false
            ],
        ];
    }

    // ─── Header Actions ─────────────────────────────────────────

    public function getHeaderActions(): array
    {
        $cid = $this->cid();

        return [
            Actions\Action::make('addTender')
                ->label('New Tender')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->form($this->tenderFormSchema())
                ->action(function (array $data) use ($cid) {
                    Tender::create(array_merge($data, [
                        'company_id' => $cid,
                        'created_by' => auth()->id(),
                    ]));
                }),
            Actions\Action::make('addBid')
                ->label('New Bid')
                ->icon('heroicon-o-document-plus')
                ->color('success')
                ->form($this->bidFormSchema())
                ->action(function (array $data) use ($cid) {
                    TenderBid::create(array_merge($data, [
                        'company_id' => $cid,
                        'created_by' => auth()->id(),
                    ]));
                }),
        ];
    }

    // ─── Tables ─────────────────────────────────────────────────

    public function table(Table $table): Table
    {
        return match ($this->activeTab) {
            'bids' => $this->bidsTable($table),
            default => $this->tendersTable($table),
        };
    }

    protected function tendersTable(Table $table): Table
    {
        $cid = $this->cid();

        return $table
            ->query(Tender::query()->where('company_id', $cid)->with(['stage', 'assignee', 'bids']))
            ->columns([
                Tables\Columns\TextColumn::make('reference')->searchable()->sortable()->weight('bold')->color('primary'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(35),
                Tables\Columns\TextColumn::make('client_name')->label('Client')->limit(20),
                Tables\Columns\TextColumn::make('stage.name')->label('Stage')->badge()
                    ->color(fn ($record) => $record->stage?->color ?? 'gray'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn (string $state) => match ($state) {
                        'identified' => 'gray', 'preparing' => 'info', 'submitted' => 'warning',
                        'shortlisted' => 'primary', 'awarded' => 'success', 'lost' => 'danger',
                        'withdrawn' => 'gray', default => 'gray'
                    }),
                Tables\Columns\TextColumn::make('estimated_value')->formatStateUsing(CurrencyHelper::formatter(0))->sortable(),
                Tables\Columns\TextColumn::make('win_probability')->suffix('%')->sortable()
                    ->color(fn ($state) => $state >= 60 ? 'success' : ($state >= 30 ? 'warning' : 'danger')),
                Tables\Columns\TextColumn::make('bids_count')->counts('bids')->label('Bids')->badge()->color('info'),
                Tables\Columns\TextColumn::make('submission_deadline')->date('M d, Y')->sortable()
                    ->color(fn ($record) => $record->isOverdue() ? 'danger' : null),
                Tables\Columns\TextColumn::make('assignee.name')->label('Assigned To')->limit(15),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(Tender::$statuses),
                Tables\Filters\SelectFilter::make('category')->options(Tender::$categories),
                Tables\Filters\SelectFilter::make('source')->options(Tender::$sources),
            ])
            ->recordActions([
                Actions\EditAction::make()
                    ->form($this->tenderFormSchema()),
                Actions\DeleteAction::make(),
            ]);
    }

    protected function bidsTable(Table $table): Table
    {
        $cid = $this->cid();

        return $table
            ->query(TenderBid::query()->where('company_id', $cid)->with(['tender', 'stage', 'evaluator']))
            ->columns([
                Tables\Columns\TextColumn::make('reference')->searchable()->sortable()->weight('bold')->color('primary'),
                Tables\Columns\TextColumn::make('tender.title')->label('Tender')->limit(25)->searchable(),
                Tables\Columns\TextColumn::make('bidder_name')->label('Bidder')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('bid_amount')->formatStateUsing(CurrencyHelper::formatter(0))->sortable(),
                Tables\Columns\TextColumn::make('stage.name')->label('Stage')->badge()
                    ->color(fn ($record) => $record->stage?->color ?? 'gray'),
                Tables\Columns\TextColumn::make('technical_score')->suffix('/100')->placeholder('—')
                    ->color(fn ($state) => $state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),
                Tables\Columns\TextColumn::make('financial_score')->suffix('/100')->placeholder('—')
                    ->color(fn ($state) => $state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),
                Tables\Columns\TextColumn::make('total_score')->suffix('/100')->sortable()->weight('bold')->placeholder('—')
                    ->color(fn ($state) => $state >= 70 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),
                Tables\Columns\TextColumn::make('submitted_at')->date('M d, Y')->sortable(),
                Tables\Columns\TextColumn::make('evaluator.name')->label('Evaluated By')->limit(15)->placeholder('—'),
            ])
            ->defaultSort('total_score', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('tender_id')->label('Tender')
                    ->options(\App\Models\Tender::where('company_id', $cid)->pluck('title', 'id')),
                Tables\Filters\SelectFilter::make('bid_stage_id')->label('Stage')
                    ->options(\App\Models\BidStage::where('company_id', $cid)->pluck('name', 'id')),
            ])
            ->recordActions([
                Actions\EditAction::make()
                    ->form($this->bidFormSchema()),
                Actions\DeleteAction::make(),
            ]);
    }

    // ─── Form Schemas ───────────────────────────────────────────

    protected function tenderFormSchema(): array
    {
        $cid = $this->cid();
        return [
            Forms\Components\TextInput::make('reference')->required()->maxLength(50)
                ->default(fn () => 'TND-' . str_pad(Tender::where('company_id', $cid)->count() + 1, 3, '0', STR_PAD_LEFT)),
            Forms\Components\TextInput::make('title')->required()->maxLength(255),
            Forms\Components\TextInput::make('client_name')->maxLength(255),
            Forms\Components\Select::make('source')->options(Tender::$sources),
            Forms\Components\Select::make('status')->options(Tender::$statuses)->default('identified')->required(),
            Forms\Components\Select::make('tender_stage_id')->label('Stage')
                ->options(TenderStage::where('company_id', $cid)->where('is_active', true)->orderBy('sort_order')->pluck('name', 'id')),
            Forms\Components\Select::make('category')->options(Tender::$categories),
            Forms\Components\TextInput::make('region')->maxLength(100),
            Forms\Components\TextInput::make('estimated_value')->numeric()
                ->prefix(fn () => CurrencyHelper::prefix())->suffix(fn () => CurrencyHelper::suffix()),
            Forms\Components\TextInput::make('bid_amount')->numeric()
                ->prefix(fn () => CurrencyHelper::prefix())->suffix(fn () => CurrencyHelper::suffix()),
            Forms\Components\TextInput::make('win_probability')->numeric()->minValue(0)->maxValue(100)->suffix('%'),
            Forms\Components\DatePicker::make('submission_deadline'),
            Forms\Components\Select::make('assigned_to')
                ->label('Lead Estimator')
                ->options(\App\Models\User::where('company_id', $cid)->where('is_active', true)->pluck('name', 'id'))
                ->searchable()->preload(),
            Forms\Components\Textarea::make('strategy_notes')->rows(2)->columnSpanFull(),
        ];
    }

    protected function bidFormSchema(): array
    {
        $cid = $this->cid();
        return [
            Forms\Components\TextInput::make('reference')->required()->maxLength(50)
                ->default(fn () => 'BID-' . str_pad(TenderBid::where('company_id', $cid)->count() + 1, 3, '0', STR_PAD_LEFT)),
            Forms\Components\Select::make('tender_id')->label('Tender')
                ->options(Tender::where('company_id', $cid)->pluck('title', 'id'))
                ->searchable()->required(),
            Forms\Components\TextInput::make('bidder_name')->required()->maxLength(255),
            Forms\Components\TextInput::make('bidder_email')->email()->maxLength(255),
            Forms\Components\TextInput::make('bidder_phone')->maxLength(50),
            Forms\Components\TextInput::make('bid_amount')->numeric()
                ->prefix(fn () => CurrencyHelper::prefix())->suffix(fn () => CurrencyHelper::suffix()),
            Forms\Components\Select::make('bid_stage_id')->label('Stage')
                ->options(BidStage::where('company_id', $cid)->where('is_active', true)->orderBy('sort_order')->pluck('name', 'id')),
            Forms\Components\TextInput::make('technical_score')->numeric()->minValue(0)->maxValue(100)->suffix('/100'),
            Forms\Components\TextInput::make('financial_score')->numeric()->minValue(0)->maxValue(100)->suffix('/100'),
            Forms\Components\TextInput::make('total_score')->numeric()->minValue(0)->maxValue(100)->suffix('/100'),
            Forms\Components\DatePicker::make('submitted_at'),
            Forms\Components\Textarea::make('evaluation_notes')->rows(2)->columnSpanFull(),
        ];
    }
}
