<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapKehadiranPengajarController extends Controller
{
    /**
     * Halaman Utama Rekap Kehadiran Pengajar
     */
    public function index()
    {
        $lembagaList = DB::table('lembaga')
            ->where('tenaga_pengajar', 'Ya')
            ->orderBy('nama_lembaga', 'asc')
            ->get(['id_lembaga', 'nama_lembaga']);

        return view('laporan.rekap-kehadiran-pengajar.index', compact('lembagaList'));
    }

    /**
     * Helper menghasilkan rentang bulan & tahun
     */
    private function generateMonthYearRange($startMonth, $startYear, $endMonth, $endYear)
    {
        $months = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
        ];

        $monthsRev = array_flip($months);

        $startM = $months[$startMonth] ?? 1;
        $endM = $months[$endMonth] ?? 12;

        $result = [];
        $currentM = $startM;
        $currentY = (int)$startYear;
        $endY = (int)$endYear;

        while ($currentY < $endY || ($currentY == $endY && $currentM <= $endM)) {
            $result[] = $monthsRev[$currentM] . '-' . $currentY;
            $currentM++;
            if ($currentM > 12) {
                $currentM = 1;
                $currentY++;
            }
        }

        return $result;
    }

    /**
     * Ambil data pivot rekap kehadiran pengajar
     */
    public function getData(Request $request)
    {
        $idLembaga = $request->input('id_lembaga');
        $startMonth = $request->input('start_month');
        $startYear = $request->input('start_year');
        $endMonth = $request->input('end_month');
        $endYear = $request->input('end_year');

        if (empty($idLembaga) || empty($startMonth) || empty($startYear) || empty($endMonth) || empty($endYear)) {
            return response()->json(['status' => 'error', 'message' => 'Lengkapi semua filter terlebih dahulu']);
        }

        $months = [
            'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
            'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
            'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
        ];

        if ($startYear > $endYear || ($startYear == $endYear && ($months[$startMonth] ?? 0) > ($months[$endMonth] ?? 0))) {
            return response()->json(['status' => 'error', 'message' => 'Periode awal tidak boleh lebih besar dari periode akhir']);
        }

        $monthYearRange = $this->generateMonthYearRange($startMonth, $startYear, $endMonth, $endYear);

        // Ambil data mentah menggunakan Query Builder
        $query = DB::table('total_barokah_pengajar as tbp')
            ->join('pengajar as p', 'p.id_pengajar', '=', 'tbp.id_pengajar')
            ->join('umana as u', 'u.nik', '=', 'p.nik')
            ->join('lembaga as l', 'l.id_lembaga', '=', 'p.id_lembaga')
            ->join('kehadiran_lembaga as kl', 'kl.id_kehadiran_lembaga', '=', 'tbp.id_kehadiran_lembaga')
            ->where('kl.kategori', 'Pengajar')
            ->where('kl.id_lembaga', $idLembaga);

        $query->where(function ($q) use ($monthYearRange) {
            foreach ($monthYearRange as $bt) {
                $parts = explode('-', $bt);
                if (count($parts) === 2) {
                    $q->orWhere(function ($subQ) use ($parts) {
                        $subQ->where('tbp.bulan', $parts[0])
                             ->where(function ($yearQ) use ($parts) {
                                 $yearQ->where('tbp.tahun', $parts[1])
                                       ->orWhere('tbp.tahun', 'like', '%' . $parts[1]);
                             });
                    });
                }
            }
        });

        $rawData = $query->select(
            'u.nama_lengkap',
            'u.gelar_depan',
            'u.gelar_belakang',
            'l.nama_lembaga',
            'p.kategori',
            'tbp.bulan',
            'tbp.tahun',
            'tbp.id_pengajar',
            'tbp.jumlah_hadir',
            'tbp.jumlah_hadir_15',
            'tbp.jumlah_hadir_10',
            'tbp.jumlah_hadir_piket'
        )
        ->orderBy('u.nama_lengkap', 'asc')
        ->get();

        // Format kolom bulan: e.g. "Februari-26"
        $columns = [];
        foreach ($monthYearRange as $bt) {
            $parts = explode('-', $bt);
            $thnShort = strlen($parts[1]) == 4 ? substr($parts[1], 2, 2) : $parts[1];
            $columns[] = $parts[0] . '-' . $thnShort;
        }

        // Proses grouping pivot
        $pivot = [];
        foreach ($rawData as $row) {
            $key = $row->id_pengajar;
            $tahunClean = trim($row->tahun);
            if (str_starts_with(strtolower($tahunClean), 'tahun ')) {
                $tahunClean = trim(substr($tahunClean, 6));
            }
            $thnShort = strlen($tahunClean) == 4 ? substr($tahunClean, 2, 2) : $tahunClean;
            if (strpos($tahunClean, '/') !== false) {
                $p = explode('/', $tahunClean);
                $thnShort = substr($p[0], 2, 2) . '/' . substr($p[1], 2, 2);
            }
            $bulanKey = $row->bulan . '-' . $thnShort;

            if (!isset($pivot[$key])) {
                $gelarDepan = $row->gelar_depan ? $row->gelar_depan . ' ' : '';
                $gelarBelakang = $row->gelar_belakang ? ', ' . $row->gelar_belakang : '';

                $pivot[$key] = [
                    'nama' => trim($gelarDepan . $row->nama_lengkap . $gelarBelakang),
                    'lembaga' => $row->nama_lembaga,
                    'bulan_data' => []
                ];
            }

            $totalHadir = (int)$row->jumlah_hadir + (int)$row->jumlah_hadir_15 + (int)$row->jumlah_hadir_10 + (int)$row->jumlah_hadir_piket;

            if (!isset($pivot[$key]['bulan_data'][$bulanKey])) {
                $pivot[$key]['bulan_data'][$bulanKey] = 0;
            }
            $pivot[$key]['bulan_data'][$bulanKey] += $totalHadir;
        }

        return response()->json([
            'status' => 'success',
            'columns' => $columns,
            'data' => array_values($pivot)
        ]);
    }
}
