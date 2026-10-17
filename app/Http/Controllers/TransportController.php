<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transport;

class TransportController extends Controller
{
    public function index()
    {
        $transport = Transport::all();
        return view('master-data.transport.index', compact('transport'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_transport' => 'required|string|max:200',
            'kategori_transport' => 'required|string|max:100',
            'nominal_transport' => 'required|numeric'
        ]);

        Transport::create($request->except('_token'));

        return response()->json(['success' => true, 'message' => 'Data transport berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_transport' => 'required|string|max:200',
            'kategori_transport' => 'required|string|max:100',
            'nominal_transport' => 'required|numeric'
        ]);

        Transport::where('id_transport', $id)->update($request->except('_token'));

        return response()->json(['success' => true, 'message' => 'Data transport berhasil diperbarui']);
    }

    public function destroy($id)
    {
        Transport::where('id_transport', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Data transport berhasil dihapus']);
    }
}
