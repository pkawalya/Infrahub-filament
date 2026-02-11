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
        Schema::create('document_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('project_documents')->onDelete('cascade');
            $table->foreignId('version_id')->nullable()->constrained('document_versions')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action'); // created, uploaded, viewed, downloaded, renamed, moved, locked, unlocked, archived, restored, deleted
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Extra context (old_name, new_name, from_folder, to_folder, etc.)
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['document_id', 'created_at']);
            $table->index('action');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_history');
    }
};
