<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('bank')) {
            Schema::create('bank', function (Blueprint $table) {
                $table->increments('id_bank');
                $table->string('nama_bank', 100);
                $table->string('kode_bank', 20)->nullable();
                $table->string('bifast_code', 50)->default('BRINIDJA');
                $table->string('tipe_transfer', 10)->default('BIF'); // 'IFT' atau 'BIF'
                $table->integer('biaya_admin')->default(2500);
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });

            // Seed initial data bank bawaan ebarokah
            $now = now();
            DB::table('bank')->insert([
                [
                    'nama_bank' => 'Bank Rakyat Indonesia',
                    'kode_bank' => '002',
                    'bifast_code' => 'BRINIDJA',
                    'tipe_transfer' => 'IFT',
                    'biaya_admin' => 1000,
                    'is_default' => 1,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'nama_bank' => 'Bank Mandiri',
                    'kode_bank' => '008',
                    'bifast_code' => 'BMRIIDJA',
                    'tipe_transfer' => 'BIF',
                    'biaya_admin' => 2500,
                    'is_default' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'nama_bank' => 'Bank BCA',
                    'kode_bank' => '014',
                    'bifast_code' => 'CENAIDJA',
                    'tipe_transfer' => 'BIF',
                    'biaya_admin' => 2500,
                    'is_default' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'nama_bank' => 'Bank BNI',
                    'kode_bank' => '009',
                    'bifast_code' => 'BNINIDJA',
                    'tipe_transfer' => 'BIF',
                    'biaya_admin' => 2500,
                    'is_default' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'nama_bank' => 'Bank Jatim',
                    'kode_bank' => '114',
                    'bifast_code' => 'PDJTIDJ1',
                    'tipe_transfer' => 'BIF',
                    'biaya_admin' => 2500,
                    'is_default' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'nama_bank' => "Bank Syari'ah Indonesia",
                    'kode_bank' => '451',
                    'bifast_code' => 'BSMDIDJA',
                    'tipe_transfer' => 'BIF',
                    'biaya_admin' => 2500,
                    'is_default' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'nama_bank' => 'Bank Muamalat',
                    'kode_bank' => '147',
                    'bifast_code' => 'MUABIDJA',
                    'tipe_transfer' => 'BIF',
                    'biaya_admin' => 2500,
                    'is_default' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'nama_bank' => 'Bank BTN',
                    'kode_bank' => '200',
                    'bifast_code' => 'BTANIDJA',
                    'tipe_transfer' => 'BIF',
                    'biaya_admin' => 2500,
                    'is_default' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'nama_bank' => 'Bank CIMB',
                    'kode_bank' => '022',
                    'bifast_code' => 'BNIAIDJA',
                    'tipe_transfer' => 'BIF',
                    'biaya_admin' => 2500,
                    'is_default' => 0,
                    'created_at' => $now,
                    'updated_at' => $now
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank');
    }
};
