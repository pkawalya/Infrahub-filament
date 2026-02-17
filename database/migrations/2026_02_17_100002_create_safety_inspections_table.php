<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('safety_inspections')) {
            Schema::create('safety_inspections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->foreignId('cde_project_id')->constrained()->cascadeOnDelete();
                $table->foreignId('inspection_template_id')->nullable()->constrained()->nullOnDelete();
                $table->string('inspection_number', 50);
                $table->string('title');
                $table->string('type')->nullable();
                $table->string('status')->default('scheduled');
                $table->dateTime('scheduled_date');
                $table->dateTime('completed_date')->nullable();
                $table->string('location')->nullable();
                $table->integer('score')->nullable();
                $table->json('findings')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('inspector_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->index(['cde_project_id', 'status']);
                $table->index('inspection_number');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('safety_inspections');
    }
};
