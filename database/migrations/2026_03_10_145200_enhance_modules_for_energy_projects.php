<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Enhance existing modules for Energy Project support.
 *
 * 1. cde_projects       → project_type, sector, capacity, voltage, grid_connection
 * 2. daily_site_diaries  → environmental monitoring (humidity, wind, noise, dust, water)
 * 3. safety_incidents    → permit to work (PTW) fields
 * 4. work_orders         → testing/inspection & commissioning fields
 * 5. snag_items          → commissioning punch list fields
 * 6. tasks               → commissioning phase tracking
 */
return new class extends Migration {
    public function up(): void
    {
        // ──────────────────────────────────────────────────────────
        // 1. CdeProject — Energy Project Classification
        // ──────────────────────────────────────────────────────────
        Schema::table('cde_projects', function (Blueprint $table) {
            $table->string('project_type', 40)->nullable()->after('code')
                ->comment('building, road, energy, water, telecom, industrial');
            $table->string('energy_sector', 40)->nullable()->after('project_type')
                ->comment('solar, wind, hydro, thermal, oil_gas, transmission, distribution');
            $table->decimal('capacity_mw', 10, 2)->nullable()->after('energy_sector')
                ->comment('Installed capacity in MW');
            $table->string('voltage_level', 30)->nullable()->after('capacity_mw')
                ->comment('e.g. 11kV, 33kV, 132kV, 400kV');
            $table->string('grid_connection_point', 100)->nullable()->after('voltage_level');
            $table->string('commissioning_status', 30)->nullable()->after('grid_connection_point')
                ->comment('pre_commissioning, mechanical_completion, energization, pac, fac');
            $table->date('commercial_operation_date')->nullable()->after('commissioning_status')
                ->comment('COD — when project starts generating revenue');
            $table->string('regulatory_license', 100)->nullable()->after('commercial_operation_date')
                ->comment('ERA license or equivalent');
        });

        // ──────────────────────────────────────────────────────────
        // 2. DailySiteDiary — Environmental Monitoring
        // ──────────────────────────────────────────────────────────
        Schema::table('daily_site_diaries', function (Blueprint $table) {
            $table->decimal('humidity_percent', 5, 1)->nullable()->after('temperature')
                ->comment('Relative humidity %');
            $table->decimal('wind_speed_kmh', 5, 1)->nullable()->after('humidity_percent')
                ->comment('Wind speed in km/h');
            $table->string('wind_direction', 10)->nullable()->after('wind_speed_kmh')
                ->comment('N, NE, E, SE, S, SW, W, NW');
            $table->decimal('noise_level_db', 5, 1)->nullable()->after('wind_direction')
                ->comment('Ambient noise in dB(A)');
            $table->decimal('dust_level_pm10', 7, 2)->nullable()->after('noise_level_db')
                ->comment('PM10 concentration µg/m³');
            $table->decimal('water_ph', 4, 2)->nullable()->after('dust_level_pm10')
                ->comment('Water pH if monitoring discharge');
            $table->text('environmental_notes')->nullable()->after('water_ph');
            $table->decimal('solar_irradiance', 6, 1)->nullable()->after('environmental_notes')
                ->comment('W/m² for solar projects');
        });

        // ──────────────────────────────────────────────────────────
        // 3. SafetyIncident — Permit to Work (PTW)
        // ──────────────────────────────────────────────────────────
        Schema::table('safety_incidents', function (Blueprint $table) {
            $table->boolean('is_ptw')->default(false)->after('preventive_action')
                ->comment('Is this a Permit to Work record?');
            $table->string('ptw_number', 30)->nullable()->after('is_ptw');
            $table->string('ptw_type', 40)->nullable()->after('ptw_number')
                ->comment('hot_work, electrical_isolation, confined_space, height, excavation, general');
            $table->string('isolation_method', 100)->nullable()->after('ptw_type')
                ->comment('LOTO, circuit breaker, valve closure, etc.');
            $table->text('isolation_points')->nullable()->after('isolation_method')
                ->comment('List of isolation points');
            $table->foreignId('ptw_issuer_id')->nullable()->after('isolation_points')
                ->constrained('users')->nullOnDelete();
            $table->foreignId('ptw_receiver_id')->nullable()->after('ptw_issuer_id')
                ->constrained('users')->nullOnDelete();
            $table->datetime('ptw_valid_from')->nullable()->after('ptw_receiver_id');
            $table->datetime('ptw_valid_until')->nullable()->after('ptw_valid_from');
            $table->string('ptw_status', 20)->nullable()->after('ptw_valid_until')
                ->comment('active, extended, closed, cancelled');
            $table->text('ptw_conditions')->nullable()->after('ptw_status')
                ->comment('Special conditions / precautions');
            $table->text('ppe_requirements')->nullable()->after('ptw_conditions')
                ->comment('JSON list of required PPE');
        });

        // ──────────────────────────────────────────────────────────
        // 4. WorkOrder — Testing / Inspection & Commissioning
        // ──────────────────────────────────────────────────────────
        Schema::table('work_orders', function (Blueprint $table) {
            $table->boolean('is_inspection')->default(false)->after('notes')
                ->comment('Testing & Inspection Plan (TIP) work order');
            $table->string('inspection_type', 40)->nullable()->after('is_inspection')
                ->comment('visual, dimensional, electrical, pressure, functional, load');
            $table->string('hold_point', 20)->nullable()->after('inspection_type')
                ->comment('hold, witness, review — inspection hold point classification');
            $table->text('acceptance_criteria')->nullable()->after('hold_point')
                ->comment('Pass/fail criteria');
            $table->string('test_result', 20)->nullable()->after('acceptance_criteria')
                ->comment('pass, fail, conditional, na');
            $table->text('test_readings')->nullable()->after('test_result')
                ->comment('JSON: measured values / readings');
            $table->string('equipment_tested', 100)->nullable()->after('test_readings')
                ->comment('Tag number or equipment reference');
            $table->string('method_statement_ref', 50)->nullable()->after('equipment_tested')
                ->comment('Reference to method statement');
            // Commissioning
            $table->boolean('is_commissioning')->default(false)->after('method_statement_ref')
                ->comment('Commissioning activity work order');
            $table->string('commissioning_phase', 30)->nullable()->after('is_commissioning')
                ->comment('pre_commissioning, mechanical_completion, energization, hot_commissioning, performance_test');
            $table->string('system_tag', 60)->nullable()->after('commissioning_phase')
                ->comment('System/subsystem tag being commissioned');
        });

        // ──────────────────────────────────────────────────────────
        // 5. SnagItem — Commissioning Punch List
        // ──────────────────────────────────────────────────────────
        Schema::table('snag_items', function (Blueprint $table) {
            $table->string('punch_category', 10)->nullable()->after('category')
                ->comment('A = must complete before handover, B = can complete after, C = cosmetic');
            $table->string('commissioning_system', 60)->nullable()->after('punch_category')
                ->comment('System tag: e.g. HVAC-01, ELEC-MV-02');
            $table->string('discipline', 30)->nullable()->after('commissioning_system')
                ->comment('mechanical, electrical, civil, instrumentation, piping');
            $table->text('photos')->nullable()->after('discipline');
            $table->foreignId('verified_by')->nullable()->after('photos')
                ->constrained('users')->nullOnDelete();
            $table->datetime('verified_at')->nullable()->after('verified_by');
        });

        // ──────────────────────────────────────────────────────────
        // 6. Tasks — Commissioning Phase
        // ──────────────────────────────────────────────────────────
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('commissioning_phase', 30)->nullable()->after('progress_percent')
                ->comment('pre_commissioning, mech_completion, energization, hot_commissioning, performance_test');
            $table->string('method_statement', 100)->nullable()->after('commissioning_phase')
                ->comment('Reference to method statement document');
        });
    }

    public function down(): void
    {
        Schema::table('cde_projects', function (Blueprint $table) {
            $table->dropColumn([
                'project_type',
                'energy_sector',
                'capacity_mw',
                'voltage_level',
                'grid_connection_point',
                'commissioning_status',
                'commercial_operation_date',
                'regulatory_license',
            ]);
        });

        Schema::table('daily_site_diaries', function (Blueprint $table) {
            $table->dropColumn([
                'humidity_percent',
                'wind_speed_kmh',
                'wind_direction',
                'noise_level_db',
                'dust_level_pm10',
                'water_ph',
                'environmental_notes',
                'solar_irradiance',
            ]);
        });

        Schema::table('safety_incidents', function (Blueprint $table) {
            $table->dropForeign(['ptw_issuer_id']);
            $table->dropForeign(['ptw_receiver_id']);
            $table->dropColumn([
                'is_ptw',
                'ptw_number',
                'ptw_type',
                'isolation_method',
                'isolation_points',
                'ptw_issuer_id',
                'ptw_receiver_id',
                'ptw_valid_from',
                'ptw_valid_until',
                'ptw_status',
                'ptw_conditions',
                'ppe_requirements',
            ]);
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn([
                'is_inspection',
                'inspection_type',
                'hold_point',
                'acceptance_criteria',
                'test_result',
                'test_readings',
                'equipment_tested',
                'method_statement_ref',
                'is_commissioning',
                'commissioning_phase',
                'system_tag',
            ]);
        });

        Schema::table('snag_items', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn([
                'punch_category',
                'commissioning_system',
                'discipline',
                'photos',
                'verified_by',
                'verified_at',
            ]);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['commissioning_phase', 'method_statement']);
        });
    }
};
