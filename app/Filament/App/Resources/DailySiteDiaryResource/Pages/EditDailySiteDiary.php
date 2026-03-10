<?php
namespace App\Filament\App\Resources\DailySiteDiaryResource\Pages;
use App\Filament\App\Resources\DailySiteDiaryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditDailySiteDiary extends EditRecord
{
    protected static string $resource = DailySiteDiaryResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
