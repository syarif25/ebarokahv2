<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajar;
use App\Models\Umana;
use App\Models\Lembaga;
use App\Models\Prodi;
use Illuminate\Support\Facades\Auth;

class PengajarController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isRestricted = $user && !$user->isSuperAdmin() && $user->lembaga;

        if ($request->ajax()) {
            $query = Pengajar::with(['umana', 'lembaga', 'prodi'])->orderBy('id_pengajar', 'desc');
            if ($isRestricted) {
                $query->where('id_lembaga', $user->lembaga);
            }
            $data = $query->get();
            return response()->json(['data' => $data]);
        }
        
        $countQuery = Pengajar::query();
        if ($isRestricted) {
            $countQuery->where('id_lembaga', $user->lembaga);
        }

        $totalGuru = (clone $countQuery)->whereIn('kategori', ['GTY', 'GTT'])->count();
        $gty = (clone $countQuery)->where('kategori', 'GTY')->count();
        $gtt = (clone $countQuery)->where('kategori', 'GTT')->count();
        
        $totalDosen = (clone $countQuery)->whereIn('kategori', ['DTY', 'DTT'])->count();
        $dty = (clone $countQuery)->where('kategori', 'DTY')->count();
        $dtt = (clone $countQuery)->where('kategori', 'DTT')->count();
        
        $umanas = Umana::all();
        if ($isRestricted) {
            $lembagas = Lembaga::where('id_lembaga', $user->lembaga)->get();
        } else {
            $lembagas = Lembaga::all();
        }
        $prodis = Prodi::all();
        $transports = \App\Models\Transport::all();
        $masterWalkes = \DB::table('master_tarif_walkes')->get();

        return view('manajemen-pengajar.penempatan.index', compact(
            'totalGuru', 'gty', 'gtt', 'totalDosen', 'dty', 'dtt',
            'umanas', 'lembagas', 'prodis', 'transports', 'masterWalkes'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user && !$user->isSuperAdmin() && $user->lembaga) {
            $request->merge(['id_lembaga' => $user->lembaga]);
        }

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
        $data['kategori_trans'] = $request->kategori_trans ?: null;
        $data['tgl_mulai'] = $request->tgl_mulai ?: date('Y-01-01');
        $data['tgl_selesai'] = $request->tgl_selesai ?: date('2040-01-01');
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
        $user = Auth::user();
        $isRestricted = $user && !$user->isSuperAdmin() && $user->lembaga;

        if ($isRestricted) {
            $request->merge(['id_lembaga' => $user->lembaga]);
        }

        $request->validate([
            'nik' => 'required',
            'kategori' => 'required',
            'status' => 'required',
            'id_lembaga' => 'required',
            'jumlah_sks' => 'required|numeric'
        ]);

        $pengajar = Pengajar::findOrFail($id);
        if ($isRestricted && $pengajar->id_lembaga != $user->lembaga) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki hak akses mengubah pengajar lembaga lain.'], 403);
        }
        
        $data = $request->all();
        $data['id_prodi'] = $request->id_prodi ?: null;
        $data['kategori_trans'] = $request->kategori_trans ?: null;
        if (!empty($request->tgl_mulai)) $data['tgl_mulai'] = $request->tgl_mulai;
        if (!empty($request->tgl_selesai)) $data['tgl_selesai'] = $request->tgl_selesai;
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
        $user = Auth::user();
        $pengajar = Pengajar::findOrFail($id);

        if ($user && !$user->isSuperAdmin() && $user->lembaga && $pengajar->id_lembaga != $user->lembaga) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki hak akses menghapus pengajar lembaga lain.'], 403);
        }

        $pengajar->delete();
        
        return response()->json(['success' => true, 'message' => 'Data Pengajar berhasil dihapus']);
    }
}
