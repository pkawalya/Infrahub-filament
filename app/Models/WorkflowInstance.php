<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;

class WorkflowInstance extends Model
{
    use BelongsToCompany;

    protected $table = 'workflow_instances';

    protected $fillable = [
        'company_id',
        'workflow_template_id',
        'approvable_type',
        'approvable_id',
        'current_step_sequence',
        'status',
        'audit_log',
    ];

    protected $casts = [
        'audit_log' => 'array',
        'current_step_sequence' => 'integer',
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

    public function canUserApprove(?User $user = null): bool
    {
        return app(\App\Services\WorkflowExecutionService::class)->canUserApprove($this, $user);
    }
}
