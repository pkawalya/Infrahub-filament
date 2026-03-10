<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_site_diaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cde_project_id')->constrained('cde_projects')->cascadeOnDelete();

            $table->date('diary_date')->index();

            // Weather
            $table->string('weather')->nullable(); // sunny, cloudy, rainy, windy, stormy
            $table->decimal('temperature', 5, 1)->nullable();

            // Workforce summary
            $table->integer('workers_on_site')->default(0);
            $table->integer('subcontractor_workers')->default(0);
            $table->json('workforce_breakdown')->nullable(); // [{"trade":"Electricians","count":5},...]

            // Equipment on site
            $table->integer('equipment_on_site')->default(0);
            $table->json('equipment_list')->nullable(); // [{"name":"Excavator","hours":8},...]

            // Work summary
            $table->text('work_performed')->nullable();
            $table->text('work_planned_tomorrow')->nullable();

            // Issues & delays
            $table->text('delays')->nullable();
            $table->text('safety_observations')->nullable();
            $table->text('quality_observations')->nullable();
            $table->text('visitor_log')->nullable(); // "John Smith (OSHA Inspector) 10:00-12:00"
            $table->text('deliveries')->nullable();

            // Photos
            $table->json('photos')->nullable();

            // Sign-off
            $table->foreignId('prepared_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->unique(['company_id', 'cde_project_id', 'diary_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_site_diaries');
    }
};
