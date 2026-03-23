<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('project_suggestions', function (Blueprint $table) {
            $table->foreignId('cde_project_id')->nullable()->change();
            $table->foreignId('company_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_suggestions', function (Blueprint $table) {
            $table->dropColumn('priority');
            $table->foreignId('cde_project_id')->nullable(false)->change();
        });
    }
};
