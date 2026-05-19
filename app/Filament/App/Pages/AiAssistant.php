<?php

namespace App\Filament\App\Pages;

use App\Services\AiAssistantService;
use Filament\Pages\Page;

class AiAssistant extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'AI Assistant';
    protected static ?string $title = 'AI Assistant';
    protected static ?int $navigationSort = -1;
    protected static string|\UnitEnum|null $navigationGroup = 'AI';

    protected string $view = 'filament.app.pages.ai-assistant';

    /**
     * Allow all authenticated users with a company — bypass Shield.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->company_id || $user->isSuperAdmin());
    }

    // ── Tool Selector ──
    public string $activeTool = 'chat';

    // ── Freeform Chat ──
    public string $chatQuestion = '';
    public string $chatAnswer = '';
    public bool $chatLoading = false;

    // ── Document Summariser ──
    public string $docTitle = '';
    public string $docDescription = '';
    public string $docDiscipline = '';
    public string $docResult = '';
    public bool $docLoading = false;

    // ── Safety Incident Analyser ──
    public string $incidentTitle = '';
    public string $incidentDescription = '';
    public string $incidentType = '';
    public string $incidentSeverity = '';
    public array $incidentResult = [];
    public bool $incidentLoading = false;

    // ── Task Generator ──
    public string $scopeOfWork = '';
    public string $projectType = 'construction';
    public array $taskResult = [];
    public bool $taskLoading = false;

    // ── BOQ Expander ──
    public string $boqLabel = '';
    public string $boqProjectType = 'building';
    public string $boqResult = '';
    public bool $boqLoading = false;

    // ── Tender Analyser ──
    public string $tenderDescription = '';
    public array $tenderResult = [];
    public bool $tenderLoading = false;

    // ── Daily Diary ──
    public int $diaryCrewCount = 0;
    public string $diaryWeather = 'clear';
    public string $diaryActivities = '';
    public string $diaryIssues = '';
    public string $diaryResult = '';
    public bool $diaryLoading = false;

    // ── History ──
    public array $history = [];

    // ═══════════════════════════════════════════════════════════
    // TOOL ACTIONS
    // ═══════════════════════════════════════════════════════════

    public function switchTool(string $tool): void
    {
        $this->activeTool = $tool;
    }

    /**
     * General-purpose chat with the AI.
     */
    public function askChat(): void
    {
        if (empty(trim($this->chatQuestion))) return;

        $this->chatLoading = true;
        $this->chatAnswer = '';

        $ai = app(AiAssistantService::class);

        if (!$ai->isAvailable()) {
            $this->chatAnswer = '⚠️ AI is not configured. Please add GEMINI_API_KEY to your .env file.';
            $this->chatLoading = false;
            return;
        }

        $context = 'You are InfraHub AI, an expert assistant for construction project management. '
            . 'You help with project planning, safety, procurement, BOQ, contracts, and general construction questions. '
            . 'Be concise, professional, and actionable. Format responses with clear headings and bullet points where appropriate.';

        $this->chatAnswer = $ai->ask($this->chatQuestion, $context, 1200, 0.4);

        $this->history[] = [
            'tool' => 'Chat',
            'question' => $this->chatQuestion,
            'time' => now()->format('H:i'),
        ];

        $this->chatLoading = false;
    }

    /**
     * Summarise a document.
     */
    public function summariseDocument(): void
    {
        if (empty(trim($this->docTitle))) return;

        $this->docLoading = true;
        $this->docResult = '';

        $ai = app(AiAssistantService::class);
        if (!$ai->isAvailable()) {
            $this->docResult = '⚠️ AI not configured.';
            $this->docLoading = false;
            return;
        }

        $this->docResult = $ai->summariseDocument($this->docTitle, $this->docDescription, $this->docDiscipline);

        $this->history[] = [
            'tool' => 'Document Summary',
            'question' => $this->docTitle,
            'time' => now()->format('H:i'),
        ];

        $this->docLoading = false;
    }

    /**
     * Analyse a safety incident.
     */
    public function analyseIncident(): void
    {
        if (empty(trim($this->incidentTitle))) return;

        $this->incidentLoading = true;
        $this->incidentResult = [];

        $ai = app(AiAssistantService::class);
        if (!$ai->isAvailable()) {
            $this->incidentResult = ['error' => '⚠️ AI not configured.'];
            $this->incidentLoading = false;
            return;
        }

        $this->incidentResult = $ai->analyseSafetyIncident(
            $this->incidentTitle,
            $this->incidentDescription,
            $this->incidentType,
            $this->incidentSeverity,
        );

        $this->history[] = [
            'tool' => 'Safety Analysis',
            'question' => $this->incidentTitle,
            'time' => now()->format('H:i'),
        ];

        $this->incidentLoading = false;
    }

    /**
     * Generate tasks from scope of work.
     */
    public function generateTasks(): void
    {
        if (empty(trim($this->scopeOfWork))) return;

        $this->taskLoading = true;
        $this->taskResult = [];

        $ai = app(AiAssistantService::class);
        if (!$ai->isAvailable()) {
            $this->taskResult = [];
            $this->taskLoading = false;
            return;
        }

        $this->taskResult = $ai->generateTaskList($this->scopeOfWork, $this->projectType);

        $this->history[] = [
            'tool' => 'Task Generator',
            'question' => \Illuminate\Support\Str::limit($this->scopeOfWork, 60),
            'time' => now()->format('H:i'),
        ];

        $this->taskLoading = false;
    }

    /**
     * Expand a BOQ description.
     */
    public function expandBOQ(): void
    {
        if (empty(trim($this->boqLabel))) return;

        $this->boqLoading = true;
        $this->boqResult = '';

        $ai = app(AiAssistantService::class);
        if (!$ai->isAvailable()) {
            $this->boqResult = '⚠️ AI not configured.';
            $this->boqLoading = false;
            return;
        }

        $this->boqResult = $ai->completeBOQDescription($this->boqLabel, $this->boqProjectType);

        $this->history[] = [
            'tool' => 'BOQ Expander',
            'question' => $this->boqLabel,
            'time' => now()->format('H:i'),
        ];

        $this->boqLoading = false;
    }

    /**
     * Analyse a tender opportunity.
     */
    public function analyseTender(): void
    {
        if (empty(trim($this->tenderDescription))) return;

        $this->tenderLoading = true;
        $this->tenderResult = [];

        $ai = app(AiAssistantService::class);
        if (!$ai->isAvailable()) {
            $this->tenderResult = ['error' => '⚠️ AI not configured.'];
            $this->tenderLoading = false;
            return;
        }

        $this->tenderResult = $ai->extractTenderRequirements($this->tenderDescription);

        $this->history[] = [
            'tool' => 'Tender Analysis',
            'question' => \Illuminate\Support\Str::limit($this->tenderDescription, 60),
            'time' => now()->format('H:i'),
        ];

        $this->tenderLoading = false;
    }

    /**
     * Draft a daily diary entry.
     */
    public function draftDiary(): void
    {
        if (empty(trim($this->diaryActivities))) return;

        $this->diaryLoading = true;
        $this->diaryResult = '';

        $ai = app(AiAssistantService::class);
        if (!$ai->isAvailable()) {
            $this->diaryResult = '⚠️ AI not configured.';
            $this->diaryLoading = false;
            return;
        }

        $this->diaryResult = $ai->draftDiaryEntry([
            'crew_count'  => $this->diaryCrewCount,
            'weather'     => $this->diaryWeather,
            'activities'  => $this->diaryActivities,
            'issues'      => $this->diaryIssues,
            'date'        => now()->toDateString(),
        ]);

        $this->history[] = [
            'tool' => 'Diary Draft',
            'question' => 'Daily diary entry',
            'time' => now()->format('H:i'),
        ];

        $this->diaryLoading = false;
    }

    public function clearHistory(): void
    {
        $this->history = [];
    }
}
