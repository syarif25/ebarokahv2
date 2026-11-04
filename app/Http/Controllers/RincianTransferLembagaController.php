<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RincianTransferLembagaController extends Controller
{
    private $bulanList = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    /**
     * Tampilan Slip Laporan Transfer Lembaga
     */
    public function index(Request $request)
    {
        $lembagaList = DB::table('lembaga')
            ->select('id_lembaga', 'nama_lembaga')
            ->orderBy('nama_lembaga', 'asc')
            ->get();

        $tahunDb = DB::table('kehadiran_lembaga')
            ->select('tahun')
            ->whereNotNull('tahun')
            ->where('tahun', '!=', '')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->toArray();

        $tahunList = !empty($tahunDb) ? $tahunDb : [date('Y'), date('Y') - 1, date('Y') - 2];
        if (!in_array(date('Y'), $tahunList)) {
            array_unshift($tahunList, date('Y'));
        }

        $bulanList = $this->bulanList;

        // Ambil default bulan dan tahun terbaru
        $latestRecord = DB::table('kehadiran_lembaga')
            ->orderBy('id_kehadiran_lembaga', 'desc')
            ->first();

        $idLembaga     = $request->input('id_lembaga');
        $filterLembaga = $request->input('filter_lembaga');
        $view          = $request->input('view');
        $bulan         = $request->input('bulan', ($latestRecord->bulan ?? 'Agustus'));
        $tahun         = $request->input('tahun', ($latestRecord->tahun ?? date('Y')));
        $isDemo        = $request->boolean('demo', false);
        $staffNama     = $request->input('staff_nama', (Auth::user()->username ?? 'Azmi'));

        $selectedLembaga = null;
        $dataRincian     = null;
        $tableRekap      = null;

        // JIKA view == 'slip' atau user mengklik tombol detail lembaga
        if (($idLembaga && $view === 'slip') || ($idLembaga && !$request->has('filter_lembaga') && !$request->has('bulan'))) {
            $selectedLembaga = DB::table('lembaga')->where('id_lembaga', $idLembaga)->first();
            if (!$selectedLembaga && $isDemo) {
                $selectedLembaga = (object)[
                    'id_lembaga' => 0,
                    'nama_lembaga' => 'Fakultas Tarbiyah'
                ];
            }
            $dataRincian = $this->getRincianData($idLembaga, $bulan, $tahun, $isDemo);
        } else {
            // TAMPILKAN TABEL REKAP SEMUA LEMBAGA
            $tableRekap = $this->getTableRekapData($bulan, $tahun, $filterLembaga);
            if ($tableRekap->isEmpty() && $isDemo) {
                $tableRekap = $this->getDemoTableRows();
            }
        }

        $tglCetak = $this->formatTglIndo(date('Y-m-d'));

        return view('laporan.rincian-transfer.index', compact(
            'lembagaList',
            'tahunList',
            'bulanList',
            'idLembaga',
            'filterLembaga',
            'bulan',
            'tahun',
            'isDemo',
            'tglCetak',
            'staffNama',
            'selectedLembaga',
            'dataRincian',
            'tableRekap'
        ));
    }

    /**
     * Cetak Lembar Slip TF
     */
    public function cetak(Request $request)
    {
        $idLembaga   = $request->input('id_lembaga');
        $bulan       = $request->input('bulan', 'Oktober');
        $tahun       = $request->input('tahun', date('Y'));
        $isDemo      = $request->boolean('demo', false);
        $staffNama   = $request->input('staff_nama', (Auth::user()->username ?? 'Azmi'));

        $selectedLembaga = null;
        if ($idLembaga) {
            $selectedLembaga = DB::table('lembaga')->where('id_lembaga', $idLembaga)->first();
        }
        if (!$selectedLembaga && $isDemo) {
            $selectedLembaga = (object)[
                'id_lembaga' => 0,
                'nama_lembaga' => 'Fakultas Tarbiyah'
            ];
        }

        $dataRincian = $this->getRincianData($idLembaga, $bulan, $tahun, $isDemo);
        $tglCetak    = $this->formatTglIndo(date('Y-m-d'));

        return view('laporan.rincian-transfer.cetak', compact(
            'selectedLembaga',
            'bulan',
            'tahun',
            'isDemo',
            'tglCetak',
            'staffNama',
            'dataRincian'
        ));
    }

    /**
     * Format Tanggal Bahasa Indonesia
     */
    private function formatTglIndo($tanggal)
    {
        $bulanMap = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $time = strtotime($tanggal);
        if (!$time) return $tanggal;
        return (int)date('j', $time) . ' ' . $bulanMap[(int)date('n', $time)] . ' ' . date('Y', $time);
    }

    /**
     * Ambil data transfer per bank & rincian potongan langsung dari database riil
     */
    private function getRincianData($idLembaga, $bulan, $tahun, $forceDemo = false)
    {
        if ($forceDemo) {
            return $this->getDummyDemoData();
        }

        // Ambil data kehadiran lembaga pada periode ini
        $klRecords = DB::table('kehadiran_lembaga')
            ->where('id_lembaga', $idLembaga)
            ->where('bulan', $bulan)
            ->where(function ($q) use ($tahun) {
                $q->where('tahun', $tahun)
                  ->orWhere('tahun', 'LIKE', "%$tahun%");
            })
            ->get()
            ->keyBy('kategori');

        $monthNumber = array_search(ucfirst(strtolower($bulan)), [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ]);
        if (!$monthNumber) $monthNumber = (int)date('m');
        $cleanYear = (int)preg_replace('/[^0-9]/', '', substr((string)$tahun, 0, 4));
        if (!$cleanYear) $cleanYear = (int)date('Y');
        $filterTanggal = sprintf('%04d-%02d-01', $cleanYear, $monthNumber);

        // ==========================================
        // 1. DATA STRUKTURAL
        // ==========================================
        $qStruktural = DB::table('total_barokah as tb')
            ->join('penempatan as p', 'tb.id_penempatan', '=', 'p.id_penempatan')
            ->join('umana as u', 'p.nik', '=', 'u.nik')
            ->where('p.id_lembaga', $idLembaga);

        if (isset($klRecords['Struktural'])) {
            $qStruktural->where(function ($w) use ($klRecords, $bulan, $tahun) {
                $w->where('tb.id_kehadiran', $klRecords['Struktural']->id_kehadiran_lembaga)
                  ->orWhere(function ($sub) use ($bulan, $tahun) {
                      $sub->where('tb.bulan', $bulan)
                          ->where(function ($t) use ($tahun) {
                              $t->where('tb.tahun', $tahun)
                                ->orWhere('tb.tahun', 'LIKE', "%$tahun%");
                          });
                  });
            });
        } else {
            $qStruktural->where('tb.bulan', $bulan)
                ->where(function ($t) use ($tahun) {
                    $t->where('tb.tahun', $tahun)
                      ->orWhere('tb.tahun', 'LIKE', "%$tahun%");
                });
        }

        // Tanggal transfer Struktural diambil dari tabel total_barokah (kolom timestamp)
        $rawTglStruktural = (clone $qStruktural)
            ->whereNotNull('tb.timestamp')
            ->where('tb.timestamp', '!=', '0000-00-00 00:00:00')
            ->orderByRaw("CASE WHEN tb.status = 'selesai' THEN 1 ELSE 2 END")
            ->orderBy('tb.timestamp', 'desc')
            ->select('tb.timestamp')
            ->first();

        $tglTransferStruktural = '-';
        if (!empty($rawTglStruktural->timestamp)) {
            $tglTransferStruktural = $this->formatTglIndo($rawTglStruktural->timestamp);
        } elseif (isset($klRecords['Struktural']) && !empty($klRecords['Struktural']->tgl_input)) {
            $tglTransferStruktural = $this->formatTglIndo($klRecords['Struktural']->tgl_input);
        }

        $strukturalRaw = (clone $qStruktural)->select('u.nama_bank', 'tb.diterima')->get();

        // Normalisasi Bank Struktural (BRI & BSI)
        $strukturalBank = $this->groupAndSortBanks($strukturalRaw);

        // Potongan Struktural
        $potonganStrukturalRaw = DB::table('potongan_umana as pu')
            ->join('penempatan as p', 'pu.id_penempatan', '=', 'p.id_penempatan')
            ->join('potongan as pot', 'pu.jenis_potongan', '=', 'pot.id_potongan')
            ->where('p.id_lembaga', $idLembaga)
            ->where(function ($q) use ($filterTanggal) {
                $q->where(function ($m) use ($filterTanggal) {
                    $m->whereNull('pu.min_periode_potongan')
                      ->orWhere('pu.min_periode_potongan', '0000-00-00')
                      ->orWhere('pu.min_periode_potongan', '<=', $filterTanggal);
                })->where(function ($mx) use ($filterTanggal) {
                    $mx->whereNull('pu.max_periode_potongan')
                       ->orWhere('pu.max_periode_potongan', '0000-00-00')
                       ->orWhere('pu.max_periode_potongan', '>=', $filterTanggal);
                });
            })
            ->select('pot.nama_potongan', DB::raw('SUM(pu.nominal_potongan) as nominal'))
            ->groupBy('pot.nama_potongan')
            ->orderBy('nominal', 'desc')
            ->get();

        if ($potonganStrukturalRaw->isEmpty()) {
            $potonganStrukturalRaw = DB::table('potongan_umana as pu')
                ->join('penempatan as p', 'pu.id_penempatan', '=', 'p.id_penempatan')
                ->join('potongan as pot', 'pu.jenis_potongan', '=', 'pot.id_potongan')
                ->where('p.id_lembaga', $idLembaga)
                ->select('pot.nama_potongan', DB::raw('SUM(pu.nominal_potongan) as nominal'))
                ->groupBy('pot.nama_potongan')
                ->orderBy('nominal', 'desc')
                ->get();
        }

        $potonganStruktural = $this->groupPotonganPimpinan($potonganStrukturalRaw);

        // ==========================================
        // 2. DATA PENGAJAR
        // ==========================================
        $qPengajar = DB::table('total_barokah_pengajar as tbp')
            ->join('pengajar as p', 'tbp.id_pengajar', '=', 'p.id_pengajar')
            ->join('umana as u', 'p.nik', '=', 'u.nik')
            ->where('p.id_lembaga', $idLembaga);

        if (isset($klRecords['Pengajar'])) {
            $qPengajar->where(function ($w) use ($klRecords, $bulan, $tahun) {
                $w->where('tbp.id_kehadiran_lembaga', $klRecords['Pengajar']->id_kehadiran_lembaga)
                  ->orWhere(function ($sub) use ($bulan, $tahun) {
                      $sub->where('tbp.bulan', $bulan)
                          ->where(function ($t) use ($tahun) {
                              $t->where('tbp.tahun', $tahun)
                                ->orWhere('tbp.tahun', 'LIKE', "%$tahun%");
                          });
                  });
            });
        } else {
            $qPengajar->where('tbp.bulan', $bulan)
                ->where(function ($t) use ($tahun) {
                    $t->where('tbp.tahun', $tahun)
                      ->orWhere('tbp.tahun', 'LIKE', "%$tahun%");
                });
        }

        // Tanggal transfer Pengajar diambil dari tabel total_barokah_pengajar (kolom timestamp)
        $rawTglPengajar = (clone $qPengajar)
            ->whereNotNull('tbp.timestamp')
            ->where('tbp.timestamp', '!=', '0000-00-00 00:00:00')
            ->orderByRaw("CASE WHEN tbp.status = 'selesai' THEN 1 ELSE 2 END")
            ->orderBy('tbp.timestamp', 'desc')
            ->select('tbp.timestamp')
            ->first();

        $tglTransferPengajar = '-';
        if (!empty($rawTglPengajar->timestamp)) {
            $tglTransferPengajar = $this->formatTglIndo($rawTglPengajar->timestamp);
        } elseif (isset($klRecords['Pengajar']) && !empty($klRecords['Pengajar']->tgl_input)) {
            $tglTransferPengajar = $this->formatTglIndo($klRecords['Pengajar']->tgl_input);
        }

        $pengajarRaw = (clone $qPengajar)->select('u.nama_bank', 'tbp.diterima')->get();

        // Normalisasi Bank Pengajar (BRI & BSI)
        $pengajarBank = $this->groupAndSortBanks($pengajarRaw);

        // Potongan Pengajar
        $potonganPengajarRaw = DB::table('potongan_pengajar as pp')
            ->join('pengajar as p', 'pp.id_pengajar', '=', 'p.id_pengajar')
            ->join('potongan as pot', 'pp.jenis_potongan', '=', 'pot.id_potongan')
            ->where('p.id_lembaga', $idLembaga)
            ->where(function ($q) use ($filterTanggal) {
                $q->where(function ($m) use ($filterTanggal) {
                    $m->whereNull('pp.min_periode_potongan')
                      ->orWhere('pp.min_periode_potongan', '0000-00-00')
                      ->orWhere('pp.min_periode_potongan', '<=', $filterTanggal);
                })->where(function ($mx) use ($filterTanggal) {
                    $mx->whereNull('pp.max_periode_potongan')
                       ->orWhere('pp.max_periode_potongan', '0000-00-00')
                       ->orWhere('pp.max_periode_potongan', '>=', $filterTanggal);
                });
            })
            ->select('pot.nama_potongan', DB::raw('SUM(pp.nominal_potongan) as nominal'))
            ->groupBy('pot.nama_potongan')
            ->orderBy('nominal', 'desc')
            ->get();

        if ($potonganPengajarRaw->isEmpty()) {
            $potonganPengajarRaw = DB::table('potongan_pengajar as pp')
                ->join('pengajar as p', 'pp.id_pengajar', '=', 'p.id_pengajar')
                ->join('potongan as pot', 'pp.jenis_potongan', '=', 'pot.id_potongan')
                ->where('p.id_lembaga', $idLembaga)
                ->select('pot.nama_potongan', DB::raw('SUM(pp.nominal_potongan) as nominal'))
                ->groupBy('pot.nama_potongan')
                ->orderBy('nominal', 'desc')
                ->get();
        }

        $potonganPengajar = $this->groupPotonganPimpinan($potonganPengajarRaw);

        $totalTfStruktural  = $strukturalBank->sum('total_nominal');
        $totalPotStruktural = $potonganStruktural->sum('nominal');

        $totalTfPengajar    = $pengajarBank->sum('total_nominal');
        $totalPotPengajar   = $potonganPengajar->sum('nominal');

        $hasStruktural = ($totalTfStruktural > 0 || $totalPotStruktural > 0);
        $hasPengajar   = ($totalTfPengajar > 0 || $totalPotPengajar > 0);
        $hasRealData   = ($hasStruktural || $hasPengajar);

        if (!$hasRealData) {
            return $this->getDummyDemoData();
        }

        // Rekapitulasi Total Gabungan (Struktural + Pengajar)
        $strukturalBri = $strukturalBank->firstWhere('nama_bank', 'BRI')->total_nominal ?? 0;
        $strukturalBsi = $strukturalBank->firstWhere('nama_bank', 'BSI')->total_nominal ?? 0;

        $pengajarBri = $pengajarBank->firstWhere('nama_bank', 'BRI')->total_nominal ?? 0;
        $pengajarBsi = $pengajarBank->firstWhere('nama_bank', 'BSI')->total_nominal ?? 0;

        $totalBri = $strukturalBri + $pengajarBri;
        $totalBsi = $strukturalBsi + $pengajarBsi;

        $rekapBank = collect([
            (object)['nama_bank' => 'BRI', 'total_nominal' => $totalBri],
            (object)['nama_bank' => 'BSI', 'total_nominal' => $totalBsi],
        ]);

        $listrikTotal    = ($potonganStruktural->firstWhere('nama_potongan', 'Listrik')->nominal ?? 0)
                         + ($potonganPengajar->firstWhere('nama_potongan', 'Listrik')->nominal ?? 0);
        $kalenderTotal   = ($potonganStruktural->firstWhere('nama_potongan', 'Kalender')->nominal ?? 0)
                         + ($potonganPengajar->firstWhere('nama_potongan', 'Kalender')->nominal ?? 0);
        $bpjsTotal       = ($potonganStruktural->firstWhere('nama_potongan', 'BPJS')->nominal ?? 0)
                         + ($potonganPengajar->firstWhere('nama_potongan', 'BPJS')->nominal ?? 0);
        $diTunaikanTotal = ($potonganStruktural->firstWhere('nama_potongan', 'Di tunaikan')->nominal ?? 0)
                         + ($potonganPengajar->firstWhere('nama_potongan', 'Di tunaikan')->nominal ?? 0);

        $rekapPotongan = collect([
            (object)['nama_potongan' => 'Listrik', 'nominal' => $listrikTotal],
            (object)['nama_potongan' => 'Kalender', 'nominal' => $kalenderTotal],
            (object)['nama_potongan' => 'BPJS', 'nominal' => $bpjsTotal],
            (object)['nama_potongan' => 'Di tunaikan', 'nominal' => $diTunaikanTotal],
        ]);

        $grandTotalTf  = $totalTfStruktural + $totalTfPengajar;
        $grandTotalPot = $totalPotStruktural + $totalPotPengajar;

        return [
            'is_demo'        => false,
            'has_struktural' => $hasStruktural,
            'has_pengajar'   => $hasPengajar,
            'show_total'     => ($hasStruktural && $hasPengajar),
            'struktural' => [
                'bank'           => $strukturalBank,
                'potongan'       => $potonganStruktural,
                'total_transfer' => $totalTfStruktural,
                'total_potongan' => $totalPotStruktural,
                'tgl_transfer'   => $tglTransferStruktural,
            ],
            'pengajar' => [
                'bank'           => $pengajarBank,
                'potongan'       => $potonganPengajar,
                'total_transfer' => $totalTfPengajar,
                'total_potongan' => $totalPotPengajar,
                'tgl_transfer'   => $tglTransferPengajar,
            ],
            'total' => [
                'bank'           => $rekapBank,
                'potongan'       => $rekapPotongan,
                'total_transfer' => $grandTotalTf,
                'total_potongan' => $grandTotalPot,
            ],
            'grand_total_transfer' => $grandTotalTf,
            'grand_total_potongan' => $grandTotalPot,
            'has_data'             => $hasRealData
        ];
    }

    /**
     * Pengelompokan Bank sesuai aturan transfer:
     * - Khusus rekening BSI masuk ke rekapan 'BSI'
     * - Rekening selain BSI (BRI, Bank Jatim, Mandiri, BCA, dll.) seluruhnya masuk ke rekapan 'BRI'
     */
    private function groupAndSortBanks($rows)
    {
        $briTotal = 0;
        $briOrang = 0;
        $bsiTotal = 0;
        $bsiOrang = 0;

        foreach ($rows as $row) {
            $raw = strtoupper(trim((string)$row->nama_bank));
            $nominal = (float)$row->diterima;

            // Cek apakah rekening BSI
            if (strpos($raw, 'BSI') !== false || strpos($raw, 'SYARIAH') !== false || strpos($raw, "SYARI'AH") !== false) {
                $bsiTotal += $nominal;
                $bsiOrang++;
            } else {
                // Selain BSI seluruhnya masuk ke rekapan bank BRI
                $briTotal += $nominal;
                $briOrang++;
            }
        }

        $bankGroups = [
            (object)[
                'nama_bank'     => 'BRI',
                'total_orang'   => $briOrang,
                'total_nominal' => $briTotal
            ],
            (object)[
                'nama_bank'     => 'BSI',
                'total_orang'   => $bsiOrang,
                'total_nominal' => $bsiTotal
            ],
        ];

        return collect($bankGroups);
    }

    /**
     * Mengelompokkan potongan sesuai permintaan pimpinan:
     * (Indisipliner tidak dihitung / tidak usah ditampilkan)
     * 1. Listrik
     * 2. Kalender
     * 3. BPJS
     * 4. Di tunaikan (semua potongan selain Listrik, Kalender & BPJS)
     */
    private function groupPotonganPimpinan($rawPotongan)
    {
        $listrik       = 0;
        $kalender      = 0;
        $bpjs          = 0;
        $diTunaikan    = 0;

        foreach ($rawPotongan as $pot) {
            $nama    = strtolower(trim($pot->nama_potongan ?? ''));
            $nominal = (float)($pot->nominal ?? 0);

            // Indisipliner tidak dihitung / tidak usah ditampilkan
            if (strpos($nama, 'indisiplin') !== false) {
                continue;
            } elseif (strpos($nama, 'listrik') !== false) {
                $listrik += $nominal;
            } elseif (strpos($nama, 'kalender') !== false) {
                $kalender += $nominal;
            } elseif (strpos($nama, 'bpjs') !== false) {
                $bpjs += $nominal;
            } else {
                $diTunaikan += $nominal;
            }
        }

        return collect([
            (object)['nama_potongan' => 'Listrik', 'nominal' => $listrik],
            (object)['nama_potongan' => 'Kalender', 'nominal' => $kalender],
            (object)['nama_potongan' => 'BPJS', 'nominal' => $bpjs],
            (object)['nama_potongan' => 'Di tunaikan', 'nominal' => $diTunaikan],
        ]);
    }

    /**
     * Data Simulasi Mockup jika Database pada periode yang dipilih belum memiliki data
     */
    private function getDummyDemoData()
    {
        $strukturalBank = collect([
            (object)['nama_bank' => 'BRI', 'total_orang' => 5, 'total_nominal' => 10633135],
            (object)['nama_bank' => 'BSI', 'total_orang' => 1, 'total_nominal' => 1670000],
        ]);

        $strukturalPotongan = collect([
            (object)['nama_potongan' => 'Listrik', 'nominal' => 350000],
            (object)['nama_potongan' => 'Kalender', 'nominal' => 25000],
            (object)['nama_potongan' => 'BPJS', 'nominal' => 450000],
            (object)['nama_potongan' => 'Di tunaikan', 'nominal' => 175000],
        ]);

        $pengajarBank = collect([
            (object)['nama_bank' => 'BRI', 'total_orang' => 20, 'total_nominal' => 32100000],
            (object)['nama_bank' => 'BSI', 'total_orang' => 7,  'total_nominal' => 12300000],
        ]);

        $pengajarPotongan = collect([
            (object)['nama_potongan' => 'Listrik', 'nominal' => 600000],
            (object)['nama_potongan' => 'Kalender', 'nominal' => 0],
            (object)['nama_potongan' => 'BPJS', 'nominal' => 850000],
            (object)['nama_potongan' => 'Di tunaikan', 'nominal' => 400000],
        ]);

        $totalTfStruktural  = $strukturalBank->sum('total_nominal');
        $totalPotStruktural = $strukturalPotongan->sum('nominal');

        $totalTfPengajar    = $pengajarBank->sum('total_nominal');
        $totalPotPengajar   = $pengajarPotongan->sum('nominal');

        $rekapDemoBank = collect([
            (object)['nama_bank' => 'BRI', 'total_nominal' => 10633135 + 32100000],
            (object)['nama_bank' => 'BSI', 'total_nominal' => 1670000 + 12300000],
        ]);

        $rekapDemoPotongan = collect([
            (object)['nama_potongan' => 'Listrik', 'nominal' => 350000 + 600000],
            (object)['nama_potongan' => 'Kalender', 'nominal' => 25000 + 0],
            (object)['nama_potongan' => 'BPJS', 'nominal' => 450000 + 850000],
            (object)['nama_potongan' => 'Di tunaikan', 'nominal' => 175000 + 400000],
        ]);

        $grandTotalTf  = $totalTfStruktural + $totalTfPengajar;
        $grandTotalPot = $totalPotStruktural + $totalPotPengajar;

        return [
            'is_demo'        => true,
            'has_struktural' => true,
            'has_pengajar'   => true,
            'show_total'     => true,
            'struktural' => [
                'bank'           => $strukturalBank,
                'potongan'       => $strukturalPotongan,
                'total_transfer' => $totalTfStruktural,
                'total_potongan' => $totalPotStruktural,
                'tgl_transfer'   => '23 November 2026',
            ],
            'pengajar' => [
                'bank'           => $pengajarBank,
                'potongan'       => $pengajarPotongan,
                'total_transfer' => $totalTfPengajar,
                'total_potongan' => $totalPotPengajar,
                'tgl_transfer'   => '23 November 2026',
            ],
            'total' => [
                'bank'           => $rekapDemoBank,
                'potongan'       => $rekapDemoPotongan,
                'total_transfer' => $grandTotalTf,
                'total_potongan' => $grandTotalPot,
            ],
            'grand_total_transfer' => $grandTotalTf,
            'grand_total_potongan' => $grandTotalPot,
            'has_data'             => true
        ];
    }

    /**
     * Ambil data rekapitulasi untuk tampilan tabel awal semua lembaga
     */
    private function getTableRekapData($bulan, $tahun, $filterLembaga = null)
    {
        $monthNumber = array_search(ucfirst(strtolower($bulan)), [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ]);
        if (!$monthNumber) $monthNumber = (int)date('m');
        $cleanYear = (int)preg_replace('/[^0-9]/', '', substr((string)$tahun, 0, 4));
        if (!$cleanYear) $cleanYear = (int)date('Y');
        $filterTanggal = sprintf('%04d-%02d-01', $cleanYear, $monthNumber);

        // 1. Ambil data kehadiran lembaga pada periode ini
        $klList = DB::table('kehadiran_lembaga as kl')
            ->join('lembaga as l', 'kl.id_lembaga', '=', 'l.id_lembaga')
            ->where('kl.bulan', $bulan)
            ->where(function ($q) use ($tahun) {
                $q->where('kl.tahun', $tahun)
                  ->orWhere('kl.tahun', 'LIKE', "%$tahun%");
            })
            ->select('kl.id_kehadiran_lembaga', 'kl.id_lembaga', 'l.nama_lembaga', 'kl.kategori', 'kl.jumlah_total')
            ->get();

        // 2. Ambil total diterima struktural per lembaga beserta nama bank
        $strukturalBankRaw = DB::table('total_barokah as tb')
            ->join('penempatan as p', 'tb.id_penempatan', '=', 'p.id_penempatan')
            ->join('umana as u', 'p.nik', '=', 'u.nik')
            ->where('tb.bulan', $bulan)
            ->where(function ($q) use ($tahun) {
                $q->where('tb.tahun', $tahun)
                  ->orWhere('tb.tahun', 'LIKE', "%$tahun%");
            })
            ->select('p.id_lembaga', 'u.nama_bank', DB::raw('SUM(tb.diterima) as nominal'))
            ->groupBy('p.id_lembaga', 'u.nama_bank')
            ->get();

        // 3. Ambil total diterima pengajar per lembaga beserta nama bank
        $pengajarBankRaw = DB::table('total_barokah_pengajar as tbp')
            ->join('pengajar as p', 'tbp.id_pengajar', '=', 'p.id_pengajar')
            ->join('umana as u', 'p.nik', '=', 'u.nik')
            ->where('tbp.bulan', $bulan)
            ->where(function ($q) use ($tahun) {
                $q->where('tbp.tahun', $tahun)
                  ->orWhere('tbp.tahun', 'LIKE', "%$tahun%");
            })
            ->select('p.id_lembaga', 'u.nama_bank', DB::raw('SUM(tbp.diterima) as nominal'))
            ->groupBy('p.id_lembaga', 'u.nama_bank')
            ->get();

        $bankByLembaga = [];
        $tbStruktural  = [];
        $tbPengajar    = [];

        foreach ($strukturalBankRaw as $s) {
            $tbStruktural[$s->id_lembaga] = ($tbStruktural[$s->id_lembaga] ?? 0) + (float)$s->nominal;
            $raw = strtoupper(trim((string)$s->nama_bank));
            if (strpos($raw, 'BSI') !== false || strpos($raw, 'SYARIAH') !== false || strpos($raw, "SYARI'AH") !== false) {
                $bankByLembaga[$s->id_lembaga]['bsi'] = ($bankByLembaga[$s->id_lembaga]['bsi'] ?? 0) + (float)$s->nominal;
            } else {
                $bankByLembaga[$s->id_lembaga]['bri'] = ($bankByLembaga[$s->id_lembaga]['bri'] ?? 0) + (float)$s->nominal;
            }
        }

        foreach ($pengajarBankRaw as $p) {
            $tbPengajar[$p->id_lembaga] = ($tbPengajar[$p->id_lembaga] ?? 0) + (float)$p->nominal;
            $raw = strtoupper(trim((string)$p->nama_bank));
            if (strpos($raw, 'BSI') !== false || strpos($raw, 'SYARIAH') !== false || strpos($raw, "SYARI'AH") !== false) {
                $bankByLembaga[$p->id_lembaga]['bsi'] = ($bankByLembaga[$p->id_lembaga]['bsi'] ?? 0) + (float)$p->nominal;
            } else {
                $bankByLembaga[$p->id_lembaga]['bri'] = ($bankByLembaga[$p->id_lembaga]['bri'] ?? 0) + (float)$p->nominal;
            }
        }

        // 4. Potongan Struktural per lembaga
        $potStruktural = DB::table('potongan_umana as pu')
            ->join('penempatan as p', 'pu.id_penempatan', '=', 'p.id_penempatan')
            ->join('potongan as pot', 'pu.jenis_potongan', '=', 'pot.id_potongan')
            ->where(function ($q) use ($filterTanggal) {
                $q->where(function ($m) use ($filterTanggal) {
                    $m->whereNull('pu.min_periode_potongan')
                      ->orWhere('pu.min_periode_potongan', '0000-00-00')
                      ->orWhere('pu.min_periode_potongan', '<=', $filterTanggal);
                })->where(function ($mx) use ($filterTanggal) {
                    $mx->whereNull('pu.max_periode_potongan')
                       ->orWhere('pu.max_periode_potongan', '0000-00-00')
                       ->orWhere('pu.max_periode_potongan', '>=', $filterTanggal);
                });
            })
            ->select('p.id_lembaga', 'pot.nama_potongan', DB::raw('SUM(pu.nominal_potongan) as nominal'))
            ->groupBy('p.id_lembaga', 'pot.nama_potongan')
            ->get();

        // 5. Potongan Pengajar per lembaga
        $potPengajar = DB::table('potongan_pengajar as pp')
            ->join('pengajar as p', 'pp.id_pengajar', '=', 'p.id_pengajar')
            ->join('potongan as pot', 'pp.jenis_potongan', '=', 'pot.id_potongan')
            ->where(function ($q) use ($filterTanggal) {
                $q->where(function ($m) use ($filterTanggal) {
                    $m->whereNull('pp.min_periode_potongan')
                      ->orWhere('pp.min_periode_potongan', '0000-00-00')
                      ->orWhere('pp.min_periode_potongan', '<=', $filterTanggal);
                })->where(function ($mx) use ($filterTanggal) {
                    $mx->whereNull('pp.max_periode_potongan')
                       ->orWhere('pp.max_periode_potongan', '0000-00-00')
                       ->orWhere('pp.max_periode_potongan', '>=', $filterTanggal);
                });
            })
            ->select('p.id_lembaga', 'pot.nama_potongan', DB::raw('SUM(pp.nominal_potongan) as nominal'))
            ->groupBy('p.id_lembaga', 'pot.nama_potongan')
            ->get();

        // Kumpulkan ID dan Nama Lembaga
        $lembagaMap = [];
        foreach ($klList as $kl) {
            if (!isset($lembagaMap[$kl->id_lembaga])) {
                $lembagaMap[$kl->id_lembaga] = $kl->nama_lembaga;
            }
        }

        $allIds = array_unique(array_merge(
            array_keys($lembagaMap),
            array_keys($tbStruktural),
            array_keys($tbPengajar)
        ));

        if (!empty($allIds)) {
            $missingIds = array_diff($allIds, array_keys($lembagaMap));
            if (!empty($missingIds)) {
                $extra = DB::table('lembaga')
                    ->whereIn('id_lembaga', $missingIds)
                    ->pluck('nama_lembaga', 'id_lembaga')
                    ->toArray();
                foreach ($extra as $id => $name) {
                    $lembagaMap[$id] = $name;
                }
            }
        }

        // Kelompokkan potongan per lembaga
        $potonganByLembaga = [];
        foreach ($potStruktural->concat($potPengajar) as $p) {
            $potonganByLembaga[$p->id_lembaga][] = $p;
        }

        $tableRows = [];
        foreach ($lembagaMap as $idLembaga => $namaLembaga) {
            $totStruktural = (float)($tbStruktural[$idLembaga] ?? 0);
            $totPengajar   = (float)($tbPengajar[$idLembaga] ?? 0);
            $totalBarokah  = $totStruktural + $totPengajar;

            if ($totalBarokah == 0) {
                $totalBarokah = (float)$klList->where('id_lembaga', $idLembaga)->sum('jumlah_total');
            }

            $listrik    = 0;
            $kalender   = 0;
            $bpjs       = 0;
            $ditunaikan = 0;

            if (isset($potonganByLembaga[$idLembaga])) {
                foreach ($potonganByLembaga[$idLembaga] as $pot) {
                    $nama    = strtolower(trim($pot->nama_potongan ?? ''));
                    $nominal = (float)($pot->nominal ?? 0);

                    if (strpos($nama, 'indisiplin') !== false) {
                        continue;
                    } elseif (strpos($nama, 'listrik') !== false) {
                        $listrik += $nominal;
                    } elseif (strpos($nama, 'kalender') !== false) {
                        $kalender += $nominal;
                    } elseif (strpos($nama, 'bpjs') !== false) {
                        $bpjs += $nominal;
                    } else {
                        $ditunaikan += $nominal;
                    }
                }
            }

            $totBri = (float)($bankByLembaga[$idLembaga]['bri'] ?? 0);
            $totBsi = (float)($bankByLembaga[$idLembaga]['bsi'] ?? 0);

            if (($totBri + $totBsi) == 0 && $totalBarokah > 0) {
                $totBri = $totalBarokah;
            }

            if ($totalBarokah > 0 || ($listrik + $kalender + $bpjs + $ditunaikan) > 0) {
                $tableRows[] = (object)[
                    'id_lembaga'    => $idLembaga,
                    'nama_lembaga'  => $namaLembaga,
                    'total_barokah' => $totalBarokah,
                    'total_bri'     => $totBri,
                    'total_bsi'     => $totBsi,
                    'bpjs'          => $bpjs,
                    'listrik'       => $listrik,
                    'kalender'      => $kalender,
                    'ditunaikan'    => $ditunaikan,
                ];
            }
        }

        usort($tableRows, function ($a, $b) {
            return strcmp($a->nama_lembaga, $b->nama_lembaga);
        });

        if ($filterLembaga) {
            $tableRows = array_values(array_filter($tableRows, function ($r) use ($filterLembaga) {
                return $r->id_lembaga == $filterLembaga;
            }));
        }

        return collect($tableRows);
    }

    /**
     * Baris simulasi untuk mode demo
     */
    private function getDemoTableRows()
    {
        return collect([
            (object)[
                'id_lembaga'    => 15,
                'nama_lembaga'  => 'Madrasah Diniyah Sufla SS 1',
                'total_barokah' => 45190450 + 262301000,
                'total_bri'     => 33043700 + 170180500,
                'total_bsi'     => 12146750 + 92120500,
                'bpjs'          => 24000,
                'listrik'       => 0,
                'kalender'      => 25000,
                'ditunaikan'    => 1350000,
            ],
            (object)[
                'id_lembaga'    => 22,
                'nama_lembaga'  => 'Bendahara',
                'total_barokah' => 70275750,
                'total_bri'     => 28424500,
                'total_bsi'     => 41851250,
                'bpjs'          => 0,
                'listrik'       => 127500,
                'kalender'      => 0,
                'ditunaikan'    => 1110000,
            ],
            (object)[
                'id_lembaga'    => 10,
                'nama_lembaga'  => 'Fakultas Tarbiyah',
                'total_barokah' => 12303135 + 44400000,
                'total_bri'     => 10633135 + 32100000,
                'total_bsi'     => 1670000 + 12300000,
                'bpjs'          => 1300000,
                'listrik'       => 950000,
                'kalender'      => 25000,
                'ditunaikan'    => 575000,
            ],
        ]);
    }
}
