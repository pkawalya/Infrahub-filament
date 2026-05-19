<?php

namespace App\Filament\App\Resources\TenderResource\Pages;

use App\Filament\App\Resources\TenderResource;
use App\Models\TenderStage;
use App\Services\AiAssistantService;
use App\Services\StageWorkflowService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewTender extends ViewRecord
{
    protected static string $resource = TenderResource::class;

    protected function getHeaderActions(): array
    {
        $service = app(StageWorkflowService::class);

        return [
            // ── 🤖 AI Analyse Tender ──────────────────────────
            Actions\Action::make('ai_analyse')
                ->label('AI Analyse')
                ->icon('heroicon-o-sparkles')
                ->color('info')
                ->modalHeading('🤖 AI Tender Analysis')
                ->modalWidth('3xl')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalContent(function () {
                    $ai = app(AiAssistantService::class);

                    if (!$ai->isAvailable()) {
                        return new \Illuminate\Support\HtmlString(
                            '<div class="p-4 text-amber-600 dark:text-amber-400">⚠️ AI is not configured. Add GEMINI_API_KEY to .env.</div>'
                        );
                    }

                    $r = $this->record;
                    $description = implode("\n", array_filter([
                        "Tender: {$r->title}",
                        $r->client_name ? "Client: {$r->client_name}" : null,
                        $r->category ? "Category: {$r->category}" : null,
                        $r->region ? "Region: {$r->region}" : null,
                        $r->estimated_value ? "Estimated Value: {$r->estimated_value}" : null,
                        $r->strategy_notes ? "Strategy Notes: {$r->strategy_notes}" : null,
                        $r->competitors ? "Known Competitors: {$r->competitors}" : null,
                    ]));

                    $result = $ai->extractTenderRequirements($description);

                    if (empty($result)) {
                        return new \Illuminate\Support\HtmlString(
                            '<div class="p-4 text-gray-500">Could not analyse this tender. Try adding more details.</div>'
                        );
                    }

                    $html = '<div class="space-y-4 text-sm">';

                    // Bid recommendation
                    $rec = $result['bid_recommendation'] ?? 'N/A';
                    $recColor = $rec === 'Bid' ? 'green' : ($rec === 'No-Bid' ? 'red' : 'amber');
                    $html .= "<div class='p-4 rounded-xl bg-{$recColor}-50 dark:bg-{$recColor}-950/30 border border-{$recColor}-200 dark:border-{$recColor}-800'>";
                    $html .= "<p class='font-bold text-lg'>{$rec}</p>";
                    $html .= "<p class='text-gray-600 dark:text-gray-400'>" . ($result['bid_reason'] ?? '') . "</p></div>";

                    foreach (['key_requirements' => '📋 Key Requirements', 'evaluation_criteria' => '📊 Evaluation Criteria', 'risks' => '⚠️ Risks', 'deadlines' => '📅 Deadlines'] as $k => $label) {
                        if (!empty($result[$k])) {
                            $html .= "<div class='p-3 bg-gray-50 dark:bg-gray-800 rounded-xl'><p class='font-semibold mb-2'>{$label}</p><ul class='list-disc list-inside space-y-1 text-gray-700 dark:text-gray-300'>";
                            foreach ($result[$k] as $item) {
                                $html .= "<li>" . e($item) . "</li>";
                            }
                            $html .= "</ul></div>";
                        }
                    }

                    $html .= '</div>';
                    return new \Illuminate\Support\HtmlString($html);
                }),

            // ── Stage Transition Action ──────────────────────
            Actions\Action::make('change_stage')
                ->label('Advance Stage')
                ->icon('heroicon-o-arrow-right-circle')
                ->color('primary')
                ->size('lg')
                ->visible(fn() => $this->record->tender_stage_id && !$this->record->stage?->is_terminal)
                ->form(function () use ($service) {
                    $nextStages = $service->getNextTenderStages($this->record);
                    return [
                        Forms\Components\Select::make('to_stage_id')
                            ->label('Move to Stage')
                            ->options($nextStages->pluck('name', 'id'))
                            ->required()
                            ->helperText('Select the next stage for this tender.'),
                        Forms\Components\Textarea::make('comment')
                            ->label('Comment / Justification')
                            ->rows(3)
                            ->helperText('Provide reasoning for this stage change.'),
                    ];
                })
                ->action(function (array $data) use ($service) {
                    try {
                        $service->transitionTender($this->record, $data['to_stage_id'], $data['comment'] ?? null);
                        Notification::make()
                            ->success()
                            ->title('Stage Updated')
                            ->body('Tender stage has been updated successfully.')
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->danger()
                            ->title('Transition Failed')
                            ->body($e->getMessage())
                            ->send();
                    }
                }),

            // ── Set Initial Stage (when no stage assigned) ───
            Actions\Action::make('set_initial_stage')
                ->label('Set Stage')
                ->icon('heroicon-o-flag')
                ->color('warning')
                ->visible(fn() => !$this->record->tender_stage_id)
                ->form(function () {
                    $companyId = auth()->user()?->company_id;
                    $stages = TenderStage::where('company_id', $companyId)
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->pluck('name', 'id');

                    return [
                        Forms\Components\Select::make('to_stage_id')
                            ->label('Initial Stage')
                            ->options($stages)
                            ->required()
                            ->default(TenderStage::getDefault()?->id),
                        Forms\Components\Textarea::make('comment')
                            ->label('Comment')
                            ->rows(2),
                    ];
                })
                ->action(function (array $data) use ($service) {
                    try {
                        $service->transitionTender($this->record, $data['to_stage_id'], $data['comment'] ?? null);
                        Notification::make()
                            ->success()
                            ->title('Initial Stage Set')
                            ->body('Tender has been assigned to a workflow stage.')
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->danger()
                            ->title('Failed')
                            ->body($e->getMessage())
                            ->send();
                    }
                }),

            Actions\EditAction::make(),
        ];
    }
}

