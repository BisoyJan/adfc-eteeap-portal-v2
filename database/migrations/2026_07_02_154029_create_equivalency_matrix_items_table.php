<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('equivalency_matrix_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equivalency_matrix_id')->constrained()->cascadeOnDelete();
            $table->string('subject_code', 20);
            $table->string('subject_title', 200);
            $table->decimal('units', 3, 1)->default(0);
            $table->string('competency_level', 20);
            $table->text('evidence_source')->nullable();
            $table->decimal('equivalent_credits', 3, 1)->default(0);
            $table->string('recommended_action', 20)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equivalency_matrix_items');
    }
};
