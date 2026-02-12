<?php

namespace App\Filament\App\Resources\CompanyEmailTemplateResource\Pages;

use App\Filament\App\Resources\CompanyEmailTemplateResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditCompanyEmailTemplate extends EditRecord
{
    protected static string $resource = CompanyEmailTemplateResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn() => !$this->record->isGlobal()),
        ];
    }
}
