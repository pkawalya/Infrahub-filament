<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowStep extends Model
{
    protected $table = 'workflow_steps';

    protected $fillable = [
        'workflow_template_id',
        'step_sequence',
        'name',
        'approver_type',
        'approver_id',
        'approver_role',
        'assigned_user_id',
        'approval_status',
        'rejection_status',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(WorkflowTemplate::class, 'workflow_template_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function canApprove(User $user): bool
    {
        if ($this->assigned_user_id) {
            return $user->id === $this->assigned_user_id;
        }

        if ($this->approver_role) {
            return $user->hasRole($this->approver_role) || $user->user_type === $this->approver_role;
        }

        return $user->isSuperAdmin() || $user->canManageCompany();
    }
}
