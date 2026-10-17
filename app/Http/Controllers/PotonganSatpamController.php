<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PotonganSatpam;
use App\Models\Satpam;
use App\Models\Potongan;

class PotonganSatpamController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $potonganList = PotonganSatpam::with(['satpam.umana', 'potongan'])
                ->orderBy('id_potongan_satpam', 'desc')
                ->get();

            $data = [];
            $no = 1;
            foreach ($potonganList as $item) {
                $u = $item->satpam->umana ?? null;

                // Format Nama Lengkap
                $gDepan = !empty($u->gelar_depan) ? trim($u->gelar_depan) . ' ' : '';
                $gBelakang = !empty($u->gelar_belakang) ? ' ' . trim($u->gelar_belakang) : '';
                $namaRaw = !empty($u->nama_lengkap) ? ucwords(strtolower(trim($u->nama_lengkap))) : '-';
                $namaFull = $gDepan . $namaRaw . $gBelakang;

                $namaPotongan = !empty($item->potongan->nama_potongan) ? e($item->potongan->nama_potongan) : '-';
                $nominal = '<span class="text-success font-weight-bold">' . number_format((int)$item->nominal_potongan, 0, ',', '.') . '</span>';

                // Format Tanggal Periode
                $minTgl = !empty($item->min_periode_potongan) && $item->min_periode_potongan != '0000-00-00'
                    ? date('d-m-Y', strtotime($item->min_periode_potongan))
                    : '-';
                $maxTgl = !empty($item->max_periode_potongan) && $item->max_periode_potongan != '0000-00-00'
                    ? date('d-m-Y', strtotime($item->max_periode_potongan))
                    : '-';
                $periode = $minTgl . ' / ' . $maxTgl;

                // Tombol Aksi: Cukup tombol Edit saja
                $btnEdit = '<button type="button" class="btn btn-outline-success btn-sm btn-rounded px-3 btn-edit-potongan font-weight-bold" data-id="' . $item->id_potongan_satpam . '" title="Edit">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </button>';

                $aksi = '<div class="d-flex justify-content-center">' . $btnEdit . '</div>';

                $data[] = [
                    'no'             => $no++,
                    'nama_lengkap'   => '<span class="font-weight-bold text-dark">' . e($namaFull) . '</span>',
                    'jenis_potongan' => $namaPotongan,
                    'nominal'        => $nominal,
                    'periode'        => $periode,
                    'aksi'           => $aksi,
                ];
            }

            return response()->json(['data' => $data]);
        }

        $satpams = Satpam::with('umana')
            ->join('umana', 'satpam.nik', '=', 'umana.nik')
            ->orderBy('umana.nama_lengkap', 'asc')
            ->select('satpam.*')
            ->get();

        $jenisPotongans = Potongan::orderBy('nama_potongan', 'asc')->get();

        return view('manajemen-satpam.potongan-satpam.index', compact('satpams', 'jenisPotongans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_satpam'            => 'required',
            'jenis_potongan'       => 'required',
            'nominal_potongan'     => 'required',
            'min_periode_potongan' => 'required|date',
            'max_periode_potongan' => 'required|date',
        ], [
            'id_satpam.required'            => 'Nama Satpam wajib dipilih.',
            'jenis_potongan.required'       => 'Jenis potongan wajib dipilih.',
            'nominal_potongan.required'     => 'Nominal potongan wajib diisi.',
            'min_periode_potongan.required' => 'Masa aktif mulai tanggal wajib diisi.',
            'max_periode_potongan.required' => 'Masa aktif sampai tanggal wajib diisi.',
        ]);

        $nominalClean = (int)str_replace(['.', ',', ' '], '', $request->nominal_potongan);

        PotonganSatpam::create([
            'id_satpam'            => $request->id_satpam,
            'jenis_potongan'       => $request->jenis_potongan,
            'nominal_potongan'     => $nominalClean,
            'min_periode_potongan' => $request->min_periode_potongan,
            'max_periode_potongan' => $request->max_periode_potongan,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Data Potongan Barokah Satpam berhasil ditambahkan.'
        ]);
    }

    public function edit($id)
    {
        $potongan = PotonganSatpam::with(['satpam.umana', 'potongan'])->find($id);
        if (!$potongan) {
            return response()->json(['status' => false, 'message' => 'Data potongan tidak ditemukan.'], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => [
                'id_potongan_satpam'   => $potongan->id_potongan_satpam,
                'id_satpam'            => $potongan->id_satpam,
                'jenis_potongan'       => $potongan->jenis_potongan,
                'nominal_potongan'     => $potongan->nominal_potongan,
                'min_periode_potongan' => $potongan->min_periode_potongan,
                'max_periode_potongan' => $potongan->max_periode_potongan,
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $potongan = PotonganSatpam::findOrFail($id);

        $request->validate([
            'id_satpam'            => 'required',
            'jenis_potongan'       => 'required',
            'nominal_potongan'     => 'required',
            'min_periode_potongan' => 'required|date',
            'max_periode_potongan' => 'required|date',
        ], [
            'id_satpam.required'            => 'Nama Satpam wajib dipilih.',
            'jenis_potongan.required'       => 'Jenis potongan wajib dipilih.',
            'nominal_potongan.required'     => 'Nominal potongan wajib diisi.',
            'min_periode_potongan.required' => 'Masa aktif mulai tanggal wajib diisi.',
            'max_periode_potongan.required' => 'Masa aktif sampai tanggal wajib diisi.',
        ]);

        $nominalClean = (int)str_replace(['.', ',', ' '], '', $request->nominal_potongan);

        $potongan->id_satpam            = $request->id_satpam;
        $potongan->jenis_potongan       = $request->jenis_potongan;
        $potongan->nominal_potongan     = $nominalClean;
        $potongan->min_periode_potongan = $request->min_periode_potongan;
        $potongan->max_periode_potongan = $request->max_periode_potongan;
        $potongan->save();

        return response()->json([
            'status'  => true,
            'message' => 'Data Potongan Barokah Satpam berhasil diperbarui.'
        ]);
    }

    public function destroy($id)
    {
        $potongan = PotonganSatpam::findOrFail($id);
        $potongan->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Data Potongan Barokah Satpam berhasil dihapus.'
        ]);
    }
}
