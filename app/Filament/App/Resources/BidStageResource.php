<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\BidStageResource\Pages;
use App\Models\BidStage;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BidStageResource extends Resource
{
    protected static ?string $model = BidStage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-list-bullet';
    protected static string|\UnitEnum|null $navigationGroup = 'Company';
    protected static ?string $navigationLabel = 'Bid Stages';
    protected static ?int $navigationSort = 8;
    protected static ?string $navigationParentItem = 'Tenders & Bids';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('company_id', auth()->user()?->company_id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Bid Stage Definition')
                ->icon('heroicon-o-tag')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(100)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn($state, Forms\Set $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(100)
                        ->unique(ignoreRecord: true, modifyRuleUsing: fn($rule) => $rule->where('company_id', auth()->user()?->company_id)),
                    Forms\Components\Select::make('color')
                        ->options([
                            'gray'    => '⬜ Gray',
                            'info'    => '🟦 Blue (Info)',
                            'primary' => '🟪 Purple (Primary)',
                            'warning' => '🟨 Yellow (Warning)',
                            'success' => '🟩 Green (Success)',
                            'danger'  => '🟥 Red (Danger)',
                        ])
                        ->default('gray')
                        ->required(),
                    Forms\Components\TextInput::make('icon')
                        ->placeholder('heroicon-o-document-arrow-up'),
                    Forms\Components\Textarea::make('description')
                        ->rows(2)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),
                    Forms\Components\Toggle::make('is_default')
                        ->label('Default Stage'),
                    Forms\Components\Toggle::make('is_terminal')
                        ->label('Terminal Stage'),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ])->columns(2),

            Section::make('Allowed Transitions')
                ->icon('heroicon-o-arrows-right-left')
                ->schema([
                    Forms\Components\Repeater::make('outgoingTransitions')
                        ->label('Can transition TO:')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('to_stage_id')
                                ->label('Target Stage')
                                ->options(function () {
                                    $companyId = auth()->user()?->company_id;
                                    return $companyId
                                        ? BidStage::where('company_id', $companyId)->where('is_active', true)->orderBy('sort_order')->pluck('name', 'id')
                                        : [];
                                })
                                ->required()
                                ->searchable(),
                            Forms\Components\TextInput::make('required_permission')
                                ->label('Required Permission')
                                ->placeholder('Optional'),
                            Forms\Components\Toggle::make('requires_comment')
                                ->label('Require Comment'),
                            Forms\Components\Toggle::make('is_active')
                                ->label('Active')
                                ->default(true),
                        ])
                        ->columns(4)
                        ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                            $data['company_id'] = auth()->user()->company_id;
                            return $data;
                        })
                        ->columnSpanFull(),
                ])
                ->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width('60px'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->badge()
                    ->color(fn(BidStage $r) => $r->color),
                Tables\Columns\TextColumn::make('slug')
                    ->fontFamily('mono')
                    ->color('gray'),
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_terminal')
                    ->label('Terminal')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('bids_count')
                    ->label('Bids')
                    ->counts('bids')
                    ->badge()
                    ->color('info'),
            ])
            ->defaultSort('sort_order', 'asc')
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make()
                    ->before(function (BidStage $record) {
                        if ($record->bids()->count() > 0) {
                            throw new \Exception('Cannot delete a stage that has bids assigned to it.');
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBidStages::route('/'),
            'create' => Pages\CreateBidStage::route('/create'),
            'edit'   => Pages\EditBidStage::route('/{record}/edit'),
        ];
    }
}
