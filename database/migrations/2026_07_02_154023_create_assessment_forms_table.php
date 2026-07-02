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
        Schema::create('assessment_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();
            $table->string('stage', 30);
            $table->foreignId('assessor_id')->constrained('users')->cascadeOnDelete();
            $table->json('criteria');
            $table->decimal('total_score', 5, 2)->nullable();
            $table->decimal('max_score', 5, 2)->nullable();
            $table->string('verdict', 20)->nullable();
            $table->string('digital_signature', 255)->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['application_id', 'stage', 'assessor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assessment_forms');
    }
};
