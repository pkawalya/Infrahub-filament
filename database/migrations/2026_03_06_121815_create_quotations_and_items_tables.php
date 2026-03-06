<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cde_project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();

            $table->string('quotation_number')->index();
            $table->string('reference')->nullable();
            $table->string('title')->nullable();

            // Financial
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);

            // Status
            $table->string('status')->default('draft');

            // Dates
            $table->date('issue_date')->nullable();
            $table->date('valid_until')->nullable();
            $table->date('accepted_at')->nullable();

            // Content
            $table->text('notes')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->text('scope_of_work')->nullable();

            // Conversion
            $table->foreignId('converted_invoice_id')->nullable()->constrained('invoices')->nullOnDelete();

            // Meta
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('quotation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->string('unit')->default('ea');
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('amount', 14, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Add invoice configuration columns to companies
        Schema::table('companies', function (Blueprint $table) {
            $table->json('invoice_config')->nullable()->after('settings');
        });

        // Add quotation_id to invoices for tracking conversion
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('quotation_id')->nullable()->after('work_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('quotation_id');
        });
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('invoice_config');
        });
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
    }
};
