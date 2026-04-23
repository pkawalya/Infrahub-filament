<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AiAssistantService
 *
 * Central AI integration layer for Infrahub using Google Gemini Flash (free tier).
 * All module-specific AI features route through this service.
 *
 * Setup:
 *   1. Add GEMINI_API_KEY to your .env
 *   2. Get a free key at https://aistudio.google.com/app/apikey
 */
class AiAssistantService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key', '');
        $this->model  = config('services.gemini.model', 'gemini-2.5-flash');
    }

    /**
     * Returns true if the service is configured and ready.
     */
    public function isAvailable(): bool
    {
        return !empty($this->apiKey);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Core method
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Ordered list of models to try. First available and non-rate-limited wins.
     * Configured via GEMINI_MODEL in .env; fallbacks are hardcoded by stability.
     */
    protected function modelChain(): array
    {
        $primary = $this->model;

        $fallbacks = [
            'gemini-flash-latest',
            'gemini-2.0-flash-lite',
            'gemini-2.0-flash',
            'gemini-2.5-flash',
        ];

        // Put primary first, then the rest (deduped)
        return array_values(array_unique(array_merge([$primary], $fallbacks)));
    }

    /**
     * Send a prompt to Gemini with automatic model fallback on 429/503 errors.
     *
     * @param  string  $prompt         The user prompt / question.
     * @param  string  $systemContext  Optional system-level instruction for the AI role.
     * @param  int     $maxTokens      Max output tokens (default 1024).
     * @param  float   $temperature    Creativity 0.0–1.0 (lower = more deterministic).
     * @param  bool    $jsonMode       Request JSON response mode (Gemini native).
     */
    public function ask(
        string $prompt,
        string $systemContext = '',
        int    $maxTokens = 1024,
        float  $temperature = 0.3,
        bool   $jsonMode = false
    ): string {
        if (!$this->isAvailable()) {
            return '⚠️ AI is not configured. Add GEMINI_API_KEY to your .env file.';
        }

        $parts = [];
        if ($systemContext) {
            $parts[] = ['text' => "SYSTEM: {$systemContext}\n\n"];
        }
        $parts[] = ['text' => $prompt];

        $payload = [
            'contents' => [['parts' => $parts]],
            'generationConfig' => [
                'temperature'     => $temperature,
                'maxOutputTokens' => $maxTokens,
            ],
        ];

        if ($jsonMode) {
            $payload['generationConfig']['responseMimeType'] = 'application/json';
        }

        $lastError = '⚠️ AI is temporarily unavailable. Please try again.';

        foreach ($this->modelChain() as $model) {
            try {
                $response = Http::timeout(45)->post(
                    "{$this->baseUrl}/{$model}:generateContent?key={$this->apiKey}",
                    $payload
                );

                $status = $response->status();

                // Retryable errors — try next model
                if (in_array($status, [429, 503, 500, 502, 504])) {
                    Log::warning("Gemini model [{$model}] returned {$status}, trying fallback.");
                    $lastError = match($status) {
                        429 => '⚠️ AI rate limit reached. Please try again in a moment.',
                        503 => '⚠️ AI service overloaded. Please try again shortly.',
                        default => '⚠️ AI service error. Please try again.',
                    };
                    continue;
                }

                if ($response->failed()) {
                    Log::error('Gemini API error', ['model' => $model, 'status' => $status, 'body' => $response->body()]);
                    $lastError = '⚠️ AI service returned an error. Please try again in a moment.';
                    continue;
                }

                return data_get(
                    $response->json(),
                    'candidates.0.content.parts.0.text',
                    'No response generated.'
                );

            } catch (\Throwable $e) {
                Log::error('Gemini API exception', ['model' => $model, 'message' => $e->getMessage()]);
                $lastError = '⚠️ AI is temporarily unavailable. Please try again.';
            }
        }

        return $lastError;
    }

    /**
     * Robustly extract and decode JSON from a Gemini response.
     * Handles markdown code fences (```json ... ```) that Gemini 2.5 always adds.
     */
    private function parseJson(string $raw): ?array
    {
        // Try direct decode first
        $decoded = json_decode(trim($raw), true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // Extract from code fence: ```json ... ``` or ``` ... ```
        if (preg_match('/```(?:json)?\s*([\s\S]*?)\s*```/i', $raw, $matches)) {
            $decoded = json_decode(trim($matches[1]), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        // Last resort: find first { or [ and extract to matching closer
        if (preg_match('/(\{[\s\S]*\}|\[[\s\S]*\])/s', $raw, $matches)) {
            $decoded = json_decode(trim($matches[1]), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        return null;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CDE — Document Intelligence
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Summarise a document title + description into concise bullet points.
     * Used in the CDE Document Management module.
     */
    public function summariseDocument(string $title, string $description = '', string $discipline = ''): string
    {
        $context = $discipline ? "This is a {$discipline} document." : '';

        $prompt = <<<PROMPT
Summarise the following construction project document in 4–6 clear, professional bullet points.
Focus on what the document covers, its purpose, and any key actions or decisions it represents.

Document Title: {$title}
{$context}
Description: {$description}

Format your response as bullet points starting with "•".
PROMPT;

        return $this->ask(
            $prompt,
            'You are an expert construction project document controller with ISO 19650 knowledge. Be concise and professional.',
            512,
            0.2
        );
    }

    /**
     * Suggest the most appropriate CDE discipline and status for a document
     * based on its title and description.
     */
    public function classifyDocument(string $title, string $description = ''): array
    {
        $prompt = <<<PROMPT
Based on this construction document title and description, suggest the best values for:
- discipline: one of [architecture, structural, mechanical, electrical, plumbing, civil, landscape, other]
- status: one of [S0, S1, S2, S3, S4, S6, S7] where S0=WIP, S3=For Review, S4=For Approval, S7=Published
- document_type: a short label e.g. "Drawing", "Specification", "Report", "Contract", "Method Statement"

Title: {$title}
Description: {$description}

Return ONLY valid JSON like: {"discipline":"structural","status":"S0","document_type":"Drawing"}
PROMPT;

        $raw = $this->ask($prompt, 'You are a construction document management expert.', 128, 0.1, true);

        return $this->parseJson($raw) ?? [];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SHEQ — Safety Intelligence
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Analyse a safety incident description and extract structured insights.
     * Used in the SHEQ module to auto-populate root cause and corrective actions.
     */
    public function analyseSafetyIncident(string $title, string $description, string $type = '', string $severity = ''): array
    {
        $context = "Incident type: {$type}. Severity: {$severity}.";

        $prompt = <<<PROMPT
Analyse this construction site safety incident and return a JSON object with these exact fields:
- root_cause: (string) the most likely root cause in 1-2 sentences
- corrective_actions: (array of 3-5 strings) specific actions to fix and prevent recurrence
- prevention_tips: (array of 2-4 strings) broader preventive measures for future
- risk_level: (string) one of: low | medium | high | critical
- regulatory_reference: (string) relevant safety standard or regulation if applicable (e.g. OSHA 1926, ISO 45001), or empty string

Incident Title: {$title}
{$context}
Description: {$description}

Return ONLY valid JSON. No markdown. No explanation.
PROMPT;

        $raw = $this->ask(
            $prompt,
            'You are a certified HSE (Health, Safety and Environment) expert specializing in construction safety.',
            800,
            0.2,
            true  // JSON mode
        );

        return $this->parseJson($raw) ?? [
            'root_cause'             => 'Unable to parse AI response. Please analyse manually.',
            'corrective_actions'     => [],
            'prevention_tips'        => [],
            'risk_level'             => 'medium',
            'regulatory_reference'   => '',
        ];
    }

    /**
     * Generate a site-specific inspection checklist based on project type and scope.
     */
    public function generateInspectionChecklist(string $projectType, string $inspectionType, string $phase = ''): array
    {
        $phaseContext = $phase ? " Currently in {$phase} phase." : '';

        $prompt = <<<PROMPT
Generate a construction site inspection checklist for:
- Project type: {$projectType}
- Inspection type: {$inspectionType}{$phaseContext}

Return a JSON array of checklist items. Each item: {"item": "Check description", "category": "category name", "critical": true/false}
Include 10-15 items covering safety, quality, and compliance.
Return ONLY valid JSON array.
PROMPT;

        $raw = $this->ask(
            $prompt,
            'You are a construction safety inspector with 20 years of experience.',
            1024,
            0.3,
            true  // JSON mode
        );

        return $this->parseJson($raw) ?? [];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Tasks & Workflows
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Generate a structured task list from a scope-of-work description.
     * Used in the Task Management module.
     */
    public function generateTaskList(string $scopeOfWork, string $projectType = 'construction'): array
    {
        $prompt = <<<PROMPT
Break down this {$projectType} scope of work into a structured task list.
Return a JSON array where each item has:
{
  "title": "Task name",
  "description": "What needs to be done",
  "estimated_days": 0,
  "phase": "Planning|Design|Procurement|Construction|Commissioning|Closeout",
  "priority": "low|medium|high"
}

Scope of Work:
{$scopeOfWork}

Return ONLY a valid JSON array with 5-15 tasks. No markdown.
PROMPT;

        $raw = $this->ask(
            $prompt,
            'You are an expert project manager for infrastructure and construction projects.',
            1200,
            0.3,
            true  // JSON mode
        );

        return $this->parseJson($raw) ?? [];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // BOQ — Cost Intelligence
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Suggest a full BOQ item description from a short label.
     */
    public function completeBOQDescription(string $shortLabel, string $projectType = 'building'): string
    {
        $prompt = <<<PROMPT
Write a professional Bill of Quantities (BOQ) item description for use in a {$projectType} construction contract.
The short label is: "{$shortLabel}"

Provide a clear, complete, and measurable description (2-4 sentences) suitable for a formal BOQ document.
Include material specification hints where appropriate.
PROMPT;

        return $this->ask(
            $prompt,
            'You are a quantity surveyor with 15 years of experience writing construction BOQ specifications.',
            300,
            0.4
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Reporting — Narrative Intelligence
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Generate a plain-English executive summary from project KPI data.
     * Used in the Reporting module.
     *
     * @param  array  $kpis  e.g. ['budget_spent_pct' => 65, 'tasks_complete' => 42, ...]
     */
    public function generateProjectSummary(string $projectName, array $kpis): string
    {
        $kpiText = collect($kpis)
            ->map(fn($v, $k) => "- " . str_replace('_', ' ', ucfirst($k)) . ": {$v}")
            ->implode("\n");

        $prompt = <<<PROMPT
Write a concise, professional executive summary (3-4 paragraphs) for a construction project status report.

Project: {$projectName}
Current KPIs:
{$kpiText}

The summary should:
1. Give an overall health assessment (on track / at risk / critical)
2. Highlight key achievements
3. Flag concerns or risks
4. Suggest 2-3 recommended actions for the project team

Write in a professional but clear tone suitable for a client-facing report.
PROMPT;

        return $this->ask(
            $prompt,
            'You are a senior project manager writing an executive summary for a board-level project report.',
            800,
            0.5
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Tenders
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Extract key requirements from a tender document description.
     */
    public function extractTenderRequirements(string $tenderDescription): array
    {
        $prompt = <<<PROMPT
Extract key information from this tender/procurement opportunity description.
Return a JSON object with:
{
  "key_requirements": ["array of main requirements"],
  "deadlines": ["array of important dates mentioned"],
  "evaluation_criteria": ["array of scoring/evaluation criteria"],
  "risks": ["array of potential risks or concerns"],
  "bid_recommendation": "Bid | No-Bid | Needs More Info",
  "bid_reason": "One sentence explaining the recommendation"
}

Tender Description:
{$tenderDescription}

Return ONLY valid JSON. No markdown.
PROMPT;

        $raw = $this->ask(
            $prompt,
            'You are a bid manager with 15 years of experience evaluating construction tenders.',
            1024,
            0.3,
            true  // JSON mode
        );

        return $this->parseJson($raw) ?? [];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Daily Site Diary
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Draft a Daily Site Diary entry from structured inputs.
     */
    public function draftDiaryEntry(array $inputs): string
    {
        $crewCount   = $inputs['crew_count']   ?? 0;
        $weather     = $inputs['weather']       ?? 'clear';
        $activities  = $inputs['activities']    ?? '';
        $issues      = $inputs['issues']        ?? '';
        $date        = $inputs['date']          ?? now()->toDateString();

        $prompt = <<<PROMPT
Write a professional Daily Site Diary entry for a construction project.

Date: {$date}
Weather: {$weather}
Crew on site: {$crewCount} personnel
Activities carried out: {$activities}
Issues or observations: {$issues}

Write a clear, professional diary entry (2-3 paragraphs) in past tense, as if written by the Site Manager.
Include weather conditions, work progress, any issues encountered, and next day's plan.
PROMPT;

        return $this->ask(
            $prompt,
            'You are an experienced Site Manager writing a formal daily site diary entry for a construction project.',
            600,
            0.5
        );
    }
}
