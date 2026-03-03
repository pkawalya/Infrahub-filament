<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cde_projects', function (Blueprint $table) {
            if (!Schema::hasColumn('cde_projects', 'currency_position')) {
                $table->string('currency_position', 10)->default('before')->after('currency_symbol');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cde_projects', function (Blueprint $table) {
            $table->dropColumn('currency_position');
        });
    }
};
