<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TBebanKerja;
use App\Models\Penempatan;

class TBebanKerjaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = TBebanKerja::with(['penempatan.umana', 'penempatan.lembaga'])
                ->orderBy('id_tbk', 'desc')
                ->get();
            return response()->json(['data' => $data]);
        }
        
        $penempatans = Penempatan::with(['umana', 'lembaga'])->get();
        return view('manajemen-umana.tunjangan-beban-kerja.index', compact('penempatans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_penempatan' => 'required',
            'jenis_tbk' => 'required|string|max:255',
            'nominal_tbk' => 'required|numeric',
            'min_periode' => 'required|date',
            'max_periode' => 'required|date'
        ]);

        TBebanKerja::create($request->all());

        return response()->json(['success' => true, 'message' => 'Data Tunjangan Beban Kerja berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_penempatan' => 'required',
            'jenis_tbk' => 'required|string|max:255',
            'nominal_tbk' => 'required|numeric',
            'min_periode' => 'required|date',
            'max_periode' => 'required|date'
        ]);

        $tbk = TBebanKerja::findOrFail($id);
        $tbk->update($request->all());

        return response()->json(['success' => true, 'message' => 'Data Tunjangan Beban Kerja berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $tbk = TBebanKerja::findOrFail($id);
        $tbk->delete();
        
        return response()->json(['success' => true, 'message' => 'Data Tunjangan Beban Kerja berhasil dihapus']);
    }
}
