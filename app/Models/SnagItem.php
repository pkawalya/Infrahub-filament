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
        // Road defects
        'chainage',
        'road_side',
        'defect_length_m',
        'defect_width_m',
        'defect_depth_mm',
    ];

    protected $casts = [
        'due_date' => 'date',
        'resolved_at' => 'datetime',
        'verified_at' => 'datetime',
        'photos' => 'array',
        'defect_length_m' => 'decimal:2',
        'defect_width_m' => 'decimal:2',
        'defect_depth_mm' => 'decimal:1',
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
        // Road-specific
        'pothole' => 'Pothole',
        'cracking' => 'Cracking (Fatigue / Block / Longitudinal)',
        'rutting' => 'Rutting',
        'ravelling' => 'Ravelling / Stripping',
        'edge_break' => 'Edge Break / Edge Drop',
        'bleeding' => 'Bleeding / Flushing',
        'corrugation' => 'Corrugation / Shoving',
        'depression' => 'Depression / Settlement',
        'drainage_defect' => 'Drainage Defect',
        'kerb_channel' => 'Kerb & Channel Defect',
        'guardrail' => 'Guardrail / Barrier Defect',
        'signage' => 'Road Sign Defect',
        'road_marking' => 'Road Marking Defect',
        'shoulder_defect' => 'Shoulder Defect',
        'culvert' => 'Culvert / Cross Drain Defect',
        'slope_failure' => 'Slope Failure / Erosion',
        'other' => 'Other',
    ];

    public static array $roadSides = [
        'lhs' => 'Left Hand Side',
        'rhs' => 'Right Hand Side',
        'cl' => 'Centre Line',
        'full_width' => 'Full Width',
        'median' => 'Median',
        'shoulder_l' => 'Left Shoulder',
        'shoulder_r' => 'Right Shoulder',
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
