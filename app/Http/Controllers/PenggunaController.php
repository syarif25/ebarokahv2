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
        // Normalisasi jabatan jika dikirim dengan spasi (misal 'Admin Lembaga')
        if ($request->has('jabatan')) {
            $rawJab = str_replace(' ', '', (string)$request->jabatan);
            if (strcasecmp($rawJab, 'AdminLembaga') === 0) $request->merge(['jabatan' => 'AdminLembaga']);
            elseif (strcasecmp($rawJab, 'SuperAdmin') === 0) $request->merge(['jabatan' => 'SuperAdmin']);
            elseif (strcasecmp($rawJab, 'SDM') === 0) $request->merge(['jabatan' => 'SDM']);
            elseif (strcasecmp($rawJab, 'Evaluasi') === 0) $request->merge(['jabatan' => 'Evaluasi']);
            elseif (strcasecmp($rawJab, 'Kasir') === 0) $request->merge(['jabatan' => 'Kasir']);
            elseif (strcasecmp($rawJab, 'Satpam') === 0) $request->merge(['jabatan' => 'Satpam']);
        }

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
        // Normalisasi jabatan jika dikirim dengan spasi (misal 'Admin Lembaga')
        if ($request->has('jabatan')) {
            $rawJab = str_replace(' ', '', (string)$request->jabatan);
            if (strcasecmp($rawJab, 'AdminLembaga') === 0) $request->merge(['jabatan' => 'AdminLembaga']);
            elseif (strcasecmp($rawJab, 'SuperAdmin') === 0) $request->merge(['jabatan' => 'SuperAdmin']);
            elseif (strcasecmp($rawJab, 'SDM') === 0) $request->merge(['jabatan' => 'SDM']);
            elseif (strcasecmp($rawJab, 'Evaluasi') === 0) $request->merge(['jabatan' => 'Evaluasi']);
            elseif (strcasecmp($rawJab, 'Kasir') === 0) $request->merge(['jabatan' => 'Kasir']);
            elseif (strcasecmp($rawJab, 'Satpam') === 0) $request->merge(['jabatan' => 'Satpam']);
        }

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
