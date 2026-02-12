<?php

namespace App\Filament\Admin\Resources\EmailTemplateResource\Pages;

use App\Filament\Admin\Resources\EmailTemplateResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditEmailTemplate extends EditRecord
{
    protected static string $resource = EmailTemplateResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
