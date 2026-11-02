<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\MasterJenisKegiatan;
use App\Models\BarokahKepanitiaan;

class JenisKegiatanController extends Controller
{
    /**
     * Tampilkan daftar jenis kegiatan
     */
    public function index()
    {
        $kegiatan = MasterJenisKegiatan::withCount('tarif')
            ->orderBy('id_jenis_kegiatan', 'asc')
            ->get();

        return view('master-data.jenis-kegiatan.index', compact('kegiatan'));
    }

    /**
     * Simpan jenis kegiatan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:150',
            'tingkat'       => 'required|in:dikdasmen,dikti,pesantren',
            'keterangan'    => 'nullable|string',
        ]);

        $baseCode = Str::slug($request->nama_kegiatan, '_') ?: 'kegiatan_' . time();
        $kode = $baseCode;
        $counter = 1;
        while (MasterJenisKegiatan::where('kode_kegiatan', $kode)->exists()) {
            $kode = $baseCode . '_' . $counter++;
        }

        MasterJenisKegiatan::create([
            'nama_kegiatan' => $request->nama_kegiatan,
            'kode_kegiatan' => $kode,
            'tingkat'       => $request->tingkat,
            'keterangan'    => $request->keterangan ?: null,
            'is_active'     => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jenis kegiatan kepanitiaan berhasil ditambahkan'
        ]);
    }

    /**
     * Update jenis kegiatan
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:150',
            'tingkat'       => 'required|in:dikdasmen,dikti,pesantren',
            'keterangan'    => 'nullable|string',
        ]);

        $kegiatan = MasterJenisKegiatan::findOrFail($id);
        $kegiatan->update([
            'nama_kegiatan' => $request->nama_kegiatan,
            'tingkat'       => $request->tingkat,
            'keterangan'    => $request->keterangan ?: null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jenis kegiatan kepanitiaan berhasil diperbarui'
        ]);
    }

    /**
     * Hapus jenis kegiatan
     */
    public function destroy($id)
    {
        $kegiatan = MasterJenisKegiatan::withCount('tarif', 'transaksi')->findOrFail($id);

        if ($kegiatan->transaksi_count > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus kegiatan ini karena sudah memiliki riwayat transaksi panitia.'
            ], 422);
        }

        return DB::transaction(function () use ($kegiatan, $id) {
            BarokahKepanitiaan::where('id_jenis_kegiatan', $id)->delete();
            $kegiatan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jenis kegiatan kepanitiaan berhasil dihapus'
            ]);
        });
    }
}
