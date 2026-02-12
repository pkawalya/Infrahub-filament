<?php

namespace App\Filament\Admin\Resources\EmailTemplateResource\Pages;

use App\Filament\Admin\Resources\EmailTemplateResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;

class ViewEmailTemplate extends ViewRecord
{
    protected static string $resource = EmailTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
