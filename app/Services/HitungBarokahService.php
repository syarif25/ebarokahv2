<?php

namespace App\Services;

use App\Models\KehadiranLembaga;
use App\Models\LiburPesantren;
use App\Models\PengaturanTahunAcuan;
use App\Models\Tunkel;
use App\Models\TunjAnak;
use App\Models\BarokahKehormatan;
use App\Models\TBebanKerja;
use App\Models\PotonganUmana;
use Illuminate\Support\Facades\DB;

class HitungBarokahService
{
    /**
     * Hitung hari wajib hadir bulanan (Kantor masuk: Sabtu - Kamis, Jumat libur).
     */
    public static function hitungWajibHadirBulanan($bulan, $tahun, $wajibHadirPerMinggu, $liburEkstra = 0)
    {
        $mapBulan = [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4, 'mei' => 5, 'juni' => 6,
            'juli' => 7, 'agustus' => 8, 'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12
        ];

        if (is_numeric($bulan) && (int)$bulan >= 1 && (int)$bulan <= 12) {
            $bln = (int)$bulan;
        } elseif (isset($mapBulan[strtolower(trim($bulan))])) {
            $bln = $mapBulan[strtolower(trim($bulan))];
        } else {
            $bln = (int)date('n');
        }

        $thn = (int)$tahun;
        $totalHari = (int)date('t', mktime(0, 0, 0, $bln, 1, $thn));

        $jumlahJumat = 0;
        $hariPertamaBulan = mktime(0, 0, 0, $bln, 1, $thn);
        $namaHariPertama = (int)date('N', $hariPertamaBulan); // 1=Senin, 5=Jumat, 6=Sabtu, 7=Minggu

        if ($namaHariPertama <= 5) {
            $tanggalJumatPertama = 5 - $namaHariPertama + 1;
        } else {
            $tanggalJumatPertama = 12 - $namaHariPertama + 1;
        }

        for ($tgl = $tanggalJumatPertama; $tgl <= $totalHari; $tgl += 7) {
            $jumlahJumat++;
        }

        $totalHariKerja = $totalHari - $jumlahJumat - (int)$liburEkstra;
        if ($totalHariKerja < 0) {
            $totalHariKerja = 0;
        }

        $hariKerjaPerMinggu = 6;
        $wajibHadirBulanan = round(($totalHariKerja / $hariKerjaPerMinggu) * (float)$wajibHadirPerMinggu);

        return (int)$wajibHadirBulanan;
    }

    /**
     * Hitung kalkulasi lengkap periode kehadiran struktural per Umana'.
     */
    public static function hitungPeriode($idKehadiranLembaga)
    {
        $sql = "
            SELECT 
                kl.id_kehadiran_lembaga, kl.bulan, kl.tahun, kl.file, kl.status as status_pengajuan, kl.catatan_umum_pimpinan,
                l.id_lembaga, l.nama_lembaga, l.id_bidang,
                k.id_kehadiran, k.jumlah_hadir, k.jumlah_tugas, k.jumlah_izin, k.jumlah_sakit, k.id_penempatan,
                u.nik, u.nama_lengkap, u.gelar_depan, u.gelar_belakang,
                u.status_sertifikasi, u.tmt_dosen, u.tmt_guru, u.tmt_maif,
                (SELECT p_sub.kategori FROM pengajar p_sub WHERE p_sub.nik = u.nik AND (p_sub.tgl_selesai IS NULL OR p_sub.tgl_selesai = '0000-00-00' OR p_sub.tgl_selesai >= CURDATE()) LIMIT 1) AS kategori_pengajar,
                u.tmt_struktural, p.tunj_kel, p.tunj_anak, p.tunj_mp, p.kehormatan,
                kb.nama_jabatan AS nama_jabatan, kb.barokah AS tunjab, kb.wajib_hadir,
                t.nominal_transport
            FROM kehadiran k
            JOIN kehadiran_lembaga kl ON kl.id_kehadiran_lembaga = k.id_kehadi
            JOIN penempatan p ON p.id_penempatan = k.id_penempatan
            JOIN umana u ON u.nik = p.nik
            JOIN lembaga l ON l.id_lembaga = p.id_lembaga
            JOIN ketentuan_barokah kb ON kb.id_ketentuan = p.id_ketentuan
            LEFT JOIN transport t ON t.id_transport = p.kategori_trans
            WHERE kl.id_kehadiran_lembaga = ?
            ORDER BY kb.barokah DESC, u.nama_lengkap ASC
        ";

        $rows = DB::select($sql, [$idKehadiranLembaga]);
        if (empty($rows)) {
            $periode = KehadiranLembaga::with('lembaga')->find($idKehadiranLembaga);
            return [
                'rows' => [],
                'periode' => $periode,
                'totals' => self::emptyTotals(),
            ];
        }

        $tunkelRef = Tunkel::first();
        $tunjAnakRef = TunjAnak::first();
        $kehormatanRef = BarokahKehormatan::all();

        // Config tahun acuan
        $tahunAcuanMap = [];
        $configTahun = PengaturanTahunAcuan::all();
        foreach ($configTahun as $cfg) {
            $tahunAcuanMap[trim($cfg->id_bidang ?? '')] = (int)$cfg->tahun_acuan;
        }

        if (isset($tahunAcuanMap['Default'])) {
            $tahunDefault = $tahunAcuanMap['Default'];
        } elseif (isset($tahunAcuanMap['Pengurus'])) {
            $tahunDefault = $tahunAcuanMap['Pengurus'];
        } elseif (isset($tahunAcuanMap['Kantor Pusat'])) {
            $tahunDefault = $tahunAcuanMap['Kantor Pusat'];
        } else {
            $tahunDefault = (int)date('Y');
        }

        // Libur Pesantren
        $bulanString = trim($rows[0]->bulan ?? '');
        $tahunInt = (int)($rows[0]->tahun ?? date('Y'));
        $liburPesantren = (int)LiburPesantren::where('bulan', $bulanString)
            ->where('tahun', $tahunInt)
            ->sum('jumlah_hari');

        // Komponen bulan lalu (dari total_barokah jika ada)
        $komponenSebelumnya = [];
        $lastPeriode = DB::select("
            SELECT DISTINCT kl.id_kehadiran_lembaga 
            FROM kehadiran_lembaga kl
            JOIN total_barokah tb ON kl.id_kehadiran_lembaga = tb.id_kehadiran
            WHERE kl.id_lembaga = ? 
            AND kl.id_kehadiran_lembaga < ? 
            AND kl.status IN ('acc', 'selesai', 'Sudah')
            ORDER BY kl.id_kehadiran_lembaga DESC 
            LIMIT 1
        ", [$rows[0]->id_lembaga, $idKehadiranLembaga]);

        if (!empty($lastPeriode)) {
            $tbPrev = DB::select("
                SELECT p.nik, tb.tunjab, tb.tmp, tb.tunkel, tb.tunj_anak, tb.kehormatan,
                       (tb.tunjab + tb.tmp + tb.tunkel + tb.tunj_anak + tb.kehormatan) AS total_tetap_prev
                FROM total_barokah tb
                JOIN penempatan p ON p.id_penempatan = tb.id_penempatan
                WHERE tb.id_kehadiran = ?
            ", [$lastPeriode[0]->id_kehadiran_lembaga]);

            foreach ($tbPrev as $p) {
                $komponenSebelumnya[$p->nik] = [
                    'tunjab' => (int)$p->tunjab,
                    'tmp' => (int)$p->tmp,
                    'tunkel' => (int)$p->tunkel,
                    'tunj_anak' => (int)$p->tunj_anak,
                    'kehormatan' => (int)$p->kehormatan,
                    'total_tetap_prev' => (int)$p->total_tetap_prev,
                ];
            }
        }

        // Catatan khusus Umana saat ini jika sudah tersimpan di total_barokah
        $catatanKhusus = [];
        $tbCurrent = DB::select("SELECT id_penempatan, catatan_khusus_umana FROM total_barokah WHERE id_kehadiran = ?", [$idKehadiranLembaga]);
        foreach ($tbCurrent as $tc) {
            $catatanKhusus[$tc->id_penempatan] = $tc->catatan_khusus_umana;
        }

        $totalTunjab = $totalTmp = $totalKehadiran = $totalTunkel = $totalTunjAnak = 0;
        $totalKehormatan = $totalTbk = $totalBarokah = $totalPotongan = $grandTotal = 0;

        foreach ($rows as $r) {
            $idBidangRow = trim($r->id_bidang ?? '');
            $tahunSkrg = isset($tahunAcuanMap[$idBidangRow]) ? $tahunAcuanMap[$idBidangRow] : $tahunDefault;

            // Masa Pengabdian (MP)
            $tahunAwal = !empty($r->tmt_struktural) ? (int)date('Y', strtotime($r->tmt_struktural)) : 0;
            $r->mp = max(0, $tahunSkrg - $tahunAwal);

            // Wajib Hadir Bulanan
            $wajibHadirPerMinggu = (int)($r->wajib_hadir ?? 0);
            $r->wajib_hadir_bulanan = self::hitungWajibHadirBulanan($r->bulan, $r->tahun, $wajibHadirPerMinggu, $liburPesantren);

            // Kehadiran Efektif: (Hadir*1) + (Tugas*1) + (Izin*0.25) + (Sakit*1.0)
            $jumlahHadir = (int)$r->jumlah_hadir;
            $jumlahTugas = (int)($r->jumlah_tugas ?? 0);
            $jumlahIzin = (int)($r->jumlah_izin ?? 0);
            $jumlahSakit = (int)($r->jumlah_sakit ?? 0);

            $kehadiranEfektif = ($jumlahHadir * 1) + ($jumlahTugas * 1) + ($jumlahIzin * 0.25) + ($jumlahSakit * 1.0);

            if ($r->wajib_hadir_bulanan > 0) {
                $r->persentase_kehadiran = round(($kehadiranEfektif / $r->wajib_hadir_bulanan) * 100, 2);
            } else {
                $r->persentase_kehadiran = 0;
            }

            // Punishment Tunjab jika kehadiran efektif < 50% dari wajib hadir
            $r->tunjab_asli = (int)$r->tunjab;
            if ($r->wajib_hadir_bulanan > 0 && ($kehadiranEfektif / $r->wajib_hadir_bulanan) < 0.5) {
                $rasioKehadiran = $kehadiranEfektif / $r->wajib_hadir_bulanan;
                $r->tunjab = (int)round($r->tunjab_asli * $rasioKehadiran);
                $r->is_punishment_tunjab = true;
            } else {
                $r->is_punishment_tunjab = false;
            }

            // TMP: jika tunj_mp != 'Tidak' dan mp >= 3 -> floor(mp / 3) * 10.000
            $r->tmp = ($r->tunj_mp != 'Tidak' && $r->mp >= 3) ? floor($r->mp / 3) * 10000 : 0;

            // Uang Kehadiran Fisik (Transport)
            $nominalTransport = (int)($r->nominal_transport ?? 0);
            $r->nominal_kehadiran = $jumlahHadir * $nominalTransport;

            // Tunkel
            $r->tunkel = ($r->tunj_kel == 'Ya' && $r->mp >= 2) ? (int)($tunkelRef->besaran_tunkel ?? 0) : 0;

            // Tunjangan Anak
            $r->tunj_anak = ($r->tunj_anak == 'Ya') ? (int)($tunjAnakRef->nominal_tunj_anak ?? 0) : 0;

            // Kehormatan
            $r->nilai_kehormatan = 0;
            if (!empty($r->kehormatan) && strtolower($r->kehormatan) == 'ya') {
                foreach ($kehormatanRef as $ref) {
                    if ($r->mp >= (int)$ref->min_masa_pengabdian && $r->mp <= (int)$ref->max_masa_pengabdian) {
                        $r->nilai_kehormatan = (int)$ref->nominal_kehormatan;
                        break;
                    }
                }
            }

            // TBK
            $tbkRow = DB::select("
                SELECT SUM(nominal_tbk) AS jumlah_tbk 
                FROM t_beban_kerja 
                WHERE id_penempatan = ? 
                AND (max_periode IS NULL OR max_periode >= CURDATE())
            ", [$r->id_penempatan]);
            $r->tbk = (int)($tbkRow[0]->jumlah_tbk ?? 0);

            // Potongan
            $potonganRow = DB::select("
                SELECT SUM(nominal_potongan) AS jumlah 
                FROM potongan_umana 
                WHERE id_penempatan = ? 
                AND (max_periode_potongan IS NULL OR max_periode_potongan >= CURDATE())
            ", [$r->id_penempatan]);
            $r->potongan = (int)($potonganRow[0]->jumlah ?? 0);

            // Subtotal Barokah Kotor
            $r->jumlah_barokah = (int)$r->tunjab + (int)$r->tmp + (int)$r->nominal_kehadiran +
                                 (int)$r->tunkel + (int)$r->tunj_anak + (int)$r->nilai_kehormatan + (int)$r->tbk;

            // Bersih Diterima
            $r->diterima = (int)$r->jumlah_barokah - (int)$r->potongan;

            // Warning Perubahan Komponen Tetap vs Bulan Lalu
            $r->catatan_khusus = $catatanKhusus[$r->id_penempatan] ?? '';
            $totalTetapSekarang = (int)$r->tunjab + (int)$r->tmp + (int)$r->tunkel + (int)$r->tunj_anak + (int)$r->nilai_kehormatan;
            $r->is_warning = false;
            $r->selisih_komponen = 0;
            $r->komponen_berubah = [];

            if (isset($komponenSebelumnya[$r->nik])) {
                $prev = $komponenSebelumnya[$r->nik];
                if ((int)$r->tunjab !== $prev['tunjab']) $r->komponen_berubah[] = 'tunjab';
                if ((int)$r->tmp !== $prev['tmp']) $r->komponen_berubah[] = 'tmp';
                if ((int)$r->tunkel !== $prev['tunkel']) $r->komponen_berubah[] = 'tunkel';
                if ((int)$r->tunj_anak !== $prev['tunj_anak']) $r->komponen_berubah[] = 'tunj_anak';
                if ((int)$r->nilai_kehormatan !== $prev['kehormatan']) $r->komponen_berubah[] = 'kehormatan';

                $tetapPrev = $prev['total_tetap_prev'];
                if ($totalTetapSekarang !== $tetapPrev) {
                    $r->is_warning = true;
                    $r->selisih_komponen = $totalTetapSekarang - $tetapPrev;
                }
            }

            // Agregat Total Lembaga
            $totalTunjab     += (int)$r->tunjab;
            $totalTmp        += (int)$r->tmp;
            $totalKehadiran  += (int)$r->nominal_kehadiran;
            $totalTunkel     += (int)$r->tunkel;
            $totalTunjAnak   += (int)$r->tunj_anak;
            $totalKehormatan += (int)$r->nilai_kehormatan;
            $totalTbk        += (int)$r->tbk;
            $totalBarokah    += (int)$r->jumlah_barokah;
            $totalPotongan   += (int)$r->potongan;
            $grandTotal      += (int)$r->diterima;
        }

        $periode = (object)[
            'id_kehadiran_lembaga' => $rows[0]->id_kehadiran_lembaga,
            'id_lembaga'           => $rows[0]->id_lembaga,
            'nama_lembaga'         => $rows[0]->nama_lembaga,
            'bulan'                => $rows[0]->bulan,
            'tahun'                => $rows[0]->tahun,
            'file'                 => $rows[0]->file,
            'status'               => $rows[0]->status_pengajuan,
            'catatan_umum_pimpinan'=> $rows[0]->catatan_umum_pimpinan ?? '',
            'libur_pesantren'      => $liburPesantren,
        ];

        return [
            'rows'    => $rows,
            'periode' => $periode,
            'totals'  => [
                'total_tunjab'     => $totalTunjab,
                'total_tmp'        => $totalTmp,
                'total_kehadiran'  => $totalKehadiran,
                'total_tunkel'     => $totalTunkel,
                'total_tunjanak'   => $totalTunjAnak,
                'total_kehormatan' => $totalKehormatan,
                'total_tbk'        => $totalTbk,
                'total_barokah'    => $totalBarokah,
                'total_potongan'   => $totalPotongan,
                'grand_total'      => $grandTotal,
            ]
        ];
    }

    private static function emptyTotals()
    {
        return [
            'total_tunjab'     => 0,
            'total_tmp'        => 0,
            'total_kehadiran'  => 0,
            'total_tunkel'     => 0,
            'total_tunjanak'   => 0,
            'total_kehormatan' => 0,
            'total_tbk'        => 0,
            'total_barokah'    => 0,
            'total_potongan'   => 0,
            'grand_total'      => 0,
        ];
    }
}
