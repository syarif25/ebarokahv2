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
        $data = [
            'username' => $request->username,
            'jabatan' => $request->jabatan,
            'lembaga' => $request->lembaga,
            'no_hp' => $request->no_hp,
        ];
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        Pengguna::insert($data);
        
        Cache::forget('master_pengguna'); // Hapus cache agar data terbaru muncul
        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $data = [
            'username' => $request->username,
            'jabatan' => $request->jabatan,
            'lembaga' => $request->lembaga,
            'no_hp' => $request->no_hp,
        ];
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
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
