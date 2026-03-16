<?php

namespace App\Filament\Admin\Resources;

use App\Models\BlockedIp;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class BlockedIpResource extends Resource
{
    protected static ?string $model = BlockedIp::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';
    protected static string|\UnitEnum|null $navigationGroup = 'System';
    protected static ?string $navigationLabel = 'Blocked IPs';
    protected static ?string $modelLabel = 'Blocked IP';
    protected static ?int $navigationSort = 99;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Section::make('Block Details')->schema([
                Forms\Components\TextInput::make('ip_address')
                    ->label('IP Address')
                    ->required()
                    ->maxLength(45)
                    ->placeholder('e.g. 192.168.1.100')
                    ->helperText('IPv4 or IPv6 address'),

                Forms\Components\TextInput::make('cidr_range')
                    ->label('CIDR Range (optional)')
                    ->maxLength(50)
                    ->placeholder('e.g. 192.168.0.0/16')
                    ->helperText('Block an entire subnet. Leave empty for single IP.'),

                Forms\Components\Textarea::make('reason')
                    ->label('Reason')
                    ->rows(2)
                    ->maxLength(500)
                    ->placeholder('e.g. Suspicious brute-force attempts'),

                Forms\Components\DateTimePicker::make('expires_at')
                    ->label('Expires At')
                    ->helperText('Leave empty for permanent block')
                    ->nullable(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('cidr_range')
                    ->label('CIDR')
                    ->placeholder('Single IP'),

                Tables\Columns\TextColumn::make('reason')
                    ->limit(40)
                    ->tooltip(fn($record) => $record->reason),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('blocked_by')
                    ->label('Blocked By')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime()
                    ->placeholder('Never')
                    ->color(fn($record) => $record->expires_at?->isPast() ? 'danger' : null),

                Tables\Columns\TextColumn::make('hit_count')
                    ->label('Hits')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_blocked_at')
                    ->label('Last Hit')
                    ->since()
                    ->placeholder('Never'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive')
                    ->placeholder('All'),
            ])
            ->actions([
                Tables\Actions\Action::make('toggle')
                    ->label(fn($record) => $record->is_active ? 'Deactivate' : 'Activate')
                    ->icon(fn($record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn($record) => $record->is_active ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['is_active' => !$record->is_active]);
                        BlockedIp::clearCache($record->ip_address);
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->after(fn($record) => BlockedIp::clearCache($record->ip_address)),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Resources\BlockedIpResource\Pages\ListBlockedIps::route('/'),
            'create' => \App\Filament\Admin\Resources\BlockedIpResource\Pages\CreateBlockedIp::route('/create'),
            'edit' => \App\Filament\Admin\Resources\BlockedIpResource\Pages\EditBlockedIp::route('/{record}/edit'),
        ];
    }
}
