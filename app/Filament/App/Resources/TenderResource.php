<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\TenderResource\Pages;
use App\Filament\Concerns\UIStandards;
use App\Models\Company;
use App\Models\Tender;
use App\Models\TenderStage;
use App\Services\StageWorkflowService;
use App\Support\CurrencyHelper;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;

class TenderResource extends Resource
{
    protected static ?string $model = Tender::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-magnifying-glass';
    protected static string|\UnitEnum|null $navigationGroup = 'Company';
    protected static ?string $navigationLabel = 'Tenders & Bids';
    protected static ?int $navigationSort = 6;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('company_id', auth()->user()?->company_id);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()
            ->whereIn('status', ['identified', 'preparing', 'submitted', 'shortlisted'])
            ->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Tender Information')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Forms\Components\TextInput::make('reference')
                        ->label('Reference #')
                        ->placeholder('e.g. TND-2026-001'),
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('client_name')
                        ->label('Client / Issuer'),
                    Forms\Components\Select::make('source')
                        ->options(fn() => Company::options('tender_sources'))
                        ->searchable(),
                    Forms\Components\Select::make('category')
                        ->options(fn() => Company::options('tender_categories'))
                        ->searchable(),
                    Forms\Components\TextInput::make('region')
                        ->placeholder('e.g. Kampala, East Region'),
                    Forms\Components\Select::make('status')
                        ->options(Tender::$statuses)
                        ->default('identified')
                        ->required(),
                    Forms\Components\Select::make('tender_stage_id')
                        ->label('Stage')
                        ->relationship('stage', 'name', function ($query) {
                            $companyId = auth()->user()?->company_id;
                            return $companyId
                                ? $query->where('company_id', $companyId)->where('is_active', true)->orderBy('sort_order')
                                : $query->whereRaw('1=0');
                        })
                        ->preload()
                        ->searchable()
                        ->nullable(),
                    Forms\Components\Select::make('assigned_to')
                        ->label('Lead Estimator')
                        ->relationship('assignee', 'name', function ($query) {
                            $companyId = auth()->user()?->company_id;
                            return $companyId
                                ? $query->where('company_id', $companyId)->where('is_active', true)
                                : $query->whereRaw('1=0');
                        })
                        ->searchable()
                        ->preload()
                        ->nullable(),
                ])->columns(2),

            Section::make('Financials & Schedule')
                ->icon('heroicon-o-currency-dollar')
                ->schema([
                    Forms\Components\TextInput::make('estimated_value')
                        ->label('Estimated Value')
                        ->numeric()
                        ->prefix(fn() => CurrencyHelper::prefix())
                        ->suffix(fn() => CurrencyHelper::suffix()),
                    Forms\Components\TextInput::make('bid_amount')
                        ->label('Our Bid Amount')
                        ->numeric()
                        ->prefix(fn() => CurrencyHelper::prefix())
                        ->suffix(fn() => CurrencyHelper::suffix()),
                    Forms\Components\DatePicker::make('submission_deadline')
                        ->label('Submission Deadline'),
                    Forms\Components\DatePicker::make('submitted_at')
                        ->label('Date Submitted'),
                    Forms\Components\DatePicker::make('decision_date')
                        ->label('Expected Decision'),
                    Forms\Components\TextInput::make('win_probability')
                        ->label('Win Probability')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->suffix('%'),
                ])->columns(3),

            Section::make('Strategy & Notes')
                ->icon('heroicon-o-light-bulb')
                ->schema([
                    Forms\Components\Textarea::make('competitors')
                        ->label('Known Competitors')
                        ->rows(2),
                    Forms\Components\Textarea::make('strategy_notes')
                        ->label('Bid Strategy')
                        ->rows(3)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('loss_reason')
                        ->label('Loss Reason (if applicable)')
                        ->rows(2)
                        ->columnSpanFull(),
                ])->collapsed(),

            Section::make('Documents')
                ->icon('heroicon-o-paper-clip')
                ->schema([
                    Forms\Components\FileUpload::make('document_path')
                        ->label('Main Tender Document')
                        ->directory('tender-documents')
                        ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                        ->maxSize(20480),
                ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Ref')
                    ->searchable()
                    ->fontFamily('mono')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->weight('bold')
                    ->description(fn(Tender $r) => $r->client_name),
                Tables\Columns\TextColumn::make('stage.name')
                    ->label('Stage')
                    ->badge()
                    ->color(fn(Tender $r) => $r->stage?->color ?? 'gray')
                    ->icon(fn(Tender $r) => $r->stage?->icon)
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color('info')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('estimated_value')
                    ->label('Est. Value')
                    ->formatStateUsing(CurrencyHelper::formatter(0))
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bid_amount')
                    ->label('Our Bid')
                    ->formatStateUsing(CurrencyHelper::formatter(0))
                    ->placeholder('—')
                    ->color('success'),
                Tables\Columns\TextColumn::make('submission_deadline')
                    ->label('Deadline')
                    ->date('M d')
                    ->sortable()
                    ->color(fn(Tender $r) => $r->isOverdue() ? 'danger' : null)
                    ->description(function (Tender $r) {
                        $days = $r->days_until_deadline;
                        if ($days === null)
                            return null;
                        if ($days < 0)
                            return abs($days) . 'd overdue';
                        if ($days === 0)
                            return 'Today!';
                        return $days . 'd left';
                    }),
                Tables\Columns\TextColumn::make('bids_count')
                    ->label('Bids')
                    ->counts('bids')
                    ->badge()
                    ->color(fn(?int $state) => $state > 0 ? 'primary' : 'gray')
                    ->placeholder('0')
                    ->sortable(),
                Tables\Columns\TextColumn::make('win_probability')
                    ->label('Win %')
                    ->suffix('%')
                    ->placeholder('—')
                    ->color(fn(?int $state) => match (true) {
                        $state >= 70 => 'success',
                        $state >= 40 => 'warning',
                        $state !== null => 'danger',
                        default => null,
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'identified' => 'gray',
                        'preparing' => 'info',
                        'submitted' => 'primary',
                        'shortlisted' => 'warning',
                        'awarded' => 'success',
                        'lost' => 'danger',
                        'withdrawn' => 'gray',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Lead')
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->defaultSort('submission_deadline', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('tender_stage_id')
                    ->label('Stage')
                    ->relationship('stage', 'name', fn($q) => $q?->where('company_id', auth()->user()?->company_id)->where('is_active', true)->orderBy('sort_order'))
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('status')
                    ->options(Tender::$statuses)
                    ->multiple(),
                Tables\Filters\SelectFilter::make('category')
                    ->options(fn() => Company::options('tender_categories')),
                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue')
                    ->query(fn($q) => $q->whereNotIn('status', ['submitted', 'awarded', 'lost', 'withdrawn'])
                        ->whereNotNull('submission_deadline')
                        ->where('submission_deadline', '<', now()))
                    ->toggle(),
                Tables\Filters\Filter::make('has_bids')
                    ->label('Has Bids')
                    ->query(fn($q) => $q->has('bids'))
                    ->toggle(),
            ])
            ->actions([
                Actions\Action::make('ai_analyse')
                    ->label('AI')
                    ->icon('heroicon-o-sparkles')
                    ->color('info')
                    ->tooltip('AI Analyse this tender')
                    ->modalHeading(fn(Tender $record) => '🤖 AI Analysis: ' . $record->title)
                    ->modalWidth('3xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(function (Tender $record) {
                        $ai = app(\App\Services\AiAssistantService::class);
                        if (!$ai->isAvailable()) {
                            return new HtmlString('<div class="p-4 text-amber-600">⚠️ AI not configured.</div>');
                        }
                        $desc = implode("\n", array_filter([
                            "Tender: {$record->title}",
                            $record->client_name ? "Client: {$record->client_name}" : null,
                            $record->category ? "Category: {$record->category}" : null,
                            $record->estimated_value ? "Value: {$record->estimated_value}" : null,
                            $record->strategy_notes ? "Notes: {$record->strategy_notes}" : null,
                        ]));
                        $result = $ai->extractTenderRequirements($desc);
                        if (empty($result)) {
                            return new HtmlString('<div class="p-4 text-gray-500">Add more tender details for better analysis.</div>');
                        }
                        $rec = $result['bid_recommendation'] ?? 'N/A';
                        $c = $rec === 'Bid' ? 'green' : ($rec === 'No-Bid' ? 'red' : 'amber');
                        $html = "<div class='space-y-3 text-sm'>";
                        $html .= "<div class='p-3 rounded-xl bg-{$c}-50 dark:bg-{$c}-950/30 border border-{$c}-200 dark:border-{$c}-800'><p class='font-bold text-lg'>{$rec}</p><p class='text-gray-600 dark:text-gray-400'>" . e($result['bid_reason'] ?? '') . "</p></div>";
                        foreach (['key_requirements' => '📋 Requirements', 'risks' => '⚠️ Risks'] as $k => $l) {
                            if (!empty($result[$k])) {
                                $html .= "<div class='p-3 bg-gray-50 dark:bg-gray-800 rounded-xl'><p class='font-semibold mb-1'>{$l}</p><ul class='list-disc list-inside space-y-0.5 text-gray-700 dark:text-gray-300'>";
                                foreach ($result[$k] as $item) { $html .= "<li>" . e($item) . "</li>"; }
                                $html .= "</ul></div>";
                            }
                        }
                        $html .= "</div>";
                        return new HtmlString($html);
                    }),
                Actions\ViewAction::make(),
                Actions\EditAction::make(),

                // ── ISO 19650 / ISO 9001 Status Transitions ──
                Actions\Action::make('beginPreparation')
                    ->label('Begin Preparation')
                    ->icon('heroicon-o-play')
                    ->color('info')
                    ->action(function (Tender $record) {
                        $record->transitionTo('preparing');
                        Notification::make()->title('Tender moved to preparation')->success()->send();
                    })
                    ->hidden(fn (Tender $record) => !$record->canTransitionTo('preparing')),

                Actions\Action::make('submitBid')
                    ->label('Submit Bid')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->action(function (Tender $record) {
                        $record->transitionTo('submitted');
                        Notification::make()->title('Tender submitted')->success()->send();
                    })
                    ->hidden(fn (Tender $record) => !$record->canTransitionTo('submitted')),

                Actions\Action::make('markShortlisted')
                    ->label('Mark Shortlisted')
                    ->icon('heroicon-o-user-group')
                    ->color('warning')
                    ->action(function (Tender $record) {
                        $record->transitionTo('shortlisted');
                        Notification::make()->title('Tender shortlisted')->warning()->send();
                    })
                    ->hidden(fn (Tender $record) => !$record->canTransitionTo('shortlisted')),

                Actions\Action::make('award')
                    ->label('Award')
                    ->icon('heroicon-o-trophy')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Tender $record) {
                        $record->transitionTo('awarded');
                        Notification::make()->title('Tender awarded!')->success()->send();
                    })
                    ->hidden(fn (Tender $record) => !$record->canTransitionTo('awarded')),

                Actions\Action::make('lose')
                    ->label('Mark Lost')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Tender $record) {
                        $record->transitionTo('lost');
                        Notification::make()->title('Tender marked as lost')->danger()->send();
                    })
                    ->hidden(fn (Tender $record) => !$record->canTransitionTo('lost')),

                Actions\Action::make('revise')
                    ->label('Revise')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('gray')
                    ->action(function (Tender $record) {
                        $record->transitionTo('identified');
                        Notification::make()->title('Tender returned to identification')->info()->send();
                    })
                    ->hidden(fn (Tender $record) => !$record->canTransitionTo('identified')),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\App\Resources\TenderResource\RelationManagers\BidsRelationManager::class,
            \App\Filament\App\Resources\TenderResource\RelationManagers\AuditLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenders::route('/'),
            'create' => Pages\CreateTender::route('/create'),
            'view' => Pages\ViewTender::route('/{record}'),
            'edit' => Pages\EditTender::route('/{record}/edit'),
        ];
    }
}
