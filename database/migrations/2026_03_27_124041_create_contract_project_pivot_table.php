<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Converts contracts from a one-to-one (cde_project_id) relationship
     * to a many-to-many relationship via a pivot table, allowing one contract
     * to be linked to multiple projects.
     */
    public function up(): void
    {
        // 1. Create the pivot table
        Schema::create('contract_project', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cde_project_id')->constrained('cde_projects')->cascadeOnDelete();
            // Optional: per-project budget allocation amount from this contract
            $table->decimal('budget_allocation', 14, 2)->nullable();
            // Optional: notes specific to this contract-project link
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['contract_id', 'cde_project_id']);
        });

        // 2. Migrate existing cde_project_id data into the new pivot table
        DB::statement("
            INSERT INTO contract_project (contract_id, cde_project_id, created_at, updated_at)
            SELECT id, cde_project_id, NOW(), NOW()
            FROM contracts
            WHERE cde_project_id IS NOT NULL AND deleted_at IS NULL
        ");

        // 3. Drop the old cde_project_id column from contracts
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['cde_project_id']);
            $table->dropColumn('cde_project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add cde_project_id (restore first project link for each contract)
        Schema::table('contracts', function (Blueprint $table) {
            $table->foreignId('cde_project_id')->nullable()->after('company_id')->constrained('cde_projects')->nullOnDelete();
        });

        // Restore the first linked project for each contract
        DB::statement("
            UPDATE contracts c
            JOIN contract_project cp ON cp.contract_id = c.id
            SET c.cde_project_id = cp.cde_project_id
            WHERE cp.id = (
                SELECT MIN(id) FROM contract_project WHERE contract_id = c.id
            )
        ");

        Schema::dropIfExists('contract_project');
    }
};

