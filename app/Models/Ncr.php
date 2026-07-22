<?php

namespace App\Models;

use App\Models\CdeActivityLog;
use App\Models\Concerns\BelongsToCompany;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ncr extends Model
{
    use SoftDeletes, BelongsToCompany, LogsActivity;

    protected $fillable = [
        'company_id',
        'cde_project_id',
        'cde_document_id',
        'ncr_number',
        'title',
        'description',
        'type',
        'severity',
        'status',
        'root_cause',
        'corrective_action',
        'preventive_action',
        'verification_notes',
        'closure_notes',
        'reported_by',
        'assigned_to',
        'verified_by',
        'due_date',
        'verified_at',
        'closed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'verified_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public static array $statuses = [
        'open' => 'Open',
        'investigating' => 'Investigating',
        'corrective_action' => 'Corrective Action',
        'verified' => 'Verified',
        'closed' => 'Closed',
    ];

    public static array $types = [
        'product' => 'Product',
        'process' => 'Process',
        'system' => 'System',
        'documentation' => 'Documentation',
    ];

    public static array $severities = [
        'minor' => 'Minor',
        'major' => 'Major',
        'critical' => 'Critical',
    ];

    public static array $validTransitions = [
        'open' => ['investigating'],
        'investigating' => ['corrective_action', 'open'],
        'corrective_action' => ['verified', 'investigating'],
        'verified' => ['closed', 'corrective_action'],
        'closed' => [],
    ];

    public function project()
    {
        return $this->belongsTo(CdeProject::class, 'cde_project_id');
    }

    public function document()
    {
        return $this->belongsTo(CdeDocument::class, 'cde_document_id');
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

    public function canTransitionTo(string $newStatus, bool $checkGuards = false): bool
    {
        if ($this->status === $newStatus) {
            return true;
        }

        $allowed = static::$validTransitions[$this->status] ?? [];

        if (!in_array($newStatus, $allowed, true)) {
            return false;
        }

        if (!$checkGuards) {
            return true;
        }

        return $this->passesTransitionGuards($newStatus);
    }

    public function transitionTo(string $newStatus): bool
    {
        if (!$this->canTransitionTo($newStatus, checkGuards: true)) {
            $reason = $this->guardFailureReason($newStatus);
            if ($reason) {
                throw new \InvalidArgumentException(
                    "Cannot transition NCR '{$this->ncr_number}' from '{$this->status}' to '{$newStatus}': {$reason}"
                );
            }
            $allowed = static::$validTransitions[$this->status] ?? [];
            throw new \InvalidArgumentException(
                "Cannot transition NCR '{$this->ncr_number}' from '{$this->status}' to '{$newStatus}'. " .
                "Allowed transitions: " . implode(', ', $allowed)
            );
        }

        $fromStatus = $this->status;
        $result = $this->update(['status' => $newStatus]);

        if ($result) {
            CdeActivityLog::record(
                $this,
                'status_changed',
                "NCR '{$this->ncr_number}' status changed from '{$fromStatus}' to '{$newStatus}'",
                ['from' => $fromStatus, 'to' => $newStatus],
            );
        }

        return $result;
    }

    protected function passesTransitionGuards(string $newStatus): bool
    {
        return match ($newStatus) {
            'corrective_action' => filled($this->root_cause),
            'verified' => filled($this->corrective_action),
            'closed' => filled($this->verification_notes),
            default => true,
        };
    }

    protected function guardFailureReason(string $newStatus): ?string
    {
        return match ($newStatus) {
            'corrective_action' => 'Root cause must be documented before proposing corrective action.',
            'verified' => 'Corrective action must be documented before verification.',
            'closed' => 'Verification notes must be documented before closure.',
            default => null,
        };
    }
}
