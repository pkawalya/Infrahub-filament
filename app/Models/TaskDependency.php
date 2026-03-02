<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskDependency extends Model
{
    protected $fillable = [
        'task_id',
        'depends_on_id',
        'dependency_type',
        'lag_days',
    ];

    /**
     * Dependency types matching MS Project terminology.
     */
    public static array $types = [
        'finish_to_start' => 'Finish-to-Start (FS)',
        'start_to_start' => 'Start-to-Start (SS)',
        'finish_to_finish' => 'Finish-to-Finish (FF)',
        'start_to_finish' => 'Start-to-Finish (SF)',
    ];

    public static array $typeShort = [
        'finish_to_start' => 'FS',
        'start_to_start' => 'SS',
        'finish_to_finish' => 'FF',
        'start_to_finish' => 'SF',
    ];

    /**
     * The task that has this dependency (the successor).
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * The task that must be completed first (the predecessor).
     */
    public function predecessor()
    {
        return $this->belongsTo(Task::class, 'depends_on_id');
    }

    /**
     * Get short label like "3FS+2d"
     */
    public function getShortLabel(): string
    {
        $short = self::$typeShort[$this->dependency_type] ?? 'FS';
        $lag = $this->lag_days;
        $lagStr = $lag > 0 ? "+{$lag}d" : ($lag < 0 ? "{$lag}d" : '');
        return $this->depends_on_id . $short . $lagStr;
    }
}
