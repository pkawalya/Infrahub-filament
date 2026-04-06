<?php

namespace App\Services;

use App\Models\Task;
use App\Models\TaskDependency;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Imports a Construction Schedule from an Excel (.xlsx) file.
 *
 * Expected column layout (no strict headers needed):
 *   Column A — Task Name
 *   Column B — Duration (e.g. "70 days", "5 days", "491 days")
 *   Column C — Predecessors (MS Project notation: "4", "22,6", "14SS+7 days")
 *
 * Outline levels (WBS hierarchy) are inferred from the actual Excel row
 * indentation: rows with no character at the start are level-0 summaries,
 * rows indented with leading spaces / shorter names under a summary are
 * sub-items.  Because Excel doesn't natively expose indentation depth we
 * use the predecessor / duration pattern and the "is_summary" heuristic:
 * a row with no predecessors and children beneath it is treated as summary.
 *
 * A simpler and reliable heuristic used here:
 * - When the spreadsheet looks like the sample (MEMD 2025-2026), the
 *   indentation is encoded in leading spaces inside the shared string.
 *   We count leading spaces ÷ 3 for the outline level.
 */
class ScheduleExcelImportService
{
    // -----------------------------------------------------------------
    // Public API
    // -----------------------------------------------------------------

    /**
     * Import an Excel construction schedule.
     *
     * @param  string  $filePath    Absolute path to the .xlsx file
     * @param  int     $projectId   CdeProject ID
     * @param  int     $companyId   Company ID (ownership guard)
     * @param  \Carbon\Carbon|null  $projectStart  Override start date (defaults to today)
     * @param  bool    $clearExisting  Wipe existing tasks first
     * @return array   ['tasks_created', 'dependencies_created', 'warnings']
     */
    public function import(
        string $filePath,
        int $projectId,
        int $companyId,
        ?Carbon $projectStart = null,
        bool $clearExisting = false,
    ): array {
        // --- ownership guard ---
        $project = \App\Models\CdeProject::withoutGlobalScopes()->find($projectId);
        if (!$project || (int) $project->company_id !== (int) $companyId) {
            throw new \RuntimeException('Access denied: project does not belong to your company.');
        }

        $start     = $projectStart ?? now()->startOfDay();
        $warnings  = [];
        $rawRows   = $this->readRawRows($filePath, $warnings);

        if (empty($rawRows)) {
            throw new \InvalidArgumentException('The Excel file appears to be empty or unreadable.');
        }

        DB::beginTransaction();
        try {
            if ($clearExisting) {
                TaskDependency::whereHas('task', fn($q) => $q->where('cde_project_id', $projectId))->delete();
                Task::where('cde_project_id', $projectId)->forceDelete();
            }

            [$tasks_created, $dependencies_created, $importWarns] = $this->persistRows(
                $rawRows, $projectId, $companyId, $start
            );

            $warnings = array_merge($warnings, $importWarns);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ScheduleExcelImport failed', ['error' => $e->getMessage(), 'project' => $projectId]);
            throw $e;
        }

        // Regenerate WBS codes
        if (method_exists(Task::class, 'regenerateWbs')) {
            Task::regenerateWbs($projectId);
        }

        return compact('tasks_created', 'dependencies_created', 'warnings');
    }

    // -----------------------------------------------------------------
    // Step 1 — Read raw rows from the xlsx via PHP ZipArchive + XML
    // (avoids loading the full Maatwebsite\Excel chunk for large files)
    // -----------------------------------------------------------------

    private function readRawRows(string $filePath, array &$warnings): array
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("File not found: {$filePath}");
        }

        $rows = [];

        try {
            $zip = new \ZipArchive();
            if ($zip->open($filePath) !== true) {
                throw new \RuntimeException('Cannot open xlsx file.');
            }

            $ns  = ['x' => 'http://schemas.openxmlformats.org/spreadsheetml/2006/main'];

            // Shared strings (text values)
            $strings = [];
            $sstXml  = $zip->getFromName('xl/sharedStrings.xml');
            if ($sstXml) {
                $sst = simplexml_load_string($sstXml);
                $sst->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                foreach ($sst->xpath('//x:si') as $si) {
                    $txt = '';
                    foreach ($si->xpath('.//x:t') as $t) {
                        $txt .= (string) $t;
                    }
                    $strings[] = $txt;
                }
            }

            // Sheet 1
            $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
            $zip->close();

            if (!$sheetXml) {
                throw new \RuntimeException('sheet1.xml not found inside the xlsx.');
            }

            $ws = simplexml_load_string($sheetXml);
            $ws->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

            // Skip the header row (row 1 = "Task Name | Duration | Predecessors")
            $headerSkipped = false;
            foreach ($ws->xpath('//x:row') as $row) {
                $cells = [];
                foreach ($row->xpath('x:c') as $c) {
                    $ref  = (string) ($c['r'] ?? '');
                    $col  = preg_replace('/\d/', '', $ref); // e.g. "A3" → "A"
                    $t    = (string) ($c['t'] ?? '');
                    $v    = $c->xpath('x:v');
                    $val  = '';
                    if (!empty($v) && (string) $v[0] !== '') {
                        $raw = (string) $v[0];
                        $val = ($t === 's') ? ($strings[(int) $raw] ?? $raw) : $raw;
                    }
                    $cells[$col] = $val;
                }

                if (empty($cells['A'])) continue;

                // First row with text in A is the header → skip it
                if (!$headerSkipped) {
                    if (stripos($cells['A'], 'task') !== false || stripos($cells['A'], 'name') !== false) {
                        $headerSkipped = true;
                        continue;
                    }
                    $headerSkipped = true; // Skip anyway if no heading found in first row
                }

                $taskName = trim($cells['A'] ?? '');
                if ($taskName === '') continue;

                // Detect leading spaces for outline nesting
                $leadingSpaces = strlen($cells['A']) - strlen(ltrim($cells['A']));
                $outlineLevel  = (int) floor($leadingSpaces / 3);

                $durationRaw   = trim($cells['B'] ?? '');
                $durationDays  = $this->parseDays($durationRaw);
                $predecessors  = trim($cells['C'] ?? '');

                $rows[] = [
                    'title'        => $taskName,
                    'outline'      => $outlineLevel,
                    'duration'     => $durationDays,
                    'predecessors' => $predecessors,
                ];
            }
        } catch (\Throwable $e) {
            $warnings[] = 'Low-level read failed, falling back to Maatwebsite: ' . $e->getMessage();
            return $this->readViaLaravel($filePath, $warnings);
        }

        return $rows;
    }

    /** Fallback using Maatwebsite\Excel if the zip-based reader fails */
    private function readViaLaravel(string $filePath, array &$warnings): array
    {
        // toArray requires an importer; we use an anonymous class implementing ToArray
        $importer = new class implements \Maatwebsite\Excel\Concerns\ToArray {
            public array $data = [];
            public function array(array $array): void { $this->data = $array; }
        };
        Excel::import($importer, $filePath);
        $data = $importer->data ?? [];

        $rows = [];
        $headerSkipped = false;

        foreach ($data as $row) {
            $a = trim((string) ($row[0] ?? ''));
            if ($a === '') continue;
            if (!$headerSkipped) { $headerSkipped = true; continue; }

            $leading      = strlen($row[0] ?? '') - strlen(ltrim((string) ($row[0] ?? '')));
            $outlineLevel = (int) floor($leading / 3);

            $rows[] = [
                'title'        => $a,
                'outline'      => $outlineLevel,
                'duration'     => $this->parseDays((string) ($row[1] ?? '')),
                'predecessors' => trim((string) ($row[2] ?? '')),
            ];
        }

        return $rows;
    }

    // -----------------------------------------------------------------
    // Step 2 — Persist tasks + build dependency map
    // -----------------------------------------------------------------

    /**
     * Insert tasks and register dependency links.
     * We do a two-pass approach:
     *   Pass 1 — insert all tasks (no dependencies), get DB IDs, map row# → task ID
     *   Pass 2 — resolve predecessor references and insert TaskDependency records
     */
    private function persistRows(array $rows, int $projectId, int $companyId, Carbon $start): array
    {
        $warnings          = [];
        $taskIdByRow       = []; // 1-based row number → DB task ID
        $taskIdByExcelSeq  = []; // sequential task number (1,2,3…) → DB task ID
        $parentStack       = []; // outline_level → task_id used to assign parent_id
        $sortOrder         = 0;
        $currentDate       = $start->copy();
        $tasks_created     = 0;

        // ----- PASS 1: Insert tasks -----
        foreach ($rows as $rowIdx => $row) {
            $rowNum  = $rowIdx + 1;   // 1-based
            $title   = $row['title'];
            $outline = $row['outline'];
            $dur     = max(0, (int) $row['duration']);
            $isSummary = ($dur === 0 && strpos($row['predecessors'], '') === 0);

            // Determine parent from stack
            $parentId = null;
            if ($outline > 0) {
                // Walk up the stack to find the nearest parent level
                for ($lvl = $outline - 1; $lvl >= 0; $lvl--) {
                    if (isset($parentStack[$lvl])) {
                        $parentId = $parentStack[$lvl];
                        break;
                    }
                }
            }

            // Calculate start/due (simple sequential: starts after previous sibling ends)
            $taskStart = $currentDate->copy();
            $taskDue   = $dur > 0 ? $taskStart->copy()->addWeekdays($dur) : $taskStart->copy();

            $task = Task::create([
                'company_id'     => $companyId,
                'cde_project_id' => $projectId,
                'parent_id'      => $parentId,
                'title'          => $title,
                'status'         => 'todo',
                'priority'       => 'medium',
                'duration_days'  => $dur > 0 ? $dur : null,
                'start_date'     => $taskStart->toDateString(),
                'due_date'       => $dur > 0 ? $taskDue->toDateString() : null,
                'outline_level'  => $outline,
                'is_summary'     => $outline === 0 && $row['predecessors'] === '',
                'is_milestone'   => $dur === 0 && $row['predecessors'] !== '',
                'sort_order'     => $sortOrder++,
                'created_by'     => auth()->id(),
            ]);

            $taskIdByRow[$rowNum]      = $task->id;
            $taskIdByExcelSeq[$rowNum] = $task->id;
            $parentStack[$outline]     = $task->id;

            // Clear any deeper levels from stack
            foreach (array_keys($parentStack) as $lvl) {
                if ($lvl > $outline) unset($parentStack[$lvl]);
            }

            // Advance date only for leaf tasks
            if ($dur > 0 && $row['predecessors'] === '') {
                $currentDate = $taskDue->copy()->addWeekday();
            }

            $tasks_created++;
        }

        // ----- PASS 2: Insert dependencies -----
        $dependencies_created = 0;

        foreach ($rows as $rowIdx => $row) {
            $rowNum       = $rowIdx + 1;
            $predecessors = $row['predecessors'];
            if ($predecessors === '') continue;

            $taskId = $taskIdByRow[$rowNum] ?? null;
            if (!$taskId) continue;

            foreach ($this->parsePredecessors($predecessors) as $pred) {
                $predRowNum = (int) $pred['row'];
                $predTaskId = $taskIdByExcelSeq[$predRowNum] ?? null;

                if (!$predTaskId) {
                    $warnings[] = "Row {$rowNum}: predecessor row #{$predRowNum} not found — skipped.";
                    continue;
                }

                if ($predTaskId === $taskId) {
                    $warnings[] = "Row {$rowNum}: task cannot depend on itself — skipped.";
                    continue;
                }

                try {
                    TaskDependency::firstOrCreate([
                        'task_id'      => $taskId,
                        'depends_on_id' => $predTaskId,
                    ], [
                        'dependency_type' => $pred['type'],
                        'lag_days'        => $pred['lag'],
                    ]);
                    $dependencies_created++;
                } catch (\Throwable $e) {
                    $warnings[] = "Row {$rowNum}: could not create dependency → " . $e->getMessage();
                }
            }
        }

        return [$tasks_created, $dependencies_created, $warnings];
    }

    // -----------------------------------------------------------------
    // Parsing helpers
    // -----------------------------------------------------------------

    /**
     * Parse "70 days" / "491 days" / "5" → integer days.
     */
    private function parseDays(string $raw): int
    {
        if ($raw === '') return 0;
        preg_match('/(\d+(?:\.\d+)?)\s*(?:day|d)?/i', $raw, $m);
        return isset($m[1]) ? (int) round((float) $m[1]) : 0;
    }

    /**
     * Parse MS Project predecessor string into an array of dependency specs.
     *
     * Handles:
     *   "4"           → FS row 4, lag 0
     *   "22,6"        → FS row 22 + FS row 6
     *   "14SS"        → SS row 14, lag 0
     *   "14SS+7 days" → SS row 14, lag +7
     *   "29SS+2 days,34SS+2 days,123" → multiple
     *
     * @return array<int, array{row:int, type:string, lag:int}>
     */
    private function parsePredecessors(string $raw): array
    {
        $results = [];

        // Split by comma, but only at top-level (not inside lag specs)
        $parts = preg_split('/,(?=\d)/', $raw);

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') continue;

            // Regex: row_number, optional type (SS|SF|FS|FF), optional lag (+/-N days)
            if (!preg_match('/^(\d+)(SS|SF|FS|FF)?([+-]\d+\s*(?:day|d)?)?$/i', $part, $m)) {
                continue;
            }

            $row     = (int) $m[1];
            $typeRaw = strtoupper($m[2] ?? 'FS');
            $lagRaw  = $m[3] ?? '';

            $type = match($typeRaw) {
                'SS' => 'start_to_start',
                'SF' => 'start_to_finish',
                'FF' => 'finish_to_finish',
                default => 'finish_to_start',
            };

            $lag = 0;
            if ($lagRaw !== '') {
                preg_match('/([+-]?\d+)/', $lagRaw, $lm);
                $lag = (int) ($lm[1] ?? 0);
            }

            $results[] = ['row' => $row, 'type' => $type, 'lag' => $lag];
        }

        return $results;
    }
}
