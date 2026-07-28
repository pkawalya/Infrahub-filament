<?php

namespace App\Filament\App\Support;

use Filament\Actions\Action as HeaderAction;
use Filament\Forms;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action as TableAction;

class WorkflowUiHelper
{
    /**
     * Build table actions for workflow step approval & rejection.
     */
    public static function getApprovalTableActions(): array
    {
        return [
            TableAction::make('approve_step')
                ->label('Approve Step')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn($record) => method_exists($record, 'canUserApprove') && $record->canUserApprove())
                ->form([
                    Forms\Components\Textarea::make('comment')
                        ->label('Approval Note / Comment')
                        ->placeholder('Add optional review comments...')
                        ->rows(2),
                ])
                ->action(function ($record, array $data) {
                    if (method_exists($record, 'advanceWorkflowStep')) {
                        $record->advanceWorkflowStep(auth()->user(), $data['comment'] ?? null);
                        Notification::make()->success()->title('Workflow step approved!')->send();
                    }
                }),

            TableAction::make('reject_step')
                ->label('Reject Step')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn($record) => method_exists($record, 'canUserApprove') && $record->canUserApprove())
                ->form([
                    Forms\Components\Textarea::make('reason')
                        ->label('Rejection Reason')
                        ->placeholder('State reason for rejection...')
                        ->rows(2)
                        ->required(),
                ])
                ->action(function ($record, array $data) {
                    if (method_exists($record, 'rejectWorkflowStep')) {
                        $record->rejectWorkflowStep(auth()->user(), $data['reason'] ?? null);
                        Notification::make()->warning()->title('Workflow step rejected!')->send();
                    }
                }),
        ];
    }

    /**
     * Build page header actions for workflow step approval & rejection.
     */
    public static function getApprovalHeaderActions(): array
    {
        return [
            HeaderAction::make('approve_step')
                ->label('Approve Workflow Step')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn($record) => method_exists($record, 'canUserApprove') && $record->canUserApprove())
                ->form([
                    Forms\Components\Textarea::make('comment')
                        ->label('Approval Note / Comment')
                        ->placeholder('Add optional review comments...')
                        ->rows(2),
                ])
                ->action(function ($record, array $data) {
                    if (method_exists($record, 'advanceWorkflowStep')) {
                        $record->advanceWorkflowStep(auth()->user(), $data['comment'] ?? null);
                        Notification::make()->success()->title('Workflow step approved!')->send();
                    }
                }),

            HeaderAction::make('reject_step')
                ->label('Reject Workflow Step')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn($record) => method_exists($record, 'canUserApprove') && $record->canUserApprove())
                ->form([
                    Forms\Components\Textarea::make('reason')
                        ->label('Rejection Reason')
                        ->placeholder('State reason for rejection...')
                        ->rows(2)
                        ->required(),
                ])
                ->action(function ($record, array $data) {
                    if (method_exists($record, 'rejectWorkflowStep')) {
                        $record->rejectWorkflowStep(auth()->user(), $data['reason'] ?? null);
                        Notification::make()->warning()->title('Workflow step rejected!')->send();
                    }
                }),
        ];
    }

    /**
     * Infolist section for workflow execution timeline.
     */
    public static function getWorkflowTimelineInfolistSection(): InfolistSection
    {
        return InfolistSection::make('Company Workflow Progress')
            ->icon('heroicon-o-arrow-path')
            ->description('Step-by-step approval hierarchy & audit log for this record.')
            ->schema([
                RepeatableEntry::make('workflow_timeline')
                    ->label('')
                    ->getStateUsing(fn($record) => method_exists($record, 'getWorkflowTimeline') ? $record->getWorkflowTimeline() : [])
                    ->schema([
                        TextEntry::make('sequence')
                            ->label('Step #')
                            ->badge()
                            ->color('gray'),

                        TextEntry::make('name')
                            ->label('Stage Name')
                            ->weight('bold'),

                        TextEntry::make('approver')
                            ->label('Required Approver')
                            ->badge()
                            ->color('info'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn(string $state) => match ($state) {
                                'approved' => 'success',
                                'active' => 'warning',
                                'rejected' => 'danger',
                                default => 'gray',
                            }),

                        TextEntry::make('actor_name')
                            ->label('Acted By')
                            ->placeholder('—'),

                        TextEntry::make('comment')
                            ->label('Comments')
                            ->placeholder('—'),
                    ])
                    ->columns(6)
                    ->columnSpanFull(),
            ])
            ->collapsible();
    }
}
