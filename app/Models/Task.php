<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Task extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'work_order_id',
        'parent_id',
        'title',
        'description',
        'notes',
        'type',
        'priority',
        'status',
        'start_date',
        'actual_start',
        'due_date',
        'actual_finish',
        'duration_days',
        'completed_at',
        'estimated_hours',
        'actual_hours',
        'progress_percent',
        'attachments',
        'sort_order',
        'wbs_code',
        'outline_level',
        'is_summary',
        'is_milestone',
        'constraint_type',
        'constraint_date',
        'baseline_start',
        'baseline_finish',
        'baseline_duration',
        'baseline_cost',
        'baseline_work',
        'fixed_cost',
        'cost_rate',
        'actual_cost',
        'resource_names',
        'resource_units',
        'calendar',
        'created_by',
        'assigned_to',
        'bcws',
        'bcwp',
        'acwp',
    ];

    protected $casts = [
        'start_date' => 'date',
        'actual_start' => 'date',
        'due_date' => 'date',
        'actual_finish' => 'date',
        'completed_at' => 'datetime',
        'constraint_date' => 'date',
        'baseline_start' => 'date',
        'baseline_finish' => 'date',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
        'fixed_cost' => 'decimal:2',
        'cost_rate' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'baseline_cost' => 'decimal:2',
        'baseline_work' => 'decimal:2',
        'bcws' => 'decimal:2',
        'bcwp' => 'decimal:2',
        'acwp' => 'decimal:2',
        'attachments' => 'array',
        'is_summary' => 'boolean',
        'is_milestone' => 'boolean',
    ];

    public static array $statuses = [
        'to_do' => 'To Do',
        'in_progress' => 'In Progress',
        'review' => 'Review',
        'done' => 'Done',
        'blocked' => 'Blocked',
        'cancelled' => 'Cancelled',
    ];

    public static array $priorities = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];

    public static array $constraintTypes = [
        'asap' => 'As Soon As Possible',
        'alap' => 'As Late As Possible',
        'mso' => 'Must Start On',
        'mfo' => 'Must Finish On',
        'snet' => 'Start No Earlier Than',
        'snlt' => 'Start No Later Than',
        'fnet' => 'Finish No Earlier Than',
        'fnlt' => 'Finish No Later Than',
    ];

    // ─── Relationships ────────────────────────────────────────────

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_id')->orderBy('sort_order');
    }

    public function allDescendants()
    {
        return $this->hasMany(Task::class, 'parent_id')
            ->with('allDescendants');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Tasks that THIS task depends on (predecessors).
     */
    public function predecessorLinks()
    {
        return $this->hasMany(TaskDependency::class, 'task_id');
    }

    /**
     * Tasks that depend on THIS task (successors).
     */
    public function successorLinks()
    {
        return $this->hasMany(TaskDependency::class, 'depends_on_id');
    }

    /**
     * Predecessor task models.
     */
    public function predecessors()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'depends_on_id')
            ->withPivot('dependency_type', 'lag_days');
    }

    /**
     * Successor task models.
     */
    public function successors()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'depends_on_id', 'task_id')
            ->withPivot('dependency_type', 'lag_days');
    }

    // ─── WBS Helpers ──────────────────────────────────────────────

    /**
     * Generate WBS codes for all tasks in a project.
     */
    public static function regenerateWbs(int $projectId): void
    {
        $tasks = static::where('cde_project_id', $projectId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $counters = [];
        foreach ($tasks as $task) {
            $parentId = $task->parent_id;
            $level = 0;

            if ($parentId) {
                $parent = $tasks->firstWhere('id', $parentId);
                $level = $parent ? ($parent->outline_level + 1) : 0;
            }

            // Increment counter at this level
            $key = $parentId ?: 'root';
            if (!isset($counters[$key])) {
                $counters[$key] = 0;
            }
            $counters[$key]++;

            // Build WBS code
            if ($parentId && $parent) {
                $wbs = $parent->wbs_code . '.' . $counters[$key];
            } else {
                $wbs = (string) $counters[$key];
            }

            $task->update([
                'wbs_code' => $wbs,
                'outline_level' => $level,
                'is_summary' => static::where('parent_id', $task->id)
                    ->where('cde_project_id', $projectId)->exists(),
            ]);
        }
    }

    /**
     * Calculate duration from start/due dates (working days).
     */
    public function calculateDuration(): int
    {
        if (!$this->start_date || !$this->due_date) {
            return $this->duration_days ?? 1;
        }

        $start = $this->start_date->copy();
        $end = $this->due_date->copy();
        $workingDays = 0;

        while ($start->lte($end)) {
            if (!$start->isWeekend()) {
                $workingDays++;
            }
            $start->addDay();
        }

        return max(1, $workingDays);
    }

    /**
     * Calculate finish date from start + duration (working days).
     */
    public static function calculateFinishDate($startDate, int $durationDays): \Carbon\Carbon
    {
        $date = \Carbon\Carbon::parse($startDate)->copy();
        $remaining = max(1, $durationDays) - 1; // Duration includes start day

        while ($remaining > 0) {
            $date->addDay();
            if (!$date->isWeekend()) {
                $remaining--;
            }
        }

        return $date;
    }

    /**
     * MS Project Auto-Scheduling Forward Pass
     * Pushes successor tasks forward if this task's dates violate their constraints.
     */
    public function forwardPass(): void
    {
        if (!$this->due_date)
            return;

        $successors = $this->successorLinks()->with('task')->get();

        foreach ($successors as $link) {
            $successor = $link->task;
            if (!$successor || !$successor->start_date)
                continue;

            $pushNeeded = false;
            $newStartDate = \Carbon\Carbon::parse($successor->start_date);

            // Finish-to-Start (FS)
            if ($link->dependency_type === 'finish_to_start') {
                $minStart = \Carbon\Carbon::parse($this->due_date)->addDay();

                // Skip weekends for early start
                while ($minStart->isWeekend()) {
                    $minStart->addDay();
                }

                // Apply Lag days
                if ($link->lag_days > 0) {
                    $lag = $link->lag_days;
                    while ($lag > 0) {
                        $minStart->addDay();
                        if (!$minStart->isWeekend())
                            $lag--;
                    }
                } elseif ($link->lag_days < 0) {
                    $lag = abs($link->lag_days);
                    while ($lag > 0) {
                        $minStart->subDay();
                        if (!$minStart->isWeekend())
                            $lag--;
                    }
                }

                // If the successor is scheduled too early, push it forward
                if ($minStart->gt($newStartDate)) {
                    $newStartDate = $minStart;
                    $pushNeeded = true;
                }
            }

            if ($pushNeeded) {
                $successor->start_date = $newStartDate->format('Y-m-d');
                if ($successor->duration_days > 0) {
                    $successor->due_date = self::calculateFinishDate($successor->start_date, $successor->duration_days)->format('Y-m-d');
                }
                $successor->save();

                // Recursively push further successors downstream
                $successor->forwardPass();
            }
        }
    }

    /**
     * Get predecessor labels string like "3FS, 5FS+2d"
     */
    public function getPredecessorString(): string
    {
        if (!$this->relationLoaded('predecessorLinks')) {
            $this->load('predecessorLinks');
        }

        return $this->predecessorLinks->map(function (TaskDependency $dep) {
            return $dep->getShortLabel();
        })->join(', ');
    }

    /**
     * Check if this task is on the critical path.
     * A simplified check: task is critical if total float is 0.
     */
    public function isCritical(): bool
    {
        // A milestone at the end or task with no float
        if ($this->is_milestone && !$this->successorLinks()->exists()) {
            return true;
        }

        // Tasks with no slack (due_date == latest possible finish)
        return $this->total_float === 0;
    }

    // ─── Baseline ─────────────────────────────────────────────────

    /**
     * Save current schedule as baseline.
     */
    public function saveBaseline(): void
    {
        $this->update([
            'baseline_start' => $this->start_date,
            'baseline_finish' => $this->due_date,
            'baseline_duration' => $this->duration_days ?? $this->calculateDuration(),
            'baseline_cost' => ($this->fixed_cost ?? 0) + (($this->estimated_hours ?? 0) * ($this->cost_rate ?? 0)),
            'baseline_work' => $this->estimated_hours,
        ]);
    }

    /**
     * Get schedule variance in days (negative = behind schedule).
     */
    public function getScheduleVariance(): ?int
    {
        if (!$this->baseline_finish || !$this->due_date) {
            return null;
        }
        return $this->baseline_finish->diffInDays($this->due_date, absolute: false);
    }

    // ─── Summary Roll-up ──────────────────────────────────────────

    /**
     * Recalculate summary task values from children.
     */
    public function rollUpFromChildren(): void
    {
        if (!$this->is_summary)
            return;

        $children = static::where('parent_id', $this->id)->get();
        if ($children->isEmpty())
            return;

        $earliestStart = $children->min('start_date');
        $latestFinish = $children->max('due_date');
        $totalEstHours = $children->sum('estimated_hours');
        $totalActHours = $children->sum('actual_hours');
        $totalCost = $children->sum('fixed_cost') + $children->sum('actual_cost');

        // Weighted average progress by estimated hours
        $weightedProgress = 0;
        $totalWeight = 0;
        foreach ($children as $child) {
            $weight = $child->estimated_hours ?: 1;
            $weightedProgress += ($child->progress_percent ?? 0) * $weight;
            $totalWeight += $weight;
        }
        $avgProgress = $totalWeight > 0 ? round($weightedProgress / $totalWeight) : 0;

        // Derive status from children
        $childStatuses = $children->pluck('status')->unique();
        $allDone = $childStatuses->count() === 1 && $childStatuses->first() === 'done';
        $anyBlocked = $childStatuses->contains('blocked');
        $anyInProgress = $childStatuses->contains('in_progress') || $childStatuses->contains('review');

        $status = $this->status;
        if ($allDone) {
            $status = 'done';
        } elseif ($anyBlocked) {
            $status = 'blocked';
        } elseif ($anyInProgress || $avgProgress > 0) {
            $status = 'in_progress';
        }

        // Actual dates from children
        $actualStart = $children->whereNotNull('actual_start')->min('actual_start');
        $actualFinish = $allDone ? $children->max('actual_finish') : null;

        $updates = [
            'start_date' => $earliestStart,
            'due_date' => $latestFinish,
            'estimated_hours' => $totalEstHours,
            'actual_hours' => $totalActHours,
            'progress_percent' => $avgProgress,
            'actual_cost' => $totalCost,
            'status' => $status,
            'duration_days' => $this->calculateDuration(),
        ];

        if ($actualStart) {
            $updates['actual_start'] = $actualStart;
        }
        if ($allDone && $actualFinish) {
            $updates['actual_finish'] = $actualFinish;
            $updates['completed_at'] = now();
        }

        $this->update($updates);
    }

    // ─── Gantt Data Export ─────────────────────────────────────────

    /**
     * Get all tasks for a project formatted for the Gantt chart.
     */
    public static function getGanttData(int $projectId): array
    {
        $tasks = static::where('cde_project_id', $projectId)
            ->with(['assignee:id,name', 'predecessorLinks'])
            ->withCount(['subtasks', 'successorLinks as successor_count'])
            ->orderBy('sort_order')
            ->orderBy('wbs_code')
            ->orderBy('id')
            ->get();

        $dependencies = TaskDependency::whereIn('task_id', $tasks->pluck('id'))
            ->get();

        return [
            'tasks' => $tasks->map(fn(Task $t) => [
                'id' => $t->id,
                'wbs' => $t->wbs_code ?: '',
                'title' => $t->title,
                'start' => $t->start_date?->format('Y-m-d'),
                'end' => $t->due_date?->format('Y-m-d'),
                'actual_start' => $t->actual_start?->format('Y-m-d'),
                'actual_finish' => $t->actual_finish?->format('Y-m-d'),
                'baseline_start' => $t->baseline_start?->format('Y-m-d'),
                'baseline_finish' => $t->baseline_finish?->format('Y-m-d'),
                'duration' => $t->duration_days ?? $t->calculateDuration(),
                'progress' => $t->progress_percent ?? 0,
                'status' => $t->status,
                'priority' => $t->priority,
                'type' => $t->type,
                'is_summary' => (bool) $t->is_summary,
                'is_milestone' => (bool) $t->is_milestone,
                'outline_level' => $t->outline_level ?? 0,
                'parent_id' => $t->parent_id,
                'assignee' => $t->assignee?->name,
                'assigned_to' => $t->assigned_to,
                'resource_names' => $t->resource_names,
                'estimated_hours' => (float) ($t->estimated_hours ?? 0),
                'actual_hours' => (float) ($t->actual_hours ?? 0),
                'fixed_cost' => (float) ($t->fixed_cost ?? 0),
                'predecessors' => $t->getPredecessorString(),
                'has_children' => ($t->subtasks_count ?? 0) > 0,
                'sort_order' => $t->sort_order,
                'is_critical' => $t->priority === 'urgent'
                    || ($t->due_date && $t->due_date->isPast() && !in_array($t->status, ['done', 'cancelled']))
                    || (($t->successor_count ?? 0) === 0 && !$t->is_summary && !$t->is_milestone),
            ])->values()->toArray(),

            'dependencies' => $dependencies->map(fn(TaskDependency $d) => [
                'from' => $d->depends_on_id,
                'to' => $d->task_id,
                'type' => $d->dependency_type,
                'lag' => $d->lag_days,
            ])->values()->toArray(),
        ];
    }
}
