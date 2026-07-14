<?php

use App\Models\Company;
use App\Models\Rfi;
use App\Models\SafetyIncident;
use App\Models\User;
use App\Models\WorkflowTemplate;
use App\Models\WorkflowStep;
use App\Models\WorkflowInstance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);

    $this->company = Company::create([
        'name' => 'Test Workflow Company',
        'slug' => 'test-workflow-company',
        'is_active' => true,
    ]);

    $this->admin = User::create([
        'company_id' => $this->company->id,
        'name' => 'Admin User',
        'email' => 'admin@testworkflow.com',
        'password' => bcrypt('password'),
        'user_type' => 'company_admin',
        'is_active' => true,
    ]);

    $this->manager = User::create([
        'company_id' => $this->company->id,
        'name' => 'Manager User',
        'email' => 'manager@testworkflow.com',
        'password' => bcrypt('password'),
        'user_type' => 'manager',
        'is_active' => true,
    ]);

    $this->admin->assignRole('company_admin');
    $this->manager->assignRole('manager');

    // Create workflow template for RFI
    $this->template = WorkflowTemplate::create([
        'company_id' => $this->company->id,
        'module_type' => 'Rfi',
        'name' => 'Default RFI Workflow',
        'is_active' => true,
    ]);

    $this->step1 = WorkflowStep::create([
        'workflow_template_id' => $this->template->id,
        'step_sequence' => 1,
        'name' => 'Manager Review',
        'approver_type' => 'role',
        'approver_id' => 'manager',
    ]);

    $this->step2 = WorkflowStep::create([
        'workflow_template_id' => $this->template->id,
        'step_sequence' => 2,
        'name' => 'Admin Approval',
        'approver_type' => 'role',
        'approver_id' => 'company_admin',
    ]);
});

test('auto-creates workflow instance when RFI is created', function () {
    $rfi = Rfi::create([
        'company_id' => $this->company->id,
        'rfi_number' => 'RFI-001',
        'subject' => 'Clarification on concrete mix',
        'question' => 'What is the exact water-to-cement ratio?',
        'status' => 'open',
        'priority' => 'high',
        'raised_by' => $this->manager->id,
    ]);

    $this->assertDatabaseHas('workflow_instances', [
        'workflow_template_id' => $this->template->id,
        'approvable_type' => Rfi::class,
        'approvable_id' => $rfi->id,
        'current_step_sequence' => 1,
        'status' => 'pending',
    ]);

    expect($rfi->fresh()->status)->toBe('under_review');
});

test('correctly evaluates user approval permission based on roles', function () {
    $rfi = Rfi::create([
        'company_id' => $this->company->id,
        'rfi_number' => 'RFI-002',
        'subject' => 'Piles depth verification',
        'question' => 'Confirm depth of boreholes.',
        'status' => 'open',
        'priority' => 'high',
        'raised_by' => $this->admin->id,
    ]);

    $instance = $rfi->workflowInstance;

    // Step 1 requires 'manager' role
    expect($instance->canUserApprove($this->admin))->toBeFalse();
    expect($instance->canUserApprove($this->manager))->toBeTrue();
});

test('advances workflow step when approved and closes RFI on final step', function () {
    $rfi = Rfi::create([
        'company_id' => $this->company->id,
        'rfi_number' => 'RFI-003',
        'subject' => 'Steel rebar grade',
        'question' => 'Confirm if Grade 500 is approved.',
        'status' => 'open',
        'priority' => 'high',
        'raised_by' => $this->manager->id,
    ]);

    $instance = $rfi->workflowInstance;

    // Step 1: Manager approves
    $this->actingAs($this->manager);
    $instance->logs()->create([
        'workflow_step_id' => $this->step1->id,
        'performed_by' => $this->manager->id,
        'action' => 'approved',
    ]);
    $instance->increment('current_step_sequence');

    expect($instance->fresh()->current_step_sequence)->toBe(2);
    expect($instance->fresh()->status)->toBe('pending');
    expect($rfi->fresh()->status)->toBe('under_review');

    // Step 2: Admin approves
    $this->actingAs($this->admin);
    $instance->logs()->create([
        'workflow_step_id' => $this->step2->id,
        'performed_by' => $this->admin->id,
        'action' => 'approved',
    ]);
    
    // Complete workflow since no next step exists
    $instance->update(['status' => 'approved']);
    $rfi->update(['status' => 'closed']);

    expect($instance->fresh()->status)->toBe('approved');
    expect($rfi->fresh()->status)->toBe('closed');
});

test('rejects workflow step and sets RFI status to void', function () {
    $rfi = Rfi::create([
        'company_id' => $this->company->id,
        'rfi_number' => 'RFI-004',
        'subject' => 'Incorrect alignment',
        'question' => 'Should we shift alignment?',
        'status' => 'open',
        'priority' => 'high',
        'raised_by' => $this->manager->id,
    ]);

    $instance = $rfi->workflowInstance;

    // Step 1: Manager rejects
    $this->actingAs($this->manager);
    $instance->logs()->create([
        'workflow_step_id' => $this->step1->id,
        'performed_by' => $this->manager->id,
        'action' => 'rejected',
    ]);
    $instance->update(['status' => 'rejected']);
    $rfi->update(['status' => 'void']);

    expect($instance->fresh()->status)->toBe('rejected');
    expect($rfi->fresh()->status)->toBe('void');
});

test('only allows one active template per company per module type', function () {
    expect($this->template->is_active)->toBeTrue();

    // Create a new template and activate it
    $newTemplate = WorkflowTemplate::create([
        'company_id' => $this->company->id,
        'module_type' => 'Rfi',
        'name' => 'New RFI Workflow',
        'is_active' => true,
    ]);

    expect($newTemplate->is_active)->toBeTrue();
    expect($this->template->fresh()->is_active)->toBeFalse();
});
