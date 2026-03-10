<?php
namespace App\Filament\App\Resources\DrawingResource\Pages;
use App\Filament\App\Resources\DrawingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
class ViewDrawing extends ViewRecord
{
    protected static string $resource = DrawingResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
