<?php
namespace App\Filament\App\Resources\TaskResource\Pages;

use App\Filament\App\Resources\TaskResource;
use App\Filament\App\Concerns\BulkImportsFromCsv;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTasks extends ListRecords
{
    use BulkImportsFromCsv;

    protected static string $resource = TaskResource::class;

    protected function importModel(): string
    {
        return \App\Models\Task::class;
    }

    protected function importColumns(): array
    {
        return ['title', 'description', 'status', 'priority', 'assigned_to', 'start_date', 'end_date', 'cde_project_id'];
    }

    protected function importRules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'status' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            $this->getBulkImportAction(),
        ];
    }
}
