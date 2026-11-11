<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengubah penamaan master bidang 'Bidang DIKJAR' menjadi 'Bidang DIKDASMEN'
     */
    public function up(): void
    {
        DB::table('lembaga')
            ->where('id_bidang', 'Bidang DIKJAR')
            ->update(['id_bidang' => 'Bidang DIKDASMEN']);

        if (DB::getSchemaBuilder()->hasTable('pengaturan_tahun_acuan')) {
            DB::table('pengaturan_tahun_acuan')
                ->where('id_bidang', 'Bidang DIKJAR')
                ->update(['id_bidang' => 'Bidang DIKDASMEN']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('lembaga')
            ->where('id_bidang', 'Bidang DIKDASMEN')
            ->update(['id_bidang' => 'Bidang DIKJAR']);

        if (DB::getSchemaBuilder()->hasTable('pengaturan_tahun_acuan')) {
            DB::table('pengaturan_tahun_acuan')
                ->where('id_bidang', 'Bidang DIKDASMEN')
                ->update(['id_bidang' => 'Bidang DIKJAR']);
        }
    }
};

