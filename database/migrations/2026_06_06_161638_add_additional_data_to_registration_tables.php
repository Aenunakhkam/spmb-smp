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
        Schema::table('registrations', function (Blueprint $table) {
            $table->json('additional_data')->nullable();
        });
        Schema::table('student_details', function (Blueprint $table) {
            $table->json('additional_data')->nullable();
        });
        Schema::table('parent_details', function (Blueprint $table) {
            $table->json('additional_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn('additional_data');
        });
        Schema::table('student_details', function (Blueprint $table) {
            $table->dropColumn('additional_data');
        });
        Schema::table('parent_details', function (Blueprint $table) {
            $table->dropColumn('additional_data');
        });
    }
};
