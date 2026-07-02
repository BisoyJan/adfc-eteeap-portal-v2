<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculum_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('title', 200);
            $table->decimal('units', 3, 1);
            $table->json('prerequisite_codes')->nullable();
            $table->string('category', 30);
            $table->unsignedTinyInteger('year_level')->nullable();
            $table->unsignedTinyInteger('semester')->nullable()->comment('1, 2, 0 = summer');
            $table->json('learning_outcomes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('curriculum_version', 50)->nullable()->comment('e.g., CMO-54-2016, AY-2026-2027');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_subjects');
    }
};
