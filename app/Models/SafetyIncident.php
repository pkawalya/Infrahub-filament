<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\HasHashedRouteKey;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SafetyIncident extends Model
{
    use BelongsToCompany, HasHashedRouteKey, LogsActivity, SoftDeletes;

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
        // Road / traffic management
        'is_traffic_incident',
        'traffic_control_type',
        'incident_chainage',
        'third_party_involved',
        'road_closure_required',
        'closure_duration_hours',
    ];

    protected $casts = [
        'incident_date' => 'datetime',
        'is_ptw' => 'boolean',
        'ptw_valid_from' => 'datetime',
        'ptw_valid_until' => 'datetime',
        'is_traffic_incident' => 'boolean',
        'third_party_involved' => 'boolean',
        'road_closure_required' => 'boolean',
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

    public static array $trafficControlTypes = [
        'stop_go' => 'Stop/Go Control',
        'flagmen' => 'Flagmen',
        'traffic_lights' => 'Temporary Traffic Lights',
        'full_closure' => 'Full Road Closure',
        'lane_closure' => 'Lane Closure',
        'diversion' => 'Diversion Route',
        'speed_reduction' => 'Speed Reduction Zone',
        'contraflow' => 'Contraflow',
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
