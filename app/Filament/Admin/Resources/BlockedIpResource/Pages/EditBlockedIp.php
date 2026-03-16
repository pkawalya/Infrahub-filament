<?php

namespace App\Filament\Admin\Resources\BlockedIpResource\Pages;

use App\Filament\Admin\Resources\BlockedIpResource;
use App\Models\BlockedIp;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBlockedIp extends EditRecord
{
    protected static string $resource = BlockedIpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(fn() => BlockedIp::clearCache($this->record->ip_address)),
        ];
    }

    protected function afterSave(): void
    {
        BlockedIp::clearCache($this->record->ip_address);
    }
}
