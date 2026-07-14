<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowStep extends Model
{
    protected $table = 'workflow_steps';

    protected $fillable = [
        'workflow_template_id',
        'step_sequence',
        'name',
        'approver_type',
        'approver_id',
    ];

    public function template()
    {
        return $this->belongsTo(WorkflowTemplate::class, 'workflow_template_id');
    }

    public function canApprove(\App\Models\User $user): bool
    {
        if ($this->approver_type === 'user') {
            return $user->id === (int) $this->approver_id;
        }

        if ($this->approver_type === 'role') {
            return $user->hasRole($this->approver_id);
        }

        return false;
    }
}
