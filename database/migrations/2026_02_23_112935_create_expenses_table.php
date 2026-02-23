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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cde_project_id')->nullable()->constrained('cde_projects')->nullOnDelete();
            $table->string('title');
            $table->string('reference_number')->nullable();
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->string('category')->nullable(); // e.g., materials, labor, travel
            $table->string('status')->default('pending'); // pending, approved, paid, rejected
            $table->text('description')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
