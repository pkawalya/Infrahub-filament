<?php

namespace App\Filament\App\Resources\ProjectSuggestionResource\Pages;

use App\Filament\App\Resources\ProjectSuggestionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectSuggestion extends CreateRecord
{
    protected static string $resource = ProjectSuggestionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()?->company_id;
        $data['author_id'] = null; // Always anonymous submission
        $data['is_anonymous'] = true;
        return $data;
    }
}
