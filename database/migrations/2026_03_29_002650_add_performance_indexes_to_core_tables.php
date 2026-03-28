<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Composite performance indexes for the most-queried tenant-scoped tables.
 *
 * All indexes use suppressions (->skipIfExists-like pattern via try/catch)
 * so this migration is safe to run multiple times or on a database that
 * already has some of these indexes from earlier migrations.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Tasks ─────────────────────────────────────────────────
        $this->safeIndex('tasks', ['company_id', 'status', 'deleted_at'], 'tasks_company_status_deleted_idx');
        $this->safeIndex('tasks', ['company_id', 'due_date'],             'tasks_company_due_date_idx');
        $this->safeIndex('tasks', ['company_id', 'assigned_to'],          'tasks_company_assignee_idx');
        $this->safeIndex('tasks', ['cde_project_id', 'status'],           'tasks_project_status_idx');

        // ── Work Orders ─────────────────────────────────────────────
        $this->safeIndex('work_orders', ['company_id', 'status', 'deleted_at'], 'wo_company_status_deleted_idx');
        $this->safeIndex('work_orders', ['company_id', 'due_date'],              'wo_company_due_date_idx');
        $this->safeIndex('work_orders', ['company_id', 'assigned_to'],           'wo_company_assignee_idx');

        // ── Crew Attendance ─────────────────────────────────────────
        $this->safeIndex('crew_attendance', ['company_id', 'attendance_date'], 'attendance_company_date_idx');
        $this->safeIndex('crew_attendance', ['company_id', 'status'],          'attendance_company_status_idx');
        $this->safeIndex('crew_attendance', ['cde_project_id', 'attendance_date'], 'attendance_project_date_idx');

        // ── Safety Incidents ────────────────────────────────────────
        $this->safeIndex('safety_incidents', ['company_id', 'incident_date'],      'safety_company_date_idx');
        $this->safeIndex('safety_incidents', ['company_id', 'status'],             'safety_company_status_idx');
        $this->safeIndex('safety_incidents', ['company_id', 'severity'],           'safety_company_severity_idx');

        // ── Daily Site Diaries ──────────────────────────────────────
        $this->safeIndex('daily_site_diaries', ['company_id', 'diary_date'],      'diary_company_date_idx');
        $this->safeIndex('daily_site_diaries', ['cde_project_id', 'diary_date'],  'diary_project_date_idx');

        // ── Invoices ────────────────────────────────────────────────
        $this->safeIndex('invoices', ['company_id', 'status', 'deleted_at'], 'invoices_company_status_deleted_idx');
        $this->safeIndex('invoices', ['company_id', 'issue_date'],           'invoices_company_issue_date_idx');
        $this->safeIndex('invoices', ['company_id', 'due_date'],             'invoices_company_due_date_idx');

        // ── Equipment Allocations ───────────────────────────────────
        $this->safeIndex('equipment_allocations', ['company_id', 'status'],        'equip_alloc_company_status_idx');
        $this->safeIndex('equipment_allocations', ['cde_project_id', 'status'],    'equip_alloc_project_status_idx');

        // ── Equipment Fuel Logs ─────────────────────────────────────
        $this->safeIndex('equipment_fuel_logs', ['company_id', 'log_date'],       'fuel_company_date_idx');
        $this->safeIndex('equipment_fuel_logs', ['company_id', 'asset_id'],       'fuel_company_asset_idx');

        // ── Drawings ────────────────────────────────────────────────
        $this->safeIndex('drawings', ['company_id', 'status', 'deleted_at'], 'drawings_company_status_deleted_idx');
        $this->safeIndex('drawings', ['cde_project_id', 'status'],           'drawings_project_status_idx');
    }

    public function down(): void
    {
        $dropMap = [
            'tasks'                 => ['tasks_company_status_deleted_idx', 'tasks_company_due_date_idx', 'tasks_company_assignee_idx', 'tasks_project_status_idx'],
            'work_orders'           => ['wo_company_status_deleted_idx', 'wo_company_due_date_idx', 'wo_company_assignee_idx'],
            'crew_attendance'       => ['attendance_company_date_idx', 'attendance_company_status_idx', 'attendance_project_date_idx'],
            'safety_incidents'      => ['safety_company_date_idx', 'safety_company_status_idx', 'safety_company_severity_idx'],
            'daily_site_diaries'    => ['diary_company_date_idx', 'diary_project_date_idx'],
            'invoices'              => ['invoices_company_status_deleted_idx', 'invoices_company_issue_date_idx', 'invoices_company_due_date_idx'],
            'equipment_allocations' => ['equip_alloc_company_status_idx', 'equip_alloc_project_status_idx'],
            'equipment_fuel_logs'   => ['fuel_company_date_idx', 'fuel_company_asset_idx'],
            'drawings'              => ['drawings_company_status_deleted_idx', 'drawings_project_status_idx'],
        ];

        foreach ($dropMap as $table => $indexes) {
            Schema::table($table, function (Blueprint $t) use ($indexes) {
                foreach ($indexes as $idx) {
                    try { $t->dropIndex($idx); } catch (\Throwable) {}
                }
            });
        }
    }

    /** Add index only if it doesn't already exist */
    private function safeIndex(string $table, array $columns, string $name): void
    {
        try {
            Schema::table($table, function (Blueprint $t) use ($columns, $name) {
                $t->index($columns, $name);
            });
        } catch (\Throwable $e) {
            // Index already exists or column missing — skip silently
            \Illuminate\Support\Facades\Log::info("Index {$name} skipped: " . $e->getMessage());
        }
    }
};
