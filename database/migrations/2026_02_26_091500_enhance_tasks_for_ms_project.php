<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // ── WBS & Hierarchy ──
            $table->string('wbs_code', 50)->nullable()->after('sort_order')
                ->comment('Work Breakdown Structure code e.g. 1.2.3');
            $table->unsignedTinyInteger('outline_level')->default(0)->after('wbs_code')
                ->comment('Indent level in WBS (0 = top level)');
            $table->boolean('is_summary')->default(false)->after('outline_level')
                ->comment('True if task has children (auto-computed)');
            $table->boolean('is_milestone')->default(false)->after('is_summary')
                ->comment('True if task is a milestone (zero duration)');

            // ── Scheduling ──
            $table->integer('duration_days')->nullable()->after('due_date')
                ->comment('Task duration in working days');
            $table->string('constraint_type', 30)->nullable()->after('duration_days')
                ->comment('ASAP, ALAP, MSO, MFO, SNET, SNLT, FNET, FNLT');
            $table->date('constraint_date')->nullable()->after('constraint_type');

            // ── Baseline Tracking ──
            $table->date('baseline_start')->nullable()->after('constraint_date');
            $table->date('baseline_finish')->nullable()->after('baseline_start');
            $table->integer('baseline_duration')->nullable()->after('baseline_finish')
                ->comment('Baseline duration in days');
            $table->decimal('baseline_cost', 14, 2)->nullable()->after('baseline_duration');
            $table->decimal('baseline_work', 10, 2)->nullable()->after('baseline_cost')
                ->comment('Baseline effort in hours');

            // ── Cost ──
            $table->decimal('fixed_cost', 14, 2)->default(0)->after('actual_hours');
            $table->decimal('cost_rate', 10, 2)->nullable()->after('fixed_cost')
                ->comment('Cost per hour for resource');
            $table->decimal('actual_cost', 14, 2)->default(0)->after('cost_rate');

            // ── Resource ──
            $table->string('resource_names', 500)->nullable()->after('assigned_to')
                ->comment('Comma-separated resource names for display');
            $table->unsignedSmallInteger('resource_units')->default(100)->after('resource_names')
                ->comment('Percentage allocation (100 = full time)');

            // ── Notes / Calendar ──
            $table->text('notes')->nullable()->after('description');
            $table->string('calendar', 50)->nullable()->after('notes')
                ->comment('Calendar name for working days (null = project default)');

            // ── Dates as actual dates ──
            $table->date('actual_start')->nullable()->after('start_date');
            $table->date('actual_finish')->nullable()->after('due_date');

            // ── Earned Value ──
            $table->decimal('bcws', 14, 2)->nullable()->comment('Budgeted Cost of Work Scheduled');
            $table->decimal('bcwp', 14, 2)->nullable()->comment('Budgeted Cost of Work Performed');
            $table->decimal('acwp', 14, 2)->nullable()->comment('Actual Cost of Work Performed');

            // ── Indexes for performance ──
            $table->index(['cde_project_id', 'wbs_code']);
            $table->index(['cde_project_id', 'outline_level']);
            $table->index(['cde_project_id', 'is_summary']);
            $table->index(['cde_project_id', 'is_milestone']);
        });

        // ── Enhance task_dependencies with lag and dependency types ──
        Schema::table('task_dependencies', function (Blueprint $table) {
            $table->integer('lag_days')->default(0)->after('dependency_type')
                ->comment('Lead (<0) or Lag (>0) time in days');
        });

        // ── Project-level scheduling settings ──
        Schema::table('cde_projects', function (Blueprint $table) {
            $table->date('baseline_saved_at')->nullable()->after('end_date');
            $table->string('schedule_mode', 20)->default('auto')->after('baseline_saved_at')
                ->comment('auto or manual scheduling');
            $table->string('default_calendar', 50)->default('standard')->after('schedule_mode');
            $table->json('working_days')->nullable()->after('default_calendar')
                ->comment('Array of working day configs, holidays, etc.');
            $table->decimal('project_cost', 14, 2)->nullable()->after('budget');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['cde_project_id', 'wbs_code']);
            $table->dropIndex(['cde_project_id', 'outline_level']);
            $table->dropIndex(['cde_project_id', 'is_summary']);
            $table->dropIndex(['cde_project_id', 'is_milestone']);

            $table->dropColumn([
                'wbs_code',
                'outline_level',
                'is_summary',
                'is_milestone',
                'duration_days',
                'constraint_type',
                'constraint_date',
                'baseline_start',
                'baseline_finish',
                'baseline_duration',
                'baseline_cost',
                'baseline_work',
                'fixed_cost',
                'cost_rate',
                'actual_cost',
                'resource_names',
                'resource_units',
                'notes',
                'calendar',
                'actual_start',
                'actual_finish',
                'bcws',
                'bcwp',
                'acwp',
            ]);
        });

        Schema::table('task_dependencies', function (Blueprint $table) {
            $table->dropColumn('lag_days');
        });

        Schema::table('cde_projects', function (Blueprint $table) {
            $table->dropColumn([
                'baseline_saved_at',
                'schedule_mode',
                'default_calendar',
                'working_days',
                'project_cost',
            ]);
        });
    }
};
