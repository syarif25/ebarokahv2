<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KetentuanBarokah;

class KetentuanBarokahController extends Controller
{
    public function index()
    {
        $ketentuan = KetentuanBarokah::all();
        return view('master-data.ketentuan-barokah.index', compact('ketentuan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:100',
            'kategori' => 'required|string|max:100',
            'barokah' => 'required|numeric',
            'wajib_hadir' => 'required|numeric',
        ]);

        KetentuanBarokah::create($request->except('_token'));

        return response()->json(['success' => true, 'message' => 'Ketentuan barokah berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:100',
            'kategori' => 'required|string|max:100',
            'barokah' => 'required|numeric',
            'wajib_hadir' => 'required|numeric',
        ]);

        KetentuanBarokah::where('id_ketentuan', $id)->update($request->except('_token'));

        return response()->json(['success' => true, 'message' => 'Ketentuan barokah berhasil diperbarui']);
    }

    public function destroy($id)
    {
        KetentuanBarokah::where('id_ketentuan', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Ketentuan barokah berhasil dihapus']);
    }
}
