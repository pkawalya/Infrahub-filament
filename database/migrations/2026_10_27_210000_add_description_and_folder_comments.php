<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cde_folders', function (Blueprint $table) {
            if (!Schema::hasColumn('cde_folders', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
        });

        Schema::table('cde_document_comments', function (Blueprint $table) {
            if (!Schema::hasColumn('cde_document_comments', 'cde_folder_id')) {
                $table->foreignId('cde_folder_id')->nullable()->constrained('cde_folders')->cascadeOnDelete()->after('cde_document_id');
            }
            $table->unsignedBigInteger('cde_document_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('cde_folders', function (Blueprint $table) {
            if (Schema::hasColumn('cde_folders', 'description')) {
                $table->dropColumn('description');
            }
        });

        Schema::table('cde_document_comments', function (Blueprint $table) {
            if (Schema::hasColumn('cde_document_comments', 'cde_folder_id')) {
                $table->dropForeign(['cde_folder_id']);
                $table->dropColumn('cde_folder_id');
            }
        });
    }
};
