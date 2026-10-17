<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajar;
use App\Models\Umana;
use App\Models\Lembaga;
use App\Models\Prodi;

class PengajarController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pengajar::with(['umana', 'lembaga', 'prodi'])->orderBy('id_pengajar', 'desc')->get();
            return response()->json(['data' => $data]);
        }
        
        $totalGuru = Pengajar::whereIn('kategori', ['GTY', 'GTT'])->count();
        $gty = Pengajar::where('kategori', 'GTY')->count();
        $gtt = Pengajar::where('kategori', 'GTT')->count();
        
        $totalDosen = Pengajar::whereIn('kategori', ['DTY', 'DTT'])->count();
        $dty = Pengajar::where('kategori', 'DTY')->count();
        $dtt = Pengajar::where('kategori', 'DTT')->count();
        
        $umanas = Umana::all();
        $lembagas = Lembaga::all();
        $prodis = Prodi::all();
        $masterWalkes = \DB::table('master_tarif_walkes')->get();

        return view('manajemen-pengajar.penempatan.index', compact(
            'totalGuru', 'gty', 'gtt', 'totalDosen', 'dty', 'dtt',
            'umanas', 'lembagas', 'prodis', 'masterWalkes'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'kategori' => 'required',
            'status' => 'required',
            'id_lembaga' => 'required',
            'jumlah_sks' => 'required|numeric',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date'
        ]);

        $data = $request->all();
        // Set default values for optional and tunjangan fields
        $data['id_prodi'] = $request->id_prodi ?: null;
        $data['tunj_kel'] = $request->tunj_kel ?: 'Tidak';
        $data['tunj_anak'] = $request->tunj_anak ?: 'Tidak';
        $data['kehormatan'] = $request->kehormatan ?: 'Tidak';
        $data['jafung'] = $request->jafung ?: 'Tidak';
        $data['walkes'] = $request->walkes ?: 'Tidak';

        Pengajar::create($data);

        return response()->json(['success' => true, 'message' => 'Data Pengajar berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nik' => 'required',
            'kategori' => 'required',
            'status' => 'required',
            'id_lembaga' => 'required',
            'jumlah_sks' => 'required|numeric',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date'
        ]);

        $pengajar = Pengajar::findOrFail($id);
        
        $data = $request->all();
        $data['id_prodi'] = $request->id_prodi ?: null;
        $data['tunj_kel'] = $request->tunj_kel ?: 'Tidak';
        $data['tunj_anak'] = $request->tunj_anak ?: 'Tidak';
        $data['kehormatan'] = $request->kehormatan ?: 'Tidak';
        $data['jafung'] = $request->jafung ?: 'Tidak';
        $data['walkes'] = $request->walkes ?: 'Tidak';

        $pengajar->update($data);

        return response()->json(['success' => true, 'message' => 'Data Pengajar berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $pengajar = Pengajar::findOrFail($id);
        $pengajar->delete();
        
        return response()->json(['success' => true, 'message' => 'Data Pengajar berhasil dihapus']);
    }
}
