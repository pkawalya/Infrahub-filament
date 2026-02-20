<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'assigned_to')) {
                $table->unsignedBigInteger('assigned_to')->nullable()->after('created_by');
                $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('tasks', 'work_order_id')) {
                $table->unsignedBigInteger('work_order_id')->nullable()->after('cde_project_id');
                $table->foreign('work_order_id')->references('id')->on('work_orders')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn('assigned_to');
            $table->dropForeign(['work_order_id']);
            $table->dropColumn('work_order_id');
        });
    }
};
