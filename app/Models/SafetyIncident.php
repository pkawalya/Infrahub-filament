<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class SafetyIncident extends Model
{
    use BelongsToCompany, LogsActivity;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'incident_number',
        'title',
        'description',
        'type',
        'severity',
        'status',
        'location',
        'incident_date',
        'root_cause',
        'corrective_action',
        'preventive_action',
        'reported_by',
        'investigated_by',
        // Permit to Work
        'is_ptw',
        'ptw_number',
        'ptw_type',
        'isolation_method',
        'isolation_points',
        'ptw_issuer_id',
        'ptw_receiver_id',
        'ptw_valid_from',
        'ptw_valid_until',
        'ptw_status',
        'ptw_conditions',
        'ppe_requirements',
    ];

    protected $casts = [
        'incident_date' => 'datetime',
        'is_ptw' => 'boolean',
        'ptw_valid_from' => 'datetime',
        'ptw_valid_until' => 'datetime',
    ];

    public static array $statuses = [
        'reported' => 'Reported',
        'investigating' => 'Investigating',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ];

    public static array $ptwTypes = [
        'hot_work' => 'Hot Work',
        'electrical_isolation' => 'Electrical Isolation',
        'confined_space' => 'Confined Space',
        'height' => 'Working at Height',
        'excavation' => 'Excavation',
        'chemical' => 'Chemical Handling',
        'radiation' => 'Radiation',
        'general' => 'General',
    ];

    public static array $ptwStatuses = [
        'active' => 'Active',
        'extended' => 'Extended',
        'closed' => 'Closed',
        'cancelled' => 'Cancelled',
    ];

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
    public function investigator()
    {
        return $this->belongsTo(User::class, 'investigated_by');
    }
    public function ptwIssuer()
    {
        return $this->belongsTo(User::class, 'ptw_issuer_id');
    }
    public function ptwReceiver()
    {
        return $this->belongsTo(User::class, 'ptw_receiver_id');
    }
}
