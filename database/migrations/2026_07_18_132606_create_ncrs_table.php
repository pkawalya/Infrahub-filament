<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ncrs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cde_project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cde_document_id')->nullable()->constrained('cde_documents')->nullOnDelete();
            $table->string('ncr_number');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('product');
            $table->string('severity')->default('minor');
            $table->string('status')->default('open');
            $table->text('root_cause')->nullable();
            $table->text('corrective_action')->nullable();
            $table->text('preventive_action')->nullable();
            $table->text('verification_notes')->nullable();
            $table->text('closure_notes')->nullable();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['cde_project_id', 'status']);
            $table->index(['cde_project_id', 'severity']);
            $table->unique(['company_id', 'ncr_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ncrs');
    }
};
