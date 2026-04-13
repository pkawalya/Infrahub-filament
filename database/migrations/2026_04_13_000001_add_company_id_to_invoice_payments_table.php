<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_payments', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('cde_project_id');
            $table->foreign('company_id')
                  ->references('id')->on('companies')
                  ->onDelete('set null');
        });

        // Back-fill company_id from the parent invoice
        DB::statement('
            UPDATE invoice_payments ip
            JOIN invoices i ON i.id = ip.invoice_id
            SET ip.company_id = i.company_id
            WHERE ip.company_id IS NULL
        ');
    }

    public function down(): void
    {
        Schema::table('invoice_payments', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
