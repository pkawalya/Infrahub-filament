<?php
namespace App\Filament\App\Resources\DailySiteDiaryResource\Pages;
use App\Filament\App\Resources\DailySiteDiaryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListDailySiteDiaries extends ListRecords
{
    protected static string $resource = DailySiteDiaryResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('New Diary Entry')];
    }
}
