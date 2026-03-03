<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cde_project_id')->constrained('cde_projects')->cascadeOnDelete();
            $table->string('dn_number', 50)->index();
            $table->foreignId('material_issuance_id')->nullable()->constrained('material_issuances')->nullOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->string('destination')->nullable();           // site name or address
            $table->string('destination_contact')->nullable();   // receiver name
            $table->string('destination_phone', 30)->nullable();
            $table->string('vehicle_number', 50)->nullable();
            $table->string('driver_name', 100)->nullable();
            $table->string('driver_phone', 30)->nullable();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->string('status', 30)->default('draft');      // draft, dispatched, in_transit, delivered, partial
            $table->date('dispatch_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->text('notes')->nullable();
            $table->text('delivery_proof')->nullable();          // file path for signed proof
            $table->foreignId('dispatched_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('received_by_user')->nullable()->constrained('users')->nullOnDelete();
            $table->string('received_by_name', 150)->nullable(); // external receiver name
            $table->string('received_by_signature')->nullable(); // file path
            $table->foreignId('milestone_id')->nullable()->constrained('milestones')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('delivery_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_note_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description')->nullable();
            $table->string('unit', 30)->default('each');
            $table->decimal('quantity_dispatched', 12, 2)->default(0);
            $table->decimal('quantity_received', 12, 2)->default(0);
            $table->string('condition', 30)->default('good');    // good, damaged, missing
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // Product tracking — link products to milestones/tasks
        Schema::create('product_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cde_project_id')->constrained('cde_projects')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('stage', 50);                          // ordered, received, stored, issued, delivered, installed
            $table->foreignId('milestone_id')->nullable()->constrained('milestones')->nullOnDelete();
            $table->foreignId('task_id')->nullable()->constrained('tasks')->nullOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->foreignId('delivery_note_id')->nullable()->constrained('delivery_notes')->nullOnDelete();
            $table->foreignId('material_issuance_id')->nullable()->constrained('material_issuances')->nullOnDelete();
            $table->decimal('quantity', 12, 2)->default(0);
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_tracking');
        Schema::dropIfExists('delivery_note_items');
        Schema::dropIfExists('delivery_notes');
    }
};
