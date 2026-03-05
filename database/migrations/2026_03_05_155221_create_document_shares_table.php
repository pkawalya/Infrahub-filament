<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('document_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cde_document_id')->constrained('cde_documents')->cascadeOnDelete();
            $table->foreignId('shared_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('shared_with')->nullable()->constrained('users')->nullOnDelete();
            $table->string('share_token', 64)->unique()->nullable();
            $table->string('permission')->default('view'); // view, download, edit
            $table->string('shared_email')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->integer('access_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['cde_document_id', 'shared_with']);
            $table->index('share_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_shares');
    }
};
