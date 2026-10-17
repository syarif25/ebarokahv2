<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarokahKehormatan;

class BarokahKehormatanController extends Controller
{
    public function index()
    {
        $barokah = BarokahKehormatan::all();
        return view('master-data.barokah-kehormatan.index', compact('barokah'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'min_masa_pengabdian' => 'required|numeric',
            'max_masa_pengabdian' => 'required|numeric',
            'nominal_kehormatan' => 'required|numeric'
        ]);

        BarokahKehormatan::create($request->except('_token'));

        return response()->json(['success' => true, 'message' => 'Data barokah kehormatan berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'min_masa_pengabdian' => 'required|numeric',
            'max_masa_pengabdian' => 'required|numeric',
            'nominal_kehormatan' => 'required|numeric'
        ]);

        BarokahKehormatan::where('id_bkehormatan', $id)->update($request->except('_token'));

        return response()->json(['success' => true, 'message' => 'Data barokah kehormatan berhasil diperbarui']);
    }

    public function destroy($id)
    {
        BarokahKehormatan::where('id_bkehormatan', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Data barokah kehormatan berhasil dihapus']);
    }
}
