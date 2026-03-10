<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChangeOrder extends Model
{
    use SoftDeletes, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'contract_id',
        'reference',
        'title',
        'description',
        'reason',
        'type',
        'status',
        'priority',
        'initiated_by',
        'estimated_cost',
        'approved_cost',
        'cost_impact',
        'time_impact_days',
        'submitted_date',
        'approved_date',
        'implementation_date',
        'submitted_by',
        'reviewed_by',
        'approved_by',
        'approval_notes',
        'rejection_reason',
        'affected_boq_items',
        'affected_tasks',
        'attachments',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'approved_cost' => 'decimal:2',
        'cost_impact' => 'decimal:2',
        'submitted_date' => 'date',
        'approved_date' => 'date',
        'implementation_date' => 'date',
        'affected_boq_items' => 'array',
        'affected_tasks' => 'array',
        'attachments' => 'array',
    ];

    public static array $types = [
        'addition' => 'Addition',
        'omission' => 'Omission',
        'time_extension' => 'Time Extension',
        'scope_change' => 'Scope Change',
    ];

    public static array $statuses = [
        'draft' => 'Draft',
        'submitted' => 'Submitted',
        'under_review' => 'Under Review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'implemented' => 'Implemented',
    ];

    public static array $priorities = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'critical' => 'Critical',
    ];

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }
    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
