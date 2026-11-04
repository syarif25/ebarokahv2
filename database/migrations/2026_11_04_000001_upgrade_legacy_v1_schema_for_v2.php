<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Upgrade skema database lama (E-Barokah V1 / ebarokah_4 nov.sql)
     * agar 100% kompatibel dengan fitur E-Barokah V2.
     */
    public function up(): void
    {
        // 1. Tambah kolom catatan_khusus_umana pada kehadiran_pengajar jika belum ada
        if (Schema::hasTable('kehadiran_pengajar') && !Schema::hasColumn('kehadiran_pengajar', 'catatan_khusus_umana')) {
            Schema::table('kehadiran_pengajar', function (Blueprint $table) {
                $table->text('catatan_khusus_umana')->nullable()->after('tgl_input');
            });
        }

        // 2. Tambah kolom catatan_khusus_umana pada total_barokah_pengajar jika belum ada
        if (Schema::hasTable('total_barokah_pengajar') && !Schema::hasColumn('total_barokah_pengajar', 'catatan_khusus_umana')) {
            Schema::table('total_barokah_pengajar', function (Blueprint $table) {
                $table->text('catatan_khusus_umana')->nullable()->after('diterima');
            });
        }

        // 3. Berikan nilai DEFAULT aman pada kolom warisan V1 yang NOT NULL tanpa DEFAULT
        $driver = DB::getDriverName();
        if (in_array($driver, ['mysql', 'mariadb'])) {
            if (Schema::hasTable('pengguna') && Schema::hasColumn('pengguna', 'foto')) {
                DB::statement("ALTER TABLE `pengguna` MODIFY COLUMN `foto` VARCHAR(50) NOT NULL DEFAULT ''");
            }
            if (Schema::hasTable('kehadiran_lembaga')) {
                DB::statement("ALTER TABLE `kehadiran_lembaga` MODIFY COLUMN `jumlah_total` INT(20) NOT NULL DEFAULT 0");
                DB::statement("ALTER TABLE `kehadiran_lembaga` MODIFY COLUMN `file` VARCHAR(200) NOT NULL DEFAULT ''");
            }
            if (Schema::hasTable('satpam') && Schema::hasColumn('satpam', 'is_danru')) {
                DB::statement("ALTER TABLE `satpam` MODIFY COLUMN `is_danru` ENUM('0','1') NOT NULL DEFAULT '0'");
            }
            if (Schema::hasTable('penempatan')) {
                DB::statement("ALTER TABLE `penempatan` MODIFY COLUMN `jabatan_lembaga` VARCHAR(100) NOT NULL DEFAULT ''");
                DB::statement("ALTER TABLE `penempatan` MODIFY COLUMN `file_sk` VARCHAR(200) NOT NULL DEFAULT ''");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak menghapus kolom pada rollback demi keamanan data produksi
    }
};

