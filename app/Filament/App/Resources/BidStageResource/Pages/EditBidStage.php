<?php

namespace App\Filament\App\Resources\BidStageResource\Pages;

use App\Filament\App\Resources\BidStageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBidStage extends EditRecord
{
    protected static string $resource = BidStageResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
