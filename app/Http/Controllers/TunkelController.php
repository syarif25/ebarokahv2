<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tunkel;

class TunkelController extends Controller
{
    public function index()
    {
        $tunkel = Tunkel::all();
        return view('master-data.tunkel.index', compact('tunkel'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tunkel' => 'required|string|max:100',
            'besaran_tunkel' => 'required|numeric'
        ]);

        Tunkel::create($request->except('_token'));

        return response()->json(['success' => true, 'message' => 'Tunjangan keluarga berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_tunkel' => 'required|string|max:100',
            'besaran_tunkel' => 'required|numeric'
        ]);

        Tunkel::where('id_tunkel', $id)->update($request->except('_token'));

        return response()->json(['success' => true, 'message' => 'Tunjangan keluarga berhasil diperbarui']);
    }

    public function destroy($id)
    {
        Tunkel::where('id_tunkel', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Tunjangan keluarga berhasil dihapus']);
    }
}
