<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanPerUmanaController extends Controller
{
    /**
     * Halaman Utama: Daftar Barokah Per Umana
     */
    public function index()
    {
        return view('laporan.per-umana.index');
    }

    /**
     * DataTables Server-Side / JSON untuk Daftar Umana
     */
    public function data(Request $request)
    {
        $query = DB::table('umana')
            ->select('umana.nik', 'umana.nama_lengkap', 'umana.nomor_hp', 'umana.alamat_domisili')
            ->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('penempatan')
                    ->whereColumn('penempatan.nik', 'umana.nik');
            })
            ->orWhereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('pengajar')
                    ->whereColumn('pengajar.nik', 'umana.nik');
            })
            ->orWhereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('satpam')
                    ->whereColumn('satpam.nik', 'umana.nik');
            });

        // Filter pencarian global atau spesifik
        if ($request->has('search') && !empty($request->input('search')['value'])) {
            $search = $request->input('search')['value'];
            $query->where(function ($q) use ($search) {
                $q->where('umana.nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('umana.nomor_hp', 'like', "%{$search}%")
                  ->orWhere('umana.alamat_domisili', 'like', "%{$search}%");
            });
        }

        $list = $query->orderBy('umana.nama_lengkap', 'asc')->get();

        $data = [];
        $no = 1;

        foreach ($list as $item) {
            $data[] = [
                'no' => $no++,
                'nama_lengkap' => $item->nama_lengkap,
                'nomor_hp' => $item->nomor_hp ?: '-',
                'alamat_domisili' => $item->alamat_domisili ?: '-',
                'aksi' => '<a href="' . url('/laporan/per-umana/rincian/' . $item->nik) . '" class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-sm font-weight-bold" style="border-color: #ef4444; color: #ef4444;">
                    <i class="fas fa-list-alt me-1"></i> Rincian
                </a>'
            ];
        }

        return response()->json(['data' => $data]);
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

        $monthOrderSql = "CASE bulan 
            WHEN 'Januari' THEN 1 WHEN 'Februari' THEN 2 WHEN 'Maret' THEN 3
            WHEN 'April' THEN 4 WHEN 'Mei' THEN 5 WHEN 'Juni' THEN 6
            WHEN 'Juli' THEN 7 WHEN 'Agustus' THEN 8 WHEN 'September' THEN 9
            WHEN 'Oktober' THEN 10 WHEN 'November' THEN 11 WHEN 'Desember' THEN 12
            ELSE 0 END";

        $rekap = DB::query()->fromSub($unionQuery, 'gabungan')
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
            ->orderBy('tahun', 'desc')
            ->orderByRaw($monthOrderSql . ' DESC')
            ->get();

        // 3. Riwayat Khusus Struktural (Data Lengkap)
        $struktural = DB::table('total_barokah as tb')
            ->join('penempatan as p', 'tb.id_penempatan', '=', 'p.id_penempatan')
            ->join('lembaga as l', 'p.id_lembaga', '=', 'l.id_lembaga')
            ->leftJoin('ketentuan_barokah as kb', 'p.id_ketentuan', '=', 'kb.id_ketentuan')
            ->leftJoin('kehadiran as k', function ($join) {
                $join->on('k.id_penempatan', '=', 'tb.id_penempatan');
            })
            ->leftJoin('kehadiran_lembaga as kl', function ($join) {
                $join->on('kl.id_kehadiran_lembaga', '=', 'k.id_kehadi')
                     ->on('kl.bulan', '=', 'tb.bulan')
                     ->on('kl.tahun', '=', 'tb.tahun');
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
                DB::raw('COALESCE(k.jumlah_hadir, 0) as jumlah_hadir'),
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
                'kb.nama_jabatan', 'tb.tunjab', 'tb.mp', 'k.jumlah_hadir',
                'tb.nominal_kehadiran', 'tb.tunkel', 'tb.tunj_anak', 'tb.tbk',
                'tb.tmp', 'tb.kehormatan', 'tb.potongan', 'tb.diterima'
            )
            ->orderBy('tb.tahun', 'desc')
            ->orderByRaw(str_replace('CASE bulan', 'CASE tb.bulan', $monthOrderSql) . ' DESC')
            ->get();

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
            ->orderBy('tbp.tahun', 'desc')
            ->orderByRaw(str_replace('CASE bulan', 'CASE tbp.bulan', $monthOrderSql) . ' DESC')
            ->get();

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
            ->orderBy('tbs.tahun', 'desc')
            ->orderByRaw(str_replace('CASE bulan', 'CASE tbs.bulan', $monthOrderSql) . ' DESC')
            ->get();

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
        $idLembaga = $request->query('id_lembaga');

        if (!$nik || !$bulan || !$tahun) {
            return response()->json(['ok' => false, 'message' => 'Parameter tidak lengkap']);
        }

        if ($jenis === 'struktural') {
            $query = DB::table('total_barokah as tb')
                ->join('penempatan as p', 'tb.id_penempatan', '=', 'p.id_penempatan')
                ->where('p.nik', $nik)
                ->where('tb.bulan', $bulan)
                ->where('tb.tahun', $tahun);

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
                ->where('kl.tahun', $tahun);
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
                ->where('tbp.tahun', $tahun);

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
                ->where('tbs.tahun', $tahun)
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
}
