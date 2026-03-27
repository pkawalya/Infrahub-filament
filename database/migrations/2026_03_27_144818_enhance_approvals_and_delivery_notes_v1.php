<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Covers three enhancements:
     *
     * 1. Delivery Notes → link to stock_transfers (inter-store transfer DN)
     * 2. Harmonized multi-level approvals on:
     *    - purchase_orders
     *    - stock_transfers
     *    - material_requisitions
     */
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────
        // 1. Link delivery_notes to stock_transfers
        //    (a DN can be issued when transferring stock between stores)
        // ─────────────────────────────────────────────────────────────
        Schema::table('delivery_notes', function (Blueprint $table) {
            // Allow a DN to be raised directly without a project (standalone)
            if (Schema::hasColumn('delivery_notes', 'cde_project_id')) {
                $table->unsignedBigInteger('cde_project_id')->nullable()->change();
            }
            if (!Schema::hasColumn('delivery_notes', 'stock_transfer_id')) {
                $table->foreignId('stock_transfer_id')
                    ->nullable()
                    ->after('purchase_order_id')
                    ->constrained('stock_transfers')
                    ->nullOnDelete();
            }
            // Source warehouse (for pure-warehouse DNs without a project)
            if (!Schema::hasColumn('delivery_notes', 'from_warehouse_id')) {
                $table->foreignId('from_warehouse_id')
                    ->nullable()
                    ->after('warehouse_id')
                    ->constrained('warehouses')
                    ->nullOnDelete();
            }
            if (!Schema::hasColumn('delivery_notes', 'to_warehouse_id')) {
                $table->foreignId('to_warehouse_id')
                    ->nullable()
                    ->after('from_warehouse_id')
                    ->constrained('warehouses')
                    ->nullOnDelete();
            }
        });

        // ─────────────────────────────────────────────────────────────
        // 2. Harmonised approval tiers on purchase_orders
        //    Level 1 = Line manager / Procurement officer
        //    Level 2 = Finance / MD (above threshold)
        // ─────────────────────────────────────────────────────────────
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'approval_level')) {
                // 1 = single-level, 2 = dual-level required
                $table->unsignedTinyInteger('approval_level')->default(1)->after('approved_at');
            }
            if (!Schema::hasColumn('purchase_orders', 'level1_approved_by')) {
                $table->unsignedBigInteger('level1_approved_by')->nullable()->after('approval_level');
                $table->foreign('level1_approved_by')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('purchase_orders', 'level1_approved_at')) {
                $table->timestamp('level1_approved_at')->nullable()->after('level1_approved_by');
            }
            if (!Schema::hasColumn('purchase_orders', 'level2_approved_by')) {
                $table->unsignedBigInteger('level2_approved_by')->nullable()->after('level1_approved_at');
                $table->foreign('level2_approved_by')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('purchase_orders', 'level2_approved_at')) {
                $table->timestamp('level2_approved_at')->nullable()->after('level2_approved_by');
            }
            if (!Schema::hasColumn('purchase_orders', 'level2_rejection_reason')) {
                $table->text('level2_rejection_reason')->nullable()->after('level2_approved_at');
            }
            // Amount threshold above which level-2 approval is required
            if (!Schema::hasColumn('purchase_orders', 'approval_threshold')) {
                $table->decimal('approval_threshold', 14, 2)->nullable()->after('level2_rejection_reason');
            }
        });

        // ─────────────────────────────────────────────────────────────
        // 3. Harmonised approval tiers on stock_transfers
        // ─────────────────────────────────────────────────────────────
        Schema::table('stock_transfers', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_transfers', 'approval_level')) {
                $table->unsignedTinyInteger('approval_level')->default(1)->after('approved_by');
            }
            if (!Schema::hasColumn('stock_transfers', 'level1_approved_by')) {
                $table->unsignedBigInteger('level1_approved_by')->nullable()->after('approval_level');
                $table->foreign('level1_approved_by')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('stock_transfers', 'level1_approved_at')) {
                $table->timestamp('level1_approved_at')->nullable()->after('level1_approved_by');
            }
            if (!Schema::hasColumn('stock_transfers', 'level2_approved_by')) {
                $table->unsignedBigInteger('level2_approved_by')->nullable()->after('level1_approved_at');
                $table->foreign('level2_approved_by')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('stock_transfers', 'level2_approved_at')) {
                $table->timestamp('level2_approved_at')->nullable()->after('level2_approved_by');
            }
            if (!Schema::hasColumn('stock_transfers', 'delivery_note_number')) {
                // Auto-generated DN number stamped when DN is raised for a transfer
                $table->string('delivery_note_number', 50)->nullable()->after('transfer_number');
            }
        });

        // ─────────────────────────────────────────────────────────────
        // 4. Harmonised approval tiers on material_requisitions
        // ─────────────────────────────────────────────────────────────
        Schema::table('material_requisitions', function (Blueprint $table) {
            if (!Schema::hasColumn('material_requisitions', 'approval_level')) {
                $table->unsignedTinyInteger('approval_level')->default(1)->after('approved_at');
            }
            if (!Schema::hasColumn('material_requisitions', 'level1_approved_by')) {
                $table->unsignedBigInteger('level1_approved_by')->nullable()->after('approval_level');
                $table->foreign('level1_approved_by')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('material_requisitions', 'level1_approved_at')) {
                $table->timestamp('level1_approved_at')->nullable()->after('level1_approved_by');
            }
            if (!Schema::hasColumn('material_requisitions', 'level2_approved_by')) {
                $table->unsignedBigInteger('level2_approved_by')->nullable()->after('level1_approved_at');
                $table->foreign('level2_approved_by')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('material_requisitions', 'level2_approved_at')) {
                $table->timestamp('level2_approved_at')->nullable()->after('level2_approved_by');
            }
            if (!Schema::hasColumn('material_requisitions', 'level2_rejection_reason')) {
                $table->text('level2_rejection_reason')->nullable()->after('level2_approved_at');
            }
        });
    }

    public function down(): void
    {
        // Delivery notes
        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('stock_transfer_id');
            $table->dropConstrainedForeignId('from_warehouse_id');
            $table->dropConstrainedForeignId('to_warehouse_id');
        });
        // PO approvals
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['level1_approved_by']);
            $table->dropForeign(['level2_approved_by']);
            $table->dropColumn(['approval_level', 'level1_approved_by', 'level1_approved_at',
                'level2_approved_by', 'level2_approved_at', 'level2_rejection_reason', 'approval_threshold']);
        });
        // Transfer approvals
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->dropForeign(['level1_approved_by']);
            $table->dropForeign(['level2_approved_by']);
            $table->dropColumn(['approval_level', 'level1_approved_by', 'level1_approved_at',
                'level2_approved_by', 'level2_approved_at', 'delivery_note_number']);
        });
        // MR approvals
        Schema::table('material_requisitions', function (Blueprint $table) {
            $table->dropForeign(['level1_approved_by']);
            $table->dropForeign(['level2_approved_by']);
            $table->dropColumn(['approval_level', 'level1_approved_by', 'level1_approved_at',
                'level2_approved_by', 'level2_approved_at', 'level2_rejection_reason']);
        });
    }
};
