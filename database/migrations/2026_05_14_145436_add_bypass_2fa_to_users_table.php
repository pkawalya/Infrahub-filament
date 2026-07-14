<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'bypass_2fa')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('bypass_2fa')->default(false);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'bypass_2fa')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('bypass_2fa');
            });
        }
    }
};
