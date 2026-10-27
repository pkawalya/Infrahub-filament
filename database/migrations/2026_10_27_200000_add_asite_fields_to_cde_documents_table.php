<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cde_documents', function (Blueprint $table) {
            if (!Schema::hasColumn('cde_documents', 'is_flagged')) {
                $table->boolean('is_flagged')->default(false)->after('status');
            }
            if (!Schema::hasColumn('cde_documents', 'due_date')) {
                $table->date('due_date')->nullable()->after('is_flagged');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cde_documents', function (Blueprint $table) {
            $table->dropColumn(['is_flagged', 'due_date']);
        });
    }
};
