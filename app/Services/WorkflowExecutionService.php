<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use Illuminate\Support\Facades\DB;

class WorkflowExecutionService
{
    /**
     * Get current active step for a workflow instance.
     */
    public function getCurrentStep(WorkflowInstance $instance): ?WorkflowStep
    {
        if (!$instance->template) {
            return null;
        }

        return $instance->template->steps()
            ->where('step_sequence', $instance->current_step_sequence)
            ->first();
    }

    /**
     * Determine if a user is authorized to approve/reject the current step.
     */
    public function canUserApprove(WorkflowInstance $instance, ?User $user = null): bool
    {
        $user = $user ?? auth()->user();
        if (!$user) {
            return false;
        }

        if ($instance->status !== 'pending') {
            return false;
        }

        $step = $this->getCurrentStep($instance);
        if (!$step) {
            return false;
        }

        // Super admins can always override/approve
        if ($user->isSuperAdmin()) {
            return true;
        }

        $assignedUserId = $step->assigned_user_id ?? ($step->approver_type === 'user' ? (int) $step->approver_id : null);
        $approverRole = $step->approver_role ?? ($step->approver_type === 'role' ? $step->approver_id : null);

        // If step has specific assigned user
        if ($assignedUserId) {
            return $assignedUserId === $user->id;
        }

        // If step has required role/permission
        if (!empty($approverRole)) {
            return $user->hasRole($approverRole) || $user->user_type === $approverRole;
        }

        // If no explicit approver is designated, company admins or managers can approve
        return $user->user_type === 'manager' || $user->canManageCompany();
    }

    /**
     * Advance the workflow instance to the next step or complete approval.
     */
    public function advanceStep(WorkflowInstance $instance, ?User $user = null, ?string $comment = null): bool
    {
        $user = $user ?? auth()->user();

        return DB::transaction(function () use ($instance, $user, $comment) {
            $currentStep = $this->getCurrentStep($instance);
            $totalSteps = $instance->template?->steps()->count() ?? 1;

            $auditData = $instance->audit_log ?? [];
            $auditData[] = [
                'action' => 'approved',
                'step_sequence' => $instance->current_step_sequence,
                'step_name' => $currentStep?->name ?? "Step {$instance->current_step_sequence}",
                'actor_id' => $user?->id,
                'actor_name' => $user?->name ?? 'System',
                'comment' => $comment,
                'timestamp' => now()->toDateTimeString(),
            ];

            if ($instance->current_step_sequence >= $totalSteps) {
                // Workflow Fully Approved
                $instance->update([
                    'status' => 'approved',
                    'audit_log' => $auditData,
                ]);

                // Update approvable model status if supported
                if ($instance->approvable && method_exists($instance->approvable, 'updateQuietly')) {
                    $targetStatus = $currentStep?->approval_status ?? 'approved';
                    $instance->approvable->update(['status' => $targetStatus]);
                }
            } else {
                // Move to next step
                $nextSequence = $instance->current_step_sequence + 1;
                $instance->update([
                    'current_step_sequence' => $nextSequence,
                    'status' => 'pending',
                    'audit_log' => $auditData,
                ]);

                if ($instance->approvable && method_exists($instance->approvable, 'updateQuietly')) {
                    $instance->approvable->update(['status' => 'under_review']);
                }

                // Notify next step approvers
                $this->notifyNextApprovers($instance, $nextSequence);
            }

            return true;
        });
    }

    /**
     * Reject the workflow instance.
     */
    public function rejectStep(WorkflowInstance $instance, ?User $user = null, ?string $reason = null): bool
    {
        $user = $user ?? auth()->user();

        return DB::transaction(function () use ($instance, $user, $reason) {
            $currentStep = $this->getCurrentStep($instance);

            $auditData = $instance->audit_log ?? [];
            $auditData[] = [
                'action' => 'rejected',
                'step_sequence' => $instance->current_step_sequence,
                'step_name' => $currentStep?->name ?? "Step {$instance->current_step_sequence}",
                'actor_id' => $user?->id,
                'actor_name' => $user?->name ?? 'System',
                'comment' => $reason,
                'timestamp' => now()->toDateTimeString(),
            ];

            $instance->update([
                'status' => 'rejected',
                'audit_log' => $auditData,
            ]);

            if ($instance->approvable) {
                $targetStatus = $currentStep?->rejection_status ?? 'rejected';
                $instance->approvable->update(['status' => $targetStatus]);
            }

            return true;
        });
    }

    /**
     * Build step timeline data array for UI display.
     */
    public function getStepTimeline(WorkflowInstance $instance): array
    {
        if (!$instance->template) {
            return [];
        }

        $steps = $instance->template->steps()->orderBy('step_sequence')->get();
        $auditLogs = collect($instance->audit_log ?? []);

        return $steps->map(function (WorkflowStep $step) use ($instance, $auditLogs) {
            $log = $auditLogs->firstWhere('step_sequence', $step->step_sequence);
            
            $status = 'pending';
            if ($log) {
                $status = $log['action'];
            } elseif ($step->step_sequence < $instance->current_step_sequence) {
                $status = 'approved';
            } elseif ($step->step_sequence === $instance->current_step_sequence) {
                $status = $instance->status === 'rejected' ? 'rejected' : 'active';
            }

            return [
                'sequence' => $step->step_sequence,
                'name' => $step->name,
                'approver' => $step->assignedUser?->name ?? ($step->approver_role ? ucfirst($step->approver_role) : 'Manager'),
                'status' => $status,
                'actor_name' => $log['actor_name'] ?? null,
                'comment' => $log['comment'] ?? null,
                'timestamp' => $log['timestamp'] ?? null,
            ];
        })->toArray();
    }

    /**
     * Send notification to approvers of the next step.
     */
    protected function notifyNextApprovers(WorkflowInstance $instance, int $nextSequence): void
    {
        $nextStep = $instance->template?->steps()->where('step_sequence', $nextSequence)->first();
        if (!$nextStep) return;

        $query = User::where('company_id', $instance->company_id)->where('is_active', true);

        if ($nextStep->assigned_user_id) {
            $query->where('id', $nextStep->assigned_user_id);
        } elseif ($nextStep->approver_role) {
            $query->where(function ($q) use ($nextStep) {
                $q->where('user_type', $nextStep->approver_role)
                  ->orWhere('user_type', 'company_admin');
            });
        }

        $recipients = $query->get();
        foreach ($recipients as $recipient) {
            Notification::create([
                'id'              => (string) \Illuminate\Support\Str::uuid(),
                'notifiable_type' => User::class,
                'notifiable_id'   => $recipient->id,
                'type'            => 'App\\Notifications\\WorkflowApprovalRequired',
                'data'            => [
                    'title'       => "Approval Required: Step {$nextSequence}",
                    'message'     => "An item requires your review for step \"{$nextStep->name}\".",
                    'instance_id' => $instance->id,
                    'module'      => $instance->approvable_type,
                    'approvable_id' => $instance->approvable_id,
                ],
            ]);
        }
    }
}
