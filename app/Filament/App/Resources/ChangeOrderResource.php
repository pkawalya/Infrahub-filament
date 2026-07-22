<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ChangeOrderResource\Pages;
use App\Models\ChangeOrder;
use App\Models\Contract;
use App\Support\CurrencyHelper;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChangeOrderResource extends Resource
{
    protected static ?string $model = ChangeOrder::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-plus';
    protected static string|\UnitEnum|null $navigationGroup = 'Projects';
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationLabel = 'Change Orders';
    protected static ?string $modelLabel = 'Change Order';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScope(SoftDeletingScope::class)
            ->where('company_id', auth()->user()?->company_id);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Change Order Details')->schema([
                Forms\Components\TextInput::make('co_number')->label('CO Number')
                    ->required()->maxLength(50)->unique(ignoreRecord: true)
                    ->default(fn() => 'CO-' . str_pad(
                        ChangeOrder::where('company_id', auth()->user()?->company_id)->count() + 1,
                        3, '0', STR_PAD_LEFT
                    )),
                Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpan(2),
                Forms\Components\Select::make('contract_id')->label('Contract')
                    ->relationship('contract', 'title', fn($q) => $q?->where('company_id', auth()->user()?->company_id))
                    ->searchable()->preload()->required(),
                Forms\Components\Select::make('status')->options(ChangeOrder::$statuses)->default('draft')->required(),
            ])->columns(3),

            Section::make('Description')->schema([
                Forms\Components\RichEditor::make('description')->columnSpanFull(),
            ]),

            Section::make('Impact Assessment')->schema([
                Forms\Components\TextInput::make('amount')->numeric()
                    ->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix()),
                Forms\Components\TextInput::make('time_extension_days')->numeric()->suffix('days')
                    ->helperText('Additional days needed'),
            ])->columns(2),

            Section::make('People & Approval')->schema([
                Forms\Components\Select::make('requested_by')
                    ->relationship('requester', 'name', fn($q) => $q?->where('company_id', auth()->user()?->company_id))
                    ->searchable()->preload(),
                Forms\Components\Select::make('approved_by')
                    ->relationship('approver', 'name', fn($q) => $q?->where('company_id', auth()->user()?->company_id))
                    ->searchable()->preload(),
                Forms\Components\DateTimePicker::make('approved_at'),
            ])->columns(3)->collapsed(),

            Forms\Components\Hidden::make('company_id')->default(fn() => auth()->user()?->company_id),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('co_number')->label('CO #')->searchable()->sortable()->weight('bold')->color('primary'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('contract.title')->label('Contract')->limit(20),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn(string $state) => match ($state) {
                    'draft' => 'gray', 'submitted' => 'info', 'under_review' => 'warning',
                    'approved' => 'success', 'rejected' => 'danger', 'implemented' => 'primary', default => 'gray'
                }),
                Tables\Columns\TextColumn::make('amount')->formatStateUsing(CurrencyHelper::formatter())->sortable(),
                Tables\Columns\TextColumn::make('time_extension_days')->suffix(' days')->placeholder('—'),
                Tables\Columns\TextColumn::make('requester.name')->label('Requested By'),
                Tables\Columns\TextColumn::make('approved_at')->date('M d, Y')->sortable()->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('status')->options(ChangeOrder::$statuses),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\RestoreAction::make(),

                // ── ISO 19650 Status Transitions ──
                Actions\Action::make('submit')
                    ->label('Submit')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->action(function (ChangeOrder $record) {
                        $record->transitionTo('submitted');
                        Notification::make()->title('Change order submitted')->success()->send();
                    })
                    ->hidden(fn (ChangeOrder $record) => !$record->canTransitionTo('submitted')),

                Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form(fn (ChangeOrder $record) => [
                        Forms\Components\TextInput::make('approved_cost')
                            ->numeric()
                            ->prefix(fn() => CurrencyHelper::prefix())
                            ->default($record->amount),
                        Forms\Components\Textarea::make('approval_notes')->rows(2),
                    ])
                    ->action(function (ChangeOrder $record, array $data): void {
                        $record->transitionTo('approved');
                        $record->update([
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()->title('Change order approved')->success()->send();
                    })
                    ->hidden(fn (ChangeOrder $record) => !$record->canTransitionTo('approved')),

                Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (ChangeOrder $record) {
                        $record->transitionTo('rejected');
                        Notification::make()->title('Change order rejected')->danger()->send();
                    })
                    ->hidden(fn (ChangeOrder $record) => !$record->canTransitionTo('rejected')),

                Actions\Action::make('implement')
                    ->label('Implement')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (ChangeOrder $record) {
                        $record->transitionTo('implemented');
                        Notification::make()->title('Change order implemented')->success()->send();
                    })
                    ->hidden(fn (ChangeOrder $record) => !$record->canTransitionTo('implemented')),

                Actions\Action::make('sendBackForReview')
                    ->label('Send Back for Review')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (ChangeOrder $record) {
                        $record->transitionTo('under_review');
                        Notification::make()->title('Change order returned to review')->warning()->send();
                    })
                    ->hidden(fn (ChangeOrder $record) => !$record->canTransitionTo('under_review')),

                Actions\Action::make('revise')
                    ->label('Revise')
                    ->icon('heroicon-o-pencil-square')
                    ->color('gray')
                    ->action(function (ChangeOrder $record) {
                        $record->transitionTo('draft');
                        Notification::make()->title('Change order returned to draft')->info()->send();
                    })
                    ->hidden(fn (ChangeOrder $record) => !$record->canTransitionTo('draft')),

                Actions\DeleteAction::make(),
                Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
                Actions\RestoreBulkAction::make(),
                Actions\ForceDeleteBulkAction::make(),
            ])
            ->persistFiltersInSession()
            ->persistSearchInSession();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChangeOrders::route('/'),
            'create' => Pages\CreateChangeOrder::route('/create'),
            'view' => Pages\ViewChangeOrder::route('/{record}'),
            'edit' => Pages\EditChangeOrder::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->whereIn('status', ['submitted', 'under_review'])->count() ?: null;
    }
}
