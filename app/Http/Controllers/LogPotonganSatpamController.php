<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LogPotonganSatpamController extends Controller
{
    /**
     * Tampilkan halaman utama Log Potongan Satpam
     */
    public function index()
    {
        $jenisPotonganList = DB::table('potongan')
            ->select('id_potongan', 'nama_potongan')
            ->orderBy('nama_potongan', 'asc')
            ->get();

        return view('log.potongan-satpam.index', compact('jenisPotonganList'));
    }

    /**
     * Endpoint DataTables Server-Side
     */
    public function data(Request $request)
    {
        $query = DB::table('potongan_satpam as ps')
            ->leftJoin('satpam as s', 's.id_satpam', '=', 'ps.id_satpam')
            ->leftJoin('umana as u', 'u.nik', '=', 's.nik')
            ->leftJoin('potongan as jp', 'jp.id_potongan', '=', 'ps.jenis_potongan')
            ->select(
                'ps.id_potongan_satpam',
                'ps.id_satpam',
                'ps.jenis_potongan',
                'ps.nominal_potongan',
                'ps.min_periode_potongan',
                'ps.max_periode_potongan',
                'u.nama_lengkap',
                'u.nik',
                'jp.nama_potongan'
            );

        // Filter Jenis Potongan
        if ($filterJenis = $request->input('filter_jenis')) {
            $query->where('ps.jenis_potongan', $filterJenis);
        }

        // Search Global
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('u.nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('u.nik', 'like', "%{$search}%")
                  ->orWhere('jp.nama_potongan', 'like', "%{$search}%")
                  ->orWhere('ps.id_potongan_satpam', 'like', "%{$search}%")
                  ->orWhere('ps.id_satpam', 'like', "%{$search}%");
            });
        }

        $totalRecords = DB::table('potongan_satpam')->count();
        $filteredRecords = (clone $query)->count();

        // Ordering
        $orderColIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        $columns = [
            0 => 'ps.id_potongan_satpam',
            1 => 'ps.id_potongan_satpam',
            2 => 'u.nama_lengkap',
            3 => 'jp.nama_potongan',
            4 => 'ps.nominal_potongan',
            5 => 'ps.min_periode_potongan',
            6 => 'ps.id_potongan_satpam',
        ];

        if (isset($columns[$orderColIndex])) {
            $query->orderBy($columns[$orderColIndex], $orderDir);
        } else {
            $query->orderBy('ps.id_potongan_satpam', 'desc');
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
            $namaSatpam = $item->nama_lengkap ?: 'Satpam (ID: #' . ($item->id_satpam ?: '-') . ')';

            $namaHtml = '<div><strong>' . e($namaSatpam) . '</strong>';
            if ($item->nik) {
                $namaHtml .= '<br><small class="text-muted">' . e($item->nik) . '</small>';
            }
            $namaHtml .= '</div>';

            $jenisHtml = '<span class="badge badge-outline-primary" style="font-weight: 600;">' . e($item->nama_potongan ?: 'Jenis #' . $item->jenis_potongan) . '</span>';
            $nominalHtml = '<strong class="text-danger">Rp ' . number_format((float)$item->nominal_potongan, 0, ',', '.') . '</strong>';

            $minPer = $item->min_periode_potongan ? date('d/m/Y', strtotime($item->min_periode_potongan)) : '-';
            $maxPer = $item->max_periode_potongan ? date('d/m/Y', strtotime($item->max_periode_potongan)) : '-';
            $periodeHtml = $minPer . ' s/d ' . $maxPer;

            $actionBtn = '
                <div class="d-flex">
                    <button type="button" class="btn btn-outline-danger btn-sm shadow-sm btn-edit-potongan" style="border-radius: 20px; padding: 5px 15px;" data-id="' . $item->id_potongan_satpam . '">
                        <i class="fas fa-pencil-alt me-1" style="pointer-events: none;"></i> Edit
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-sm shadow-sm btn-delete-potongan" style="border-radius: 20px; padding: 5px 15px; margin-left: 5px;" data-id="' . $item->id_potongan_satpam . '" data-nama="' . htmlspecialchars($namaSatpam, ENT_QUOTES, 'UTF-8') . '">
                        <i class="fas fa-trash me-1" style="pointer-events: none;"></i> Hapus
                    </button>
                </div>
            ';

            $data[] = [
                'no' => '<strong>' . ($no++) . '</strong>',
                'id_potongan_satpam' => '#' . $item->id_potongan_satpam,
                'nama_satpam' => $namaHtml,
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
     * Ambil data tunggal potongan satpam
     */
    public function show($id)
    {
        $item = DB::table('potongan_satpam as ps')
            ->leftJoin('satpam as s', 's.id_satpam', '=', 'ps.id_satpam')
            ->leftJoin('umana as u', 'u.nik', '=', 's.nik')
            ->where('ps.id_potongan_satpam', $id)
            ->select(
                'ps.*',
                'u.nama_lengkap',
                'u.nik'
            )
            ->first();

        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        return response()->json(['status' => true, 'data' => $item]);
    }

    /**
     * Simpan data log potongan satpam baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_satpam' => 'required',
            'jenis_potongan' => 'required',
            'nominal_potongan' => 'required|numeric|min:0',
            'min_periode_potongan' => 'required|date',
            'max_periode_potongan' => 'required|date|after_or_equal:min_periode_potongan',
        ], [
            'id_satpam.required' => 'Satpam wajib dipilih',
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
            'id_satpam' => $request->input('id_satpam'),
            'jenis_potongan' => $request->input('jenis_potongan'),
            'nominal_potongan' => (float) $request->input('nominal_potongan'),
            'min_periode_potongan' => $request->input('min_periode_potongan'),
            'max_periode_potongan' => $request->input('max_periode_potongan'),
        ];

        $newId = DB::table('potongan_satpam')->insertGetId($insertData);

        return response()->json([
            'status' => true,
            'message' => 'Data log potongan satpam berhasil ditambahkan!',
            'id' => $newId,
        ]);
    }

    /**
     * Update data log potongan satpam
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_satpam' => 'required',
            'jenis_potongan' => 'required',
            'nominal_potongan' => 'required|numeric|min:0',
            'min_periode_potongan' => 'required|date',
            'max_periode_potongan' => 'required|date|after_or_equal:min_periode_potongan',
        ], [
            'id_satpam.required' => 'Satpam wajib dipilih',
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

        $existing = DB::table('potongan_satpam')->where('id_potongan_satpam', $id)->first();
        if (!$existing) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        $updateData = [
            'id_satpam' => $request->input('id_satpam'),
            'jenis_potongan' => $request->input('jenis_potongan'),
            'nominal_potongan' => (float) $request->input('nominal_potongan'),
            'min_periode_potongan' => $request->input('min_periode_potongan'),
            'max_periode_potongan' => $request->input('max_periode_potongan'),
        ];

        DB::table('potongan_satpam')->where('id_potongan_satpam', $id)->update($updateData);

        return response()->json([
            'status' => true,
            'message' => 'Data log potongan satpam berhasil diperbarui!',
        ]);
    }

    /**
     * Hapus data log potongan satpam
     */
    public function destroy($id)
    {
        try {
            $id = (int) $id;
            $item = DB::table('potongan_satpam')->where('id_potongan_satpam', $id)->first();
            if (!$item) {
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json(['status' => false, 'message' => 'Data tidak ditemukan atau sudah dihapus!'], 404);
                }
                return redirect('/log/potongan-satpam')->with('error', 'Data tidak ditemukan!');
            }

            DB::table('potongan_satpam')->where('id_potongan_satpam', $id)->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Data potongan satpam #' . $id . ' berhasil dihapus!',
                ]);
            }

            return redirect('/log/potongan-satpam')->with('success', 'Data potongan satpam #' . $id . ' berhasil dihapus!');
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
            }
            return redirect('/log/potongan-satpam')->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * AJAX Search untuk Select2 Satpam
     */
    public function searchSatpam(Request $request)
    {
        $q = $request->input('q');

        $query = DB::table('satpam as s')
            ->leftJoin('umana as u', 'u.nik', '=', 's.nik')
            ->select('s.id_satpam', 'u.nama_lengkap', 'u.nik');

        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('u.nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('u.nik', 'like', "%{$q}%")
                    ->orWhere('s.id_satpam', '=', $q);
            });
        }

        $results = $query->limit(35)->get();

        $data = $results->map(function ($item) {
            $label = ($item->nama_lengkap ?: 'Satpam') . ' [ID: ' . $item->id_satpam . ', NIK: ' . ($item->nik ?: '-') . ']';
            return [
                'id' => $item->id_satpam,
                'text' => $label,
                'nama' => $item->nama_lengkap,
                'nik' => $item->nik,
            ];
        });

        return response()->json($data);
    }
}
