<?php

namespace App\Filament\App\Resources\ProjectSuggestionResource\Pages;

use App\Filament\App\Resources\ProjectSuggestionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProjectSuggestion extends EditRecord
{
    protected static string $resource = ProjectSuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
