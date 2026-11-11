<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengubah penamaan master bidang 'Bidang DIKJAR-M' menjadi 'Bidang DIKDASMEN-M'
     */
    public function up(): void
    {
        DB::table('lembaga')
            ->where('id_bidang', 'Bidang DIKJAR-M')
            ->update(['id_bidang' => 'Bidang DIKDASMEN-M']);

        if (DB::getSchemaBuilder()->hasTable('pengaturan_tahun_acuan')) {
            DB::table('pengaturan_tahun_acuan')
                ->where('id_bidang', 'Bidang DIKJAR-M')
                ->update(['id_bidang' => 'Bidang DIKDASMEN-M']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('lembaga')
            ->where('id_bidang', 'Bidang DIKDASMEN-M')
            ->update(['id_bidang' => 'Bidang DIKJAR-M']);

        if (DB::getSchemaBuilder()->hasTable('pengaturan_tahun_acuan')) {
            DB::table('pengaturan_tahun_acuan')
                ->where('id_bidang', 'Bidang DIKDASMEN-M')
                ->update(['id_bidang' => 'Bidang DIKJAR-M']);
        }
    }
};

