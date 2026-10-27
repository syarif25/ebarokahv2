<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penempatan;
use App\Models\Umana;
use App\Models\KetentuanBarokah;
use App\Models\Lembaga;
use Illuminate\Support\Facades\Storage;

class PenempatanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Penempatan::with(['umana', 'ketentuan', 'lembaga'])->get();
            return response()->json(['data' => $data]);
        }
        
        $umanas = Umana::select('nik', 'nama_lengkap', 'niy', 'status_sertifikasi')->get();
        $ketentuans = KetentuanBarokah::all();
        $lembagas = Lembaga::all();
        $transports = \App\Models\Transport::all();

        return view('manajemen-umana.penempatan.index', compact('umanas', 'ketentuans', 'lembagas', 'transports'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'id_ketentuan' => 'required',
            'id_lembaga' => 'nullable',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'nullable|date',
            'status' => 'required',
            'kategori_trans' => 'required',
            'file_sk' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ], [
            'kategori_trans.required' => 'Kolom Transport wajib dipilih.',
            'file_sk.required' => 'File SK wajib diunggah untuk penempatan baru.',
        ]);

        // Validasi Aturan: Jika sudah sertifikasi, tidak boleh dapat kehormatan dan tunj anak
        $umana = \App\Models\Umana::where('nik', $request->nik)->first();
        if ($umana) {
            $rawSertifikasi = trim(strtolower($umana->status_sertifikasi ?? ''));
            if ($rawSertifikasi !== '' && !in_array($rawSertifikasi, ['belum', 'tidak', 'tidak ada', '0', 'belum sertifikasi'])) {
                if ($request->tunj_anak === 'Ya' || $request->kehormatan === 'Ya') {
                    return response()->json([
                        'message' => 'The given data was invalid.',
                        'errors' => [
                            'sertifikasi' => ['Umana dengan status sertifikasi tidak boleh mendapatkan Tunjangan Anak dan Kehormatan.']
                        ]
                    ], 422);
                }
            }
        }

        $data = $request->except(['_token', 'file_sk']);
        $data['tunj_mp'] = $data['tunj_mp'] ?? 'Tidak';

        if ($request->hasFile('file_sk')) {
            $file = $request->file('file_sk');
            $filename = 'sk_' . \Illuminate\Support\Str::random(30) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/sk_penempatan'), $filename);
            $data['file_sk'] = $filename;
        }

        Penempatan::create($data);

        return response()->json(['success' => true, 'message' => 'Data penempatan berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nik' => 'required',
            'id_ketentuan' => 'required',
            'id_lembaga' => 'nullable',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'nullable|date',
            'status' => 'required',
            'kategori_trans' => 'required',
            'file_sk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ], [
            'kategori_trans.required' => 'Kolom Transport wajib dipilih.'
        ]);

        // Validasi Aturan: Jika sudah sertifikasi, tidak boleh dapat kehormatan dan tunj anak
        $umana = \App\Models\Umana::where('nik', $request->nik)->first();
        if ($umana) {
            $rawSertifikasi = trim(strtolower($umana->status_sertifikasi ?? ''));
            if ($rawSertifikasi !== '' && !in_array($rawSertifikasi, ['belum', 'tidak', 'tidak ada', '0', 'belum sertifikasi'])) {
                if ($request->tunj_anak === 'Ya' || $request->kehormatan === 'Ya') {
                    return response()->json([
                        'message' => 'The given data was invalid.',
                        'errors' => [
                            'sertifikasi' => ['Umana dengan status sertifikasi tidak boleh mendapatkan Tunjangan Anak dan Kehormatan. Harap ubah menjadi "Tidak".']
                        ]
                    ], 422);
                }
            }
        }

        $penempatan = Penempatan::findOrFail($id);
        $data = $request->except(['_token', '_method', 'file_sk']);
        $data['tunj_mp'] = $data['tunj_mp'] ?? 'Tidak';

        if ($request->hasFile('file_sk')) {
            $file = $request->file('file_sk');
            $filename = 'sk_' . \Illuminate\Support\Str::random(30) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/sk_penempatan'), $filename);
            $data['file_sk'] = $filename;
            
            // Delete old file if exists
            if($penempatan->file_sk && file_exists(public_path('uploads/sk_penempatan/' . $penempatan->file_sk))) {
                unlink(public_path('uploads/sk_penempatan/' . $penempatan->file_sk));
            }
        }

        $penempatan->update($data);

        return response()->json(['success' => true, 'message' => 'Data penempatan berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $penempatan = Penempatan::findOrFail($id);
        if($penempatan->file_sk && file_exists(public_path('uploads/sk_penempatan/' . $penempatan->file_sk))) {
            unlink(public_path('uploads/sk_penempatan/' . $penempatan->file_sk));
        }
        $penempatan->delete();
        
        return response()->json(['success' => true, 'message' => 'Data penempatan berhasil dihapus']);
    }
}
