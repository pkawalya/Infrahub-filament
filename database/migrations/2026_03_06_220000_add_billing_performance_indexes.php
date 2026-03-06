<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Helper: check if an index exists
        $hasIndex = function (string $table, string $indexName): bool {
            $indexes = collect(DB::select("SHOW INDEX FROM {$table}"))
                ->pluck('Key_name')->unique()->values()->toArray();
            return in_array($indexName, $indexes);
        };

        // ── Billing records ─────────────────────────────────────
        if (Schema::hasTable('billing_records')) {
            Schema::table('billing_records', function (Blueprint $table) use ($hasIndex) {
                if (!$hasIndex('billing_records', 'billing_records_status_index')) {
                    $table->index('status');
                }
                if (!$hasIndex('billing_records', 'billing_records_company_id_status_index')) {
                    $table->index(['company_id', 'status']);
                }
            });
        }

        // ── Project billing status ──────────────────────────────
        if (Schema::hasTable('cde_projects') && Schema::hasColumn('cde_projects', 'billing_status')) {
            Schema::table('cde_projects', function (Blueprint $table) use ($hasIndex) {
                if (!$hasIndex('cde_projects', 'cde_projects_billing_status_index')) {
                    $table->index('billing_status');
                }
            });
        }

        // ── Invoice overdue queries ─────────────────────────────
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) use ($hasIndex) {
                if (!$hasIndex('invoices', 'invoices_status_due_date_index')) {
                    $table->index(['status', 'due_date']);
                }
                if (!$hasIndex('invoices', 'invoices_company_id_status_index')) {
                    $table->index(['company_id', 'status']);
                }
            });
        }

        // ── Subscription expiry ─────────────────────────────────
        if (Schema::hasTable('company_subscriptions') && Schema::hasColumn('company_subscriptions', 'ends_at')) {
            Schema::table('company_subscriptions', function (Blueprint $table) use ($hasIndex) {
                if (!$hasIndex('company_subscriptions', 'company_subscriptions_status_ends_at_index')) {
                    $table->index(['status', 'ends_at']);
                }
            });
        }
    }

    public function down(): void
    {
        $dropSafe = function (string $table, string $indexName): void {
            try {
                Schema::table($table, fn(Blueprint $t) => $t->dropIndex($indexName));
            } catch (\Throwable) {
                // Index may not exist
            }
        };

        $dropSafe('billing_records', 'billing_records_status_index');
        $dropSafe('billing_records', 'billing_records_company_id_status_index');
        $dropSafe('cde_projects', 'cde_projects_billing_status_index');
        $dropSafe('invoices', 'invoices_status_due_date_index');
        $dropSafe('invoices', 'invoices_company_id_status_index');
        $dropSafe('company_subscriptions', 'company_subscriptions_status_ends_at_index');
    }
};
