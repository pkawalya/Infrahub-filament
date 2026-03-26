<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── Enhance Products: min/max order levels + expiry tracking ──
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                if (!Schema::hasColumn('products', 'max_order_level')) {
                    $table->integer('max_order_level')->default(0)->after('reorder_quantity')
                        ->comment('Maximum stock level; triggers over-stock alert when exceeded');
                }
                if (!Schema::hasColumn('products', 'expiry_tracking_enabled')) {
                    $table->boolean('expiry_tracking_enabled')->default(false)->after('max_order_level');
                }
                if (!Schema::hasColumn('products', 'expiry_date')) {
                    $table->date('expiry_date')->nullable()->after('expiry_tracking_enabled');
                }
                if (!Schema::hasColumn('products', 'supplier_id')) {
                    $table->unsignedBigInteger('supplier_id')->nullable()->after('product_category_id');
                    $table->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();
                }
                if (!Schema::hasColumn('products', 'lead_time_days')) {
                    $table->integer('lead_time_days')->nullable()->after('supplier_id')
                        ->comment('Lead time in days for procurement');
                }
            });
        }

        // ── Enhance Purchase Orders: quarterly procurement + URA fields ──
        if (Schema::hasTable('purchase_orders')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                if (!Schema::hasColumn('purchase_orders', 'is_quarterly')) {
                    $table->boolean('is_quarterly')->default(false)->after('notes')
                        ->comment('Flag for quarterly procurement cycle tracking');
                }
                if (!Schema::hasColumn('purchase_orders', 'quarter')) {
                    $table->string('quarter')->nullable()->after('is_quarterly')
                        ->comment('e.g., Q1-2026, Q2-2026');
                }
                if (!Schema::hasColumn('purchase_orders', 'delivery_address')) {
                    $table->text('delivery_address')->nullable()->after('quarter');
                }
                if (!Schema::hasColumn('purchase_orders', 'payment_terms')) {
                    $table->string('payment_terms')->nullable()->after('delivery_address');
                }
                if (!Schema::hasColumn('purchase_orders', 'currency')) {
                    $table->string('currency')->default('UGX')->after('payment_terms');
                }
                if (!Schema::hasColumn('purchase_orders', 'submitted_at')) {
                    $table->timestamp('submitted_at')->nullable()->after('currency');
                }
                if (!Schema::hasColumn('purchase_orders', 'approved_at')) {
                    $table->timestamp('approved_at')->nullable()->after('submitted_at');
                }
                if (!Schema::hasColumn('purchase_orders', 'rejection_reason')) {
                    $table->text('rejection_reason')->nullable()->after('approved_at');
                }
            });
        }

        // ── Stock Level: add cost snapshot for valuation ──
        if (Schema::hasTable('stock_levels')) {
            Schema::table('stock_levels', function (Blueprint $table) {
                if (!Schema::hasColumn('stock_levels', 'average_cost')) {
                    $table->decimal('average_cost', 12, 2)->default(0)->after('bin_location')
                        ->comment('Weighted average cost at time of last receipt');
                }
                if (!Schema::hasColumn('stock_levels', 'last_movement_at')) {
                    $table->timestamp('last_movement_at')->nullable()->after('average_cost');
                }
            });
        }

        // ── Inventory Audit Log (URA compliance) ──
        if (!Schema::hasTable('inventory_audit_logs')) {
            Schema::create('inventory_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('cde_project_id')->nullable();
                $table->string('event_type'); // po_created, grn_received, stock_adjusted, material_issued, asset_registered, etc.
                $table->string('reference_type')->nullable(); // PurchaseOrder, GoodsReceivedNote, MaterialIssuance, etc.
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->string('reference_number')->nullable();
                $table->unsignedBigInteger('product_id')->nullable();
                $table->unsignedBigInteger('warehouse_id')->nullable();
                $table->decimal('quantity_before', 12, 2)->nullable();
                $table->decimal('quantity_after', 12, 2)->nullable();
                $table->decimal('quantity_change', 12, 2)->nullable();
                $table->decimal('unit_cost', 12, 2)->nullable();
                $table->decimal('total_value', 12, 2)->nullable();
                $table->text('description')->nullable();
                $table->json('metadata')->nullable();
                $table->unsignedBigInteger('performed_by')->nullable();
                $table->string('ip_address')->nullable();
                $table->timestamps();

                $table->foreign('cde_project_id')->references('id')->on('cde_projects')->nullOnDelete();
                $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
                $table->foreign('warehouse_id')->references('id')->on('warehouses')->nullOnDelete();
                $table->foreign('performed_by')->references('id')->on('users')->nullOnDelete();

                $table->index(['company_id', 'event_type', 'created_at']);
                $table->index(['reference_type', 'reference_id']);
            });
        }

        // ── Delivery Receipts: extend GRN with delivery receipt fields ──
        if (Schema::hasTable('goods_received_notes')) {
            Schema::table('goods_received_notes', function (Blueprint $table) {
                if (!Schema::hasColumn('goods_received_notes', 'delivery_date')) {
                    $table->date('delivery_date')->nullable()->after('received_date');
                }
                if (!Schema::hasColumn('goods_received_notes', 'carrier_name')) {
                    $table->string('carrier_name')->nullable()->after('delivery_date');
                }
                if (!Schema::hasColumn('goods_received_notes', 'vehicle_plate')) {
                    $table->string('vehicle_plate')->nullable()->after('carrier_name');
                }
                if (!Schema::hasColumn('goods_received_notes', 'driver_name')) {
                    $table->string('driver_name')->nullable()->after('vehicle_plate');
                }
                if (!Schema::hasColumn('goods_received_notes', 'inspector_id')) {
                    $table->unsignedBigInteger('inspector_id')->nullable()->after('driver_name');
                    $table->foreign('inspector_id')->references('id')->on('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('goods_received_notes', 'inspection_passed')) {
                    $table->boolean('inspection_passed')->default(true)->after('inspector_id');
                }
                if (!Schema::hasColumn('goods_received_notes', 'invoice_reference')) {
                    $table->string('invoice_reference')->nullable()->after('inspection_passed')
                        ->comment('Supplier invoice number for URA audit matching');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_audit_logs');

        if (Schema::hasTable('goods_received_notes')) {
            Schema::table('goods_received_notes', function (Blueprint $table) {
                $columns = ['delivery_date', 'carrier_name', 'vehicle_plate', 'driver_name', 'inspector_id', 'inspection_passed', 'invoice_reference'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('goods_received_notes', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('stock_levels')) {
            Schema::table('stock_levels', function (Blueprint $table) {
                foreach (['average_cost', 'last_movement_at'] as $col) {
                    if (Schema::hasColumn('stock_levels', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('purchase_orders')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                foreach (['is_quarterly', 'quarter', 'delivery_address', 'payment_terms', 'currency', 'submitted_at', 'approved_at', 'rejection_reason'] as $col) {
                    if (Schema::hasColumn('purchase_orders', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                foreach (['max_order_level', 'expiry_tracking_enabled', 'expiry_date', 'supplier_id', 'lead_time_days'] as $col) {
                    if (Schema::hasColumn('products', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
