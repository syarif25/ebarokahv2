<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LiburPesantren;

class LiburPesantrenController extends Controller
{
    public function index()
    {
        $libur = LiburPesantren::all();
        return view('master-data.libur-pesantren.index', compact('libur'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bulan' => 'required|string|max:20',
            'tahun' => 'required|numeric|digits:4',
            'jumlah_hari' => 'required|numeric',
            'keterangan' => 'nullable|string'
        ]);

        LiburPesantren::create($request->except('_token'));

        return response()->json(['success' => true, 'message' => 'Data libur pesantren berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'bulan' => 'required|string|max:20',
            'tahun' => 'required|numeric|digits:4',
            'jumlah_hari' => 'required|numeric',
            'keterangan' => 'nullable|string'
        ]);

        LiburPesantren::where('id_libur', $id)->update($request->except('_token'));

        return response()->json(['success' => true, 'message' => 'Data libur pesantren berhasil diperbarui']);
    }

    public function destroy($id)
    {
        LiburPesantren::where('id_libur', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Data libur pesantren berhasil dihapus']);
    }
}
