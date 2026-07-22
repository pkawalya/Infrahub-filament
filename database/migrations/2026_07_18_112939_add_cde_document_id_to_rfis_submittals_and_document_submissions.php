<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rfis', function (Blueprint $table) {
            $table->foreignId('cde_document_id')->nullable()->constrained('cde_documents')->nullOnDelete()->after('cde_project_id');
        });

        Schema::table('submittals', function (Blueprint $table) {
            $table->foreignId('cde_document_id')->nullable()->constrained('cde_documents')->nullOnDelete()->after('cde_project_id');
        });

        Schema::table('document_submissions', function (Blueprint $table) {
            $table->foreignId('cde_document_id')->nullable()->constrained('cde_documents')->nullOnDelete()->after('cde_project_id');
        });
    }

    public function down(): void
    {
        Schema::table('rfis', function (Blueprint $table) {
            $table->dropForeign(['cde_document_id']);
            $table->dropColumn('cde_document_id');
        });

        Schema::table('submittals', function (Blueprint $table) {
            $table->dropForeign(['cde_document_id']);
            $table->dropColumn('cde_document_id');
        });

        Schema::table('document_submissions', function (Blueprint $table) {
            $table->dropForeign(['cde_document_id']);
            $table->dropColumn('cde_document_id');
        });
    }
};
