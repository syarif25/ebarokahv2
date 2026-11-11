<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanRealisasiBidangController extends Controller
{
    /**
     * Tampilan Laporan Realisasi Barokah Per Bidang (Data Real)
     * Format kolom: No | Periode | Nama Bidang | Tunjab | TMP | Kehadiran | Tunkel | Tunj Anak | Kehormatan | TBK | Jumlah | Potongan | Diterima | Aksi
     */
    public function index(Request $request)
    {
        // Ambil daftar Bidang dari master lembaga (otomatis ubah DIKJAR -> DIKDASMEN dan DIKJAR-M -> DIKDASMEN-M)
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

        // Cari periode default dari total_barokah terbaru
        $latestRecord = DB::table('total_barokah')
            ->select('bulan', 'tahun')
            ->whereNotNull('bulan')
            ->where('bulan', '!=', '')
            ->whereNotNull('tahun')
            ->where('tahun', '!=', '')
            ->orderBy('id_total_barokah', 'desc')
            ->first();

        $defaultBulan = $latestRecord ? $latestRecord->bulan : 'Oktober';
        $defaultTahun = $latestRecord ? $latestRecord->tahun : date('Y');

        $selectedBulan  = $request->input('bulan', $defaultBulan);
        $selectedTahun  = $request->input('tahun', $defaultTahun);
        $selectedBidang = $request->input('bidang', '');

        // Parsing jika filter_periode (misal: "Oktober 2026") dikirimkan
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

        // Query real agregasi per Bidang (DIKJAR -> DIKDASMEN & DIKJAR-M -> DIKDASMEN-M)
        $dataBidangRaw = DB::table('total_barokah as tb')
            ->join('penempatan as p', 'tb.id_penempatan', '=', 'p.id_penempatan')
            ->join('lembaga as l', 'p.id_lembaga', '=', 'l.id_lembaga')
            ->where('tb.bulan', $selectedBulan)
            ->where('tb.tahun', $selectedTahun)
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
                DB::raw('COUNT(DISTINCT p.nik) as jml_umana'),
                DB::raw('SUM(tb.tunjab) as tunjab'),
                DB::raw('SUM(tb.tmp) as tmp'),
                DB::raw('SUM(tb.nominal_kehadiran) as kehadiran'),
                DB::raw('SUM(tb.tunkel) as tunkel'),
                DB::raw('SUM(tb.tunj_anak) as tunj_anak'),
                DB::raw('SUM(tb.kehormatan) as kehormatan'),
                DB::raw('SUM(tb.tbk) as tbk'),
                DB::raw('SUM(tb.tunjab + tb.tmp + tb.nominal_kehadiran + tb.tunkel + tb.tunj_anak + tb.kehormatan + tb.tbk) as jumlah'),
                DB::raw('SUM(tb.potongan) as potongan'),
                DB::raw('SUM(tb.diterima) as diterima')
            )
            ->groupBy('nama_bidang')
            ->orderBy('nama_bidang', 'asc')
            ->get();

        // Query real rincian per Lembaga untuk modal rincian
        $dataLembagaRaw = DB::table('total_barokah as tb')
            ->join('penempatan as p', 'tb.id_penempatan', '=', 'p.id_penempatan')
            ->join('lembaga as l', 'p.id_lembaga', '=', 'l.id_lembaga')
            ->where('tb.bulan', $selectedBulan)
            ->where('tb.tahun', $selectedTahun)
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
                DB::raw('COUNT(DISTINCT p.nik) as jml_umana'),
                DB::raw('SUM(tb.tunjab) as tunjab'),
                DB::raw('SUM(tb.tmp) as tmp'),
                DB::raw('SUM(tb.nominal_kehadiran) as kehadiran'),
                DB::raw('SUM(tb.tunkel) as tunkel'),
                DB::raw('SUM(tb.tunj_anak) as tunj_anak'),
                DB::raw('SUM(tb.kehormatan) as kehormatan'),
                DB::raw('SUM(tb.tbk) as tbk'),
                DB::raw('SUM(tb.tunjab + tb.tmp + tb.nominal_kehadiran + tb.tunkel + tb.tunj_anak + tb.kehormatan + tb.tbk) as jumlah'),
                DB::raw('SUM(tb.potongan) as potongan'),
                DB::raw('SUM(tb.diterima) as diterima')
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
                    'jml_umana'    => (int)$lem->jml_umana,
                    'tunjab'       => (float)$lem->tunjab,
                    'tmp'          => (float)$lem->tmp,
                    'kehadiran'    => (float)$lem->kehadiran,
                    'tunkel'       => (float)$lem->tunkel,
                    'tunj_anak'    => (float)$lem->tunj_anak,
                    'kehormatan'   => (float)$lem->kehormatan,
                    'tbk'          => (float)$lem->tbk,
                    'jumlah'       => (float)$lem->jumlah,
                    'potongan'     => (float)$lem->potongan,
                    'diterima'     => (float)$lem->diterima,
                ];
            })->values()->all();

            $dataBidang[] = [
                'id_bidang'   => $idx + 1,
                'periode'     => $periodeStr,
                'nama_bidang' => $b->nama_bidang,
                'jml_umana'   => (int)$b->jml_umana,
                'tunjab'      => (float)$b->tunjab,
                'tmp'         => (float)$b->tmp,
                'kehadiran'   => (float)$b->kehadiran,
                'tunkel'      => (float)$b->tunkel,
                'tunj_anak'   => (float)$b->tunj_anak,
                'kehormatan'  => (float)$b->kehormatan,
                'tbk'         => (float)$b->tbk,
                'jumlah'      => (float)$b->jumlah,
                'potongan'    => (float)$b->potongan,
                'diterima'    => (float)$b->diterima,
                'lembaga'     => $lembagaListPerBidang
            ];
        }

        // Total akumulatif keseluruhan (Footer)
        $totals = [
            'total_tunjab'      => (float)$dataBidangRaw->sum('tunjab'),
            'total_tmp'         => (float)$dataBidangRaw->sum('tmp'),
            'total_kehadiran'   => (float)$dataBidangRaw->sum('kehadiran'),
            'total_tunkel'      => (float)$dataBidangRaw->sum('tunkel'),
            'total_tunj_anak'   => (float)$dataBidangRaw->sum('tunj_anak'),
            'total_kehormatan'  => (float)$dataBidangRaw->sum('kehormatan'),
            'total_tbk'         => (float)$dataBidangRaw->sum('tbk'),
            'total_jumlah'      => (float)$dataBidangRaw->sum('jumlah'),
            'total_potongan'    => (float)$dataBidangRaw->sum('potongan'),
            'total_diterima'    => (float)$dataBidangRaw->sum('diterima'),
        ];

        return view('laporan.realisasi-bidang.index', compact(
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
     * Export Laporan Realisasi Barokah Per Bidang ke format Excel (.xls)
     */
    public function exportExcel(Request $request)
    {
        $latestRecord = DB::table('total_barokah')
            ->select('bulan', 'tahun')
            ->whereNotNull('bulan')
            ->where('bulan', '!=', '')
            ->whereNotNull('tahun')
            ->where('tahun', '!=', '')
            ->orderBy('id_total_barokah', 'desc')
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

        // Query agregasi per Bidang
        $dataBidangRaw = DB::table('total_barokah as tb')
            ->join('penempatan as p', 'tb.id_penempatan', '=', 'p.id_penempatan')
            ->join('lembaga as l', 'p.id_lembaga', '=', 'l.id_lembaga')
            ->where('tb.bulan', $selectedBulan)
            ->where('tb.tahun', $selectedTahun)
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
                DB::raw('COUNT(DISTINCT p.nik) as jml_umana'),
                DB::raw('SUM(tb.tunjab) as tunjab'),
                DB::raw('SUM(tb.tmp) as tmp'),
                DB::raw('SUM(tb.nominal_kehadiran) as kehadiran'),
                DB::raw('SUM(tb.tunkel) as tunkel'),
                DB::raw('SUM(tb.tunj_anak) as tunj_anak'),
                DB::raw('SUM(tb.kehormatan) as kehormatan'),
                DB::raw('SUM(tb.tbk) as tbk'),
                DB::raw('SUM(tb.tunjab + tb.tmp + tb.nominal_kehadiran + tb.tunkel + tb.tunj_anak + tb.kehormatan + tb.tbk) as jumlah'),
                DB::raw('SUM(tb.potongan) as potongan'),
                DB::raw('SUM(tb.diterima) as diterima')
            )
            ->groupBy('nama_bidang')
            ->orderBy('nama_bidang', 'asc')
            ->get();

        // Query rincian per Lembaga
        $dataLembagaRaw = DB::table('total_barokah as tb')
            ->join('penempatan as p', 'tb.id_penempatan', '=', 'p.id_penempatan')
            ->join('lembaga as l', 'p.id_lembaga', '=', 'l.id_lembaga')
            ->where('tb.bulan', $selectedBulan)
            ->where('tb.tahun', $selectedTahun)
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
                DB::raw('COUNT(DISTINCT p.nik) as jml_umana'),
                DB::raw('SUM(tb.tunjab) as tunjab'),
                DB::raw('SUM(tb.tmp) as tmp'),
                DB::raw('SUM(tb.nominal_kehadiran) as kehadiran'),
                DB::raw('SUM(tb.tunkel) as tunkel'),
                DB::raw('SUM(tb.tunj_anak) as tunj_anak'),
                DB::raw('SUM(tb.kehormatan) as kehormatan'),
                DB::raw('SUM(tb.tbk) as tbk'),
                DB::raw('SUM(tb.tunjab + tb.tmp + tb.nominal_kehadiran + tb.tunkel + tb.tunj_anak + tb.kehormatan + tb.tbk) as jumlah'),
                DB::raw('SUM(tb.potongan) as potongan'),
                DB::raw('SUM(tb.diterima) as diterima')
            )
            ->groupBy('l.id_lembaga', 'l.nama_lembaga', 'nama_bidang')
            ->orderBy('nama_bidang', 'asc')
            ->orderBy('l.nama_lembaga', 'asc')
            ->get();

        $totals = [
            'total_tunjab'      => (float)$dataBidangRaw->sum('tunjab'),
            'total_tmp'         => (float)$dataBidangRaw->sum('tmp'),
            'total_kehadiran'   => (float)$dataBidangRaw->sum('kehadiran'),
            'total_tunkel'      => (float)$dataBidangRaw->sum('tunkel'),
            'total_tunj_anak'   => (float)$dataBidangRaw->sum('tunj_anak'),
            'total_kehormatan'  => (float)$dataBidangRaw->sum('kehormatan'),
            'total_tbk'         => (float)$dataBidangRaw->sum('tbk'),
            'total_jumlah'      => (float)$dataBidangRaw->sum('jumlah'),
            'total_potongan'    => (float)$dataBidangRaw->sum('potongan'),
            'total_diterima'    => (float)$dataBidangRaw->sum('diterima'),
        ];

        $cleanPeriode = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $periodeStr);
        $filename = "Laporan_Realisasi_Barokah_Per_Bidang_{$cleanPeriode}.xls";

        return response()->view('laporan.realisasi-bidang.excel', compact(
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
