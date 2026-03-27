<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\HasHashedRouteKey;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrder extends Model
{
    use SoftDeletes, BelongsToCompany, HasHashedRouteKey, LogsActivity;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'wo_number',
        'title',
        'description',
        'work_order_type_id',
        'client_id',
        'asset_id',
        'work_order_request_id',
        'priority',
        'status',
        'assigned_to',
        'due_date',
        'preferred_date',
        'preferred_time',
        'preferred_notes',
        'started_at',
        'completed_at',
        'notes',
        'created_by',
        // Testing & Inspection
        'is_inspection',
        'inspection_type',
        'hold_point',
        'acceptance_criteria',
        'test_result',
        'test_readings',
        'equipment_tested',
        'method_statement_ref',
        // Commissioning
        'is_commissioning',
        'commissioning_phase',
        'system_tag',
        // Road material testing
        'is_road_test',
        'road_test_type',
        'test_chainage',
        'test_layer',
        'sample_reference',
        'test_lab',
        'test_value_achieved',
        'test_value_required',
        'test_unit',
    ];

    protected $casts = [
        'due_date' => 'date',
        'preferred_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_inspection' => 'boolean',
        'is_commissioning' => 'boolean',
        'test_readings' => 'array',
        'is_road_test' => 'boolean',
        'test_value_achieved' => 'decimal:2',
        'test_value_required' => 'decimal:2',
    ];

    public static array $statuses = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'in_progress' => 'In Progress',
        'on_hold' => 'On Hold',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public static array $priorities = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];

    public static array $inspectionTypes = [
        'visual' => 'Visual Inspection',
        'dimensional' => 'Dimensional Check',
        'electrical' => 'Electrical Testing',
        'pressure' => 'Pressure Test',
        'functional' => 'Functional Test',
        'load' => 'Load Test',
        'insulation' => 'Insulation Resistance',
        'continuity' => 'Continuity Test',
        'relay' => 'Relay Protection Test',
    ];

    public static array $holdPoints = [
        'hold' => 'Hold (must witness)',
        'witness' => 'Witness (notified)',
        'review' => 'Review (records only)',
    ];

    public static array $testResults = [
        'pass' => 'Pass',
        'fail' => 'Fail',
        'conditional' => 'Conditional Pass',
        'na' => 'Not Applicable',
    ];

    public static array $commissioningPhases = [
        // ── Overhead Line (OHL) / Energy Line construction stages ──
        'smoking'             => 'Smoking',
        'bald_pt'             => 'Bald / Pt (Excavation)',
        'pole_erection'       => 'Pole (Erection)',
        'pole_dressing'       => 'Pole (Dressing)',
        'pole_stringing'      => 'Pole (Stringing)',
        'transformer_install' => 'Transformer / Switchgear (Installation)',
        'pre_commissioning'   => 'Pre-Commissioning',
        // ── Generic commissioning stages ──
        'mechanical_completion' => 'Mechanical Completion',
        'energization'          => 'Energization',
        'hot_commissioning'     => 'Hot Commissioning',
        'performance_test'      => 'Performance Testing',
    ];

    public static array $roadTestTypes = [
        'cbr' => 'CBR (California Bearing Ratio)',
        'compaction' => 'Compaction (Proctor)',
        'sieve_analysis' => 'Sieve / Grading Analysis',
        'atterberg' => 'Atterberg Limits',
        'asphalt_core' => 'Asphalt Core',
        'marshall' => 'Marshall Stability',
        'deflection' => 'Deflection (Benkelman Beam)',
        'dcp' => 'DCP (Dynamic Cone Penetrometer)',
        'sand_replacement' => 'Sand Replacement (FDD)',
        'plate_bearing' => 'Plate Bearing Test',
        'roughness' => 'Roughness (IRI)',
        'permeability' => 'Permeability Test',
    ];

    public function cdeProject()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }
    public function type()
    {
        return $this->belongsTo(WorkOrderType::class, 'work_order_type_id');
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
    public function request()
    {
        return $this->belongsTo(WorkOrderRequest::class, 'work_order_request_id');
    }
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function items()
    {
        return $this->hasMany(WorkOrderItem::class);
    }
    public function tasks()
    {
        return $this->hasMany(WorkOrderTask::class);
    }
    public function appointments()
    {
        return $this->hasMany(WorkOrderAppointment::class);
    }
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }
}
