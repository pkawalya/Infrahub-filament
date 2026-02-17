<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages;

use App\Filament\App\Resources\CdeProjectResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateCdeProject extends CreateRecord
{
    protected static string $resource = CdeProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $company = auth()->user()->company;

        // Enforce project limit
        if ($company && !$company->canAddProject()) {
            Notification::make()
                ->danger()
                ->title('Project limit reached')
                ->body("Your plan allows a maximum of {$company->max_projects} projects. Please upgrade your subscription to add more.")
                ->persistent()
                ->send();

            $this->redirect(route('filament.app.pages.settings.upgrade'));
            $this->halt();
        }

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
