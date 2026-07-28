<?php

namespace App\Filament\App\Resources\WorkflowTemplateResource\Pages;

use App\Filament\App\Resources\WorkflowTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkflowTemplates extends ListRecords
{
    protected static string $resource = WorkflowTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Workflow Template')
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
