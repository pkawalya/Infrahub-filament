<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // ─── Project Templates ──────────────────────────────
            [
                'name' => 'Project Assignment',
                'slug' => 'project-assigned',
                'category' => 'project',
                'subject' => 'You\'ve been added to {{project_name}} — {{app_name}}',
                'body' => '<h2 style="color:#1f2937;margin:0 0 16px;">Welcome to the project! 🎉</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;"><strong>{{assigned_by}}</strong> has added you to the project <strong>{{project_name}}</strong> ({{project_code}}).</p>'
                    . '<table style="width:100%;border-collapse:collapse;margin:20px 0;"><tr>'
                    . '<td style="padding:12px 16px;background:#f0f9ff;border-radius:8px 0 0 8px;border-left:4px solid #3b82f6;"><strong style="color:#1e40af;">Project</strong><br><span style="color:#4b5563;">{{project_name}}</span></td>'
                    . '<td style="padding:12px 16px;background:#f0f9ff;"><strong style="color:#1e40af;">Role</strong><br><span style="color:#4b5563;">{{role_in_project}}</span></td>'
                    . '<td style="padding:12px 16px;background:#f0f9ff;border-radius:0 8px 8px 0;"><strong style="color:#1e40af;">Status</strong><br><span style="color:#4b5563;">{{project_status}}</span></td>'
                    . '</tr></table>'
                    . '<p style="text-align:center;margin:24px 0;"><a href="{{project_url}}" style="display:inline-block;padding:12px 32px;background:linear-gradient(135deg,#3b82f6,#4f46e5);color:#ffffff;text-decoration:none;border-radius:8px;font-weight:600;">Open Project</a></p>'
                    . '<p style="color:#9ca3af;font-size:13px;">If you believe this was a mistake, please contact your project manager.</p>',
                'available_variables' => ['user_name', 'user_email', 'company_name', 'app_name', 'app_url', 'current_date', 'project_name', 'project_code', 'project_status', 'project_url', 'assigned_by', 'role_in_project'],
            ],
            [
                'name' => 'Project Removal',
                'slug' => 'project-removed',
                'category' => 'project',
                'subject' => 'You\'ve been removed from {{project_name}}',
                'body' => '<h2 style="color:#1f2937;margin:0 0 16px;">Project Update</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">You have been removed from the project <strong>{{project_name}}</strong> by <strong>{{removed_by}}</strong>.</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">If you have any questions, please reach out to your team lead.</p>',
                'available_variables' => ['user_name', 'company_name', 'app_name', 'project_name', 'removed_by'],
            ],

            // ─── Task Templates ─────────────────────────────────
            [
                'name' => 'Task Assigned',
                'slug' => 'task-assigned',
                'category' => 'task',
                'subject' => 'New task: {{task_title}} — {{project_name}}',
                'body' => '<h2 style="color:#1f2937;margin:0 0 16px;">You have a new task 📋</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;"><strong>{{assigned_by}}</strong> assigned you a task in <strong>{{project_name}}</strong>.</p>'
                    . '<div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:20px;margin:20px 0;">'
                    . '<p style="margin:0 0 8px;"><strong style="color:#1f2937;">{{task_title}}</strong></p>'
                    . '<p style="color:#6b7280;margin:0 0 12px;">{{task_description}}</p>'
                    . '<table style="width:100%;"><tr>'
                    . '<td><span style="background:#fef3c7;color:#92400e;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:600;">⚡ {{task_priority}}</span></td>'
                    . '<td><span style="background:#dbeafe;color:#1e40af;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:600;">{{task_status}}</span></td>'
                    . '<td style="color:#6b7280;font-size:13px;">📅 Due: {{task_due_date}}</td>'
                    . '</tr></table></div>'
                    . '<p style="text-align:center;margin:24px 0;"><a href="{{task_url}}" style="display:inline-block;padding:12px 32px;background:linear-gradient(135deg,#3b82f6,#4f46e5);color:#ffffff;text-decoration:none;border-radius:8px;font-weight:600;">View Task</a></p>',
                'available_variables' => ['user_name', 'company_name', 'app_name', 'project_name', 'task_title', 'task_description', 'task_status', 'task_priority', 'task_due_date', 'task_url', 'assigned_by'],
            ],
            [
                'name' => 'Task Status Changed',
                'slug' => 'task-status-changed',
                'category' => 'task',
                'subject' => 'Task updated: {{task_title}} is now {{task_status}}',
                'body' => '<h2 style="color:#1f2937;margin:0 0 16px;">Task Status Update</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">The task <strong>{{task_title}}</strong> in <strong>{{project_name}}</strong> has been updated to <strong>{{task_status}}</strong>.</p>'
                    . '<p style="text-align:center;margin:24px 0;"><a href="{{task_url}}" style="display:inline-block;padding:12px 32px;background:#4f46e5;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:600;">View Task</a></p>',
                'available_variables' => ['user_name', 'company_name', 'project_name', 'task_title', 'task_status', 'task_url'],
            ],

            // ─── Billing Templates ──────────────────────────────
            [
                'name' => 'Invoice Sent',
                'slug' => 'invoice-sent',
                'category' => 'billing',
                'subject' => 'Invoice from {{company_name}} — {{amount}}',
                'body' => '<h2 style="color:#1f2937;margin:0 0 16px;">New Invoice 💰</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">A new invoice has been generated for your account.</p>'
                    . '<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:20px;margin:20px 0;text-align:center;">'
                    . '<p style="color:#15803d;font-size:28px;font-weight:700;margin:0;">{{amount}}</p>'
                    . '<p style="color:#4ade80;font-size:13px;margin:4px 0 0;">{{billing_cycle}} • {{plan_name}}</p>'
                    . '</div>'
                    . '<p style="text-align:center;margin:24px 0;"><a href="{{invoice_url}}" style="display:inline-block;padding:12px 32px;background:linear-gradient(135deg,#22c55e,#16a34a);color:#ffffff;text-decoration:none;border-radius:8px;font-weight:600;">View Invoice</a></p>',
                'available_variables' => ['user_name', 'company_name', 'app_name', 'amount', 'billing_cycle', 'plan_name', 'invoice_url', 'expiry_date'],
            ],
            [
                'name' => 'Subscription Expiring',
                'slug' => 'subscription-expiring',
                'category' => 'billing',
                'subject' => 'Your {{plan_name}} plan expires on {{expiry_date}}',
                'body' => '<h2 style="color:#1f2937;margin:0 0 16px;">Subscription Reminder ⏰</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">Your <strong>{{plan_name}}</strong> subscription for <strong>{{company_name}}</strong> will expire on <strong>{{expiry_date}}</strong>.</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">To avoid service interruption, please renew your subscription before the expiration date.</p>'
                    . '<p style="text-align:center;margin:24px 0;"><a href="{{app_url}}/app" style="display:inline-block;padding:12px 32px;background:linear-gradient(135deg,#f59e0b,#d97706);color:#ffffff;text-decoration:none;border-radius:8px;font-weight:600;">Renew Now</a></p>',
                'available_variables' => ['user_name', 'company_name', 'app_name', 'plan_name', 'expiry_date', 'billing_cycle', 'app_url'],
            ],

            // ─── Notification Templates ─────────────────────────
            [
                'name' => 'General Notification',
                'slug' => 'general-notification',
                'category' => 'notification',
                'subject' => '{{notification_title}} — {{app_name}}',
                'body' => '<h2 style="color:#1f2937;margin:0 0 16px;">{{notification_title}}</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">{{notification_body}}</p>'
                    . '<p style="text-align:center;margin:24px 0;"><a href="{{action_url}}" style="display:inline-block;padding:12px 32px;background:#4f46e5;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:600;">View Details</a></p>',
                'available_variables' => ['user_name', 'company_name', 'app_name', 'notification_title', 'notification_body', 'action_url'],
            ],

            // ─── Reporting Templates ────────────────────────────
            [
                'name' => 'Report Ready',
                'slug' => 'report-ready',
                'category' => 'reporting',
                'subject' => 'Your {{report_name}} report is ready — {{app_name}}',
                'body' => '<h2 style="color:#1f2937;margin:0 0 16px;">Report Ready 📊</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">Your <strong>{{report_name}}</strong> for <strong>{{report_period}}</strong> has been generated and is ready to view.</p>'
                    . '<p style="text-align:center;margin:24px 0;"><a href="{{report_url}}" style="display:inline-block;padding:12px 32px;background:linear-gradient(135deg,#8b5cf6,#6d28d9);color:#ffffff;text-decoration:none;border-radius:8px;font-weight:600;">View Report</a></p>',
                'available_variables' => ['user_name', 'company_name', 'app_name', 'report_name', 'report_period', 'report_url'],
            ],

            // ─── Welcome / Auth Templates ───────────────────────
            [
                'name' => 'Welcome Email',
                'slug' => 'welcome',
                'category' => 'authentication',
                'subject' => 'Welcome to {{app_name}}, {{user_name}}! 🎉',
                'body' => '<h2 style="color:#1f2937;margin:0 0 16px;">Welcome to {{app_name}}! 🎉</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">Your account has been created for <strong>{{company_name}}</strong>. You can now log in and start collaborating with your team.</p>'
                    . '<p style="text-align:center;margin:24px 0;"><a href="{{login_url}}" style="display:inline-block;padding:12px 32px;background:linear-gradient(135deg,#3b82f6,#4f46e5);color:#ffffff;text-decoration:none;border-radius:8px;font-weight:600;">Log In Now</a></p>'
                    . '<p style="color:#9ca3af;font-size:13px;">If you didn\'t create this account, you can safely ignore this email.</p>',
                'available_variables' => ['user_name', 'user_email', 'company_name', 'app_name', 'login_url'],
            ],
            [
                'name' => 'New User Credentials',
                'slug' => 'welcome-new-user',
                'category' => 'authentication',
                'subject' => 'Your {{app_name}} account is ready — Login Details Inside',
                'body' => '<h2 style="color:#1f2937;margin:0 0 16px;">Your Account is Ready! 🔑</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;"><strong>{{creator_name}}</strong> has created an account for you at <strong>{{company_name}}</strong> on {{app_name}}.</p>'
                    . '<div style="background:#f0f9ff;border:1px solid #bfdbfe;border-radius:12px;padding:24px;margin:24px 0;">'
                    . '<h3 style="color:#1e40af;margin:0 0 16px;font-size:16px;">Your Login Credentials</h3>'
                    . '<table style="width:100%;border-collapse:collapse;">'
                    . '<tr><td style="padding:8px 0;color:#6b7280;width:100px;">Email:</td><td style="padding:8px 0;color:#1f2937;font-weight:600;">{{user_email}}</td></tr>'
                    . '<tr><td style="padding:8px 0;color:#6b7280;">Password:</td><td style="padding:8px 0;color:#1f2937;font-weight:600;font-family:monospace;letter-spacing:1px;">{{user_password}}</td></tr>'
                    . '<tr><td style="padding:8px 0;color:#6b7280;">Role:</td><td style="padding:8px 0;color:#1f2937;font-weight:600;">{{user_role}}</td></tr>'
                    . '</table>'
                    . '</div>'
                    . '<div style="background:#fef3c7;border:1px solid #fde68a;border-radius:8px;padding:12px 16px;margin:0 0 24px;">'
                    . '<p style="color:#92400e;margin:0;font-size:13px;">⚠️ <strong>Important:</strong> You will be asked to change your password when you first log in. Please keep these credentials safe until then.</p>'
                    . '</div>'
                    . '<p style="text-align:center;margin:24px 0;"><a href="{{login_url}}" style="display:inline-block;padding:14px 40px;background:linear-gradient(135deg,#3b82f6,#4f46e5);color:#ffffff;text-decoration:none;border-radius:8px;font-weight:600;font-size:16px;">Log In Now →</a></p>'
                    . '<p style="color:#9ca3af;font-size:12px;text-align:center;">If you did not expect this email, please contact your administrator.</p>',
                'available_variables' => ['user_name', 'user_email', 'company_name', 'app_name', 'login_url', 'user_password', 'user_role', 'creator_name'],
            ],
            [
                'name' => 'Password Reset',
                'slug' => 'password-reset',
                'category' => 'authentication',
                'subject' => 'Reset Your Password — {{app_name}}',
                'body' => '<h2 style="color:#1f2937;margin:0 0 16px;">Password Reset Request 🔒</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">We received a request to reset the password for your <strong>{{app_name}}</strong> account associated with <strong>{{user_email}}</strong>.</p>'
                    . '<p style="text-align:center;margin:28px 0;"><a href="{{reset_url}}" style="display:inline-block;padding:14px 40px;background:linear-gradient(135deg,#3b82f6,#4f46e5);color:#ffffff;text-decoration:none;border-radius:8px;font-weight:600;font-size:16px;">Reset Password →</a></p>'
                    . '<div style="background:#fef3c7;border:1px solid #fde68a;border-radius:8px;padding:12px 16px;margin:0 0 24px;">'
                    . '<p style="color:#92400e;margin:0;font-size:13px;">⏰ This link will expire in <strong>{{expire_minutes}} minutes</strong>. If you didn\'t request a password reset, you can safely ignore this email — your password will remain unchanged.</p>'
                    . '</div>'
                    . '<p style="color:#9ca3af;font-size:12px;text-align:center;">For security, never share this link with anyone.</p>',
                'available_variables' => ['user_name', 'user_email', 'company_name', 'app_name', 'reset_url', 'expire_minutes'],
            ],

            // ─── Invoice Alerts ─────────────────────────────────
            [
                'name' => 'Invoice Overdue Notice',
                'slug' => 'invoice-overdue',
                'category' => 'billing',
                'subject' => '⚠️ {{overdue_count}} overdue invoice(s) — {{company_name}}',
                'body' => '<h2 style="color:#dc2626;margin:0 0 16px;">Overdue Invoices ⚠️</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">You have <strong>{{overdue_count}}</strong> overdue invoice(s) totaling <strong>{{total_overdue_amount}}</strong>.</p>'
                    . '<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:16px;margin:20px 0;">'
                    . '<p style="color:#991b1b;margin:0;font-weight:600;">Overdue: {{invoice_numbers}}</p>'
                    . '</div>'
                    . '<p style="text-align:center;margin:24px 0;"><a href="{{app_url}}/app" style="display:inline-block;padding:12px 32px;background:#dc2626;color:#ffffff;text-decoration:none;border-radius:8px;font-weight:600;">Review Invoices</a></p>',
                'available_variables' => ['user_name', 'company_name', 'app_name', 'app_url', 'overdue_count', 'total_overdue_amount', 'invoice_numbers'],
            ],

            // ─── Work Order Templates ──────────────────────────
            [
                'name' => 'Work Order Assigned',
                'slug' => 'work-order-assigned',
                'category' => 'project',
                'subject' => 'Work Order {{wo_number}} assigned to you — {{project_name}}',
                'body' => '<h2 style="color:#1f2937;margin:0 0 16px;">New Work Order Assigned 🔧</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;"><strong>{{assigned_by}}</strong> assigned you work order <strong>{{wo_number}}</strong> in <strong>{{project_name}}</strong>.</p>'
                    . '<div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:20px;margin:20px 0;">'
                    . '<p style="margin:0 0 8px;"><strong style="color:#1f2937;">{{wo_title}}</strong></p>'
                    . '<table style="width:100%;"><tr>'
                    . '<td><span style="background:#fef3c7;color:#92400e;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:600;">⚡ {{priority}}</span></td>'
                    . '<td style="color:#6b7280;font-size:13px;">📅 Due: {{due_date}}</td>'
                    . '</tr></table></div>',
                'available_variables' => ['user_name', 'company_name', 'wo_number', 'wo_title', 'priority', 'due_date', 'project_name', 'assigned_by'],
            ],
            [
                'name' => 'Work Order Approved',
                'slug' => 'work-order-approved',
                'category' => 'project',
                'subject' => 'Work Order {{wo_number}} approved — {{project_name}}',
                'body' => '<h2 style="color:#059669;margin:0 0 16px;">Work Order Approved ✅</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">Work order <strong>{{wo_number}} — {{wo_title}}</strong> has been approved by <strong>{{actioned_by}}</strong>.</p>',
                'available_variables' => ['user_name', 'wo_number', 'wo_title', 'project_name', 'actioned_by'],
            ],
            [
                'name' => 'Work Order Completed',
                'slug' => 'work-order-completed',
                'category' => 'project',
                'subject' => 'Work Order {{wo_number}} completed — {{project_name}}',
                'body' => '<h2 style="color:#059669;margin:0 0 16px;">Work Order Completed 🎉</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">Work order <strong>{{wo_number}} — {{wo_title}}</strong> has been marked as completed by <strong>{{actioned_by}}</strong>.</p>',
                'available_variables' => ['user_name', 'wo_number', 'wo_title', 'project_name', 'actioned_by'],
            ],
            [
                'name' => 'Work Order Started',
                'slug' => 'work-order-started',
                'category' => 'project',
                'subject' => 'Work Order {{wo_number}} started — {{project_name}}',
                'body' => '<h2 style="color:#2563eb;margin:0 0 16px;">Work Order In Progress 🚧</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">Work order <strong>{{wo_number}} — {{wo_title}}</strong> is now in progress.</p>',
                'available_variables' => ['user_name', 'wo_number', 'wo_title', 'project_name', 'actioned_by'],
            ],

            // ─── RFI Templates ─────────────────────────────────
            [
                'name' => 'RFI Created',
                'slug' => 'rfi-created',
                'category' => 'project',
                'subject' => 'RFI {{rfi_number}}: {{rfi_subject}} — {{project_name}}',
                'body' => '<h2 style="color:#1f2937;margin:0 0 16px;">New RFI Requires Your Response 📝</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;"><strong>{{raised_by}}</strong> has raised an RFI that requires your response.</p>'
                    . '<div style="background:#f0f9ff;border-left:4px solid #3b82f6;padding:16px;margin:20px 0;border-radius:0 8px 8px 0;">'
                    . '<p style="margin:0 0 4px;"><strong>{{rfi_number}}</strong> — {{rfi_subject}}</p>'
                    . '<p style="color:#6b7280;margin:0;font-size:13px;">Priority: {{priority}} · Due: {{due_date}}</p>'
                    . '</div>',
                'available_variables' => ['user_name', 'rfi_number', 'rfi_subject', 'priority', 'due_date', 'project_name', 'raised_by'],
            ],
            [
                'name' => 'RFI Answered',
                'slug' => 'rfi-answered',
                'category' => 'project',
                'subject' => 'RFI {{rfi_number}} answered — {{project_name}}',
                'body' => '<h2 style="color:#059669;margin:0 0 16px;">Your RFI Has Been Answered ✅</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">RFI <strong>{{rfi_number}} — {{rfi_subject}}</strong> has been answered by <strong>{{actioned_by}}</strong>.</p>',
                'available_variables' => ['user_name', 'rfi_number', 'rfi_subject', 'project_name', 'actioned_by'],
            ],
            [
                'name' => 'RFI Closed',
                'slug' => 'rfi-closed',
                'category' => 'project',
                'subject' => 'RFI {{rfi_number}} closed — {{project_name}}',
                'body' => '<h2 style="color:#6b7280;margin:0 0 16px;">RFI Closed</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">RFI <strong>{{rfi_number}} — {{rfi_subject}}</strong> has been closed by <strong>{{actioned_by}}</strong>.</p>',
                'available_variables' => ['user_name', 'rfi_number', 'rfi_subject', 'project_name', 'actioned_by'],
            ],

            // ─── Submittal Templates ───────────────────────────
            [
                'name' => 'Submittal Submitted for Review',
                'slug' => 'submittal-submitted',
                'category' => 'project',
                'subject' => 'Submittal {{submittal_number}} for review — {{project_name}}',
                'body' => '<h2 style="color:#1f2937;margin:0 0 16px;">New Submittal for Review 📋</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;"><strong>{{submitted_by}}</strong> submitted <strong>{{submittal_number}} — {{submittal_title}}</strong> ({{type}}) for your review.</p>',
                'available_variables' => ['user_name', 'submittal_number', 'submittal_title', 'type', 'project_name', 'submitted_by'],
            ],
            [
                'name' => 'Submittal Approved',
                'slug' => 'submittal-approved',
                'category' => 'project',
                'subject' => 'Submittal {{submittal_number}} approved — {{project_name}}',
                'body' => '<h2 style="color:#059669;margin:0 0 16px;">Submittal Approved ✅</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">Submittal <strong>{{submittal_number}} — {{submittal_title}}</strong> has been <strong>{{status}}</strong> by <strong>{{actioned_by}}</strong>.</p>'
                    . '<p style="color:#6b7280;">{{review_comments}}</p>',
                'available_variables' => ['user_name', 'submittal_number', 'submittal_title', 'status', 'project_name', 'actioned_by', 'review_comments'],
            ],
            [
                'name' => 'Submittal Rejected',
                'slug' => 'submittal-rejected',
                'category' => 'project',
                'subject' => 'Submittal {{submittal_number}} needs revision — {{project_name}}',
                'body' => '<h2 style="color:#dc2626;margin:0 0 16px;">Submittal Requires Revision ❌</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">Submittal <strong>{{submittal_number}} — {{submittal_title}}</strong> has been <strong>{{status}}</strong> by <strong>{{actioned_by}}</strong>.</p>'
                    . '<div style="background:#fef2f2;border-left:4px solid #dc2626;padding:16px;margin:20px 0;border-radius:0 8px 8px 0;">'
                    . '<p style="color:#991b1b;margin:0;">{{review_comments}}</p>'
                    . '</div>',
                'available_variables' => ['user_name', 'submittal_number', 'submittal_title', 'status', 'project_name', 'actioned_by', 'review_comments'],
            ],

            // ─── Purchase Order Templates ──────────────────────
            [
                'name' => 'Purchase Order Submitted',
                'slug' => 'po-submitted',
                'category' => 'billing',
                'subject' => 'PO {{po_number}} submitted for approval — {{supplier}}',
                'body' => '<h2 style="color:#1f2937;margin:0 0 16px;">Purchase Order Awaiting Approval 📦</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">A purchase order has been submitted for your approval.</p>'
                    . '<div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:20px;margin:20px 0;">'
                    . '<table style="width:100%;"><tr>'
                    . '<td><strong>PO #:</strong> {{po_number}}</td>'
                    . '<td><strong>Supplier:</strong> {{supplier}}</td>'
                    . '<td><strong>Amount:</strong> {{total_amount}}</td>'
                    . '</tr></table></div>',
                'available_variables' => ['user_name', 'po_number', 'total_amount', 'supplier', 'project_name', 'actioned_by'],
            ],
            [
                'name' => 'Purchase Order Approved',
                'slug' => 'po-approved',
                'category' => 'billing',
                'subject' => 'PO {{po_number}} approved ✅',
                'body' => '<h2 style="color:#059669;margin:0 0 16px;">Purchase Order Approved ✅</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">Your purchase order <strong>{{po_number}}</strong> for <strong>{{supplier}}</strong> ({{total_amount}}) has been approved by <strong>{{actioned_by}}</strong>.</p>',
                'available_variables' => ['user_name', 'po_number', 'total_amount', 'supplier', 'project_name', 'actioned_by'],
            ],
            [
                'name' => 'Purchase Order Rejected',
                'slug' => 'po-rejected',
                'category' => 'billing',
                'subject' => 'PO {{po_number}} rejected ❌',
                'body' => '<h2 style="color:#dc2626;margin:0 0 16px;">Purchase Order Rejected ❌</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">Your purchase order <strong>{{po_number}}</strong> has been rejected by <strong>{{actioned_by}}</strong>.</p>'
                    . '<div style="background:#fef2f2;border-left:4px solid #dc2626;padding:16px;margin:20px 0;">'
                    . '<p style="color:#991b1b;margin:0;"><strong>Reason:</strong> {{rejection_reason}}</p></div>',
                'available_variables' => ['user_name', 'po_number', 'total_amount', 'supplier', 'project_name', 'actioned_by', 'rejection_reason'],
            ],
            [
                'name' => 'Purchase Order Received',
                'slug' => 'po-received',
                'category' => 'billing',
                'subject' => 'PO {{po_number}} goods received — {{supplier}}',
                'body' => '<h2 style="color:#059669;margin:0 0 16px;">Goods Received 📥</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">Purchase order <strong>{{po_number}}</strong> from <strong>{{supplier}}</strong> has been received.</p>',
                'available_variables' => ['user_name', 'po_number', 'total_amount', 'supplier', 'project_name'],
            ],

            // ─── Material Requisition Templates ────────────────
            [
                'name' => 'Material Requisition Submitted',
                'slug' => 'requisition-submitted',
                'category' => 'project',
                'subject' => 'Material Requisition {{requisition_number}} awaiting approval',
                'body' => '<h2 style="color:#1f2937;margin:0 0 16px;">Material Requisition Pending Approval 📋</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">A material requisition <strong>{{requisition_number}}</strong> ({{priority}}) has been submitted for approval.</p>'
                    . '<p style="color:#6b7280;">Purpose: {{purpose}}</p>'
                    . '<p style="color:#6b7280;">Required by: {{required_date}}</p>',
                'available_variables' => ['user_name', 'requisition_number', 'purpose', 'priority', 'required_date', 'project_name'],
            ],
            [
                'name' => 'Material Requisition Approved',
                'slug' => 'requisition-approved',
                'category' => 'project',
                'subject' => 'Material Requisition {{requisition_number}} approved ✅',
                'body' => '<h2 style="color:#059669;margin:0 0 16px;">Requisition Approved ✅</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">Your material requisition <strong>{{requisition_number}}</strong> has been approved by <strong>{{actioned_by}}</strong>.</p>',
                'available_variables' => ['user_name', 'requisition_number', 'purpose', 'project_name', 'actioned_by'],
            ],
            [
                'name' => 'Material Requisition Rejected',
                'slug' => 'requisition-rejected',
                'category' => 'project',
                'subject' => 'Material Requisition {{requisition_number}} rejected',
                'body' => '<h2 style="color:#dc2626;margin:0 0 16px;">Requisition Rejected ❌</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">Your material requisition <strong>{{requisition_number}}</strong> has been rejected by <strong>{{actioned_by}}</strong>.</p>',
                'available_variables' => ['user_name', 'requisition_number', 'purpose', 'project_name', 'actioned_by'],
            ],
            [
                'name' => 'Material Requisition Issued',
                'slug' => 'requisition-issued',
                'category' => 'project',
                'subject' => 'Material Requisition {{requisition_number}} issued — materials ready',
                'body' => '<h2 style="color:#059669;margin:0 0 16px;">Materials Issued 📦</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">The materials for requisition <strong>{{requisition_number}}</strong> have been issued and are ready for collection.</p>',
                'available_variables' => ['user_name', 'requisition_number', 'purpose', 'project_name'],
            ],

            // ─── Snag / Punch List Templates ──────────────────
            [
                'name' => 'Snag Item Created',
                'slug' => 'snag-created',
                'category' => 'project',
                'subject' => 'Snag {{snag_number}}: {{snag_title}} — {{project_name}}',
                'body' => '<h2 style="color:#1f2937;margin:0 0 16px;">New Snag/Defect Assigned ⚠️</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">A snag has been reported and assigned to you by <strong>{{reported_by}}</strong>.</p>'
                    . '<div style="background:#fef3c7;border-left:4px solid #f59e0b;padding:16px;margin:20px 0;border-radius:0 8px 8px 0;">'
                    . '<p style="margin:0 0 4px;"><strong>{{snag_number}}</strong> — {{snag_title}}</p>'
                    . '<p style="color:#6b7280;margin:0;font-size:13px;">Severity: {{severity}} · Location: {{location}} · Due: {{due_date}}</p>'
                    . '</div>',
                'available_variables' => ['user_name', 'snag_number', 'snag_title', 'severity', 'location', 'due_date', 'project_name', 'reported_by'],
            ],
            [
                'name' => 'Snag Item Resolved',
                'slug' => 'snag-resolved',
                'category' => 'project',
                'subject' => 'Snag {{snag_number}} resolved — {{project_name}}',
                'body' => '<h2 style="color:#059669;margin:0 0 16px;">Snag Resolved ✅</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">Snag <strong>{{snag_number}} — {{snag_title}}</strong> has been resolved by <strong>{{actioned_by}}</strong>.</p>',
                'available_variables' => ['user_name', 'snag_number', 'snag_title', 'project_name', 'actioned_by'],
            ],

            // ─── Equipment Templates ──────────────────────────
            [
                'name' => 'Equipment Assigned',
                'slug' => 'equipment-assigned',
                'category' => 'project',
                'subject' => 'Equipment assigned: {{asset_name}} — {{project_name}}',
                'body' => '<h2 style="color:#1f2937;margin:0 0 16px;">Equipment Allocated to You 🚜</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;"><strong>{{assigned_by}}</strong> has assigned you equipment <strong>{{asset_name}}</strong> for project <strong>{{project_name}}</strong>.</p>'
                    . '<p style="color:#6b7280;">Period: {{start_date}} — {{end_date}}</p>',
                'available_variables' => ['user_name', 'asset_name', 'project_name', 'start_date', 'end_date', 'assigned_by'],
            ],
            [
                'name' => 'Equipment Returned',
                'slug' => 'equipment-returned',
                'category' => 'project',
                'subject' => 'Equipment returned: {{asset_name}} — {{project_name}}',
                'body' => '<h2 style="color:#6b7280;margin:0 0 16px;">Equipment Returned 🔄</h2>'
                    . '<p style="color:#4b5563;line-height:1.7;">Hi <strong>{{user_name}}</strong>,</p>'
                    . '<p style="color:#4b5563;line-height:1.7;">Equipment <strong>{{asset_name}}</strong> has been returned from project <strong>{{project_name}}</strong> by <strong>{{returned_by}}</strong>.</p>',
                'available_variables' => ['user_name', 'asset_name', 'project_name', 'returned_by'],
            ],
        ];

        foreach ($templates as $templateData) {
            EmailTemplate::updateOrCreate(
                ['slug' => $templateData['slug'], 'company_id' => null],
                array_merge($templateData, [
                    'company_id' => null, // Global templates
                    'is_active' => true,
                ])
            );
        }

        $this->command->info('Seeded ' . count($templates) . ' global email templates.');
    }
}
