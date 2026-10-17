<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Umana;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UmanaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Umana::select('nik', 'nama_lengkap', 'alamat_domisili', 'nomor_hp', 'gelar_depan', 'gelar_belakang')->get();
            return response()->json(['data' => $data]);
        }
        
        $jafung = DB::table('barokah_jafung')->get();
        return view('master-data.umana.index', compact('jafung'));
    }

    private function _validate(Request $request, $isEdit = false, $oldNik = null)
    {
        $rules = [
            'niy' => 'required|string',
            'nama_lengkap' => 'required|string|max:200',
            'jk' => 'required|in:Laki-laki,Perempuan',
            'tgl_lahir' => 'required|date',
            'status_nikah' => 'required|in:Menikah,Belum Menikah',
            'alamat_domisili' => 'required|string',
            'ijazah_terakhir' => 'required|in:SD,SMP,SLTA/D1,SMK,D2/D3,S1,S2,S3',
            'ikatan_khidmah' => 'required|in:Pegawai Tetap,Dosen Tetap,Guru Tetap,Magang',
            'no_rekening' => 'required|string',
            'atas_nama' => 'required|string',
            'nama_bank' => 'required|in:Bank Rakyat Indonesia,Bank Syari\'ah Indonesia,Bank Mandiri,Bank Jatim,Bank BNI,Bank BCA,CIMB,Bank Muamalat,BTN',
            'status_aktif' => 'required|in:Aktif,Tidak Aktif,Cuti 50%,Cuti 100%'
        ];

        if ($isEdit) {
            $rules['nik'] = 'required|string|max:30|unique:umana,nik,' . $oldNik . ',nik';
            $rules['nomor_hp'] = 'nullable|string';
            $rules['password'] = 'nullable|string';
        } else {
            $rules['nik'] = 'required|string|max:30|unique:umana,nik';
            $rules['nomor_hp'] = 'nullable|string';
            $rules['password'] = 'required|string';
        }

        $request->validate($rules);
    }

    public function edit($id)
    {
        $umana = DB::table('umana')->where('nik', $id)->first();
        return response()->json(['success' => true, 'data' => $umana]);
    }

    public function store(Request $request)
    {
        $this->_validate($request);

        $data = $request->except(['_token', 'is_edit']);
        $data['password'] = Hash::make($request->password);
        
        // Handle optional empty dates by setting to null
        $dateFields = ['tmt_struktural', 'tmt_guru', 'tmt_dosen', 'tmt_maif'];
        foreach($dateFields as $field) {
            if (empty($data[$field])) {
                $data[$field] = null;
            }
        }

        $data['jumlah_anak'] = 0; // Default required by database

        Umana::create($data);

        return response()->json(['success' => true, 'message' => 'Data humana berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $this->_validate($request, true, $id);

        $data = $request->except(['_token', 'is_edit']);
        
        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        } else {
            unset($data['password']);
        }
        
        $dateFields = ['tmt_struktural', 'tmt_guru', 'tmt_dosen', 'tmt_maif'];
        foreach($dateFields as $field) {
            if (empty($data[$field])) {
                $data[$field] = null;
            }
        }

        DB::beginTransaction();
        try {
            // Update umana data using DB facade to avoid Eloquent primary key issues if NIK changes
            DB::table('umana')->where('nik', $id)->update($data);

            // Sync to penempatan and pengajar if NIK is changed
            if ($id !== $request->nik) {
                DB::table('penempatan')->where('nik', $id)->update(['nik' => $request->nik]);
                DB::table('pengajar')->where('nik', $id)->update(['nik' => $request->nik]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Data humana berhasil diperbarui']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        Umana::where('nik', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Data humana berhasil dihapus']);
    }
}
