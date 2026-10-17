<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajar;
use App\Models\BarokahTambahan;
use Illuminate\Support\Facades\DB;

class BarokahTambahanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pengajar::with(['umana', 'lembaga', 'barokahTambahans'])
                            ->whereHas('barokahTambahans')
                            ->get();

            return response()->json(['data' => $data]);
        }

        // Ambil semua pengajar untuk dropdown form tambah
        $pengajars = Pengajar::with(['umana', 'lembaga'])->get();
        
        return view('manajemen-pengajar.barokah-tambahan.index', compact('pengajars'));
    }

    public function detail($id_pengajar)
    {
        $data = BarokahTambahan::where('id_pengajar', $id_pengajar)
                              ->orderBy('id_barokah_tambahan', 'desc')
                              ->get();
        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pengajar' => 'required',
            'nama_barokah' => 'required|string',
            'nominal_tambahan' => 'required|numeric',
            'min_periode_tambahan' => 'required|date',
            'max_periode_tambahan' => 'required|date',
        ]);

        try {
            BarokahTambahan::create($request->all());
            return response()->json(['status' => 'success', 'message' => 'Data berhasil disimpan']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_barokah' => 'required|string',
            'nominal_tambahan' => 'required|numeric',
            'min_periode_tambahan' => 'required|date',
            'max_periode_tambahan' => 'required|date',
        ]);

        try {
            $barokah = BarokahTambahan::findOrFail($id);
            // Ignore id_pengajar in update to prevent shifting to another person
            $barokah->update($request->except(['id_pengajar']));
            return response()->json(['status' => 'success', 'message' => 'Data berhasil diupdate']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
