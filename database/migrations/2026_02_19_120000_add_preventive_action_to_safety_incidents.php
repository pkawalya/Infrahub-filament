<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('safety_incidents', function (Blueprint $table) {
            if (!Schema::hasColumn('safety_incidents', 'preventive_action')) {
                $table->text('preventive_action')->nullable()->after('corrective_action');
            }
        });
    }

    public function down(): void
    {
        Schema::table('safety_incidents', function (Blueprint $table) {
            $table->dropColumn('preventive_action');
        });
    }
};
