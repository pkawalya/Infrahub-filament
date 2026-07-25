<?php
namespace App\Filament\App\Resources\DrawingResource\Pages;
use App\Filament\App\Resources\DrawingResource;
use App\Models\Drawing;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
class ViewDrawing extends ViewRecord
{
    protected static string $resource = DrawingResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('submitForReview')
                ->label('Submit for Review')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->visible(fn() => $this->record->canTransitionTo('for_review'))
                ->action(function () {
                    $this->record->transitionTo('for_review');
                    Notification::make()->title('Drawing submitted for review')->success()->send();
                }),

            Actions\Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->canTransitionTo('approved'))
                ->action(function () {
                    $this->record->transitionTo('approved');
                    Notification::make()->title('Drawing approved')->success()->send();
                }),

            Actions\Action::make('issueForConstruction')
                ->label('Issue for Construction')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->canTransitionTo('ifc'))
                ->action(function () {
                    $this->record->transitionTo('ifc');
                    Notification::make()->title('Drawing issued for construction')->success()->send();
                }),

            Actions\Action::make('markAsBuilt')
                ->label('Mark As-Built')
                ->icon('heroicon-o-wrench')
                ->color('info')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->canTransitionTo('as_built'))
                ->action(function () {
                    $this->record->transitionTo('as_built');
                    Notification::make()->title('Drawing marked as as-built')->success()->send();
                }),

            Actions\Action::make('supersede')
                ->label('Supersede')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->canTransitionTo('superseded'))
                ->form([
                    Forms\Components\Textarea::make('reason')->label('Reason for Superseding')->required(),
                ])
                ->action(function (array $data) {
                    $this->record->transitionTo('superseded');
                    Notification::make()->title('Drawing superseded')->warning()->send();
                }),

            Actions\Action::make('sendBackToWip')
                ->label('Send Back to WIP')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('gray')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->canTransitionTo('wip'))
                ->form([
                    Forms\Components\Textarea::make('reason')->label('Reason for Returning')->required(),
                ])
                ->action(function (array $data) {
                    $this->record->transitionTo('wip');
                    Notification::make()->title('Drawing returned to WIP')->warning()->send();
                }),

            Actions\Action::make('newRevision')
                ->icon('heroicon-o-arrow-path')->color('warning')->label('New Revision')
                ->form([
                    Forms\Components\TextInput::make('revision_code')->required()->maxLength(10),
                    Forms\Components\Textarea::make('revision_description')->rows(2),
                    Forms\Components\FileUpload::make('file')->directory('drawings'),
                ])
                ->action(function (array $data): void {
                    $record = $this->record;
                    $record->revisions()->where('status', 'current')->update(['status' => 'superseded']);
                    $record->revisions()->create([
                        'revision_code'        => $data['revision_code'],
                        'revision_description' => $data['revision_description'] ?? null,
                        'file_path'            => $data['file'] ?? null,
                        'status'               => 'current',
                        'revision_date'        => now(),
                        'revised_by'           => auth()->id(),
                    ]);
                    $record->update(['current_revision' => $data['revision_code']]);
                    Notification::make()->title('New revision created')->success()->send();
                }),

            Actions\EditAction::make(),
        ];
    }
}
