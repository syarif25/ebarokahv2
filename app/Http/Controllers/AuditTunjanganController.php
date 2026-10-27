<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Umana;

class AuditTunjanganController extends Controller
{
    /**
     * Halaman Utama Audit Tunjangan Ganda
     */
    public function index(Request $request)
    {
        $scope = $request->get('scope', 'berlaku');
        $stats = $this->getStatistics($scope);

        return view('validasi.audit-tunjangan', compact('stats', 'scope'));
    }

    /**
     * AJAX Endpoint untuk DataTables / Data List
     */
    public function data(Request $request)
    {
        $scope = $request->get('scope', 'berlaku');
        $kategoriGanda = $request->get('kategori_ganda', 'all');

        $data = $this->getAuditData($scope, $kategoriGanda);

        return response()->json([
            'success' => true,
            'data' => $data,
            'stats' => $this->getStatistics($scope)
        ]);
    }

    /**
     * AJAX Detail Penugasan Umana untuk Modal Validasi Silang
     */
    public function detail($nik, Request $request)
    {
        $scope = $request->get('scope', 'berlaku');
        $umana = DB::table('umana')->where('nik', $nik)->first();
        if (!$umana) {
            return response()->json(['status' => false, 'message' => 'Data Umana tidak ditemukan.'], 404);
        }

        $dateFilterP = $scope === 'berlaku' 
            ? "AND (p.tgl_selesai IS NULL OR p.tgl_selesai = '0000-00-00' OR p.tgl_selesai >= CURDATE())" 
            : "";
        $dateFilterPeng = $scope === 'berlaku' 
            ? "AND (peng.tgl_selesai IS NULL OR peng.tgl_selesai = '0000-00-00' OR peng.tgl_selesai >= CURDATE())" 
            : "";

        // 1. Data Penempatan Struktural
        $struktural = DB::table('penempatan as p')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'p.id_lembaga')
            ->leftJoin('ketentuan_barokah as kb', 'kb.id_ketentuan', '=', 'p.id_ketentuan')
            ->where('p.nik', $nik)
            ->where('p.status', 'Aktif')
            ->when($scope === 'berlaku', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('p.tgl_selesai')
                        ->orWhere('p.tgl_selesai', '0000-00-00')
                        ->orWhere('p.tgl_selesai', '>=', DB::raw('CURDATE()'));
                });
            })
            ->select(
                'p.id_penempatan',
                'l.nama_lembaga',
                'kb.nama_jabatan',
                'p.jabatan_lembaga',
                'p.tgl_mulai',
                'p.tgl_selesai',
                'p.tunj_kel',
                'p.tunj_anak',
                'p.kehormatan',
                'p.status'
            )
            ->get();

        // 2. Data Penugasan Pengajar
        $pengajar = DB::table('pengajar as peng')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'peng.id_lembaga')
            ->where('peng.nik', $nik)
            ->where('peng.status', 'Aktif')
            ->when($scope === 'berlaku', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('peng.tgl_selesai')
                        ->orWhere('peng.tgl_selesai', '0000-00-00')
                        ->orWhere('peng.tgl_selesai', '>=', DB::raw('CURDATE()'));
                });
            })
            ->select(
                'peng.id_pengajar',
                'l.nama_lembaga',
                'peng.kategori as jenis_pengajar',
                'peng.jumlah_sks',
                'peng.tgl_mulai',
                'peng.tgl_selesai',
                'peng.tunj_kel',
                'peng.tunj_anak',
                'peng.kehormatan',
                'peng.status'
            )
            ->get();

        $namaFull = trim(($umana->gelar_depan ? $umana->gelar_depan . ' ' : '') . $umana->nama_lengkap . ($umana->gelar_belakang ? ', ' . $umana->gelar_belakang : ''));

        return response()->json([
            'status' => true,
            'umana' => [
                'nik' => $umana->nik,
                'nama_lengkap' => $namaFull,
                'status_sertifikasi' => $umana->status_sertifikasi ?? 'Belum',
                'nomor_hp' => $umana->nomor_hp ?? '-',
                'alamat_domisili' => $umana->alamat_domisili ?? '-',
            ],
            'struktural' => $struktural,
            'pengajar' => $pengajar
        ]);
    }

    /**
     * Export Hasil Audit ke format CSV / Excel
     */
    public function exportExcel(Request $request)
    {
        $scope = $request->get('scope', 'berlaku');
        $kategoriGanda = $request->get('kategori_ganda', 'all');
        $data = $this->getAuditData($scope, $kategoriGanda);

        $filename = "Audit_Tunjangan_Ganda_" . date('Y-m-d_His') . ".csv";
        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'No',
            'NIK',
            'Nama Lengkap',
            'Sertifikasi',
            'Indikasi Ganda',
            'Jml Tunkel Ya',
            'Jml Tunj Anak Ya',
            'Jml Kehormatan Ya',
            'Rincian SK & Penempatan'
        ];

        $callback = function() use ($data, $columns) {
            $file = fopen('php://output', 'w');
            // Menulis BOM agar Excel mengenali UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns, ';');

            foreach ($data as $idx => $row) {
                $rincianArr = [];
                foreach ($row['details'] as $d) {
                    $rincianArr[] = "[{$d['tipe']}] {$d['lembaga']} ({$d['posisi']}) -> Tunkel: {$d['tunkel']} | Anak: {$d['tunj_anak']} | Kehormatan: {$d['kehormatan']}";
                }
                $rincianStr = implode(' | ', $rincianArr);

                fputcsv($file, [
                    $idx + 1,
                    "'" . $row['nik'], // Petik agar NIK tidak diformat angka scientific di Excel
                    $row['nama_lengkap'],
                    $row['status_sertifikasi'],
                    implode(', ', $row['pelanggaran']),
                    $row['count_tunkel'],
                    $row['count_anak'],
                    $row['count_kehormatan'],
                    $rincianStr
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Hitung Statistik Ringkasan Duplikasi
     */
    private function getStatistics($scope)
    {
        $dateFilterP = $scope === 'berlaku' 
            ? "AND (p.tgl_selesai IS NULL OR p.tgl_selesai = '0000-00-00' OR p.tgl_selesai >= CURDATE())" 
            : "";
        $dateFilterPeng = $scope === 'berlaku' 
            ? "AND (peng.tgl_selesai IS NULL OR peng.tgl_selesai = '0000-00-00' OR peng.tgl_selesai >= CURDATE())" 
            : "";

        $sql = "
            SELECT 
                nik,
                SUM(CASE WHEN tunj_kel = 'Ya' THEN 1 ELSE 0 END) as count_tunkel,
                SUM(CASE WHEN tunj_anak = 'Ya' THEN 1 ELSE 0 END) as count_anak,
                SUM(CASE WHEN kehormatan = 'Ya' THEN 1 ELSE 0 END) as count_kehormatan
            FROM (
                SELECT p.nik, p.tunj_kel, p.tunj_anak, p.kehormatan
                FROM penempatan p
                WHERE p.status = 'Aktif' $dateFilterP
                UNION ALL
                SELECT peng.nik, peng.tunj_kel, peng.tunj_anak, peng.kehormatan
                FROM pengajar peng
                WHERE peng.status = 'Aktif' $dateFilterPeng
            ) as u_tunj
            GROUP BY nik
            HAVING count_tunkel > 1 OR count_anak > 1 OR count_kehormatan > 1
        ";

        $records = DB::select($sql);

        $totalGanda = count($records);
        $totalTunkel = 0;
        $totalAnak = 0;
        $totalKehormatan = 0;

        foreach ($records as $r) {
            if ($r->count_tunkel > 1) $totalTunkel++;
            if ($r->count_anak > 1) $totalAnak++;
            if ($r->count_kehormatan > 1) $totalKehormatan++;
        }

        return [
            'total_ganda' => $totalGanda,
            'total_tunkel' => $totalTunkel,
            'total_anak' => $totalAnak,
            'total_kehormatan' => $totalKehormatan
        ];
    }

    /**
     * Ambil Data Detail Umana yang Terindikasi Memiliki Tunjangan Ganda
     */
    private function getAuditData($scope, $kategoriGanda = 'all')
    {
        $dateFilterP = $scope === 'berlaku' 
            ? "AND (p.tgl_selesai IS NULL OR p.tgl_selesai = '0000-00-00' OR p.tgl_selesai >= CURDATE())" 
            : "";
        $dateFilterPeng = $scope === 'berlaku' 
            ? "AND (peng.tgl_selesai IS NULL OR peng.tgl_selesai = '0000-00-00' OR peng.tgl_selesai >= CURDATE())" 
            : "";

        // Filter HAVING sesuai pilihan user
        $havingCondition = "count_tunkel > 1 OR count_anak > 1 OR count_kehormatan > 1";
        if ($kategoriGanda === 'tunkel') {
            $havingCondition = "count_tunkel > 1";
        } elseif ($kategoriGanda === 'anak') {
            $havingCondition = "count_anak > 1";
        } elseif ($kategoriGanda === 'kehormatan') {
            $havingCondition = "count_kehormatan > 1";
        }

        $sql = "
            SELECT 
                nik,
                SUM(CASE WHEN tunj_kel = 'Ya' THEN 1 ELSE 0 END) as count_tunkel,
                SUM(CASE WHEN tunj_anak = 'Ya' THEN 1 ELSE 0 END) as count_anak,
                SUM(CASE WHEN kehormatan = 'Ya' THEN 1 ELSE 0 END) as count_kehormatan
            FROM (
                SELECT p.nik, p.tunj_kel, p.tunj_anak, p.kehormatan
                FROM penempatan p
                WHERE p.status = 'Aktif' $dateFilterP
                UNION ALL
                SELECT peng.nik, peng.tunj_kel, peng.tunj_anak, peng.kehormatan
                FROM pengajar peng
                WHERE peng.status = 'Aktif' $dateFilterPeng
            ) as u_tunj
            GROUP BY nik
            HAVING $havingCondition
            ORDER BY count_tunkel DESC, count_anak DESC, count_kehormatan DESC
        ";

        $records = DB::select($sql);
        if (empty($records)) {
            return [];
        }

        $nikList = array_map(function($r) { return $r->nik; }, $records);

        // Ambil profil Umana
        $umanaMap = DB::table('umana')
            ->whereIn('nik', $nikList)
            ->select('nik', 'nama_lengkap', 'gelar_depan', 'gelar_belakang', 'status_sertifikasi', 'nomor_hp')
            ->get()
            ->keyBy('nik');

        // Ambil semua SK penempatan struktural Umana tersebut
        $penempatanRows = DB::table('penempatan as p')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'p.id_lembaga')
            ->leftJoin('ketentuan_barokah as kb', 'kb.id_ketentuan', '=', 'p.id_ketentuan')
            ->whereIn('p.nik', $nikList)
            ->where('p.status', 'Aktif')
            ->when($scope === 'berlaku', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('p.tgl_selesai')
                        ->orWhere('p.tgl_selesai', '0000-00-00')
                        ->orWhere('p.tgl_selesai', '>=', DB::raw('CURDATE()'));
                });
            })
            ->select(
                'p.nik',
                'p.id_penempatan',
                'l.nama_lembaga',
                'kb.nama_jabatan',
                'p.jabatan_lembaga',
                'p.tunj_kel',
                'p.tunj_anak',
                'p.kehormatan'
            )
            ->get()
            ->groupBy('nik');

        // Ambil semua SK penugasan pengajar Umana tersebut
        $pengajarRows = DB::table('pengajar as peng')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'peng.id_lembaga')
            ->whereIn('peng.nik', $nikList)
            ->where('peng.status', 'Aktif')
            ->when($scope === 'berlaku', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('peng.tgl_selesai')
                        ->orWhere('peng.tgl_selesai', '0000-00-00')
                        ->orWhere('peng.tgl_selesai', '>=', DB::raw('CURDATE()'));
                });
            })
            ->select(
                'peng.nik',
                'peng.id_pengajar',
                'l.nama_lembaga',
                'peng.kategori as jenis_pengajar',
                'peng.tunj_kel',
                'peng.tunj_anak',
                'peng.kehormatan'
            )
            ->get()
            ->groupBy('nik');

        $result = [];
        foreach ($records as $r) {
            $nik = $r->nik;
            $u = $umanaMap->get($nik);
            if (!$u) continue;

            $namaFull = trim(($u->gelar_depan ? $u->gelar_depan . ' ' : '') . $u->nama_lengkap . ($u->gelar_belakang ? ', ' . $u->gelar_belakang : ''));
            // Title case
            $namaFormatted = ucwords(strtolower($namaFull));

            $pelanggaran = [];
            if ($r->count_tunkel > 1) {
                $pelanggaran[] = "Double Tunkel ({$r->count_tunkel} SK)";
            }
            if ($r->count_anak > 1) {
                $pelanggaran[] = "Double Tunj. Anak ({$r->count_anak} SK)";
            }
            if ($r->count_kehormatan > 1) {
                $pelanggaran[] = "Double Kehormatan ({$r->count_kehormatan} SK)";
            }

            // Gabungkan rincian penugasan
            $details = [];
            if (isset($penempatanRows[$nik])) {
                foreach ($penempatanRows[$nik] as $p) {
                    $posisi = $p->nama_jabatan ?: ($p->jabatan_lembaga ?: 'Struktural');
                    $details[] = [
                        'tipe' => 'Struktural',
                        'lembaga' => $p->nama_lembaga ?: '-',
                        'posisi' => $posisi,
                        'tunkel' => $p->tunj_kel,
                        'tunj_anak' => $p->tunj_anak,
                        'kehormatan' => $p->kehormatan,
                        'id' => $p->id_penempatan
                    ];
                }
            }

            if (isset($pengajarRows[$nik])) {
                foreach ($pengajarRows[$nik] as $peng) {
                    $posisi = $peng->jenis_pengajar ?: 'Pengajar';
                    $details[] = [
                        'tipe' => 'Pengajar',
                        'lembaga' => $peng->nama_lembaga ?: '-',
                        'posisi' => $posisi,
                        'tunkel' => $peng->tunj_kel,
                        'tunj_anak' => $peng->tunj_anak,
                        'kehormatan' => $peng->kehormatan,
                        'id' => $peng->id_pengajar
                    ];
                }
            }

            $result[] = [
                'nik' => $nik,
                'nama_lengkap' => $namaFormatted,
                'status_sertifikasi' => $u->status_sertifikasi ?? 'Belum',
                'nomor_hp' => $u->nomor_hp ?? '-',
                'count_tunkel' => (int)$r->count_tunkel,
                'count_anak' => (int)$r->count_anak,
                'count_kehormatan' => (int)$r->count_kehormatan,
                'pelanggaran' => $pelanggaran,
                'details' => $details
            ];
        }

        return $result;
    }
}
