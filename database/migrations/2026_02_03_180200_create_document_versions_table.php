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
        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('project_documents')->onDelete('cascade');
            $table->string('version_number'); // e.g., "1.0", "1.1", "2.0"
            $table->integer('major_version')->default(1);
            $table->integer('minor_version')->default(0);
            $table->string('file_path'); // Storage path
            $table->string('original_filename');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size'); // in bytes
            $table->string('file_hash')->nullable(); // MD5 or SHA for duplicate detection
            $table->text('change_notes')->nullable(); // What changed in this version
            $table->enum('change_type', ['initial', 'revision', 'correction', 'supersede'])->default('initial');
            $table->boolean('is_current')->default(true);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index(['document_id', 'is_current']);
            $table->index('file_hash');
            $table->index('version_number');
        });

        // Add foreign key for current_version_id after document_versions table exists
        Schema::table('project_documents', function (Blueprint $table) {
            $table->foreign('current_version_id')
                ->references('id')
                ->on('document_versions')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_documents', function (Blueprint $table) {
            $table->dropForeign(['current_version_id']);
        });

        Schema::dropIfExists('document_versions');
    }
};
