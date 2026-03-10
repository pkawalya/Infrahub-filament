<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Enhance existing modules for Road & Highway project support.
 *
 * 1. cde_projects        → road classification, alignment, pavement design
 * 2. daily_site_diaries   → chainage tracking, layer works, compaction, material haulage
 * 3. work_orders          → road testing (CBR, compaction, asphalt cores, deflection)
 * 4. snag_items           → road defect categories (potholes, cracking, rutting, drainage)
 * 5. safety_incidents     → traffic management / road work zone safety
 * 6. tasks                → chainage-based progress tracking
 */
return new class extends Migration {
    public function up(): void
    {
        // ──────────────────────────────────────────────────────────
        // 1. CdeProject — Road Classification & Design
        // ──────────────────────────────────────────────────────────
        Schema::table('cde_projects', function (Blueprint $table) {
            $table->string('road_class', 30)->nullable()->after('regulatory_license')
                ->comment('national_trunk, district, urban, community, feeder');
            $table->decimal('road_length_km', 10, 3)->nullable()->after('road_class')
                ->comment('Total road length in km');
            $table->decimal('road_width_m', 6, 2)->nullable()->after('road_length_km')
                ->comment('Carriageway width in metres');
            $table->unsignedTinyInteger('number_of_lanes')->nullable()->after('road_width_m');
            $table->string('pavement_type', 30)->nullable()->after('number_of_lanes')
                ->comment('flexible, rigid, composite, gravel, earth');
            $table->string('design_speed_kph', 10)->nullable()->after('pavement_type')
                ->comment('Design speed e.g. 60, 80, 120');
            $table->string('chainage_start', 20)->nullable()->after('design_speed_kph')
                ->comment('Start chainage e.g. 0+000');
            $table->string('chainage_end', 20)->nullable()->after('chainage_start')
                ->comment('End chainage e.g. 45+350');
            $table->string('terrain', 20)->nullable()->after('chainage_end')
                ->comment('flat, rolling, mountainous');
            $table->string('funding_source', 100)->nullable()->after('terrain')
                ->comment('World Bank, AfDB, GoU, PPP, etc.');
            $table->string('road_authority', 100)->nullable()->after('funding_source')
                ->comment('UNRA, KCCA, District LG, etc.');
        });

        // ──────────────────────────────────────────────────────────
        // 2. DailySiteDiary — Chainage & Layer Works
        // ──────────────────────────────────────────────────────────
        Schema::table('daily_site_diaries', function (Blueprint $table) {
            $table->string('chainage_from', 20)->nullable()->after('solar_irradiance')
                ->comment('Start chainage of work e.g. 12+450');
            $table->string('chainage_to', 20)->nullable()->after('chainage_from')
                ->comment('End chainage of work e.g. 12+850');
            $table->string('road_layer', 30)->nullable()->after('chainage_to')
                ->comment('subgrade, improved_subgrade, subbase, base, primer, binder, wearing, shoulder');
            $table->decimal('layer_thickness_mm', 6, 1)->nullable()->after('road_layer')
                ->comment('Compacted layer thickness in mm');
            $table->string('material_source', 100)->nullable()->after('layer_thickness_mm')
                ->comment('Quarry or borrow pit name');
            $table->unsignedInteger('truck_loads')->nullable()->after('material_source')
                ->comment('Number of material truck loads delivered');
            $table->decimal('compaction_achieved', 5, 1)->nullable()->after('truck_loads')
                ->comment('Compaction % achieved (MDD)');
            $table->decimal('compaction_required', 5, 1)->nullable()->after('compaction_achieved')
                ->comment('Required compaction % (specification)');
            $table->decimal('moisture_content', 5, 1)->nullable()->after('compaction_required')
                ->comment('Field moisture content %');
            $table->text('survey_data')->nullable()->after('moisture_content')
                ->comment('Survey levels, alignment checks');
            $table->text('traffic_management_notes')->nullable()->after('survey_data')
                ->comment('Diversions, flagmen, lane closures');
        });

        // ──────────────────────────────────────────────────────────
        // 3. WorkOrder — Road Material Testing
        // ──────────────────────────────────────────────────────────
        Schema::table('work_orders', function (Blueprint $table) {
            $table->boolean('is_road_test')->default(false)->after('system_tag')
                ->comment('Road material/pavement test');
            $table->string('road_test_type', 40)->nullable()->after('is_road_test')
                ->comment('cbr, compaction, sieve_analysis, atterberg, asphalt_core, marshall, deflection, dcp, sand_replacement, plate_bearing');
            $table->string('test_chainage', 20)->nullable()->after('road_test_type')
                ->comment('Chainage where test was done');
            $table->string('test_layer', 30)->nullable()->after('test_chainage')
                ->comment('Which road layer was tested');
            $table->string('sample_reference', 50)->nullable()->after('test_layer')
                ->comment('Lab sample reference number');
            $table->string('test_lab', 100)->nullable()->after('sample_reference')
                ->comment('Name of testing laboratory');
            $table->decimal('test_value_achieved', 10, 2)->nullable()->after('test_lab')
                ->comment('Actual test result value');
            $table->decimal('test_value_required', 10, 2)->nullable()->after('test_value_achieved')
                ->comment('Specification requirement');
            $table->string('test_unit', 20)->nullable()->after('test_value_required')
                ->comment('%, MPa, mm, kN, etc.');
        });

        // ──────────────────────────────────────────────────────────
        // 4. SnagItem — Road Defect Categories
        // ──────────────────────────────────────────────────────────
        Schema::table('snag_items', function (Blueprint $table) {
            $table->string('chainage', 20)->nullable()->after('verified_at')
                ->comment('Chainage location of defect');
            $table->string('road_side', 10)->nullable()->after('chainage')
                ->comment('lhs, rhs, cl, full_width');
            $table->decimal('defect_length_m', 8, 2)->nullable()->after('road_side')
                ->comment('Defect length in metres');
            $table->decimal('defect_width_m', 6, 2)->nullable()->after('defect_length_m')
                ->comment('Defect width in metres');
            $table->decimal('defect_depth_mm', 6, 1)->nullable()->after('defect_width_m')
                ->comment('Defect depth in mm (for potholes, rutting)');
        });

        // ──────────────────────────────────────────────────────────
        // 5. SafetyIncident — Traffic Management / Road Work Zone
        // ──────────────────────────────────────────────────────────
        Schema::table('safety_incidents', function (Blueprint $table) {
            $table->boolean('is_traffic_incident')->default(false)->after('ppe_requirements')
                ->comment('Traffic/road work zone incident');
            $table->string('traffic_control_type', 40)->nullable()->after('is_traffic_incident')
                ->comment('stop_go, flagmen, traffic_lights, full_closure, lane_closure, diversion');
            $table->string('incident_chainage', 20)->nullable()->after('traffic_control_type')
                ->comment('Chainage where incident occurred');
            $table->boolean('third_party_involved')->default(false)->after('incident_chainage')
                ->comment('Public/3rd party vehicle or person involved');
            $table->boolean('road_closure_required')->default(false)->after('third_party_involved');
            $table->unsignedInteger('closure_duration_hours')->nullable()->after('road_closure_required');
        });

        // ──────────────────────────────────────────────────────────
        // 6. Tasks — Chainage-Based Progress
        // ──────────────────────────────────────────────────────────
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('chainage_from', 20)->nullable()->after('method_statement')
                ->comment('Start chainage for this task');
            $table->string('chainage_to', 20)->nullable()->after('chainage_from')
                ->comment('End chainage for this task');
            $table->string('road_layer', 30)->nullable()->after('chainage_to')
                ->comment('Layer being worked on');
        });
    }

    public function down(): void
    {
        Schema::table('cde_projects', function (Blueprint $table) {
            $table->dropColumn([
                'road_class',
                'road_length_km',
                'road_width_m',
                'number_of_lanes',
                'pavement_type',
                'design_speed_kph',
                'chainage_start',
                'chainage_end',
                'terrain',
                'funding_source',
                'road_authority',
            ]);
        });

        Schema::table('daily_site_diaries', function (Blueprint $table) {
            $table->dropColumn([
                'chainage_from',
                'chainage_to',
                'road_layer',
                'layer_thickness_mm',
                'material_source',
                'truck_loads',
                'compaction_achieved',
                'compaction_required',
                'moisture_content',
                'survey_data',
                'traffic_management_notes',
            ]);
        });

        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn([
                'is_road_test',
                'road_test_type',
                'test_chainage',
                'test_layer',
                'sample_reference',
                'test_lab',
                'test_value_achieved',
                'test_value_required',
                'test_unit',
            ]);
        });

        Schema::table('snag_items', function (Blueprint $table) {
            $table->dropColumn([
                'chainage',
                'road_side',
                'defect_length_m',
                'defect_width_m',
                'defect_depth_mm',
            ]);
        });

        Schema::table('safety_incidents', function (Blueprint $table) {
            $table->dropColumn([
                'is_traffic_incident',
                'traffic_control_type',
                'incident_chainage',
                'third_party_involved',
                'road_closure_required',
                'closure_duration_hours',
            ]);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['chainage_from', 'chainage_to', 'road_layer']);
        });
    }
};
