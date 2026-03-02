<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailySiteLogTask extends Model
{
    protected $fillable = [
        'daily_site_log_id',
        'task_id',
        'progress_today',
        'cumulative_progress',
        'hours_worked',
        'workers_assigned',
        'status_update',
        'remarks',
    ];

    protected $casts = [
        'progress_today' => 'integer',
        'cumulative_progress' => 'integer',
        'hours_worked' => 'decimal:2',
        'workers_assigned' => 'integer',
    ];

    public static array $statusOptions = [
        'not_started' => 'Not Started',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'blocked' => 'Blocked',
    ];

    public function siteLog()
    {
        return $this->belongsTo(DailySiteLog::class, 'daily_site_log_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
