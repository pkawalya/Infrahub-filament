<?php
namespace App\Filament\Admin\Resources\SubscriptionResource\Pages;
use App\Filament\Admin\Resources\SubscriptionResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
class EditSubscription extends EditRecord
{
    protected static string $resource = SubscriptionResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
