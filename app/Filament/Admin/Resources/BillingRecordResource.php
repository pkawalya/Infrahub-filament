<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BillingRecordResource\Pages;
use App\Models\BillingRecord;
use Filament\Actions;
use Filament\Schemas;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class BillingRecordResource extends Resource
{
    protected static ?string $model = BillingRecord::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static string|\UnitEnum|null $navigationGroup = 'Subscription & Billing';
    protected static ?string $label = 'Billing Record';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Billing Period')->schema([
                Forms\Components\Select::make('company_id')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('period')
                    ->required()
                    ->placeholder('2026-03')
                    ->helperText('Format: YYYY-MM'),
                Forms\Components\DatePicker::make('period_start'),
                Forms\Components\DatePicker::make('period_end'),
                Forms\Components\Select::make('status')
                    ->options(BillingRecord::$statuses)
                    ->default('draft')
                    ->required(),
            ])->columns(2),

            Schemas\Components\Section::make('Fee Breakdown')->schema([
                Forms\Components\TextInput::make('base_platform_fee')->numeric()->prefix('$')->default(0),
                Forms\Components\TextInput::make('project_fees')->numeric()->prefix('$')->default(0),
                Forms\Components\TextInput::make('module_fees')->numeric()->prefix('$')->default(0),
                Forms\Components\TextInput::make('addon_fees')->numeric()->prefix('$')->default(0),
                Forms\Components\TextInput::make('discount_amount')->numeric()->prefix('$')->default(0),
                Forms\Components\TextInput::make('tax_amount')->numeric()->prefix('$')->default(0),
                Forms\Components\TextInput::make('total_amount')->numeric()->prefix('$')->required(),
            ])->columns(3),

            Schemas\Components\Section::make('Usage Metrics')->schema([
                Forms\Components\TextInput::make('active_projects_count')->numeric()->default(0),
                Forms\Components\TextInput::make('active_users_count')->numeric()->default(0),
                Forms\Components\TextInput::make('storage_used_gb')->numeric()->suffix('GB')->default(0),
            ])->columns(3),

            Schemas\Components\Section::make('Payment')->schema([
                Forms\Components\DateTimePicker::make('finalized_at'),
                Forms\Components\DateTimePicker::make('paid_at'),
                Forms\Components\TextInput::make('payment_reference')->maxLength(255),
                Forms\Components\Textarea::make('notes')->rows(2),
            ])->columns(2)->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name')->sortable()->searchable()->label('Company'),
                Tables\Columns\TextColumn::make('period')->sortable()->searchable()
                    ->badge()->color('gray'),
                Tables\Columns\TextColumn::make('total_amount')->money('USD')->sortable()
                    ->weight('bold')->label('Total'),
                Tables\Columns\TextColumn::make('base_platform_fee')->money('USD')->label('Base Fee')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('project_fees')->money('USD')->label('Projects')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('active_projects_count')->label('Projects')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('active_users_count')->label('Users')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'finalized' => 'warning',
                        'paid' => 'success',
                        'overdue' => 'danger',
                        'void' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('period_end')->date()->label('Due')
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_at')->dateTime('M d, Y')->label('Paid On')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('period', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(BillingRecord::$statuses),
                Tables\Filters\SelectFilter::make('company_id')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->label('Company'),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\Action::make('markPaid')
                    ->label('Mark Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(BillingRecord $record) => in_array($record->status, ['finalized', 'overdue']))
                    ->requiresConfirmation()
                    ->schema([
                        Forms\Components\TextInput::make('payment_reference')
                            ->label('Payment Reference')
                            ->placeholder('e.g. TXN-12345'),
                    ])
                    ->action(function (BillingRecord $record, array $data): void {
                        $record->markPaid($data['payment_reference'] ?? null);
                    }),
                Actions\Action::make('void')
                    ->label('Void')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(BillingRecord $record) => $record->status === 'draft')
                    ->requiresConfirmation()
                    ->action(fn(BillingRecord $record) => $record->update(['status' => 'void'])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBillingRecords::route('/'),
            'create' => Pages\CreateBillingRecord::route('/create'),
            'edit' => Pages\EditBillingRecord::route('/{record}/edit'),
        ];
    }
}
