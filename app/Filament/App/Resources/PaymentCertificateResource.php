<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\PaymentCertificateResource\Pages;
use App\Models\PaymentCertificate;
use App\Support\CurrencyHelper;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentCertificateResource extends Resource
{
    protected static ?string $model = PaymentCertificate::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static string|\UnitEnum|null $navigationGroup = 'Projects';
    protected static ?int $navigationSort = 8;
    protected static ?string $navigationLabel = 'Payment Certs';
    protected static ?string $modelLabel = 'Payment Certificate';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('company_id', auth()->user()?->company_id);
    }

    public static function form(Schema $schema): Schema
    {
        $cf = fn() => CurrencyHelper::prefix();
        $cs = fn() => CurrencyHelper::suffix();

        return $schema->schema([
            Section::make('Certificate Details')->schema([
                Forms\Components\TextInput::make('certificate_number')->required()->unique(ignoreRecord: true)
                    ->default(fn() => 'IPC-' . str_pad(PaymentCertificate::where('company_id', auth()->user()?->company_id)->count() + 1, 3, '0', STR_PAD_LEFT)),
                Forms\Components\Select::make('type')->options(PaymentCertificate::$types)->default('interim')->required(),
                Forms\Components\Select::make('status')->options(PaymentCertificate::$statuses)->default('draft')->required(),
                Forms\Components\Select::make('cde_project_id')->label('Project')
                    ->relationship('project', 'name', fn($q) => $q?->where('company_id', auth()->user()?->company_id))
                    ->searchable()->preload()->required(),
                Forms\Components\Select::make('contract_id')->label('Contract')
                    ->relationship('contract', 'title', fn($q) => $q?->where('company_id', auth()->user()?->company_id))
                    ->searchable()->preload(),
                Forms\Components\DatePicker::make('period_from')->required(),
                Forms\Components\DatePicker::make('period_to')->required(),
            ])->columns(3),

            Section::make('Valuation')->schema([
                Forms\Components\TextInput::make('gross_value_to_date')->numeric()->prefix($cf())->suffix($cs())
                    ->helperText('Total value of work done to date'),
                Forms\Components\TextInput::make('previous_certified')->numeric()->prefix($cf())->suffix($cs())
                    ->helperText('Amount already certified in previous certificates'),
                Forms\Components\TextInput::make('this_certificate_gross')->numeric()->prefix($cf())->suffix($cs())
                    ->helperText('Auto-calculated: gross_to_date - previous_certified')
                    ->disabled()->dehydrated(),
                Forms\Components\TextInput::make('variations_amount')->numeric()->prefix($cf())->suffix($cs())
                    ->helperText('Change order amounts applicable this period'),
                Forms\Components\TextInput::make('materials_on_site')->numeric()->prefix($cf())->suffix($cs()),
            ])->columns(3),

            Section::make('Deductions')->schema([
                Forms\Components\TextInput::make('retention_deduction')->numeric()->prefix($cf())->suffix($cs())
                    ->helperText('Retention held this period'),
                Forms\Components\TextInput::make('retention_release')->numeric()->prefix($cf())->suffix($cs())
                    ->helperText('Retention released this period'),
                Forms\Components\TextInput::make('advance_recovery')->numeric()->prefix($cf())->suffix($cs())
                    ->helperText('Advance payment recovery'),
                Forms\Components\TextInput::make('other_deductions')->numeric()->prefix($cf())->suffix($cs()),
                Forms\Components\Textarea::make('deduction_description')->rows(2)->columnSpan(2),
            ])->columns(3),

            Section::make('Payable Summary')->schema([
                Forms\Components\TextInput::make('net_payable')->numeric()->prefix($cf())->suffix($cs())
                    ->helperText('Net amount due'),
                Forms\Components\TextInput::make('vat_amount')->numeric()->prefix($cf())->suffix($cs()),
                Forms\Components\TextInput::make('total_payable')->numeric()->prefix($cf())->suffix($cs())
                    ->helperText('Final amount payable (net + VAT)'),
            ])->columns(3),

            Section::make('Workflow & Approvals')->schema([
                Forms\Components\Select::make('prepared_by')
                    ->relationship('preparedByUser', 'name', fn($q) => $q?->where('company_id', auth()->user()?->company_id))->searchable()->preload()
                    ->default(fn() => auth()->id()),
                Forms\Components\Select::make('checked_by')
                    ->relationship('checkedByUser', 'name', fn($q) => $q?->where('company_id', auth()->user()?->company_id))->searchable()->preload(),
                Forms\Components\Select::make('certified_by')
                    ->relationship('certifiedByUser', 'name', fn($q) => $q?->where('company_id', auth()->user()?->company_id))->searchable()->preload(),
                Forms\Components\DatePicker::make('submitted_date'),
                Forms\Components\DatePicker::make('certified_date'),
                Forms\Components\DatePicker::make('paid_date'),
                Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
            ])->columns(3)->collapsed(),

            Forms\Components\Hidden::make('company_id')->default(fn() => auth()->user()?->company_id),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('certificate_number')->searchable()->sortable()->weight('bold')->color('primary'),
                Tables\Columns\TextColumn::make('project.name')->label('Project')->limit(20),
                Tables\Columns\TextColumn::make('type')->badge()->color(fn(string $s) => match ($s) {
                    'interim' => 'info', 'final' => 'success', 'retention_release' => 'warning', 'advance' => 'primary', default => 'gray'
                })->formatStateUsing(fn(string $s) => PaymentCertificate::$types[$s] ?? $s),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn(string $s) => match ($s) {
                    'draft' => 'gray', 'submitted' => 'info', 'certified' => 'success', 'paid' => 'primary', 'rejected' => 'danger', default => 'gray'
                }),
                Tables\Columns\TextColumn::make('period_from')->date('M d')->label('From'),
                Tables\Columns\TextColumn::make('period_to')->date('M d')->label('To'),
                Tables\Columns\TextColumn::make('this_certificate_gross')->formatStateUsing(CurrencyHelper::formatter())->label('Gross'),
                Tables\Columns\TextColumn::make('net_payable')->formatStateUsing(CurrencyHelper::formatter())->label('Net')->weight('bold'),
                Tables\Columns\TextColumn::make('total_payable')->formatStateUsing(CurrencyHelper::formatter())->label('Total')->weight('bold')->color('success'),
                Tables\Columns\TextColumn::make('certified_date')->date('M d, Y')->placeholder('Pending'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(PaymentCertificate::$statuses),
                Tables\Filters\SelectFilter::make('type')->options(PaymentCertificate::$types),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\Action::make('certify')
                    ->icon('heroicon-o-check-badge')->color('success')->label('Certify')
                    ->visible(fn(PaymentCertificate $r) => $r->status === 'submitted')
                    ->requiresConfirmation()
                    ->action(fn(PaymentCertificate $record) => $record->update([
                        'status' => 'certified',
                        'certified_by' => auth()->id(),
                        'certified_date' => now(),
                    ])),
                Actions\Action::make('markPaid')
                    ->icon('heroicon-o-banknotes')->color('primary')->label('Mark Paid')
                    ->visible(fn(PaymentCertificate $r) => $r->status === 'certified')
                    ->requiresConfirmation()
                    ->action(fn(PaymentCertificate $record) => $record->update([
                        'status' => 'paid',
                        'paid_date' => now(),
                    ])),
            ])
            ->bulkActions([Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentCertificates::route('/'),
            'create' => Pages\CreatePaymentCertificate::route('/create'),
            'view' => Pages\ViewPaymentCertificate::route('/{record}'),
            'edit' => Pages\EditPaymentCertificate::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->where('status', 'submitted')->count();
        return $count > 0 ? (string) $count : null;
    }
}
