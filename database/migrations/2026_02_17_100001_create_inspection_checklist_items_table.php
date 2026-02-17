<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('inspection_checklist_items')) {
            Schema::create('inspection_checklist_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inspection_template_id')->constrained()->cascadeOnDelete();
                $table->string('section')->nullable();
                $table->text('question');
                $table->string('type')->default('yes_no');
                $table->integer('sort_order')->default(0);
                $table->boolean('is_required')->default(true);
                $table->timestamps();
                $table->index(['inspection_template_id', 'sort_order']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_checklist_items');
    }
};
