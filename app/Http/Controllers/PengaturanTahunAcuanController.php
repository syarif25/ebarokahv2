<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengaturanTahunAcuan;
use Illuminate\Support\Facades\Cache;

class PengaturanTahunAcuanController extends Controller
{
    public function index()
    {
        $tahun_acuan = PengaturanTahunAcuan::all();
        return view('master-data.tahun-acuan.index', compact('tahun_acuan'));
    }

    public function store(Request $request)
    {
        PengaturanTahunAcuan::insert([
            'id_bidang' => $request->id_bidang,
            'tahun_acuan' => $request->tahun_acuan,
            'keterangan' => $request->keterangan,
        ]);
        
        Cache::forget('master_tahun_acuan');
        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        PengaturanTahunAcuan::where('id', $id)->update([
            'id_bidang' => $request->id_bidang,
            'tahun_acuan' => $request->tahun_acuan,
            'keterangan' => $request->keterangan,
        ]);
        
        Cache::forget('master_tahun_acuan');
        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        PengaturanTahunAcuan::where('id', $id)->delete();
        
        Cache::forget('master_tahun_acuan');
        return response()->json(['success' => true]);
    }
}
