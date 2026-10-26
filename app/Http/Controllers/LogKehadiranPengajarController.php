<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LogKehadiranPengajarController extends Controller
{
    /**
     * Tampilkan halaman utama Log Kehadiran Pengajar
     */
    public function index()
    {
        $bulanList = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        // Daftar tahun dari tabel kehadiran_pengajar
        $tahunList = DB::table('kehadiran_pengajar')
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

        return view('log.kehadiran-pengajar.index', compact('bulanList', 'tahunList', 'lembagaList'));
    }

    /**
     * Endpoint DataTables Server-Side
     */
    public function data(Request $request)
    {
        $query = DB::table('kehadiran_pengajar as kp')
            ->leftJoin('pengajar as p', 'p.id_pengajar', '=', 'kp.id_pengajar')
            ->leftJoin('umana as u', 'u.nik', '=', 'p.nik')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'p.id_lembaga')
            ->select(
                'kp.id_kehadiran_pengajar',
                'kp.bulan',
                'kp.tahun',
                'kp.id_pengajar',
                'kp.id_kehadiran_lembaga',
                'kp.jumlah_hadir',
                'kp.jumlah_hadir_15',
                'kp.jumlah_hadir_10',
                'kp.jumlah_hadir_piket',
                'kp.tgl_input',
                'kp.catatan_khusus_umana',
                'u.nama_lengkap',
                'u.nik',
                'l.nama_lembaga',
                'p.kategori as kategori_pengajar',
                'p.jumlah_sks'
            );

        // Filter Lembaga
        if ($filterLembaga = $request->input('filter_lembaga')) {
            $query->where('p.id_lembaga', $filterLembaga);
        }

        // Filter Bulan
        if ($filterBulan = $request->input('filter_bulan')) {
            $query->where('kp.bulan', $filterBulan);
        }

        // Filter Tahun
        if ($filterTahun = $request->input('filter_tahun')) {
            $query->where('kp.tahun', $filterTahun);
        }

        // Filter ID Blanko (id_kehadiran_lembaga)
        if ($filterBlanko = $request->input('filter_blanko')) {
            $query->where('kp.id_kehadiran_lembaga', $filterBlanko);
        }

        // Search Global DataTables
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('u.nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('u.nik', 'like', "%{$search}%")
                  ->orWhere('l.nama_lembaga', 'like', "%{$search}%")
                  ->orWhere('kp.id_kehadiran_pengajar', 'like', "%{$search}%")
                  ->orWhere('kp.id_pengajar', 'like', "%{$search}%")
                  ->orWhere('kp.id_kehadiran_lembaga', 'like', "%{$search}%")
                  ->orWhere('kp.bulan', 'like', "%{$search}%")
                  ->orWhere('kp.tahun', 'like', "%{$search}%");
            });
        }

        $totalRecords = DB::table('kehadiran_pengajar')->count();
        $filteredRecords = (clone $query)->count();

        // Ordering
        $orderColIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        $columns = [
            0 => 'kp.id_kehadiran_pengajar',
            1 => 'kp.id_kehadiran_pengajar',
            2 => 'u.nama_lengkap',
            3 => 'l.nama_lembaga',
            4 => 'kp.tahun',
            5 => 'kp.id_kehadiran_lembaga',
            6 => 'kp.jumlah_hadir',
            7 => 'kp.tgl_input',
            8 => 'kp.id_kehadiran_pengajar',
        ];

        if (isset($columns[$orderColIndex])) {
            $query->orderBy($columns[$orderColIndex], $orderDir);
        } else {
            $query->orderBy('kp.id_kehadiran_pengajar', 'desc');
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
            $namaPengajar = $item->nama_lengkap ?: 'Pengajar (NIK: ' . ($item->nik ?: 'N/A') . ')';
            $kategori = $item->kategori_pengajar ?: '';
            $lembaga = $item->nama_lembaga ?: '-';

            // Format Nama & NIK & Kategori
            $namaHtml = '<div><strong>' . e($namaPengajar) . '</strong>';
            if ($item->nik || $kategori) {
                $sub = [];
                if ($item->nik) $sub[] = e($item->nik);
                if ($kategori) $sub[] = e($kategori);
                if ($item->jumlah_sks) $sub[] = (int)$item->jumlah_sks . ' SKS';
                $namaHtml .= '<br><small class="text-muted">' . implode(' &bull; ', $sub) . '</small>';
            }
            $namaHtml .= '</div>';

            // Lembaga
            $lembagaHtml = e($lembaga);

            // Periode
            $periodeHtml = e($item->bulan ?: '-') . ' ' . e($item->tahun ?: '-');

            // ID Blanko
            $blankoHtml = $item->id_kehadiran_lembaga ? '#' . e($item->id_kehadiran_lembaga) : '-';

            // Kehadiran (Kehadiran Pokok, 15%, 10%, Piket)
            $kehadiranHtml = (int)$item->jumlah_hadir . ' Hadir';
            $extra = [];
            if ($item->jumlah_hadir_15 !== null && $item->jumlah_hadir_15 !== '') {
                $extra[] = (int)$item->jumlah_hadir_15 . ' Hadir 15%';
            }
            if ($item->jumlah_hadir_10 !== null && $item->jumlah_hadir_10 !== '') {
                $extra[] = (int)$item->jumlah_hadir_10 . ' Hadir 10%';
            }
            if ($item->jumlah_hadir_piket !== null && $item->jumlah_hadir_piket !== '' && (int)$item->jumlah_hadir_piket > 0) {
                $extra[] = (int)$item->jumlah_hadir_piket . ' Piket';
            }
            if (!empty($extra)) {
                $kehadiranHtml .= '<br><small class="text-muted">' . implode(', ', $extra) . '</small>';
            }

            // Tanggal Input
            $tglHtml = $item->tgl_input ? date('d/m/Y H:i', strtotime($item->tgl_input)) : '-';

            // Action buttons (exact matching Master menu style)
            $actionBtn = '
                <div class="d-flex">
                    <button type="button" class="btn btn-outline-danger btn-sm shadow-sm btn-edit-kehadiran" style="border-radius: 20px; padding: 5px 15px;" data-id="' . $item->id_kehadiran_pengajar . '">
                        <i class="fas fa-pencil-alt me-1" style="pointer-events: none;"></i> Edit
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-sm shadow-sm btn-delete-kehadiran" style="border-radius: 20px; padding: 5px 15px; margin-left: 5px;" data-id="' . $item->id_kehadiran_pengajar . '" data-nama="' . htmlspecialchars($namaPengajar, ENT_QUOTES, 'UTF-8') . '">
                        <i class="fas fa-trash me-1" style="pointer-events: none;"></i> Hapus
                    </button>
                </div>
            ';

            $data[] = [
                'no' => '<strong>' . ($no++) . '</strong>',
                'id_kehadiran_pengajar' => '#' . $item->id_kehadiran_pengajar,
                'nama_pengajar' => $namaHtml,
                'lembaga' => $lembagaHtml,
                'periode' => $periodeHtml,
                'id_blanko' => $blankoHtml,
                'kehadiran' => $kehadiranHtml,
                'tgl_input' => $tglHtml,
                'aksi' => $actionBtn,
            ];
        }

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    /**
     * Ambil data tunggal kehadiran pengajar untuk Modal Edit
     */
    public function show($id)
    {
        $item = DB::table('kehadiran_pengajar as kp')
            ->leftJoin('pengajar as p', 'p.id_pengajar', '=', 'kp.id_pengajar')
            ->leftJoin('umana as u', 'u.nik', '=', 'p.nik')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'p.id_lembaga')
            ->where('kp.id_kehadiran_pengajar', $id)
            ->select(
                'kp.*',
                'u.nama_lengkap',
                'u.nik',
                'l.nama_lembaga',
                'p.kategori as kategori_pengajar',
                'p.id_lembaga'
            )
            ->first();

        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        return response()->json(['status' => true, 'data' => $item]);
    }

    /**
     * Simpan data log kehadiran pengajar baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pengajar' => 'required',
            'bulan' => 'required',
            'tahun' => 'required',
            'jumlah_hadir' => 'required|numeric|min:0',
            'jumlah_hadir_15' => 'nullable|numeric|min:0',
            'jumlah_hadir_10' => 'nullable|numeric|min:0',
            'jumlah_hadir_piket' => 'nullable|numeric|min:0',
        ], [
            'id_pengajar.required' => 'Pengajar wajib dipilih',
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
            'id_pengajar' => $request->input('id_pengajar'),
            'id_kehadiran_lembaga' => $request->input('id_kehadiran_lembaga') ?: null,
            'bulan' => $request->input('bulan'),
            'tahun' => $request->input('tahun'),
            'jumlah_hadir' => (int) $request->input('jumlah_hadir'),
            'jumlah_hadir_15' => $request->filled('jumlah_hadir_15') ? (int)$request->input('jumlah_hadir_15') : null,
            'jumlah_hadir_10' => $request->filled('jumlah_hadir_10') ? (int)$request->input('jumlah_hadir_10') : null,
            'jumlah_hadir_piket' => $request->filled('jumlah_hadir_piket') ? (int)$request->input('jumlah_hadir_piket') : 0,
            'catatan_khusus_umana' => $request->input('catatan_khusus_umana') ?: null,
            'tgl_input' => now(),
        ];

        $newId = DB::table('kehadiran_pengajar')->insertGetId($insertData);

        return response()->json([
            'status' => true,
            'message' => 'Data log kehadiran pengajar berhasil ditambahkan!',
            'id' => $newId,
        ]);
    }

    /**
     * Update data log kehadiran pengajar
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_pengajar' => 'required',
            'bulan' => 'required',
            'tahun' => 'required',
            'jumlah_hadir' => 'required|numeric|min:0',
            'jumlah_hadir_15' => 'nullable|numeric|min:0',
            'jumlah_hadir_10' => 'nullable|numeric|min:0',
            'jumlah_hadir_piket' => 'nullable|numeric|min:0',
        ], [
            'id_pengajar.required' => 'Pengajar wajib dipilih',
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

        $existing = DB::table('kehadiran_pengajar')->where('id_kehadiran_pengajar', $id)->first();
        if (!$existing) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        $updateData = [
            'id_pengajar' => $request->input('id_pengajar'),
            'id_kehadiran_lembaga' => $request->input('id_kehadiran_lembaga') ?: null,
            'bulan' => $request->input('bulan'),
            'tahun' => $request->input('tahun'),
            'jumlah_hadir' => (int) $request->input('jumlah_hadir'),
            'jumlah_hadir_15' => $request->filled('jumlah_hadir_15') ? (int)$request->input('jumlah_hadir_15') : null,
            'jumlah_hadir_10' => $request->filled('jumlah_hadir_10') ? (int)$request->input('jumlah_hadir_10') : null,
            'jumlah_hadir_piket' => $request->filled('jumlah_hadir_piket') ? (int)$request->input('jumlah_hadir_piket') : 0,
            'catatan_khusus_umana' => $request->input('catatan_khusus_umana') ?: null,
        ];

        DB::table('kehadiran_pengajar')->where('id_kehadiran_pengajar', $id)->update($updateData);

        return response()->json([
            'status' => true,
            'message' => 'Data log kehadiran pengajar berhasil diperbarui!',
        ]);
    }

    /**
     * Hapus data kehadiran pengajar
     */
    public function destroy($id)
    {
        try {
            $id = (int) $id;
            $item = DB::table('kehadiran_pengajar')->where('id_kehadiran_pengajar', $id)->first();
            if (!$item) {
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json(['status' => false, 'message' => 'Data tidak ditemukan atau sudah dihapus!'], 404);
                }
                return redirect('/log/kehadiran-pengajar')->with('error', 'Data tidak ditemukan!');
            }

            DB::table('kehadiran_pengajar')->where('id_kehadiran_pengajar', $id)->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Data kehadiran pengajar #' . $id . ' berhasil dihapus!',
                ]);
            }

            return redirect('/log/kehadiran-pengajar')->with('success', 'Data kehadiran pengajar #' . $id . ' berhasil dihapus!');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
            }
            return redirect('/log/kehadiran-pengajar')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * AJAX Search untuk Select2 Pengajar
     */
    public function searchPengajar(Request $request)
    {
        $q = $request->input('q');

        $query = DB::table('pengajar as p')
            ->leftJoin('umana as u', 'u.nik', '=', 'p.nik')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'p.id_lembaga')
            ->select(
                'p.id_pengajar',
                'u.nama_lengkap',
                'u.nik',
                'l.nama_lembaga',
                'p.kategori'
            );

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('u.nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('u.nik', 'like', "%{$q}%")
                    ->orWhere('l.nama_lembaga', 'like', "%{$q}%")
                    ->orWhere('p.id_pengajar', '=', $q);
            });
        }

        $results = $query->limit(35)->get();

        $data = $results->map(function ($item) {
            $label = ($item->nama_lengkap ?: 'Pengajar') . ' (' . ($item->nama_lembaga ?: 'Lembaga -') . ($item->kategori ? ' &bull; ' . $item->kategori : '') . ') [ID: ' . $item->id_pengajar . ']';
            return [
                'id' => $item->id_pengajar,
                'text' => $label,
                'nama' => $item->nama_lengkap,
                'lembaga' => $item->nama_lembaga,
                'kategori' => $item->kategori,
                'nik' => $item->nik,
            ];
        });

        return response()->json($data);
    }
}
