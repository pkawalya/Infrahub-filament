<?php

namespace App\Filament\App\Resources\CompanyEmailTemplateResource\Pages;

use App\Filament\App\Resources\CompanyEmailTemplateResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewCompanyEmailTemplate extends ViewRecord
{
    protected static string $resource = CompanyEmailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn() => !$this->record->isGlobal()),
        ];
    }
}
