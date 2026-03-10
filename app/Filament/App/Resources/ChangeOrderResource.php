<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ChangeOrderResource\Pages;
use App\Models\ChangeOrder;
use App\Models\Contract;
use App\Support\CurrencyHelper;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
        return parent::getEloquentQuery()->where('company_id', auth()->user()?->company_id);
    }

    public static function form(Schema $schema): Schema
    {
        $cid = auth()->user()?->company_id;
        return $schema->schema([
            Section::make('Change Order Details')->schema([
                Forms\Components\TextInput::make('reference')
                    ->required()->maxLength(50)->unique(ignoreRecord: true)
                    ->default(fn() => 'CO-' . str_pad(ChangeOrder::where('company_id', $cid)->count() + 1, 3, '0', STR_PAD_LEFT)),
                Forms\Components\TextInput::make('title')->required()->maxLength(255)->columnSpan(2),
                Forms\Components\Select::make('cde_project_id')->label('Project')
                    ->relationship('project', 'name', fn($q) => $q->where('company_id', $cid))
                    ->searchable()->preload()->required(),
                Forms\Components\Select::make('contract_id')->label('Contract')
                    ->relationship('contract', 'title', fn($q) => $q->where('company_id', $cid))
                    ->searchable()->preload(),
                Forms\Components\Select::make('type')->options(ChangeOrder::$types)->default('addition')->required(),
                Forms\Components\Select::make('priority')->options(ChangeOrder::$priorities)->default('medium')->required(),
                Forms\Components\Select::make('initiated_by')->options([
                    'contractor' => 'Contractor',
                    'client' => 'Client',
                    'consultant' => 'Consultant',
                    'engineer' => 'Engineer',
                ]),
                Forms\Components\Select::make('status')->options(ChangeOrder::$statuses)->default('draft')->required(),
            ])->columns(3),

            Section::make('Description & Justification')->schema([
                Forms\Components\RichEditor::make('description')->columnSpanFull(),
                Forms\Components\Textarea::make('reason')->label('Reason for Change')->rows(3)->columnSpanFull(),
            ]),

            Section::make('Impact Assessment')->schema([
                Forms\Components\TextInput::make('estimated_cost')->numeric()
                    ->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix()),
                Forms\Components\TextInput::make('approved_cost')->numeric()
                    ->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix()),
                Forms\Components\TextInput::make('cost_impact')->numeric()
                    ->prefix(fn() => CurrencyHelper::prefix())->suffix(fn() => CurrencyHelper::suffix())
                    ->helperText('Net impact on contract value (negative for omissions)'),
                Forms\Components\TextInput::make('time_impact_days')->numeric()->suffix('days')
                    ->helperText('Additional days needed (negative to reduce)'),
            ])->columns(2),

            Section::make('Dates & Workflow')->schema([
                Forms\Components\DatePicker::make('submitted_date'),
                Forms\Components\DatePicker::make('approved_date'),
                Forms\Components\DatePicker::make('implementation_date'),
                Forms\Components\Select::make('submitted_by')->relationship('submitter', 'name', fn($q) => $q->where('company_id', $cid))->searchable()->preload(),
                Forms\Components\Select::make('reviewed_by')->relationship('reviewer', 'name', fn($q) => $q->where('company_id', $cid))->searchable()->preload(),
                Forms\Components\Select::make('approved_by')->relationship('approver', 'name', fn($q) => $q->where('company_id', $cid))->searchable()->preload(),
                Forms\Components\Textarea::make('approval_notes')->rows(2)->columnSpanFull(),
            ])->columns(3)->collapsed(),

            Forms\Components\Hidden::make('company_id')->default(fn() => auth()->user()?->company_id),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')->searchable()->sortable()->weight('bold')->color('primary'),
                Tables\Columns\TextColumn::make('title')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('project.name')->label('Project')->limit(20),
                Tables\Columns\TextColumn::make('type')->badge()->color(fn(string $s) => match ($s) {
                    'addition' => 'success', 'omission' => 'danger', 'time_extension' => 'info', default => 'warning'
                }),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn(string $s) => match ($s) {
                    'draft' => 'gray', 'submitted' => 'info', 'under_review' => 'warning',
                    'approved' => 'success', 'rejected' => 'danger', 'implemented' => 'primary', default => 'gray'
                }),
                Tables\Columns\TextColumn::make('priority')->badge()->color(fn(string $s) => match ($s) {
                    'critical' => 'danger', 'high' => 'warning', 'medium' => 'info', default => 'gray'
                }),
                Tables\Columns\TextColumn::make('estimated_cost')->formatStateUsing(CurrencyHelper::formatter())->sortable(),
                Tables\Columns\TextColumn::make('cost_impact')->formatStateUsing(CurrencyHelper::formatter())
                    ->color(fn($state) => $state > 0 ? 'danger' : ($state < 0 ? 'success' : 'gray')),
                Tables\Columns\TextColumn::make('time_impact_days')->suffix(' days')->placeholder('—'),
                Tables\Columns\TextColumn::make('submitted_date')->date('M d, Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(ChangeOrder::$statuses),
                Tables\Filters\SelectFilter::make('type')->options(ChangeOrder::$types),
                Tables\Filters\SelectFilter::make('priority')->options(ChangeOrder::$priorities),
                Tables\Filters\SelectFilter::make('cde_project_id')->label('Project')
                    ->relationship('project', 'name'),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn(ChangeOrder $r) => in_array($r->status, ['submitted', 'under_review']))
                    ->form([
                        Forms\Components\TextInput::make('approved_cost')->numeric()->prefix(fn() => CurrencyHelper::prefix()),
                        Forms\Components\Textarea::make('approval_notes')->rows(2),
                    ])
                    ->action(function (ChangeOrder $record, array $data): void {
                        $record->update([
                            'status' => 'approved',
                            'approved_cost' => $data['approved_cost'] ?? $record->estimated_cost,
                            'cost_impact' => $data['approved_cost'] ?? $record->estimated_cost,
                            'approval_notes' => $data['approval_notes'] ?? null,
                            'approved_by' => auth()->id(),
                            'approved_date' => now(),
                        ]);
                    }),
            ])
            ->bulkActions([Actions\DeleteBulkAction::make()]);
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
