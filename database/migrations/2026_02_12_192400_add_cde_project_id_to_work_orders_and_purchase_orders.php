<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->foreignId('cde_project_id')->nullable()->after('company_id')
                ->constrained('cde_projects')->nullOnDelete();
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreignId('cde_project_id')->nullable()->after('company_id')
                ->constrained('cde_projects')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropForeign(['cde_project_id']);
            $table->dropColumn('cde_project_id');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['cde_project_id']);
            $table->dropColumn('cde_project_id');
        });
    }
};
