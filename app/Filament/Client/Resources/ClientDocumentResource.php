<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\ClientDocumentResource\Pages;
use App\Models\CdeDocument;
use Filament\Actions;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientDocumentResource extends Resource
{
    protected static ?string $model = CdeDocument::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Documents';
    protected static ?string $modelLabel = 'Document';
    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        $settings = auth()->user()?->company?->settings['client_portal'] ?? [];
        return $settings['show_documents'] ?? true;
    }

    public static function canAccess(): bool
    {
        return static::shouldRegisterNavigation();
    }

    public static function canCreate(): bool
    {
        return false;
    }
    public static function canEdit($record): bool
    {
        return false;
    }
    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $settings = $user?->company?->settings['client_portal'] ?? [];
        $allowedStatuses = $settings['allowed_document_statuses'] ?? ['approved'];

        return parent::getEloquentQuery()
            ->whereHas('project', function (Builder $q) use ($user) {
                $q->where(function (Builder $sub) use ($user) {
                    $sub->whereHas('client', fn(Builder $c) => $c->where('user_id', $user?->id))
                        ->orWhereHas('members', fn(Builder $m) => $m->where('users.id', $user?->id));
                });
            })
            ->whereIn('status', $allowedStatuses);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('document_number')->badge()->color('primary'),
                Tables\Columns\TextColumn::make('project.name')->label('Project')->limit(20),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('revision')->badge()->color('warning'),
                Tables\Columns\TextColumn::make('status')->badge()->color('success'),
                Tables\Columns\TextColumn::make('updated_at')->dateTime('M d, Y')->label('Last Updated'),
            ])
            ->defaultSort('updated_at', 'desc')
            ->actions([
                Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientDocuments::route('/'),
        ];
    }
}
