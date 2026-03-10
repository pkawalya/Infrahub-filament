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
        // Fuel Logs for heavy machinery/generators
        Schema::create('equipment_fuel_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignId('cde_project_id')->nullable()->constrained('cde_projects')->nullOnDelete();

            $table->date('log_date');
            $table->decimal('liters', 10, 2);
            $table->decimal('cost_per_liter', 10, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->decimal('meter_reading', 10, 1)->nullable()
                ->comment('Hour meter or odometer at time of fueling');

            $table->string('filled_by')->nullable();
            $table->string('supplier')->nullable();
            $table->string('receipt_path')->nullable(); // Image of receipt
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['company_id', 'asset_id']);
        });

        // Equipment Allocations (where is this piece of plant today?)
        Schema::create('equipment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignId('cde_project_id')->nullable()->constrained('cde_projects')->nullOnDelete();
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();

            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status')->default('active'); // active, completed, cancelled

            $table->decimal('daily_rate', 10, 2)->nullable()
                ->comment('Internal cross-charge rate per day');

            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['company_id', 'asset_id']);
            $table->index(['company_id', 'cde_project_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plant_and_equipment_tables');
    }
};
