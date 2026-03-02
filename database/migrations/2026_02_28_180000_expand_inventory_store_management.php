<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add QR code field to products
        if (!Schema::hasColumn('products', 'qr_code')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('qr_code')->nullable()->after('barcode');
                $table->string('brand')->nullable()->after('name');
                $table->string('model_number')->nullable()->after('brand');
                $table->string('serial_number')->nullable()->after('model_number');
                $table->string('location')->nullable()->after('track_inventory');
                $table->string('condition')->default('new')->after('location'); // new, good, fair, poor, damaged
            });
        }

        // ── Material Issuances (who received what) ──
        if (!Schema::hasTable('material_issuances')) {
            Schema::create('material_issuances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('cde_project_id')->nullable();
                $table->string('issuance_number');
                $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('issued_to')->nullable(); // user receiving the items
                $table->string('issued_to_name')->nullable(); // if external person
                $table->string('purpose')->nullable(); // site_use, tool_checkout, maintenance, return
                $table->string('status')->default('draft'); // draft, issued, returned, partial_return
                $table->date('issue_date')->nullable();
                $table->date('expected_return_date')->nullable();
                $table->date('actual_return_date')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamps();

                $table->foreign('cde_project_id')->references('id')->on('cde_projects')->nullOnDelete();
                $table->foreign('issued_to')->references('id')->on('users')->nullOnDelete();
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        // ── Material Issuance Items ──
        if (!Schema::hasTable('material_issuance_items')) {
            Schema::create('material_issuance_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('material_issuance_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->integer('quantity_issued')->default(0);
                $table->integer('quantity_returned')->default(0);
                $table->string('condition_on_issue')->default('good');
                $table->string('condition_on_return')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('material_issuance_items');
        Schema::dropIfExists('material_issuances');

        if (Schema::hasColumn('products', 'qr_code')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn(['qr_code', 'brand', 'model_number', 'serial_number', 'location', 'condition']);
            });
        }
    }
};
