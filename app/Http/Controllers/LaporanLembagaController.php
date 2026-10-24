<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanLembagaController extends Controller
{
    /**
     * Tampilkan halaman utama Laporan Barokah Per Lembaga
     */
    public function index()
    {
        // Ambil kombinasi unik lembaga dan kategori dari kehadiran_lembaga
        $lembagaList = DB::table('kehadiran_lembaga')
            ->join('lembaga', 'kehadiran_lembaga.id_lembaga', '=', 'lembaga.id_lembaga')
            ->select('kehadiran_lembaga.id_lembaga', 'lembaga.nama_lembaga', 'kehadiran_lembaga.kategori')
            ->whereNotNull('kehadiran_lembaga.kategori')
            ->where('kehadiran_lembaga.kategori', '!=', '')
            ->distinct()
            ->orderBy('lembaga.nama_lembaga', 'asc')
            ->orderBy('kehadiran_lembaga.kategori', 'asc')
            ->get();

        // Ambil daftar tahun dari kehadiran_lembaga
        $tahunList = DB::table('kehadiran_lembaga')
            ->select('tahun')
            ->whereNotNull('tahun')
            ->where('tahun', '!=', '')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        $bulanList = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        return view('laporan.per-lembaga.index', compact('lembagaList', 'tahunList', 'bulanList'));
    }

    /**
     * Ambil data JSON untuk DataTable Laporan Per Lembaga
     */
    public function data(Request $request)
    {
        $query = DB::table('kehadiran_lembaga')
            ->join('lembaga', 'kehadiran_lembaga.id_lembaga', '=', 'lembaga.id_lembaga')
            ->select(
                'kehadiran_lembaga.id_kehadiran_lembaga',
                'kehadiran_lembaga.id_lembaga',
                'lembaga.nama_lembaga',
                'kehadiran_lembaga.bulan',
                'kehadiran_lembaga.tahun',
                'kehadiran_lembaga.kategori',
                'kehadiran_lembaga.jumlah_total',
                'kehadiran_lembaga.status'
            );

        // Filter Lembaga
        if ($request->filled('filter_lembaga')) {
            if (strpos($request->filter_lembaga, '__') !== false) {
                [$idLem, $katLem] = explode('__', $request->filter_lembaga, 2);
                $query->where('kehadiran_lembaga.id_lembaga', $idLem);
                if (!empty($katLem)) {
                    $query->where('kehadiran_lembaga.kategori', $katLem);
                }
            } else {
                $query->where('kehadiran_lembaga.id_lembaga', $request->filter_lembaga);
            }
        }

        // Filter Lembaga Kategori (jika opsi lembaga memiliki data-kategori spesifik)
        if ($request->filled('filter_lembaga_kategori')) {
            $query->where('kehadiran_lembaga.kategori', $request->filter_lembaga_kategori);
        }

        // Filter Kategori
        if ($request->filled('filter_kategori') && $request->filter_kategori !== 'Semua Kategori' && $request->filter_kategori !== '') {
            $query->where('kehadiran_lembaga.kategori', $request->filter_kategori);
        }

        // Kamus normalisasi bulan (Inggris / Singkatan -> Indonesia)
        $monthMap = [
            'january' => 'Januari', 'february' => 'Februari', 'march' => 'Maret', 'april' => 'April',
            'may' => 'Mei', 'june' => 'Juni', 'july' => 'Juli', 'august' => 'Agustus',
            'september' => 'September', 'october' => 'Oktober', 'november' => 'November', 'december' => 'Desember',
            'jan' => 'Januari', 'feb' => 'Februari', 'mar' => 'Maret', 'apr' => 'April',
            'jun' => 'Juni', 'jul' => 'Juli', 'aug' => 'Agustus', 'sep' => 'September',
            'oct' => 'Oktober', 'nov' => 'November', 'dec' => 'Desember',
        ];

        $bulanFilter = null;
        $tahunFilter = null;

        if ($request->filled('filter_bulan')) {
            $bulanFilter = trim($request->filter_bulan);
        }
        if ($request->filled('filter_tahun')) {
            $tahunFilter = trim($request->filter_tahun);
        }

        // Jika filter_bulan belum diset tapi filter_periode dikirim (misal "March 2026" atau "Maret 2026")
        if (!$bulanFilter && $request->filled('filter_periode')) {
            $parts = explode(' ', trim($request->filter_periode));
            if (count($parts) >= 2) {
                $bulanFilter = $parts[0];
                if (!$tahunFilter) {
                    $tahunFilter = $parts[1];
                }
            } elseif (count($parts) === 1) {
                if (is_numeric($parts[0])) {
                    if (!$tahunFilter) {
                        $tahunFilter = $parts[0];
                    }
                } else {
                    $bulanFilter = $parts[0];
                }
            }
        }

        // Terapkan Filter Bulan dengan normalisasi bahasa
        if (!empty($bulanFilter)) {
            $lowerBulan = strtolower($bulanFilter);
            if (isset($monthMap[$lowerBulan])) {
                $bulanFilter = $monthMap[$lowerBulan];
            }
            $query->where('kehadiran_lembaga.bulan', $bulanFilter);
        }

        // Terapkan Filter Tahun
        if (!empty($tahunFilter)) {
            $query->where('kehadiran_lembaga.tahun', 'like', '%' . $tahunFilter . '%');
        }

        $items = $query->orderBy('kehadiran_lembaga.id_kehadiran_lembaga', 'asc')->get();

        $data = [];
        $no = 1;

        foreach ($items as $item) {
            $kategori = $item->kategori ?: 'Struktural';
            
            // Badge kategori dengan styling khas V2
            $badgeColor = '#d946ef'; // Magenta untuk Struktural
            if ($kategori === 'Pengajar') {
                $badgeColor = '#0284c7'; // Biru untuk Pengajar
            } elseif ($kategori === 'Satpam') {
                $badgeColor = '#ea580c'; // Oranye untuk Satpam
            }

            $kategoriBadge = '<span class="badge" style="background-color: ' . $badgeColor . '; color: #fff; border-radius: 12px; padding: 4px 12px; font-weight: 600; font-size: 11px;">' . e($kategori) . '</span>';
            $nominalFormatted = number_format((float)$item->jumlah_total, 0, ',', '.');
            $namaLembagaWithKategori = $item->nama_lembaga . ' - ' . $kategori;

            $data[] = [
                'no' => $no++,
                'id_kehadiran_lembaga' => $item->id_kehadiran_lembaga,
                'nama_lembaga' => $namaLembagaWithKategori,
                'nama_lembaga_murni' => $item->nama_lembaga,
                'bulan' => $item->bulan ?: '-',
                'tahun' => $item->tahun ?: '-',
                'kategori' => $kategori,
                'kategori_html' => $kategoriBadge,
                'nominal' => $nominalFormatted,
                'aksi' => '<a href="' . url('/laporan/per-lembaga/rincian/' . $item->id_kehadiran_lembaga) . '" class="btn btn-sm text-white px-3" style="background-color: #00a15d; border-radius: 20px; font-weight: 600; font-size: 12px;">
                    <i class="fas fa-list-alt me-1"></i> Detail
                </a>'
            ];
        }

        return response()->json(['data' => $data]);
    }

    /**
     * Halaman Rincian Laporan Barokah Lembaga
     */
    public function rincian($id)
    {
        $lembaga = DB::table('kehadiran_lembaga')
            ->join('lembaga', 'kehadiran_lembaga.id_lembaga', '=', 'lembaga.id_lembaga')
            ->where('kehadiran_lembaga.id_kehadiran_lembaga', $id)
            ->select('kehadiran_lembaga.*', 'lembaga.nama_lembaga')
            ->first();

        if (!$lembaga) {
            return redirect('/laporan/per-lembaga')->with('error', 'Data lembaga tidak ditemukan');
        }

        $kategori = $lembaga->kategori ?: 'Struktural';
        $rows = [];

        if ($kategori === 'Struktural') {
            $rows = DB::table('kehadiran')
                ->join('kehadiran_lembaga', 'kehadiran_lembaga.id_kehadiran_lembaga', '=', 'kehadiran.id_kehadi')
                ->join('penempatan', 'penempatan.id_penempatan', '=', 'kehadiran.id_penempatan')
                ->join('umana', 'penempatan.nik', '=', 'umana.nik')
                ->join('total_barokah', 'total_barokah.id_penempatan', '=', 'kehadiran.id_penempatan')
                ->join('ketentuan_barokah', 'penempatan.id_ketentuan', '=', 'ketentuan_barokah.id_ketentuan')
                ->join('lembaga', 'lembaga.id_lembaga', '=', 'penempatan.id_lembaga')
                ->where('kehadiran_lembaga.id_kehadiran_lembaga', $id)
                ->where('total_barokah.bulan', DB::raw('kehadiran_lembaga.bulan'))
                ->where('total_barokah.tahun', DB::raw('kehadiran_lembaga.tahun'))
                ->select(
                    'umana.gelar_depan',
                    'umana.gelar_belakang',
                    'umana.nama_lengkap',
                    'umana.nik',
                    'ketentuan_barokah.nama_jabatan',
                    'umana.tmt_struktural',
                    'ketentuan_barokah.barokah',
                    'total_barokah.mp',
                    'kehadiran.jumlah_hadir',
                    'total_barokah.nominal_kehadiran',
                    'total_barokah.tunkel',
                    'total_barokah.tunj_anak',
                    'total_barokah.tbk',
                    'total_barokah.tmp',
                    'total_barokah.kehormatan',
                    'total_barokah.potongan',
                    'total_barokah.diterima'
                )
                ->groupBy('umana.nik', 'umana.gelar_depan', 'umana.gelar_belakang', 'umana.nama_lengkap', 'ketentuan_barokah.nama_jabatan', 'umana.tmt_struktural', 'ketentuan_barokah.barokah', 'total_barokah.mp', 'kehadiran.jumlah_hadir', 'total_barokah.nominal_kehadiran', 'total_barokah.tunkel', 'total_barokah.tunj_anak', 'total_barokah.tbk', 'total_barokah.tmp', 'total_barokah.kehormatan', 'total_barokah.potongan', 'total_barokah.diterima', 'ketentuan_barokah.id_ketentuan')
                ->orderBy('ketentuan_barokah.id_ketentuan', 'asc')
                ->get();
        } elseif ($kategori === 'Pengajar') {
            $rows = DB::table('kehadiran_lembaga as kl')
                ->join('kehadiran_pengajar as kp', 'kl.id_kehadiran_lembaga', '=', 'kp.id_kehadiran_lembaga')
                ->join('pengajar as p', 'kp.id_pengajar', '=', 'p.id_pengajar')
                ->join('umana as u', 'p.nik', '=', 'u.nik')
                ->join('lembaga as l', 'p.id_lembaga', '=', 'l.id_lembaga')
                ->join('total_barokah_pengajar as tbp', function($join) {
                    $join->on('tbp.id_pengajar', '=', 'kp.id_pengajar')
                         ->on('tbp.bulan', '=', 'kl.bulan')
                         ->on('tbp.tahun', '=', 'kl.tahun');
                })
                ->where('kl.id_kehadiran_lembaga', $id)
                ->select(
                    'u.gelar_depan',
                    'u.gelar_belakang',
                    'u.nama_lengkap',
                    'u.nik',
                    'p.kategori as nama_jabatan',
                    'tbp.jumlah_sks',
                    'tbp.mengajar',
                    'tbp.mp',
                    'tbp.dty',
                    'tbp.jafung',
                    'tbp.jumlah_hadir',
                    'tbp.nominal_kehadiran',
                    'tbp.jumlah_hadir_15',
                    'tbp.nominal_hadir_15',
                    'tbp.jumlah_hadir_10',
                    'tbp.nominal_hadir_10',
                    'tbp.jumlah_hadir_piket',
                    'tbp.barokah_piket',
                    'tbp.tunkel',
                    'tbp.tun_anak as tunj_anak',
                    'tbp.kehormatan',
                    'tbp.walkes',
                    'tbp.khusus',
                    'tbp.potongan',
                    'tbp.diterima'
                )
                ->orderBy('u.nama_lengkap', 'asc')
                ->get();
        } elseif ($kategori === 'Satpam') {
            $rows = DB::table('total_barokah_satpam as tb')
                ->join('satpam as s', 'tb.id_satpam', '=', 's.id_satpam')
                ->join('umana as u', 's.nik', '=', 'u.nik')
                ->where('tb.id_kehadiran_lembaga', $id)
                ->select(
                    'u.gelar_depan',
                    'u.gelar_belakang',
                    'u.nama_lengkap',
                    'u.nik',
                    'tb.jumlah_hari as jumlah_hadir',
                    'tb.potongan',
                    'tb.diterima'
                )
                ->orderBy('u.nama_lengkap', 'asc')
                ->get();
        }

        $totalUmana = count($rows);
        $totalBarokah = $rows->sum('diterima');
        $rataRata = $totalUmana > 0 ? ($totalBarokah / $totalUmana) : 0;

        return view('laporan.per-lembaga.rincian', compact(
            'lembaga',
            'rows',
            'kategori',
            'totalUmana',
            'totalBarokah',
            'rataRata'
        ));
    }

    /**
     * Cetak Laporan Rincian Lembaga (Format Cetak / PDF)
     */
    public function cetak($id)
    {
        $lembaga = DB::table('kehadiran_lembaga')
            ->join('lembaga', 'kehadiran_lembaga.id_lembaga', '=', 'lembaga.id_lembaga')
            ->where('kehadiran_lembaga.id_kehadiran_lembaga', $id)
            ->select('kehadiran_lembaga.*', 'lembaga.nama_lembaga')
            ->first();

        if (!$lembaga) {
            abort(404, 'Data lembaga tidak ditemukan');
        }

        $kategori = $lembaga->kategori ?: 'Struktural';
        $rows = [];

        if ($kategori === 'Struktural') {
            $rows = DB::table('kehadiran')
                ->join('kehadiran_lembaga', 'kehadiran_lembaga.id_kehadiran_lembaga', '=', 'kehadiran.id_kehadi')
                ->join('penempatan', 'penempatan.id_penempatan', '=', 'kehadiran.id_penempatan')
                ->join('umana', 'penempatan.nik', '=', 'umana.nik')
                ->join('total_barokah', 'total_barokah.id_penempatan', '=', 'kehadiran.id_penempatan')
                ->join('ketentuan_barokah', 'penempatan.id_ketentuan', '=', 'ketentuan_barokah.id_ketentuan')
                ->join('lembaga', 'lembaga.id_lembaga', '=', 'penempatan.id_lembaga')
                ->where('kehadiran_lembaga.id_kehadiran_lembaga', $id)
                ->where('total_barokah.bulan', DB::raw('kehadiran_lembaga.bulan'))
                ->where('total_barokah.tahun', DB::raw('kehadiran_lembaga.tahun'))
                ->select(
                    'umana.gelar_depan',
                    'umana.gelar_belakang',
                    'umana.nama_lengkap',
                    'umana.nik',
                    'ketentuan_barokah.nama_jabatan',
                    'umana.tmt_struktural',
                    'ketentuan_barokah.barokah',
                    'total_barokah.mp',
                    'kehadiran.jumlah_hadir',
                    'total_barokah.nominal_kehadiran',
                    'total_barokah.tunkel',
                    'total_barokah.tunj_anak',
                    'total_barokah.tbk',
                    'total_barokah.tmp',
                    'total_barokah.kehormatan',
                    'total_barokah.potongan',
                    'total_barokah.diterima'
                )
                ->groupBy('umana.nik', 'umana.gelar_depan', 'umana.gelar_belakang', 'umana.nama_lengkap', 'ketentuan_barokah.nama_jabatan', 'umana.tmt_struktural', 'ketentuan_barokah.barokah', 'total_barokah.mp', 'kehadiran.jumlah_hadir', 'total_barokah.nominal_kehadiran', 'total_barokah.tunkel', 'total_barokah.tunj_anak', 'total_barokah.tbk', 'total_barokah.tmp', 'total_barokah.kehormatan', 'total_barokah.potongan', 'total_barokah.diterima', 'ketentuan_barokah.id_ketentuan')
                ->orderBy('ketentuan_barokah.id_ketentuan', 'asc')
                ->get();
        } elseif ($kategori === 'Pengajar') {
            $rows = DB::table('kehadiran_lembaga as kl')
                ->join('kehadiran_pengajar as kp', 'kl.id_kehadiran_lembaga', '=', 'kp.id_kehadiran_lembaga')
                ->join('pengajar as p', 'kp.id_pengajar', '=', 'p.id_pengajar')
                ->join('umana as u', 'p.nik', '=', 'u.nik')
                ->join('lembaga as l', 'p.id_lembaga', '=', 'l.id_lembaga')
                ->join('total_barokah_pengajar as tbp', function($join) {
                    $join->on('tbp.id_pengajar', '=', 'kp.id_pengajar')
                         ->on('tbp.bulan', '=', 'kl.bulan')
                         ->on('tbp.tahun', '=', 'kl.tahun');
                })
                ->where('kl.id_kehadiran_lembaga', $id)
                ->select(
                    'u.gelar_depan',
                    'u.gelar_belakang',
                    'u.nama_lengkap',
                    'u.nik',
                    'p.kategori as nama_jabatan',
                    'tbp.jumlah_sks',
                    'tbp.mengajar',
                    'tbp.mp',
                    'tbp.dty',
                    'tbp.jafung',
                    'tbp.jumlah_hadir',
                    'tbp.nominal_kehadiran',
                    'tbp.jumlah_hadir_15',
                    'tbp.nominal_hadir_15',
                    'tbp.jumlah_hadir_10',
                    'tbp.nominal_hadir_10',
                    'tbp.jumlah_hadir_piket',
                    'tbp.barokah_piket',
                    'tbp.tunkel',
                    'tbp.tun_anak as tunj_anak',
                    'tbp.kehormatan',
                    'tbp.walkes',
                    'tbp.khusus',
                    'tbp.potongan',
                    'tbp.diterima'
                )
                ->orderBy('u.nama_lengkap', 'asc')
                ->get();
        } elseif ($kategori === 'Satpam') {
            $rows = DB::table('total_barokah_satpam as tb')
                ->join('satpam as s', 'tb.id_satpam', '=', 's.id_satpam')
                ->join('umana as u', 's.nik', '=', 'u.nik')
                ->where('tb.id_kehadiran_lembaga', $id)
                ->select(
                    'u.gelar_depan',
                    'u.gelar_belakang',
                    'u.nama_lengkap',
                    'u.nik',
                    'tb.jumlah_hari as jumlah_hadir',
                    'tb.potongan',
                    'tb.diterima'
                )
                ->orderBy('u.nama_lengkap', 'asc')
                ->get();
        }

        return view('laporan.per-lembaga.cetak', compact('lembaga', 'rows', 'kategori'));
    }

    /**
     * Export Rincian Barokah Lembaga ke format Excel (.xls)
     */
    public function exportExcel($id)
    {
        $lembaga = DB::table('kehadiran_lembaga')
            ->join('lembaga', 'kehadiran_lembaga.id_lembaga', '=', 'lembaga.id_lembaga')
            ->where('kehadiran_lembaga.id_kehadiran_lembaga', $id)
            ->select('kehadiran_lembaga.*', 'lembaga.nama_lembaga')
            ->first();

        if (!$lembaga) {
            abort(404, 'Data lembaga tidak ditemukan');
        }

        $kategori = $lembaga->kategori ?: 'Struktural';
        $rows = [];

        if ($kategori === 'Struktural') {
            $rows = DB::table('kehadiran')
                ->join('kehadiran_lembaga', 'kehadiran_lembaga.id_kehadiran_lembaga', '=', 'kehadiran.id_kehadi')
                ->join('penempatan', 'penempatan.id_penempatan', '=', 'kehadiran.id_penempatan')
                ->join('umana', 'penempatan.nik', '=', 'umana.nik')
                ->join('total_barokah', 'total_barokah.id_penempatan', '=', 'kehadiran.id_penempatan')
                ->join('ketentuan_barokah', 'penempatan.id_ketentuan', '=', 'ketentuan_barokah.id_ketentuan')
                ->join('lembaga', 'lembaga.id_lembaga', '=', 'penempatan.id_lembaga')
                ->where('kehadiran_lembaga.id_kehadiran_lembaga', $id)
                ->where('total_barokah.bulan', DB::raw('kehadiran_lembaga.bulan'))
                ->where('total_barokah.tahun', DB::raw('kehadiran_lembaga.tahun'))
                ->select(
                    'umana.gelar_depan',
                    'umana.gelar_belakang',
                    'umana.nama_lengkap',
                    'umana.nik',
                    'ketentuan_barokah.nama_jabatan',
                    'umana.tmt_struktural',
                    'ketentuan_barokah.barokah',
                    'total_barokah.mp',
                    'kehadiran.jumlah_hadir',
                    'total_barokah.nominal_kehadiran',
                    'total_barokah.tunkel',
                    'total_barokah.tunj_anak',
                    'total_barokah.tbk',
                    'total_barokah.tmp',
                    'total_barokah.kehormatan',
                    'total_barokah.potongan',
                    'total_barokah.diterima'
                )
                ->groupBy('umana.nik', 'umana.gelar_depan', 'umana.gelar_belakang', 'umana.nama_lengkap', 'ketentuan_barokah.nama_jabatan', 'umana.tmt_struktural', 'ketentuan_barokah.barokah', 'total_barokah.mp', 'kehadiran.jumlah_hadir', 'total_barokah.nominal_kehadiran', 'total_barokah.tunkel', 'total_barokah.tunj_anak', 'total_barokah.tbk', 'total_barokah.tmp', 'total_barokah.kehormatan', 'total_barokah.potongan', 'total_barokah.diterima', 'ketentuan_barokah.id_ketentuan')
                ->orderBy('ketentuan_barokah.id_ketentuan', 'asc')
                ->get();
        } elseif ($kategori === 'Pengajar') {
            $rows = DB::table('kehadiran_lembaga as kl')
                ->join('kehadiran_pengajar as kp', 'kl.id_kehadiran_lembaga', '=', 'kp.id_kehadiran_lembaga')
                ->join('pengajar as p', 'kp.id_pengajar', '=', 'p.id_pengajar')
                ->join('umana as u', 'p.nik', '=', 'u.nik')
                ->join('lembaga as l', 'p.id_lembaga', '=', 'l.id_lembaga')
                ->join('total_barokah_pengajar as tbp', function($join) {
                    $join->on('tbp.id_pengajar', '=', 'kp.id_pengajar')
                         ->on('tbp.bulan', '=', 'kl.bulan')
                         ->on('tbp.tahun', '=', 'kl.tahun');
                })
                ->where('kl.id_kehadiran_lembaga', $id)
                ->select(
                    'u.gelar_depan',
                    'u.gelar_belakang',
                    'u.nama_lengkap',
                    'u.nik',
                    'p.kategori as nama_jabatan',
                    'tbp.jumlah_sks',
                    'tbp.mengajar',
                    'tbp.mp',
                    'tbp.dty',
                    'tbp.jafung',
                    'tbp.jumlah_hadir',
                    'tbp.nominal_kehadiran',
                    'tbp.jumlah_hadir_15',
                    'tbp.nominal_hadir_15',
                    'tbp.jumlah_hadir_10',
                    'tbp.nominal_hadir_10',
                    'tbp.jumlah_hadir_piket',
                    'tbp.barokah_piket',
                    'tbp.tunkel',
                    'tbp.tun_anak as tunj_anak',
                    'tbp.kehormatan',
                    'tbp.walkes',
                    'tbp.khusus',
                    'tbp.potongan',
                    'tbp.diterima'
                )
                ->orderBy('u.nama_lengkap', 'asc')
                ->get();
        } elseif ($kategori === 'Satpam') {
            $rows = DB::table('total_barokah_satpam as tb')
                ->join('satpam as s', 'tb.id_satpam', '=', 's.id_satpam')
                ->join('umana as u', 's.nik', '=', 'u.nik')
                ->where('tb.id_kehadiran_lembaga', $id)
                ->select(
                    'u.gelar_depan',
                    'u.gelar_belakang',
                    'u.nama_lengkap',
                    'u.nik',
                    'tb.jumlah_hari as jumlah_hadir',
                    'tb.potongan',
                    'tb.diterima'
                )
                ->orderBy('u.nama_lengkap', 'asc')
                ->get();
        }

        $cleanLembaga = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $lembaga->nama_lembaga);
        $cleanPeriode = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $lembaga->bulan . '_' . $lembaga->tahun);
        $filename = "Laporan_Barokah_{$cleanLembaga}_{$kategori}_{$cleanPeriode}.xls";

        return response()->view('laporan.per-lembaga.excel', compact('lembaga', 'rows', 'kategori'))
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Cache-Control', 'max-age=0');
    }
}
