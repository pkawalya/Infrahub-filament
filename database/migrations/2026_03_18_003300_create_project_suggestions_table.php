<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cde_project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_anonymous')->default(true);
            $table->string('category')->default('general');
            $table->text('content');
            $table->string('status')->default('new'); // new, reviewed, in_progress, implemented, dismissed
            $table->text('admin_response')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('responded_at')->nullable();
            $table->unsignedInteger('upvotes')->default(0);
            $table->timestamps();

            $table->index(['cde_project_id', 'status']);
            $table->index(['company_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_suggestions');
    }
};
