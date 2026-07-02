<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clearance_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('graduation_clearance_id')->constrained('graduation_clearances')->cascadeOnDelete();
            $table->string('document_type', 50);
            $table->string('file_path', 255)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique(['graduation_clearance_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clearance_documents');
    }
};
