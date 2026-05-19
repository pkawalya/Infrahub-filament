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
     * General-purpose chat with the AI — searches user's project data first.
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

        // ── Pull the user's real company data as context ──────────────────
        $companyId = auth()->user()->company_id;
        $dataContext = $this->gatherCompanyContext($companyId, $this->chatQuestion);

        $context = 'You are InfraHub AI, an intelligent assistant embedded in a construction project management platform. '
            . 'You have access to the user\'s ACTUAL project data shown below. '
            . 'ALWAYS answer based on this real data first. Only use general knowledge if the data doesn\'t cover the question. '
            . 'When referencing data, cite specific project names, document titles, task names, or numbers. '
            . 'Be concise, professional, and actionable. Format responses with clear headings and bullet points where appropriate.'
            . "\n\n═══ USER'S COMPANY DATA ═══\n" . $dataContext;

        $this->chatAnswer = $ai->ask($this->chatQuestion, $context, 2048, 0.3);

        $this->history[] = [
            'tool' => 'Chat',
            'question' => $this->chatQuestion,
            'time' => now()->format('H:i'),
        ];

        $this->chatLoading = false;
    }

    /**
     * Gather relevant company data to inject as AI context.
     * Queries projects, documents, tasks, tenders, BOQ, safety, and more.
     */
    protected function gatherCompanyContext(int $companyId, string $question): string
    {
        $sections = [];
        $q = strtolower($question);

        // ── Always include: Projects overview ──
        $projects = \App\Models\CdeProject::where('company_id', $companyId)
            ->select('id', 'name', 'status', 'start_date', 'end_date', 'budget', 'created_at')
            ->latest()
            ->limit(20)
            ->get();

        if ($projects->isNotEmpty()) {
            $sections[] = "PROJECTS ({$projects->count()}):\n" . $projects->map(fn($p) =>
                "• [{$p->id}] {$p->name} | Status: {$p->status} | Budget: " . number_format($p->budget ?? 0) .
                " | Start: " . ($p->start_date ?? 'N/A') . " | End: " . ($p->end_date ?? 'N/A')
            )->implode("\n");
        }

        // ── Documents (if question relates to docs/files/documents) ──
        if ($this->questionRelatesTo($q, ['document', 'file', 'drawing', 'report', 'upload', 'cde', 'revision', 'transmittal', 'rfi'])) {
            $docs = \App\Models\CdeDocument::where('company_id', $companyId)
                ->select('id', 'title', 'document_number', 'discipline', 'status', 'revision', 'file_type', 'created_at')
                ->latest()
                ->limit(30)
                ->get();

            if ($docs->isNotEmpty()) {
                $sections[] = "DOCUMENTS ({$docs->count()} recent):\n" . $docs->map(fn($d) =>
                    "• [{$d->document_number}] {$d->title} | Discipline: {$d->discipline} | Status: {$d->status} | Rev: {$d->revision} | Type: {$d->file_type}"
                )->implode("\n");
            }
        }

        // ── Tasks ──
        if ($this->questionRelatesTo($q, ['task', 'todo', 'progress', 'assign', 'deadline', 'workflow', 'milestone', 'schedule'])) {
            $tasks = \App\Models\Task::whereHas('project', fn($pq) => $pq->where('company_id', $companyId))
                ->select('id', 'title', 'status', 'priority', 'assignee_id', 'due_date', 'cde_project_id')
                ->latest()
                ->limit(30)
                ->get();

            if ($tasks->isNotEmpty()) {
                $sections[] = "TASKS ({$tasks->count()} recent):\n" . $tasks->map(fn($t) =>
                    "• {$t->title} | Status: {$t->status} | Priority: {$t->priority} | Due: " . ($t->due_date ?? 'N/A')
                )->implode("\n");
            }
        }

        // ── Tenders ──
        if ($this->questionRelatesTo($q, ['tender', 'bid', 'procurement', 'rfp', 'rfq', 'quotation', 'award'])) {
            $tenders = \App\Models\Tender::where('company_id', $companyId)
                ->select('id', 'title', 'status', 'submission_deadline', 'estimated_value', 'created_at')
                ->latest()
                ->limit(20)
                ->get();

            if ($tenders->isNotEmpty()) {
                $sections[] = "TENDERS ({$tenders->count()}):\n" . $tenders->map(fn($t) =>
                    "• {$t->title} | Status: {$t->status} | Deadline: " . ($t->submission_deadline ?? 'N/A') .
                    " | Est. Value: " . number_format($t->estimated_value ?? 0)
                )->implode("\n");
            }
        }

        // ── BOQ ──
        if ($this->questionRelatesTo($q, ['boq', 'bill of quantities', 'cost', 'budget', 'expense', 'amount', 'payment', 'price'])) {
            $boqs = \App\Models\Boq::whereHas('project', fn($pq) => $pq->where('company_id', $companyId))
                ->with('items:id,boq_id,description,quantity,unit,unit_rate')
                ->latest()
                ->limit(5)
                ->get();

            if ($boqs->isNotEmpty()) {
                $boqData = $boqs->map(function ($boq) {
                    $itemsSummary = $boq->items->take(10)->map(function ($i) {
                        $rate = number_format($i->unit_rate ?? 0);
                        $total = number_format(($i->quantity ?? 0) * ($i->unit_rate ?? 0));
                        return "  - {$i->description} | Qty: {$i->quantity} {$i->unit} × {$rate} = {$total}";
                    })->implode("\n");
                    $projectName = $boq->project->name ?? 'N/A';
                    return "• BOQ #{$boq->id} (Project: {$projectName}):\n{$itemsSummary}";
                })->implode("\n");

                $sections[] = "BILLS OF QUANTITIES:\n{$boqData}";
            }
        }

        // ── Safety / SHEQ ──
        if ($this->questionRelatesTo($q, ['safety', 'incident', 'hazard', 'risk', 'sheq', 'inspection', 'accident', 'injury'])) {
            $incidents = \App\Models\SafetyIncident::where('company_id', $companyId)
                ->select('id', 'title', 'type', 'severity', 'status', 'location', 'created_at')
                ->latest()
                ->limit(20)
                ->get();

            if ($incidents->isNotEmpty()) {
                $sections[] = "SAFETY INCIDENTS ({$incidents->count()}):\n" . $incidents->map(fn($i) =>
                    "• {$i->title} | Type: {$i->type} | Severity: {$i->severity} | Status: {$i->status} | Location: " . ($i->location ?? 'N/A')
                )->implode("\n");
            }
        }

        // ── Contracts ──
        if ($this->questionRelatesTo($q, ['contract', 'subcontract', 'agreement', 'vendor', 'supplier'])) {
            $contracts = \App\Models\Contract::where('company_id', $companyId)
                ->select('id', 'title', 'contract_number', 'type', 'original_value', 'status', 'start_date', 'end_date')
                ->latest()
                ->limit(15)
                ->get();

            if ($contracts->isNotEmpty()) {
                $sections[] = "CONTRACTS ({$contracts->count()}):\n" . $contracts->map(fn($c) =>
                    "• [{$c->contract_number}] {$c->title} | Type: {$c->type}" .
                    " | Value: " . number_format($c->original_value ?? 0) . " | Status: {$c->status}"
                )->implode("\n");
            }
        }

        // ── Fallback: if no specific topic matched, include a broad summary ──
        if (count($sections) <= 1) {
            $docCount = \App\Models\CdeDocument::where('company_id', $companyId)->count();
            $taskCount = \App\Models\Task::whereHas('project', fn($pq) => $pq->where('company_id', $companyId))->count();
            $tenderCount = \App\Models\Tender::where('company_id', $companyId)->count();
            $incidentCount = \App\Models\SafetyIncident::where('company_id', $companyId)->count();

            $sections[] = "SUMMARY STATS:\n"
                . "• Total Projects: {$projects->count()}\n"
                . "• Total Documents: {$docCount}\n"
                . "• Total Tasks: {$taskCount}\n"
                . "• Total Tenders: {$tenderCount}\n"
                . "• Total Safety Incidents: {$incidentCount}";

            // Include recent activity across all types
            $recentDocs = \App\Models\CdeDocument::where('company_id', $companyId)
                ->select('title', 'document_number', 'status', 'created_at')
                ->latest()->limit(10)->get();

            if ($recentDocs->isNotEmpty()) {
                $sections[] = "RECENT DOCUMENTS:\n" . $recentDocs->map(fn($d) =>
                    "• [{$d->document_number}] {$d->title} | {$d->status} | {$d->created_at->diffForHumans()}"
                )->implode("\n");
            }
        }

        return implode("\n\n", $sections) ?: 'No project data found for this company.';
    }

    /**
     * Check if the user's question relates to specific topics.
     */
    protected function questionRelatesTo(string $question, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($question, $keyword)) {
                return true;
            }
        }
        return false;
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
