<?php

namespace App\Filament\App\Resources\TenderResource\RelationManagers;

use App\Filament\Concerns\UIStandards;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AuditLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'auditLogs';
    protected static ?string $title = 'Stage History';
    protected static string|\BackedEnum|null $icon = 'heroicon-o-clock';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transitioned_at')
                    ->label('Date')
                    ->dateTime(UIStandards::DATETIME_FORMAT)
                    ->sortable(),
                Tables\Columns\TextColumn::make('from_stage')
                    ->label('From')
                    ->badge()
                    ->color('gray')
                    ->placeholder('Initial'),
                Tables\Columns\TextColumn::make('to_stage')
                    ->label('To')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('By')
                    ->placeholder('System'),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Comment')
                    ->limit(60)
                    ->placeholder('—')
                    ->wrap(),
            ])
            ->defaultSort('transitioned_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}
