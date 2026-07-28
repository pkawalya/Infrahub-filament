<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('workflow_instances') && !Schema::hasColumn('workflow_instances', 'company_id')) {
            Schema::table('workflow_instances', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
                $table->json('audit_log')->nullable()->after('status');
            });
        }

        if (Schema::hasTable('workflow_steps')) {
            Schema::table('workflow_steps', function (Blueprint $table) {
                if (!Schema::hasColumn('workflow_steps', 'approver_role')) {
                    $table->string('approver_role')->nullable()->after('name');
                }
                if (!Schema::hasColumn('workflow_steps', 'assigned_user_id')) {
                    $table->foreignId('assigned_user_id')->nullable()->after('approver_role')->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('workflow_steps', 'approval_status')) {
                    $table->string('approval_status')->default('approved')->after('assigned_user_id');
                }
                if (!Schema::hasColumn('workflow_steps', 'rejection_status')) {
                    $table->string('rejection_status')->default('rejected')->after('approval_status');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('workflow_instances')) {
            Schema::table('workflow_instances', function (Blueprint $table) {
                if (Schema::hasColumn('workflow_instances', 'company_id')) {
                    $table->dropForeign(['company_id']);
                    $table->dropColumn(['company_id', 'audit_log']);
                }
            });
        }

        if (Schema::hasTable('workflow_steps')) {
            Schema::table('workflow_steps', function (Blueprint $table) {
                if (Schema::hasColumn('workflow_steps', 'assigned_user_id')) {
                    $table->dropForeign(['assigned_user_id']);
                }
                $columns = array_filter(['approver_role', 'assigned_user_id', 'approval_status', 'rejection_status'], fn($col) => Schema::hasColumn('workflow_steps', $col));
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
