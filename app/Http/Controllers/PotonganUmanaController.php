<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PotonganUmana;
use App\Models\Penempatan;
use App\Models\Potongan;
use Illuminate\Support\Facades\Auth;

class PotonganUmanaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isRestricted = $user && !$user->isSuperAdmin() && $user->lembaga;

        if ($request->ajax()) {
            $query = Penempatan::with(['umana', 'lembaga', 'ketentuan', 'potongans.potongan'])
                ->whereHas('potongans');

            if ($isRestricted) {
                $query->where('id_lembaga', $user->lembaga);
            }

            $data = $query->get();
            return response()->json(['data' => $data]);
        }
        
        $penempatanQuery = Penempatan::with(['umana', 'lembaga', 'ketentuan']);
        if ($isRestricted) {
            $penempatanQuery->where('id_lembaga', $user->lembaga);
        }
        $penempatans = $penempatanQuery->get();
        $jenisPotongans = Potongan::all();
        
        return view('manajemen-umana.potongan-umana.index', compact('penempatans', 'jenisPotongans'));
    }

    public function getDetail($id_penempatan)
    {
        $user = Auth::user();
        if ($user && !$user->isSuperAdmin() && $user->lembaga) {
            $penempatan = Penempatan::find($id_penempatan);
            if (!$penempatan || $penempatan->id_lembaga != $user->lembaga) {
                return response()->json(['data' => []]);
            }
        }

        $data = PotonganUmana::with('potongan')
            ->where('id_penempatan', $id_penempatan)
            ->orderBy('id_potongan_umana', 'desc')
            ->get();
        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user && !$user->isSuperAdmin() && $user->lembaga) {
            $penempatan = Penempatan::find($request->id_penempatan);
            if (!$penempatan || $penempatan->id_lembaga != $user->lembaga) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak: Umana bukan dari lembaga Anda'], 403);
            }
        }

        $request->validate([
            'id_penempatan' => 'required',
            'jenis_potongan' => 'required',
            'nominal_potongan' => 'required|numeric',
            'min_periode_potongan' => 'required|date',
            'max_periode_potongan' => 'required|date'
        ]);

        PotonganUmana::create($request->all());

        return response()->json(['success' => true, 'message' => 'Potongan berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $potongan = PotonganUmana::findOrFail($id);

        if ($user && !$user->isSuperAdmin() && $user->lembaga) {
            $penempatan = Penempatan::find($potongan->id_penempatan);
            if (!$penempatan || $penempatan->id_lembaga != $user->lembaga) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
        }

        $request->validate([
            'id_penempatan' => 'required',
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
        $potongan = PotonganUmana::findOrFail($id);

        if ($user && !$user->isSuperAdmin() && $user->lembaga) {
            $penempatan = Penempatan::find($potongan->id_penempatan);
            if (!$penempatan || $penempatan->id_lembaga != $user->lembaga) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }
        }

        $potongan->delete();
        
        return response()->json(['success' => true, 'message' => 'Potongan berhasil dihapus']);
    }
}
