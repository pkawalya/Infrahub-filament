<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->timestamp('submitted_at')->nullable()->after('approved_by');
            $table->timestamp('approved_at')->nullable()->after('submitted_at');
            $table->text('rejection_reason')->nullable()->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['submitted_at', 'approved_at', 'rejection_reason']);
        });
    }
};
