<?php
namespace App\Filament\App\Resources\DrawingResource\Pages;

use App\Filament\App\Resources\DrawingResource;
use App\Filament\App\Concerns\BulkImportsFromCsv;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDrawings extends ListRecords
{
    use BulkImportsFromCsv;

    protected static string $resource = DrawingResource::class;

    protected function importModel(): string
    {
        return \App\Models\Drawing::class;
    }

    protected function importColumns(): array
    {
        return [
            'drawing_number',
            'title',
            'description',
            'discipline',
            'drawing_type',
            'current_revision',
            'status',
            'scale',
            'sheet_size',
            'suitability_code',
            'originator',
            'zone',
            'level',
            'cde_project_id'
        ];
    }

    protected function importRules(): array
    {
        return [
            'drawing_number' => 'required|string|max:50',
            'title' => 'required|string|max:255',
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
