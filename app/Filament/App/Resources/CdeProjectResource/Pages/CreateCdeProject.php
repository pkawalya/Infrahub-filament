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
            $effectiveMax = $company->getEffectiveMaxProjects();
            Notification::make()
                ->danger()
                ->title('Project limit reached')
                ->body("Your plan allows a maximum of {$effectiveMax} projects (including addons). You currently have {$company->projects()->count()}. Please upgrade your plan or add extra projects.")
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
        $company = auth()->user()->company;
        $modules = $this->data['modules'] ?? [];

        // If user didn't select any modules, default to ALL modules the company has enabled
        if (empty($modules) && $company) {
            $modules = $company->getEnabledModules();
        }

        if (!empty($modules)) {
            $this->record->syncModules($modules, auth()->id());

            Notification::make()
                ->title('Project created successfully')
                ->body(count($modules) . ' module(s) enabled: ' . implode(', ', array_map(
                    fn($code) => \App\Models\Module::$availableModules[$code]['name'] ?? $code,
                    $modules
                )))
                ->success()
                ->send();
        }
    }
}
