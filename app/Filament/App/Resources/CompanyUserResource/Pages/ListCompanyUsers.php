<?php

namespace App\Filament\App\Resources\CompanyUserResource\Pages;

use App\Filament\App\Resources\CompanyUserResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListCompanyUsers extends ListRecords
{
    protected static string $resource = CompanyUserResource::class;

    protected function getHeaderActions(): array
    {
        $company = auth()->user()?->company;
        $atLimit = $company && !$company->canAddUser();

        if ($atLimit) {
            $effectiveLimit = $company->getEffectiveMaxUsers();
            return [
                Action::make('create')
                    ->label('New User')
                    ->icon('heroicon-o-plus')
                    ->color('danger')
                    ->action(function () use ($effectiveLimit) {
                        Notification::make()
                            ->danger()
                            ->title('User limit reached')
                            ->body("Your plan allows a maximum of {$effectiveLimit} users. Please upgrade or add extra users.")
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
