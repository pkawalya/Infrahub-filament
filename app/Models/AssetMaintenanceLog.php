<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetMaintenanceLog extends Model
{
    protected $fillable = [
        'asset_id',
        'type',
        'title',
        'description',
        'status',
        'scheduled_date',
        'completed_date',
        'cost',
        'vendor',
        'condition_before',
        'condition_after',
        'notes',
        'performed_by',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public static array $types = [
        'inspection' => 'Inspection',
        'repair' => 'Repair',
        'calibration' => 'Calibration',
        'preventive' => 'Preventive',
        'cleaning' => 'Cleaning',
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
