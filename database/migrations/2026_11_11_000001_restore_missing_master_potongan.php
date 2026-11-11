<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Pulihkan ID potongan historis (7 = Piutang, 10 = KPM 2) jika tidak ada di tabel master potongan
     * agar join ke rincian potongan di PWA dan laporan tidak hilang/kosong.
     */
    public function up(): void
    {
        if (Schema::hasTable('potongan')) {
            $has7 = DB::table('potongan')->where('id_potongan', 7)->exists();
            if (!$has7) {
                DB::table('potongan')->insert([
                    'id_potongan'   => 7,
                    'nama_potongan' => 'Piutang',
                    'nominal'       => 0,
                ]);
            }

            $has10 = DB::table('potongan')->where('id_potongan', 10)->exists();
            if (!$has10) {
                DB::table('potongan')->insert([
                    'id_potongan'   => 10,
                    'nama_potongan' => 'KPM 2',
                    'nominal'       => 140000,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tetap dipertahankan agar tidak menghilangkan referensi historis
    }
};
