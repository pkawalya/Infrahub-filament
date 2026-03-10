<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ─── Subcontractors ─────────────────────────────────────────
        Schema::create('subcontractors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('specialty')->nullable(); // e.g. Electrical, Plumbing, Steelwork
            $table->string('registration_number')->nullable();
            $table->string('tax_id')->nullable();

            $table->string('status')->default('active'); // active, suspended, blacklisted
            $table->integer('rating')->nullable()->comment('1-5 star rating');

            // Compliance / pre-qualification
            $table->date('insurance_expiry')->nullable();
            $table->date('license_expiry')->nullable();
            $table->boolean('safety_certified')->default(false);
            $table->json('certifications')->nullable(); // ['ISO 9001', 'OSHA']

            $table->text('address')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
        });

        // Subcontractor Work Packages (assigned scopes on a project)
        Schema::create('subcontractor_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subcontractor_id')->constrained('subcontractors')->cascadeOnDelete();
            $table->foreignId('cde_project_id')->constrained('cde_projects')->cascadeOnDelete();

            $table->string('title'); // "Electrical Rough-In Phase 1"
            $table->string('scope_of_work')->nullable();
            $table->string('status')->default('draft'); // draft, awarded, in_progress, completed, terminated
            $table->decimal('contract_value', 14, 2)->nullable();
            $table->decimal('paid_to_date', 14, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('progress_percent')->default(0);
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['company_id', 'cde_project_id']);
        });

        // ─── Tenders / Bids ─────────────────────────────────────────
        Schema::create('tenders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->string('reference')->nullable(); // e.g. TND-2026-001
            $table->string('title');
            $table->string('client_name')->nullable();
            $table->string('source')->nullable(); // public, private, referral, portal

            $table->string('status')->default('identified');
            // identified, preparing, submitted, shortlisted, awarded, lost, withdrawn

            $table->decimal('estimated_value', 14, 2)->nullable();
            $table->decimal('bid_amount', 14, 2)->nullable();
            $table->date('submission_deadline')->nullable();
            $table->date('submitted_at')->nullable();
            $table->date('decision_date')->nullable();

            $table->string('category')->nullable(); // construction, renovation, maintenance, supply
            $table->string('region')->nullable();

            // Scoring / analysis
            $table->integer('win_probability')->nullable()->comment('0-100%');
            $table->text('competitors')->nullable(); // JSON or text list
            $table->text('strategy_notes')->nullable();
            $table->text('loss_reason')->nullable();

            $table->string('document_path')->nullable(); // main tender document
            $table->json('attachments')->nullable(); // additional files

            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('company_id');
            $table->index('status');
        });

        // ─── Labor & HR ─────────────────────────────────────────────
        Schema::create('crew_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('cde_project_id')->nullable()->constrained('cde_projects')->nullOnDelete();

            $table->date('attendance_date');
            $table->time('clock_in')->nullable();
            $table->time('clock_out')->nullable();
            $table->decimal('hours_worked', 5, 2)->nullable();
            $table->decimal('overtime_hours', 5, 2)->default(0);

            $table->string('status')->default('present');
            // present, absent, late, half_day, leave

            $table->string('site_location')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['company_id', 'user_id', 'attendance_date']);
            $table->index(['company_id', 'attendance_date']);
        });

        // Worker Skills / Certifications
        Schema::create('worker_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('certification_name'); // e.g. "Forklift License", "First Aid"
            $table->string('issuing_body')->nullable();
            $table->string('certificate_number')->nullable();
            $table->date('issued_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('document_path')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['company_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worker_certifications');
        Schema::dropIfExists('crew_attendance');
        Schema::dropIfExists('tenders');
        Schema::dropIfExists('subcontractor_packages');
        Schema::dropIfExists('subcontractors');
    }
};
