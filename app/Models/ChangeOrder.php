<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\HasHashedRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChangeOrder extends Model
{
    use SoftDeletes, BelongsToCompany, HasHashedRouteKey;

    protected $fillable = [
        'company_id',
        'contract_id',
        'co_number',
        'title',
        'description',
        'status',
        'amount',
        'time_extension_days',
        'requested_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
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

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
