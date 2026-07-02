<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('enrollment_subjects', function (Blueprint $table) {
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('grades', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('enrollment_subjects', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
    }
};
