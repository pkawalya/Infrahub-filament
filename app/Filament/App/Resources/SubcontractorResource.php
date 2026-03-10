<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\SubcontractorResource\Pages;
use App\Models\Subcontractor;
use App\Support\CurrencyHelper;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class SubcontractorResource extends Resource
{
    protected static ?string $model = Subcontractor::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    protected static string|\UnitEnum|null $navigationGroup = 'Company';
    protected static ?string $navigationLabel = 'Subcontractors';
    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Company Details')
                ->icon('heroicon-o-building-office-2')
                ->schema([
                    Forms\Components\TextInput::make('name')->required()->maxLength(255),
                    Forms\Components\TextInput::make('contact_person')->maxLength(255),
                    Forms\Components\TextInput::make('email')->email()->maxLength(255),
                    Forms\Components\TextInput::make('phone')->maxLength(50),
                    Forms\Components\Select::make('specialty')
                        ->options(Subcontractor::$specialties)
                        ->searchable(),
                    Forms\Components\Select::make('status')
                        ->options(Subcontractor::$statuses)
                        ->default('active')
                        ->required(),
                    Forms\Components\Select::make('rating')
                        ->label('Rating')
                        ->options([1 => '⭐', 2 => '⭐⭐', 3 => '⭐⭐⭐', 4 => '⭐⭐⭐⭐', 5 => '⭐⭐⭐⭐⭐'])
                        ->nullable(),
                    Forms\Components\Textarea::make('address')->rows(2)->columnSpanFull(),
                ])->columns(2),

            Section::make('Registration & Compliance')
                ->icon('heroicon-o-shield-check')
                ->schema([
                    Forms\Components\TextInput::make('registration_number')
                        ->label('Registration #'),
                    Forms\Components\TextInput::make('tax_id')
                        ->label('Tax ID / TIN'),
                    Forms\Components\DatePicker::make('insurance_expiry')
                        ->label('Insurance Expires'),
                    Forms\Components\DatePicker::make('license_expiry')
                        ->label('License Expires'),
                    Forms\Components\Toggle::make('safety_certified')
                        ->label('Safety Certified')
                        ->helperText('Has current safety training certification'),
                    Forms\Components\TagsInput::make('certifications')
                        ->label('Certifications')
                        ->placeholder('e.g. ISO 9001, OSHA')
                        ->columnSpanFull(),
                ])->columns(2)->collapsed(),

            Section::make('Notes')
                ->schema([
                    Forms\Components\Textarea::make('notes')->rows(3)->columnSpanFull(),
                ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn(Subcontractor $r) => $r->contact_person),
                Tables\Columns\TextColumn::make('specialty')
                    ->badge()
                    ->color('info')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('phone')
                    ->icon('heroicon-o-phone')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn(?int $state) => $state ? str_repeat('⭐', $state) : '—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('packages_count')
                    ->counts('packages')
                    ->label('Packages')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'active' => 'success',
                        'suspended' => 'warning',
                        'blacklisted' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('compliance')
                    ->label('Compliance')
                    ->state(fn(Subcontractor $r) => $r->isComplianceCurrent())
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(Subcontractor::$statuses),
                Tables\Filters\SelectFilter::make('specialty')
                    ->options(Subcontractor::$specialties),
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
            'index' => Pages\ListSubcontractors::route('/'),
            'create' => Pages\CreateSubcontractor::route('/create'),
            'edit' => Pages\EditSubcontractor::route('/{record}/edit'),
        ];
    }
}
