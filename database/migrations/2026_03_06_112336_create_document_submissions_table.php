<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('document_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cde_project_id')->constrained()->cascadeOnDelete();

            // What's required
            $table->string('title');           // e.g. "Site Investigation Report"
            $table->text('description')->nullable();
            $table->string('discipline')->nullable(); // Structural, MEP, etc.
            $table->string('stage');            // design, procurement, construction, testing, handover
            $table->date('due_date')->nullable();

            // Submission tracking
            $table->string('status')->default('pending'); // pending, submitted, approved, rejected, overdue, waived
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();

            // Uploaded file
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            // Notes / feedback
            $table->text('review_notes')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            $table->index(['cde_project_id', 'stage']);
            $table->index(['cde_project_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_submissions');
    }
};
