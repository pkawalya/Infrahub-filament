<?php

namespace App\Filament\App\Resources\ProjectSuggestionResource\Pages;

use App\Filament\App\Resources\ProjectSuggestionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjectSuggestions extends ListRecords
{
    protected static string $resource = ProjectSuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Suggestion')
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
