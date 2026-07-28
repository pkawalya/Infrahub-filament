<?php
namespace App\Filament\App\Resources\ChangeOrderResource\Pages;
use App\Filament\App\Resources\ChangeOrderResource;
use App\Models\ChangeOrder;
use App\Support\CurrencyHelper;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
class ViewChangeOrder extends ViewRecord
{
    protected static string $resource = ChangeOrderResource::class;
    protected function getHeaderActions(): array
    {
        return [
            ...\App\Filament\App\Support\WorkflowUiHelper::getApprovalHeaderActions(),
            Actions\Action::make('submit')
                ->label('Submit')
                ->icon('heroicon-o-paper-airplane')
                ->color('info')
                ->visible(fn() => $this->record->canTransitionTo('submitted'))
                ->action(function () {
                    $this->record->transitionTo('submitted');
                    Notification::make()->title('Change order submitted')->success()->send();
                }),

            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->canTransitionTo('approved'))
                ->form(fn() => [
                    Forms\Components\TextInput::make('approved_cost')
                        ->numeric()
                        ->prefix(fn() => CurrencyHelper::prefix())
                        ->default($this->record->amount),
                    Forms\Components\Textarea::make('approval_notes')->rows(2)->label('Approval Notes'),
                ])
                ->action(function (array $data): void {
                    $this->record->transitionTo('approved');
                    $this->record->update([
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ]);
                    Notification::make()->title('Change order approved')->success()->send();
                }),

            Actions\Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->canTransitionTo('rejected'))
                ->form([
                    Forms\Components\Textarea::make('reason')->label('Rejection Reason')->required(),
                ])
                ->action(function (array $data) {
                    $this->record->transitionTo('rejected');
                    Notification::make()->title('Change order rejected')->danger()->send();
                }),

            Actions\Action::make('sendBackForReview')
                ->label('Send Back for Review')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->canTransitionTo('under_review'))
                ->form([
                    Forms\Components\Textarea::make('reason')->label('Reason')->required(),
                ])
                ->action(function (array $data) {
                    $this->record->transitionTo('under_review');
                    Notification::make()->title('Change order returned to review')->warning()->send();
                }),

            Actions\Action::make('implement')
                ->label('Implement')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->canTransitionTo('implemented'))
                ->form([
                    Forms\Components\Textarea::make('implementation_notes')->label('Implementation Notes'),
                ])
                ->action(function (array $data) {
                    $this->record->transitionTo('implemented');
                    Notification::make()->title('Change order implemented')->success()->send();
                }),

            Actions\Action::make('revise')
                ->label('Revise')
                ->icon('heroicon-o-pencil-square')
                ->color('gray')
                ->visible(fn() => $this->record->canTransitionTo('draft'))
                ->form([
                    Forms\Components\Textarea::make('reason')->label('Reason for Revision')->required(),
                ])
                ->action(function (array $data) {
                    $this->record->transitionTo('draft');
                    Notification::make()->title('Change order returned to draft')->info()->send();
                }),

            Actions\EditAction::make(),
        ];
    }
}
