<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── 1. Update subscriptions: add per-project pricing ──────────
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->decimal('base_platform_price', 12, 2)->default(0)->after('yearly_price')
                ->comment('Fixed monthly platform fee for the company');
            $table->decimal('per_project_price', 12, 2)->default(0)->after('base_platform_price')
                ->comment('Default monthly cost per active project');
            $table->json('module_prices')->nullable()->after('per_project_price')
                ->comment('Per-module monthly prices: {"cde": 50, "boq": 30, ...}');
            $table->integer('included_projects')->default(0)->after('module_prices')
                ->comment('Number of projects included in base price (0 = none)');
        });

        // ── 2. Add billing fields to cde_projects ────────────────────
        Schema::table('cde_projects', function (Blueprint $table) {
            $table->string('billing_status')->default('active')->after('status')
                ->comment('active = billed, paused = not billed, archived = project closed');
            $table->decimal('monthly_rate', 12, 2)->default(0)->after('billing_status')
                ->comment('Calculated monthly cost for this project');
            $table->timestamp('billing_started_at')->nullable()->after('monthly_rate');
            $table->timestamp('billing_paused_at')->nullable()->after('billing_started_at');
            $table->text('billing_notes')->nullable()->after('billing_paused_at');
        });

        // ── 3. Add pricing to project_module_access ──────────────────
        Schema::table('project_module_access', function (Blueprint $table) {
            $table->decimal('monthly_price', 10, 2)->default(0)->after('is_enabled')
                ->comment('Monthly price for this module on this project');
        });

        // ── 4. Create billing_records table for monthly tracking ─────
        Schema::create('billing_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            // Billing period
            $table->string('period')->index()->comment('YYYY-MM format');
            $table->date('period_start');
            $table->date('period_end');

            // Amounts
            $table->decimal('base_platform_fee', 12, 2)->default(0);
            $table->decimal('project_fees', 12, 2)->default(0);
            $table->decimal('module_fees', 12, 2)->default(0);
            $table->decimal('addon_fees', 12, 2)->default(0)->comment('Extra users, storage, etc.');
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);

            // Status
            $table->string('status')->default('draft')->comment('draft, finalized, paid, overdue, void');

            // Tracking
            $table->integer('active_projects_count')->default(0);
            $table->integer('active_users_count')->default(0);
            $table->decimal('storage_used_gb', 8, 2)->default(0);

            // Breakdown detail
            $table->json('line_items')->nullable()->comment('Detailed breakdown of charges');

            // Payment
            $table->timestamp('finalized_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->unique(['company_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_records');

        Schema::table('project_module_access', function (Blueprint $table) {
            $table->dropColumn('monthly_price');
        });

        Schema::table('cde_projects', function (Blueprint $table) {
            $table->dropColumn([
                'billing_status',
                'monthly_rate',
                'billing_started_at',
                'billing_paused_at',
                'billing_notes',
            ]);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'base_platform_price',
                'per_project_price',
                'module_prices',
                'included_projects',
            ]);
        });
    }
};
