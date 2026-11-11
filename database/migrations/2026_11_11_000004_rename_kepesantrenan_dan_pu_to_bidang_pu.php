<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengubah opsi 'Bidang Kepesantrenan dan PU' menjadi 'Bidang PU'
     * serta memperbarui lembaga 'Bidang Pekerjaan Umum' menjadi 'Bidang PU'.
     */
    public function up(): void
    {
        // Pastikan kolom id_bidang muat karakter lebih panjang (minimal 50 karakter)
        try {
            DB::statement("ALTER TABLE `lembaga` MODIFY COLUMN `id_bidang` VARCHAR(50) NULL");
        } catch (\Throwable $e) {}

        if (Schema::hasTable('pengaturan_tahun_acuan')) {
            try {
                DB::statement("ALTER TABLE `pengaturan_tahun_acuan` MODIFY COLUMN `id_bidang` VARCHAR(50) NULL");
            } catch (\Throwable $e) {}
        }

        // Update lembaga 'Bidang Pekerjaan Umum' ke 'Bidang PU'
        DB::table('lembaga')
            ->where('nama_lembaga', 'Bidang Pekerjaan Umum')
            ->update(['id_bidang' => 'Bidang PU']);

        // Update jika ada id_bidang 'Bidang Kepesantrenan dan PU'
        DB::table('lembaga')
            ->where('id_bidang', 'Bidang Kepesantrenan dan PU')
            ->update(['id_bidang' => 'Bidang PU']);

        if (Schema::hasTable('pengaturan_tahun_acuan')) {
            DB::table('pengaturan_tahun_acuan')
                ->where('id_bidang', 'Bidang Kepesantrenan dan PU')
                ->update(['id_bidang' => 'Bidang PU']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('lembaga')
            ->where('nama_lembaga', 'Bidang Pekerjaan Umum')
            ->update(['id_bidang' => 'Bidang Kepesantrenan']);

        DB::table('lembaga')
            ->where('id_bidang', 'Bidang PU')
            ->update(['id_bidang' => 'Bidang Kepesantrenan dan PU']);

        if (Schema::hasTable('pengaturan_tahun_acuan')) {
            DB::table('pengaturan_tahun_acuan')
                ->where('id_bidang', 'Bidang PU')
                ->update(['id_bidang' => 'Bidang Kepesantrenan dan PU']);
        }
    }
};
