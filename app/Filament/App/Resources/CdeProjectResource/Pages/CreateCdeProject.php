<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages;

use App\Filament\App\Resources\CdeProjectResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateCdeProject extends CreateRecord
{
    protected static string $resource = CdeProjectResource::class;

    public function mount(): void
    {
        $company = auth()->user()?->company;

        if ($company && !$company->canAddProject()) {
            $effectiveMax = $company->getEffectiveMaxProjects();
            Notification::make()
                ->danger()
                ->title('Project limit reached')
                ->body("Your plan allows a maximum of {$effectiveMax} projects. Please upgrade your plan to create more.")
                ->persistent()
                ->send();

            $this->redirect(CdeProjectResource::getUrl('index'));
            return;
        }

        parent::mount();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
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

