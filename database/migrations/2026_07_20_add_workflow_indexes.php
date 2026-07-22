<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tablesWithStatus = [
            'safety_incidents', 'social_records', 'safety_inspections', 'ncrs',
            'cde_documents', 'document_submissions', 'snag_items', 'submittals',
            'rfis', 'purchase_orders', 'drawings', 'invoices', 'work_orders',
            'project_suggestions', 'equipment_allocations', 'material_requisitions',
            'payment_certificates', 'tasks',
        ];

        $tablesWithoutStatus = [
            'daily_site_diaries', 'change_orders', 'tenders', 'contracts',
        ];

        $projectTables = [
            'safety_incidents', 'social_records', 'safety_inspections', 'ncrs',
            'cde_documents', 'document_submissions', 'snag_items', 'submittals',
            'rfis', 'purchase_orders', 'drawings', 'invoices', 'work_orders',
            'project_suggestions', 'equipment_allocations', 'material_requisitions',
            'daily_site_diaries', 'payment_certificates', 'tasks',
        ];

        foreach ($tablesWithStatus as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            try {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->index(['company_id', 'status', 'created_at'], "idx_{$tableName}_company_status_created");
                });
            } catch (\Throwable $e) {
                // skip
            }
        }

        foreach ($tablesWithoutStatus as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            try {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->index(['company_id', 'created_at'], "idx_{$tableName}_company_created");
                });
            } catch (\Throwable $e) {
                // skip
            }
        }

        foreach ($projectTables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            try {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->index(['cde_project_id', 'created_at'], "idx_{$tableName}_project_created");
                });
            } catch (\Throwable $e) {
                // skip
            }
        }
    }

    public function down(): void
    {
        // No need to reverse index additions — they are purely performance optimizations
    }
};
