<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PotonganPengajar;
use App\Models\Pengajar;
use App\Models\Potongan;

class PotonganPengajarController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pengajar::with(['umana', 'lembaga', 'potongans.potongan'])
                ->whereHas('potongans')
                ->get();
            return response()->json(['data' => $data]);
        }
        
        $pengajars = Pengajar::with(['umana', 'lembaga'])->get();
        $jenisPotongans = Potongan::all();
        
        return view('manajemen-pengajar.potongan-pengajar.index', compact('pengajars', 'jenisPotongans'));
    }

    public function getDetail($id_pengajar)
    {
        $data = PotonganPengajar::with('potongan')
            ->where('id_pengajar', $id_pengajar)
            ->orderBy('id_potongan_pengajar', 'desc')
            ->get();
        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
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
        $request->validate([
            'id_pengajar' => 'required',
            'jenis_potongan' => 'required',
            'nominal_potongan' => 'required|numeric',
            'min_periode_potongan' => 'required|date',
            'max_periode_potongan' => 'required|date'
        ]);

        $potongan = PotonganPengajar::findOrFail($id);
        $potongan->update($request->all());

        return response()->json(['success' => true, 'message' => 'Potongan berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $potongan = PotonganPengajar::findOrFail($id);
        $potongan->delete();
        
        return response()->json(['success' => true, 'message' => 'Potongan berhasil dihapus']);
    }
}
