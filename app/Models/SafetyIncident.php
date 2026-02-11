<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class SafetyIncident extends Model
{
    use BelongsToCompany;

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
        'reported_by',
        'investigated_by',
    ];

    protected $casts = ['incident_date' => 'datetime'];

    public static array $statuses = [
        'reported' => 'Reported',
        'investigating' => 'Investigating',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
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
}
