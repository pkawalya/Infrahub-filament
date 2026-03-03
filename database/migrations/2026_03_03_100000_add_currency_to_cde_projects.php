<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cde_projects', function (Blueprint $table) {
            if (!Schema::hasColumn('cde_projects', 'currency')) {
                $table->string('currency', 10)->nullable()->after('budget');
            }
            if (!Schema::hasColumn('cde_projects', 'currency_symbol')) {
                $table->string('currency_symbol', 10)->nullable()->after('currency');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cde_projects', function (Blueprint $table) {
            $table->dropColumn(['currency', 'currency_symbol']);
        });
    }
};
