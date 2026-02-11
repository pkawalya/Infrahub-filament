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
        Schema::create('project_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('folder_id')->nullable()->constrained('project_folders')->onDelete('cascade');
            $table->string('title');
            $table->string('document_number')->nullable(); // For formal document numbering
            $table->text('description')->nullable();
            $table->string('file_type')->nullable(); // pdf, image, doc, excel, etc.
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('current_version_id')->nullable(); // Points to latest version
            $table->integer('version_count')->default(1);
            $table->enum('status', ['draft', 'active', 'archived', 'superseded'])->default('active');
            $table->boolean('is_locked')->default(false);
            $table->foreignId('locked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['project_id', 'folder_id']);
            $table->index('status');
            $table->index('file_type');
            $table->fullText(['title', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_documents');
    }
};
