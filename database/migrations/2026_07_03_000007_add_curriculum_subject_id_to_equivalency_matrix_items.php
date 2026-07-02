<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equivalency_matrix_items', function (Blueprint $table) {
            $table->foreignId('curriculum_subject_id')->nullable()->after('equivalency_matrix_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('equivalency_matrix_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('curriculum_subject_id');
        });
    }
};
