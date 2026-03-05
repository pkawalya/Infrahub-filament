<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── Enhance boq_items with product link + variance columns ──
        Schema::table('boq_items', function (Blueprint $t) {
            if (!Schema::hasColumn('boq_items', 'product_id')) {
                $t->unsignedBigInteger('product_id')->nullable()->after('boq_id');
                $t->foreign('product_id')->references('id')->on('products')->nullOnDelete();
            }
            if (!Schema::hasColumn('boq_items', 'actual_quantity')) {
                $t->decimal('actual_quantity', 12, 4)->default(0)->after('quantity_completed');
            }
            if (!Schema::hasColumn('boq_items', 'actual_cost')) {
                $t->decimal('actual_cost', 14, 2)->default(0)->after('actual_quantity');
            }
            if (!Schema::hasColumn('boq_items', 'variance_amount')) {
                $t->decimal('variance_amount', 14, 2)->default(0)->after('actual_cost');
            }
            if (!Schema::hasColumn('boq_items', 'variance_percent')) {
                $t->decimal('variance_percent', 8, 2)->default(0)->after('variance_amount');
            }
            if (!Schema::hasColumn('boq_items', 'last_synced_at')) {
                $t->timestamp('last_synced_at')->nullable()->after('variance_percent');
            }
        });

        // ── BOQ Variance Alerts ──
        Schema::create('boq_variance_alerts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('boq_id')->constrained()->cascadeOnDelete();
            $t->foreignId('boq_item_id')->nullable()->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('cde_project_id')->nullable();
            $t->string('severity'); // low, medium, high, critical
            $t->string('alert_type')->default('overrun'); // overrun, underrun, quantity_exceeded
            $t->string('title');
            $t->text('message');
            $t->decimal('budgeted_value', 14, 2)->default(0);
            $t->decimal('actual_value', 14, 2)->default(0);
            $t->decimal('variance_percent', 8, 2)->default(0);
            $t->boolean('is_acknowledged')->default(false);
            $t->unsignedBigInteger('acknowledged_by')->nullable();
            $t->timestamp('acknowledged_at')->nullable();
            $t->timestamps();

            $t->foreign('cde_project_id')->references('id')->on('cde_projects')->nullOnDelete();
            $t->foreign('acknowledged_by')->references('id')->on('users')->nullOnDelete();

            $t->index(['boq_id', 'severity']);
            $t->index(['cde_project_id', 'is_acknowledged']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boq_variance_alerts');

        Schema::table('boq_items', function (Blueprint $t) {
            if (Schema::hasColumn('boq_items', 'product_id')) {
                $t->dropForeign(['product_id']);
                $t->dropColumn('product_id');
            }
            $cols = ['actual_quantity', 'actual_cost', 'variance_amount', 'variance_percent', 'last_synced_at'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('boq_items', $col)) {
                    $t->dropColumn($col);
                }
            }
        });
    }
};
