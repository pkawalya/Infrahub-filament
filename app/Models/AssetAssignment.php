<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetAssignment extends Model
{
    protected $fillable = [
        'asset_id',
        'action',
        'assigned_to',
        'assigned_to_name',
        'assigned_from',
        'location',
        'project_id',
        'condition_before',
        'condition_after',
        'checkout_date',
        'expected_return_date',
        'return_date',
        'notes',
        'performed_by',
    ];

    protected $casts = [
        'checkout_date' => 'date',
        'expected_return_date' => 'date',
        'return_date' => 'date',
    ];

    public static array $actions = [
        'checkout' => 'Checked Out',
        'checkin' => 'Checked In',
        'transfer' => 'Transferred',
        'maintenance' => 'Sent to Maintenance',
        'retire' => 'Retired',
        'lost' => 'Reported Lost',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    public function assignedFrom()
    {
        return $this->belongsTo(User::class, 'assigned_from');
    }
    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'project_id');
    }
}
