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
        Schema::table('grades', function (Blueprint $table) {
            $table->decimal('ipa', 5, 2)->default(0)->after('religion');
            $table->decimal('ips', 5, 2)->default(0)->after('ipa');
            $table->decimal('pkn', 5, 2)->default(0)->after('ips');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropColumn(['ipa', 'ips', 'pkn']);
        });
    }
};
