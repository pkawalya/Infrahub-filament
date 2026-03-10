<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

/**
 * Seeds default notification email templates for all modules.
 *
 * Templates are created as GLOBAL (company_id = null).
 * Companies can override by creating a template with the same slug.
 *
 * Run: php artisan db:seed --class=ModuleNotificationTemplateSeeder
 */
class ModuleNotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = $this->getTemplates();

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['slug' => $template['slug'], 'company_id' => null],
                $template,
            );
        }

        $this->command->info('✅ ' . count($templates) . ' notification templates seeded.');
    }

    protected function getTemplates(): array
    {
        return [

            // ═══════════════════════════════════════════════
            // TASKS MODULE
            // ═══════════════════════════════════════════════
            [
                'slug' => 'task-assigned',
                'name' => 'Task Assigned',
                'category' => 'task',
                'subject' => '📋 New Task Assigned: {{task_title}}',
                'body' => '<p>Hello {{user_name}},</p>
<p>You have been assigned a new task:</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Task</td><td style="padding:8px;">{{task_title}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Priority</td><td style="padding:8px;">{{task_priority}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Due Date</td><td style="padding:8px;">{{task_due_date}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Project</td><td style="padding:8px;">{{project_name}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Assigned By</td><td style="padding:8px;">{{assigned_by}}</td></tr>
</table>
<p>Please review and start working on this task at your earliest convenience.</p>',
                'available_variables' => ['user_name', 'task_title', 'task_priority', 'task_due_date', 'project_name', 'assigned_by'],
                'is_active' => true,
            ],

            [
                'slug' => 'task-completed',
                'name' => 'Task Completed',
                'category' => 'task',
                'subject' => '✅ Task Completed: {{task_title}}',
                'body' => '<p>Hello {{user_name}},</p>
<p>A task you created has been marked as <strong>completed</strong>.</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Task</td><td style="padding:8px;">{{task_title}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Completed By</td><td style="padding:8px;">{{completed_by}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Project</td><td style="padding:8px;">{{project_name}}</td></tr>
</table>',
                'available_variables' => ['user_name', 'task_title', 'completed_by', 'project_name'],
                'is_active' => true,
            ],

            // ═══════════════════════════════════════════════
            // CHANGE ORDERS MODULE
            // ═══════════════════════════════════════════════
            [
                'slug' => 'change-order-created',
                'name' => 'Change Order Created',
                'category' => 'project',
                'subject' => '🔄 New Change Order: {{co_reference}} — {{co_title}}',
                'body' => '<p>Hello {{user_name}},</p>
<p>A new change order has been raised on <strong>{{project_name}}</strong>.</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Reference</td><td style="padding:8px;">{{co_reference}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Title</td><td style="padding:8px;">{{co_title}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Type</td><td style="padding:8px;">{{co_type}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Estimated Cost</td><td style="padding:8px;">{{estimated_cost}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Initiated By</td><td style="padding:8px;">{{initiated_by}}</td></tr>
</table>
<p>Please review this change order and take appropriate action.</p>',
                'available_variables' => ['user_name', 'co_reference', 'co_title', 'co_type', 'estimated_cost', 'initiated_by', 'project_name'],
                'is_active' => true,
            ],

            [
                'slug' => 'change-order-approved',
                'name' => 'Change Order Approved',
                'category' => 'project',
                'subject' => '✅ Change Order Approved: {{co_reference}}',
                'body' => '<p>Hello {{user_name}},</p>
<p>Change order <strong>{{co_reference}}</strong> has been <span style="color:#16a34a;font-weight:bold;">APPROVED</span>.</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Reference</td><td style="padding:8px;">{{co_reference}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Title</td><td style="padding:8px;">{{co_title}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Approved Cost</td><td style="padding:8px;">{{approved_cost}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Notes</td><td style="padding:8px;">{{approval_notes}}</td></tr>
</table>',
                'available_variables' => ['user_name', 'co_reference', 'co_title', 'approved_cost', 'approval_notes', 'project_name'],
                'is_active' => true,
            ],

            [
                'slug' => 'change-order-rejected',
                'name' => 'Change Order Rejected',
                'category' => 'project',
                'subject' => '❌ Change Order Rejected: {{co_reference}}',
                'body' => '<p>Hello {{user_name}},</p>
<p>Change order <strong>{{co_reference}}</strong> has been <span style="color:#dc2626;font-weight:bold;">REJECTED</span>.</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Reference</td><td style="padding:8px;">{{co_reference}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Title</td><td style="padding:8px;">{{co_title}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Reason</td><td style="padding:8px;">{{rejection_reason}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Rejected By</td><td style="padding:8px;">{{actioned_by}}</td></tr>
</table>',
                'available_variables' => ['user_name', 'co_reference', 'co_title', 'rejection_reason', 'actioned_by'],
                'is_active' => true,
            ],

            [
                'slug' => 'change-order-submitted',
                'name' => 'Change Order Submitted for Review',
                'category' => 'project',
                'subject' => '📤 Change Order Submitted: {{co_reference}}',
                'body' => '<p>Hello {{user_name}},</p>
<p>Change order <strong>{{co_reference}} — {{co_title}}</strong> has been submitted for your review.</p>
<p>Please review and approve or reject at your earliest convenience.</p>',
                'available_variables' => ['user_name', 'co_reference', 'co_title'],
                'is_active' => true,
            ],

            // ═══════════════════════════════════════════════
            // DRAWING MANAGEMENT MODULE
            // ═══════════════════════════════════════════════
            [
                'slug' => 'drawing-submitted-review',
                'name' => 'Drawing Submitted for Review',
                'category' => 'project',
                'subject' => '📐 Drawing for Review: {{drawing_number}} Rev {{revision}}',
                'body' => '<p>Hello {{user_name}},</p>
<p>A drawing has been submitted for review on <strong>{{project_name}}</strong>.</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Drawing No.</td><td style="padding:8px;">{{drawing_number}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Title</td><td style="padding:8px;">{{drawing_title}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Discipline</td><td style="padding:8px;">{{discipline}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Revision</td><td style="padding:8px;">{{revision}}</td></tr>
</table>',
                'available_variables' => ['user_name', 'drawing_number', 'drawing_title', 'discipline', 'revision', 'project_name'],
                'is_active' => true,
            ],

            [
                'slug' => 'drawing-approved',
                'name' => 'Drawing Approved',
                'category' => 'project',
                'subject' => '✅ Drawing Approved: {{drawing_number}}',
                'body' => '<p>Hello {{user_name}},</p>
<p>Your drawing <strong>{{drawing_number}} — {{drawing_title}}</strong> has been <span style="color:#16a34a;font-weight:bold;">APPROVED</span>.</p>
<p>Approved by: {{actioned_by}}</p>',
                'available_variables' => ['user_name', 'drawing_number', 'drawing_title', 'actioned_by', 'project_name'],
                'is_active' => true,
            ],

            [
                'slug' => 'drawing-issued-construction',
                'name' => 'Drawing Issued for Construction',
                'category' => 'project',
                'subject' => '🏗️ Drawing IFC: {{drawing_number}} Rev {{revision}}',
                'body' => '<p>Hello {{user_name}},</p>
<p>Drawing <strong>{{drawing_number}}</strong> (Rev {{revision}}) has been issued for construction on <strong>{{project_name}}</strong>.</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Title</td><td style="padding:8px;">{{drawing_title}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Discipline</td><td style="padding:8px;">{{discipline}}</td></tr>
</table>
<p>Please ensure all site teams are using the latest revision.</p>',
                'available_variables' => ['user_name', 'drawing_number', 'drawing_title', 'discipline', 'revision', 'project_name'],
                'is_active' => true,
            ],

            // ═══════════════════════════════════════════════
            // PAYMENT CERTIFICATES MODULE
            // ═══════════════════════════════════════════════
            [
                'slug' => 'payment-cert-submitted',
                'name' => 'Payment Certificate Submitted',
                'category' => 'billing',
                'subject' => '📄 Payment Certificate Submitted: {{cert_number}}',
                'body' => '<p>Hello {{user_name}},</p>
<p>A payment certificate has been submitted for certification.</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Certificate</td><td style="padding:8px;">{{cert_number}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Type</td><td style="padding:8px;">{{cert_type}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Period</td><td style="padding:8px;">{{period}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Net Payable</td><td style="padding:8px;">{{net_payable}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Total Payable</td><td style="padding:8px;">{{total_payable}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Project</td><td style="padding:8px;">{{project_name}}</td></tr>
</table>
<p>Please review and certify.</p>',
                'available_variables' => ['user_name', 'cert_number', 'cert_type', 'period', 'net_payable', 'total_payable', 'project_name'],
                'is_active' => true,
            ],

            [
                'slug' => 'payment-cert-certified',
                'name' => 'Payment Certificate Certified',
                'category' => 'billing',
                'subject' => '✅ Payment Certificate Certified: {{cert_number}}',
                'body' => '<p>Hello {{user_name}},</p>
<p>Payment certificate <strong>{{cert_number}}</strong> has been <span style="color:#16a34a;font-weight:bold;">CERTIFIED</span>.</p>
<p><strong>Total Payable:</strong> {{total_payable}}</p>
<p>The certificate is now pending payment.</p>',
                'available_variables' => ['user_name', 'cert_number', 'total_payable', 'project_name'],
                'is_active' => true,
            ],

            [
                'slug' => 'payment-cert-paid',
                'name' => 'Payment Certificate Paid',
                'category' => 'billing',
                'subject' => '💰 Payment Received: {{cert_number}}',
                'body' => '<p>Hello {{user_name}},</p>
<p>Payment for certificate <strong>{{cert_number}}</strong> has been processed.</p>
<p><strong>Amount Paid:</strong> {{total_payable}}</p>',
                'available_variables' => ['user_name', 'cert_number', 'total_payable', 'project_name'],
                'is_active' => true,
            ],

            [
                'slug' => 'payment-cert-rejected',
                'name' => 'Payment Certificate Rejected',
                'category' => 'billing',
                'subject' => '❌ Payment Certificate Rejected: {{cert_number}}',
                'body' => '<p>Hello {{user_name}},</p>
<p>Payment certificate <strong>{{cert_number}}</strong> has been <span style="color:#dc2626;font-weight:bold;">REJECTED</span>.</p>
<p><strong>Reason:</strong> {{rejection_reason}}</p>
<p>Please review and resubmit with the necessary corrections.</p>',
                'available_variables' => ['user_name', 'cert_number', 'rejection_reason', 'project_name'],
                'is_active' => true,
            ],

            // ═══════════════════════════════════════════════
            // SAFETY MODULE
            // ═══════════════════════════════════════════════
            [
                'slug' => 'safety-incident-reported',
                'name' => 'Safety Incident Reported',
                'category' => 'notification',
                'subject' => '⚠️ Safety Incident Reported on {{project_name}}',
                'body' => '<p>Hello {{user_name}},</p>
<p>A safety incident has been reported.</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Incident</td><td style="padding:8px;">{{incident_title}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Severity</td><td style="padding:8px;">{{severity}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Location</td><td style="padding:8px;">{{location}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Date</td><td style="padding:8px;">{{incident_date}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Reported By</td><td style="padding:8px;">{{reported_by}}</td></tr>
</table>',
                'available_variables' => ['user_name', 'incident_title', 'severity', 'location', 'incident_date', 'reported_by', 'project_name'],
                'is_active' => true,
            ],

            [
                'slug' => 'safety-incident-critical',
                'name' => 'Critical Safety Incident',
                'category' => 'notification',
                'subject' => '🚨 CRITICAL Safety Incident: {{incident_title}}',
                'body' => '<p style="color:#dc2626;font-weight:bold;">⚠️ URGENT — Critical Safety Incident</p>
<p>Hello {{user_name}},</p>
<p>A <strong>{{severity}}</strong> safety incident has been reported on <strong>{{project_name}}</strong> and requires immediate attention.</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;border:2px solid #dc2626;">
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Incident</td><td style="padding:8px;">{{incident_title}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Severity</td><td style="padding:8px;color:#dc2626;font-weight:bold;">{{severity}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Location</td><td style="padding:8px;">{{location}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Date</td><td style="padding:8px;">{{incident_date}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Reported By</td><td style="padding:8px;">{{reported_by}}</td></tr>
</table>
<p>Please take immediate action. This incident has been escalated to management.</p>',
                'available_variables' => ['user_name', 'incident_title', 'severity', 'location', 'incident_date', 'reported_by', 'project_name'],
                'is_active' => true,
            ],

            // ═══════════════════════════════════════════════
            // DAILY SITE DIARY MODULE
            // ═══════════════════════════════════════════════
            [
                'slug' => 'site-diary-submitted',
                'name' => 'Site Diary Submitted',
                'category' => 'project',
                'subject' => '📔 Site Diary Submitted: {{project_name}} — {{diary_date}}',
                'body' => '<p>Hello {{user_name}},</p>
<p>A daily site diary has been submitted for approval.</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Date</td><td style="padding:8px;">{{diary_date}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Project</td><td style="padding:8px;">{{project_name}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Submitted By</td><td style="padding:8px;">{{submitted_by}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Weather</td><td style="padding:8px;">{{weather}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Workers</td><td style="padding:8px;">{{total_workers}}</td></tr>
</table>',
                'available_variables' => ['user_name', 'diary_date', 'project_name', 'submitted_by', 'weather', 'total_workers'],
                'is_active' => true,
            ],

            [
                'slug' => 'site-diary-approved',
                'name' => 'Site Diary Approved',
                'category' => 'project',
                'subject' => '✅ Site Diary Approved: {{project_name}} — {{diary_date}}',
                'body' => '<p>Hello {{user_name}},</p>
<p>Your daily site diary for <strong>{{diary_date}}</strong> on project <strong>{{project_name}}</strong> has been approved.</p>
<p>Approved by: {{approved_by}}</p>',
                'available_variables' => ['user_name', 'diary_date', 'project_name', 'approved_by'],
                'is_active' => true,
            ],

            // ═══════════════════════════════════════════════
            // INVOICES MODULE
            // ═══════════════════════════════════════════════
            [
                'slug' => 'invoice-sent',
                'name' => 'Invoice Sent',
                'category' => 'billing',
                'subject' => '📧 Invoice Sent: {{invoice_number}}',
                'body' => '<p>Hello {{user_name}},</p>
<p>Invoice <strong>{{invoice_number}}</strong> has been sent to the client.</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Invoice</td><td style="padding:8px;">{{invoice_number}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Amount</td><td style="padding:8px;">{{total_amount}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Due Date</td><td style="padding:8px;">{{due_date}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Client</td><td style="padding:8px;">{{client_name}}</td></tr>
</table>',
                'available_variables' => ['user_name', 'invoice_number', 'total_amount', 'due_date', 'client_name', 'project_name'],
                'is_active' => true,
            ],

            [
                'slug' => 'invoice-paid',
                'name' => 'Invoice Paid',
                'category' => 'billing',
                'subject' => '💰 Invoice Paid: {{invoice_number}}',
                'body' => '<p>Hello {{user_name}},</p>
<p>Invoice <strong>{{invoice_number}}</strong> has been marked as <span style="color:#16a34a;font-weight:bold;">PAID</span>.</p>
<p><strong>Amount:</strong> {{total_amount}}</p>',
                'available_variables' => ['user_name', 'invoice_number', 'total_amount', 'project_name'],
                'is_active' => true,
            ],

            [
                'slug' => 'invoice-overdue-alert',
                'name' => 'Invoice Overdue Alert',
                'category' => 'billing',
                'subject' => '🔴 OVERDUE Invoice: {{invoice_number}}',
                'body' => '<p style="color:#dc2626;font-weight:bold;">⚠️ Invoice Overdue</p>
<p>Hello {{user_name}},</p>
<p>Invoice <strong>{{invoice_number}}</strong> is now <strong>overdue</strong>.</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;border:2px solid #f59e0b;">
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Invoice</td><td style="padding:8px;">{{invoice_number}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Amount</td><td style="padding:8px;">{{total_amount}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Due Date</td><td style="padding:8px;color:#dc2626;">{{due_date}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Client</td><td style="padding:8px;">{{client_name}}</td></tr>
</table>
<p>Please follow up with the client to collect payment.</p>',
                'available_variables' => ['user_name', 'invoice_number', 'total_amount', 'due_date', 'client_name', 'project_name'],
                'is_active' => true,
            ],

            // ═══════════════════════════════════════════════
            // PROJECT MODULE
            // ═══════════════════════════════════════════════
            [
                'slug' => 'project-assigned',
                'name' => 'Project Assignment',
                'category' => 'project',
                'subject' => '🏗️ You\'ve been added to project: {{project_name}}',
                'body' => '<p>Hello {{user_name}},</p>
<p>You have been added to the project <strong>{{project_name}}</strong> ({{project_code}}).</p>
<table style="width:100%;border-collapse:collapse;margin:16px 0;">
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Project</td><td style="padding:8px;">{{project_name}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Code</td><td style="padding:8px;">{{project_code}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Your Role</td><td style="padding:8px;">{{role_in_project}}</td></tr>
<tr><td style="padding:8px;font-weight:bold;color:#6b7280;">Added By</td><td style="padding:8px;">{{assigned_by}}</td></tr>
</table>
<p>You can now access all project modules and documents.</p>',
                'available_variables' => ['user_name', 'project_name', 'project_code', 'role_in_project', 'assigned_by'],
                'is_active' => true,
            ],

        ];
    }
}
