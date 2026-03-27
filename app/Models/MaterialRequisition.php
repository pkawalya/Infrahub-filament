<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialRequisition extends Model
{
    protected $fillable = [
        'company_id',
        'cde_project_id',
        'warehouse_id',
        'requisition_number',
        'requester_id',
        'status',
        'priority',
        'required_date',
        'purpose',
        'notes',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'approval_level',
        'level1_approved_by',
        'level1_approved_at',
        'level2_approved_by',
        'level2_approved_at',
        'level2_rejection_reason',
    ];

    protected $casts = [
        'required_date'      => 'date',
        'approved_at'        => 'datetime',
        'level1_approved_at' => 'datetime',
        'level2_approved_at' => 'datetime',
    ];

    public static array $statuses = [
        'pending'          => 'Pending',
        'level1_approved'  => 'L1 Approved',
        'approved'         => 'Fully Approved',
        'rejected'         => 'Rejected',
        'partially_issued' => 'Partially Issued',
        'issued'           => 'Issued',
    ];

    public function cdeProject()
    {
        return $this->belongsTo(CdeProject::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(MaterialRequisitionItem::class);
    }

    public function issuances()
    {
        return $this->hasMany(MaterialIssuance::class);
    }
    public function level1Approver()
    {
        return $this->belongsTo(User::class, 'level1_approved_by');
    }
    public function level2Approver()
    {
        return $this->belongsTo(User::class, 'level2_approved_by');
    }

    // ─── Approval helpers ───────────────────────────────────
    public function needsLevel1Approval(): bool
    {
        return $this->status === 'pending' && is_null($this->level1_approved_at);
    }
    public function needsLevel2Approval(): bool
    {
        return $this->approval_level >= 2
            && $this->status === 'level1_approved'
            && is_null($this->level2_approved_at);
    }
    public function isFullyApproved(): bool
    {
        if ($this->approval_level >= 2) {
            return $this->status === 'approved';
        }
        return in_array($this->status, ['approved', 'partially_issued', 'issued']);
    }
}
