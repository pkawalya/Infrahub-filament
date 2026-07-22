<?php

namespace App\Models;

use App\Models\CdeActivityLog;
use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\HasHashedRouteKey;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChangeOrder extends Model
{
    use SoftDeletes, BelongsToCompany, HasHashedRouteKey, LogsActivity;

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

    public static array $validTransitions = [
        'draft' => ['submitted'],
        'submitted' => ['under_review', 'draft'],
        'under_review' => ['approved', 'rejected'],
        'approved' => ['implemented', 'under_review'],
        'rejected' => ['draft'],
        'implemented' => [],
    ];

    public function canTransitionTo(string $newStatus): bool
    {
        if ($this->status === $newStatus) {
            return true;
        }

        $allowed = static::$validTransitions[$this->status] ?? [];

        return in_array($newStatus, $allowed, true);
    }

    public function transitionTo(string $newStatus): bool
    {
        if (!$this->canTransitionTo($newStatus)) {
            $allowed = static::$validTransitions[$this->status] ?? [];
            throw new \InvalidArgumentException(
                "Cannot transition change order '{$this->co_number}' from '{$this->status}' to '{$newStatus}'. " .
                "Allowed transitions: " . implode(', ', $allowed)
            );
        }

        $fromStatus = $this->status;
        $result = $this->update(['status' => $newStatus]);

        if ($result) {
            CdeActivityLog::record(
                $this,
                'status_changed',
                "Change order '{$this->co_number}' status changed from '{$fromStatus}' to '{$newStatus}'",
                ['from' => $fromStatus, 'to' => $newStatus],
            );
        }

        return $result;
    }

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
