<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('goods_received_notes')) {
            Schema::create('goods_received_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('cde_project_id')->nullable();
                $table->unsignedBigInteger('purchase_order_id')->nullable();
                $table->string('grn_number')->unique();
                $table->unsignedBigInteger('supplier_id')->nullable();
                $table->unsignedBigInteger('warehouse_id')->nullable();
                $table->string('status')->default('draft'); // draft|inspecting|accepted|partial|rejected
                $table->date('received_date')->nullable();
                $table->string('delivery_note_ref')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('received_by')->nullable();
                $table->unsignedBigInteger('inspected_by')->nullable();
                $table->softDeletes();
                $table->timestamps();

                $table->foreign('cde_project_id')->references('id')->on('cde_projects')->nullOnDelete();
                $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->nullOnDelete();
                $table->foreign('supplier_id')->references('id')->on('suppliers')->nullOnDelete();
                $table->foreign('warehouse_id')->references('id')->on('warehouses')->nullOnDelete();
                $table->foreign('received_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('inspected_by')->references('id')->on('users')->nullOnDelete();

                $table->index(['company_id', 'status']);
                $table->index(['company_id', 'received_date']);
            });
        }

        if (! Schema::hasTable('grn_items')) {
            Schema::create('grn_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('goods_received_note_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('product_id')->nullable();
                $table->string('description')->nullable();
                $table->string('unit')->nullable();
                $table->decimal('quantity_expected', 12, 2)->default(0);
                $table->decimal('quantity_received', 12, 2)->default(0);
                $table->decimal('quantity_rejected', 12, 2)->default(0);
                $table->decimal('unit_cost', 12, 2)->nullable();
                $table->text('rejection_reason')->nullable();
                $table->timestamps();

                $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
                $table->index('goods_received_note_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('grn_items');
        Schema::dropIfExists('goods_received_notes');
    }
};
