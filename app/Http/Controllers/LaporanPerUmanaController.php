<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanPerUmanaController extends Controller
{
    /**
     * Halaman Utama: Daftar Barokah Per Umana (Opsi A: Berbasis Periode & Lembaga)
     */
    public function index(Request $request)
    {
        // 1. Ambil daftar tahun unik dari ketiga tabel barokah
        $tahunT1 = DB::table('total_barokah')->whereNotNull('tahun')->where('tahun', '!=', '')->distinct()->pluck('tahun');
        $tahunT2 = DB::table('total_barokah_pengajar')->whereNotNull('tahun')->where('tahun', '!=', '')->distinct()->pluck('tahun');
        $tahunT3 = DB::table('total_barokah_satpam')->whereNotNull('tahun')->where('tahun', '!=', '')->distinct()->pluck('tahun');

        $daftarTahun = $tahunT1->concat($tahunT2)->concat($tahunT3)->unique()->values();
        $daftarTahun = $daftarTahun->sort(function ($a, $b) {
            return $this->getPeriodSortKey('Desember', $b) <=> $this->getPeriodSortKey('Desember', $a);
        })->values();

        // 2. Daftar Bulan
        $daftarBulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        // 3. Daftar Lembaga
        $daftarLembaga = DB::table('lembaga')->orderBy('nama_lembaga', 'asc')->get(['id_lembaga', 'nama_lembaga']);

        // 4. Default Periode Terbaru
        $latest = DB::table('total_barokah')->orderBy('id_total_barokah', 'desc')->first(['bulan', 'tahun']);
        $defaultBulan = $latest ? $latest->bulan : 'Oktober';
        $defaultTahun = $latest ? $latest->tahun : ($daftarTahun->first() ?: date('Y'));

        $selectedBulan = $request->input('bulan', $defaultBulan);
        $selectedTahun = $request->input('tahun', $defaultTahun);
        $selectedLembaga = $request->input('id_lembaga', '');

        return view('laporan.per-umana.index', compact(
            'daftarTahun',
            'daftarBulan',
            'daftarLembaga',
            'selectedBulan',
            'selectedTahun',
            'selectedLembaga'
        ));
    }

    /**
     * DataTables Server-Side / JSON untuk Daftar Barokah Per Umana (Periode Terpilih)
     */
    public function data(Request $request)
    {
        $latest = DB::table('total_barokah')->orderBy('id_total_barokah', 'desc')->first(['bulan', 'tahun']);
        $defaultBulan = $latest ? $latest->bulan : 'Oktober';
        $defaultTahun = $latest ? $latest->tahun : date('Y');

        $bulan = $request->input('bulan', $defaultBulan);
        $tahun = $request->input('tahun', $defaultTahun);
        $idLembaga = $request->input('id_lembaga');

        $cleanTahun = trim(preg_replace('/tahun/i', '', (string)$tahun));

        // 1. Query Struktural
        $q1 = DB::table('total_barokah as tb')
            ->join('penempatan as p', 'tb.id_penempatan', '=', 'p.id_penempatan')
            ->join('lembaga as l', 'p.id_lembaga', '=', 'l.id_lembaga')
            ->where('tb.bulan', $bulan)
            ->where(function ($q) use ($tahun, $cleanTahun) {
                $q->where('tb.tahun', $tahun)
                  ->orWhere('tb.tahun', $cleanTahun)
                  ->orWhere('tb.tahun', 'Tahun ' . $cleanTahun);
            });

        if ($idLembaga) {
            $q1->where('p.id_lembaga', $idLembaga);
        }

        $q1->select(
            'p.nik',
            'l.id_lembaga',
            'l.nama_lembaga',
            DB::raw("'Struktural' as kategori"),
            'tb.diterima as nominal_struktural',
            DB::raw('0 as nominal_pengajar'),
            DB::raw('0 as nominal_satpam'),
            'tb.diterima as nominal'
        );

        // 2. Query Pengajar
        $q2 = DB::table('total_barokah_pengajar as tbp')
            ->join('pengajar as p', 'tbp.id_pengajar', '=', 'p.id_pengajar')
            ->join('lembaga as l', 'p.id_lembaga', '=', 'l.id_lembaga')
            ->where('tbp.bulan', $bulan)
            ->where(function ($q) use ($tahun, $cleanTahun) {
                $q->where('tbp.tahun', $tahun)
                  ->orWhere('tbp.tahun', $cleanTahun)
                  ->orWhere('tbp.tahun', 'Tahun ' . $cleanTahun);
            });

        if ($idLembaga) {
            $q2->where('p.id_lembaga', $idLembaga);
        }

        $q2->select(
            'p.nik',
            'l.id_lembaga',
            'l.nama_lembaga',
            DB::raw("'Pengajar' as kategori"),
            DB::raw('0 as nominal_struktural'),
            'tbp.diterima as nominal_pengajar',
            DB::raw('0 as nominal_satpam'),
            'tbp.diterima as nominal'
        );

        // 3. Query Satpam
        $q3 = DB::table('total_barokah_satpam as tbs')
            ->join('satpam as s', 'tbs.id_satpam', '=', 's.id_satpam')
            ->leftJoin('kehadiran_lembaga as kl', 'kl.id_kehadiran_lembaga', '=', 'tbs.id_kehadiran_lembaga')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'kl.id_lembaga')
            ->where('tbs.bulan', $bulan)
            ->where(function ($q) use ($tahun, $cleanTahun) {
                $q->where('tbs.tahun', $tahun)
                  ->orWhere('tbs.tahun', $cleanTahun)
                  ->orWhere('tbs.tahun', 'Tahun ' . $cleanTahun);
            });

        if ($idLembaga) {
            $q3->where('l.id_lembaga', $idLembaga);
        }

        $q3->select(
            's.nik',
            DB::raw('COALESCE(l.id_lembaga, 0) as id_lembaga'),
            DB::raw("COALESCE(l.nama_lembaga, 'Satpam') as nama_lembaga"),
            DB::raw("'Satpam' as kategori"),
            DB::raw('0 as nominal_struktural'),
            DB::raw('0 as nominal_pengajar'),
            'tbs.diterima as nominal_satpam',
            'tbs.diterima as nominal'
        );

        $union = $q1->unionAll($q2)->unionAll($q3);

        $mainQuery = DB::query()->fromSub($union, 'g')
            ->join('umana as u', 'g.nik', '=', 'u.nik')
            ->select(
                'g.nik',
                'u.nama_lengkap',
                'u.gelar_depan',
                'u.gelar_belakang',
                'u.nomor_hp',
                'u.alamat_domisili',
                DB::raw('GROUP_CONCAT(DISTINCT g.nama_lembaga ORDER BY g.nama_lembaga SEPARATOR ", ") as daftar_lembaga'),
                DB::raw('GROUP_CONCAT(DISTINCT g.kategori ORDER BY g.kategori SEPARATOR ", ") as daftar_kategori'),
                DB::raw('SUM(g.nominal_struktural) as total_struktural'),
                DB::raw('SUM(g.nominal_pengajar) as total_pengajar'),
                DB::raw('SUM(g.nominal_satpam) as total_satpam'),
                DB::raw('SUM(g.nominal) as grand_total')
            )
            ->groupBy('g.nik', 'u.nama_lengkap', 'u.gelar_depan', 'u.gelar_belakang', 'u.nomor_hp', 'u.alamat_domisili');

        // Filter pencarian nama/nik
        if ($request->has('search') && !empty($request->input('search')['value'])) {
            $search = trim($request->input('search')['value']);
            $mainQuery->where(function ($q) use ($search) {
                $q->where('u.nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('g.nik', 'like', "%{$search}%")
                  ->orWhere('u.nomor_hp', 'like', "%{$search}%");
            });
        }

        $list = $mainQuery->orderBy('u.nama_lengkap', 'asc')->get();

        $summary = [
            'total_umana' => $list->count(),
            'grand_total_rp' => 'Rp ' . number_format($list->sum('grand_total'), 0, ',', '.'),
            'struktural_rp' => 'Rp ' . number_format($list->sum('total_struktural'), 0, ',', '.'),
            'pengajar_rp' => 'Rp ' . number_format($list->sum('total_pengajar'), 0, ',', '.'),
            'satpam_rp' => 'Rp ' . number_format($list->sum('total_satpam'), 0, ',', '.'),
        ];

        $data = [];
        $no = 1;

        foreach ($list as $item) {
            $nama = trim(($item->gelar_depan ? $item->gelar_depan . ' ' : '') . $item->nama_lengkap . ($item->gelar_belakang ? ', ' . $item->gelar_belakang : ''));

            $badgesKategori = '';
            $katArr = array_filter(array_map('trim', explode(',', $item->daftar_kategori)));
            foreach ($katArr as $k) {
                if ($k === 'Struktural') {
                    $badgesKategori .= '<span class="badge bg-success-light text-success me-1">Struktural</span>';
                } elseif ($k === 'Pengajar') {
                    $badgesKategori .= '<span class="badge bg-info-light text-info me-1">Pengajar</span>';
                } elseif ($k === 'Satpam') {
                    $badgesKategori .= '<span class="badge bg-warning-light text-warning me-1">Satpam</span>';
                }
            }

            $data[] = [
                'no' => $no++,
                'nama_lengkap' => '<div><strong class="text-dark">' . e($nama) . '</strong><br><small class="text-muted"><i class="fas fa-id-card me-1"></i>' . e($item->nik) . '</small></div>',
                'lembaga' => '<span class="text-secondary fw-semibold">' . e($item->daftar_lembaga ?: '-') . '</span>',
                'kategori' => $badgesKategori ?: '<span class="badge bg-light text-muted">-</span>',
                'periode' => '<span class="badge bg-light text-dark border px-2 py-1">' . e($bulan . ' ' . $cleanTahun) . '</span>',
                'grand_total' => '<span class="text-success fw-bold fs-14">Rp ' . number_format((float)$item->grand_total, 0, ',', '.') . '</span>',
                'aksi' => '<div class="d-flex gap-1 justify-content-center">
                    <a href="' . url('/laporan/per-umana/rincian/' . $item->nik) . '" class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-sm font-weight-bold" title="Lihat Riwayat Lengkap">
                        <i class="fas fa-list-alt me-1"></i> Rincian
                    </a>
                </div>'
            ];
        }

        return response()->json([
            'data' => $data,
            'summary' => $summary
        ]);
    }

    /**
     * Export Excel Daftar Barokah Per Umana (Berdasarkan Filter Periode & Lembaga)
     */
    public function exportExcel(Request $request)
    {
        $latest = DB::table('total_barokah')->orderBy('id_total_barokah', 'desc')->first(['bulan', 'tahun']);
        $defaultBulan = $latest ? $latest->bulan : 'Oktober';
        $defaultTahun = $latest ? $latest->tahun : date('Y');

        $bulan = $request->input('bulan', $defaultBulan);
        $tahun = $request->input('tahun', $defaultTahun);
        $idLembaga = $request->input('id_lembaga');
        $cleanTahun = trim(preg_replace('/tahun/i', '', (string)$tahun));

        $namaLembagaTerpilih = 'Semua Lembaga';
        if ($idLembaga) {
            $lem = DB::table('lembaga')->where('id_lembaga', $idLembaga)->first();
            if ($lem) {
                $namaLembagaTerpilih = $lem->nama_lembaga;
            }
        }

        // 1. Query Struktural
        $q1 = DB::table('total_barokah as tb')
            ->join('penempatan as p', 'tb.id_penempatan', '=', 'p.id_penempatan')
            ->join('lembaga as l', 'p.id_lembaga', '=', 'l.id_lembaga')
            ->where('tb.bulan', $bulan)
            ->where(function ($q) use ($tahun, $cleanTahun) {
                $q->where('tb.tahun', $tahun)
                  ->orWhere('tb.tahun', $cleanTahun)
                  ->orWhere('tb.tahun', 'Tahun ' . $cleanTahun);
            });
        if ($idLembaga) $q1->where('p.id_lembaga', $idLembaga);
        $q1->select('p.nik', 'l.id_lembaga', 'l.nama_lembaga', DB::raw("'Struktural' as kategori"), 'tb.diterima as nominal_struktural', DB::raw('0 as nominal_pengajar'), DB::raw('0 as nominal_satpam'), 'tb.diterima as nominal');

        // 2. Query Pengajar
        $q2 = DB::table('total_barokah_pengajar as tbp')
            ->join('pengajar as p', 'tbp.id_pengajar', '=', 'p.id_pengajar')
            ->join('lembaga as l', 'p.id_lembaga', '=', 'l.id_lembaga')
            ->where('tbp.bulan', $bulan)
            ->where(function ($q) use ($tahun, $cleanTahun) {
                $q->where('tbp.tahun', $tahun)
                  ->orWhere('tbp.tahun', $cleanTahun)
                  ->orWhere('tbp.tahun', 'Tahun ' . $cleanTahun);
            });
        if ($idLembaga) $q2->where('p.id_lembaga', $idLembaga);
        $q2->select('p.nik', 'l.id_lembaga', 'l.nama_lembaga', DB::raw("'Pengajar' as kategori"), DB::raw('0 as nominal_struktural'), 'tbp.diterima as nominal_pengajar', DB::raw('0 as nominal_satpam'), 'tbp.diterima as nominal');

        // 3. Query Satpam
        $q3 = DB::table('total_barokah_satpam as tbs')
            ->join('satpam as s', 'tbs.id_satpam', '=', 's.id_satpam')
            ->leftJoin('kehadiran_lembaga as kl', 'kl.id_kehadiran_lembaga', '=', 'tbs.id_kehadiran_lembaga')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'kl.id_lembaga')
            ->where('tbs.bulan', $bulan)
            ->where(function ($q) use ($tahun, $cleanTahun) {
                $q->where('tbs.tahun', $tahun)
                  ->orWhere('tbs.tahun', $cleanTahun)
                  ->orWhere('tbs.tahun', 'Tahun ' . $cleanTahun);
            });
        if ($idLembaga) $q3->where('l.id_lembaga', $idLembaga);
        $q3->select('s.nik', DB::raw('COALESCE(l.id_lembaga, 0) as id_lembaga'), DB::raw("COALESCE(l.nama_lembaga, 'Satpam') as nama_lembaga"), DB::raw("'Satpam' as kategori"), DB::raw('0 as nominal_struktural'), DB::raw('0 as nominal_pengajar'), 'tbs.diterima as nominal_satpam', 'tbs.diterima as nominal');

        $union = $q1->unionAll($q2)->unionAll($q3);

        $mainQuery = DB::query()->fromSub($union, 'g')
            ->join('umana as u', 'g.nik', '=', 'u.nik')
            ->select(
                'g.nik',
                'u.nama_lengkap',
                'u.gelar_depan',
                'u.gelar_belakang',
                'u.nomor_hp',
                DB::raw('GROUP_CONCAT(DISTINCT g.nama_lembaga ORDER BY g.nama_lembaga SEPARATOR ", ") as daftar_lembaga'),
                DB::raw('GROUP_CONCAT(DISTINCT g.kategori ORDER BY g.kategori SEPARATOR ", ") as daftar_kategori'),
                DB::raw('SUM(g.nominal_struktural) as total_struktural'),
                DB::raw('SUM(g.nominal_pengajar) as total_pengajar'),
                DB::raw('SUM(g.nominal_satpam) as total_satpam'),
                DB::raw('SUM(g.nominal) as grand_total')
            )
            ->groupBy('g.nik', 'u.nama_lengkap', 'u.gelar_depan', 'u.gelar_belakang', 'u.nomor_hp');

        $list = $mainQuery->orderBy('u.nama_lengkap', 'asc')->get();

        $periodeStr = $bulan . ' ' . $cleanTahun;
        $cleanFilename = preg_replace('/[^a-zA-Z0-9_\-]/', '_', "Laporan_Barokah_Per_Umana_{$periodeStr}");

        return response()->view('laporan.per-umana.excel', compact(
            'list',
            'bulan',
            'cleanTahun',
            'periodeStr',
            'namaLembagaTerpilih'
        ), 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"{$cleanFilename}.xls\"",
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }

    /**
     * Halaman Rincian Riwayat Barokah Umana
     */
    public function rincian($nik)
    {
        $umana = DB::table('umana')->where('nik', $nik)->first();
        if (!$umana) {
            return redirect('/laporan/per-umana')->with('error', 'Data Umana tidak ditemukan');
        }

        // 1. Profil Info Keaktifan
        $strukturalList = DB::table('penempatan')
            ->join('lembaga', 'penempatan.id_lembaga', '=', 'lembaga.id_lembaga')
            ->where('penempatan.nik', $nik)
            ->distinct()
            ->pluck('lembaga.nama_lembaga')
            ->toArray();

        $pengajarList = DB::table('pengajar')
            ->join('lembaga', 'pengajar.id_lembaga', '=', 'lembaga.id_lembaga')
            ->where('pengajar.nik', $nik)
            ->distinct()
            ->pluck('lembaga.nama_lembaga')
            ->toArray();

        $satpamCount = DB::table('satpam')->where('nik', $nik)->count();

        $umanaInfo = [
            'nik' => $umana->nik,
            'nama_lengkap' => trim(($umana->gelar_depan ? $umana->gelar_depan . ' ' : '') . $umana->nama_lengkap . ($umana->gelar_belakang ? ', ' . $umana->gelar_belakang : '')),
            'struktural' => !empty($strukturalList) ? implode(', ', $strukturalList) : 'Tidak Aktif',
            'pengajar' => !empty($pengajarList) ? implode(', ', $pengajarList) : 'Tidak Aktif',
            'satpam' => $satpamCount > 0 ? 'Aktif' : 'Tidak Aktif',
        ];

        // 2. Rekapitulasi Gabungan Bulanan (Union All)
        $qStruktural = DB::table('total_barokah as tb')
            ->join('penempatan as p', 'tb.id_penempatan', '=', 'p.id_penempatan')
            ->join('lembaga as l', 'p.id_lembaga', '=', 'l.id_lembaga')
            ->where('p.nik', $nik)
            ->select(
                'tb.bulan',
                'tb.tahun',
                'tb.diterima as nominal_struktural',
                DB::raw('0 as nominal_pengajar'),
                DB::raw('0 as nominal_satpam'),
                'l.nama_lembaga'
            );

        $qPengajar = DB::table('total_barokah_pengajar as tbp')
            ->join('pengajar as p', 'tbp.id_pengajar', '=', 'p.id_pengajar')
            ->join('lembaga as l', 'p.id_lembaga', '=', 'l.id_lembaga')
            ->where('p.nik', $nik)
            ->select(
                'tbp.bulan',
                'tbp.tahun',
                DB::raw('0 as nominal_struktural'),
                'tbp.diterima as nominal_pengajar',
                DB::raw('0 as nominal_satpam'),
                'l.nama_lembaga'
            );

        $qSatpam = DB::table('total_barokah_satpam as tbs')
            ->join('satpam as s', 'tbs.id_satpam', '=', 's.id_satpam')
            ->where('s.nik', $nik)
            ->select(
                'tbs.bulan',
                'tbs.tahun',
                DB::raw('0 as nominal_struktural'),
                DB::raw('0 as nominal_pengajar'),
                'tbs.diterima as nominal_satpam',
                DB::raw("'Satpam' as nama_lembaga")
            );

        $unionQuery = $qStruktural->unionAll($qPengajar)->unionAll($qSatpam);

        $rekapRaw = DB::query()->fromSub($unionQuery, 'gabungan')
            ->select(
                'bulan',
                'tahun',
                DB::raw('GROUP_CONCAT(DISTINCT nama_lembaga SEPARATOR ", ") as daftar_lembaga'),
                DB::raw('SUM(nominal_struktural) as total_struktural'),
                DB::raw('SUM(nominal_pengajar) as total_pengajar'),
                DB::raw('SUM(nominal_satpam) as total_satpam'),
                DB::raw('SUM(nominal_struktural + nominal_pengajar + nominal_satpam) as grand_total')
            )
            ->groupBy('tahun', 'bulan')
            ->get();

        $rekap = $rekapRaw->sortByDesc(fn($item) => $this->getPeriodSortKey($item->bulan, $item->tahun))->values();

        // 3. Riwayat Khusus Struktural (Data Lengkap)
        $struktural = DB::table('total_barokah as tb')
            ->join('penempatan as p', 'tb.id_penempatan', '=', 'p.id_penempatan')
            ->join('lembaga as l', 'p.id_lembaga', '=', 'l.id_lembaga')
            ->leftJoin('ketentuan_barokah as kb', 'p.id_ketentuan', '=', 'kb.id_ketentuan')
            ->leftJoin('kehadiran as k', function ($join) {
                $join->on('k.id_penempatan', '=', 'tb.id_penempatan')
                     ->on('k.bulan', '=', 'tb.bulan')
                     ->on('k.tahun', '=', 'tb.tahun');
            })
            ->where('p.nik', $nik)
            ->select(
                'tb.id_total_barokah',
                'tb.bulan',
                'tb.tahun',
                'l.nama_lembaga',
                'kb.nama_jabatan',
                'tb.tunjab',
                'tb.mp',
                DB::raw('MAX(COALESCE(k.jumlah_hadir, 0)) as jumlah_hadir'),
                'tb.nominal_kehadiran',
                'tb.tunkel',
                'tb.tunj_anak',
                'tb.tbk',
                'tb.tmp',
                'tb.kehormatan',
                'tb.potongan',
                'tb.diterima'
            )
            ->groupBy(
                'tb.id_total_barokah', 'tb.bulan', 'tb.tahun', 'l.nama_lembaga',
                'kb.nama_jabatan', 'tb.tunjab', 'tb.mp',
                'tb.nominal_kehadiran', 'tb.tunkel', 'tb.tunj_anak', 'tb.tbk',
                'tb.tmp', 'tb.kehormatan', 'tb.potongan', 'tb.diterima'
            )
            ->get()
            ->sortByDesc(fn($item) => $this->getPeriodSortKey($item->bulan, $item->tahun))
            ->values();

        // 4. Riwayat Khusus Pengajar (Data Lengkap)
        $pengajar = DB::table('total_barokah_pengajar as tbp')
            ->join('pengajar as p', 'tbp.id_pengajar', '=', 'p.id_pengajar')
            ->join('lembaga as l', 'p.id_lembaga', '=', 'l.id_lembaga')
            ->where('p.nik', $nik)
            ->select(
                'tbp.id_total_barokah_pengajar',
                'tbp.bulan',
                'tbp.tahun',
                'l.nama_lembaga',
                'p.kategori as nama_jabatan',
                'tbp.jumlah_sks',
                'tbp.mengajar',
                'tbp.jumlah_hadir',
                'tbp.nominal_kehadiran',
                'tbp.jumlah_hadir_piket',
                'tbp.barokah_piket',
                'tbp.tunkel',
                'tbp.tun_anak as tunj_anak',
                'tbp.kehormatan',
                'tbp.walkes',
                'tbp.khusus as tambahan',
                'tbp.potongan',
                'tbp.diterima'
            )
            ->get()
            ->sortByDesc(fn($item) => $this->getPeriodSortKey($item->bulan, $item->tahun))
            ->values();

        // 5. Riwayat Khusus Satpam (Data Lengkap)
        $satpam = DB::table('total_barokah_satpam as tbs')
            ->join('satpam as s', 'tbs.id_satpam', '=', 's.id_satpam')
            ->leftJoin('kehadiran_lembaga as kl', 'kl.id_kehadiran_lembaga', '=', 'tbs.id_kehadiran_lembaga')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'kl.id_lembaga')
            ->where('s.nik', $nik)
            ->select(
                'tbs.id_total_barokah_satpam',
                'tbs.bulan',
                'tbs.tahun',
                DB::raw('COALESCE(l.nama_lembaga, "Satpam") as nama_lembaga'),
                'tbs.jumlah_hari as jumlah_hadir',
                'tbs.nominal_transport',
                'tbs.jumlah_shift',
                'tbs.rank as nominal_shift',
                'tbs.jumlah_dinihari',
                'tbs.konsumsi as nominal_dinihari',
                'tbs.potongan',
                'tbs.diterima'
            )
            ->get()
            ->sortByDesc(fn($item) => $this->getPeriodSortKey($item->bulan, $item->tahun))
            ->values();

        return view('laporan.per-umana.rincian', compact(
            'umanaInfo',
            'rekap',
            'struktural',
            'pengajar',
            'satpam',
            'nik'
        ));
    }

    /**
     * Breakdown Komponen Rincian Barokah (AJAX Modal)
     */
    public function breakdown(Request $request)
    {
        $nik = $request->query('nik');
        $jenis = strtolower($request->query('k', 'struktural'));
        $bulan = $request->query('bulan');
        $tahun = $request->query('tahun');
        $cleanTahun = trim(preg_replace('/tahun/i', '', (string)$tahun));
        $idLembaga = $request->query('id_lembaga');

        if (!$nik || !$bulan || !$tahun) {
            return response()->json(['ok' => false, 'message' => 'Parameter tidak lengkap']);
        }

        if ($jenis === 'struktural') {
            $query = DB::table('total_barokah as tb')
                ->join('penempatan as p', 'tb.id_penempatan', '=', 'p.id_penempatan')
                ->where('p.nik', $nik)
                ->where('tb.bulan', $bulan)
                ->where(function ($q) use ($tahun, $cleanTahun) {
                    $q->where('tb.tahun', $tahun)
                      ->orWhere('tb.tahun', $cleanTahun)
                      ->orWhere('tb.tahun', 'Tahun ' . $cleanTahun);
                });

            if ($idLembaga) {
                $query->where('p.id_lembaga', $idLembaga);
            }

            $row = $query->select(
                DB::raw('COALESCE(SUM(tb.barokah),0) as barokah_pokok'),
                DB::raw('COALESCE(SUM(tb.nominal_kehadiran),0) as kehadiran_nominal'),
                DB::raw('COALESCE(SUM(tb.tunkel),0) as tunkel'),
                DB::raw('COALESCE(SUM(tb.tunj_anak),0) as tun_anak'),
                DB::raw('COALESCE(SUM(tb.tmp),0) as tmp'),
                DB::raw('COALESCE(SUM(tb.tbk),0) as tbk'),
                DB::raw('COALESCE(SUM(tb.kehormatan),0) as kehormatan'),
                DB::raw('COALESCE(SUM(tb.potongan),0) as potongan'),
                DB::raw('COALESCE(SUM(tb.diterima),0) as total')
            )->first();

            // Hadir normal (kali)
            $qHadir = DB::table('kehadiran as k')
                ->join('penempatan as p', 'k.id_penempatan', '=', 'p.id_penempatan')
                ->join('kehadiran_lembaga as kl', 'k.id_kehadi', '=', 'kl.id_kehadiran_lembaga')
                ->where('p.nik', $nik)
                ->where('kl.bulan', $bulan)
                ->where(function ($q) use ($tahun, $cleanTahun) {
                    $q->where('kl.tahun', $tahun)
                      ->orWhere('kl.tahun', $cleanTahun)
                      ->orWhere('kl.tahun', 'Tahun ' . $cleanTahun);
                });
            if ($idLembaga) {
                $qHadir->where('p.id_lembaga', $idLembaga);
            }
            $jhadir = (int)($qHadir->sum('k.jumlah_hadir') ?? 0);

            // Item Potongan
            $potonganItems = DB::table('potongan_umana as pu')
                ->join('potongan as p', 'p.id_potongan', '=', 'pu.jenis_potongan')
                ->join('penempatan as pen', 'pen.id_penempatan', '=', 'pu.id_penempatan')
                ->where('pen.nik', $nik)
                ->select('p.nama_potongan as nama', DB::raw('SUM(pu.nominal_potongan) as nominal'))
                ->groupBy('p.nama_potongan')
                ->get();

            return response()->json([
                'ok' => true,
                'data' => [
                    'kehadiran' => $jhadir,
                    'f' => [
                        'barokah_pokok' => (float)($row->barokah_pokok ?? 0),
                        'kehadiran_nominal' => (float)($row->kehadiran_nominal ?? 0),
                        'tunkel' => (float)($row->tunkel ?? 0),
                        'tun_anak' => (float)($row->tun_anak ?? 0),
                        'tmp' => (float)($row->tmp ?? 0),
                        'tbk' => (float)($row->tbk ?? 0),
                        'kehormatan' => (float)($row->kehormatan ?? 0),
                        'potongan' => (float)($row->potongan ?? 0),
                        'total' => (float)($row->total ?? 0),
                    ],
                    'potongan_items' => $potonganItems
                ]
            ]);
        } elseif ($jenis === 'pengajar') {
            $query = DB::table('total_barokah_pengajar as tbp')
                ->join('pengajar as p', 'tbp.id_pengajar', '=', 'p.id_pengajar')
                ->where('p.nik', $nik)
                ->where('tbp.bulan', $bulan)
                ->where(function ($q) use ($tahun, $cleanTahun) {
                    $q->where('tbp.tahun', $tahun)
                      ->orWhere('tbp.tahun', $cleanTahun)
                      ->orWhere('tbp.tahun', 'Tahun ' . $cleanTahun);
                });

            if ($idLembaga) {
                $query->where('p.id_lembaga', $idLembaga);
            }

            $row = $query->select(
                DB::raw('COALESCE(SUM(tbp.jumlah_sks),0) as sks'),
                DB::raw('COALESCE(SUM(tbp.mengajar),0) as mengajar'),
                DB::raw('COALESCE(SUM(tbp.dty),0) as gty_dty'),
                DB::raw('COALESCE(SUM(tbp.jafung),0) as jafung'),
                DB::raw('COALESCE(SUM(tbp.nominal_kehadiran),0) as kehadiran'),
                DB::raw('COALESCE(SUM(tbp.jumlah_hadir),0) as jhadir'),
                DB::raw('COALESCE(SUM(tbp.jumlah_hadir_15),0) as jhadir15'),
                DB::raw('COALESCE(SUM(tbp.nominal_hadir_15),0) as nhadir15'),
                DB::raw('COALESCE(SUM(tbp.jumlah_hadir_10),0) as jhadir10'),
                DB::raw('COALESCE(SUM(tbp.nominal_hadir_10),0) as nhadir10'),
                DB::raw('COALESCE(SUM(tbp.walkes),0) as walkes'),
                DB::raw('COALESCE(SUM(tbp.kehormatan),0) as kehormatan'),
                DB::raw('COALESCE(SUM(tbp.khusus),0) as tambahan'),
                DB::raw('COALESCE(SUM(tbp.barokah_piket),0) as piket'),
                DB::raw('COALESCE(SUM(tbp.tun_anak),0) as tun_anak'),
                DB::raw('COALESCE(SUM(tbp.tunkel),0) as tunkel'),
                DB::raw('COALESCE(SUM(tbp.potongan),0) as potongan'),
                DB::raw('COALESCE(SUM(tbp.diterima),0) as total')
            )->first();

            $perSks = (!empty($row->sks) && (int)$row->sks > 0) ? round(((int)$row->mengajar) / (int)$row->sks) : 0;

            $potonganItems = DB::table('potongan_pengajar as pp')
                ->join('potongan as p', 'p.id_potongan', '=', 'pp.jenis_potongan')
                ->join('pengajar as pg', 'pg.id_pengajar', '=', 'pp.id_pengajar')
                ->where('pg.nik', $nik)
                ->select('p.nama_potongan as nama', DB::raw('SUM(pp.nominal_potongan) as nominal'))
                ->groupBy('p.nama_potongan')
                ->get();

            return response()->json([
                'ok' => true,
                'data' => [
                    'sks' => (int)($row->sks ?? 0),
                    'jhadir' => (int)($row->jhadir ?? 0),
                    'jhadir15' => (int)($row->jhadir15 ?? 0),
                    'jhadir10' => (int)($row->jhadir10 ?? 0),
                    'f' => [
                        'per_sks' => $perSks,
                        'mengajar' => (float)($row->mengajar ?? 0),
                        'gty_dty' => (float)($row->gty_dty ?? 0),
                        'jafung' => (float)($row->jafung ?? 0),
                        'kehadiran' => (float)($row->kehadiran ?? 0),
                        'nhadir15' => (float)($row->nhadir15 ?? 0),
                        'nhadir10' => (float)($row->nhadir10 ?? 0),
                        'walkes' => (float)($row->walkes ?? 0),
                        'kehormatan' => (float)($row->kehormatan ?? 0),
                        'tambahan' => (float)($row->tambahan ?? 0),
                        'piket' => (float)($row->piket ?? 0),
                        'tun_anak' => (float)($row->tun_anak ?? 0),
                        'tunkel' => (float)($row->tunkel ?? 0),
                        'potongan' => (float)($row->potongan ?? 0),
                        'total' => (float)($row->total ?? 0),
                    ],
                    'potongan_items' => $potonganItems
                ]
            ]);
        } elseif ($jenis === 'satpam') {
            $row = DB::table('total_barokah_satpam as tbs')
                ->join('satpam as s', 'tbs.id_satpam', '=', 's.id_satpam')
                ->where('s.nik', $nik)
                ->where('tbs.bulan', $bulan)
                ->where(function ($q) use ($tahun, $cleanTahun) {
                    $q->where('tbs.tahun', $tahun)
                      ->orWhere('tbs.tahun', $cleanTahun)
                      ->orWhere('tbs.tahun', 'Tahun ' . $cleanTahun);
                })
                ->select(
                    DB::raw('COALESCE(SUM(tbs.jumlah_hari),0) as j_hari'),
                    DB::raw('COALESCE(SUM(tbs.nominal_transport * tbs.jumlah_hari),0) as k1_total'),
                    DB::raw('COALESCE(SUM(tbs.jumlah_shift),0) as j_shift'),
                    DB::raw('COALESCE(SUM(tbs.rank * tbs.jumlah_shift),0) as k2_total'),
                    DB::raw('COALESCE(SUM(tbs.jumlah_dinihari),0) as j_dinihari'),
                    DB::raw('COALESCE(SUM(tbs.konsumsi * tbs.jumlah_dinihari),0) as k3_total'),
                    DB::raw('COALESCE(SUM(tbs.potongan),0) as potongan'),
                    DB::raw('COALESCE(SUM(tbs.diterima),0) as total')
                )->first();

            $k1Unit = (!empty($row->j_hari) && (int)$row->j_hari > 0) ? round((int)$row->k1_total / (int)$row->j_hari) : 0;
            $k2Unit = (!empty($row->j_shift) && (int)$row->j_shift > 0) ? round((int)$row->k2_total / (int)$row->j_shift) : 0;
            $k3Unit = (!empty($row->j_dinihari) && (int)$row->j_dinihari > 0) ? round((int)$row->k3_total / (int)$row->j_dinihari) : 0;

            return response()->json([
                'ok' => true,
                'data' => [
                    'j_hari' => (int)($row->j_hari ?? 0),
                    'j_shift' => (int)($row->j_shift ?? 0),
                    'j_dinihari' => (int)($row->j_dinihari ?? 0),
                    'k1_unit' => $k1Unit,
                    'k2_unit' => $k2Unit,
                    'k3_unit' => $k3Unit,
                    'f' => [
                        'k1_total' => (float)($row->k1_total ?? 0),
                        'k2_total' => (float)($row->k2_total ?? 0),
                        'k3_total' => (float)($row->k3_total ?? 0),
                        'potongan' => (float)($row->potongan ?? 0),
                        'total' => (float)($row->total ?? 0),
                    ],
                    'potongan_items' => []
                ]
            ]);
        }

        return response()->json(['ok' => false, 'message' => 'Kategori tidak valid']);
    }

    /**
     * Helper untuk menghasilkan sort key kronologis (terbaru ke terlama)
     * Format tahun bisa berupa tahun tunggal (2026) atau tahun ajaran (2024/2025).
     */
    protected function getPeriodSortKey($bulan, $tahun)
    {
        $monthMap = [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
            'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
            'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12
        ];

        $b = strtolower(trim((string)$bulan));
        $m = $monthMap[$b] ?? 0;
        $cleanTahun = trim(preg_replace('/tahun/i', '', (string)$tahun));
        preg_match_all('/\d{4}/', $cleanTahun, $matches);
        $years = $matches[0] ?? [];

        if (count($years) >= 2) {
            $y1 = (int)$years[0];
            $y2 = (int)$years[1];
            if ($m >= 7) {
                // Semester ganjil (Juli - Desember) masuk ke tahun pertama
                $calYear = $y1;
            } elseif ($m === 6) {
                // Kasus transisi Juni pada tahun ajaran
                $calYear = ($y1 === 2025) ? $y1 : $y2;
                return sprintf('%04d%02d_%d', $calYear, $m, ($y1 === 2025 ? 2 : 1));
            } else {
                // Semester genap (Januari - Mei) masuk ke tahun kedua
                $calYear = $y2;
            }
        } elseif (count($years) === 1) {
            $calYear = (int)$years[0];
        } else {
            $calYear = 2000;
        }

        return sprintf('%04d%02d_0', $calYear, $m);
    }
}
