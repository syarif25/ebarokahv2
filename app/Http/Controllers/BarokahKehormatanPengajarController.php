<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarokahKehormatanPengajar;

class BarokahKehormatanPengajarController extends Controller
{
    public function index()
    {
        $barokah = BarokahKehormatanPengajar::all();
        return view('master-data.barokah-kehormatan-pengajar.index', compact('barokah'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:100',
            'min_masa_pengabdian' => 'required|numeric',
            'max_masa_pengabdian' => 'required|numeric',
            'nominal' => 'required|numeric'
        ]);

        BarokahKehormatanPengajar::create($request->except('_token'));

        return response()->json(['success' => true, 'message' => 'Data barokah kehormatan pengajar berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori' => 'required|string|max:100',
            'min_masa_pengabdian' => 'required|numeric',
            'max_masa_pengabdian' => 'required|numeric',
            'nominal' => 'required|numeric'
        ]);

        BarokahKehormatanPengajar::where('id_barokah_kehormatan_pengajar', $id)->update($request->except('_token'));

        return response()->json(['success' => true, 'message' => 'Data barokah kehormatan pengajar berhasil diperbarui']);
    }

    public function destroy($id)
    {
        BarokahKehormatanPengajar::where('id_barokah_kehormatan_pengajar', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Data barokah kehormatan pengajar berhasil dihapus']);
    }
}
