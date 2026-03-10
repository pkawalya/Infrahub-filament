<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ══════════════════════════════════════════════════════
        // Change Orders (Variations)
        // ══════════════════════════════════════════════════════
        if (!Schema::hasTable('change_orders'))
            Schema::create('change_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->foreignId('cde_project_id')->constrained()->cascadeOnDelete();
                $table->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();

                $table->string('reference')->unique();          // CO-001
                $table->string('title');
                $table->text('description')->nullable();
                $table->text('reason')->nullable();              // Why the change was needed
                $table->string('type')->default('addition');     // addition, omission, time_extension, scope_change
                $table->string('status')->default('draft');      // draft, submitted, under_review, approved, rejected, implemented
                $table->string('priority')->default('medium');   // low, medium, high, critical
                $table->string('initiated_by')->nullable();      // contractor, client, consultant, engineer

                // Financial impact
                $table->decimal('estimated_cost', 15, 2)->default(0);
                $table->decimal('approved_cost', 15, 2)->nullable();
                $table->decimal('cost_impact', 15, 2)->default(0);  // net impact on contract value

                // Schedule impact
                $table->integer('time_impact_days')->default(0);
                $table->date('submitted_date')->nullable();
                $table->date('approved_date')->nullable();
                $table->date('implementation_date')->nullable();

                // Approvals
                $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->text('approval_notes')->nullable();
                $table->text('rejection_reason')->nullable();

                // Linked items
                $table->json('affected_boq_items')->nullable();
                $table->json('affected_tasks')->nullable();
                $table->json('attachments')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->index(['company_id', 'cde_project_id', 'status']);
            });

        // ══════════════════════════════════════════════════════
        // Drawing Register
        // ══════════════════════════════════════════════════════
        if (!Schema::hasTable('drawings'))
            Schema::create('drawings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->foreignId('cde_project_id')->constrained()->cascadeOnDelete();

                $table->string('drawing_number');                // DWG-ARCH-001
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('discipline')->default('architectural'); // architectural, structural, mechanical, electrical, civil, plumbing
                $table->string('drawing_type')->default('plan');        // plan, elevation, section, detail, schedule, diagram
                $table->string('current_revision')->default('A');       // A, B, C, ... or 01, 02
                $table->string('status')->default('wip');               // wip, for_review, approved, ifc, as_built, superseded
                $table->string('scale')->nullable();                    // 1:100, 1:50
                $table->string('sheet_size')->nullable();               // A0, A1, A2, A3

                // ISO 19650 metadata
                $table->string('suitability_code')->nullable();  // S0, S1, S2, S3, S4
                $table->string('originator')->nullable();
                $table->string('zone')->nullable();
                $table->string('level')->nullable();

                // Approval
                $table->foreignId('drawn_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->date('drawn_date')->nullable();
                $table->date('checked_date')->nullable();
                $table->date('approved_date')->nullable();

                $table->json('tags')->nullable();
                $table->text('notes')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->unique(['company_id', 'cde_project_id', 'drawing_number']);
                $table->index(['company_id', 'cde_project_id', 'discipline', 'status']);
            });

        // Drawing Revisions
        if (!Schema::hasTable('drawing_revisions'))
            Schema::create('drawing_revisions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('drawing_id')->constrained()->cascadeOnDelete();
                $table->string('revision_code');          // A, B, C ...
                $table->text('revision_description')->nullable();
                $table->string('file_path')->nullable();  // Stored file
                $table->string('file_name')->nullable();
                $table->unsignedBigInteger('file_size')->nullable();
                $table->string('status')->default('current'); // current, superseded
                $table->date('revision_date')->nullable();
                $table->foreignId('revised_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['drawing_id', 'status']);
            });

        // ══════════════════════════════════════════════════════
        // Payment Certificates
        // ══════════════════════════════════════════════════════
        if (!Schema::hasTable('payment_certificates'))
            Schema::create('payment_certificates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->foreignId('cde_project_id')->constrained()->cascadeOnDelete();
                $table->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();

                $table->string('certificate_number')->unique();  // IPC-001
                $table->string('type')->default('interim');       // interim, final, retention_release, advance
                $table->string('status')->default('draft');       // draft, submitted, certified, paid, rejected

                // Valuation period
                $table->date('period_from');
                $table->date('period_to');

                // Amounts
                $table->decimal('gross_value_to_date', 15, 2)->default(0);        // Total work done to date
                $table->decimal('previous_certified', 15, 2)->default(0);         // Already certified before
                $table->decimal('this_certificate_gross', 15, 2)->default(0);     // This period
                $table->decimal('variations_amount', 15, 2)->default(0);          // Change order costs this period
                $table->decimal('materials_on_site', 15, 2)->default(0);
                $table->decimal('retention_deduction', 15, 2)->default(0);
                $table->decimal('retention_release', 15, 2)->default(0);
                $table->decimal('advance_recovery', 15, 2)->default(0);
                $table->decimal('other_deductions', 15, 2)->default(0);
                $table->text('deduction_description')->nullable();
                $table->decimal('net_payable', 15, 2)->default(0);                // Net amount due
                $table->decimal('vat_amount', 15, 2)->default(0);
                $table->decimal('total_payable', 15, 2)->default(0);              // Final payable

                // Workflow
                $table->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('certified_by')->nullable()->constrained('users')->nullOnDelete();
                $table->date('submitted_date')->nullable();
                $table->date('certified_date')->nullable();
                $table->date('paid_date')->nullable();
                $table->text('notes')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->json('attachments')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->index(['company_id', 'cde_project_id', 'status']);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_certificates');
        Schema::dropIfExists('drawing_revisions');
        Schema::dropIfExists('drawings');
        Schema::dropIfExists('change_orders');
    }
};
