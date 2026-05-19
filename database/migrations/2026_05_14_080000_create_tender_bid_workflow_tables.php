<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ─── Tender Stages (configurable per company) ────────────
        Schema::create('tender_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');               // e.g. "Draft", "Published"
            $table->string('slug');               // e.g. "draft", "published"
            $table->string('color')->default('gray'); // Filament badge color
            $table->string('icon')->nullable();   // heroicon name
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_default')->default(false); // initial stage for new tenders
            $table->boolean('is_terminal')->default(false); // no further transitions
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'slug']);
            $table->index(['company_id', 'sort_order']);
        });

        // ─── Tender Stage Transitions ────────────────────────────
        Schema::create('tender_stage_transitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_stage_id')->constrained('tender_stages')->cascadeOnDelete();
            $table->foreignId('to_stage_id')->constrained('tender_stages')->cascadeOnDelete();
            $table->string('required_permission')->nullable(); // e.g. 'tender.advance_stage'
            $table->boolean('requires_comment')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'from_stage_id', 'to_stage_id'], 'tender_transition_unique');
        });

        // ─── Add stage_id to existing tenders table ──────────────
        Schema::table('tenders', function (Blueprint $table) {
            $table->foreignId('tender_stage_id')->nullable()->after('status')
                ->constrained('tender_stages')->nullOnDelete();
            $table->timestamp('stage_changed_at')->nullable()->after('tender_stage_id');
        });

        // ─── Bid Stages (configurable per company) ───────────────
        Schema::create('bid_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('color')->default('gray');
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_terminal')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'slug']);
            $table->index(['company_id', 'sort_order']);
        });

        // ─── Bid Stage Transitions ───────────────────────────────
        Schema::create('bid_stage_transitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_stage_id')->constrained('bid_stages')->cascadeOnDelete();
            $table->foreignId('to_stage_id')->constrained('bid_stages')->cascadeOnDelete();
            $table->string('required_permission')->nullable();
            $table->boolean('requires_comment')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'from_stage_id', 'to_stage_id'], 'bid_transition_unique');
        });

        // ─── Tender Bids ─────────────────────────────────────────
        Schema::create('tender_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tender_id')->constrained('tenders')->cascadeOnDelete();

            $table->string('reference')->nullable();       // BID-2026-001
            $table->string('bidder_name');                  // Company/consortium name
            $table->string('bidder_email')->nullable();
            $table->string('bidder_phone')->nullable();
            $table->decimal('bid_amount', 14, 2)->nullable();
            $table->decimal('technical_score', 5, 2)->nullable();
            $table->decimal('financial_score', 5, 2)->nullable();
            $table->decimal('total_score', 5, 2)->nullable();

            $table->foreignId('bid_stage_id')->nullable()->constrained('bid_stages')->nullOnDelete();
            $table->timestamp('stage_changed_at')->nullable();

            $table->date('submitted_at')->nullable();
            $table->date('evaluated_at')->nullable();
            $table->text('evaluation_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('document_path')->nullable();
            $table->json('attachments')->nullable();

            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['company_id', 'tender_id']);
            $table->index('bid_stage_id');
        });

        // ─── Stage Audit Log (polymorphic: tender or bid) ────────
        Schema::create('stage_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->morphs('auditable');  // auditable_type, auditable_id
            $table->string('from_stage')->nullable();
            $table->unsignedBigInteger('from_stage_id')->nullable();
            $table->string('to_stage');
            $table->unsignedBigInteger('to_stage_id')->nullable();
            $table->text('comment')->nullable();
            $table->json('metadata')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('transitioned_at');
            $table->timestamps();

            $table->index(['company_id', 'auditable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stage_audit_logs');
        Schema::dropIfExists('tender_bids');
        Schema::dropIfExists('bid_stage_transitions');
        Schema::dropIfExists('bid_stages');
        Schema::table('tenders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tender_stage_id');
            $table->dropColumn('stage_changed_at');
        });
        Schema::dropIfExists('tender_stage_transitions');
        Schema::dropIfExists('tender_stages');
    }
};
