<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'schedule_id',
        'name',
        'description',
        'target_date',
        'actual_date',
        'status',
        'priority',
    ];

    protected $casts = [
        'target_date' => 'date',
        'actual_date' => 'date',
    ];

    public static array $statuses = [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'delayed' => 'Delayed',
        'cancelled' => 'Cancelled',
    ];

    public static array $priorities = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'critical' => 'Critical',
    ];

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'completed'
            && $this->target_date
            && $this->target_date->isPast();
    }
}
