<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Add the polymorphic notifiable columns that Laravel/Filament expects
            $table->string('notifiable_type')->nullable()->after('id');
            $table->unsignedBigInteger('notifiable_id')->nullable()->after('notifiable_type');

            $table->index(['notifiable_type', 'notifiable_id']);
        });

        // Backfill existing rows: map user_id -> notifiable morph columns
        DB::table('notifications')
            ->whereNotNull('user_id')
            ->update([
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => DB::raw('user_id'),
            ]);
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['notifiable_type', 'notifiable_id']);
            $table->dropColumn(['notifiable_type', 'notifiable_id']);
        });
    }
};
