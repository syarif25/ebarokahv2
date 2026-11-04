<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Master Jenis Kegiatan Kepanitiaan
        if (!Schema::hasTable('master_jenis_kegiatan')) {
            Schema::create('master_jenis_kegiatan', function (Blueprint $table) {
                $table->increments('id_jenis_kegiatan');
                $table->string('nama_kegiatan', 150);
                $table->string('kode_kegiatan', 50)->unique();
                $table->enum('tingkat', ['dikdasmen', 'dikti', 'pesantren'])->default('dikdasmen');
                $table->text('keterangan')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Barokah Kepanitiaan (Master Tarif Komponen APBM 2026)
        if (!Schema::hasTable('barokah_kepanitiaan')) {
            Schema::create('barokah_kepanitiaan', function (Blueprint $table) {
                $table->increments('id_barokah_kepanitiaan');
                $table->unsignedInteger('id_jenis_kegiatan');
                $table->enum('kolom_kategori', [
                    'kepanitiaan',
                    'kehadiran',
                    'konsumsi',
                    'pemb_soal',
                    'input_soal',
                    'pengawas',
                    'penguji',
                    'korektor',
                    'input_nilai',
                    'pembimbing'
                ]);
                $table->string('nama_komponen', 150);
                $table->enum('satuan', ['mhs', 'sesi', 'shift', 'hari', 'kosong'])->default('kosong');
                $table->decimal('nominal', 12, 2)->default(0);
                $table->string('keterangan_aturan', 255)->nullable();
                $table->string('tahun_anggaran', 10)->default('2026');
                $table->timestamps();

                $table->foreign('id_jenis_kegiatan')
                      ->references('id_jenis_kegiatan')
                      ->on('master_jenis_kegiatan')
                      ->onDelete('cascade');
            });
        }

        // 3. Kepanitiaan Transaksi (Header Event Transaksi)
        if (!Schema::hasTable('kepanitiaan_transaksi')) {
            Schema::create('kepanitiaan_transaksi', function (Blueprint $table) {
                $table->increments('id_kepanitiaan_transaksi');
                $table->string('nama_event', 200);
                $table->unsignedInteger('id_jenis_kegiatan');
                $table->integer('id_lembaga')->nullable();
                $table->string('bulan', 20);
                $table->string('tahun', 10);
                $table->enum('status', ['draft', 'pengajuan', 'validasi', 'disetujui', 'ditolak'])->default('draft');
                $table->text('catatan_pimpinan')->nullable();
                $table->decimal('jumlah_total', 15, 2)->default(0);
                $table->string('id_petugas_input', 50)->nullable();
                $table->timestamp('tgl_input')->nullable();
                $table->timestamp('tgl_validasi')->nullable();
                $table->timestamps();

                $table->foreign('id_jenis_kegiatan')
                      ->references('id_jenis_kegiatan')
                      ->on('master_jenis_kegiatan')
                      ->onDelete('restrict');
            });
        }

        // 4. Kepanitiaan Detail (Rincian 10 Kolom per Panitia)
        if (!Schema::hasTable('kepanitiaan_detail')) {
            Schema::create('kepanitiaan_detail', function (Blueprint $table) {
                $table->increments('id_kepanitiaan_detail');
                $table->unsignedInteger('id_kepanitiaan_transaksi');
                $table->string('nik', 50)->nullable();
                $table->string('nama_panitia', 150);
                $table->string('lembaga_asal', 100)->nullable();

                // 1. Kepanitiaan (Jabatan Inti: Ketua, Sekretaris, Bendahara, dll.)
                $table->string('jabatan_panitia', 100)->nullable();
                $table->decimal('nominal_kepanitiaan', 12, 2)->default(0);

                // 2. Kehadiran
                $table->decimal('qty_kehadiran', 8, 2)->default(0);
                $table->decimal('nominal_kehadiran', 12, 2)->default(0);

                // 3. Konsumsi
                $table->decimal('qty_konsumsi', 8, 2)->default(0);
                $table->decimal('nominal_konsumsi', 12, 2)->default(0);

                // 4. Pembuatan Soal
                $table->decimal('qty_pemb_soal', 8, 2)->default(0);
                $table->decimal('nominal_pemb_soal', 12, 2)->default(0);

                // 5. Input Soal
                $table->decimal('qty_input_soal', 8, 2)->default(0);
                $table->decimal('nominal_input_soal', 12, 2)->default(0);

                // 6. Pengawas
                $table->decimal('qty_pengawas', 8, 2)->default(0);
                $table->decimal('nominal_pengawas', 12, 2)->default(0);

                // 7. Penguji
                $table->decimal('qty_penguji', 8, 2)->default(0);
                $table->decimal('nominal_penguji', 12, 2)->default(0);

                // 8. Korektor
                $table->decimal('qty_korektor', 8, 2)->default(0);
                $table->decimal('nominal_korektor', 12, 2)->default(0);

                // 9. Input Nilai
                $table->decimal('qty_input_nilai', 8, 2)->default(0);
                $table->decimal('nominal_input_nilai', 12, 2)->default(0);

                // 10. Pembimbing
                $table->decimal('qty_pembimbing', 8, 2)->default(0);
                $table->decimal('nominal_pembimbing', 12, 2)->default(0);

                // Total & Potongan
                $table->decimal('total_bruto', 12, 2)->default(0);
                $table->decimal('potongan', 12, 2)->default(0);
                $table->decimal('total_diterima', 12, 2)->default(0);
                $table->string('catatan', 255)->nullable();
                $table->timestamps();

                $table->foreign('id_kepanitiaan_transaksi')
                      ->references('id_kepanitiaan_transaksi')
                      ->on('kepanitiaan_transaksi')
                      ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepanitiaan_detail');
        Schema::dropIfExists('kepanitiaan_transaksi');
        Schema::dropIfExists('barokah_kepanitiaan');
        Schema::dropIfExists('master_jenis_kegiatan');
    }
};
