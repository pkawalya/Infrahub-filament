<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ──────────────────────────────── Subscriptions / Plans ────────────────────────────────
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('name');                     // Starter, Professional, Enterprise
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('monthly_price', 10, 2)->default(0);
            $table->decimal('yearly_price', 10, 2)->default(0);
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly, unlimited
            $table->integer('max_users')->default(5);
            $table->integer('max_projects')->default(10);
            $table->integer('max_storage_gb')->default(5);
            $table->json('included_modules')->nullable();   // ['core','cde','inventory',…]
            $table->json('features')->nullable();           // additional feature flags
            $table->boolean('is_active')->default(true);
            $table->boolean('is_popular')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // ──────────────────────────────── Companies (Tenants) ────────────────────────────────
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();
            $table->string('timezone')->default('UTC');
            $table->string('date_format')->default('Y-m-d');
            $table->string('time_format')->default('H:i');
            $table->string('currency')->default('USD');
            $table->string('currency_symbol')->default('$');
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('billing_cycle')->default('monthly');
            $table->timestamp('subscription_starts_at')->nullable();
            $table->timestamp('subscription_expires_at')->nullable();
            $table->integer('max_users')->default(5);
            $table->integer('max_projects')->default(10);
            $table->integer('max_storage_gb')->default(5);
            $table->bigInteger('current_storage_bytes')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_trial')->default(true);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->text('suspension_reason')->nullable();
            $table->json('settings')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ──────────────────────────────── Modules Registry ────────────────────────────────
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_core')->default(false);     // core modules always available
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // ──────────────────────────────── Company Module Access ────────────────────────────────
        Schema::create('company_module_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('module_code');
            $table->boolean('is_enabled')->default(true);
            $table->timestamp('enabled_at')->nullable();
            $table->unsignedBigInteger('enabled_by')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->unsignedBigInteger('disabled_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'module_code']);
        });

        // ──────────────────────────────── Add tenant fields to users ────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('user_type')->default('member')->after('email');  // super_admin, company_admin, manager, member, technician, client
            $table->string('job_title')->nullable()->after('user_type');
            $table->string('department')->nullable()->after('job_title');
            $table->string('phone')->nullable()->after('department');
            $table->string('avatar')->nullable()->after('phone');
            $table->string('timezone')->nullable()->after('avatar');
            $table->boolean('is_active')->default(true)->after('timezone');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn([
                'company_id',
                'user_type',
                'job_title',
                'department',
                'phone',
                'avatar',
                'timezone',
                'is_active',
                'last_login_at',
            ]);
        });

        Schema::dropIfExists('company_module_access');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('subscriptions');
    }
};
