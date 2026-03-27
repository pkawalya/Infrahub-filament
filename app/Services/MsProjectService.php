<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskDependency;
use App\Models\Milestone;
use Carbon\Carbon;
use SimpleXMLElement;
use Illuminate\Support\Collection;

/**
 * MS Project XML Importer & Exporter.
 *
 * Supports Microsoft Project 2003/2007/2010/2013/2016/2019/2021 XML format.
 * ISO 8601 duration format (PT8H = 8 hours, P5D = 5 days, etc.)
 */
class MsProjectService
{
    // ──────────────────────────────────────
    // Constants
    // ──────────────────────────────────────

    /** Max tasks allowed per import */
    const MAX_TASKS = 2000;

    /** MS Project dependency type codes → our internal keys */
    private const DEPENDENCY_MAP = [
        0 => 'finish_to_finish',
        1 => 'finish_to_start',
        2 => 'start_to_finish',
        3 => 'start_to_start',
    ];

    /** Reverse map for export */
    private const DEPENDENCY_EXPORT_MAP = [
        'finish_to_finish' => 0,
        'finish_to_start'  => 1,
        'start_to_finish'  => 2,
        'start_to_start'   => 3,
    ];

    /** MS Project task type codes */
    private const TASK_TYPE_MAP = [
        0 => 'fixed_units',
        1 => 'fixed_duration',
        2 => 'fixed_work',
    ];

    // ──────────────────────────────────────
    // IMPORT
    // ──────────────────────────────────────

    /**
     * Import an MS Project XML file for a given project.
     *
     * @param  string  $xmlContent  Raw XML string
     * @param  int     $projectId
     * @param  int     $companyId
     * @param  bool    $clearExisting  Delete existing tasks first
     * @return array   Import summary
     * @throws ImportValidationException|\RuntimeException
     */
    public function import(
        string $xmlContent,
        int $projectId,
        int $companyId,
        bool $clearExisting = false
    ): array {
        // ── 1. Parse & Validate XML ──────────────────────────────────
        $xml = $this->parseXml($xmlContent);
        $this->validateProjectXml($xml);

        $rawTasks = $this->extractTasks($xml);

        if (count($rawTasks) === 0) {
            throw new \RuntimeException('The MS Project file contains no tasks.');
        }
        if (count($rawTasks) > self::MAX_TASKS) {
            throw new \RuntimeException(
                'File contains ' . count($rawTasks) . ' tasks. Maximum allowed: ' . self::MAX_TASKS . '.'
            );
        }

        // ── 2. Validate tasks ────────────────────────────────────────
        $errors = $this->validateTasks($rawTasks);
        if (!empty($errors)) {
            throw new ImportValidationException($errors);
        }

        // ── 3. Check circular dependencies ──────────────────────────
        $this->detectCircularDependencies($rawTasks);

        // ── 4. Persist ──────────────────────────────────────────────
        \DB::beginTransaction();
        try {
            if ($clearExisting) {
                TaskDependency::whereIn(
                    'task_id',
                    Task::where('cde_project_id', $projectId)->pluck('id')
                )->delete();
                Task::where('cde_project_id', $projectId)->delete();
            }

            $uidToLocalId = [];
            $tasksCreated = 0;
            $skipped = 0;

            // Sort by outline level so parents are created before children
            usort($rawTasks, fn($a, $b) => ($a['outline_level'] ?? 0) <=> ($b['outline_level'] ?? 0));

            foreach ($rawTasks as $raw) {
                // Resolve parent WBS
                $parentId = null;
                if (!empty($raw['parent_uid']) && isset($uidToLocalId[$raw['parent_uid']])) {
                    $parentId = $uidToLocalId[$raw['parent_uid']];
                }

                $task = Task::create([
                    'company_id'       => $companyId,
                    'cde_project_id'   => $projectId,
                    'parent_id'        => $parentId,
                    'title'            => $raw['title'],
                    'description'      => $raw['notes'] ?? null,
                    'notes'            => $raw['notes'] ?? null,
                    'wbs_code'         => $raw['wbs'] ?? null,
                    'outline_level'    => $raw['outline_level'] ?? 0,
                    'is_summary'       => (bool) ($raw['is_summary'] ?? false),
                    'is_milestone'     => (bool) ($raw['is_milestone'] ?? false),
                    'start_date'       => $raw['start'] ?? null,
                    'due_date'         => $raw['finish'] ?? null,
                    'duration_days'    => $raw['duration_days'] ?? null,
                    'estimated_hours'  => $raw['work_hours'] ?? null,
                    'progress_percent' => $raw['percent_complete'] ?? 0,
                    'priority'         => $this->mapPriority($raw['priority'] ?? 500),
                    'status'           => $this->mapStatus($raw),
                    'fixed_cost'       => $raw['fixed_cost'] ?? null,
                    'resource_names'   => $raw['resource_names'] ?? null,
                    'baseline_start'   => $raw['baseline_start'] ?? null,
                    'baseline_finish'  => $raw['baseline_finish'] ?? null,
                    'baseline_duration'=> $raw['baseline_duration_days'] ?? null,
                    'constraint_type'  => $raw['constraint_type'] ?? null,
                    'constraint_date'  => $raw['constraint_date'] ?? null,
                    'sort_order'       => $raw['id'] ?? $tasksCreated,
                    'created_by'       => auth()->id(),
                ]);

                $uidToLocalId[$raw['uid']] = $task->id;
                $tasksCreated++;
            }

            // ── Dependencies ─────────────────────────────────────────
            $depsCreated = 0;
            foreach ($rawTasks as $raw) {
                $localSuccessorId = $uidToLocalId[$raw['uid']] ?? null;
                if (!$localSuccessorId) continue;

                foreach ($raw['predecessors'] ?? [] as $pred) {
                    $localPredId = $uidToLocalId[$pred['uid']] ?? null;
                    if (!$localPredId) continue;

                    TaskDependency::create([
                        'task_id'         => $localSuccessorId,
                        'depends_on_id'   => $localPredId,
                        'dependency_type' => self::DEPENDENCY_MAP[$pred['type'] ?? 1] ?? 'finish_to_start',
                        'lag_days'        => $pred['lag_days'] ?? 0,
                    ]);
                    $depsCreated++;
                }
            }

            // ── Auto-import milestones ───────────────────────────────
            $milestonesCreated = 0;
            foreach ($rawTasks as $raw) {
                if (!($raw['is_milestone'] ?? false)) continue;
                Milestone::create([
                    'company_id'      => $companyId,
                    'cde_project_id'  => $projectId,
                    'name'            => $raw['title'],
                    'description'     => $raw['notes'] ?? null,
                    'target_date'     => $raw['finish'] ?? $raw['start'] ?? null,
                    'status'          => ($raw['percent_complete'] ?? 0) >= 100 ? 'completed' : 'pending',
                    'priority'        => $this->mapPriority($raw['priority'] ?? 500),
                ]);
                $milestonesCreated++;
            }

            \DB::commit();

            return [
                'tasks_created'      => $tasksCreated,
                'tasks_skipped'      => $skipped,
                'dependencies_created' => $depsCreated,
                'milestones_created' => $milestonesCreated,
                'project_name'       => (string) ($xml->Name ?? ''),
                'project_start'      => isset($xml->StartDate) ? Carbon::parse((string) $xml->StartDate)->format('M d, Y') : null,
                'project_finish'     => isset($xml->FinishDate) ? Carbon::parse((string) $xml->FinishDate)->format('M d, Y') : null,
            ];
        } catch (\Throwable $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    // ──────────────────────────────────────
    // EXPORT
    // ──────────────────────────────────────

    /**
     * Export project tasks as MS Project XML string.
     *
     * @param  int  $projectId
     * @param  string  $projectName
     * @param  string|null  $projectStart
     * @return string  XML content
     */
    public function export(int $projectId, string $projectName, ?string $projectStart = null): string
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, Task> $tasks */
        $tasks = Task::where('cde_project_id', $projectId)
            ->with(['predecessorLinks'])
            ->orderBy('sort_order')
            ->orderBy('wbs_code')
            ->orderBy('id')
            ->get();

        $deps = TaskDependency::whereIn('task_id', $tasks->pluck('id'))->get();

        // Build local id → UID map (1-based for MSP)
        $idToUid = [];
        foreach ($tasks as $idx => $task) {
            $idToUid[$task->id] = $idx + 1;
        }

        $now = now()->format('Y-m-d\TH:i:s');
        $start = $projectStart
            ? Carbon::parse($projectStart)->format('Y-m-d\TH:i:s')
            : ($tasks->min('start_date') ? Carbon::parse($tasks->min('start_date'))->format('Y-m-d\TH:i:s') : $now);

        $finish = $tasks->max('due_date')
            ? Carbon::parse($tasks->max('due_date'))->format('Y-m-d\TH:i:s')
            : $now;

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $project = $dom->createElement('Project');
        $project->setAttribute('xmlns', 'http://schemas.microsoft.com/project');
        $dom->appendChild($project);

        // ── Project Header ───────────────────────────────────────────
        $this->addTextNode($dom, $project, 'SaveVersion', '14');
        $this->addTextNode($dom, $project, 'Name', e($projectName));
        $this->addTextNode($dom, $project, 'CreationDate', $now);
        $this->addTextNode($dom, $project, 'LastSaved', $now);
        $this->addTextNode($dom, $project, 'StartDate', $start);
        $this->addTextNode($dom, $project, 'FinishDate', $finish);
        $this->addTextNode($dom, $project, 'FYStartDate', '1');
        $this->addTextNode($dom, $project, 'CriticalSlackLimit', '0');
        $this->addTextNode($dom, $project, 'CurrencyDigits', '2');
        $this->addTextNode($dom, $project, 'CurrencySymbol', 'UGX');
        $this->addTextNode($dom, $project, 'CalendarUID', '1');
        $this->addTextNode($dom, $project, 'DefaultStartTime', '08:00:00');
        $this->addTextNode($dom, $project, 'DefaultFinishTime', '17:00:00');
        $this->addTextNode($dom, $project, 'MinutesPerDay', '480');
        $this->addTextNode($dom, $project, 'MinutesPerWeek', '2400');
        $this->addTextNode($dom, $project, 'DaysPerMonth', '20');

        // ── Tasks ────────────────────────────────────────────────────
        $tasksEl = $dom->createElement('Tasks');
        $project->appendChild($tasksEl);

        // Summary row (UID=0) required by MSP
        $this->buildSummaryTask($dom, $tasksEl, $projectName, $start, $finish);

        // Build dependency index
        $predecessorsByTask = $deps->groupBy('task_id');

        foreach ($tasks as $idx => $task) {
            $uid = $idToUid[$task->id];
            $taskDeps = $predecessorsByTask->get($task->id, collect());
            $this->buildTaskXml($dom, $tasksEl, $task, $uid, $idToUid, $taskDeps);
        }

        // ── Resources (placeholder) ───────────────────────────────────
        $project->appendChild($dom->createElement('Resources'));

        // ── Assignments (placeholder) ─────────────────────────────────
        $project->appendChild($dom->createElement('Assignments'));

        return $dom->saveXML();
    }

    // ──────────────────────────────────────
    // Private: XML PARSING
    // ──────────────────────────────────────

    private function parseXml(string $content): SimpleXMLElement
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);

        if ($xml === false) {
            $errors = collect(libxml_get_errors())
                ->map(fn($e) => 'Line ' . $e->line . ': ' . trim($e->message))
                ->implode('; ');
            libxml_clear_errors();
            throw new \RuntimeException('Invalid XML: ' . $errors);
        }
        libxml_clear_errors();
        return $xml;
    }

    private function validateProjectXml(SimpleXMLElement $xml): void
    {
        $root = $xml->getName();
        if ($root !== 'Project') {
            throw new \RuntimeException(
                "Invalid MS Project file. Expected root element <Project>, found <{$root}>."
            );
        }

        if (!isset($xml->Tasks)) {
            throw new \RuntimeException('MS Project XML missing <Tasks> section.');
        }
    }

    private function extractTasks(SimpleXMLElement $xml): array
    {
        $tasks = [];
        foreach ($xml->Tasks->Task ?? [] as $task) {
            $uid  = (int) ($task->UID ?? 0);
            $id   = (int) ($task->ID ?? 0);

            // UID=0 is the project summary node — skip
            if ($uid === 0) continue;

            $name = (string) ($task->Name ?? '');
            if ($name === '') continue; // skip blank rows

            $start  = $this->parseDate((string) ($task->Start ?? ''));
            $finish = $this->parseDate((string) ($task->Finish ?? ''));

            // Duration: MSP stores as ISO 8601 duration e.g. PT8H, P2DT0H etc.
            $durationDays     = $this->parseDurationToDays((string) ($task->Duration ?? ''));
            $baselineDurDays  = $this->parseDurationToDays((string) ($task->BaselineDuration ?? ''));
            $workHours        = $this->parseDurationToHours((string) ($task->Work ?? ''));

            // Find parent via WBS — MS Project tracks outline via OutlineLevel
            $outlineLevel = (int) ($task->OutlineLevel ?? 0);

            // Predecessors
            $predecessors = [];
            foreach ($task->PredecessorLink ?? [] as $link) {
                $predUid = (int) ($link->PredecessorUID ?? 0);
                if ($predUid > 0) {
                    $lagDuration = (string) ($link->LinkLag ?? '0');
                    $predecessors[] = [
                        'uid'      => $predUid,
                        'type'     => (int) ($link->Type ?? 1),
                        'lag_days' => $this->parseLagTodays($lagDuration),
                    ];
                }
            }

            // Constraint type mapping
            $constraintTypeRaw = (int) ($task->ConstraintType ?? 0);
            $constraintMap = [
                0 => null,                    // ASAP
                1 => null,                    // ALAP
                2 => 'must_start_on',
                3 => 'must_finish_on',
                4 => 'start_no_earlier_than',
                5 => 'start_no_later_than',
                6 => 'finish_no_earlier_than',
                7 => 'finish_no_later_than',
            ];

            $tasks[] = [
                'uid'                  => $uid,
                'id'                   => $id,
                'title'                => $name,
                'wbs'                  => (string) ($task->WBS ?? ''),
                'outline_level'        => $outlineLevel,
                'is_summary'           => (bool) (int) ($task->Summary ?? 0),
                'is_milestone'         => (bool) (int) ($task->Milestone ?? 0),
                'start'                => $start,
                'finish'               => $finish,
                'duration_days'        => $durationDays,
                'baseline_start'       => $this->parseDate((string) ($task->BaselineStart ?? '')),
                'baseline_finish'      => $this->parseDate((string) ($task->BaselineFinish ?? '')),
                'baseline_duration_days' => $baselineDurDays,
                'work_hours'           => $workHours,
                'percent_complete'     => (int) ($task->PercentComplete ?? 0),
                'priority'             => (int) ($task->Priority ?? 500),
                'notes'                => $this->cleanNotes((string) ($task->Notes ?? '')),
                'fixed_cost'           => (float) ($task->FixedCost ?? 0) ?: null,
                'resource_names'       => (string) ($task->ResourceNames ?? '') ?: null,
                'constraint_type'      => $constraintMap[$constraintTypeRaw] ?? null,
                'constraint_date'      => $this->parseDate((string) ($task->ConstraintDate ?? '')),
                'predecessors'         => $predecessors,
                'parent_uid'           => null, // Resolved from WBS in a later pass
            ];
        }

        // Resolve parent UIDs from outline level / WBS hierarchy
        $this->resolveParents($tasks);

        return $tasks;
    }

    /**
     * Walk the task list and assign parent_uid based on outline level (WBS hierarchy).
     */
    private function resolveParents(array &$tasks): void
    {
        $stack = []; // stack of [level => uid]
        foreach ($tasks as &$task) {
            $level = $task['outline_level'];
            // Pop stack until we find the parent level
            while (!empty($stack) && end($stack)['level'] >= $level) {
                array_pop($stack);
            }
            $task['parent_uid'] = !empty($stack) ? end($stack)['uid'] : null;
            $stack[] = ['level' => $level, 'uid' => $task['uid']];
        }
        unset($task);
    }

    // ──────────────────────────────────────
    // Private: VALIDATION
    // ──────────────────────────────────────

    private function validateTasks(array $tasks): array
    {
        $errors = [];
        $seenTitles = [];

        foreach ($tasks as $i => $task) {
            $row = $task['wbs'] ?: ('#' . ($i + 1));

            // Required title
            if (empty(trim($task['title']))) {
                $errors[] = "Task {$row}: Task name is required.";
            }

            // Date range
            if ($task['start'] && $task['finish']) {
                $s = Carbon::parse($task['start']);
                $f = Carbon::parse($task['finish']);
                if ($f->lt($s)) {
                    $errors[] = "Task {$row} '{$task['title']}': Finish date ({$f->format('M d, Y')}) is before Start date ({$s->format('M d, Y')}).";
                }
            }

            // Non-milestone must have a start date
            if (!$task['is_milestone'] && !$task['is_summary'] && empty($task['start'])) {
                $errors[] = "Task {$row} '{$task['title']}': Missing start date.";
            }

            // Duplicate names warning (not blocking)
            if (!empty($task['title'])) {
                $key = strtolower(trim($task['title']));
                if (isset($seenTitles[$key])) {
                    $errors[] = "Warning — Task {$row}: Duplicate task name '{$task['title']}' (also at {$seenTitles[$key]}).";
                } else {
                    $seenTitles[$key] = $row;
                }
            }

            // Percent complete range
            if ($task['percent_complete'] < 0 || $task['percent_complete'] > 100) {
                $errors[] = "Task {$row} '{$task['title']}': % Complete must be between 0 and 100 (got {$task['percent_complete']}).";
            }

            // Duration sanity
            if ($task['duration_days'] !== null && $task['duration_days'] < 0) {
                $errors[] = "Task {$row} '{$task['title']}': Duration cannot be negative.";
            }

            // Summary tasks should not have predecessors
            if ($task['is_summary'] && !empty($task['predecessors'])) {
                $errors[] = "Warning — Task {$row} '{$task['title']}': Summary task has predecessor links, which may cause scheduling conflicts.";
            }
        }

        return $errors;
    }

    /**
     * Detect circular dependencies using DFS.
     */
    private function detectCircularDependencies(array $tasks): void
    {
        // Build adjacency list (uid → [predecessor uids])
        $adj = [];
        foreach ($tasks as $task) {
            $uid = $task['uid'];
            $adj[$uid] = array_column($task['predecessors'], 'uid');
        }

        $visited = [];
        $stack   = [];

        $dfs = function (int $node) use (&$dfs, &$adj, &$visited, &$stack): ?array {
            if (isset($stack[$node])) {
                return array_keys($stack); // cycle detected
            }
            if (isset($visited[$node])) {
                return null;
            }
            $visited[$node] = true;
            $stack[$node]   = true;

            foreach ($adj[$node] ?? [] as $neighbour) {
                $cycle = $dfs($neighbour);
                if ($cycle !== null) {
                    return $cycle;
                }
            }

            unset($stack[$node]);
            return null;
        };

        foreach (array_keys($adj) as $uid) {
            $cycle = $dfs($uid);
            if ($cycle !== null) {
                throw new \RuntimeException(
                    'Circular dependency detected in the task schedule. ' .
                    'Affected task UIDs: ' . implode(' → ', $cycle) . '.'
                );
            }
        }
    }

    // ──────────────────────────────────────
    // Private: EXPORT HELPERS
    // ──────────────────────────────────────

    private function buildSummaryTask(\DOMDocument $dom, \DOMElement $parent, string $name, string $start, string $finish): void
    {
        $task = $dom->createElement('Task');
        $parent->appendChild($task);
        $this->addTextNode($dom, $task, 'UID', '0');
        $this->addTextNode($dom, $task, 'ID', '0');
        $this->addTextNode($dom, $task, 'Name', e($name));
        $this->addTextNode($dom, $task, 'Type', '1');
        $this->addTextNode($dom, $task, 'IsNull', '0');
        $this->addTextNode($dom, $task, 'CreateDate', now()->format('Y-m-d\TH:i:s'));
        $this->addTextNode($dom, $task, 'WBS', '0');
        $this->addTextNode($dom, $task, 'OutlineLevel', '0');
        $this->addTextNode($dom, $task, 'OutlineNumber', '0');
        $this->addTextNode($dom, $task, 'Priority', '500');
        $this->addTextNode($dom, $task, 'Start', $start);
        $this->addTextNode($dom, $task, 'Finish', $finish);
        $this->addTextNode($dom, $task, 'Duration', 'PT0H0M0S');
        $this->addTextNode($dom, $task, 'Summary', '1');
        $this->addTextNode($dom, $task, 'Milestone', '0');
        $this->addTextNode($dom, $task, 'PercentComplete', '0');
        $this->addTextNode($dom, $task, 'PercentWorkComplete', '0');
    }

    private function buildTaskXml(
        \DOMDocument $dom,
        \DOMElement $parent,
        Task $task,
        int $uid,
        array $idToUid,
        Collection $deps
    ): void {
        $taskEl = $dom->createElement('Task');
        $parent->appendChild($taskEl);

        $start  = $task->start_date  ? $task->start_date->format('Y-m-d\TH:i:s')  : now()->format('Y-m-d\TH:i:s');
        $finish = $task->due_date    ? $task->due_date->format('Y-m-d\TH:i:s')    : $start;

        $durationDays = $task->duration_days ?? max(1, $task->start_date && $task->due_date
            ? $task->start_date->diffInDays($task->due_date)
            : 1);

        $durationISO = $this->daysToDurationISO((float) $durationDays);
        $workISO     = $task->estimated_hours
            ? $this->hoursToDurationISO((float) $task->estimated_hours)
            : $durationISO;

        $this->addTextNode($dom, $taskEl, 'UID', (string) $uid);
        $this->addTextNode($dom, $taskEl, 'ID', (string) $uid);
        $this->addTextNode($dom, $taskEl, 'Name', e($task->title));
        $this->addTextNode($dom, $taskEl, 'Type', '1');
        $this->addTextNode($dom, $taskEl, 'IsNull', '0');
        $this->addTextNode($dom, $taskEl, 'CreateDate', $task->created_at ? $task->created_at->format('Y-m-d\TH:i:s') : now()->format('Y-m-d\TH:i:s'));
        $this->addTextNode($dom, $taskEl, 'WBS', $task->wbs_code ?? (string) $uid);
        $this->addTextNode($dom, $taskEl, 'OutlineLevel', (string) ($task->outline_level ?? 1));
        $this->addTextNode($dom, $taskEl, 'OutlineNumber', $task->wbs_code ?? (string) $uid);
        $this->addTextNode($dom, $taskEl, 'Priority', (string) $this->priorityToMsp($task->priority));
        $this->addTextNode($dom, $taskEl, 'Start', $start);
        $this->addTextNode($dom, $taskEl, 'Finish', $finish);
        $this->addTextNode($dom, $taskEl, 'Duration', $durationISO);
        $this->addTextNode($dom, $taskEl, 'Work', $workISO);
        $this->addTextNode($dom, $taskEl, 'Summary', $task->is_summary ? '1' : '0');
        $this->addTextNode($dom, $taskEl, 'Milestone', $task->is_milestone ? '1' : '0');
        $this->addTextNode($dom, $taskEl, 'PercentComplete', (string) ($task->progress_percent ?? 0));
        $this->addTextNode($dom, $taskEl, 'PercentWorkComplete', (string) ($task->progress_percent ?? 0));

        // Cost
        if ($task->fixed_cost) {
            $this->addTextNode($dom, $taskEl, 'FixedCost', number_format((float) $task->fixed_cost, 2, '.', ''));
        }
        if ($task->actual_cost) {
            $this->addTextNode($dom, $taskEl, 'ActualCost', number_format((float) $task->actual_cost, 2, '.', ''));
        }

        // Actual dates
        if ($task->actual_start) {
            $this->addTextNode($dom, $taskEl, 'ActualStart', $task->actual_start->format('Y-m-d\TH:i:s'));
        }
        if ($task->actual_finish) {
            $this->addTextNode($dom, $taskEl, 'ActualFinish', $task->actual_finish->format('Y-m-d\TH:i:s'));
        }
        if ($task->actual_hours) {
            $this->addTextNode($dom, $taskEl, 'ActualWork', $this->hoursToDurationISO((float) $task->actual_hours));
        }

        // Baseline
        if ($task->baseline_start) {
            $this->addTextNode($dom, $taskEl, 'BaselineStart', $task->baseline_start->format('Y-m-d\TH:i:s'));
        }
        if ($task->baseline_finish) {
            $this->addTextNode($dom, $taskEl, 'BaselineFinish', $task->baseline_finish->format('Y-m-d\TH:i:s'));
        }
        if ($task->baseline_duration) {
            $this->addTextNode($dom, $taskEl, 'BaselineDuration', $this->daysToDurationISO((float) $task->baseline_duration));
        }

        // Notes
        if ($task->notes || $task->description) {
            $this->addTextNode($dom, $taskEl, 'Notes', e($task->notes ?? $task->description));
        }

        // Resource names
        if ($task->resource_names) {
            $this->addTextNode($dom, $taskEl, 'ResourceNames', e($task->resource_names));
        }

        // Predecessor links
        foreach ($deps as $dep) {
            $predUid = $idToUid[$dep->depends_on_id] ?? null;
            if (!$predUid) continue;

            $linkEl = $dom->createElement('PredecessorLink');
            $taskEl->appendChild($linkEl);
            $this->addTextNode($dom, $linkEl, 'PredecessorUID', (string) $predUid);
            $this->addTextNode($dom, $linkEl, 'Type', (string) (self::DEPENDENCY_EXPORT_MAP[$dep->dependency_type] ?? 1));
            $this->addTextNode($dom, $linkEl, 'CrossProject', 'false');
            $lagDays = (int) ($dep->lag_days ?? 0);
            // MSP stores lag in minutes (480min = 1 day in standard 8h/day)
            $this->addTextNode($dom, $linkEl, 'LinkLag', (string) ($lagDays * 480));
            $this->addTextNode($dom, $linkEl, 'LagFormat', '7'); // 7 = days
        }
    }

    private function addTextNode(\DOMDocument $dom, \DOMElement $parent, string $tag, string $value): void
    {
        $el = $dom->createElement($tag);
        $el->appendChild($dom->createTextNode($value));
        $parent->appendChild($el);
    }

    // ──────────────────────────────────────
    // Private: DURATION PARSERS
    // ──────────────────────────────────────

    /**
     * Parse ISO 8601 duration to decimal days (8h/day standard).
     * e.g. "PT8H" → 1.0, "P5D" → 5.0, "P2DT4H" → 2.5, "PT0H0M0S" → 0
     */
    private function parseDurationToDays(string $duration): ?float
    {
        if (empty($duration) || $duration === 'PT0H0M0S') return null;

        preg_match('/P(?:(\d+(?:\.\d+)?)Y)?(?:(\d+(?:\.\d+)?)M)?(?:(\d+(?:\.\d+)?)D)?(?:T(?:(\d+(?:\.\d+)?)H)?(?:(\d+(?:\.\d+)?)M)?(?:(\d+(?:\.\d+)?)S)?)?/', $duration, $m);

        $days   = (float) ($m[3] ?? 0);
        $hours  = (float) ($m[4] ?? 0);
        $minutes= (float) ($m[5] ?? 0);

        $totalHours = ($days * 8) + $hours + ($minutes / 60);
        return $totalHours > 0 ? round($totalHours / 8, 2) : null;
    }

    /** Parse ISO 8601 duration to total hours. */
    private function parseDurationToHours(string $duration): ?float
    {
        if (empty($duration) || $duration === 'PT0H0M0S') return null;

        preg_match('/P(?:(\d+(?:\.\d+)?)D)?(?:T(?:(\d+(?:\.\d+)?)H)?(?:(\d+(?:\.\d+)?)M)?)?/', $duration, $m);

        $days  = (float) ($m[1] ?? 0);
        $hours = (float) ($m[2] ?? 0);
        $mins  = (float) ($m[3] ?? 0);

        $total = ($days * 8) + $hours + ($mins / 60);
        return $total > 0 ? round($total, 2) : null;
    }

    /**
     * MS Project stores lag as integer minutes (e.g. 480 = 1 day).
     * Value may also be "PT8H0M0S" string.
     */
    private function parseLagTodays(string $lag): float
    {
        if (is_numeric($lag)) {
            return round((float) $lag / 480, 2);
        }
        return $this->parseDurationToDays($lag) ?? 0;
    }

    /** Convert decimal days to MS Project ISO 8601 duration string */
    private function daysToDurationISO(float $days): string
    {
        $totalHours = $days * 8;
        $h = (int) $totalHours;
        $m = (int) round(($totalHours - $h) * 60);
        return "PT{$h}H{$m}M0S";
    }

    /** Convert decimal hours to MS Project ISO 8601 duration string */
    private function hoursToDurationISO(float $hours): string
    {
        $h = (int) $hours;
        $m = (int) round(($hours - $h) * 60);
        return "PT{$h}H{$m}M0S";
    }

    // ──────────────────────────────────────
    // Private: MAPPING HELPERS
    // ──────────────────────────────────────

    private function parseDate(string $dateStr): ?string
    {
        if (empty($dateStr) || $dateStr === 'NA') return null;
        try {
            return Carbon::parse($dateStr)->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * MS Project priority: 0–1000 (500 = medium)
     * Map to our priority keys.
     */
    private function mapPriority(int $mspPriority): string
    {
        return match (true) {
            $mspPriority >= 900 => 'urgent',
            $mspPriority >= 700 => 'high',
            $mspPriority >= 400 => 'medium',
            default             => 'low',
        };
    }

    private function priorityToMsp(?string $priority): int
    {
        return match ($priority) {
            'urgent' => 900,
            'high'   => 700,
            'medium' => 500,
            'low'    => 300,
            default  => 500,
        };
    }

    private function mapStatus(array $raw): string
    {
        if ($raw['percent_complete'] >= 100) return 'done';
        if ($raw['percent_complete'] > 0)   return 'in_progress';
        return 'to_do'; // matches Task::$statuses key ('to_do' not 'todo')
    }

    /** Strip HTML tags from MSP notes (MSP uses HTML internally) */
    private function cleanNotes(string $notes): string
    {
        if (empty($notes)) return '';
        return trim(strip_tags(html_entity_decode($notes)));
    }
}
