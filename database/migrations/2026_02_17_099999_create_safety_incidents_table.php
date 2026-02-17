<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('safety_incidents')) {
            Schema::create('safety_incidents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->foreignId('cde_project_id')->constrained()->cascadeOnDelete();
                $table->string('incident_number', 50);
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('type')->nullable();
                $table->string('severity')->default('minor');
                $table->string('status')->default('reported');
                $table->string('location')->nullable();
                $table->dateTime('incident_date');
                $table->text('root_cause')->nullable();
                $table->text('corrective_action')->nullable();
                $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('investigated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['cde_project_id', 'status']);
                $table->index('incident_number');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('safety_incidents');
    }
};
