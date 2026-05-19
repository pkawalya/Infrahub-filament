<?php

namespace App\Filament\App\Resources\BidStageResource\Pages;

use App\Filament\App\Resources\BidStageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBidStages extends ListRecords
{
    protected static string $resource = BidStageResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
