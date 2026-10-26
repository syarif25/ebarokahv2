<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LogPotonganPengajarController extends Controller
{
    /**
     * Tampilkan halaman utama Log Potongan Pengajar
     */
    public function index()
    {
        // Daftar jenis potongan
        $jenisPotonganList = DB::table('potongan')
            ->select('id_potongan', 'nama_potongan')
            ->orderBy('nama_potongan', 'asc')
            ->get();

        // Daftar lembaga
        $lembagaList = DB::table('lembaga')
            ->select('id_lembaga', 'nama_lembaga')
            ->orderBy('nama_lembaga', 'asc')
            ->get();

        return view('log.potongan-pengajar.index', compact('jenisPotonganList', 'lembagaList'));
    }

    /**
     * Endpoint DataTables Server-Side
     */
    public function data(Request $request)
    {
        $query = DB::table('potongan_pengajar as pp')
            ->leftJoin('pengajar as p', 'p.id_pengajar', '=', 'pp.id_pengajar')
            ->leftJoin('umana as u', 'u.nik', '=', 'p.nik')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'p.id_lembaga')
            ->leftJoin('potongan as jp', 'jp.id_potongan', '=', 'pp.jenis_potongan')
            ->select(
                'pp.id_potongan_pengajar',
                'pp.id_pengajar',
                'pp.jenis_potongan',
                'pp.nominal_potongan',
                'pp.min_periode_potongan',
                'pp.max_periode_potongan',
                'u.nama_lengkap',
                'u.nik',
                'l.nama_lembaga',
                'p.kategori as kategori_pengajar',
                'jp.nama_potongan'
            );

        // Filter Lembaga
        if ($filterLembaga = $request->input('filter_lembaga')) {
            $query->where('p.id_lembaga', $filterLembaga);
        }

        // Filter Jenis Potongan
        if ($filterJenis = $request->input('filter_jenis')) {
            $query->where('pp.jenis_potongan', $filterJenis);
        }

        // Search Global
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('u.nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('u.nik', 'like', "%{$search}%")
                  ->orWhere('l.nama_lembaga', 'like', "%{$search}%")
                  ->orWhere('jp.nama_potongan', 'like', "%{$search}%")
                  ->orWhere('pp.id_potongan_pengajar', 'like', "%{$search}%")
                  ->orWhere('pp.id_pengajar', 'like', "%{$search}%");
            });
        }

        $totalRecords = DB::table('potongan_pengajar')->count();
        $filteredRecords = (clone $query)->count();

        // Ordering
        $orderColIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        $columns = [
            0 => 'pp.id_potongan_pengajar',
            1 => 'pp.id_potongan_pengajar',
            2 => 'u.nama_lengkap',
            3 => 'l.nama_lembaga',
            4 => 'jp.nama_potongan',
            5 => 'pp.nominal_potongan',
            6 => 'pp.min_periode_potongan',
            7 => 'pp.id_potongan_pengajar',
        ];

        if (isset($columns[$orderColIndex])) {
            $query->orderBy($columns[$orderColIndex], $orderDir);
        } else {
            $query->orderBy('pp.id_potongan_pengajar', 'desc');
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
            $namaPengajar = $item->nama_lengkap ?: 'Pengajar (ID: #' . ($item->id_pengajar ?: '-') . ')';
            $kategori = $item->kategori_pengajar ?: '';
            $lembaga = $item->nama_lembaga ?: '-';

            // Format Nama & NIK
            $namaHtml = '<div><strong>' . e($namaPengajar) . '</strong>';
            if ($item->nik || $kategori) {
                $sub = [];
                if ($item->nik) $sub[] = e($item->nik);
                if ($kategori) $sub[] = e($kategori);
                $namaHtml .= '<br><small class="text-muted">' . implode(' &bull; ', $sub) . '</small>';
            }
            $namaHtml .= '</div>';

            $lembagaHtml = e($lembaga);
            $jenisHtml = '<span class="badge badge-outline-primary" style="font-weight: 600;">' . e($item->nama_potongan ?: 'Jenis #' . $item->jenis_potongan) . '</span>';
            $nominalHtml = '<strong class="text-danger">Rp ' . number_format((float)$item->nominal_potongan, 0, ',', '.') . '</strong>';

            $minPer = $item->min_periode_potongan ? date('d/m/Y', strtotime($item->min_periode_potongan)) : '-';
            $maxPer = $item->max_periode_potongan ? date('d/m/Y', strtotime($item->max_periode_potongan)) : '-';
            $periodeHtml = $minPer . ' s/d ' . $maxPer;

            // Action buttons (Master style)
            $actionBtn = '
                <div class="d-flex">
                    <button type="button" class="btn btn-outline-danger btn-sm shadow-sm btn-edit-potongan" style="border-radius: 20px; padding: 5px 15px;" data-id="' . $item->id_potongan_pengajar . '">
                        <i class="fas fa-pencil-alt me-1" style="pointer-events: none;"></i> Edit
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-sm shadow-sm btn-delete-potongan" style="border-radius: 20px; padding: 5px 15px; margin-left: 5px;" data-id="' . $item->id_potongan_pengajar . '" data-nama="' . htmlspecialchars($namaPengajar, ENT_QUOTES, 'UTF-8') . '">
                        <i class="fas fa-trash me-1" style="pointer-events: none;"></i> Hapus
                    </button>
                </div>
            ';

            $data[] = [
                'no' => '<strong>' . ($no++) . '</strong>',
                'id_potongan_pengajar' => '#' . $item->id_potongan_pengajar,
                'nama_pengajar' => $namaHtml,
                'lembaga' => $lembagaHtml,
                'jenis_potongan' => $jenisHtml,
                'nominal' => $nominalHtml,
                'periode' => $periodeHtml,
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
     * Ambil data tunggal potongan pengajar
     */
    public function show($id)
    {
        $item = DB::table('potongan_pengajar as pp')
            ->leftJoin('pengajar as p', 'p.id_pengajar', '=', 'pp.id_pengajar')
            ->leftJoin('umana as u', 'u.nik', '=', 'p.nik')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'p.id_lembaga')
            ->where('pp.id_potongan_pengajar', $id)
            ->select(
                'pp.*',
                'u.nama_lengkap',
                'u.nik',
                'l.nama_lembaga',
                'p.kategori as kategori_pengajar'
            )
            ->first();

        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        return response()->json(['status' => true, 'data' => $item]);
    }

    /**
     * Simpan data log potongan pengajar baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_pengajar' => 'required',
            'jenis_potongan' => 'required',
            'nominal_potongan' => 'required|numeric|min:0',
            'min_periode_potongan' => 'required|date',
            'max_periode_potongan' => 'required|date|after_or_equal:min_periode_potongan',
        ], [
            'id_pengajar.required' => 'Pengajar wajib dipilih',
            'jenis_potongan.required' => 'Jenis potongan wajib dipilih',
            'nominal_potongan.required' => 'Nominal potongan wajib diisi',
            'min_periode_potongan.required' => 'Periode mulai wajib diisi',
            'max_periode_potongan.required' => 'Periode selesai wajib diisi',
            'max_periode_potongan.after_or_equal' => 'Periode selesai tidak boleh sebelum periode mulai',
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
            'jenis_potongan' => $request->input('jenis_potongan'),
            'nominal_potongan' => (float) $request->input('nominal_potongan'),
            'min_periode_potongan' => $request->input('min_periode_potongan'),
            'max_periode_potongan' => $request->input('max_periode_potongan'),
        ];

        $newId = DB::table('potongan_pengajar')->insertGetId($insertData);

        return response()->json([
            'status' => true,
            'message' => 'Data log potongan pengajar berhasil ditambahkan!',
            'id' => $newId,
        ]);
    }

    /**
     * Update data log potongan pengajar
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_pengajar' => 'required',
            'jenis_potongan' => 'required',
            'nominal_potongan' => 'required|numeric|min:0',
            'min_periode_potongan' => 'required|date',
            'max_periode_potongan' => 'required|date|after_or_equal:min_periode_potongan',
        ], [
            'id_pengajar.required' => 'Pengajar wajib dipilih',
            'jenis_potongan.required' => 'Jenis potongan wajib dipilih',
            'nominal_potongan.required' => 'Nominal potongan wajib diisi',
            'min_periode_potongan.required' => 'Periode mulai wajib diisi',
            'max_periode_potongan.required' => 'Periode selesai wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $validator->errors()->all()),
                'errors' => $validator->errors()
            ], 422);
        }

        $existing = DB::table('potongan_pengajar')->where('id_potongan_pengajar', $id)->first();
        if (!$existing) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        $updateData = [
            'id_pengajar' => $request->input('id_pengajar'),
            'jenis_potongan' => $request->input('jenis_potongan'),
            'nominal_potongan' => (float) $request->input('nominal_potongan'),
            'min_periode_potongan' => $request->input('min_periode_potongan'),
            'max_periode_potongan' => $request->input('max_periode_potongan'),
        ];

        DB::table('potongan_pengajar')->where('id_potongan_pengajar', $id)->update($updateData);

        return response()->json([
            'status' => true,
            'message' => 'Data log potongan pengajar berhasil diperbarui!',
        ]);
    }

    /**
     * Hapus data log potongan pengajar
     */
    public function destroy($id)
    {
        try {
            $id = (int) $id;
            $item = DB::table('potongan_pengajar')->where('id_potongan_pengajar', $id)->first();
            if (!$item) {
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json(['status' => false, 'message' => 'Data tidak ditemukan atau sudah dihapus!'], 404);
                }
                return redirect('/log/potongan-pengajar')->with('error', 'Data tidak ditemukan!');
            }

            DB::table('potongan_pengajar')->where('id_potongan_pengajar', $id)->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Data potongan pengajar #' . $id . ' berhasil dihapus!',
                ]);
            }

            return redirect('/log/potongan-pengajar')->with('success', 'Data potongan pengajar #' . $id . ' berhasil dihapus!');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
            }
            return redirect('/log/potongan-pengajar')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
