<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('boq_items', function (Blueprint $t) {
            $t->decimal('quantity_completed', 12, 4)->default(0)->after('quantity');
            $t->text('remarks')->nullable()->after('category');
            $t->boolean('is_variation')->default(false)->after('remarks');
        });
        Schema::table('boqs', function (Blueprint $t) {
            $t->text('notes')->nullable()->after('currency');
            $t->unsignedBigInteger('approved_by')->nullable()->after('notes');
            $t->timestamp('approved_at')->nullable()->after('approved_by');
            $t->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('boq_items', function (Blueprint $t) {
            $t->dropColumn(['quantity_completed', 'remarks', 'is_variation']);
        });
        Schema::table('boqs', function (Blueprint $t) {
            $t->dropForeign(['approved_by']);
            $t->dropColumn(['notes', 'approved_by', 'approved_at']);
        });
    }
};
