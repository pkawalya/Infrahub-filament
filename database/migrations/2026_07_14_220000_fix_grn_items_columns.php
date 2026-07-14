<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('grn_items')) {
            Schema::table('grn_items', function (Blueprint $table) {
                if (!Schema::hasColumn('grn_items', 'purchase_order_item_id')) {
                    $table->unsignedBigInteger('purchase_order_item_id')->nullable()->after('goods_received_note_id');
                    $table->foreign('purchase_order_item_id')->references('id')->on('purchase_order_items')->nullOnDelete();
                }
                if (!Schema::hasColumn('grn_items', 'quantity_accepted')) {
                    $table->decimal('quantity_accepted', 12, 2)->default(0.00)->after('quantity_received');
                }
                if (!Schema::hasColumn('grn_items', 'condition')) {
                    $table->string('condition', 30)->default('good')->after('quantity_rejected');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('grn_items')) {
            Schema::table('grn_items', function (Blueprint $table) {
                $columns = ['condition', 'quantity_accepted', 'purchase_order_item_id'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('grn_items', $col)) {
                        if ($col === 'purchase_order_item_id') {
                            try {
                                $table->dropForeign(['purchase_order_item_id']);
                            } catch (\Exception $e) {
                                // Ignore if constraint doesn't exist
                            }
                        }
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
