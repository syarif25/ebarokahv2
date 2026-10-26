<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LogPotonganUmanaController extends Controller
{
    /**
     * Tampilkan halaman utama Log Potongan Umana
     */
    public function index()
    {
        $jenisPotonganList = DB::table('potongan')
            ->select('id_potongan', 'nama_potongan')
            ->orderBy('nama_potongan', 'asc')
            ->get();

        $lembagaList = DB::table('lembaga')
            ->select('id_lembaga', 'nama_lembaga')
            ->orderBy('nama_lembaga', 'asc')
            ->get();

        return view('log.potongan-umana.index', compact('jenisPotonganList', 'lembagaList'));
    }

    /**
     * Endpoint DataTables Server-Side
     */
    public function data(Request $request)
    {
        $query = DB::table('potongan_umana as pu')
            ->leftJoin('penempatan as p', 'p.id_penempatan', '=', 'pu.id_penempatan')
            ->leftJoin('umana as u', 'u.nik', '=', 'p.nik')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'p.id_lembaga')
            ->leftJoin('potongan as jp', 'jp.id_potongan', '=', 'pu.jenis_potongan')
            ->select(
                'pu.id_potongan_umana',
                'pu.id_penempatan',
                'pu.jenis_potongan',
                'pu.nominal_potongan',
                'pu.min_periode_potongan',
                'pu.max_periode_potongan',
                'u.nama_lengkap',
                'u.nik',
                'l.nama_lembaga',
                'p.jabatan_lembaga',
                'jp.nama_potongan'
            );

        // Filter Lembaga
        if ($filterLembaga = $request->input('filter_lembaga')) {
            $query->where('p.id_lembaga', $filterLembaga);
        }

        // Filter Jenis Potongan
        if ($filterJenis = $request->input('filter_jenis')) {
            $query->where('pu.jenis_potongan', $filterJenis);
        }

        // Search Global
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('u.nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('u.nik', 'like', "%{$search}%")
                  ->orWhere('l.nama_lembaga', 'like', "%{$search}%")
                  ->orWhere('p.jabatan_lembaga', 'like', "%{$search}%")
                  ->orWhere('jp.nama_potongan', 'like', "%{$search}%")
                  ->orWhere('pu.id_potongan_umana', 'like', "%{$search}%")
                  ->orWhere('pu.id_penempatan', 'like', "%{$search}%");
            });
        }

        $totalRecords = DB::table('potongan_umana')->count();
        $filteredRecords = (clone $query)->count();

        // Ordering
        $orderColIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        $columns = [
            0 => 'pu.id_potongan_umana',
            1 => 'pu.id_potongan_umana',
            2 => 'u.nama_lengkap',
            3 => 'l.nama_lembaga',
            4 => 'jp.nama_potongan',
            5 => 'pu.nominal_potongan',
            6 => 'pu.min_periode_potongan',
            7 => 'pu.id_potongan_umana',
        ];

        if (isset($columns[$orderColIndex])) {
            $query->orderBy($columns[$orderColIndex], $orderDir);
        } else {
            $query->orderBy('pu.id_potongan_umana', 'desc');
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
            $namaUmana = $item->nama_lengkap ?: 'Umana (ID Penempatan: #' . ($item->id_penempatan ?: '-') . ')';
            $jabatan = $item->jabatan_lembaga ?: '';
            $lembaga = $item->nama_lembaga ?: '-';

            $namaHtml = '<div><strong>' . e($namaUmana) . '</strong>';
            if ($item->nik || ($jabatan && $jabatan !== '-')) {
                $sub = [];
                if ($item->nik) $sub[] = e($item->nik);
                if ($jabatan && $jabatan !== '-') $sub[] = e($jabatan);
                $namaHtml .= '<br><small class="text-muted">' . implode(' &bull; ', $sub) . '</small>';
            }
            $namaHtml .= '</div>';

            $lembagaHtml = e($lembaga);
            $jenisHtml = '<span class="badge badge-outline-primary" style="font-weight: 600;">' . e($item->nama_potongan ?: 'Jenis #' . $item->jenis_potongan) . '</span>';
            $nominalHtml = '<strong class="text-danger">Rp ' . number_format((float)$item->nominal_potongan, 0, ',', '.') . '</strong>';

            $minPer = $item->min_periode_potongan ? date('d/m/Y', strtotime($item->min_periode_potongan)) : '-';
            $maxPer = $item->max_periode_potongan ? date('d/m/Y', strtotime($item->max_periode_potongan)) : '-';
            $periodeHtml = $minPer . ' s/d ' . $maxPer;

            $actionBtn = '
                <div class="d-flex">
                    <button type="button" class="btn btn-outline-danger btn-sm shadow-sm btn-edit-potongan" style="border-radius: 20px; padding: 5px 15px;" data-id="' . $item->id_potongan_umana . '">
                        <i class="fas fa-pencil-alt me-1" style="pointer-events: none;"></i> Edit
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-sm shadow-sm btn-delete-potongan" style="border-radius: 20px; padding: 5px 15px; margin-left: 5px;" data-id="' . $item->id_potongan_umana . '" data-nama="' . htmlspecialchars($namaUmana, ENT_QUOTES, 'UTF-8') . '">
                        <i class="fas fa-trash me-1" style="pointer-events: none;"></i> Hapus
                    </button>
                </div>
            ';

            $data[] = [
                'no' => '<strong>' . ($no++) . '</strong>',
                'id_potongan_umana' => '#' . $item->id_potongan_umana,
                'nama_umana' => $namaHtml,
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
     * Ambil data tunggal potongan umana
     */
    public function show($id)
    {
        $item = DB::table('potongan_umana as pu')
            ->leftJoin('penempatan as p', 'p.id_penempatan', '=', 'pu.id_penempatan')
            ->leftJoin('umana as u', 'u.nik', '=', 'p.nik')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'p.id_lembaga')
            ->where('pu.id_potongan_umana', $id)
            ->select(
                'pu.*',
                'u.nama_lengkap',
                'u.nik',
                'l.nama_lembaga',
                'p.jabatan_lembaga'
            )
            ->first();

        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        return response()->json(['status' => true, 'data' => $item]);
    }

    /**
     * Simpan data log potongan umana baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_penempatan' => 'required',
            'jenis_potongan' => 'required',
            'nominal_potongan' => 'required|numeric|min:0',
            'min_periode_potongan' => 'required|date',
            'max_periode_potongan' => 'required|date|after_or_equal:min_periode_potongan',
        ], [
            'id_penempatan.required' => 'Penempatan / Umana wajib dipilih',
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
            'id_penempatan' => $request->input('id_penempatan'),
            'jenis_potongan' => $request->input('jenis_potongan'),
            'nominal_potongan' => (float) $request->input('nominal_potongan'),
            'min_periode_potongan' => $request->input('min_periode_potongan'),
            'max_periode_potongan' => $request->input('max_periode_potongan'),
        ];

        $newId = DB::table('potongan_umana')->insertGetId($insertData);

        return response()->json([
            'status' => true,
            'message' => 'Data log potongan umana berhasil ditambahkan!',
            'id' => $newId,
        ]);
    }

    /**
     * Update data log potongan umana
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_penempatan' => 'required',
            'jenis_potongan' => 'required',
            'nominal_potongan' => 'required|numeric|min:0',
            'min_periode_potongan' => 'required|date',
            'max_periode_potongan' => 'required|date|after_or_equal:min_periode_potongan',
        ], [
            'id_penempatan.required' => 'Penempatan / Umana wajib dipilih',
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

        $existing = DB::table('potongan_umana')->where('id_potongan_umana', $id)->first();
        if (!$existing) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        $updateData = [
            'id_penempatan' => $request->input('id_penempatan'),
            'jenis_potongan' => $request->input('jenis_potongan'),
            'nominal_potongan' => (float) $request->input('nominal_potongan'),
            'min_periode_potongan' => $request->input('min_periode_potongan'),
            'max_periode_potongan' => $request->input('max_periode_potongan'),
        ];

        DB::table('potongan_umana')->where('id_potongan_umana', $id)->update($updateData);

        return response()->json([
            'status' => true,
            'message' => 'Data log potongan umana berhasil diperbarui!',
        ]);
    }

    /**
     * Hapus data log potongan umana
     */
    public function destroy($id)
    {
        try {
            $id = (int) $id;
            $item = DB::table('potongan_umana')->where('id_potongan_umana', $id)->first();
            if (!$item) {
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json(['status' => false, 'message' => 'Data tidak ditemukan atau sudah dihapus!'], 404);
                }
                return redirect('/log/potongan-umana')->with('error', 'Data tidak ditemukan!');
            }

            DB::table('potongan_umana')->where('id_potongan_umana', $id)->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Data potongan umana #' . $id . ' berhasil dihapus!',
                ]);
            }

            return redirect('/log/potongan-umana')->with('success', 'Data potongan umana #' . $id . ' berhasil dihapus!');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
            }
            return redirect('/log/potongan-umana')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
