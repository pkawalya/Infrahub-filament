<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cde_projects', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->nullable()->after('manager_id');
            $table->decimal('budget', 14, 2)->nullable()->after('end_date');
            $table->string('address')->nullable()->after('budget');
            $table->string('city')->nullable()->after('address');
            $table->string('country')->nullable()->after('city');
            $table->string('image')->nullable()->after('country');

            $table->foreign('client_id')->references('id')->on('clients')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cde_projects', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn(['client_id', 'budget', 'address', 'city', 'country', 'image']);
        });
    }
};
