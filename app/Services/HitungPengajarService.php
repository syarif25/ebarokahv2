<?php

namespace App\Services;

use App\Models\KehadiranLembaga;
use App\Models\KehadiranPengajar;
use App\Models\PengaturanTahunAcuan;
use App\Models\Tunkel;
use App\Models\TunjAnak;
use Illuminate\Support\Facades\DB;

class HitungPengajarService
{
    /**
     * Konversi string bulan Indonesia ke format YYYY-MM-01
     */
    public static function getPeriodeDate($bulan, $tahun)
    {
        $mapBulan = [
            'januari' => '01', 'februari' => '02', 'maret' => '03', 'april' => '04',
            'mei' => '05', 'juni' => '06', 'juli' => '07', 'agustus' => '08',
            'september' => '09', 'oktober' => '10', 'november' => '11', 'desember' => '12'
        ];

        $blnRaw = strtolower(trim($bulan));
        if (is_numeric($blnRaw)) {
            $blnNum = str_pad((int)$blnRaw, 2, '0', STR_PAD_LEFT);
        } else {
            $blnNum = $mapBulan[$blnRaw] ?? date('m');
        }

        return $tahun . '-' . $blnNum . '-01';
    }

    /**
     * Build Metadata pengajar (tunkel, tunj_anak, tahun_acuan_map, walkes_config)
     */
    public static function buildMetaPengajar($periodeDate = null)
    {
        // 1. Walkes Config
        $walkesConfig = [];
        $walkesRows = DB::table('master_tarif_walkes')->get();
        foreach ($walkesRows as $w) {
            $walkesConfig[$w->kode_walkes] = (int)$w->nominal;
        }
        if (empty($walkesConfig)) {
            $walkesConfig = ['Ya' => 100000, 'walkes_sklh' => 75000, 'walkes_amsilati' => 25000];
        }

        // 2. Referensi Tunkel & Tunj Anak
        $nominalTunkel = DB::table('tunkel')->first();
        $nominalTunjAnak = DB::table('tunjanak')->first();

        // 3. Tahun Acuan
        $tahunAcuanMap = [];
        $acuanRows = DB::table('pengaturan_tahun_acuan')->get();
        foreach ($acuanRows as $cfg) {
            $tahunAcuanMap[trim($cfg->id_bidang)] = (int)$cfg->tahun_acuan;
        }

        return [
            'nominaltunkel'    => $nominalTunkel,
            'nominaltunj_anak' => $nominalTunjAnak,
            'tahun_acuan_map'  => $tahunAcuanMap,
            'walkes_config'    => $walkesConfig,
            'periode_date'     => $periodeDate ?: date('Y-m-01'),
        ];
    }

    /**
     * Hitung gaji dan barokah untuk 1 row pengajar
     */
    public static function hitungRowPengajar($key, $metaData)
    {
        $nominaltunkel    = $metaData['nominaltunkel'] ?? null;
        $nominaltunj_anak = $metaData['nominaltunj_anak'] ?? null;
        $tahun_acuan_map  = $metaData['tahun_acuan_map'] ?? [];
        $walkes_config    = $metaData['walkes_config'] ?? ['Ya' => 100000, 'walkes_sklh' => 75000, 'walkes_amsilati' => 25000];
        $periode_date     = $metaData['periode_date'] ?? date('Y-m-01');

        // Kehadiran dasar
        $transportTarif   = (int)($key->nominal_transport ?? 0);
        $jml_kehadiran    = (int)($key->jumlah_hadir ?? 0) * $transportTarif;
        $nominal_hadir_15 = (int)($key->jumlah_hadir_15 ?? 0) * 15000;
        $nominal_hadir_10 = (int)($key->jumlah_hadir_10 ?? 0) * 10000;

        // Tahun Acuan
        $tahun_default  = (int)date('Y');
        if (isset($tahun_acuan_map['Default']))      $tahun_default = $tahun_acuan_map['Default'];
        if (isset($tahun_acuan_map['Pengurus']))     $tahun_default = $tahun_acuan_map['Pengurus'];
        if (isset($tahun_acuan_map['Kantor Pusat'])) $tahun_default = $tahun_acuan_map['Kantor Pusat'];

        $tahun_madrasah = $tahun_acuan_map['Bidang DIKJAR-M'] ?? $tahun_default;
        $tahun_sekolah  = $tahun_acuan_map['Bidang DIKJAR']   ?? $tahun_default;
        $tahun_pt       = $tahun_acuan_map['Bidang DIKTI']    ?? $tahun_default;

        $id_bidang      = $key->id_bidang ?? '';
        $kategori       = $key->kategori ?? '';

        // Masa Pengabdian (MP)
        if (($kategori == 'GTY' || $kategori == 'GTT') && $id_bidang == 'Bidang DIKJAR-M') {
            $tmt = !empty($key->tmt_guru) ? strtotime($key->tmt_guru) : time();
            $mp = $tahun_madrasah - (int)date('Y', $tmt);
        } elseif (($kategori == 'GTY' || $kategori == 'GTT') && $id_bidang == 'Bidang DIKJAR') {
            $tmt = !empty($key->tmt_guru) ? strtotime($key->tmt_guru) : time();
            $mp = $tahun_sekolah - (int)date('Y', $tmt);
        } elseif (($kategori == 'DTY' || $kategori == 'DTT') && isset($key->nama_lembaga) && $key->nama_lembaga == "Ma'had Aly Sukorejo") {
            $tmt = !empty($key->tmt_maif) ? strtotime($key->tmt_maif) : time();
            $mp = $tahun_pt - (int)date('Y', $tmt);
        } else {
            $tmt_dosen_val = !empty($key->tmt_dosen) ? $key->tmt_dosen : date('Y-m-d');
            $mp = $tahun_pt - (int)date('Y', strtotime($tmt_dosen_val));
        }
        $masa_p = ($mp > 0) ? (int)$mp : 0;

        // Tunkel
        $tunkel = 0;
        if (isset($key->tunj_kel) && $key->tunj_kel == 'Ya' && $masa_p >= 2 && $nominaltunkel) {
            $tunkel = (int)$nominaltunkel->besaran_tunkel;
        }
        $status_aktif = $key->status_aktif ?? '';
        if ($status_aktif == 'Cuti 50%')  $tunkel = (int)round($tunkel * 0.5);
        if ($status_aktif == 'Cuti 100%') $tunkel = 0;

        // Tunjangan Anak
        $tunja_anak = 0;
        if (isset($key->tunj_anak) && $key->tunj_anak == 'Ya' && $nominaltunj_anak) {
            $tunja_anak = (int)$nominaltunj_anak->nominal_tunj_anak;
        }
        if ($status_aktif == 'Cuti 50%')  $tunja_anak = (int)round($tunja_anak * 0.5);
        if ($status_aktif == 'Cuti 100%') $tunja_anak = 0;

        // Walkes
        $walkes_key  = $key->walkes ?? '';
        $tunj_walkes = (int)($walkes_config[$walkes_key] ?? 0);
        if ($status_aktif == 'Cuti 50%')  $tunj_walkes = (int)round($tunj_walkes * 0.5);
        if ($status_aktif == 'Cuti 100%') $tunj_walkes = 0;

        // Rank / Honor Mengajar
        $rank     = 0;
        $ijazah   = $key->ijazah_terakhir ?? '';
        $id_lemb  = (int)($key->id_lembaga ?? 0);

        if ($kategori == 'GTY' || $kategori == 'GTT') {
            $rowRank = DB::table('barokah_pengajar')
                ->where('min_tmp_mengajar', '<=', $masa_p)
                ->where('max_tmp_mengajar', '>=', $masa_p)
                ->where('ijazah', $ijazah)
                ->where('kategori', 'Guru')
                ->first();
            if ($rowRank) $rank = (int)$rowRank->nominal;
        } else {
            $kat_dosen = ($id_lemb == 39) ? 'Dosen MAIF' : (($id_lemb == 37) ? 'Dosen FIK' : 'Dosen UNIB');
            $rowRank = DB::table('barokah_pengajar')
                ->where('min_tmp_mengajar', '<=', $masa_p)
                ->where('max_tmp_mengajar', '>=', $masa_p)
                ->where('ijazah', $ijazah)
                ->where('kategori', $kat_dosen)
                ->first();
            if ($rowRank) $rank = (int)$rowRank->nominal;
        }

        $jumlah_sks    = (int)($key->jumlah_sks ?? 0);
        $jumlah_piket  = (int)($key->jumlah_hadir_piket ?? 0);
        $mengajar      = (int)round($rank * $jumlah_sks);
        if ($status_aktif == 'Cuti 50%')  $mengajar = (int)round($mengajar * 0.5);
        if ($status_aktif == 'Cuti 100%') $mengajar = 0;

        $rank_piket    = ($rank > 0) ? (float)($rank / 4) : 0;
        $barokah_piket = (int)round($rank_piket * $jumlah_piket);

        // Kehormatan
        $kehormatan     = 0;
        $kat_kehormatan = (in_array($kategori, ['GTY', 'GTT'])) ? 'Guru' : 'Dosen';
        if (isset($key->kehormatan) && strtolower($key->kehormatan) == 'ya') {
            $rowKeh = DB::table('barokah_kehormatan_pengajar')
                ->where('min_masa_pengabdian', '<=', $masa_p)
                ->where('max_masa_pengabdian', '>=', $masa_p)
                ->where('kategori', $kat_kehormatan)
                ->first();
            if ($rowKeh) $kehormatan = (int)$rowKeh->nominal;
        }
        if ($status_aktif == 'Cuti 50%')  $kehormatan = (int)round($kehormatan * 0.5);
        if ($status_aktif == 'Cuti 100%') $kehormatan = 0;

        // DTY / GTY Tetap
        $dty          = 0;
        $status_serti = $key->status_sertifikasi ?? '';
        if ($kategori == 'GTY' && $status_serti == 'Belum' && $masa_p > 2) {
            $rowTetap = DB::table('barokah_pengajar_tetap')->where('kategori', 'Guru')->first();
            if ($rowTetap) $dty = (int)$rowTetap->nominal;
        } elseif ($kategori == 'DTY' && $status_serti == 'Belum' && $masa_p > 2) {
            $rowTetap = DB::table('barokah_pengajar_tetap')->where('kategori', 'Dosen')->first();
            if ($rowTetap) $dty = (int)$rowTetap->nominal;
        }
        if ($status_aktif == 'Cuti 50%')  $dty = (int)round($dty * 0.5);
        if ($status_aktif == 'Cuti 100%') $dty = 0;

        // Jafung
        $jafung     = 0;
        $jabatan_ak = $key->jabatan_akademik ?? '';
        $jafung_flag = $key->jafung ?? '';
        if ($jabatan_ak != '' && $jafung_flag == 'Ya') {
            $rowJaf = DB::table('barokah_jafung')
                ->where('id_barokah_jafung', $jabatan_ak)
                ->first();
            if ($rowJaf) $jafung = (int)$rowJaf->nominal;
        }
        if ($status_aktif == 'Cuti 50%')  $jafung = (int)round($jafung * 0.5);
        if ($status_aktif == 'Cuti 100%') $jafung = 0;

        // Potongan Historis
        $id_pengajar = (int)($key->id_pengajar ?? 0);
        $potongan = (int)DB::table('potongan_pengajar')
            ->where('id_pengajar', $id_pengajar)
            ->where(function ($q) use ($periode_date) {
                $q->whereNull('min_periode_potongan')
                  ->orWhere('min_periode_potongan', '<=', DB::raw("LAST_DAY('$periode_date')"));
            })
            ->where(function ($q) use ($periode_date) {
                $q->whereNull('max_periode_potongan')
                  ->orWhere('max_periode_potongan', '>=', $periode_date);
            })
            ->sum('nominal_potongan');

        // Barokah Tambahan Khusus Historis
        $tambahan = (int)DB::table('barokah_tambahan')
            ->where('id_pengajar', $id_pengajar)
            ->where(function ($q) use ($periode_date) {
                $q->whereNull('min_periode_tambahan')
                  ->orWhere('min_periode_tambahan', '<=', DB::raw("LAST_DAY('$periode_date')"));
            })
            ->where(function ($q) use ($periode_date) {
                $q->whereNull('max_periode_tambahan')
                  ->orWhere('max_periode_tambahan', '>=', $periode_date);
            })
            ->sum('nominal_tambahan');

        // Grand Total
        $jumlah   = $jml_kehadiran + $nominal_hadir_15 + $nominal_hadir_10
                  + $barokah_piket + $mengajar + $tunkel + $tunja_anak
                  + $tunj_walkes + $kehormatan + $dty + $jafung + $tambahan;
        $diterima = $jumlah - $potongan;

        return [
            'id_pengajar'        => $id_pengajar,
            'jumlah_sks'         => $jumlah_sks,
            'mp'                 => $masa_p,
            'jumlah_hadir'       => (int)($key->jumlah_hadir ?? 0),
            'nominal_kehadiran'  => $jml_kehadiran,
            'jumlah_hadir_15'    => (int)($key->jumlah_hadir_15 ?? 0),
            'nominal_hadir_15'   => $nominal_hadir_15,
            'jumlah_hadir_10'    => (int)($key->jumlah_hadir_10 ?? 0),
            'nominal_hadir_10'   => $nominal_hadir_10,
            'jumlah_hadir_piket' => $jumlah_piket,
            'rank_piket'         => $rank_piket,
            'barokah_piket'      => $barokah_piket,
            'rank'               => $rank,
            'mengajar'           => $mengajar,
            'dty'                => $dty,
            'jafung'             => $jafung,
            'tunkel'             => $tunkel,
            'tun_anak'           => $tunja_anak,
            'walkes'             => $tunj_walkes,
            'kehormatan'         => $kehormatan,
            'khusus'             => $tambahan,
            'potongan'           => $potongan,
            'jumlah'             => $jumlah,
            'diterima'           => $diterima,
            'jml_kehadiran'      => $jml_kehadiran,
            'tunja_anak'         => $tunja_anak,
            'tunj_walkes'        => $tunj_walkes,
            'tambahan'           => $tambahan,
            'masa_p'             => $masa_p,
        ];
    }

    /**
     * Hitung perbandingan komponen sebelumnya
     */
    public static function getKomponenSebelumnyaPengajar($idLembaga, $idKehadiranLembaga)
    {
        $komponenSebelumnya = [];

        $lastPeriode = DB::table('kehadiran_lembaga as kl')
            ->join('total_barokah_pengajar as tbp', 'kl.id_kehadiran_lembaga', '=', 'tbp.id_kehadiran_lembaga')
            ->where('kl.id_lembaga', $idLembaga)
            ->where('kl.id_kehadiran_lembaga', '<', $idKehadiranLembaga)
            ->whereIn('kl.status', ['acc', 'selesai', 'Sudah'])
            ->orderBy('kl.id_kehadiran_lembaga', 'desc')
            ->select('kl.id_kehadiran_lembaga')
            ->first();

        if ($lastPeriode) {
            $prevRows = DB::table('total_barokah_pengajar')
                ->where('id_kehadiran_lembaga', $lastPeriode->id_kehadiran_lembaga)
                ->select(
                    'id_pengajar', 'mengajar', 'dty', 'jafung', 'tunkel', 'tun_anak', 'kehormatan', 'walkes',
                    DB::raw('(mengajar + dty + jafung + tunkel + tun_anak + kehormatan + walkes) as total_tetap_prev')
                )
                ->get();

            foreach ($prevRows as $p) {
                $komponenSebelumnya[$p->id_pengajar] = [
                    'mengajar'         => (int)$p->mengajar,
                    'dty'              => (int)$p->dty,
                    'jafung'           => (int)$p->jafung,
                    'tunkel'           => (int)$p->tunkel,
                    'tun_anak'         => (int)$p->tun_anak,
                    'kehormatan'       => (int)$p->kehormatan,
                    'walkes'           => (int)$p->walkes,
                    'total_tetap_prev' => (int)$p->total_tetap_prev
                ];
            }
        }

        return $komponenSebelumnya;
    }
}
