<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Potongan;

class PotonganController extends Controller
{
    public function index()
    {
        $potongan = Potongan::all();
        return view('master-data.jenis-potongan.index', compact('potongan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_potongan' => 'required|string|max:100',
            'nominal' => 'required|numeric'
        ]);

        Potongan::create($request->except('_token'));

        return response()->json(['success' => true, 'message' => 'Jenis potongan berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_potongan' => 'required|string|max:100',
            'nominal' => 'required|numeric'
        ]);

        Potongan::where('id_potongan', $id)->update($request->except('_token'));

        return response()->json(['success' => true, 'message' => 'Jenis potongan berhasil diperbarui']);
    }

    public function destroy($id)
    {
        Potongan::where('id_potongan', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Jenis potongan berhasil dihapus']);
    }
}
