<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarokahPengajar;

class BarokahPengajarController extends Controller
{
    public function index()
    {
        $barokah = BarokahPengajar::all();
        return view('master-data.barokah-pengajar.index', compact('barokah'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kategori' => 'required|string|max:100',
            'golongan' => 'nullable|string|max:50',
            'min_tmp_mengajar' => 'required|numeric',
            'max_tmp_mengajar' => 'required|numeric',
            'ijazah' => 'required|string|max:50',
            'nominal' => 'required|numeric'
        ]);

        BarokahPengajar::create($request->except('_token'));

        return response()->json(['success' => true, 'message' => 'Data barokah pengajar berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kategori' => 'required|string|max:100',
            'golongan' => 'nullable|string|max:50',
            'min_tmp_mengajar' => 'required|numeric',
            'max_tmp_mengajar' => 'required|numeric',
            'ijazah' => 'required|string|max:50',
            'nominal' => 'required|numeric'
        ]);

        BarokahPengajar::where('id_barokah_pengajar', $id)->update($request->except('_token'));

        return response()->json(['success' => true, 'message' => 'Data barokah pengajar berhasil diperbarui']);
    }

    public function destroy($id)
    {
        BarokahPengajar::where('id_barokah_pengajar', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Data barokah pengajar berhasil dihapus']);
    }
}
