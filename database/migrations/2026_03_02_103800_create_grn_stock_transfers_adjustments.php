<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ─── Enhance stock_transfers (add missing columns) ──
        Schema::table('stock_transfers', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transfers', 'cde_project_id')) {
                $table->foreignId('cde_project_id')->nullable()->after('company_id');
            }
            if (!Schema::hasColumn('stock_transfers', 'priority')) {
                $table->string('priority', 20)->default('normal')->after('status');
            }
            if (!Schema::hasColumn('stock_transfers', 'requested_date')) {
                $table->date('requested_date')->nullable()->after('priority');
            }
            if (!Schema::hasColumn('stock_transfers', 'shipped_date')) {
                $table->date('shipped_date')->nullable()->after('requested_date');
            }
            if (!Schema::hasColumn('stock_transfers', 'received_date')) {
                $table->date('received_date')->nullable()->after('shipped_date');
            }
            if (!Schema::hasColumn('stock_transfers', 'reason')) {
                $table->text('reason')->nullable()->after('received_date');
            }
            if (!Schema::hasColumn('stock_transfers', 'requested_by')) {
                $table->unsignedBigInteger('requested_by')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('stock_transfers', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('requested_by');
            }
            if (!Schema::hasColumn('stock_transfers', 'shipped_by')) {
                $table->unsignedBigInteger('shipped_by')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('stock_transfers', 'received_by')) {
                $table->unsignedBigInteger('received_by')->nullable()->after('shipped_by');
            }
            if (!Schema::hasColumn('stock_transfers', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // ─── Enhance stock_transfer_items ───
        Schema::table('stock_transfer_items', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transfer_items', 'quantity_requested')) {
                $table->decimal('quantity_requested', 12, 2)->default(0)->after('product_id');
            }
            if (!Schema::hasColumn('stock_transfer_items', 'quantity_shipped')) {
                $table->decimal('quantity_shipped', 12, 2)->default(0)->after('quantity_requested');
            }
            if (!Schema::hasColumn('stock_transfer_items', 'quantity_received')) {
                $table->decimal('quantity_received', 12, 2)->default(0)->after('quantity_shipped');
            }
        });

        // ─── Enhance stock_adjustments ───
        Schema::table('stock_adjustments', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_adjustments', 'cde_project_id')) {
                $table->foreignId('cde_project_id')->nullable()->after('company_id');
            }
            if (!Schema::hasColumn('stock_adjustments', 'product_id')) {
                $table->unsignedBigInteger('product_id')->nullable()->after('warehouse_id');
            }
            if (!Schema::hasColumn('stock_adjustments', 'quantity_before')) {
                $table->decimal('quantity_before', 12, 2)->default(0)->after('type');
            }
            if (!Schema::hasColumn('stock_adjustments', 'quantity_after')) {
                $table->decimal('quantity_after', 12, 2)->default(0)->after('quantity_before');
            }
            if (!Schema::hasColumn('stock_adjustments', 'quantity_change')) {
                $table->decimal('quantity_change', 12, 2)->default(0)->after('quantity_after');
            }
            if (!Schema::hasColumn('stock_adjustments', 'performed_by')) {
                $table->unsignedBigInteger('performed_by')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        // Column drops are risky, keeping empty
    }
};
