<?php

namespace App\Filament\App\Resources\WorkOrderResource\Pages;

use App\Filament\App\Resources\WorkOrderResource;
use App\Models\WorkOrder;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewWorkOrder extends ViewRecord
{
    protected static string $resource = WorkOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn() => $this->record->canTransitionTo('approved'))
                ->action(function () {
                    $this->record->transitionTo('approved');
                    Notification::make()->title('Work order approved')->success()->send();
                }),

            Actions\Action::make('start')
                ->label('Start Work')
                ->icon('heroicon-o-play')
                ->color('primary')
                ->visible(fn() => $this->record->canTransitionTo('in_progress'))
                ->action(function () {
                    $this->record->transitionTo('in_progress');
                    Notification::make()->title('Work order started')->success()->send();
                }),

            Actions\Action::make('complete')
                ->label('Complete')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->canTransitionTo('completed'))
                ->form(fn() => $this->isTransitionIrreversible('completed') ? [
                    Forms\Components\Textarea::make('reason')->label('Completion Notes')->required(),
                ] : [])
                ->action(function (array $data) {
                    $this->record->transitionTo('completed');
                    Notification::make()->title('Work order completed')->success()->send();
                }),

            Actions\Action::make('hold')
                ->label('Place on Hold')
                ->icon('heroicon-o-pause-circle')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->canTransitionTo('on_hold'))
                ->form([
                    Forms\Components\Textarea::make('reason')->label('Reason for Hold')->required(),
                ])
                ->action(function (array $data) {
                    $this->record->transitionTo('on_hold');
                    Notification::make()->title('Work order placed on hold')->warning()->send();
                }),

            Actions\Action::make('cancel')
                ->label('Cancel')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->canTransitionTo('cancelled'))
                ->form([
                    Forms\Components\Textarea::make('reason')->label('Cancellation Reason')->required(),
                ])
                ->action(function (array $data) {
                    $this->record->transitionTo('cancelled');
                    Notification::make()->title('Work order cancelled')->danger()->send();
                }),

            Actions\EditAction::make(),
        ];
    }

    private function isTransitionIrreversible(string $status): bool
    {
        return in_array($status, ['completed', 'cancelled']);
    }
}
