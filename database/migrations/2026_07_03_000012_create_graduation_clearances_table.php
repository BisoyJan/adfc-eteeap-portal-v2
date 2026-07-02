<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('graduation_clearances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 30)->default('pending');
            $table->string('confirmation_letter_path', 255)->nullable();
            $table->timestamp('vpaa_approved_at')->nullable();
            $table->string('registry_number', 30)->unique()->nullable();
            $table->timestamp('diploma_printed_at')->nullable();
            $table->timestamp('tor_released_at')->nullable();
            $table->string('clearance_form_path', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graduation_clearances');
    }
};
