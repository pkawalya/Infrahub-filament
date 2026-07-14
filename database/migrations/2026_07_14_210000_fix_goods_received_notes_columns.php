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
        if (Schema::hasTable('goods_received_notes')) {
            Schema::table('goods_received_notes', function (Blueprint $table) {
                if (!Schema::hasColumn('goods_received_notes', 'delivery_date')) {
                    $table->date('delivery_date')->nullable()->after('received_date');
                }
                if (!Schema::hasColumn('goods_received_notes', 'carrier_name')) {
                    $table->string('carrier_name')->nullable()->after('delivery_date');
                }
                if (!Schema::hasColumn('goods_received_notes', 'vehicle_plate')) {
                    $table->string('vehicle_plate')->nullable()->after('carrier_name');
                }
                if (!Schema::hasColumn('goods_received_notes', 'driver_name')) {
                    $table->string('driver_name')->nullable()->after('vehicle_plate');
                }
                if (!Schema::hasColumn('goods_received_notes', 'inspector_id')) {
                    $table->unsignedBigInteger('inspector_id')->nullable()->after('driver_name');
                    $table->foreign('inspector_id')->references('id')->on('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('goods_received_notes', 'inspection_passed')) {
                    $table->boolean('inspection_passed')->default(true)->after('inspector_id');
                }
                if (!Schema::hasColumn('goods_received_notes', 'invoice_reference')) {
                    $table->string('invoice_reference')->nullable()->after('inspection_passed')
                        ->comment('Supplier invoice number for URA audit matching');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('goods_received_notes')) {
            Schema::table('goods_received_notes', function (Blueprint $table) {
                $columns = ['invoice_reference', 'inspection_passed', 'inspector_id', 'driver_name', 'vehicle_plate', 'carrier_name', 'delivery_date'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('goods_received_notes', $col)) {
                        if ($col === 'inspector_id') {
                            try {
                                $table->dropForeign(['inspector_id']);
                            } catch (\Exception $e) {
                                // Ignore if constraint doesn't exist
                            }
                        }
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
