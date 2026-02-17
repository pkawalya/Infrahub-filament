<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rfis', function (Blueprint $table) {
            $table->string('cost_impact')->nullable()->after('due_date');
            $table->string('schedule_impact')->nullable()->after('cost_impact');
        });
    }

    public function down(): void
    {
        Schema::table('rfis', function (Blueprint $table) {
            $table->dropColumn(['cost_impact', 'schedule_impact']);
        });
    }
};
