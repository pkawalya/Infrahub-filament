<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Track all login attempts for security monitoring
        if (!Schema::hasTable('login_activities'))
            Schema::create('login_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('email')->index();
                $table->string('ip_address', 45);
                $table->string('user_agent', 500)->nullable();
                $table->string('status'); // success, failed, locked, blocked
                $table->string('failure_reason')->nullable(); // wrong_password, disabled, no_company, rate_limited
                $table->json('metadata')->nullable(); // Device info, geo, etc.
                $table->timestamp('created_at')->useCurrent();

                $table->index(['ip_address', 'created_at']);
                $table->index(['email', 'status', 'created_at']);
            });

        // API request log for high-value operations
        if (!Schema::hasTable('api_audit_logs'))
            Schema::create('api_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->unsignedBigInteger('company_id')->nullable();
                $table->string('method', 10);
                $table->string('endpoint', 500);
                $table->string('ip_address', 45);
                $table->smallInteger('status_code');
                $table->unsignedInteger('response_time_ms')->nullable(); // Performance monitoring
                $table->json('request_params')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index(['user_id', 'created_at']);
                $table->index(['company_id', 'created_at']);
                $table->index(['endpoint', 'method']);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_audit_logs');
        Schema::dropIfExists('login_activities');
    }
};
