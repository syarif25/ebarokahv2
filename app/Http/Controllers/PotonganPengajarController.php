<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PotonganPengajar;
use App\Models\Pengajar;
use App\Models\Potongan;
use Illuminate\Support\Facades\Auth;

class PotonganPengajarController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isRestricted = $user && !$user->isSuperAdmin() && $user->lembaga;

        if ($request->ajax()) {
            $query = Pengajar::with(['umana', 'lembaga', 'potongans.potongan'])
                ->whereHas('potongans');

            if ($isRestricted) {
                $query->where('id_lembaga', $user->lembaga);
            }

            $data = $query->get();
            return response()->json(['data' => $data]);
        }
        
        $pengajarQuery = Pengajar::with(['umana', 'lembaga']);
        if ($isRestricted) {
            $pengajarQuery->where('id_lembaga', $user->lembaga);
        }
        $pengajars = $pengajarQuery->get();
        $jenisPotongans = Potongan::all();
        
        return view('manajemen-pengajar.potongan-pengajar.index', compact('pengajars', 'jenisPotongans'));
    }

    public function getDetail($id_pengajar)
    {
        $user = Auth::user();
        if ($user && !$user->isSuperAdmin() && $user->lembaga) {
            $pengajar = Pengajar::find($id_pengajar);
            if (!$pengajar || $pengajar->id_lembaga != $user->lembaga) {
                return response()->json(['data' => []]);
            }
        }

        $data = PotonganPengajar::with('potongan')
            ->where('id_pengajar', $id_pengajar)
            ->orderBy('id_potongan_pengajar', 'desc')
            ->get();
        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user && !$user->isSuperAdmin() && $user->lembaga) {
            $pengajar = Pengajar::find($request->id_pengajar);
            if (!$pengajar || $pengajar->id_lembaga != $user->lembaga) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak: Pengajar bukan dari lembaga Anda'], 403);
            }
        }

        $request->validate([
            'id_pengajar' => 'required',
            'jenis_potongan' => 'required',
            'nominal_potongan' => 'required|numeric',
            'min_periode_potongan' => 'required|date',
            'max_periode_potongan' => 'required|date'
        ]);

        PotonganPengajar::create($request->all());

        return response()->json(['success' => true, 'message' => 'Potongan berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $potongan = PotonganPengajar::findOrFail($id);

        if ($user && !$user->isSuperAdmin() && $user->lembaga) {
            $pengajar = Pengajar::find($potongan->id_pengajar);
            if (!$pengajar || $pengajar->id_lembaga != $user->lembaga) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
        }

        $request->validate([
            'id_pengajar' => 'required',
            'jenis_potongan' => 'required',
            'nominal_potongan' => 'required|numeric',
            'min_periode_potongan' => 'required|date',
            'max_periode_potongan' => 'required|date'
        ]);

        $potongan->update($request->all());

        return response()->json(['success' => true, 'message' => 'Potongan berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $potongan = PotonganPengajar::findOrFail($id);

        if ($user && !$user->isSuperAdmin() && $user->lembaga) {
            $pengajar = Pengajar::find($potongan->id_pengajar);
            if (!$pengajar || $pengajar->id_lembaga != $user->lembaga) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
        }

        $potongan->delete();
        
        return response()->json(['success' => true, 'message' => 'Potongan berhasil dihapus']);
    }
}
