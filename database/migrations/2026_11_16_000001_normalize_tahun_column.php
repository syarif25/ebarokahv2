<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'total_barokah',
            'total_barokah_pengajar',
            'total_barokah_satpam',
            'kehadiran_lembaga',
            'kehadiran',
            'kehadiran_pengajar',
            'wa_log',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'tahun')) {
                // Standarisasi nilai tahun dengan menghapus prefix 'Tahun ' atau 'tahun '
                DB::table($table)->where('tahun', 'Tahun 2024/2025')->update(['tahun' => '2024/2025']);
                DB::table($table)->where('tahun', 'tahun 2026')->update(['tahun' => '2026']);
                DB::statement("UPDATE {$table} SET tahun = TRIM(REPLACE(REPLACE(REPLACE(tahun, 'Tahun', ''), 'tahun', ''), 'TAHUN', '')) WHERE LOWER(tahun) LIKE '%tahun%'");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data cleaning / normalization tidak perlu di-reverse ke format typo
    }
};
