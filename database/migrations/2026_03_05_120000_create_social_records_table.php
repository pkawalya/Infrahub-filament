<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('social_records', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->unsignedBigInteger('cde_project_id')->nullable();
            $t->string('record_number')->nullable();
            $t->string('title');
            $t->string('category'); // grievance, stakeholder_engagement, labour_welfare, training, csr_activity, community_impact, land_resettlement
            $t->string('priority')->default('normal'); // low, normal, high, urgent
            $t->string('status')->default('open'); // open, in_progress, resolved, closed
            $t->text('description')->nullable();
            $t->string('affected_party')->nullable(); // who is affected (community, workers, etc)
            $t->string('location')->nullable();
            $t->date('record_date')->nullable();
            $t->date('resolution_date')->nullable();
            $t->text('resolution_notes')->nullable();
            $t->text('follow_up_actions')->nullable();
            $t->unsignedBigInteger('reported_by')->nullable();
            $t->unsignedBigInteger('assigned_to')->nullable();
            $t->timestamps();

            $t->foreign('cde_project_id')->references('id')->on('cde_projects')->nullOnDelete();
            $t->foreign('reported_by')->references('id')->on('users')->nullOnDelete();
            $t->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();

            $t->index(['cde_project_id', 'category']);
            $t->index(['cde_project_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_records');
    }
};
