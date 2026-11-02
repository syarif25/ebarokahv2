<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MasterJenisKegiatan;
use App\Models\BarokahKepanitiaan;

class KepanitiaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = [
            // 1. PMB / PPDB MADRASAH & SEKOLAH
            [
                'kegiatan' => [
                    'nama_kegiatan' => 'PMB / PPDB Madrasah & Sekolah',
                    'kode_kegiatan' => 'pmb_madrasah_sekolah',
                    'tingkat'       => 'dikdasmen',
                    'keterangan'    => 'Penerimaan Murid / Santri Baru tingkat Madrasah dan Sekolah',
                ],
                'tarif' => [
                    // Kolom Kepanitiaan
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Penanggung Jawab', 'satuan' => 'kosong', 'nominal' => 350000, 'keterangan_aturan' => 'Maksimal 1 orang'],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Ketua', 'satuan' => 'kosong', 'nominal' => 350000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Sekretaris', 'satuan' => 'kosong', 'nominal' => 325000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Bendahara', 'satuan' => 'kosong', 'nominal' => 325000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Koordinator', 'satuan' => 'kosong', 'nominal' => 300000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Anggota', 'satuan' => 'kosong', 'nominal' => 250000],
                    // Kolom Kehadiran
                    ['kolom_kategori' => 'kehadiran', 'nama_komponen' => 'Kehadiran Panitia', 'satuan' => 'shift', 'nominal' => 15000, 'keterangan_aturan' => 'Maksimal 15 hari, di luar jam efektif'],
                    // Kolom Pengawas
                    ['kolom_kategori' => 'pengawas', 'nama_komponen' => 'Pengawas tes masuk kelas intensif/unggulan', 'satuan' => 'sesi', 'nominal' => 5000],
                    // Kolom Penguji
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Tutor / Narasumber MOS', 'satuan' => 'sesi', 'nominal' => 75000, 'keterangan_aturan' => 'Per sesi / kelas'],
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Penguji Praktik Tes masuk/PPDB', 'satuan' => 'mhs', 'nominal' => 2500, 'keterangan_aturan' => 'Per MU / siswa'],
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Penguji lisan kelas intensif/unggulan', 'satuan' => 'mhs', 'nominal' => 2000, 'keterangan_aturan' => 'Per siswa'],
                    // Kolom Korektor
                    ['kolom_kategori' => 'korektor', 'nama_komponen' => 'Korektor Test masuk/PPDB', 'satuan' => 'mhs', 'nominal' => 2000, 'keterangan_aturan' => 'Per siswa'],
                    ['kolom_kategori' => 'korektor', 'nama_komponen' => 'Korektor Test intensif/unggulan', 'satuan' => 'mhs', 'nominal' => 2000, 'keterangan_aturan' => 'Per MU / siswa'],
                    // Kolom Input Nilai / Administrasi
                    ['kolom_kategori' => 'input_nilai', 'nama_komponen' => 'Penulisan Data Siswa Baru ke Buku Rapot', 'satuan' => 'mhs', 'nominal' => 2000, 'keterangan_aturan' => 'Per siswa'],
                    ['kolom_kategori' => 'input_nilai', 'nama_komponen' => 'Penulisan Data Siswa Baru ke Buku Induk', 'satuan' => 'mhs', 'nominal' => 2000, 'keterangan_aturan' => 'Per siswa'],
                    ['kolom_kategori' => 'input_nilai', 'nama_komponen' => 'Input Data Siswa Baru ke e-Raport/Komputer', 'satuan' => 'mhs', 'nominal' => 2000, 'keterangan_aturan' => 'Per siswa'],
                    ['kolom_kategori' => 'input_nilai', 'nama_komponen' => 'Tanda Tangan raport Kepala (biodata)', 'satuan' => 'mhs', 'nominal' => 2000, 'keterangan_aturan' => 'Per siswa'],
                    // Kolom Pembuatan Soal & Input Soal
                    ['kolom_kategori' => 'pemb_soal', 'nama_komponen' => 'Pembuatan soal tes intensif/unggulan', 'satuan' => 'kosong', 'nominal' => 35000, 'keterangan_aturan' => 'Per MU'],
                    ['kolom_kategori' => 'input_soal', 'nama_komponen' => 'Pengetikan soal tes intensif/unggulan', 'satuan' => 'kosong', 'nominal' => 15000, 'keterangan_aturan' => 'Per MU'],
                ]
            ],

            // 2. KEPANITIAAN UJIAN SEMESTER MADRASAH & SEKOLAH
            [
                'kegiatan' => [
                    'nama_kegiatan' => 'Ujian Semester Madrasah & Sekolah',
                    'kode_kegiatan' => 'ujian_semester_sekolah',
                    'tingkat'       => 'dikdasmen',
                    'keterangan'    => 'Ujian Semester / Penilaian Akhir Madrasah Diniyah dan Sekolah',
                ],
                'tarif' => [
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Penanggung Jawab', 'satuan' => 'kosong', 'nominal' => 350000, 'keterangan_aturan' => 'Maksimal 1 orang'],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Ketua', 'satuan' => 'kosong', 'nominal' => 350000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Sekretaris', 'satuan' => 'kosong', 'nominal' => 325000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Bendahara', 'satuan' => 'kosong', 'nominal' => 325000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Koordinator', 'satuan' => 'kosong', 'nominal' => 300000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Anggota', 'satuan' => 'kosong', 'nominal' => 250000],
                    // Pembuatan Soal
                    ['kolom_kategori' => 'pemb_soal', 'nama_komponen' => 'Pembuatan Soal MD (SD), Kilat & Kurnas', 'satuan' => 'kosong', 'nominal' => 25000, 'keterangan_aturan' => 'Per mapel / paket'],
                    ['kolom_kategori' => 'pemb_soal', 'nama_komponen' => 'Pembuatan Soal MD (Usia SLTP/SLTA)', 'satuan' => 'kosong', 'nominal' => 35000, 'keterangan_aturan' => 'Per mapel / paket'],
                    ['kolom_kategori' => 'pemb_soal', 'nama_komponen' => 'Pembuatan Soal Sekolah (tulis/CBT)', 'satuan' => 'kosong', 'nominal' => 50000, 'keterangan_aturan' => 'Per mapel / paket'],
                    ['kolom_kategori' => 'pemb_soal', 'nama_komponen' => 'Pembuatan Kisi-kisi Soal', 'satuan' => 'kosong', 'nominal' => 30000, 'keterangan_aturan' => 'Per mapel / paket'],
                    // Input / Pengetikan Soal
                    ['kolom_kategori' => 'input_soal', 'nama_komponen' => 'Pengetikan Soal Madrasah dan Sekolah', 'satuan' => 'kosong', 'nominal' => 20000, 'keterangan_aturan' => 'Per mapel / paket oleh panitia'],
                    ['kolom_kategori' => 'input_soal', 'nama_komponen' => 'Input soal ke program CBT', 'satuan' => 'kosong', 'nominal' => 5000, 'keterangan_aturan' => 'Per mapel / paket'],
                    // Penguji
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Penguji Praktik', 'satuan' => 'mhs', 'nominal' => 3000],
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Penguji Lisan (Al-Qur\'an)', 'satuan' => 'mhs', 'nominal' => 3000],
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Penguji Lisan (Kitab)', 'satuan' => 'mhs', 'nominal' => 3500],
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Penguji Al-Qur\'an (Tahfidz)', 'satuan' => 'mhs', 'nominal' => 10000, 'keterangan_aturan' => 'Per siswa (MQ)'],
                    // Korektor
                    ['kolom_kategori' => 'korektor', 'nama_komponen' => 'Korektor Akhlak (BK)', 'satuan' => 'mhs', 'nominal' => 2000],
                    ['kolom_kategori' => 'korektor', 'nama_komponen' => 'Korektor Akhlak (Wali Kelas)', 'satuan' => 'mhs', 'nominal' => 2000],
                    ['kolom_kategori' => 'korektor', 'nama_komponen' => 'Korektor Mapel (MD/KTSP)', 'satuan' => 'mhs', 'nominal' => 3500],
                    ['kolom_kategori' => 'korektor', 'nama_komponen' => 'Pengolahan Nilai CBT', 'satuan' => 'mhs', 'nominal' => 1500],
                    // Input Nilai
                    ['kolom_kategori' => 'input_nilai', 'nama_komponen' => 'Input nilai ke Komputer/Ledger (MD/KTSP)', 'satuan' => 'mhs', 'nominal' => 1500, 'keterangan_aturan' => 'Per siswa input manual'],
                    ['kolom_kategori' => 'input_nilai', 'nama_komponen' => 'Input nilai ke Komputer/Ledger/Dapodik (K-13)', 'satuan' => 'mhs', 'nominal' => 2000, 'keterangan_aturan' => 'Per siswa input manual'],
                    ['kolom_kategori' => 'input_nilai', 'nama_komponen' => 'Input Nilai ke Stambuk (MD/KTSP)', 'satuan' => 'mhs', 'nominal' => 2500, 'keterangan_aturan' => 'Per siswa input manual'],
                    ['kolom_kategori' => 'input_nilai', 'nama_komponen' => 'Input Nilai ke Stambuk (K-2013)', 'satuan' => 'mhs', 'nominal' => 3000, 'keterangan_aturan' => 'Per siswa input manual'],
                    ['kolom_kategori' => 'input_nilai', 'nama_komponen' => 'Penulisan Raport (MD/KTSP)', 'satuan' => 'mhs', 'nominal' => 2500],
                ]
            ],

            // 3. PMB UNIVERSITAS IBRAHIMY (S1 / D3)
            [
                'kegiatan' => [
                    'nama_kegiatan' => 'PMB Universitas Ibrahimy',
                    'kode_kegiatan' => 'pmb_unib',
                    'tingkat'       => 'dikti',
                    'keterangan'    => 'Penerimaan Mahasiswa Baru Universitas Ibrahimy Tingkat S1 dan D3',
                ],
                'tarif' => [
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Penanggung Jawab', 'satuan' => 'kosong', 'nominal' => 400000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Pengarah', 'satuan' => 'kosong', 'nominal' => 375000, 'keterangan_aturan' => 'Maksimal 3 orang'],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Ketua', 'satuan' => 'kosong', 'nominal' => 375000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Wakil Ketua', 'satuan' => 'kosong', 'nominal' => 350000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Sekretaris', 'satuan' => 'kosong', 'nominal' => 350000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Wakil Sekretaris', 'satuan' => 'kosong', 'nominal' => 350000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Bendahara', 'satuan' => 'kosong', 'nominal' => 350000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Koordinator', 'satuan' => 'kosong', 'nominal' => 350000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Anggota', 'satuan' => 'kosong', 'nominal' => 325000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Supervisi Bidang', 'satuan' => 'kosong', 'nominal' => 75000],
                    // Kehadiran & Konsumsi
                    ['kolom_kategori' => 'kehadiran', 'nama_komponen' => 'Barokah Kehadiran', 'satuan' => 'hari', 'nominal' => 10000, 'keterangan_aturan' => 'Disesuaikan pelaksanaan teknis'],
                    ['kolom_kategori' => 'konsumsi', 'nama_komponen' => 'Konsumsi Pengawas', 'satuan' => 'hari', 'nominal' => 10000],
                    // Soal & Pengawas
                    ['kolom_kategori' => 'pemb_soal', 'nama_komponen' => 'Pembuat soal dan jawaban', 'satuan' => 'kosong', 'nominal' => 125000, 'keterangan_aturan' => 'Per MU'],
                    ['kolom_kategori' => 'pengawas', 'nama_komponen' => 'Pengawas Ruang', 'satuan' => 'sesi', 'nominal' => 10000, 'keterangan_aturan' => 'Per MU'],
                    // Penguji & Korektor
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Penguji Psycotes (PMB & Prodi)', 'satuan' => 'sesi', 'nominal' => 150000, 'keterangan_aturan' => 'Per ruang'],
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Narasumber Sosialisasi', 'satuan' => 'sesi', 'nominal' => 50000, 'keterangan_aturan' => 'Per sesi (min 60 menit)'],
                    ['kolom_kategori' => 'korektor', 'nama_komponen' => 'Korektor', 'satuan' => 'mhs', 'nominal' => 3000],
                    // Input Nilai & Administrasi
                    ['kolom_kategori' => 'input_nilai', 'nama_komponen' => 'Penulisan Stambook', 'satuan' => 'mhs', 'nominal' => 1000],
                    ['kolom_kategori' => 'input_nilai', 'nama_komponen' => 'Input Biodata ke Sistem Informasi', 'satuan' => 'mhs', 'nominal' => 1500],
                    ['kolom_kategori' => 'input_nilai', 'nama_komponen' => 'Input Nilai ke Sistem Informasi', 'satuan' => 'mhs', 'nominal' => 1500],
                    ['kolom_kategori' => 'input_nilai', 'nama_komponen' => 'Iuran Keanggotaan Perpustakaan', 'satuan' => 'mhs', 'nominal' => 20000],
                    ['kolom_kategori' => 'input_nilai', 'nama_komponen' => 'Pemberkasan, input data dan pemotretan (Fakultas)', 'satuan' => 'mhs', 'nominal' => 1500],
                    ['kolom_kategori' => 'input_nilai', 'nama_komponen' => 'Verifikasi, olah data, edit, cetak KTM, distribusi (UNIB)', 'satuan' => 'mhs', 'nominal' => 3000],
                ]
            ],

            // 4. PMB PROGRAM PASCASARJANA (S2 & S3)
            [
                'kegiatan' => [
                    'nama_kegiatan' => 'PMB Pascasarjana (S2 & S3)',
                    'kode_kegiatan' => 'pmb_pascasarjana',
                    'tingkat'       => 'dikti',
                    'keterangan'    => 'Penerimaan Mahasiswa Baru Pascasarjana (S2 dan S3 digabung 1 kepanitiaan)',
                ],
                'tarif' => [
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Penanggung Jawab', 'satuan' => 'kosong', 'nominal' => 300000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Pengarah', 'satuan' => 'kosong', 'nominal' => 250000, 'keterangan_aturan' => '2 orang pengarah'],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Ketua', 'satuan' => 'kosong', 'nominal' => 250000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Sekretaris', 'satuan' => 'kosong', 'nominal' => 225000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Bendahara', 'satuan' => 'kosong', 'nominal' => 225000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Anggota', 'satuan' => 'kosong', 'nominal' => 200000],
                    ['kolom_kategori' => 'konsumsi', 'nama_komponen' => 'Konsumsi Piket Jaga', 'satuan' => 'kosong', 'nominal' => 200000, 'keterangan_aturan' => 'Selama PMB'],
                    ['kolom_kategori' => 'pemb_soal', 'nama_komponen' => 'Pembuatan Soal S2', 'satuan' => 'kosong', 'nominal' => 100000, 'keterangan_aturan' => 'Per MU'],
                    ['kolom_kategori' => 'pemb_soal', 'nama_komponen' => 'Pembuatan Soal S3', 'satuan' => 'kosong', 'nominal' => 200000, 'keterangan_aturan' => 'Per MU'],
                    ['kolom_kategori' => 'pengawas', 'nama_komponen' => 'Pengawas Ruang', 'satuan' => 'sesi', 'nominal' => 25000, 'keterangan_aturan' => 'Per MU'],
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Penguji Lisan', 'satuan' => 'sesi', 'nominal' => 150000, 'keterangan_aturan' => 'Per ruang'],
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Penguji Proposal (Pasca S3)', 'satuan' => 'mhs', 'nominal' => 5000],
                    ['kolom_kategori' => 'korektor', 'nama_komponen' => 'Korektor S2', 'satuan' => 'mhs', 'nominal' => 5000, 'keterangan_aturan' => 'Per Mhs & Per MU'],
                    ['kolom_kategori' => 'korektor', 'nama_komponen' => 'Korektor S3', 'satuan' => 'mhs', 'nominal' => 10000, 'keterangan_aturan' => 'Per LJK'],
                    ['kolom_kategori' => 'pembimbing', 'nama_komponen' => 'Dosen MK. Matrikulasi', 'satuan' => 'sesi', 'nominal' => 50000, 'keterangan_aturan' => 'Per SKS'],
                    ['kolom_kategori' => 'input_nilai', 'nama_komponen' => 'Keanggotaan Perpustakaan', 'satuan' => 'mhs', 'nominal' => 20000],
                    ['kolom_kategori' => 'input_nilai', 'nama_komponen' => 'Pemberkasan dan Input (Pasca S3)', 'satuan' => 'mhs', 'nominal' => 2000],
                ]
            ],

            // 5. KEPANITIAAN UAS FAKULTAS (UTS & UAS)
            [
                'kegiatan' => [
                    'nama_kegiatan' => 'UAS & UTS Fakultas',
                    'kode_kegiatan' => 'uas_fakultas',
                    'tingkat'       => 'dikti',
                    'keterangan'    => 'Ujian Akhir Semester & Tengah Semester Fakultas (termasuk kekhususan FIK)',
                ],
                'tarif' => [
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Penanggung Jawab', 'satuan' => 'kosong', 'nominal' => 400000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Supervisor (Dikti)', 'satuan' => 'kosong', 'nominal' => 375000, 'keterangan_aturan' => '1 orang unsur Dikti'],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Pengarah (Rektorat)', 'satuan' => 'kosong', 'nominal' => 375000, 'keterangan_aturan' => '1 orang unsur Rektorat'],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Ketua', 'satuan' => 'kosong', 'nominal' => 375000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Sekretaris', 'satuan' => 'kosong', 'nominal' => 350000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Bendahara', 'satuan' => 'kosong', 'nominal' => 350000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Koordinator', 'satuan' => 'kosong', 'nominal' => 350000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Anggota', 'satuan' => 'kosong', 'nominal' => 325000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Verifikator/PjMK (FIK)', 'satuan' => 'kosong', 'nominal' => 325000],
                    // Kehadiran & Konsumsi
                    ['kolom_kategori' => 'kehadiran', 'nama_komponen' => 'Kehadiran Panitia', 'satuan' => 'hari', 'nominal' => 10000, 'keterangan_aturan' => '5 hari pra & 5 hari pasca (lembur)'],
                    ['kolom_kategori' => 'konsumsi', 'nama_komponen' => 'Konsumsi Pengawas/Penguji Lisan', 'satuan' => 'hari', 'nominal' => 10000],
                    // Soal & Pengawas
                    ['kolom_kategori' => 'pemb_soal', 'nama_komponen' => 'Pembuatan Soal', 'satuan' => 'kosong', 'nominal' => 25000, 'keterangan_aturan' => 'Per MK'],
                    ['kolom_kategori' => 'pemb_soal', 'nama_komponen' => 'Pembuatan soal MKDU Pilihan Ganda (FIK)', 'satuan' => 'kosong', 'nominal' => 50000, 'keterangan_aturan' => 'Per MK'],
                    ['kolom_kategori' => 'pemb_soal', 'nama_komponen' => 'Pembuatan soal Prodi Pilihan Ganda (FIK)', 'satuan' => 'kosong', 'nominal' => 75000, 'keterangan_aturan' => 'Per MK'],
                    ['kolom_kategori' => 'pemb_soal', 'nama_komponen' => 'Pembuatan soal Pejabat Public Pilihan Ganda (FIK)', 'satuan' => 'kosong', 'nominal' => 100000, 'keterangan_aturan' => 'Per MK'],
                    ['kolom_kategori' => 'pengawas', 'nama_komponen' => 'Pengawas Ruang', 'satuan' => 'shift', 'nominal' => 10000, 'keterangan_aturan' => 'Per shift'],
                    // Korektor
                    ['kolom_kategori' => 'korektor', 'nama_komponen' => 'Korektor', 'satuan' => 'mhs', 'nominal' => 7500],
                    ['kolom_kategori' => 'korektor', 'nama_komponen' => 'Korektor mandiri/Penguji Lisan (FIK)', 'satuan' => 'mhs', 'nominal' => 5000],
                    ['kolom_kategori' => 'korektor', 'nama_komponen' => 'Korektor tim teaching (FIK)', 'satuan' => 'mhs', 'nominal' => 3500],
                    // Penguji FIK
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Penguji Praktek Farmasi Internal (FIK)', 'satuan' => 'mhs', 'nominal' => 40000],
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Penguji Praktek Farmasi External (FIK)', 'satuan' => 'mhs', 'nominal' => 60000],
                    // Pembimbing & Dosen Wali
                    ['kolom_kategori' => 'pembimbing', 'nama_komponen' => 'Dosen Wali', 'satuan' => 'kosong', 'nominal' => 200000, 'keterangan_aturan' => 'Min 20 mhs, bila kurang dihitung 10.000/mhs'],
                    ['kolom_kategori' => 'pembimbing', 'nama_komponen' => 'Pembimbing PA (FIK)', 'satuan' => 'mhs', 'nominal' => 5000],
                    ['kolom_kategori' => 'pembimbing', 'nama_komponen' => 'Pembimbing ASKEB (FIK)', 'satuan' => 'mhs', 'nominal' => 5000],
                ]
            ],

            // 6. KEPANITIAAN PPL, PMS, MAGANG, PKL & BAKSOS
            [
                'kegiatan' => [
                    'nama_kegiatan' => 'PPL, PMS, Magang, PKL & Baksos',
                    'kode_kegiatan' => 'pkl_magang_baksos',
                    'tingkat'       => 'dikti',
                    'keterangan'    => 'Praktik Pengalaman Lapangan, PKL, Magang & Bakti Sosial',
                ],
                'tarif' => [
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Penanggung Jawab', 'satuan' => 'kosong', 'nominal' => 375000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Ketua', 'satuan' => 'kosong', 'nominal' => 350000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Sekretaris', 'satuan' => 'kosong', 'nominal' => 325000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Bendahara', 'satuan' => 'kosong', 'nominal' => 325000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Anggota', 'satuan' => 'kosong', 'nominal' => 300000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Supervisor (DPL Baksos 50%)', 'satuan' => 'kosong', 'nominal' => 325000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'HR. Pimpinan Instansi', 'satuan' => 'kosong', 'nominal' => 350000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'HR. Wakasek/Panitera', 'satuan' => 'kosong', 'nominal' => 300000],
                    // Penguji & Narasumber
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Penguji', 'satuan' => 'kosong', 'nominal' => 50000, 'keterangan_aturan' => 'Per produk'],
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Sekretaris Penguji', 'satuan' => 'kosong', 'nominal' => 25000, 'keterangan_aturan' => 'Per produk'],
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Moderator Pembekalan', 'satuan' => 'sesi', 'nominal' => 75000],
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Narasumber Pembekalan', 'satuan' => 'sesi', 'nominal' => 150000],
                    // Pembimbing
                    ['kolom_kategori' => 'pembimbing', 'nama_komponen' => 'Dosen Pembimbing Kolektif (1 Laporan)', 'satuan' => 'kosong', 'nominal' => 250000, 'keterangan_aturan' => 'Per instansi'],
                    ['kolom_kategori' => 'pembimbing', 'nama_komponen' => 'Dosen Pembimbing Individu (Laporan & Produk)', 'satuan' => 'mhs', 'nominal' => 100000],
                    ['kolom_kategori' => 'pembimbing', 'nama_komponen' => 'Dosen Pembimbing Individu (Laporan)', 'satuan' => 'mhs', 'nominal' => 75000],
                    ['kolom_kategori' => 'pembimbing', 'nama_komponen' => 'Guru Pamong / Dosen Pamong / Pembimbing Instansi', 'satuan' => 'mhs', 'nominal' => 75000],
                ]
            ],

            // 7. KEPANITIAAN MUNAQASAH DAN TUGAS AKHIR S1/D3
            [
                'kegiatan' => [
                    'nama_kegiatan' => 'Munaqasah dan Tugas Akhir S1/D3',
                    'kode_kegiatan' => 'munaqasah_ta',
                    'tingkat'       => 'dikti',
                    'keterangan'    => 'Sidang Proposal, Munaqasah Skripsi & Tugas Akhir S1/D3',
                ],
                'tarif' => [
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Penanggung Jawab', 'satuan' => 'kosong', 'nominal' => 375000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Ketua', 'satuan' => 'kosong', 'nominal' => 350000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Sekretaris', 'satuan' => 'kosong', 'nominal' => 325000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Bendahara', 'satuan' => 'kosong', 'nominal' => 325000],
                    ['kolom_kategori' => 'kepanitiaan', 'nama_komponen' => 'Anggota', 'satuan' => 'kosong', 'nominal' => 300000],
                    // Penguji Sidang
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Penguji Proposal I & II', 'satuan' => 'mhs', 'nominal' => 25000],
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Penguji I & II', 'satuan' => 'mhs', 'nominal' => 100000],
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Penguji Tamu', 'satuan' => 'mhs', 'nominal' => 100000],
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Ketua Sidang Munaqasah & TA', 'satuan' => 'mhs', 'nominal' => 100000],
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Sekretaris Sidang Munaqasah & TA', 'satuan' => 'mhs', 'nominal' => 75000],
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Penguji Produk I & II', 'satuan' => 'mhs', 'nominal' => 50000],
                    ['kolom_kategori' => 'penguji', 'nama_komponen' => 'Penguji Komprehensif/kompetensi I & II', 'satuan' => 'mhs', 'nominal' => 15000],
                    // Pembimbing Skripsi & Proposal
                    ['kolom_kategori' => 'pembimbing', 'nama_komponen' => 'Pembimbing Proposal I & II', 'satuan' => 'mhs', 'nominal' => 50000],
                    ['kolom_kategori' => 'pembimbing', 'nama_komponen' => 'Pembimbing I & II', 'satuan' => 'mhs', 'nominal' => 125000],
                    // Layanan Perpustakaan / Input Nilai
                    ['kolom_kategori' => 'input_nilai', 'nama_komponen' => 'Scan Plagiasi di Perpustakaan', 'satuan' => 'kosong', 'nominal' => 5000, 'keterangan_aturan' => 'Per dokumen'],
                    ['kolom_kategori' => 'input_nilai', 'nama_komponen' => 'Pemrosesan Skripsi di Perpustakaan', 'satuan' => 'kosong', 'nominal' => 4000, 'keterangan_aturan' => 'Per eksemplar'],
                ]
            ],
        ];

        DB::beginTransaction();
        try {
            foreach ($activities as $act) {
                $kegiatan = MasterJenisKegiatan::updateOrCreate(
                    ['kode_kegiatan' => $act['kegiatan']['kode_kegiatan']],
                    $act['kegiatan']
                );

                foreach ($act['tarif'] as $item) {
                    BarokahKepanitiaan::updateOrCreate(
                        [
                            'id_jenis_kegiatan' => $kegiatan->id_jenis_kegiatan,
                            'nama_komponen'     => $item['nama_komponen'],
                            'kolom_kategori'    => $item['kolom_kategori'],
                        ],
                        array_merge($item, [
                            'tahun_anggaran' => '2026',
                        ])
                    );
                }
            }

            DB::commit();
            $this->command->info('Data Master Kegiatan & Barokah Kepanitiaan APBM 2026 berhasil di-seed.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Gagal seeding: ' . $e->getMessage());
        }
    }
}
