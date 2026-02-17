<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedInteger('extra_users')->default(0)->after('max_users');
            $table->unsignedInteger('extra_projects')->default(0)->after('max_projects');
            $table->unsignedInteger('extra_storage_gb')->default(0)->after('max_storage_gb');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['extra_users', 'extra_projects', 'extra_storage_gb']);
        });
    }
};
