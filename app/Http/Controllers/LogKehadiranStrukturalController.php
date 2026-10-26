<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LogKehadiranStrukturalController extends Controller
{
    /**
     * Tampilkan halaman utama Log Kehadiran Struktural
     */
    public function index()
    {
        $bulanList = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        // Daftar tahun dari tabel kehadiran
        $tahunList = DB::table('kehadiran')
            ->select('tahun')
            ->whereNotNull('tahun')
            ->where('tahun', '!=', '')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        // Daftar lembaga untuk filter
        $lembagaList = DB::table('lembaga')
            ->select('id_lembaga', 'nama_lembaga')
            ->orderBy('nama_lembaga', 'asc')
            ->get();

        return view('log.kehadiran-struktural.index', compact('bulanList', 'tahunList', 'lembagaList'));
    }

    /**
     * Endpoint DataTables Server-Side
     */
    public function data(Request $request)
    {
        $query = DB::table('kehadiran as k')
            ->leftJoin('penempatan as p', 'p.id_penempatan', '=', 'k.id_penempatan')
            ->leftJoin('umana as u', 'u.nik', '=', 'p.nik')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'p.id_lembaga')
            ->select(
                'k.id_kehadiran',
                'k.bulan',
                'k.tahun',
                'k.id_penempatan',
                'k.id_kehadi',
                'k.jumlah_hadir',
                'k.jumlah_tugas',
                'k.jumlah_izin',
                'k.jumlah_sakit',
                'k.tgl_input',
                'u.nama_lengkap',
                'u.nik',
                'l.nama_lembaga',
                'p.jabatan_lembaga'
            );

        // Filter Lembaga
        if ($filterLembaga = $request->input('filter_lembaga')) {
            $query->where('p.id_lembaga', $filterLembaga);
        }

        // Filter Bulan
        if ($filterBulan = $request->input('filter_bulan')) {
            $query->where('k.bulan', $filterBulan);
        }

        // Filter Tahun
        if ($filterTahun = $request->input('filter_tahun')) {
            $query->where('k.tahun', $filterTahun);
        }

        // Filter ID Kehadi (Blanko Lembaga)
        if ($filterKehadi = $request->input('filter_kehadi')) {
            $query->where('k.id_kehadi', $filterKehadi);
        }

        // Search Global DataTables
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('u.nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('u.nik', 'like', "%{$search}%")
                  ->orWhere('l.nama_lembaga', 'like', "%{$search}%")
                  ->orWhere('p.jabatan_lembaga', 'like', "%{$search}%")
                  ->orWhere('k.id_kehadiran', 'like', "%{$search}%")
                  ->orWhere('k.id_penempatan', 'like', "%{$search}%")
                  ->orWhere('k.id_kehadi', 'like', "%{$search}%")
                  ->orWhere('k.bulan', 'like', "%{$search}%")
                  ->orWhere('k.tahun', 'like', "%{$search}%");
            });
        }

        $totalRecords = DB::table('kehadiran')->count();
        $filteredRecords = (clone $query)->count();

        // Ordering
        $orderColIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        $columns = [
            0 => 'k.id_kehadiran',
            1 => 'k.id_kehadiran',
            2 => 'u.nama_lengkap',
            3 => 'l.nama_lembaga',
            4 => 'k.tahun',
            5 => 'k.id_kehadi',
            6 => 'k.jumlah_hadir',
            7 => 'k.tgl_input',
            8 => 'k.id_kehadiran',
        ];

        if (isset($columns[$orderColIndex])) {
            $query->orderBy($columns[$orderColIndex], $orderDir);
        } else {
            $query->orderBy('k.id_kehadiran', 'desc');
        }

        // Pagination
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $list = $query->get();

        $data = [];
        $no = $start + 1;
        foreach ($list as $item) {
            $namaUmana = $item->nama_lengkap ?: 'Umana (NIK: ' . ($item->nik ?: 'N/A') . ')';
            $jabatan = $item->jabatan_lembaga ?: '-';
            $lembaga = $item->nama_lembaga ?: '-';

            // Format Nama & NIK & Jabatan
            $namaHtml = '
                <div class="d-flex flex-column">
                    <span class="fw-bold text-dark">' . e($namaUmana) . '</span>
                    <small class="text-muted"><i class="far fa-id-card me-1"></i>' . e($item->nik ?: '-') . ' &bull; ' . e($jabatan) . '</small>
                </div>
            ';

            // Periode
            $periodeHtml = '<span class="badge badge-periode"><i class="far fa-calendar-alt me-1 text-success"></i>' . e($item->bulan ?: '-') . ' ' . e($item->tahun ?: '-') . '</span>';

            // ID Kehadi (Blanko)
            $kehadiHtml = $item->id_kehadi 
                ? '<span class="badge badge-kehadi">#' . e($item->id_kehadi) . '</span>'
                : '<span class="text-muted">-</span>';

            // Kehadiran metrics
            $kehadiranHtml = '
                <div class="d-flex flex-wrap gap-1 align-items-center">
                    <span class="badge badge-stat-hadir" title="Jumlah Hadir">H: ' . (int)$item->jumlah_hadir . '</span>
            ';
            if ($item->jumlah_tugas !== null && $item->jumlah_tugas !== '') {
                $kehadiranHtml .= '<span class="badge badge-stat-tugas" title="Jumlah Tugas">T: ' . (int)$item->jumlah_tugas . '</span>';
            }
            if ($item->jumlah_izin !== null && $item->jumlah_izin !== '') {
                $kehadiranHtml .= '<span class="badge badge-stat-izin" title="Jumlah Izin">I: ' . (int)$item->jumlah_izin . '</span>';
            }
            if ($item->jumlah_sakit !== null && $item->jumlah_sakit !== '') {
                $kehadiranHtml .= '<span class="badge badge-stat-sakit" title="Jumlah Sakit">S: ' . (int)$item->jumlah_sakit . '</span>';
            }
            $kehadiranHtml .= '</div>';

            // Tanggal Input
            $tglInputStr = $item->tgl_input ? date('d/m/Y H:i', strtotime($item->tgl_input)) : '-';
            $tglHtml = '<small class="text-muted d-inline-flex align-items-center gap-1"><i class="far fa-clock"></i>' . $tglInputStr . '</small>';

            // Action buttons
            $actionBtn = '
                <div class="d-flex justify-content-center gap-1">
                    <button type="button" class="btn btn-warning btn-xs text-white btn-edit-kehadiran" data-id="' . $item->id_kehadiran . '" title="Edit">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-xs btn-delete-kehadiran" data-id="' . $item->id_kehadiran . '" data-nama="' . e($namaUmana) . '" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            ';

            $data[] = [
                'no' => $no++,
                'id_kehadiran' => '<span class="badge badge-id-kehadiran">#' . $item->id_kehadiran . '</span>',
                'nama_umana' => $namaHtml,
                'lembaga' => '<span class="fw-semibold text-secondary">' . e($lembaga) . '</span>',
                'periode' => $periodeHtml,
                'id_kehadi' => $kehadiHtml,
                'kehadiran' => $kehadiranHtml,
                'tgl_input' => $tglHtml,
                'aksi' => $actionBtn,
            ];
        }

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    /**
     * Ambil data tunggal kehadiran untuk edit modal
     */
    public function show($id)
    {
        $item = DB::table('kehadiran as k')
            ->leftJoin('penempatan as p', 'p.id_penempatan', '=', 'k.id_penempatan')
            ->leftJoin('umana as u', 'u.nik', '=', 'p.nik')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'p.id_lembaga')
            ->select(
                'k.*',
                'u.nama_lengkap',
                'u.nik',
                'l.nama_lembaga',
                'p.jabatan_lembaga'
            )
            ->where('k.id_kehadiran', $id)
            ->first();

        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        return response()->json(['status' => true, 'data' => $item]);
    }

    /**
     * Tambah data kehadiran struktural
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_penempatan' => 'required',
            'bulan' => 'required|string',
            'tahun' => 'required|string',
            'jumlah_hadir' => 'required|numeric|min:0',
            'id_kehadi' => 'nullable',
            'jumlah_tugas' => 'nullable|numeric|min:0',
            'jumlah_izin' => 'nullable|numeric|min:0',
            'jumlah_sakit' => 'nullable|numeric|min:0',
        ], [
            'id_penempatan.required' => 'Penempatan / Umana wajib dipilih',
            'bulan.required' => 'Bulan wajib dipilih',
            'tahun.required' => 'Tahun wajib diisi',
            'jumlah_hadir.required' => 'Jumlah hadir wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $validator->errors()->all()),
                'errors' => $validator->errors()
            ], 422);
        }

        $insertData = [
            'id_penempatan' => $request->input('id_penempatan'),
            'id_kehadi' => $request->input('id_kehadi') ?: null,
            'bulan' => $request->input('bulan'),
            'tahun' => $request->input('tahun'),
            'jumlah_hadir' => $request->input('jumlah_hadir') !== null ? (int)$request->input('jumlah_hadir') : 0,
            'jumlah_tugas' => $request->filled('jumlah_tugas') ? (int)$request->input('jumlah_tugas') : null,
            'jumlah_izin' => $request->filled('jumlah_izin') ? (int)$request->input('jumlah_izin') : null,
            'jumlah_sakit' => $request->filled('jumlah_sakit') ? (int)$request->input('jumlah_sakit') : null,
            'tgl_input' => now(),
        ];

        $insertId = DB::table('kehadiran')->insertGetId($insertData);

        return response()->json([
            'status' => true,
            'message' => 'Data kehadiran struktural berhasil ditambahkan!',
            'id' => $insertId,
        ]);
    }

    /**
     * Update data kehadiran struktural
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_penempatan' => 'required',
            'bulan' => 'required|string',
            'tahun' => 'required|string',
            'jumlah_hadir' => 'required|numeric|min:0',
            'id_kehadi' => 'nullable',
            'jumlah_tugas' => 'nullable|numeric|min:0',
            'jumlah_izin' => 'nullable|numeric|min:0',
            'jumlah_sakit' => 'nullable|numeric|min:0',
        ], [
            'id_penempatan.required' => 'Penempatan / Umana wajib dipilih',
            'bulan.required' => 'Bulan wajib dipilih',
            'tahun.required' => 'Tahun wajib diisi',
            'jumlah_hadir.required' => 'Jumlah hadir wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $validator->errors()->all()),
                'errors' => $validator->errors()
            ], 422);
        }

        $existing = DB::table('kehadiran')->where('id_kehadiran', $id)->first();
        if (!$existing) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        $updateData = [
            'id_penempatan' => $request->input('id_penempatan'),
            'id_kehadi' => $request->input('id_kehadi') ?: null,
            'bulan' => $request->input('bulan'),
            'tahun' => $request->input('tahun'),
            'jumlah_hadir' => $request->input('jumlah_hadir') !== null ? (int)$request->input('jumlah_hadir') : 0,
            'jumlah_tugas' => $request->filled('jumlah_tugas') ? (int)$request->input('jumlah_tugas') : null,
            'jumlah_izin' => $request->filled('jumlah_izin') ? (int)$request->input('jumlah_izin') : null,
            'jumlah_sakit' => $request->filled('jumlah_sakit') ? (int)$request->input('jumlah_sakit') : null,
        ];

        DB::table('kehadiran')->where('id_kehadiran', $id)->update($updateData);

        return response()->json([
            'status' => true,
            'message' => 'Data kehadiran struktural berhasil diperbarui!',
        ]);
    }

    /**
     * Hapus data kehadiran struktural
     */
    public function destroy($id)
    {
        $item = DB::table('kehadiran')->where('id_kehadiran', $id)->first();
        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        DB::table('kehadiran')->where('id_kehadiran', $id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data kehadiran struktural #' . $id . ' berhasil dihapus!',
        ]);
    }

    /**
     * AJAX Search untuk Select2 Penempatan / Umana
     */
    public function searchPenempatan(Request $request)
    {
        $q = $request->input('q');

        $query = DB::table('penempatan as p')
            ->leftJoin('umana as u', 'u.nik', '=', 'p.nik')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'p.id_lembaga')
            ->select(
                'p.id_penempatan',
                'u.nama_lengkap',
                'u.nik',
                'l.nama_lembaga',
                'p.jabatan_lembaga'
            );

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('u.nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('u.nik', 'like', "%{$q}%")
                    ->orWhere('l.nama_lembaga', 'like', "%{$q}%")
                    ->orWhere('p.jabatan_lembaga', 'like', "%{$q}%")
                    ->orWhere('p.id_penempatan', '=', $q);
            });
        }

        $results = $query->limit(35)->get();

        $data = $results->map(function ($item) {
            $label = ($item->nama_lengkap ?: 'Umana') . ' (' . ($item->nama_lembaga ?: 'Lembaga -') . ($item->jabatan_lembaga ? ' &bull; ' . $item->jabatan_lembaga : '') . ') [ID: ' . $item->id_penempatan . ']';
            return [
                'id' => $item->id_penempatan,
                'text' => $label,
                'nama' => $item->nama_lengkap,
                'lembaga' => $item->nama_lembaga,
                'jabatan' => $item->jabatan_lembaga,
                'nik' => $item->nik,
            ];
        });

        return response()->json($data);
    }
}
