<?php

namespace App\Filament\App\Resources\TenderResource\RelationManagers;

use App\Models\BidStage;
use App\Models\TenderBid;
use App\Services\StageWorkflowService;
use App\Support\CurrencyHelper;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class BidsRelationManager extends RelationManager
{
    protected static string $relationship = 'bids';
    protected static ?string $title = 'Bids';
    protected static string|\BackedEnum|null $icon = 'heroicon-o-document-arrow-up';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('reference')
                ->label('Bid Reference')
                ->placeholder('e.g. BID-2026-001'),
            Forms\Components\TextInput::make('bidder_name')
                ->label('Bidder / Company')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('bidder_email')
                ->label('Email')
                ->email(),
            Forms\Components\TextInput::make('bidder_phone')
                ->label('Phone'),
            Forms\Components\TextInput::make('bid_amount')
                ->label('Bid Amount')
                ->numeric()
                ->prefix(fn() => CurrencyHelper::prefix())
                ->suffix(fn() => CurrencyHelper::suffix()),
            Forms\Components\Select::make('bid_stage_id')
                ->label('Stage')
                ->options(function () {
                    $companyId = auth()->user()?->company_id;
                    return $companyId
                        ? BidStage::where('company_id', $companyId)->where('is_active', true)->orderBy('sort_order')->pluck('name', 'id')
                        : [];
                })
                ->preload()
                ->searchable()
                ->nullable(),
            Forms\Components\DatePicker::make('submitted_at')
                ->label('Date Submitted'),
            Forms\Components\TextInput::make('technical_score')
                ->label('Technical Score')
                ->numeric()
                ->minValue(0)
                ->maxValue(100),
            Forms\Components\TextInput::make('financial_score')
                ->label('Financial Score')
                ->numeric()
                ->minValue(0)
                ->maxValue(100),
            Forms\Components\TextInput::make('total_score')
                ->label('Total Score')
                ->numeric()
                ->minValue(0)
                ->maxValue(100),
            Forms\Components\Textarea::make('evaluation_notes')
                ->label('Evaluation Notes')
                ->rows(2)
                ->columnSpanFull(),
            Forms\Components\FileUpload::make('document_path')
                ->label('Bid Document')
                ->directory('bid-documents')
                ->acceptedFileTypes(['application/pdf'])
                ->maxSize(20480),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Ref')
                    ->fontFamily('mono')
                    ->searchable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('bidder_name')
                    ->label('Bidder')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(30),
                Tables\Columns\TextColumn::make('bid_amount')
                    ->label('Amount')
                    ->formatStateUsing(CurrencyHelper::formatter(0))
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stage.name')
                    ->label('Stage')
                    ->badge()
                    ->color(fn(TenderBid $r) => $r->stage?->color ?? 'gray')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('technical_score')
                    ->label('Tech')
                    ->suffix('/100')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('financial_score')
                    ->label('Fin')
                    ->suffix('/100')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_score')
                    ->label('Total')
                    ->suffix('/100')
                    ->placeholder('—')
                    ->weight('bold')
                    ->color(fn(?float $state) => match (true) {
                        $state >= 70 => 'success',
                        $state >= 50 => 'warning',
                        $state !== null => 'danger',
                        default => null,
                    }),
                Tables\Columns\TextColumn::make('submitted_at')
                    ->label('Submitted')
                    ->date('M d, Y')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->defaultSort('total_score', 'desc')
            ->headerActions([
                Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['company_id'] = auth()->user()->company_id;
                        $data['created_by'] = auth()->id();

                        // If no stage set, apply default
                        if (empty($data['bid_stage_id'])) {
                            $default = BidStage::getDefault();
                            $data['bid_stage_id'] = $default?->id;
                        }
                        $data['stage_changed_at'] = now();
                        return $data;
                    }),
            ])
            ->actions([
                Actions\Action::make('advance_stage')
                    ->label('Advance')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('primary')
                    ->size('sm')
                    ->form(function (TenderBid $record) {
                        $service = app(StageWorkflowService::class);
                        $nextStages = $service->getNextBidStages($record);
                        return [
                            Forms\Components\Select::make('to_stage_id')
                                ->label('Move to Stage')
                                ->options($nextStages->pluck('name', 'id'))
                                ->required(),
                            Forms\Components\Textarea::make('comment')
                                ->label('Comment / Justification')
                                ->rows(2),
                        ];
                    })
                    ->action(function (TenderBid $record, array $data) {
                        $service = app(StageWorkflowService::class);
                        $service->transitionBid($record, $data['to_stage_id'], $data['comment'] ?? null);
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Bid stage updated')
                            ->send();
                    })
                    ->visible(fn(TenderBid $record) => $record->bid_stage_id && !$record->stage?->is_terminal),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }
}
