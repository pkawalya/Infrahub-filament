<?php

namespace App\Filament\App\Resources\WorkflowTemplateResource\Pages;

use App\Filament\App\Resources\WorkflowTemplateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkflowTemplate extends CreateRecord
{
    protected static string $resource = WorkflowTemplateResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = auth()->user()?->company_id;
        return $data;
    }
}
