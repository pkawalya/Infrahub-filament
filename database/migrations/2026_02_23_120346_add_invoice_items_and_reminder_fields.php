<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('reminder_sent_at')->nullable();
            $table->integer('reminder_count')->default(0);
            $table->text('terms_and_conditions')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['reminder_sent_at', 'reminder_count', 'terms_and_conditions']);
        });
    }
};
