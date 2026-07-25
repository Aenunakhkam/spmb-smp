<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE documents MODIFY type VARCHAR(255) NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting to ENUM might cause data loss if there are 'prestasi' rows, so we leave it as VARCHAR or drop it
        // DB::statement("ALTER TABLE documents MODIFY type ENUM('kk', 'akta', 'ijazah', 'ktp_ayah', 'ktp_ibu', 'kip', 'pkh', 'kks', 'foto') NOT NULL");
    }
};
