<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\TenderResource\Pages;
use App\Models\Company;
use App\Models\Tender;
use App\Support\CurrencyHelper;
use Filament\Actions;
use Filament\Forms;
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
                    Forms\Components\Select::make('assigned_to')
                        ->label('Lead Estimator')
                        ->relationship('assignee', 'name')
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
                    }),
                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Lead')
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->defaultSort('submission_deadline', 'asc')
            ->filters([
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
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenders::route('/'),
            'create' => Pages\CreateTender::route('/create'),
            'edit' => Pages\EditTender::route('/{record}/edit'),
        ];
    }
}
