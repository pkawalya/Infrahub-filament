<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('company_user')) {
            Schema::create('company_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('user_type')->nullable();
                $table->string('job_title')->nullable();
                $table->string('department')->nullable();
                $table->string('phone')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['company_id', 'user_id']);
                $table->index(['user_id', 'is_active']);
                $table->index(['company_id', 'is_active']);
            });
        }

        // Backfill existing users into company_user table
        $users = DB::table('users')
            ->whereNotNull('company_id')
            ->select('id as user_id', 'company_id', 'user_type', 'job_title', 'department', 'phone', 'is_active', 'created_at', 'updated_at')
            ->get();

        foreach ($users as $user) {
            DB::table('company_user')->updateOrInsert(
                [
                    'company_id' => $user->company_id,
                    'user_id' => $user->user_id,
                ],
                [
                    'user_type' => $user->user_type,
                    'job_title' => $user->job_title,
                    'department' => $user->department,
                    'phone' => $user->phone,
                    'is_active' => $user->is_active ?? true,
                    'created_at' => $user->created_at ?? now(),
                    'updated_at' => $user->updated_at ?? now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_user');
    }
};
