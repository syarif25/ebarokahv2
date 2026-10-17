<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prodi;
use App\Models\Lembaga;

class ProdiController extends Controller
{
    public function index()
    {
        $prodi = Prodi::with('lembagaData')->get();
        $lembaga_list = Lembaga::all();
        
        return view('master-data.prodi.index', compact('prodi', 'lembaga_list'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_lembaga' => 'required',
            'nama_prodi' => 'required|string|max:255',
        ]);

        Prodi::create([
            'id_lembaga' => $request->id_lembaga,
            'nama_prodi' => $request->nama_prodi,
        ]);

        return response()->json(['success' => true, 'message' => 'Data prodi berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_lembaga' => 'required',
            'nama_prodi' => 'required|string|max:255',
        ]);

        Prodi::where('id_prodi', $id)->update([
            'id_lembaga' => $request->id_lembaga,
            'nama_prodi' => $request->nama_prodi,
        ]);

        return response()->json(['success' => true, 'message' => 'Data prodi berhasil diperbarui']);
    }

    public function destroy($id)
    {
        Prodi::where('id_prodi', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Data prodi berhasil dihapus']);
    }
}
