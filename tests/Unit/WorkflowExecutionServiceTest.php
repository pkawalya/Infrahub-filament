<?php

use App\Models\Company;
use App\Models\PaymentCertificate;
use App\Models\User;
use App\Models\WorkflowInstance;
use App\Models\WorkflowStep;
use App\Models\WorkflowTemplate;
use App\Services\WorkflowExecutionService;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->company = Company::create([
        'name' => 'Service Test Co',
        'slug' => 'service-test-co',
        'is_active' => true,
    ]);

    $this->admin = User::create([
        'company_id' => $this->company->id,
        'name' => 'Workflow Admin',
        'email' => 'admin@servicetest.com',
        'password' => bcrypt('password'),
        'user_type' => 'company_admin',
        'is_active' => true,
    ]);

    $this->manager = User::create([
        'company_id' => $this->company->id,
        'name' => 'Workflow Manager',
        'email' => 'manager@servicetest.com',
        'password' => bcrypt('password'),
        'user_type' => 'manager',
        'is_active' => true,
    ]);

    $this->template = WorkflowTemplate::create([
        'company_id' => $this->company->id,
        'module_type' => 'PaymentCertificate',
        'name' => 'Payment Cert Approval Workflow',
        'is_active' => true,
    ]);

    $this->step1 = WorkflowStep::create([
        'workflow_template_id' => $this->template->id,
        'step_sequence' => 1,
        'name' => 'Reviewer Approval',
        'approver_type' => 'role',
        'approver_id' => 'manager',
        'approver_role' => 'manager',
        'approval_status' => 'under_review',
        'rejection_status' => 'rejected',
    ]);

    $this->step2 = WorkflowStep::create([
        'workflow_template_id' => $this->template->id,
        'step_sequence' => 2,
        'name' => 'Final Sign-off',
        'approver_type' => 'role',
        'approver_id' => 'company_admin',
        'approver_role' => 'company_admin',
        'approval_status' => 'approved',
        'rejection_status' => 'rejected',
    ]);

    $this->service = new WorkflowExecutionService();
});

test('service returns correct current step', function () {
    $instance = WorkflowInstance::create([
        'company_id' => $this->company->id,
        'workflow_template_id' => $this->template->id,
        'approvable_type' => PaymentCertificate::class,
        'approvable_id' => 1,
        'current_step_sequence' => 1,
        'status' => 'pending',
    ]);

    $currentStep = $this->service->getCurrentStep($instance);
    expect($currentStep)->not->toBeNull();
    expect($currentStep->id)->toBe($this->step1->id);
});

test('service advances step and logs audit details', function () {
    $cert = PaymentCertificate::create([
        'company_id' => $this->company->id,
        'cde_project_id' => 1,
        'certificate_number' => 'CERT-999',
        'period_from' => '2026-07-01',
        'period_to' => '2026-07-31',
        'status' => 'draft',
        'total_claimed' => 50000,
        'net_payable' => 45000,
    ]);

    $instance = WorkflowInstance::create([
        'company_id' => $this->company->id,
        'workflow_template_id' => $this->template->id,
        'approvable_type' => PaymentCertificate::class,
        'approvable_id' => $cert->id,
        'current_step_sequence' => 1,
        'status' => 'pending',
    ]);

    $this->service->advanceStep($instance, $this->manager, 'Approved initial review');

    $instance->refresh();
    expect($instance->current_step_sequence)->toBe(2);
    expect($instance->status)->toBe('pending');
    expect($instance->audit_log)->toHaveCount(1);
    expect($instance->audit_log[0]['comment'])->toBe('Approved initial review');
});

test('service completes workflow on final approval', function () {
    $cert = PaymentCertificate::create([
        'company_id' => $this->company->id,
        'cde_project_id' => 1,
        'certificate_number' => 'CERT-1000',
        'period_from' => '2026-07-01',
        'period_to' => '2026-07-31',
        'status' => 'under_review',
        'total_claimed' => 100000,
        'net_payable' => 95000,
    ]);

    $instance = WorkflowInstance::create([
        'company_id' => $this->company->id,
        'workflow_template_id' => $this->template->id,
        'approvable_type' => PaymentCertificate::class,
        'approvable_id' => $cert->id,
        'current_step_sequence' => 2,
        'status' => 'pending',
    ]);

    $this->service->advanceStep($instance, $this->admin, 'Final approval confirmed');

    $instance->refresh();
    expect($instance->status)->toBe('approved');
    expect($cert->fresh()->status)->toBe('approved');
});

test('service rejects workflow step and updates target status', function () {
    $cert = PaymentCertificate::create([
        'company_id' => $this->company->id,
        'cde_project_id' => 1,
        'certificate_number' => 'CERT-1001',
        'period_from' => '2026-07-01',
        'period_to' => '2026-07-31',
        'status' => 'under_review',
        'total_claimed' => 120000,
        'net_payable' => 110000,
    ]);

    $instance = WorkflowInstance::create([
        'company_id' => $this->company->id,
        'workflow_template_id' => $this->template->id,
        'approvable_type' => PaymentCertificate::class,
        'approvable_id' => $cert->id,
        'current_step_sequence' => 1,
        'status' => 'pending',
    ]);

    $this->service->rejectStep($instance, $this->manager, 'Budget limit exceeded');

    $instance->refresh();
    expect($instance->status)->toBe('rejected');
    expect($cert->fresh()->status)->toBe('rejected');
    expect($instance->audit_log[0]['action'])->toBe('rejected');
});
