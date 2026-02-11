<?php
namespace App\Filament\App\Resources\CdeProjectResource\Pages;

use App\Filament\App\Resources\CdeProjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCdeProject extends CreateRecord
{
    protected static string $resource = CdeProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove the virtual 'modules' field before saving the project record
        unset($data['modules']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $modules = $this->data['modules'] ?? [];

        if (!empty($modules)) {
            $this->record->syncModules($modules, auth()->id());
        }
    }
}
