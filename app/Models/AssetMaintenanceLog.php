<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetMaintenanceLog extends Model
{
    protected $fillable = [
        'asset_id',
        'type',
        'priority',
        'title',
        'description',
        'status',
        'scheduled_date',
        'completed_date',
        'next_service_date',
        'cost',
        'downtime_hours',
        'meter_reading',
        'vendor',
        'parts_used',
        'condition_before',
        'condition_after',
        'notes',
        'performed_by',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed_date' => 'date',
        'next_service_date' => 'date',
        'cost' => 'decimal:2',
        'downtime_hours' => 'decimal:1',
        'meter_reading' => 'decimal:1',
    ];

    public static array $types = [
        'inspection' => 'Inspection',
        'repair' => 'Repair',
        'calibration' => 'Calibration',
        'preventive' => 'Preventive',
        'cleaning' => 'Cleaning',
        'overhaul' => 'Overhaul',
        'replacement' => 'Part Replacement',
    ];

    public static array $priorities = [
        'low' => 'Low',
        'normal' => 'Normal',
        'high' => 'High',
        'critical' => 'Critical',
    ];

    public static array $statuses = [
        'scheduled' => 'Scheduled',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
