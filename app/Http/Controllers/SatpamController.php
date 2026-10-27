<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Satpam;
use App\Models\Umana;
use App\Models\Transport;

class SatpamController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $satpamList = Satpam::with(['umana', 'transport'])
                ->orderBy('id_satpam', 'desc')
                ->get();

            $data = [];
            $no = 1;
            foreach ($satpamList as $item) {
                $u = $item->umana;
                $t = $item->transport;

                // Format Nama Lengkap
                $gDepan = !empty($u->gelar_depan) ? trim($u->gelar_depan) . ' ' : '';
                $gBelakang = !empty($u->gelar_belakang) ? ' ' . trim($u->gelar_belakang) : '';
                $namaRaw = !empty($u->nama_lengkap) ? ucwords(strtolower(trim($u->nama_lengkap))) : '-';
                $namaFull = $gDepan . $namaRaw . $gBelakang;

                $namaFullHtml = '<span class="font-weight-bold text-dark">' . e($namaFull) . '</span>';
                if ($item->is_danru == 1) {
                    $namaFullHtml .= ' <span class="badge badge-primary px-2 py-0 ml-1 font-weight-bold" style="font-size: 10px;"><i class="fa fa-star mr-1"></i>Danru</span>';
                }

                // Format Tanggal
                $tglMulai = !empty($item->tgl_mulai) && $item->tgl_mulai != '0000-00-00'
                    ? date('d-m-Y', strtotime($item->tgl_mulai))
                    : '-';
                $tglSelesai = !empty($item->tgl_selesai) && $item->tgl_selesai != '0000-00-00'
                    ? date('d-m-Y', strtotime($item->tgl_selesai))
                    : '-';

                $masaTugas = $tglMulai . '<br><small class="text-muted">Sampai</small><br>' . $tglSelesai;

                // Tombol Aksi: Cukup tombol Edit saja
                $btnEdit = '<button type="button" class="btn btn-edit-satpam shadow-sm btn-sm" data-id="' . $item->id_satpam . '" title="Edit">
                                <i class="fa fa-edit mr-1"></i> Edit
                            </button>';

                $aksi = '<div class="d-flex justify-content-center">' . $btnEdit . '</div>';

                // Badge Status
                $statusBadge = strtolower($item->status) === 'aktif'
                    ? '<span class="badge badge-status">Aktif</span>'
                    : '<span class="badge badge-danger px-2 py-1 font-weight-bold">' . e($item->status) . '</span>';

                $alamat = !empty($u->alamat_domisili) ? e($u->alamat_domisili) : '-';

                $data[] = [
                    'no'              => $no++,
                    'id_satpam'       => $item->id_satpam,
                    'nama_lengkap'    => $namaFullHtml,
                    'alamat'          => $alamat,
                    'masa_pengabdian' => $masaTugas,
                    'status'          => $statusBadge,
                    'aksi'            => $aksi,
                ];
            }

            return response()->json(['data' => $data]);
        }

        // Stats card
        $totalSatpam    = Satpam::count();
        $totalAktif     = Satpam::where('status', 'Aktif')->count();
        $totalDanru     = Satpam::where('is_danru', 1)->count();
        $totalTidakAktif = Satpam::where('status', '!=', 'Aktif')->count();

        $umanas     = Umana::orderBy('nama_lengkap', 'asc')->get();
        $transports = Transport::orderBy('nama_transport', 'asc')->get();

        return view('manajemen-satpam.penempatan.index', compact(
            'totalSatpam', 'totalAktif', 'totalDanru', 'totalTidakAktif',
            'umanas', 'transports'
        ));
    }

    public function getUmana($nik)
    {
        $umana = Umana::where('nik', $nik)->first();
        if ($umana) {
            return response()->json([
                'status' => true,
                'data'   => [
                    'nik'             => $umana->nik,
                    'niy'             => $umana->niy ?? '-',
                    'nama_lengkap'    => $umana->nama_lengkap,
                    'gelar_depan'     => $umana->gelar_depan ?? '',
                    'gelar_belakang'  => $umana->gelar_belakang ?? '',
                    'alamat_domisili' => $umana->alamat_domisili ?? '-',
                ]
            ]);
        }

        return response()->json(['status' => false, 'message' => 'Data Umana tidak ditemukan']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik'          => 'required',
            'transport'    => 'required',
            'tgl_mulai'    => 'required|date',
            'tgl_selesai'  => 'required|date',
            'status'       => 'required',
            'file_sk'      => 'nullable|mimes:pdf|max:10240',
        ], [
            'nik.required'          => 'NIK Umana wajib dipilih.',
            'transport.required'    => 'Transport / Kehadiran wajib dipilih.',
            'tgl_mulai.required'    => 'Tanggal mulai tugas wajib diisi.',
            'tgl_selesai.required'  => 'Tanggal akhir tugas wajib diisi.',
            'file_sk.mimes'         => 'File SK harus berformat PDF.',
            'file_sk.max'           => 'Ukuran file SK maksimal 10MB.',
        ]);

        $data = [
            'nik'          => $request->nik,
            'id_transport' => $request->transport,
            'tgl_mulai'    => $request->tgl_mulai,
            'tgl_selesai'  => $request->tgl_selesai,
            'status'       => $request->status,
            'is_danru'     => $request->input('is_danru', 0),
            'tgl_input'    => date('Y-m-d H:i:s'),
            'file_sk'      => '',
        ];

        if ($request->hasFile('file_sk')) {
            $file = $request->file('file_sk');
            $filename = 'sk_satpam_' . \Illuminate\Support\Str::random(30) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload'), $filename);
            $data['file_sk'] = $filename;
        }

        Satpam::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Data Penempatan Satpam berhasil ditambahkan.'
        ]);
    }

    public function edit($id)
    {
        $satpam = Satpam::with('umana')->find($id);
        if (!$satpam) {
            return response()->json(['status' => false, 'message' => 'Data satpam tidak ditemukan.'], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => [
                'id_satpam'    => $satpam->id_satpam,
                'nik'          => $satpam->nik,
                'id_transport' => $satpam->id_transport,
                'tgl_mulai'    => $satpam->tgl_mulai,
                'tgl_selesai'  => $satpam->tgl_selesai,
                'status'       => $satpam->status,
                'is_danru'     => $satpam->is_danru,
                'file_sk'      => $satpam->file_sk,
                'file_url'     => !empty($satpam->file_sk) ? asset('upload/' . $satpam->file_sk) : null,
                'umana'        => [
                    'nama_lengkap' => $satpam->umana->nama_lengkap ?? '',
                    'niy'          => $satpam->umana->niy ?? '',
                ]
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $satpam = Satpam::findOrFail($id);

        $request->validate([
            'nik'          => 'required',
            'transport'    => 'required',
            'tgl_mulai'    => 'required|date',
            'tgl_selesai'  => 'required|date',
            'status'       => 'required',
            'file_sk'      => 'nullable|mimes:pdf|max:10240',
        ], [
            'nik.required'          => 'NIK Umana wajib dipilih.',
            'transport.required'    => 'Transport / Kehadiran wajib dipilih.',
            'tgl_mulai.required'    => 'Tanggal mulai tugas wajib diisi.',
            'tgl_selesai.required'  => 'Tanggal akhir tugas wajib diisi.',
            'file_sk.mimes'         => 'File SK harus berformat PDF.',
            'file_sk.max'           => 'Ukuran file SK maksimal 10MB.',
        ]);

        $satpam->nik          = $request->nik;
        $satpam->id_transport = $request->transport;
        $satpam->tgl_mulai    = $request->tgl_mulai;
        $satpam->tgl_selesai  = $request->tgl_selesai;
        $satpam->status       = $request->status;
        $satpam->is_danru     = $request->input('is_danru', 0);

        if ($request->hasFile('file_sk')) {
            $file = $request->file('file_sk');
            $filename = 'sk_satpam_' . \Illuminate\Support\Str::random(30) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload'), $filename);

            // Hapus file lama jika ada
            if (!empty($satpam->file_sk) && file_exists(public_path('upload/' . $satpam->file_sk))) {
                @unlink(public_path('upload/' . $satpam->file_sk));
            }

            $satpam->file_sk = $filename;
        }

        $satpam->save();

        return response()->json([
            'status'  => true,
            'message' => 'Data Penempatan Satpam berhasil diperbarui.'
        ]);
    }

    public function destroy($id)
    {
        $satpam = Satpam::findOrFail($id);

        if (!empty($satpam->file_sk) && file_exists(public_path('upload/' . $satpam->file_sk))) {
            @unlink(public_path('upload/' . $satpam->file_sk));
        }

        $satpam->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Data Penempatan Satpam berhasil dihapus.'
        ]);
    }
}
