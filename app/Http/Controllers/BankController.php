<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bank;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::orderBy('is_default', 'desc')
            ->orderBy('nama_bank', 'asc')
            ->get();

        return view('master-data.bank.index', compact('banks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bank'     => 'required|string|max:100',
            'kode_bank'     => 'nullable|string|max:20',
            'bifast_code'   => 'required|string|max:50',
            'tipe_transfer' => 'required|in:IFT,BIF',
            'biaya_admin'   => 'required|numeric|min:0',
        ]);

        $isDefault = $request->has('is_default') && $request->is_default ? 1 : 0;

        if ($isDefault) {
            Bank::query()->update(['is_default' => 0]);
        }

        Bank::create([
            'nama_bank'     => $request->nama_bank,
            'kode_bank'     => $request->kode_bank,
            'bifast_code'   => strtoupper($request->bifast_code),
            'tipe_transfer' => $request->tipe_transfer,
            'biaya_admin'   => (int) $request->biaya_admin,
            'is_default'    => $isDefault,
        ]);

        return response()->json(['success' => true, 'message' => 'Data bank & potongan admin berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_bank'     => 'required|string|max:100',
            'kode_bank'     => 'nullable|string|max:20',
            'bifast_code'   => 'required|string|max:50',
            'tipe_transfer' => 'required|in:IFT,BIF',
            'biaya_admin'   => 'required|numeric|min:0',
        ]);

        $isDefault = $request->has('is_default') && $request->is_default ? 1 : 0;

        if ($isDefault) {
            Bank::where('id_bank', '!=', $id)->update(['is_default' => 0]);
        }

        Bank::where('id_bank', $id)->update([
            'nama_bank'     => $request->nama_bank,
            'kode_bank'     => $request->kode_bank,
            'bifast_code'   => strtoupper($request->bifast_code),
            'tipe_transfer' => $request->tipe_transfer,
            'biaya_admin'   => (int) $request->biaya_admin,
            'is_default'    => $isDefault,
        ]);

        return response()->json(['success' => true, 'message' => 'Data bank & potongan admin berhasil diperbarui']);
    }

    public function destroy($id)
    {
        Bank::where('id_bank', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Data bank berhasil dihapus']);
    }
}
