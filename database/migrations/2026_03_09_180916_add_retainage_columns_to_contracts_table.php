<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('contracts', 'retainage_percent')) {
                $table->decimal('retainage_percent', 5, 2)->default(0)->after('amount_paid');
            }
            if (!Schema::hasColumn('contracts', 'retainage_held')) {
                $table->decimal('retainage_held', 15, 2)->default(0)->after('retainage_percent');
            }
            if (!Schema::hasColumn('contracts', 'retainage_released')) {
                $table->decimal('retainage_released', 15, 2)->default(0)->after('retainage_held');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['retainage_percent', 'retainage_held', 'retainage_released']);
        });
    }
};
