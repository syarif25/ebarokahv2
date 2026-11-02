<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarokahKepanitiaan;
use App\Models\MasterJenisKegiatan;

class BarokahKepanitiaanController extends Controller
{
    /**
     * Tampilkan halaman index master barokah kepanitiaan
     */
    public function index()
    {
        $barokah = BarokahKepanitiaan::with('jenisKegiatan')
            ->orderBy('id_jenis_kegiatan', 'asc')
            ->orderBy('kolom_kategori', 'asc')
            ->get();

        $jenisKegiatan = MasterJenisKegiatan::where('is_active', 1)
            ->orderBy('nama_kegiatan', 'asc')
            ->get();

        return view('master-data.barokah-kepanitiaan.index', compact('barokah', 'jenisKegiatan'));
    }

    /**
     * Tambah data komponen baru
     */
    public function store(Request $request)
    {
        if ($request->has('nominal')) {
            $request->merge(['nominal' => str_replace('.', '', $request->nominal)]);
        }

        $request->validate([
            'id_jenis_kegiatan' => 'required|exists:master_jenis_kegiatan,id_jenis_kegiatan',
            'kolom_kategori'    => 'required|in:kepanitiaan,kehadiran,konsumsi,pemb_soal,input_soal,pengawas,penguji,korektor,input_nilai,pembimbing',
            'nama_komponen'     => 'required|string|max:150',
            'satuan'            => 'required|in:mhs,sesi,shift,hari,kosong',
            'nominal'           => 'required|numeric|min:0',
            'keterangan_aturan' => 'nullable|string|max:255',
        ]);

        BarokahKepanitiaan::create([
            'id_jenis_kegiatan' => $request->id_jenis_kegiatan,
            'kolom_kategori'    => $request->kolom_kategori,
            'nama_komponen'     => $request->nama_komponen,
            'satuan'            => $request->satuan,
            'nominal'           => $request->nominal,
            'keterangan_aturan' => $request->keterangan_aturan ?: null,
            'tahun_anggaran'    => date('Y'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data barokah kepanitiaan berhasil ditambahkan'
        ]);
    }

    /**
     * Update data komponen
     */
    public function update(Request $request, $id)
    {
        if ($request->has('nominal')) {
            $request->merge(['nominal' => str_replace('.', '', $request->nominal)]);
        }

        $request->validate([
            'id_jenis_kegiatan' => 'required|exists:master_jenis_kegiatan,id_jenis_kegiatan',
            'kolom_kategori'    => 'required|in:kepanitiaan,kehadiran,konsumsi,pemb_soal,input_soal,pengawas,penguji,korektor,input_nilai,pembimbing',
            'nama_komponen'     => 'required|string|max:150',
            'satuan'            => 'required|in:mhs,sesi,shift,hari,kosong',
            'nominal'           => 'required|numeric|min:0',
            'keterangan_aturan' => 'nullable|string|max:255',
        ]);

        $item = BarokahKepanitiaan::findOrFail($id);
        $item->update([
            'id_jenis_kegiatan' => $request->id_jenis_kegiatan,
            'kolom_kategori'    => $request->kolom_kategori,
            'nama_komponen'     => $request->nama_komponen,
            'satuan'            => $request->satuan,
            'nominal'           => $request->nominal,
            'keterangan_aturan' => $request->keterangan_aturan ?: null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data barokah kepanitiaan berhasil diperbarui'
        ]);
    }

    /**
     * Hapus data komponen
     */
    public function destroy($id)
    {
        $item = BarokahKepanitiaan::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data barokah kepanitiaan berhasil dihapus'
        ]);
    }
}
