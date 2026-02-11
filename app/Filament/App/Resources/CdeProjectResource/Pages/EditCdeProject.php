<?php
namespace App\Filament\App\Resources\CdeProjectResource\Pages;

use App\Filament\App\Resources\CdeProjectResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditCdeProject extends EditRecord
{
    protected static string $resource = CdeProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove the virtual 'modules' field before updating the project record
        unset($data['modules']);
        return $data;
    }

    protected function afterSave(): void
    {
        $modules = $this->data['modules'] ?? [];
        $this->record->syncModules($modules, auth()->id());
    }
}
