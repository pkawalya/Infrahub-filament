<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Company;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentCompaniesTable extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Recent Company Registrations';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Company::query()
                    ->with(['subscription'])
                    ->latest()
                    ->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Company')
                    ->searchable()
                    ->icon('heroicon-m-building-office-2')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('subscription.name')
                    ->label('Plan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Enterprise' => 'primary',
                        'Professional' => 'success',
                        'Starter' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),

                Tables\Columns\IconColumn::make('is_trial')
                    ->label('Trial')
                    ->boolean()
                    ->trueIcon('heroicon-o-beaker')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('info')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->icon('heroicon-m-users')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('country')
                    ->label('Region')
                    ->icon('heroicon-m-globe-alt')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registered')
                    ->since()
                    ->sortable()
                    ->color('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated(false)
            ->striped();
    }
}
