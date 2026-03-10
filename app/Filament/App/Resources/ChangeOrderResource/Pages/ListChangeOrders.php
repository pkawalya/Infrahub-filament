<?php
namespace App\Filament\App\Resources\ChangeOrderResource\Pages;

use App\Filament\App\Resources\ChangeOrderResource;
use App\Filament\App\Concerns\BulkImportsFromCsv;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChangeOrders extends ListRecords
{
    use BulkImportsFromCsv;

    protected static string $resource = ChangeOrderResource::class;

    protected function importModel(): string
    {
        return \App\Models\ChangeOrder::class;
    }

    protected function importColumns(): array
    {
        return [
            'reference',
            'title',
            'description',
            'reason',
            'type',
            'status',
            'priority',
            'initiated_by',
            'estimated_cost',
            'time_impact_days',
            'cde_project_id',
            'contract_id'
        ];
    }

    protected function importRules(): array
    {
        return [
            'reference' => 'required|string|max:50',
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
