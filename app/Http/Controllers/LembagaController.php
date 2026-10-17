<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lembaga;
use Illuminate\Support\Facades\Cache;

class LembagaController extends Controller
{
    public function index()
    {
        $lembaga = Lembaga::all();
        return view('master-data.lembaga.index', compact('lembaga'));
    }

    public function store(Request $request)
    {
        Lembaga::insert([
            'nama_lembaga' => $request->nama_lembaga,
            'id_bidang' => $request->id_bidang,
            'tenaga_pengajar' => $request->tenaga_pengajar,
            'nama_pimpinan' => $request->nama_pimpinan,
        ]);
        
        Cache::forget('master_lembaga');
        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        Lembaga::where('id_lembaga', $id)->update([
            'nama_lembaga' => $request->nama_lembaga,
            'id_bidang' => $request->id_bidang,
            'tenaga_pengajar' => $request->tenaga_pengajar,
            'nama_pimpinan' => $request->nama_pimpinan,
        ]);
        
        Cache::forget('master_lembaga');
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        Lembaga::where('id_lembaga', $id)->delete();
        
        Cache::forget('master_lembaga');
        return response()->json(['success' => true]);
    }
}
