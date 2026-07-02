<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equivalency_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_type', 30);
            $table->string('condition_key', 100);
            $table->text('condition_value');
            $table->foreignId('curriculum_subject_id')->constrained()->cascadeOnDelete();
            $table->decimal('equivalent_credits', 3, 1)->default(0);
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equivalency_rules');
    }
};
