<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blocked_ips', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45)->index();         // IPv4 or IPv6
            $table->string('cidr_range', 50)->nullable();       // e.g. 192.168.0.0/16
            $table->string('reason')->nullable();               // Why it was blocked
            $table->string('blocked_by')->nullable();           // Admin who blocked it
            $table->timestamp('expires_at')->nullable();        // null = permanent
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedBigInteger('hit_count')->default(0); // Times this IP was blocked
            $table->timestamp('last_blocked_at')->nullable();
            $table->timestamps();

            $table->unique(['ip_address', 'cidr_range']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocked_ips');
    }
};
