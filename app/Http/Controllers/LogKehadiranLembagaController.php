<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LogKehadiranLembagaController extends Controller
{
    public function index()
    {
        $bulanList = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        // Daftar tahun dari kehadiran_lembaga
        $tahunList = DB::table('kehadiran_lembaga')
            ->select('tahun')
            ->whereNotNull('tahun')
            ->where('tahun', '!=', '')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        $lembagaList = DB::table('lembaga')
            ->select('id_lembaga', 'nama_lembaga')
            ->orderBy('nama_lembaga', 'asc')
            ->get();

        return view('log.kehadiran-lembaga.index', compact('bulanList', 'tahunList', 'lembagaList'));
    }

    public function data(Request $request)
    {
        $query = DB::table('kehadiran_lembaga as kl')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'kl.id_lembaga')
            ->select(
                'kl.id_kehadiran_lembaga',
                'kl.id_lembaga',
                'l.nama_lembaga',
                'kl.kategori',
                'kl.bulan',
                'kl.tahun',
                'kl.status',
                'kl.jumlah_total',
                'kl.catatan_umum_pimpinan',
                'kl.file',
                'kl.tgl_input'
            );

        // Filter lembaga
        if ($filterLembaga = $request->input('filter_lembaga')) {
            $query->where('kl.id_lembaga', $filterLembaga);
        }

        // Filter kategori
        if ($filterKategori = $request->input('filter_kategori')) {
            $query->where('kl.kategori', $filterKategori);
        }

        // Filter bulan
        if ($filterBulan = $request->input('filter_bulan')) {
            $query->where('kl.bulan', $filterBulan);
        }

        // Filter tahun
        if ($filterTahun = $request->input('filter_tahun')) {
            $query->where('kl.tahun', $filterTahun);
        }

        // Filter status
        if ($filterStatus = $request->input('filter_status')) {
            $query->where('kl.status', $filterStatus);
        }

        // Search global DataTables
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('l.nama_lembaga', 'like', "%{$search}%")
                  ->orWhere('kl.kategori', 'like', "%{$search}%")
                  ->orWhere('kl.bulan', 'like', "%{$search}%")
                  ->orWhere('kl.tahun', 'like', "%{$search}%")
                  ->orWhere('kl.status', 'like', "%{$search}%")
                  ->orWhere('kl.id_kehadiran_lembaga', 'like', "%{$search}%");
            });
        }

        $totalRecords = DB::table('kehadiran_lembaga')->count();
        $filteredRecords = (clone $query)->count();

        // Ordering
        $orderColIndex = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        $columns = [
            0 => 'kl.id_kehadiran_lembaga',
            1 => 'kl.id_kehadiran_lembaga',
            2 => 'l.nama_lembaga',
            3 => 'kl.kategori',
            4 => 'kl.bulan',
            5 => 'kl.tahun',
            6 => 'kl.jumlah_total',
            7 => 'kl.status',
            8 => 'kl.file',
            9 => 'kl.id_kehadiran_lembaga',
        ];

        if (isset($columns[$orderColIndex])) {
            $query->orderBy($columns[$orderColIndex], $orderDir);
        } else {
            $query->orderBy('kl.id_kehadiran_lembaga', 'desc');
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
            // Badge Kategori
            $katClass = 'badge badge-kategori-default';
            if ($item->kategori === 'Struktural') {
                $katClass = 'badge badge-kategori-struktural';
            } elseif ($item->kategori === 'Pengajar') {
                $katClass = 'badge badge-kategori-pengajar';
            } elseif ($item->kategori === 'Satpam') {
                $katClass = 'badge badge-kategori-satpam';
            }

            // Badge Status
            $st = strtolower($item->status);
            $statusBadge = '<span class="badge bg-secondary text-white">' . e($item->status) . '</span>';
            if ($st === 'selesai') {
                $statusBadge = '<span class="badge badge-status-selesai"><i class="fas fa-check-circle me-1"></i>Selesai</span>';
            } elseif ($st === 'acc') {
                $statusBadge = '<span class="badge badge-status-acc"><i class="fas fa-thumbs-up me-1"></i>ACC</span>';
            } elseif ($st === 'sudah' || $st === 'terkirim') {
                $statusBadge = '<span class="badge badge-status-terkirim"><i class="fas fa-paper-plane me-1"></i>' . e($item->status) . '</span>';
            } elseif ($st === 'belum') {
                $statusBadge = '<span class="badge badge-status-belum"><i class="fas fa-hourglass-half me-1"></i>Belum</span>';
            }

            // File button
            $fileBtn = '<span class="text-muted">-</span>';
            if (!empty($item->file)) {
                $fileUrl = asset('upload/' . $item->file);
                $fileBtn = '<a href="' . $fileUrl . '" target="_blank" class="btn btn-xs btn-outline-info d-inline-flex align-items-center gap-1" title="Lihat File">
                    <i class="fas fa-file-pdf"></i> PDF
                </a>';
            }

            // Nama Lembaga
            $namaLembaga = $item->nama_lembaga ?: ($item->kategori === 'Satpam' ? 'Satpam' : 'Lembaga #' . $item->id_lembaga);

            // Action buttons
            $actionBtn = '
                <div class="d-flex justify-content-center gap-1">
                    <button type="button" class="btn btn-warning btn-xs text-white" onclick="editData(' . $item->id_kehadiran_lembaga . ')" title="Edit">
                        <i class="fas fa-pencil-alt"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-xs" onclick="deleteData(' . $item->id_kehadiran_lembaga . ', \'' . addslashes($namaLembaga) . '\')" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            ';

            $data[] = [
                'no' => $no++,
                'id_kehadiran_lembaga' => '#' . $item->id_kehadiran_lembaga,
                'nama_lembaga' => '<span class="fw-bold text-dark">' . e($namaLembaga) . '</span>',
                'kategori' => '<span class="' . $katClass . '">' . e($item->kategori) . '</span>',
                'bulan' => '<span class="fw-semibold">' . e($item->bulan ?: '-') . '</span>',
                'tahun' => '<span class="fw-semibold">' . e($item->tahun ?: '-') . '</span>',
                'jumlah_total' => '<span class="fw-bold text-dark">Rp ' . number_format($item->jumlah_total, 0, ',', '.') . '</span>',
                'status' => $statusBadge,
                'file' => $fileBtn,
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

    public function show($id)
    {
        $item = DB::table('kehadiran_lembaga')
            ->where('id_kehadiran_lembaga', $id)
            ->first();

        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        return response()->json(['status' => true, 'data' => $item]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_lembaga' => 'nullable|integer',
            'kategori' => 'required|in:Struktural,Pengajar,Satpam',
            'bulan' => 'required|string',
            'tahun' => 'required|string',
            'status' => 'required|string',
            'jumlah_total' => 'nullable|string',
            'file_upload' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $validator->errors()->all()),
                'errors' => $validator->errors()
            ], 422);
        }

        // Sanitasi jumlah_total jika ada format rupiah titik
        $jumlahTotal = $request->input('jumlah_total', 0);
        $jumlahTotal = (int) preg_replace('/[^0-9]/', '', (string)$jumlahTotal);

        $fileName = '';
        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            $fileName = Str::random(40) . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload'), $fileName);
        }

        $insertData = [
            'id_lembaga' => $request->input('id_lembaga') ?: null,
            'kategori' => $request->input('kategori'),
            'bulan' => $request->input('bulan'),
            'tahun' => $request->input('tahun'),
            'status' => $request->input('status'),
            'jumlah_total' => $jumlahTotal,
            'catatan_umum_pimpinan' => $request->input('catatan_umum_pimpinan'),
            'file' => $fileName,
            'tgl_input' => now(),
        ];

        $insertId = DB::table('kehadiran_lembaga')->insertGetId($insertData);

        return response()->json([
            'status' => true,
            'message' => 'Data kehadiran lembaga berhasil ditambahkan!',
            'id' => $insertId,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_lembaga' => 'nullable|integer',
            'kategori' => 'required|in:Struktural,Pengajar,Satpam',
            'bulan' => 'required|string',
            'tahun' => 'required|string',
            'status' => 'required|string',
            'jumlah_total' => 'nullable|string',
            'file_upload' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $validator->errors()->all()),
                'errors' => $validator->errors()
            ], 422);
        }

        $existing = DB::table('kehadiran_lembaga')
            ->where('id_kehadiran_lembaga', $id)
            ->first();

        if (!$existing) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        $jumlahTotal = $request->input('jumlah_total', 0);
        $jumlahTotal = (int) preg_replace('/[^0-9]/', '', (string)$jumlahTotal);

        $fileName = $existing->file;
        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            $fileName = Str::random(40) . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload'), $fileName);
        }

        $updateData = [
            'id_lembaga' => $request->input('id_lembaga') ?: null,
            'kategori' => $request->input('kategori'),
            'bulan' => $request->input('bulan'),
            'tahun' => $request->input('tahun'),
            'status' => $request->input('status'),
            'jumlah_total' => $jumlahTotal,
            'catatan_umum_pimpinan' => $request->input('catatan_umum_pimpinan'),
            'file' => $fileName,
        ];

        DB::table('kehadiran_lembaga')
            ->where('id_kehadiran_lembaga', $id)
            ->update($updateData);

        return response()->json([
            'status' => true,
            'message' => 'Data kehadiran lembaga berhasil diperbarui!',
        ]);
    }

    public function destroy($id)
    {
        $item = DB::table('kehadiran_lembaga')
            ->where('id_kehadiran_lembaga', $id)
            ->first();

        if (!$item) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan!'], 404);
        }

        DB::table('kehadiran_lembaga')
            ->where('id_kehadiran_lembaga', $id)
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data kehadiran lembaga #' . $id . ' berhasil dihapus!',
        ]);
    }
}
