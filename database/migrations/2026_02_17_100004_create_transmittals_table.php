<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('transmittals')) {
            Schema::create('transmittals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->foreignId('cde_project_id')->constrained()->cascadeOnDelete();
                $table->string('transmittal_number', 50);
                $table->string('subject');
                $table->text('description')->nullable();
                $table->string('status')->default('draft');
                $table->foreignId('from_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('to_organization')->nullable();
                $table->string('to_contact')->nullable();
                $table->string('purpose')->default('for_information');
                $table->dateTime('sent_at')->nullable();
                $table->dateTime('acknowledged_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['cde_project_id', 'status']);
                $table->index('transmittal_number');
            });
        }

        if (!Schema::hasTable('transmittal_items')) {
            Schema::create('transmittal_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('transmittal_id')->constrained()->cascadeOnDelete();
                $table->foreignId('cde_document_id')->constrained()->cascadeOnDelete();
                $table->integer('copies')->default(1);
                $table->text('remarks')->nullable();
                $table->timestamps();
                $table->unique(['transmittal_id', 'cde_document_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transmittal_items');
        Schema::dropIfExists('transmittals');
    }
};
