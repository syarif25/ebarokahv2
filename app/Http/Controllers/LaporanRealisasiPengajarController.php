<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanRealisasiPengajarController extends Controller
{
    /**
     * Tampilan Laporan Realisasi Barokah Per Bidang (Pengajar)
     * Format kolom: No | Periode | Nama Bidang | Mengajar | DTY | Jafung | Transport | Hadir 15 | Hadir 10 | Piket | Tunkel | Tunj Anak | Kehormatan | Walkes | Tambahan | Jumlah | Potongan | Diterima | Aksi
     */
    public function index(Request $request)
    {
        // Ambil daftar Bidang dari master lembaga yang memiliki tenaga pengajar
        $bidangList = DB::table('lembaga')
            ->whereNotNull('id_bidang')
            ->where('id_bidang', '!=', '')
            ->distinct()
            ->orderBy('id_bidang', 'asc')
            ->pluck('id_bidang')
            ->map(function($b) {
                $trimmed = trim($b);
                if ($trimmed === 'Bidang DIKJAR') return 'Bidang DIKDASMEN';
                if ($trimmed === 'Bidang DIKJAR-M') return 'Bidang DIKDASMEN-M';
                if ($trimmed === 'Bidang Kepesantrenan dan PU') return 'Bidang PU';
                return $trimmed;
            })
            ->unique()
            ->sort()
            ->values()
            ->toArray();

        // Cari periode default dari total_barokah_pengajar terbaru
        $latestRecord = DB::table('total_barokah_pengajar')
            ->select('bulan', 'tahun')
            ->whereNotNull('bulan')
            ->where('bulan', '!=', '')
            ->whereNotNull('tahun')
            ->where('tahun', '!=', '')
            ->orderBy('id_total_barokah_pengajar', 'desc')
            ->first();

        $defaultBulan = $latestRecord ? $latestRecord->bulan : 'Oktober';
        $defaultTahun = $latestRecord ? $latestRecord->tahun : date('Y');

        $selectedBulan  = $request->input('bulan', $defaultBulan);
        $selectedTahun  = $request->input('tahun', $defaultTahun);
        $selectedBidang = $request->input('bidang', '');

        if ($request->filled('periode')) {
            $parts = explode(' ', trim($request->input('periode')));
            if (count($parts) >= 2) {
                $selectedBulan = $parts[0];
                $selectedTahun = $parts[1];
            }
        } elseif ($request->filled('filter_periode')) {
            $parts = explode(' ', trim($request->input('filter_periode')));
            if (count($parts) >= 2) {
                $selectedBulan = $parts[0];
                $selectedTahun = $parts[1];
            }
        }

        $periodeStr = trim($selectedBulan . ' ' . $selectedTahun);

        // Query agregasi Pengajar per Bidang
        $dataBidangRaw = DB::table('total_barokah_pengajar as tbp')
            ->join('pengajar as p', 'tbp.id_pengajar', '=', 'p.id_pengajar')
            ->join('lembaga as l', 'p.id_lembaga', '=', 'l.id_lembaga')
            ->where('tbp.bulan', $selectedBulan)
            ->where('tbp.tahun', $selectedTahun)
            ->when($selectedBidang, function($q, $bidang) {
                if ($bidang === 'Bidang DIKDASMEN' || $bidang === 'Bidang DIKJAR') {
                    return $q->whereIn('l.id_bidang', ['Bidang DIKDASMEN', 'Bidang DIKJAR']);
                }
                if ($bidang === 'Bidang DIKDASMEN-M' || $bidang === 'Bidang DIKJAR-M') {
                    return $q->whereIn('l.id_bidang', ['Bidang DIKDASMEN-M', 'Bidang DIKJAR-M']);
                }
                if ($bidang === 'Bidang PU' || $bidang === 'Bidang Kepesantrenan dan PU') {
                    return $q->whereIn('l.id_bidang', ['Bidang PU', 'Bidang Kepesantrenan dan PU']);
                }
                return $q->where('l.id_bidang', $bidang);
            })
            ->select(
                DB::raw('CASE 
                    WHEN TRIM(l.id_bidang) IN ("Bidang DIKJAR", "Bidang DIKDASMEN") THEN "Bidang DIKDASMEN"
                    WHEN TRIM(l.id_bidang) IN ("Bidang DIKJAR-M", "Bidang DIKDASMEN-M") THEN "Bidang DIKDASMEN-M"
                    WHEN TRIM(l.id_bidang) = "Bidang Kepesantrenan dan PU" THEN "Bidang PU"
                    ELSE COALESCE(NULLIF(TRIM(l.id_bidang), ""), "Lainnya") 
                END as nama_bidang'),
                DB::raw('COUNT(DISTINCT p.nik) as jml_guru'),
                DB::raw('SUM(tbp.mengajar) as mengajar'),
                DB::raw('SUM(tbp.dty) as dty'),
                DB::raw('SUM(tbp.jafung) as jafung'),
                DB::raw('SUM(tbp.nominal_kehadiran) as transport'),
                DB::raw('SUM(tbp.nominal_hadir_15) as hadir_15'),
                DB::raw('SUM(tbp.nominal_hadir_10) as hadir_10'),
                DB::raw('SUM(tbp.barokah_piket) as piket'),
                DB::raw('SUM(tbp.tunkel) as tunkel'),
                DB::raw('SUM(tbp.tun_anak) as tunj_anak'),
                DB::raw('SUM(tbp.kehormatan) as kehormatan'),
                DB::raw('SUM(tbp.walkes) as walkes'),
                DB::raw('SUM(tbp.khusus) as tambahan'),
                DB::raw('SUM(tbp.mengajar + tbp.dty + tbp.jafung + tbp.nominal_kehadiran + tbp.nominal_hadir_15 + tbp.nominal_hadir_10 + tbp.barokah_piket + tbp.tunkel + tbp.tun_anak + tbp.kehormatan + tbp.walkes + tbp.khusus) as jumlah'),
                DB::raw('SUM(tbp.potongan) as potongan'),
                DB::raw('SUM(tbp.diterima) as diterima')
            )
            ->groupBy('nama_bidang')
            ->orderBy('nama_bidang', 'asc')
            ->get();

        // Query rincian per Lembaga untuk modal rincian
        $dataLembagaRaw = DB::table('total_barokah_pengajar as tbp')
            ->join('pengajar as p', 'tbp.id_pengajar', '=', 'p.id_pengajar')
            ->join('lembaga as l', 'p.id_lembaga', '=', 'l.id_lembaga')
            ->where('tbp.bulan', $selectedBulan)
            ->where('tbp.tahun', $selectedTahun)
            ->when($selectedBidang, function($q, $bidang) {
                if ($bidang === 'Bidang DIKDASMEN' || $bidang === 'Bidang DIKJAR') {
                    return $q->whereIn('l.id_bidang', ['Bidang DIKDASMEN', 'Bidang DIKJAR']);
                }
                if ($bidang === 'Bidang DIKDASMEN-M' || $bidang === 'Bidang DIKJAR-M') {
                    return $q->whereIn('l.id_bidang', ['Bidang DIKDASMEN-M', 'Bidang DIKJAR-M']);
                }
                if ($bidang === 'Bidang PU' || $bidang === 'Bidang Kepesantrenan dan PU') {
                    return $q->whereIn('l.id_bidang', ['Bidang PU', 'Bidang Kepesantrenan dan PU']);
                }
                return $q->where('l.id_bidang', $bidang);
            })
            ->select(
                'l.id_lembaga',
                'l.nama_lembaga',
                DB::raw('CASE 
                    WHEN TRIM(l.id_bidang) IN ("Bidang DIKJAR", "Bidang DIKDASMEN") THEN "Bidang DIKDASMEN"
                    WHEN TRIM(l.id_bidang) IN ("Bidang DIKJAR-M", "Bidang DIKDASMEN-M") THEN "Bidang DIKDASMEN-M"
                    WHEN TRIM(l.id_bidang) = "Bidang Kepesantrenan dan PU" THEN "Bidang PU"
                    ELSE COALESCE(NULLIF(TRIM(l.id_bidang), ""), "Lainnya") 
                END as nama_bidang'),
                DB::raw('COUNT(DISTINCT p.nik) as jml_guru'),
                DB::raw('SUM(tbp.mengajar) as mengajar'),
                DB::raw('SUM(tbp.dty) as dty'),
                DB::raw('SUM(tbp.jafung) as jafung'),
                DB::raw('SUM(tbp.nominal_kehadiran) as transport'),
                DB::raw('SUM(tbp.nominal_hadir_15) as hadir_15'),
                DB::raw('SUM(tbp.nominal_hadir_10) as hadir_10'),
                DB::raw('SUM(tbp.barokah_piket) as piket'),
                DB::raw('SUM(tbp.tunkel) as tunkel'),
                DB::raw('SUM(tbp.tun_anak) as tunj_anak'),
                DB::raw('SUM(tbp.kehormatan) as kehormatan'),
                DB::raw('SUM(tbp.walkes) as walkes'),
                DB::raw('SUM(tbp.khusus) as tambahan'),
                DB::raw('SUM(tbp.mengajar + tbp.dty + tbp.jafung + tbp.nominal_kehadiran + tbp.nominal_hadir_15 + tbp.nominal_hadir_10 + tbp.barokah_piket + tbp.tunkel + tbp.tun_anak + tbp.kehormatan + tbp.walkes + tbp.khusus) as jumlah'),
                DB::raw('SUM(tbp.potongan) as potongan'),
                DB::raw('SUM(tbp.diterima) as diterima')
            )
            ->groupBy('l.id_lembaga', 'l.nama_lembaga', 'nama_bidang')
            ->orderBy('l.nama_lembaga', 'asc')
            ->get()
            ->groupBy('nama_bidang');

        $dataBidang = [];
        foreach ($dataBidangRaw as $idx => $b) {
            $lembagaListPerBidang = $dataLembagaRaw->get($b->nama_bidang, collect([]))->map(function($lem) {
                return [
                    'nama_lembaga' => $lem->nama_lembaga,
                    'jml_guru'     => (int)$lem->jml_guru,
                    'mengajar'     => (float)$lem->mengajar,
                    'dty'          => (float)$lem->dty,
                    'jafung'       => (float)$lem->jafung,
                    'transport'    => (float)$lem->transport,
                    'hadir_15'     => (float)$lem->hadir_15,
                    'hadir_10'     => (float)$lem->hadir_10,
                    'piket'        => (float)$lem->piket,
                    'tunkel'       => (float)$lem->tunkel,
                    'tunj_anak'    => (float)$lem->tunj_anak,
                    'kehormatan'   => (float)$lem->kehormatan,
                    'walkes'       => (float)$lem->walkes,
                    'tambahan'     => (float)$lem->tambahan,
                    'jumlah'       => (float)$lem->jumlah,
                    'potongan'     => (float)$lem->potongan,
                    'diterima'     => (float)$lem->diterima,
                ];
            })->values()->all();

            $dataBidang[] = [
                'id_bidang'    => $idx + 1,
                'periode'      => $periodeStr,
                'nama_bidang'  => $b->nama_bidang,
                'jml_guru'     => (int)$b->jml_guru,
                'mengajar'     => (float)$b->mengajar,
                'dty'          => (float)$b->dty,
                'jafung'       => (float)$b->jafung,
                'transport'    => (float)$b->transport,
                'hadir_15'     => (float)$b->hadir_15,
                'hadir_10'     => (float)$b->hadir_10,
                'piket'        => (float)$b->piket,
                'tunkel'       => (float)$b->tunkel,
                'tunj_anak'    => (float)$b->tunj_anak,
                'kehormatan'   => (float)$b->kehormatan,
                'walkes'       => (float)$b->walkes,
                'tambahan'     => (float)$b->tambahan,
                'jumlah'       => (float)$b->jumlah,
                'potongan'     => (float)$b->potongan,
                'diterima'     => (float)$b->diterima,
                'lembaga'      => $lembagaListPerBidang
            ];
        }

        // Total akumulatif keseluruhan (Footer)
        $totals = [
            'total_mengajar'   => (float)$dataBidangRaw->sum('mengajar'),
            'total_dty'        => (float)$dataBidangRaw->sum('dty'),
            'total_jafung'     => (float)$dataBidangRaw->sum('jafung'),
            'total_transport'  => (float)$dataBidangRaw->sum('transport'),
            'total_hadir_15'   => (float)$dataBidangRaw->sum('hadir_15'),
            'total_hadir_10'   => (float)$dataBidangRaw->sum('hadir_10'),
            'total_piket'      => (float)$dataBidangRaw->sum('piket'),
            'total_tunkel'     => (float)$dataBidangRaw->sum('tunkel'),
            'total_tunj_anak'  => (float)$dataBidangRaw->sum('tunj_anak'),
            'total_kehormatan' => (float)$dataBidangRaw->sum('kehormatan'),
            'total_walkes'     => (float)$dataBidangRaw->sum('walkes'),
            'total_tambahan'   => (float)$dataBidangRaw->sum('tambahan'),
            'total_jumlah'     => (float)$dataBidangRaw->sum('jumlah'),
            'total_potongan'   => (float)$dataBidangRaw->sum('potongan'),
            'total_diterima'   => (float)$dataBidangRaw->sum('diterima'),
        ];

        return view('laporan.realisasi-pengajar.index', compact(
            'bidangList',
            'dataBidang',
            'totals',
            'selectedBulan',
            'selectedTahun',
            'selectedBidang',
            'periodeStr'
        ));
    }

    /**
     * Export Laporan Realisasi Barokah Pengajar Per Bidang ke format Excel (.xls)
     */
    public function exportExcel(Request $request)
    {
        $latestRecord = DB::table('total_barokah_pengajar')
            ->select('bulan', 'tahun')
            ->whereNotNull('bulan')
            ->where('bulan', '!=', '')
            ->whereNotNull('tahun')
            ->where('tahun', '!=', '')
            ->orderBy('id_total_barokah_pengajar', 'desc')
            ->first();

        $defaultBulan = $latestRecord ? $latestRecord->bulan : 'Oktober';
        $defaultTahun = $latestRecord ? $latestRecord->tahun : date('Y');

        $selectedBulan  = $request->input('bulan', $defaultBulan);
        $selectedTahun  = $request->input('tahun', $defaultTahun);
        $selectedBidang = $request->input('bidang', '');

        if ($request->filled('periode')) {
            $parts = explode(' ', trim($request->input('periode')));
            if (count($parts) >= 2) {
                $selectedBulan = $parts[0];
                $selectedTahun = $parts[1];
            }
        } elseif ($request->filled('filter_periode')) {
            $parts = explode(' ', trim($request->input('filter_periode')));
            if (count($parts) >= 2) {
                $selectedBulan = $parts[0];
                $selectedTahun = $parts[1];
            }
        }

        $periodeStr = trim($selectedBulan . ' ' . $selectedTahun);

        // Query agregasi Pengajar per Bidang
        $dataBidangRaw = DB::table('total_barokah_pengajar as tbp')
            ->join('pengajar as p', 'tbp.id_pengajar', '=', 'p.id_pengajar')
            ->join('lembaga as l', 'p.id_lembaga', '=', 'l.id_lembaga')
            ->where('tbp.bulan', $selectedBulan)
            ->where('tbp.tahun', $selectedTahun)
            ->when($selectedBidang, function($q, $bidang) {
                if ($bidang === 'Bidang DIKDASMEN' || $bidang === 'Bidang DIKJAR') {
                    return $q->whereIn('l.id_bidang', ['Bidang DIKDASMEN', 'Bidang DIKJAR']);
                }
                if ($bidang === 'Bidang DIKDASMEN-M' || $bidang === 'Bidang DIKJAR-M') {
                    return $q->whereIn('l.id_bidang', ['Bidang DIKDASMEN-M', 'Bidang DIKJAR-M']);
                }
                if ($bidang === 'Bidang PU' || $bidang === 'Bidang Kepesantrenan dan PU') {
                    return $q->whereIn('l.id_bidang', ['Bidang PU', 'Bidang Kepesantrenan dan PU']);
                }
                return $q->where('l.id_bidang', $bidang);
            })
            ->select(
                DB::raw('CASE 
                    WHEN TRIM(l.id_bidang) IN ("Bidang DIKJAR", "Bidang DIKDASMEN") THEN "Bidang DIKDASMEN"
                    WHEN TRIM(l.id_bidang) IN ("Bidang DIKJAR-M", "Bidang DIKDASMEN-M") THEN "Bidang DIKDASMEN-M"
                    WHEN TRIM(l.id_bidang) = "Bidang Kepesantrenan dan PU" THEN "Bidang PU"
                    ELSE COALESCE(NULLIF(TRIM(l.id_bidang), ""), "Lainnya") 
                END as nama_bidang'),
                DB::raw('COUNT(DISTINCT p.nik) as jml_guru'),
                DB::raw('SUM(tbp.mengajar) as mengajar'),
                DB::raw('SUM(tbp.dty) as dty'),
                DB::raw('SUM(tbp.jafung) as jafung'),
                DB::raw('SUM(tbp.nominal_kehadiran) as transport'),
                DB::raw('SUM(tbp.nominal_hadir_15) as hadir_15'),
                DB::raw('SUM(tbp.nominal_hadir_10) as hadir_10'),
                DB::raw('SUM(tbp.barokah_piket) as piket'),
                DB::raw('SUM(tbp.tunkel) as tunkel'),
                DB::raw('SUM(tbp.tun_anak) as tunj_anak'),
                DB::raw('SUM(tbp.kehormatan) as kehormatan'),
                DB::raw('SUM(tbp.walkes) as walkes'),
                DB::raw('SUM(tbp.khusus) as tambahan'),
                DB::raw('SUM(tbp.mengajar + tbp.dty + tbp.jafung + tbp.nominal_kehadiran + tbp.nominal_hadir_15 + tbp.nominal_hadir_10 + tbp.barokah_piket + tbp.tunkel + tbp.tun_anak + tbp.kehormatan + tbp.walkes + tbp.khusus) as jumlah'),
                DB::raw('SUM(tbp.potongan) as potongan'),
                DB::raw('SUM(tbp.diterima) as diterima')
            )
            ->groupBy('nama_bidang')
            ->orderBy('nama_bidang', 'asc')
            ->get();

        // Query rincian per Lembaga
        $dataLembagaRaw = DB::table('total_barokah_pengajar as tbp')
            ->join('pengajar as p', 'tbp.id_pengajar', '=', 'p.id_pengajar')
            ->join('lembaga as l', 'p.id_lembaga', '=', 'l.id_lembaga')
            ->where('tbp.bulan', $selectedBulan)
            ->where('tbp.tahun', $selectedTahun)
            ->when($selectedBidang, function($q, $bidang) {
                if ($bidang === 'Bidang DIKDASMEN' || $bidang === 'Bidang DIKJAR') {
                    return $q->whereIn('l.id_bidang', ['Bidang DIKDASMEN', 'Bidang DIKJAR']);
                }
                if ($bidang === 'Bidang DIKDASMEN-M' || $bidang === 'Bidang DIKJAR-M') {
                    return $q->whereIn('l.id_bidang', ['Bidang DIKDASMEN-M', 'Bidang DIKJAR-M']);
                }
                if ($bidang === 'Bidang PU' || $bidang === 'Bidang Kepesantrenan dan PU') {
                    return $q->whereIn('l.id_bidang', ['Bidang PU', 'Bidang Kepesantrenan dan PU']);
                }
                return $q->where('l.id_bidang', $bidang);
            })
            ->select(
                'l.id_lembaga',
                'l.nama_lembaga',
                DB::raw('CASE 
                    WHEN TRIM(l.id_bidang) IN ("Bidang DIKJAR", "Bidang DIKDASMEN") THEN "Bidang DIKDASMEN"
                    WHEN TRIM(l.id_bidang) IN ("Bidang DIKJAR-M", "Bidang DIKDASMEN-M") THEN "Bidang DIKDASMEN-M"
                    WHEN TRIM(l.id_bidang) = "Bidang Kepesantrenan dan PU" THEN "Bidang PU"
                    ELSE COALESCE(NULLIF(TRIM(l.id_bidang), ""), "Lainnya") 
                END as nama_bidang'),
                DB::raw('COUNT(DISTINCT p.nik) as jml_guru'),
                DB::raw('SUM(tbp.mengajar) as mengajar'),
                DB::raw('SUM(tbp.dty) as dty'),
                DB::raw('SUM(tbp.jafung) as jafung'),
                DB::raw('SUM(tbp.nominal_kehadiran) as transport'),
                DB::raw('SUM(tbp.nominal_hadir_15) as hadir_15'),
                DB::raw('SUM(tbp.nominal_hadir_10) as hadir_10'),
                DB::raw('SUM(tbp.barokah_piket) as piket'),
                DB::raw('SUM(tbp.tunkel) as tunkel'),
                DB::raw('SUM(tbp.tun_anak) as tunj_anak'),
                DB::raw('SUM(tbp.kehormatan) as kehormatan'),
                DB::raw('SUM(tbp.walkes) as walkes'),
                DB::raw('SUM(tbp.khusus) as tambahan'),
                DB::raw('SUM(tbp.mengajar + tbp.dty + tbp.jafung + tbp.nominal_kehadiran + tbp.nominal_hadir_15 + tbp.nominal_hadir_10 + tbp.barokah_piket + tbp.tunkel + tbp.tun_anak + tbp.kehormatan + tbp.walkes + tbp.khusus) as jumlah'),
                DB::raw('SUM(tbp.potongan) as potongan'),
                DB::raw('SUM(tbp.diterima) as diterima')
            )
            ->groupBy('l.id_lembaga', 'l.nama_lembaga', 'nama_bidang')
            ->orderBy('nama_bidang', 'asc')
            ->orderBy('l.nama_lembaga', 'asc')
            ->get();

        $totals = [
            'total_mengajar'   => (float)$dataBidangRaw->sum('mengajar'),
            'total_dty'        => (float)$dataBidangRaw->sum('dty'),
            'total_jafung'     => (float)$dataBidangRaw->sum('jafung'),
            'total_transport'  => (float)$dataBidangRaw->sum('transport'),
            'total_hadir_15'   => (float)$dataBidangRaw->sum('hadir_15'),
            'total_hadir_10'   => (float)$dataBidangRaw->sum('hadir_10'),
            'total_piket'      => (float)$dataBidangRaw->sum('piket'),
            'total_tunkel'     => (float)$dataBidangRaw->sum('tunkel'),
            'total_tunj_anak'  => (float)$dataBidangRaw->sum('tunj_anak'),
            'total_kehormatan' => (float)$dataBidangRaw->sum('kehormatan'),
            'total_walkes'     => (float)$dataBidangRaw->sum('walkes'),
            'total_tambahan'   => (float)$dataBidangRaw->sum('tambahan'),
            'total_jumlah'     => (float)$dataBidangRaw->sum('jumlah'),
            'total_potongan'   => (float)$dataBidangRaw->sum('potongan'),
            'total_diterima'   => (float)$dataBidangRaw->sum('diterima'),
        ];

        $cleanPeriode = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $periodeStr);
        $filename = "Laporan_Realisasi_Barokah_Pengajar_Per_Bidang_{$cleanPeriode}.xls";

        return response()->view('laporan.realisasi-pengajar.excel', compact(
            'dataBidangRaw',
            'dataLembagaRaw',
            'totals',
            'periodeStr',
            'selectedBulan',
            'selectedTahun',
            'selectedBidang'
        ))
        ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
        ->header('Cache-Control', 'max-age=0');
    }
}

