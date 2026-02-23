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
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('cde_project_id')->nullable()->constrained('cde_projects')->nullOnDelete();
        });

        Schema::table('invoice_payments', function (Blueprint $table) {
            $table->foreignId('cde_project_id')->nullable()->constrained('cde_projects')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['cde_project_id']);
            $table->dropColumn('cde_project_id');
        });

        Schema::table('invoice_payments', function (Blueprint $table) {
            $table->dropForeign(['cde_project_id']);
            $table->dropColumn('cde_project_id');
        });
    }
};
