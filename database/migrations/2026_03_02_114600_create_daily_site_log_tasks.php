<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('daily_site_log_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_site_log_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->integer('progress_today')->default(0)->comment('% progress made today');
            $table->integer('cumulative_progress')->nullable()->comment('Cumulative % after this update');
            $table->decimal('hours_worked', 6, 2)->default(0);
            $table->integer('workers_assigned')->default(0);
            $table->string('status_update')->nullable()->comment('not_started, in_progress, completed, blocked');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['daily_site_log_id', 'task_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_site_log_tasks');
    }
};
