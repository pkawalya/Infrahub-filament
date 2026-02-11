<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ──── Field: Service Locations ────
        Schema::create('service_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('geofence_radius', 8, 2)->default(100); // meters
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ──── Field: Technician Status ────
        Schema::create('technician_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('available'); // available, busy, on_route, on_site, offline
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('current_address')->nullable();
            $table->foreignId('work_order_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('status_updated_at')->nullable();
            $table->timestamps();
        });

        // ──── Field: Technician Locations (GPS history) ────
        Schema::create('technician_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('address')->nullable();
            $table->decimal('speed', 8, 2)->nullable();
            $table->decimal('heading', 5, 2)->nullable();
            $table->decimal('accuracy', 8, 2)->nullable();
            $table->string('battery_level')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        // ──── Field: Routes ────
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->string('status')->default('planned'); // planned, in_progress, completed
            $table->decimal('estimated_distance', 10, 2)->nullable();
            $table->decimal('actual_distance', 10, 2)->nullable();
            $table->string('optimization_method')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
        });

        // ──── Field: Route Stops ────
        Schema::create('route_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_location_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('work_order_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('sequence')->default(0);
            $table->time('estimated_arrival')->nullable();
            $table->time('actual_arrival')->nullable();
            $table->time('departure_time')->nullable();
            $table->string('status')->default('pending'); // pending, arrived, completed, skipped
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ──── Field: Site Check-ins ────
        Schema::create('site_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_location_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('work_order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type')->default('checkin'); // checkin, checkout
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('address')->nullable();
            $table->string('photo')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_within_geofence')->default(false);
            $table->timestamps();
        });

        // ──── Field: Geofence Events ────
        Schema::create('geofence_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_location_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type'); // enter, exit
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });

        // ──── Field: Mileage Logs ────
        Schema::create('mileage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_order_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');
            $table->decimal('start_odometer', 10, 2)->nullable();
            $table->decimal('end_odometer', 10, 2)->nullable();
            $table->decimal('distance', 10, 2)->nullable();
            $table->string('vehicle')->nullable();
            $table->string('purpose')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ──── Field: Daily Site Logs ────
        Schema::create('daily_site_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cde_project_id')->nullable()->constrained()->nullOnDelete();
            $table->date('log_date');
            $table->string('weather')->nullable();
            $table->decimal('temperature_high', 5, 2)->nullable();
            $table->decimal('temperature_low', 5, 2)->nullable();
            $table->integer('workers_on_site')->default(0);
            $table->integer('visitors_on_site')->default(0);
            $table->text('work_performed')->nullable();
            $table->text('materials_received')->nullable();
            $table->text('equipment_used')->nullable();
            $table->text('delays')->nullable();
            $table->text('safety_incidents')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('draft'); // draft, submitted, approved
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_site_logs');
        Schema::dropIfExists('mileage_logs');
        Schema::dropIfExists('geofence_events');
        Schema::dropIfExists('site_checkins');
        Schema::dropIfExists('route_stops');
        Schema::dropIfExists('routes');
        Schema::dropIfExists('technician_locations');
        Schema::dropIfExists('technician_statuses');
        Schema::dropIfExists('service_locations');
    }
};
