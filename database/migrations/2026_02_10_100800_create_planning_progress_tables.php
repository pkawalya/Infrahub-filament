<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('cde_project_id')->nullable()->constrained()->nullOnDelete();
            $t->string('name');
            $t->text('description')->nullable();
            $t->date('start_date')->nullable();
            $t->date('end_date')->nullable();
            $t->string('status')->default('draft');
            $t->unsignedBigInteger('created_by')->nullable();
            $t->timestamps();
            $t->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('schedule_activities', function (Blueprint $t) {
            $t->id();
            $t->foreignId('schedule_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('wbs_code')->nullable();
            $t->date('planned_start')->nullable();
            $t->date('planned_finish')->nullable();
            $t->date('actual_start')->nullable();
            $t->date('actual_finish')->nullable();
            $t->integer('duration_days')->default(0);
            $t->integer('progress_percent')->default(0);
            $t->unsignedBigInteger('parent_id')->nullable();
            $t->unsignedBigInteger('assigned_to')->nullable();
            $t->integer('sort_order')->default(0);
            $t->timestamps();
            $t->foreign('parent_id')->references('id')->on('schedule_activities')->nullOnDelete();
            $t->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('milestones', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('cde_project_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('schedule_id')->nullable()->constrained()->nullOnDelete();
            $t->string('name');
            $t->text('description')->nullable();
            $t->date('target_date');
            $t->date('actual_date')->nullable();
            $t->string('status')->default('upcoming'); // upcoming, achieved, missed
            $t->string('priority')->default('medium');
            $t->timestamps();
        });

        Schema::create('timesheets', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('cde_project_id')->nullable()->constrained()->nullOnDelete();
            $t->date('date');
            $t->decimal('regular_hours', 5, 2)->default(0);
            $t->decimal('overtime_hours', 5, 2)->default(0);
            $t->string('status')->default('draft'); // draft, submitted, approved, rejected
            $t->text('notes')->nullable();
            $t->unsignedBigInteger('approved_by')->nullable();
            $t->timestamps();
            $t->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('lookahead_plans', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('cde_project_id')->nullable()->constrained()->nullOnDelete();
            $t->string('name');
            $t->date('period_start');
            $t->date('period_end');
            $t->string('status')->default('draft');
            $t->unsignedBigInteger('created_by')->nullable();
            $t->timestamps();
            $t->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('lookahead_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('lookahead_plan_id')->constrained()->cascadeOnDelete();
            $t->foreignId('schedule_activity_id')->nullable()->constrained()->nullOnDelete();
            $t->string('description');
            $t->date('planned_date')->nullable();
            $t->string('status')->default('planned');
            $t->text('constraints')->nullable();
            $t->unsignedBigInteger('assigned_to')->nullable();
            $t->timestamps();
            $t->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('progress_updates', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('cde_project_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('schedule_activity_id')->nullable()->constrained()->nullOnDelete();
            $t->date('update_date');
            $t->integer('progress_percent')->default(0);
            $t->text('description')->nullable();
            $t->json('photos')->nullable();
            $t->unsignedBigInteger('reported_by')->nullable();
            $t->timestamps();
            $t->foreign('reported_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_updates');
        Schema::dropIfExists('lookahead_items');
        Schema::dropIfExists('lookahead_plans');
        Schema::dropIfExists('timesheets');
        Schema::dropIfExists('milestones');
        Schema::dropIfExists('schedule_activities');
        Schema::dropIfExists('schedules');
    }
};
