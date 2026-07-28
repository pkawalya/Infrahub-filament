<?php

namespace App\Models\Concerns;

use App\Models\WorkflowInstance;
use App\Models\WorkflowTemplate;
use App\Services\WorkflowExecutionService;

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

    public function startWorkflow(): ?WorkflowInstance
    {
        if (empty($this->company_id)) {
            return null;
        }

        $type = class_basename($this);

        // Find active template for this company and module type
        $template = WorkflowTemplate::where('company_id', $this->company_id)
            ->where('module_type', $type)
            ->where('is_active', true)
            ->first();

        if ($template) {
            /** @var WorkflowInstance $instance */
            $instance = $this->workflowInstance()->create([
                'company_id' => $this->company_id,
                'workflow_template_id' => $template->id,
                'current_step_sequence' => 1,
                'status' => 'pending',
                'audit_log' => [],
            ]);

            // Adjust status automatically on start
            if (in_array($type, ['Rfi', 'ChangeOrder', 'MaterialRequisition', 'PaymentCertificate', 'Invoice'])) {
                if (method_exists($this, 'updateQuietly')) {
                    $this->updateQuietly(['status' => 'under_review']);
                } else {
                    $this->update(['status' => 'under_review']);
                }
            }

            return $instance;
        }

        return null;
    }

    public function canUserApprove(?\App\Models\User $user = null): bool
    {
        $instance = $this->workflowInstance;
        if (!$instance) {
            return false;
        }

        return app(WorkflowExecutionService::class)->canUserApprove($instance, $user);
    }

    public function advanceWorkflowStep(?\App\Models\User $user = null, ?string $comment = null): bool
    {
        $instance = $this->workflowInstance;
        if (!$instance) {
            return false;
        }

        return app(WorkflowExecutionService::class)->advanceStep($instance, $user, $comment);
    }

    public function rejectWorkflowStep(?\App\Models\User $user = null, ?string $reason = null): bool
    {
        $instance = $this->workflowInstance;
        if (!$instance) {
            return false;
        }

        return app(WorkflowExecutionService::class)->rejectStep($instance, $user, $reason);
    }

    public function getWorkflowTimeline(): array
    {
        $instance = $this->workflowInstance;
        if (!$instance) {
            return [];
        }

        return app(WorkflowExecutionService::class)->getStepTimeline($instance);
    }
}
