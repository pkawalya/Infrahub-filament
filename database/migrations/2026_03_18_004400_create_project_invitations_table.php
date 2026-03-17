<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Pivot table: which users belong to which CDE projects
        Schema::create('cde_project_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cde_project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('member'); // member, engineer, supervisor, viewer
            $table->foreignId('invited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['cde_project_id', 'user_id']);
        });

        // Project invitations — one email can be invited to many projects
        Schema::create('project_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cde_project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invited_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('email');
            $table->string('name')->nullable();
            $table->string('role')->default('member');
            $table->string('token', 64)->unique();
            $table->string('status')->default('pending'); // pending, accepted, expired, revoked
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            // One email can be invited to many projects, but only one active invite per email+project
            $table->index(['cde_project_id', 'email']);
            $table->index(['email', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_invitations');
        Schema::dropIfExists('cde_project_members');
    }
};
