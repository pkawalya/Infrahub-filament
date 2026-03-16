<?php
namespace App\Filament\Admin\Resources\CompanyResource\Pages;

use App\Filament\Admin\Resources\CompanyResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditCompany extends EditRecord
{
    protected static string $resource = CompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->modalDescription('Are you absolutely sure you want to delete this company? This is a destructive action and cannot be undone.')
                ->form([
                    \Filament\Forms\Components\TextInput::make('password')
                        ->password()
                        ->required()
                        ->currentPassword()
                        ->label('Enter your password to confirm deletion'),
                ]),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['enabled_modules']);
        return $data;
    }

    protected function afterSave(): void
    {
        $selectedModules = $this->data['enabled_modules'] ?? [];
        $currentModules = $this->record->getEnabledModules();

        // Enable newly selected modules
        foreach ($selectedModules as $code) {
            if (!in_array($code, $currentModules)) {
                $this->record->enableModule($code, auth()->id());
            }
        }

        // Disable removed modules
        foreach ($currentModules as $code) {
            if (!in_array($code, $selectedModules)) {
                $this->record->disableModule($code, auth()->id());
            }
        }
    }
}
