<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengubah kolom jumlah_sks agar mendukung angka pecahan / desimal (contoh: 12.5 atau 12,5)
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        if (in_array($driver, ['mysql', 'mariadb'])) {
            if (Schema::hasTable('pengajar') && Schema::hasColumn('pengajar', 'jumlah_sks')) {
                DB::statement("ALTER TABLE `pengajar` MODIFY COLUMN `jumlah_sks` DECIMAL(6,2) NOT NULL DEFAULT 0.00");
            }
            if (Schema::hasTable('total_barokah_pengajar') && Schema::hasColumn('total_barokah_pengajar', 'jumlah_sks')) {
                DB::statement("ALTER TABLE `total_barokah_pengajar` MODIFY COLUMN `jumlah_sks` DECIMAL(6,2) NOT NULL DEFAULT 0.00");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if (in_array($driver, ['mysql', 'mariadb'])) {
            if (Schema::hasTable('pengajar') && Schema::hasColumn('pengajar', 'jumlah_sks')) {
                DB::statement("ALTER TABLE `pengajar` MODIFY COLUMN `jumlah_sks` INT(11) NOT NULL DEFAULT 0");
            }
            if (Schema::hasTable('total_barokah_pengajar') && Schema::hasColumn('total_barokah_pengajar', 'jumlah_sks')) {
                DB::statement("ALTER TABLE `total_barokah_pengajar` MODIFY COLUMN `jumlah_sks` INT(11) NOT NULL DEFAULT 0");
            }
        }
    }
};

