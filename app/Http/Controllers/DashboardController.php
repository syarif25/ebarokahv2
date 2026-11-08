<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Penempatan;
use App\Models\Pengajar;
use App\Models\KehadiranLembaga;
use App\Models\PotonganUmana;
use App\Models\PotonganPengajar;
use App\Models\Lembaga;
use App\Models\Umana;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdminLembaga = $user && $user->jabatan === 'AdminLembaga' && $user->lembaga;

        if ($isAdminLembaga) {
            $lembagaId = $user->lembaga;
            $lembaga = Lembaga::find($lembagaId);

            // 1. Data Umana Struktural & Pengajar Aktif
            $totalStruktural = Penempatan::where('id_lembaga', $lembagaId)
                ->where('status', 'aktif')
                ->count();

            $totalPengajar = Pengajar::where('id_lembaga', $lembagaId)
                ->where('status', 'aktif')
                ->count();

            // 2. Status Kehadiran Lembaga Terbaru
            $kehadiranStrukturalTerbaru = KehadiranLembaga::where('id_lembaga', $lembagaId)
                ->where('kategori', 'Struktural')
                ->orderBy('id_kehadiran_lembaga', 'desc')
                ->first();

            $kehadiranPengajarTerbaru = KehadiranLembaga::where('id_lembaga', $lembagaId)
                ->where('kategori', 'Pengajar')
                ->orderBy('id_kehadiran_lembaga', 'desc')
                ->first();

            // 3. Potongan Lembaga
            $totalPotonganUmana = PotonganUmana::whereHas('penempatan', function ($q) use ($lembagaId) {
                $q->where('id_lembaga', $lembagaId);
            })->sum('nominal_potongan');

            $totalPotonganPengajar = PotonganPengajar::whereHas('pengajar', function ($q) use ($lembagaId) {
                $q->where('id_lembaga', $lembagaId);
            })->sum('nominal_potongan');

            $totalNominalPotongan = $totalPotonganUmana + $totalPotonganPengajar;

            // 4. Daftar Potongan Terbaru (5 Terakhir)
            $recentPotonganUmana = PotonganUmana::with(['penempatan.umana', 'potongan'])
                ->whereHas('penempatan', function ($q) use ($lembagaId) {
                    $q->where('id_lembaga', $lembagaId);
                })
                ->orderBy('id_potongan_umana', 'desc')
                ->take(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'nama' => $item->penempatan && $item->penempatan->umana ? $item->penempatan->umana->nama_lengkap : '-',
                        'kategori' => 'Struktural',
                        'jenis' => $item->potongan ? $item->potongan->nama_potongan : $item->jenis_potongan,
                        'nominal' => $item->nominal_potongan,
                        'periode' => $item->min_periode_potongan ? date('M Y', strtotime($item->min_periode_potongan)) : '-'
                    ];
                });

            $recentPotonganPengajar = PotonganPengajar::with(['pengajar.umana', 'potongan'])
                ->whereHas('pengajar', function ($q) use ($lembagaId) {
                    $q->where('id_lembaga', $lembagaId);
                })
                ->orderBy('id_potongan_pengajar', 'desc')
                ->take(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'nama' => $item->pengajar && $item->pengajar->umana ? $item->pengajar->umana->nama_lengkap : '-',
                        'kategori' => 'Pengajar',
                        'jenis' => $item->potongan ? $item->potongan->nama_potongan : $item->jenis_potongan,
                        'nominal' => $item->nominal_potongan,
                        'periode' => $item->min_periode_potongan ? date('M Y', strtotime($item->min_periode_potongan)) : '-'
                    ];
                });

            $recentPotongan = collect($recentPotonganUmana)
                ->concat($recentPotonganPengajar)
                ->take(5);

            return view('dashboard.admin_lembaga', compact(
                'lembaga',
                'totalStruktural',
                'totalPengajar',
                'kehadiranStrukturalTerbaru',
                'kehadiranPengajarTerbaru',
                'totalNominalPotongan',
                'recentPotongan'
            ));
        }

        // ========================================================
        // Default Dashboard (SuperAdmin, Evaluasi, SDM, Kasir)
        // ========================================================
        $totalUmana = Umana::count();
        $totalLembaga = Lembaga::count();
        $totalPenempatan = Penempatan::where('status', 'aktif')->count();
        $totalPengajar = Pengajar::where('status', 'aktif')->count();

        // Pemetaan Bulan Bahasa Indonesia
        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // 1. Tentukan Periode "Bulan Ini" (Default mengikuti siklus pencairan: kalender berjalan memproses bulan sebelumnya)
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $bulanIni = $request->bulan;
            $tahunIni = $request->tahun;
        } else {
            // SIKLUS BAROKAH:
            // Barokah yang dicairkan/direkap pada bulan kalender berjalan adalah data kehadiran bulan sebelumnya.
            // Contoh: Kalender berjalan bulan November memproses Barokah bulan Oktober.
            $currentMonthNum = (int)date('n');
            $currentYear     = (int)date('Y');

            if ($currentMonthNum === 1) {
                $cycleMonthNum = 12;
                $cycleYear     = $currentYear - 1;
            } else {
                $cycleMonthNum = $currentMonthNum - 1;
                $cycleYear     = $currentYear;
            }

            $bulanIni = $bulanIndo[$cycleMonthNum] ?? 'Oktober';
            $tahunIni = (string)$cycleYear;

            // Cek apakah ada data di kehadiran_lembaga untuk siklus tersebut (cocokkan tahun persis atau format tahun ajaran)
            $matchingYear = DB::table('kehadiran_lembaga')
                ->where('bulan', $bulanIni)
                ->where(function ($q) use ($tahunIni) {
                    $q->where('tahun', $tahunIni)
                      ->orWhere('tahun', 'LIKE', "%$tahunIni%");
                })
                ->orderBy('id_kehadiran_lembaga', 'desc')
                ->value('tahun');

            if ($matchingYear) {
                $tahunIni = $matchingYear;
            } else {
                // Fallback jika belum ada data untuk siklus ini, gunakan periode terbaru dari database
                $latest = DB::table('kehadiran_lembaga')
                    ->whereNotNull('bulan')->where('bulan', '!=', '')
                    ->whereNotNull('tahun')->where('tahun', '!=', '')
                    ->orderBy('id_kehadiran_lembaga', 'desc')
                    ->first();
                if ($latest) {
                    $bulanIni = $latest->bulan;
                    $tahunIni = $latest->tahun;
                }
            }
        }

        // 2. Tentukan Periode "Bulan Lalu"
        $idx = array_search(ucfirst(strtolower($bulanIni)), $bulanIndo);
        if ($idx === false || $idx === 1) {
            $prevMonthNum = 12;
            $cleanY = (int)preg_replace('/[^0-9]/', '', substr((string)$tahunIni, 0, 4));
            if (!$cleanY) $cleanY = (int)date('Y');
            $tahunLalu = (string)($cleanY - 1);
        } else {
            $prevMonthNum = $idx - 1;
            $tahunLalu = $tahunIni;
        }
        $bulanLalu = $bulanIndo[$prevMonthNum];

        // Pastikan format tahunLalu sesuai dengan data kehadiran_lembaga bila tersedia
        $matchingYearLalu = DB::table('kehadiran_lembaga')
            ->where('bulan', $bulanLalu)
            ->where(function ($q) use ($tahunLalu) {
                $q->where('tahun', $tahunLalu)
                  ->orWhere('tahun', 'LIKE', "%$tahunLalu%");
            })
            ->orderBy('id_kehadiran_lembaga', 'desc')
            ->value('tahun');

        if ($matchingYearLalu) {
            $tahunLalu = $matchingYearLalu;
        }

        // 3. Ambil Data Barokah (Struktural + Pengajar + Satpam) & Lembaga
        $dataBulanIni  = $this->getBarokahDataPeriode($bulanIni, $tahunIni);
        $dataBulanLalu = $this->getBarokahDataPeriode($bulanLalu, $tahunLalu);

        // 4. Hitung Selisih & Pertumbuhan
        $selisihNominal = $dataBulanIni['total_nominal'] - $dataBulanLalu['total_nominal'];
        $selisihLembaga = $dataBulanIni['total_lembaga'] - $dataBulanLalu['total_lembaga'];
        $persenTumbuh   = $dataBulanLalu['total_nominal'] > 0
            ? round(($selisihNominal / $dataBulanLalu['total_nominal']) * 100, 1)
            : ($dataBulanIni['total_nominal'] > 0 ? 100 : 0);

        // Daftar opsi filter periode
        $daftarTahun = DB::table('kehadiran_lembaga')
            ->select('tahun')
            ->whereNotNull('tahun')->where('tahun', '!=', '')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');
        if ($daftarTahun->isEmpty()) {
            $daftarTahun = collect([(string)date('Y'), (string)(date('Y') - 1)]);
        }
        $daftarBulan = array_values($bulanIndo);

        // 5. Statistik Rekening Umana per Kategori Bank
        $bankRaw = DB::table('umana')
            ->select('nama_bank', DB::raw('COUNT(*) as total'))
            ->groupBy('nama_bank')
            ->orderBy('total', 'desc')
            ->get();

        $bankStats = [
            'bri'         => 0,
            'bsi'         => 0,
            'jatim'       => 0,
            'mandiri'     => 0,
            'bni'         => 0,
            'bca'         => 0,
            'lainnya'     => 0,
            'tanpa_rek'   => 0,
        ];
        $totalDenganRekening = 0;

        foreach ($bankRaw as $b) {
            $nama  = strtoupper(trim((string)$b->nama_bank));
            $count = (int)$b->total;

            if (empty($nama) || $nama === '-' || $nama === 'NULL') {
                $bankStats['tanpa_rek'] += $count;
            } elseif (strpos($nama, 'BSI') !== false || strpos($nama, 'SYARIAH') !== false || strpos($nama, "SYARI'AH") !== false) {
                $bankStats['bsi'] += $count;
                $totalDenganRekening += $count;
            } elseif (strpos($nama, 'BRI') !== false || strpos($nama, 'RAKYAT') !== false) {
                $bankStats['bri'] += $count;
                $totalDenganRekening += $count;
            } elseif (strpos($nama, 'JATIM') !== false) {
                $bankStats['jatim'] += $count;
                $totalDenganRekening += $count;
            } elseif (strpos($nama, 'MANDIRI') !== false) {
                $bankStats['mandiri'] += $count;
                $totalDenganRekening += $count;
            } elseif (strpos($nama, 'BNI') !== false) {
                $bankStats['bni'] += $count;
                $totalDenganRekening += $count;
            } elseif (strpos($nama, 'BCA') !== false) {
                $bankStats['bca'] += $count;
                $totalDenganRekening += $count;
            } else {
                $bankStats['lainnya'] += $count;
                $totalDenganRekening += $count;
            }
        }

        // Kategori Transfer Utama (BSI khusus vs Selain BSI masuk ke BRI & Rekanan)
        $kategoriTransfer = [
            'bsi'          => $bankStats['bsi'],
            'bri_mitra'    => $bankStats['bri'] + $bankStats['jatim'] + $bankStats['mandiri'] + $bankStats['bni'] + $bankStats['bca'] + $bankStats['lainnya'],
            'belum_ada'    => $bankStats['tanpa_rek']
        ];

        return view('dashboard.index', compact(
            'totalUmana',
            'totalLembaga',
            'totalPenempatan',
            'totalPengajar',
            'dataBulanIni',
            'dataBulanLalu',
            'selisihNominal',
            'selisihLembaga',
            'persenTumbuh',
            'bulanIni',
            'tahunIni',
            'daftarBulan',
            'daftarTahun',
            'bankStats',
            'kategoriTransfer',
            'totalDenganRekening'
        ));
    }

    /**
     * Hitung akumulasi barokah Struktural, Pengajar, dan Satpam serta jumlah lembaga unik
     */
    private function getBarokahDataPeriode($bulan, $tahun)
    {
        // 1. STRUKTURAL
        $strukturalRaw = DB::table('total_barokah as tb')
            ->join('penempatan as p', 'tb.id_penempatan', '=', 'p.id_penempatan')
            ->where('tb.bulan', $bulan)
            ->where('tb.tahun', $tahun)
            ->select('p.id_lembaga', DB::raw('SUM(tb.diterima) as nominal'))
            ->groupBy('p.id_lembaga')
            ->get();

        if ($strukturalRaw->isEmpty()) {
            $strukturalRaw = DB::table('kehadiran_lembaga')
                ->where('kategori', 'Struktural')
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->select('id_lembaga', DB::raw('SUM(jumlah_total) as nominal'))
                ->groupBy('id_lembaga')
                ->get();
        }

        $nomStruktural = (float)$strukturalRaw->sum('nominal');
        $lemStruktural = $strukturalRaw->pluck('id_lembaga')->filter()->unique()->toArray();

        // 2. PENGAJAR
        $pengajarRaw = DB::table('total_barokah_pengajar as tbp')
            ->join('pengajar as p', 'tbp.id_pengajar', '=', 'p.id_pengajar')
            ->where('tbp.bulan', $bulan)
            ->where('tbp.tahun', $tahun)
            ->select('p.id_lembaga', DB::raw('SUM(tbp.diterima) as nominal'))
            ->groupBy('p.id_lembaga')
            ->get();

        if ($pengajarRaw->isEmpty()) {
            $pengajarRaw = DB::table('kehadiran_lembaga')
                ->where('kategori', 'Pengajar')
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->select('id_lembaga', DB::raw('SUM(jumlah_total) as nominal'))
                ->groupBy('id_lembaga')
                ->get();
        }

        $nomPengajar = (float)$pengajarRaw->sum('nominal');
        $lemPengajar = $pengajarRaw->pluck('id_lembaga')->filter()->unique()->toArray();

        // 3. SATPAM
        $satpamRaw = DB::table('total_barokah_satpam as tbs')
            ->leftJoin('kehadiran_lembaga as kl', 'tbs.id_kehadiran_lembaga', '=', 'kl.id_kehadiran_lembaga')
            ->where(function ($q) use ($bulan) {
                $q->where('tbs.bulan', $bulan)
                  ->orWhere('kl.bulan', $bulan);
            })
            ->where(function ($q) use ($tahun) {
                $q->where('tbs.tahun', $tahun)
                  ->orWhere('kl.tahun', $tahun);
            })
            ->select(DB::raw('COALESCE(kl.id_lembaga, 59) as id_lembaga'), DB::raw('SUM(tbs.diterima) as nominal'))
            ->groupBy(DB::raw('COALESCE(kl.id_lembaga, 59)'))
            ->get();

        if ($satpamRaw->isEmpty()) {
            $satpamRaw = DB::table('kehadiran_lembaga')
                ->where('kategori', 'Satpam')
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->select('id_lembaga', DB::raw('SUM(jumlah_total) as nominal'))
                ->groupBy('id_lembaga')
                ->get();
        }

        $nomSatpam = (float)$satpamRaw->sum('nominal');
        $lemSatpam = $satpamRaw->pluck('id_lembaga')->filter()->unique()->toArray();

        // Gabungan seluruh lembaga unik yang memiliki barokah pada periode ini
        $allUniqueLembaga = array_unique(array_filter(array_merge($lemStruktural, $lemPengajar, $lemSatpam)));
        $totalNominal     = $nomStruktural + $nomPengajar + $nomSatpam;

        return [
            'bulan'               => $bulan,
            'tahun'               => $tahun,
            'total_nominal'       => $totalNominal,
            'total_lembaga'       => count($allUniqueLembaga),
            'struktural_nominal'  => $nomStruktural,
            'struktural_lembaga'  => count($lemStruktural),
            'pengajar_nominal'    => $nomPengajar,
            'pengajar_lembaga'    => count($lemPengajar),
            'satpam_nominal'      => $nomSatpam,
            'satpam_lembaga'      => count($lemSatpam),
        ];
    }
}
