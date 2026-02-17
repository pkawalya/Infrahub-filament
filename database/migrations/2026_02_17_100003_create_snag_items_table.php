<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('snag_items')) {
            Schema::create('snag_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->foreignId('cde_project_id')->constrained()->cascadeOnDelete();
                $table->string('snag_number', 50);
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('category')->default('other');
                $table->string('severity')->default('minor');
                $table->string('status')->default('open');
                $table->string('location')->nullable();
                $table->string('trade', 100)->nullable();
                $table->date('due_date')->nullable();
                $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('resolved_at')->nullable();
                $table->timestamps();
                $table->index(['cde_project_id', 'status']);
                $table->index('snag_number');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('snag_items');
    }
};
