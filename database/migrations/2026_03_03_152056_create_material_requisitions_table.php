<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('material_requisitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('cde_project_id')->nullable();
            $table->string('requisition_number');
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected, partially_issued, issued
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->string('purpose')->nullable();
            $table->date('required_date')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('cde_project_id')->references('id')->on('cde_projects')->nullOnDelete();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->nullOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('material_requisition_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_requisition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity_requested')->default(1);
            $table->integer('quantity_approved')->nullable();
            $table->integer('quantity_issued')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('material_issuances', function (Blueprint $table) {
            if (!Schema::hasColumn('material_issuances', 'material_requisition_id')) {
                $table->unsignedBigInteger('material_requisition_id')->nullable()->after('warehouse_id');
                $table->foreign('material_requisition_id')->references('id')->on('material_requisitions')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_issuances', function (Blueprint $table) {
            if (Schema::hasColumn('material_issuances', 'material_requisition_id')) {
                $table->dropForeign(['material_requisition_id']);
                $table->dropColumn('material_requisition_id');
            }
        });
        Schema::dropIfExists('material_requisition_items');
        Schema::dropIfExists('material_requisitions');
    }
};
