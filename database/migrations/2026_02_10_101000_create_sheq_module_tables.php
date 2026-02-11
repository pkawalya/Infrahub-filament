<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('safety_incidents', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('cde_project_id')->nullable()->constrained()->nullOnDelete();
            $t->string('incident_number');
            $t->string('title');
            $t->text('description')->nullable();
            $t->string('type')->nullable(); // near_miss, first_aid, medical, lost_time, fatality
            $t->string('severity')->default('low'); // low, medium, high, critical
            $t->string('status')->default('reported'); // reported, investigating, resolved, closed
            $t->string('location')->nullable();
            $t->dateTime('incident_date');
            $t->text('root_cause')->nullable();
            $t->text('corrective_action')->nullable();
            $t->unsignedBigInteger('reported_by')->nullable();
            $t->unsignedBigInteger('investigated_by')->nullable();
            $t->timestamps();
            $t->foreign('reported_by')->references('id')->on('users')->nullOnDelete();
            $t->foreign('investigated_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('inspection_templates', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->text('description')->nullable();
            $t->string('type')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
        });

        Schema::create('inspection_checklist_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('inspection_template_id')->constrained()->cascadeOnDelete();
            $t->string('item');
            $t->string('category')->nullable();
            $t->boolean('is_required')->default(false);
            $t->integer('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('safety_inspections', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('cde_project_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('inspection_template_id')->nullable()->constrained()->nullOnDelete();
            $t->string('inspection_number');
            $t->string('title');
            $t->string('type')->nullable();
            $t->string('status')->default('scheduled'); // scheduled, in_progress, completed
            $t->dateTime('scheduled_date');
            $t->dateTime('completed_date')->nullable();
            $t->string('location')->nullable();
            $t->integer('score')->nullable();
            $t->json('findings')->nullable();
            $t->text('notes')->nullable();
            $t->unsignedBigInteger('inspector_id')->nullable();
            $t->timestamps();
            $t->foreign('inspector_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('permits_to_work', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('cde_project_id')->nullable()->constrained()->nullOnDelete();
            $t->string('permit_number');
            $t->string('title');
            $t->string('type')->nullable(); // hot_work, confined_space, working_at_height, excavation
            $t->string('status')->default('draft'); // draft, submitted, approved, active, closed, expired
            $t->text('work_description')->nullable();
            $t->string('location')->nullable();
            $t->dateTime('valid_from')->nullable();
            $t->dateTime('valid_to')->nullable();
            $t->json('hazards')->nullable();
            $t->json('precautions')->nullable();
            $t->unsignedBigInteger('requested_by')->nullable();
            $t->unsignedBigInteger('approved_by')->nullable();
            $t->timestamps();
            $t->foreign('requested_by')->references('id')->on('users')->nullOnDelete();
            $t->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('toolbox_talks', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('cde_project_id')->nullable()->constrained()->nullOnDelete();
            $t->string('title');
            $t->string('topic')->nullable();
            $t->text('content')->nullable();
            $t->dateTime('conducted_date');
            $t->string('location')->nullable();
            $t->integer('attendee_count')->default(0);
            $t->json('attendees')->nullable();
            $t->unsignedBigInteger('conducted_by')->nullable();
            $t->timestamps();
            $t->foreign('conducted_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('snag_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('cde_project_id')->nullable()->constrained()->nullOnDelete();
            $t->string('snag_number');
            $t->string('title');
            $t->text('description')->nullable();
            $t->string('category')->nullable();
            $t->string('severity')->default('minor'); // minor, major, critical
            $t->string('status')->default('open'); // open, in_progress, resolved, verified, closed
            $t->string('location')->nullable();
            $t->string('trade')->nullable();
            $t->date('due_date')->nullable();
            $t->unsignedBigInteger('reported_by')->nullable();
            $t->unsignedBigInteger('assigned_to')->nullable();
            $t->timestamp('resolved_at')->nullable();
            $t->timestamps();
            $t->foreign('reported_by')->references('id')->on('users')->nullOnDelete();
            $t->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('snag_items');
        Schema::dropIfExists('toolbox_talks');
        Schema::dropIfExists('permits_to_work');
        Schema::dropIfExists('safety_inspections');
        Schema::dropIfExists('inspection_checklist_items');
        Schema::dropIfExists('inspection_templates');
        Schema::dropIfExists('safety_incidents');
    }
};
