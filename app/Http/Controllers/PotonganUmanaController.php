<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PotonganUmana;
use App\Models\Penempatan;
use App\Models\Potongan;

class PotonganUmanaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Penempatan::with(['umana', 'lembaga', 'ketentuan', 'potongans.potongan'])
                ->whereHas('potongans')
                ->get();
            return response()->json(['data' => $data]);
        }
        
        $penempatans = Penempatan::with(['umana', 'lembaga', 'ketentuan'])->get();
        $jenisPotongans = Potongan::all();
        
        return view('manajemen-umana.potongan-umana.index', compact('penempatans', 'jenisPotongans'));
    }

    public function getDetail($id_penempatan)
    {
        $data = PotonganUmana::with('potongan')
            ->where('id_penempatan', $id_penempatan)
            ->orderBy('id_potongan_umana', 'desc')
            ->get();
        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
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
        $request->validate([
            'id_penempatan' => 'required',
            'jenis_potongan' => 'required',
            'nominal_potongan' => 'required|numeric',
            'min_periode_potongan' => 'required|date',
            'max_periode_potongan' => 'required|date'
        ]);

        $potongan = PotonganUmana::findOrFail($id);
        $potongan->update($request->all());

        return response()->json(['success' => true, 'message' => 'Potongan berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $potongan = PotonganUmana::findOrFail($id);
        $potongan->delete();
        
        return response()->json(['success' => true, 'message' => 'Potongan berhasil dihapus']);
    }
}
