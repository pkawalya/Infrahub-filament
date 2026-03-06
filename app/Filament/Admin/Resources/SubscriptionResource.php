<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SubscriptionResource\Pages;
use App\Models\Module;
use App\Models\Subscription;
use Filament\Actions;
use Filament\Schemas;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';
    protected static string|\UnitEnum|null $navigationGroup = 'Subscription & Billing';
    protected static ?string $label = 'Plan';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Plan Details')->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('slug')->unique(ignoreRecord: true),
                Forms\Components\Textarea::make('description')->rows(2),
                Forms\Components\Toggle::make('is_active')->default(true),
                Forms\Components\Toggle::make('is_popular'),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            ])->columns(2),

            Schemas\Components\Section::make('Traditional Pricing')
                ->description('Classic per-plan subscription pricing.')
                ->schema([
                    Forms\Components\TextInput::make('monthly_price')->numeric()->prefix('$'),
                    Forms\Components\TextInput::make('yearly_price')->numeric()->prefix('$'),
                ])->columns(2),

            Schemas\Components\Section::make('Per-Project Pricing')
                ->description('Hybrid billing model: base platform fee + charge per active project beyond the included allowance.')
                ->icon('heroicon-o-calculator')
                ->schema([
                    Forms\Components\TextInput::make('base_platform_price')
                        ->label('Base Platform Fee')
                        ->numeric()
                        ->prefix('$')
                        ->helperText('Fixed monthly fee charged to the company regardless of project count.'),
                    Forms\Components\TextInput::make('per_project_price')
                        ->label('Per Project Price')
                        ->numeric()
                        ->prefix('$')
                        ->helperText('Monthly cost per active project beyond the included allowance.'),
                    Forms\Components\TextInput::make('included_projects')
                        ->label('Included Projects')
                        ->numeric()
                        ->default(0)
                        ->helperText('Number of projects included in the base fee (no extra charge).'),
                    Forms\Components\KeyValue::make('module_prices')
                        ->label('Per-Module Pricing')
                        ->keyLabel('Module Code')
                        ->valueLabel('Monthly Price ($)')
                        ->helperText('Optional per-module monthly fees. Use module codes (e.g. cde, tasks, planning).'),
                ])->columns(2)
                ->collapsed(),

            Schemas\Components\Section::make('Limits')->schema([
                Forms\Components\TextInput::make('max_users')->numeric()->default(5),
                Forms\Components\TextInput::make('max_projects')->numeric()->default(10),
                Forms\Components\TextInput::make('max_storage_gb')->numeric()->default(5),
            ])->columns(3),

            Schemas\Components\Section::make('Included Modules')->schema([
                Forms\Components\CheckboxList::make('included_modules')
                    ->options(collect(Module::$availableModules)->mapWithKeys(fn($m, $k) => [$k => $m['name']]))
                    ->columns(3)
                    ->descriptions(collect(Module::$availableModules)->mapWithKeys(fn($m, $k) => [$k => $m['description']])),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('monthly_price')->money('USD')->sortable()->label('Monthly'),
                Tables\Columns\TextColumn::make('yearly_price')->money('USD')->sortable()->label('Yearly'),
                Tables\Columns\TextColumn::make('base_platform_price')->money('USD')->label('Base Fee')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('per_project_price')->money('USD')->label('Per Project')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('included_projects')->label('Incl. Projects')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('max_users')->label('Users'),
                Tables\Columns\TextColumn::make('max_projects')->label('Projects'),
                Tables\Columns\TextColumn::make('companies_count')->counts('companies')->label('Companies'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\IconColumn::make('is_popular')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
