<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TunjAnak;

class TunjAnakController extends Controller
{
    public function index()
    {
        $tunjanak = TunjAnak::all();
        return view('master-data.tunj-anak.index', compact('tunjanak'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_tunjangan' => 'required|string|max:100',
            'nominal_tunj_anak' => 'required|numeric'
        ]);

        TunjAnak::create($request->except('_token'));

        return response()->json(['success' => true, 'message' => 'Tunjangan anak berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_tunjangan' => 'required|string|max:100',
            'nominal_tunj_anak' => 'required|numeric'
        ]);

        TunjAnak::where('id_tunj_anak', $id)->update($request->except('_token'));

        return response()->json(['success' => true, 'message' => 'Tunjangan anak berhasil diperbarui']);
    }

    public function destroy($id)
    {
        TunjAnak::where('id_tunj_anak', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Tunjangan anak berhasil dihapus']);
    }
}
