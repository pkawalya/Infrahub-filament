<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class SnagItem extends Model
{
    use BelongsToCompany, LogsActivity;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'snag_number',
        'title',
        'description',
        'category',
        'severity',
        'status',
        'location',
        'trade',
        'due_date',
        'reported_by',
        'assigned_to',
        'resolved_at',
        // Commissioning punch list
        'punch_category',
        'commissioning_system',
        'discipline',
        'photos',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'resolved_at' => 'datetime',
        'verified_at' => 'datetime',
        'photos' => 'array',
    ];

    public static array $severities = [
        'minor' => 'Minor',
        'major' => 'Major',
        'critical' => 'Critical',
    ];

    public static array $statuses = [
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'verified' => 'Verified',
        'closed' => 'Closed',
    ];

    public static array $punchCategories = [
        'A' => 'A — Must complete before handover',
        'B' => 'B — Can complete after handover',
        'C' => 'C — Cosmetic / minor',
    ];

    public static array $disciplines = [
        'mechanical' => 'Mechanical',
        'electrical' => 'Electrical',
        'civil' => 'Civil',
        'instrumentation' => 'Instrumentation & Control',
        'piping' => 'Piping',
        'hvac' => 'HVAC',
        'structural' => 'Structural Steel',
        'telecom' => 'Telecommunications',
    ];

    public static array $categories = [
        'structural' => 'Structural',
        'finishing' => 'Finishing',
        'mep' => 'MEP',
        'electrical' => 'Electrical',
        'plumbing' => 'Plumbing',
        'painting' => 'Painting',
        'waterproofing' => 'Waterproofing',
        'landscaping' => 'Landscaping',
        // Energy-specific
        'solar_panels' => 'Solar Panels',
        'inverters' => 'Inverters',
        'transformers' => 'Transformers',
        'switchgear' => 'Switchgear',
        'cabling' => 'Cabling & Termination',
        'earthing' => 'Earthing & Lightning',
        'scada' => 'SCADA / Monitoring',
        'turbine' => 'Turbine',
        'generator' => 'Generator',
        'other' => 'Other',
    ];

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
