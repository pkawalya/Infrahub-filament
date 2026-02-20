<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'type',
        'priority',
        'status',
        'start_date',
        'due_date',
        'completed_at',
        'estimated_hours',
        'actual_hours',
        'progress_percent',
        'sort_order',
        'created_by',
        'assigned_to',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
    ];

    public static array $statuses = [
        'to_do' => 'To Do',
        'in_progress' => 'In Progress',
        'review' => 'Review',
        'done' => 'Done',
        'blocked' => 'Blocked',
    ];

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
        return $this->hasMany(Task::class, 'parent_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
