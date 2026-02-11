<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('boqs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('cde_project_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
            $t->string('name');
            $t->string('boq_number')->nullable();
            $t->text('description')->nullable();
            $t->string('status')->default('draft');
            $t->decimal('total_value', 14, 2)->default(0);
            $t->string('currency')->default('USD');
            $t->unsignedBigInteger('created_by')->nullable();
            $t->timestamps();
            $t->softDeletes();
            $t->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('boq_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('boq_id')->constrained()->cascadeOnDelete();
            $t->string('item_code')->nullable();
            $t->string('description');
            $t->string('unit')->nullable();
            $t->decimal('quantity', 12, 4)->default(0);
            $t->decimal('unit_rate', 12, 2)->default(0);
            $t->decimal('amount', 14, 2)->default(0);
            $t->string('category')->nullable();
            $t->integer('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('boq_revisions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('boq_id')->constrained()->cascadeOnDelete();
            $t->string('revision_number');
            $t->text('change_description')->nullable();
            $t->json('snapshot')->nullable();
            $t->unsignedBigInteger('created_by')->nullable();
            $t->timestamps();
            $t->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('boq_material_usages', function (Blueprint $t) {
            $t->id();
            $t->foreignId('boq_item_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $t->decimal('quantity_used', 12, 4)->default(0);
            $t->date('usage_date');
            $t->text('notes')->nullable();
            $t->unsignedBigInteger('recorded_by')->nullable();
            $t->timestamps();
            $t->foreign('recorded_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boq_material_usages');
        Schema::dropIfExists('boq_revisions');
        Schema::dropIfExists('boq_items');
        Schema::dropIfExists('boqs');
    }
};
