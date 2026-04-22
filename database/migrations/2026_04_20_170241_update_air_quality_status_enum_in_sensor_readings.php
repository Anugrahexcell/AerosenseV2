<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update air_quality_status column to accept new labels:
     * Bagus, Sedang, Buruk
     * (replacing old: Baik, Tidak Sehat, Sangat Tidak Sehat, Berbahaya)
     */
    public function up(): void
    {
        // Change column from ENUM to VARCHAR so any string value is accepted
        DB::statement("ALTER TABLE sensor_readings MODIFY COLUMN air_quality_status VARCHAR(30) NOT NULL DEFAULT 'Bagus'");

        // Backfill old records with new labels
        DB::statement("UPDATE sensor_readings SET air_quality_status = CASE
            WHEN co2 <= 1000 THEN 'Bagus'
            WHEN co2 <= 2000 THEN 'Sedang'
            ELSE 'Buruk'
        END");
    }

    public function down(): void
    {
        // Revert to original ENUM if needed
        DB::statement("ALTER TABLE sensor_readings MODIFY COLUMN air_quality_status ENUM('Baik','Sedang','Tidak Sehat','Sangat Tidak Sehat','Berbahaya') NOT NULL DEFAULT 'Baik'");
    }
};
