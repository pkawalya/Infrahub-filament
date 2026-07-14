<?php

namespace App\Models\Concerns;

use App\Models\WorkflowInstance;
use App\Models\WorkflowTemplate;

trait HasWorkflow
{
    public static function bootHasWorkflow()
    {
        static::created(function ($model) {
            $model->startWorkflow();
        });
    }

    public function workflowInstance()
    {
        return $this->morphOne(WorkflowInstance::class, 'approvable');
    }

    public function startWorkflow()
    {
        $type = class_basename($this);

        // Find active template for this company and module type
        $template = WorkflowTemplate::where('company_id', $this->company_id)
            ->where('module_type', $type)
            ->where('is_active', true)
            ->first();

        if ($template) {
            $this->workflowInstance()->create([
                'workflow_template_id' => $template->id,
                'current_step_sequence' => 1,
                'status' => 'pending',
            ]);

            // Adjust statuses automatically on start
            if ($type === 'Rfi') {
                $this->updateQuietly(['status' => 'under_review']);
            } elseif ($type === 'SafetyIncident') {
                $this->updateQuietly(['status' => 'investigating']);
            }
        }
    }
}
