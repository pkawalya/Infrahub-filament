<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->string('name');
            $t->string('email')->nullable();
            $t->string('phone')->nullable();
            $t->text('address')->nullable();
            $t->string('contact_person')->nullable();
            $t->string('type')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->softDeletes();
        });

        Schema::create('cost_codes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->string('code');
            $t->string('name');
            $t->text('description')->nullable();
            $t->unsignedBigInteger('parent_id')->nullable();
            $t->boolean('is_active')->default(true);
            $t->timestamps();
            $t->foreign('parent_id')->references('id')->on('cost_codes')->nullOnDelete();
        });

        Schema::create('contracts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('cde_project_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete();
            $t->string('contract_number');
            $t->string('title');
            $t->text('description')->nullable();
            $t->string('type')->default('lump_sum');
            $t->string('status')->default('draft');
            $t->decimal('original_value', 14, 2)->default(0);
            $t->decimal('revised_value', 14, 2)->default(0);
            $t->decimal('amount_paid', 14, 2)->default(0);
            $t->decimal('retainage_percent', 5, 2)->default(0);
            $t->date('start_date')->nullable();
            $t->date('end_date')->nullable();
            $t->text('scope_of_work')->nullable();
            $t->unsignedBigInteger('created_by')->nullable();
            $t->timestamps();
            $t->softDeletes();
            $t->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('budgets', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('cde_project_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
            $t->string('name');
            $t->decimal('total_amount', 14, 2)->default(0);
            $t->string('status')->default('draft');
            $t->timestamps();
        });

        Schema::create('budget_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('budget_id')->constrained()->cascadeOnDelete();
            $t->foreignId('cost_code_id')->nullable()->constrained()->nullOnDelete();
            $t->string('description');
            $t->decimal('budgeted_amount', 14, 2)->default(0);
            $t->decimal('actual_amount', 14, 2)->default(0);
            $t->timestamps();
        });

        Schema::create('change_orders', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $t->string('co_number');
            $t->string('title');
            $t->text('description')->nullable();
            $t->string('status')->default('draft');
            $t->decimal('amount', 14, 2)->default(0);
            $t->integer('time_extension_days')->default(0);
            $t->unsignedBigInteger('requested_by')->nullable();
            $t->unsignedBigInteger('approved_by')->nullable();
            $t->timestamp('approved_at')->nullable();
            $t->timestamps();
            $t->foreign('requested_by')->references('id')->on('users')->nullOnDelete();
            $t->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('claims', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $t->string('claim_number');
            $t->string('title');
            $t->text('description')->nullable();
            $t->string('status')->default('draft');
            $t->decimal('claimed_amount', 14, 2)->default(0);
            $t->decimal('approved_amount', 14, 2)->nullable();
            $t->unsignedBigInteger('submitted_by')->nullable();
            $t->timestamps();
            $t->foreign('submitted_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('certificates', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $t->string('certificate_number');
            $t->string('type')->default('interim');
            $t->string('status')->default('draft');
            $t->decimal('gross_amount', 14, 2)->default(0);
            $t->decimal('net_amount', 14, 2)->default(0);
            $t->date('period_from')->nullable();
            $t->date('period_to')->nullable();
            $t->date('certified_date')->nullable();
            $t->unsignedBigInteger('certified_by')->nullable();
            $t->timestamps();
            $t->foreign('certified_by')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('cost_actuals', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->foreignId('cost_code_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
            $t->string('description');
            $t->decimal('amount', 14, 2)->default(0);
            $t->date('transaction_date');
            $t->string('reference')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cost_actuals');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('claims');
        Schema::dropIfExists('change_orders');
        Schema::dropIfExists('budget_items');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('cost_codes');
        Schema::dropIfExists('vendors');
    }
};
