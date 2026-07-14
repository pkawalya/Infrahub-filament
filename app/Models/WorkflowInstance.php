<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowInstance extends Model
{
    protected $table = 'workflow_instances';

    protected $fillable = [
        'workflow_template_id',
        'approvable_type',
        'approvable_id',
        'current_step_sequence',
        'status',
    ];

    public function template()
    {
        return $this->belongsTo(WorkflowTemplate::class, 'workflow_template_id');
    }

    public function approvable()
    {
        return $this->morphTo();
    }

    public function logs()
    {
        return $this->hasMany(WorkflowLog::class, 'workflow_instance_id');
    }

    public function currentStep()
    {
        return $this->template?->steps()
            ->where('step_sequence', $this->current_step_sequence)
            ->first();
    }

    public function canUserApprove(\App\Models\User $user): bool
    {
        $step = $this->currentStep();
        if (!$step) {
            return false;
        }

        return $step->canApprove($user);
    }
}
