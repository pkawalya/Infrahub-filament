<?php

namespace App\Filament\Admin\Resources\BlockedIpResource\Pages;

use App\Filament\Admin\Resources\BlockedIpResource;
use App\Models\BlockedIp;
use Filament\Resources\Pages\CreateRecord;

class CreateBlockedIp extends CreateRecord
{
    protected static string $resource = BlockedIpResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['blocked_by'] = auth()->user()?->name ?? 'admin';
        return $data;
    }

    protected function afterCreate(): void
    {
        BlockedIp::clearCache($this->record->ip_address);
    }
}
