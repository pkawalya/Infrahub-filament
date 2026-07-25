<?php

namespace App\Filament\App\Resources\InvoiceResource\Pages;

use App\Filament\App\Resources\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('send')
                ->label('Send Invoice')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->visible(fn() => $this->record->canTransitionTo('sent'))
                ->action(function () {
                    $this->record->transitionTo('sent');
                    Notification::make()->title('Invoice sent to client')->success()->send();
                }),

            Actions\Action::make('markPartiallyPaid')
                ->label('Partially Paid')
                ->icon('heroicon-o-adjustments-horizontal')
                ->color('info')
                ->visible(fn() => $this->record->canTransitionTo('partially_paid'))
                ->form([
                    Forms\Components\TextInput::make('amount_paid')
                        ->label('Amount Received')
                        ->numeric()
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->transitionTo('partially_paid');
                    if (isset($data['amount_paid'])) {
                        $this->record->update(['amount_paid' => $data['amount_paid']]);
                    }
                    Notification::make()->title('Invoice marked as partially paid')->info()->send();
                }),

            Actions\Action::make('markPaid')
                ->label('Mark Paid')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->canTransitionTo('paid'))
                ->form([
                    Forms\Components\TextInput::make('amount_paid')
                        ->label('Amount Received')
                        ->numeric()
                        ->default(fn() => $this->record->total_amount),
                ])
                ->action(function (array $data) {
                    $this->record->transitionTo('paid');
                    $this->record->update(['amount_paid' => $data['amount_paid'] ?? $this->record->total_amount]);
                    Notification::make()->title('Invoice marked as paid')->success()->send();
                }),

            Actions\Action::make('cancel')
                ->label('Cancel Invoice')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->canTransitionTo('cancelled'))
                ->form([
                    Forms\Components\Textarea::make('reason')->label('Cancellation Reason')->required(),
                ])
                ->action(function (array $data) {
                    $this->record->transitionTo('cancelled');
                    Notification::make()->title('Invoice cancelled')->danger()->send();
                }),

            Actions\EditAction::make(),
        ];
    }
}
