<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add is_asset flag to products
        if (!Schema::hasColumn('products', 'is_asset')) {
            Schema::table('products', function (Blueprint $table) {
                $table->boolean('is_asset')->default(false)->after('track_inventory');
                $table->string('warranty_period')->nullable()->after('condition');
            });
        }

        // ── Expand existing assets table with new columns ──
        Schema::table('assets', function (Blueprint $table) {
            if (!Schema::hasColumn('assets', 'product_id')) {
                $table->unsignedBigInteger('product_id')->nullable()->after('company_id');
                $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
            }
            if (!Schema::hasColumn('assets', 'cde_project_id')) {
                $table->unsignedBigInteger('cde_project_id')->nullable()->after('product_id');
                $table->foreign('cde_project_id')->references('id')->on('cde_projects')->nullOnDelete();
            }
            if (!Schema::hasColumn('assets', 'asset_tag')) {
                $table->string('asset_tag')->nullable()->after('cde_project_id');
            }
            if (!Schema::hasColumn('assets', 'current_holder_id')) {
                $table->unsignedBigInteger('current_holder_id')->nullable()->after('condition');
                $table->foreign('current_holder_id')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('assets', 'current_location')) {
                $table->string('current_location')->nullable()->after('current_holder_id');
            }
            if (!Schema::hasColumn('assets', 'warehouse_id')) {
                $table->unsignedBigInteger('warehouse_id')->nullable()->after('current_location');
                $table->foreign('warehouse_id')->references('id')->on('warehouses')->nullOnDelete();
            }
            if (!Schema::hasColumn('assets', 'warranty_expiry')) {
                $table->date('warranty_expiry')->nullable()->after('purchase_cost');
            }
            if (!Schema::hasColumn('assets', 'depreciation_method')) {
                $table->string('depreciation_method')->default('straight_line')->after('warranty_expiry');
            }
            if (!Schema::hasColumn('assets', 'useful_life_years')) {
                $table->integer('useful_life_years')->default(5)->after('depreciation_method');
            }
            if (!Schema::hasColumn('assets', 'salvage_value')) {
                $table->decimal('salvage_value', 12, 2)->default(0)->after('useful_life_years');
            }
            if (!Schema::hasColumn('assets', 'qr_code')) {
                $table->string('qr_code')->nullable()->after('salvage_value');
            }
            if (!Schema::hasColumn('assets', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('notes');
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            }
        });

        // ── Asset Assignment History ──
        if (!Schema::hasTable('asset_assignments')) {
            Schema::create('asset_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
                $table->string('action'); // checkout, checkin, transfer, maintenance, retire, lost
                $table->unsignedBigInteger('assigned_to')->nullable();
                $table->string('assigned_to_name')->nullable();
                $table->unsignedBigInteger('assigned_from')->nullable();
                $table->string('location')->nullable();
                $table->unsignedBigInteger('project_id')->nullable();
                $table->string('condition_before')->nullable();
                $table->string('condition_after')->nullable();
                $table->date('checkout_date')->nullable();
                $table->date('expected_return_date')->nullable();
                $table->date('return_date')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('performed_by')->nullable();
                $table->timestamps();

                $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
                $table->foreign('assigned_from')->references('id')->on('users')->nullOnDelete();
                $table->foreign('project_id')->references('id')->on('cde_projects')->nullOnDelete();
                $table->foreign('performed_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        // ── Asset Maintenance Log ──
        if (!Schema::hasTable('asset_maintenance_logs')) {
            Schema::create('asset_maintenance_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
                $table->string('type');
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('status')->default('scheduled');
                $table->date('scheduled_date')->nullable();
                $table->date('completed_date')->nullable();
                $table->decimal('cost', 12, 2)->default(0);
                $table->string('vendor')->nullable();
                $table->string('condition_before')->nullable();
                $table->string('condition_after')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('performed_by')->nullable();
                $table->timestamps();

                $table->foreign('performed_by')->references('id')->on('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_maintenance_logs');
        Schema::dropIfExists('asset_assignments');
    }
};
