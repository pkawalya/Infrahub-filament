<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ──── CDE Projects ────
        Schema::create('cde_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('manager_id')->references('id')->on('users')->nullOnDelete();
        });

        // ──── CDE Folders ────
        Schema::create('cde_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cde_project_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name');
            $table->string('suitability_code')->nullable(); // S0-S7 per ISO 19650
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('parent_id')->references('id')->on('cde_folders')->nullOnDelete();
        });

        // ──── CDE Documents ────
        Schema::create('cde_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cde_folder_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cde_project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('document_number')->nullable();
            $table->string('revision')->default('A');
            $table->string('status')->default('wip'); // wip, shared, published, archived
            $table->string('discipline')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('uploaded_by')->references('id')->on('users')->nullOnDelete();
        });

        // ──── Document Versions ────
        Schema::create('cde_document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cde_document_id')->constrained()->cascadeOnDelete();
            $table->string('version_number');
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->text('change_description')->nullable();
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->foreign('uploaded_by')->references('id')->on('users')->nullOnDelete();
        });

        // ──── Document Comments ────
        Schema::create('cde_document_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cde_document_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('user_id');
            $table->text('comment');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('is_resolved')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('parent_id')->references('id')->on('cde_document_comments')->nullOnDelete();
        });

        // ──── RFIs (Requests for Information) ────
        Schema::create('rfis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cde_project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('rfi_number');
            $table->string('subject');
            $table->text('question');
            $table->text('answer')->nullable();
            $table->string('priority')->default('medium');
            $table->string('status')->default('open'); // open, answered, closed
            $table->unsignedBigInteger('raised_by')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('raised_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
        });

        // ──── Submittals ────
        Schema::create('submittals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cde_project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('submittal_number');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->nullable(); // shop_drawing, product_data, sample, etc.
            $table->string('status')->default('pending'); // pending, under_review, approved, rejected, resubmit
            $table->string('current_revision')->default('0');
            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->unsignedBigInteger('reviewer_id')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_comments')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('submitted_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('reviewer_id')->references('id')->on('users')->nullOnDelete();
        });

        // ──── Transmittals ────
        Schema::create('transmittals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cde_project_id')->nullable()->constrained()->nullOnDelete();
            $table->string('transmittal_number');
            $table->string('subject');
            $table->text('description')->nullable();
            $table->string('status')->default('draft'); // draft, sent, acknowledged
            $table->unsignedBigInteger('from_user_id')->nullable();
            $table->string('to_organization')->nullable();
            $table->string('to_contact')->nullable();
            $table->string('purpose')->nullable(); // for_approval, for_review, for_information, as_requested
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('from_user_id')->references('id')->on('users')->nullOnDelete();
        });

        // ──── Transmittal Items ────
        Schema::create('transmittal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transmittal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cde_document_id')->nullable()->constrained()->nullOnDelete();
            $table->string('description')->nullable();
            $table->integer('copies')->default(1);
            $table->timestamps();
        });

        // ──── Activity Logs ────
        Schema::create('cde_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->morphs('loggable');
            $table->string('action'); // created, updated, downloaded, viewed, shared
            $table->text('description')->nullable();
            $table->json('changes')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cde_activity_logs');
        Schema::dropIfExists('transmittal_items');
        Schema::dropIfExists('transmittals');
        Schema::dropIfExists('submittals');
        Schema::dropIfExists('rfis');
        Schema::dropIfExists('cde_document_comments');
        Schema::dropIfExists('cde_document_versions');
        Schema::dropIfExists('cde_documents');
        Schema::dropIfExists('cde_folders');
        Schema::dropIfExists('cde_projects');
    }
};
