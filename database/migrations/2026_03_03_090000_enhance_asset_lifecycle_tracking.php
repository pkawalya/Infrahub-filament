<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── Enhanced Maintenance Logs ──
        Schema::table('asset_maintenance_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('asset_maintenance_logs', 'priority')) {
                $table->string('priority')->default('normal')->after('type');
            }
            if (!Schema::hasColumn('asset_maintenance_logs', 'downtime_hours')) {
                $table->decimal('downtime_hours', 8, 1)->nullable()->after('cost');
            }
            if (!Schema::hasColumn('asset_maintenance_logs', 'next_service_date')) {
                $table->date('next_service_date')->nullable()->after('completed_date');
            }
            if (!Schema::hasColumn('asset_maintenance_logs', 'parts_used')) {
                $table->text('parts_used')->nullable()->after('vendor');
            }
            if (!Schema::hasColumn('asset_maintenance_logs', 'meter_reading')) {
                $table->decimal('meter_reading', 12, 1)->nullable()->after('downtime_hours');
            }
        });

        // ── Asset: meter reading + replacement chain ──
        Schema::table('assets', function (Blueprint $table) {
            if (!Schema::hasColumn('assets', 'meter_reading')) {
                $table->decimal('meter_reading', 12, 1)->nullable()->after('condition');
            }
            if (!Schema::hasColumn('assets', 'meter_unit')) {
                $table->string('meter_unit')->nullable()->after('meter_reading'); // hours, km, miles
            }
            if (!Schema::hasColumn('assets', 'last_service_date')) {
                $table->date('last_service_date')->nullable()->after('warranty_expiry');
            }
            if (!Schema::hasColumn('assets', 'next_service_date')) {
                $table->date('next_service_date')->nullable()->after('last_service_date');
            }
            if (!Schema::hasColumn('assets', 'replaced_by_id')) {
                $table->unsignedBigInteger('replaced_by_id')->nullable()->after('notes');
                $table->foreign('replaced_by_id')->references('id')->on('assets')->nullOnDelete();
            }
            if (!Schema::hasColumn('assets', 'replaces_id')) {
                $table->unsignedBigInteger('replaces_id')->nullable()->after('replaced_by_id');
                $table->foreign('replaces_id')->references('id')->on('assets')->nullOnDelete();
            }
        });

        // ── Asset Assignment: meter reading at time of action ──
        Schema::table('asset_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('asset_assignments', 'meter_reading')) {
                $table->decimal('meter_reading', 12, 1)->nullable()->after('condition_after');
            }
        });
    }

    public function down(): void
    {
        Schema::table('asset_maintenance_logs', function (Blueprint $table) {
            $table->dropColumn(['priority', 'downtime_hours', 'next_service_date', 'parts_used', 'meter_reading']);
        });
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['replaced_by_id']);
            $table->dropForeign(['replaces_id']);
            $table->dropColumn(['meter_reading', 'meter_unit', 'last_service_date', 'next_service_date', 'replaced_by_id', 'replaces_id']);
        });
        Schema::table('asset_assignments', function (Blueprint $table) {
            $table->dropColumn(['meter_reading']);
        });
    }
};
