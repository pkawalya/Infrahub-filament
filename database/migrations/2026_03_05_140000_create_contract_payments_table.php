<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('contract_payments', function (Blueprint $t) {
            $t->id();
            $t->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $t->foreignId('company_id')->constrained()->cascadeOnDelete();
            $t->string('reference', 100)->nullable();
            $t->string('type', 30)->default('payment'); // payment, advance, retention_release, deduction
            $t->decimal('amount', 15, 2);
            $t->date('payment_date');
            $t->string('payment_method', 50)->nullable(); // bank_transfer, cheque, cash, mobile_money
            $t->text('notes')->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();

            $t->index(['contract_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_payments');
    }
};
