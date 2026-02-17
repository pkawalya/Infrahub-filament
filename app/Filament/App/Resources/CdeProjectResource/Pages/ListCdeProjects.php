<?php

namespace App\Filament\App\Resources\CdeProjectResource\Pages;

use App\Filament\App\Resources\CdeProjectResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListCdeProjects extends ListRecords
{
    protected static string $resource = CdeProjectResource::class;

    protected function getHeaderActions(): array
    {
        $company = auth()->user()?->company;
        $atLimit = $company && !$company->canAddProject();

        if ($atLimit) {
            $effectiveLimit = $company->getEffectiveMaxProjects();
            return [
                Action::make('create')
                    ->label('New Project')
                    ->icon('heroicon-o-plus')
                    ->color('danger')
                    ->action(function () use ($effectiveLimit) {
                        Notification::make()
                            ->danger()
                            ->title('Project limit reached')
                            ->body("Your plan allows a maximum of {$effectiveLimit} projects. Please upgrade or add extra projects.")
                            ->persistent()
                            ->send();

                        $this->redirect(route('filament.app.pages.settings.upgrade'));
                    }),
                Action::make('upgrade')
                    ->label('Upgrade Plan')
                    ->icon('heroicon-o-arrow-trending-up')
                    ->color('primary')
                    ->url(route('filament.app.pages.settings.upgrade')),
            ];
        }

        return [CreateAction::make()];
    }
}
