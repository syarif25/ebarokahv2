<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Cache;

class PenggunaController extends Controller
{
    public function index()
    {
        $pengguna = Pengguna::with('lembagaData')->get();
        $lembaga_list = \App\Models\Lembaga::all();

        return view('master-data.pengguna.index', compact('pengguna', 'lembaga_list'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:100|unique:pengguna,username',
            'jabatan'  => 'required|string|in:SuperAdmin,Evaluasi,AdminLembaga,SDM,Kasir,Satpam',
            'lembaga'  => 'nullable|integer',
            'no_hp'    => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
        ]);

        $data = [
            'username' => $validated['username'],
            'jabatan'  => $validated['jabatan'],
            'lembaga'  => $validated['lembaga'] ?? null,
            'no_hp'    => $validated['no_hp'] ?? null,
            'password' => bcrypt($validated['password']),
        ];
        Pengguna::insert($data);
        
        Cache::forget('master_pengguna');
        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:100|unique:pengguna,username,' . (int)$id . ',id_pengguna',
            'jabatan'  => 'required|string|in:SuperAdmin,Evaluasi,AdminLembaga,SDM,Kasir,Satpam',
            'lembaga'  => 'nullable|integer',
            'no_hp'    => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
        ]);

        $data = [
            'username' => $validated['username'],
            'jabatan'  => $validated['jabatan'],
            'lembaga'  => $validated['lembaga'] ?? null,
            'no_hp'    => $validated['no_hp'] ?? null,
        ];
        if (!empty($validated['password'])) {
            $data['password'] = bcrypt($validated['password']);
        }
        Pengguna::where('id_pengguna', $id)->update($data);
        
        Cache::forget('master_pengguna');
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Pengguna::where('id_pengguna', $id)->delete();
        
        Cache::forget('master_pengguna');
        return response()->json(['success' => true]);
    }
}
