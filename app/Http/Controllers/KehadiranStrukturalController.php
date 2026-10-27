<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KehadiranLembaga;
use App\Models\Lembaga;
use App\Models\Penempatan;
use App\Services\HitungBarokahService;
use App\Services\PdfBarokahService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KehadiranStrukturalController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $jabatan = $user ? $user->jabatan : 'SuperAdmin';
            $canManageAll = in_array($jabatan, ['SuperAdmin', 'Evaluasi']);

            $query = KehadiranLembaga::with(['lembaga', 'detail'])->where('kategori', 'Struktural');

            // RBAC: Jika AdminLembaga, hanya tampilkan data lembaga miliknya
            if (!$canManageAll && $user && $user->lembaga) {
                $query->where('id_lembaga', $user->lembaga);
            }

            $data = $query->orderBy('id_kehadiran_lembaga', 'desc')->get();

            $bulanMap = [
                'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
                'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
                'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12
            ];

            // Mapping untuk frontend
            $data = $data->map(function($item) use ($bulanMap, $canManageAll) {
                // Hitung jumlah umana sesuai rekomendasi Tech Lead:
                // 1. Jika sudah pernah diisi (detail kehadiran > 0), ambil dari tabel detail
                if ($item->status !== 'Belum' && $item->detail->count() > 0) {
                    $jumlahUmana = $item->detail->count();
                } else {
                    // 2. Jika status 'Belum' (atau detail belum ada), hitung dinamis dari penempatan aktif periode bulan & tahun tersebut
                    $bulanLower = strtolower(trim((string)$item->bulan));
                    $bulanNum = $bulanMap[$bulanLower] ?? null;
                    $tahunNum = is_numeric($item->tahun) ? (int)$item->tahun : (int)substr((string)$item->tahun, 0, 4);

                    if ($bulanNum && $tahunNum) {
                        $startDate = sprintf('%04d-%02d-01', $tahunNum, $bulanNum);
                        $endDate = date('Y-m-t', strtotime($startDate));
                        $jumlahUmana = Penempatan::where('id_lembaga', $item->id_lembaga)
                            ->where(function($q) use ($startDate, $endDate) {
                                $q->where(function($sub) use ($startDate, $endDate) {
                                    $sub->where('tgl_mulai', '<=', $endDate)
                                        ->where('tgl_selesai', '>=', $startDate);
                                })->orWhere(function($sub) {
                                    $sub->whereNull('tgl_selesai')
                                        ->orWhere('tgl_selesai', '0000-00-00');
                                });
                            })->count();
                    } else {
                        $jumlahUmana = Penempatan::where('id_lembaga', $item->id_lembaga)->count();
                    }
                }

                return [
                    'id_kehadiran_lembaga' => $item->id_kehadiran_lembaga,
                    'nama_lembaga' => $item->lembaga ? $item->lembaga->nama_lembaga : '-',
                    'jumlah_umana' => $jumlahUmana,
                    'bulan' => $item->bulan,
                    'tahun' => $item->tahun,
                    'status' => $item->status,
                    'can_reset' => $canManageAll,
                ];
            });

            return response()->json(['data' => $data]);
        }
        
        $lembagas = Lembaga::all();
        
        return view('kehadiran-struktural.index', compact('lembagas'));
    }

    public function storeBlanko(Request $request)
    {
        $request->validate([
            'id_lembaga' => 'required',
            'bulan' => 'required',
            'tahun' => 'required',
        ]);

        try {
            // Cek duplikasi
            $exists = KehadiranLembaga::where('id_lembaga', $request->id_lembaga)
                        ->where('bulan', $request->bulan)
                        ->where('tahun', $request->tahun)
                        ->where('kategori', 'Struktural')
                        ->first();
            
            if ($exists) {
                return response()->json(['status' => 'error', 'message' => 'Blanko kehadiran untuk bulan dan tahun ini sudah ada!'], 400);
            }

            DB::beginTransaction();
            $kehadiran = KehadiranLembaga::create([
                'id_lembaga' => $request->id_lembaga,
                'bulan' => $request->bulan,
                'tahun' => $request->tahun,
                'kategori' => 'Struktural',
                'status' => 'Belum',
                'jumlah_total' => 0,
                'file' => '' // Default empty for now
            ]);

            // Catat log
            \App\Models\LogRiwayatBarokah::create([
                'id_kehadiran_lembaga' => $kehadiran->id_kehadiran_lembaga,
                'status_aksi' => 'Buat Blanko',
                'waktu_eksekusi' => now(),
                'id_pengguna' => Auth::id() ?? 1, // fallback to 1 if no auth
                'catatan_log' => ''
            ]);

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Blanko berhasil ditambahkan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function rekap($id)
    {
        $periode = KehadiranLembaga::with('lembaga')->findOrFail($id);

        // RBAC: Jika AdminLembaga, pastikan hanya mengakses lembaga miliknya
        $user = Auth::user();
        if ($user && $user->jabatan === 'AdminLembaga' && $periode->id_lembaga != $user->lembaga) {
            abort(403, 'Anda tidak memiliki hak akses untuk data lembaga ini.');
        }

        // Tentukan rentang tanggal periode bulan & tahun
        $bulanMap = [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
            'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
            'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12
        ];
        $bulanLower = strtolower(trim((string)$periode->bulan));
        $bulanNum = $bulanMap[$bulanLower] ?? null;
        $tahunNum = is_numeric($periode->tahun) ? (int)$periode->tahun : (int)substr((string)$periode->tahun, 0, 4);

        $pegawaiQuery = Penempatan::with(['umana', 'ketentuan', 'lembaga'])
            ->where('id_lembaga', $periode->id_lembaga);

        if ($bulanNum && $tahunNum) {
            $startDate = sprintf('%04d-%02d-01', $tahunNum, $bulanNum);
            $endDate = date('Y-m-t', strtotime($startDate));
            $pegawaiQuery->where(function($q) use ($startDate, $endDate) {
                $q->where(function($sub) use ($startDate, $endDate) {
                    $sub->where('tgl_mulai', '<=', $endDate)
                        ->where('tgl_selesai', '>=', $startDate);
                })->orWhere(function($sub) {
                    $sub->whereNull('tgl_selesai')
                        ->orWhere('tgl_selesai', '0000-00-00');
                });
            });
        }

        $pegawai = $pegawaiQuery->get()->sortBy(function($item) {
            return -($item->ketentuan->barokah ?? 0);
        });

        // Ambil data detail kehadiran jika sudah pernah diisi sebelumnya
        $existingKehadiran = \App\Models\Kehadiran::where('id_kehadi', $periode->id_kehadiran_lembaga)
            ->get()
            ->keyBy('id_penempatan');

        return view('kehadiran-struktural.rekap', compact('periode', 'pegawai', 'existingKehadiran'));
    }

    public function saveRekap(Request $request)
    {
        $request->validate([
            'id_kehadiran_lembaga' => 'required',
            'bulan' => 'required',
            'tahun' => 'required',
        ]);

        $periode = KehadiranLembaga::findOrFail($request->id_kehadiran_lembaga);

        // RBAC Check
        $user = Auth::user();
        if ($user && $user->jabatan === 'AdminLembaga' && $periode->id_lembaga != $user->lembaga) {
            return response()->json(['status' => 'error', 'message' => 'Anda tidak memiliki hak akses.'], 403);
        }

        // Handle File Upload jika ada file baru diunggah
        $fileName = $periode->file;
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            ], [
                'file.mimes' => 'File bukti absensi harus berformat PDF, JPG, atau PNG.',
                'file.max'   => 'Ukuran file maksimal 10MB.'
            ]);
            $file = $request->file('file');
            $customName = \Illuminate\Support\Str::random(50) . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload'), $customName);
            $fileName = $customName;
        }

        if (empty($fileName)) {
            return response()->json(['status' => 'error', 'message' => 'File bukti absensi (PDF) wajib diunggah!'], 422);
        }

        try {
            DB::beginTransaction();

            // Susun batch kehadiran
            $idPenempatans = $request->input('id_penempatan', []);
            $jumlahHadir = $request->input('jumlah_hadir', []);
            $jumlahTugas = $request->input('jumlah_tugas', []);
            $jumlahIzin = $request->input('jumlah_izin', []);
            $jumlahSakit = $request->input('jumlah_sakit', []);

            $dataKehadiran = [];
            foreach ($idPenempatans as $i => $idp) {
                $hadir = isset($jumlahHadir[$i]) ? max(0, (int)$jumlahHadir[$i]) : 0;
                $tugas = isset($jumlahTugas[$i]) ? max(0, (int)$jumlahTugas[$i]) : 0;
                $izin = isset($jumlahIzin[$i]) ? max(0, (int)$jumlahIzin[$i]) : 0;
                $sakit = isset($jumlahSakit[$i]) ? max(0, (int)$jumlahSakit[$i]) : 0;

                $dataKehadiran[] = [
                    'bulan' => $request->bulan,
                    'tahun' => $request->tahun,
                    'id_penempatan' => $idp,
                    'id_kehadi' => $periode->id_kehadiran_lembaga,
                    'jumlah_hadir' => $hadir,
                    'jumlah_tugas' => $tugas,
                    'jumlah_izin' => $izin,
                    'jumlah_sakit' => $sakit,
                ];
            }

            // Hapus detail lama agar idempotent
            \App\Models\Kehadiran::where('id_kehadi', $periode->id_kehadiran_lembaga)->delete();

            // Masukkan data baru
            if (!empty($dataKehadiran)) {
                \App\Models\Kehadiran::insert($dataKehadiran);
            }

            // Update status periode menjadi 'Sudah' dan simpan file
            $periode->update([
                'status' => 'Sudah',
                'file' => $fileName,
            ]);

            // Catat log audit
            \App\Models\LogRiwayatBarokah::create([
                'id_kehadiran_lembaga' => $periode->id_kehadiran_lembaga,
                'status_aksi' => 'Isi Rekap',
                'waktu_eksekusi' => now(),
                'id_pengguna' => Auth::id() ?? 1,
                'catatan_log' => ''
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Rekap kehadiran berhasil disimpan!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Halaman Cek Barokah / Koreksi Validasi Kehadiran Struktural
     */
    public function koreksi($id)
    {
        $res = \App\Services\HitungBarokahService::hitungPeriode($id);
        if (!$res['periode']) {
            abort(404, 'Data periode kehadiran tidak ditemukan');
        }

        $user = Auth::user();
        if ($user && $user->jabatan === 'AdminLembaga' && $res['periode']->id_lembaga != $user->lembaga) {
            abort(403, 'Anda tidak memiliki hak akses untuk data lembaga ini.');
        }

        return view('kehadiran-struktural.koreksi', [
            'isilist' => $res['rows'],
            'periode' => $res['periode'],
            'totals'  => $res['totals'],
        ]);
    }

    /**
     * Update baris kehadiran individual via AJAX modal koreksi
     */
    public function updateRow(Request $request)
    {
        $id_kehadiran = (int) $request->input('id_kehadiran');
        $id_kehadiran_lembaga = $request->input('id_kehadiran_lembaga');
        $hadir = max(0, (int) $request->input('jumlah_hadir', 0));
        $tugas = max(0, (int) $request->input('jumlah_tugas', 0));
        $izin  = max(0, (int) $request->input('jumlah_izin', 0));
        $sakit = max(0, (int) $request->input('jumlah_sakit', 0));

        if (!$id_kehadiran || !$id_kehadiran_lembaga) {
            return response()->json(['status' => false, 'message' => 'Parameter tidak lengkap.'], 400);
        }

        \App\Models\Kehadiran::where('id_kehadiran', $id_kehadiran)->update([
            'jumlah_hadir' => $hadir,
            'jumlah_tugas' => $tugas,
            'jumlah_izin'  => $izin,
            'jumlah_sakit' => $sakit,
        ]);

        $res = \App\Services\HitungBarokahService::hitungPeriode($id_kehadiran_lembaga);
        $updatedRow = null;
        foreach ($res['rows'] as $r) {
            if ((int)$r->id_kehadiran === $id_kehadiran) {
                $updatedRow = $r;
                break;
            }
        }

        if (!$updatedRow) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan setelah pembaruan.'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data kehadiran berhasil diperbarui.',
            'row' => [
                'id_kehadiran'         => (int)$updatedRow->id_kehadiran,
                'jumlah_hadir'         => (int)$updatedRow->jumlah_hadir,
                'jumlah_tugas'         => (int)$updatedRow->jumlah_tugas,
                'jumlah_izin'          => (int)$updatedRow->jumlah_izin,
                'jumlah_sakit'         => (int)$updatedRow->jumlah_sakit,
                'persentase_kehadiran' => $updatedRow->persentase_kehadiran,
                'nominal_kehadiran'    => number_format((int)$updatedRow->nominal_kehadiran, 0, ',', '.'),
                'jumlah_barokah'       => number_format((int)$updatedRow->jumlah_barokah, 0, ',', '.'),
                'diterima'             => number_format((int)$updatedRow->diterima, 0, ',', '.'),
            ],
            'totals' => $res['totals'],
        ]);
    }

    /**
     * Detail TBK per Umana'
     */
    public function detailTbk($id_penempatan)
    {
        $data = DB::table('t_beban_kerja')
            ->where('id_penempatan', $id_penempatan)
            ->where(function($q) {
                $q->whereNull('max_periode')->orWhere('max_periode', '>=', now()->toDateString());
            })
            ->get();

        return response()->json($data);
    }

    /**
     * Simpan / Tambah TBK
     */
    public function saveTbk(Request $request)
    {
        $id_penempatan = $request->input('id_penempatan');
        $tbkData = $request->input('tbk', []);

        if ($id_penempatan && is_array($tbkData)) {
            foreach ($tbkData as $item) {
                $jenis = $item['jenis_tbk'] ?? '';
                $nominal = isset($item['nominal_tbk']) ? (int)str_replace(['.', ','], '', $item['nominal_tbk']) : 0;
                $maxPeriode = !empty($item['max_periode']) ? $item['max_periode'] : null;
                $idTbk = $item['id_tbk'] ?? null;

                if (empty($jenis)) continue;

                $payload = [
                    'id_penempatan' => $id_penempatan,
                    'jenis_tbk'     => $jenis,
                    'nominal_tbk'   => $nominal,
                    'max_periode'   => $maxPeriode,
                ];

                if (!empty($idTbk)) {
                    DB::table('t_beban_kerja')->where('id_tbk', $idTbk)->update($payload);
                } else {
                    DB::table('t_beban_kerja')->insert($payload);
                }
            }
        }

        return response()->json(['status' => true, 'message' => 'Data TBK berhasil disimpan.']);
    }

    /**
     * Options master jenis potongan
     */
    public function getPotonganOptions()
    {
        $options = DB::table('potongan')->orderBy('nama_potongan', 'asc')->get();
        return response()->json($options);
    }

    /**
     * Detail Potongan per Umana'
     */
    public function detailPotongan($id_penempatan, $periode_date = null)
    {
        $query = DB::table('potongan_umana as pu')
            ->leftJoin('potongan as p', 'p.id_potongan', '=', 'pu.jenis_potongan')
            ->select('pu.id_potongan_umana', 'pu.jenis_potongan', 'p.nama_potongan', 'pu.nominal_potongan', 'pu.min_periode_potongan', 'pu.max_periode_potongan')
            ->where('pu.id_penempatan', $id_penempatan);

        if ($periode_date) {
            $query->where(function($q) use ($periode_date) {
                $q->whereNull('pu.max_periode_potongan')->orWhere('pu.max_periode_potongan', '>=', $periode_date);
            });
        }

        return response()->json($query->get());
    }

    /**
     * Simpan / Tambah Potongan
     */
    public function savePotongan(Request $request)
    {
        $id_penempatan = $request->input('id_penempatan');
        $potonganData = $request->input('potongan', []);

        if ($id_penempatan && is_array($potonganData)) {
            foreach ($potonganData as $item) {
                $jenis = $item['jenis_potongan'] ?? '';
                $nominal = isset($item['nominal_potongan']) ? (int)str_replace(['.', ','], '', $item['nominal_potongan']) : 0;
                $minPeriode = !empty($item['min_periode_potongan']) ? $item['min_periode_potongan'] : null;
                $maxPeriode = !empty($item['max_periode_potongan']) ? $item['max_periode_potongan'] : null;
                $idPu = $item['id_potongan_umana'] ?? null;

                if (empty($jenis)) continue;

                $payload = [
                    'id_penempatan'        => $id_penempatan,
                    'jenis_potongan'       => $jenis,
                    'nominal_potongan'     => $nominal,
                    'min_periode_potongan' => $minPeriode,
                    'max_periode_potongan' => $maxPeriode,
                ];

                if (!empty($idPu)) {
                    DB::table('potongan_umana')->where('id_potongan_umana', $idPu)->update($payload);
                } else {
                    DB::table('potongan_umana')->insert($payload);
                }
            }
        }

        return response()->json(['status' => true, 'message' => 'Data Potongan berhasil disimpan.']);
    }

    /**
     * Kirim pengajuan kehadiran (Sudah / Revisi -> Terkirim)
     */
    public function kirim(Request $request)
    {
        $id = $request->input('id_kehadiran_lembaga');
        if (!$id) {
            return response()->json(['status' => false, 'message' => 'ID periode tidak ditemukan.'], 400);
        }

        $periode = KehadiranLembaga::findOrFail($id);

        $periode->update(['status' => 'Terkirim']);

        \App\Models\LogRiwayatBarokah::create([
            'id_kehadiran_lembaga' => $id,
            'status_aksi'          => 'Kirim',
            'waktu_eksekusi'       => now(),
            'id_pengguna'          => Auth::id() ?? 1,
            'catatan_log'          => '',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Periode kehadiran berhasil dikirim ke Evaluasi.',
        ]);
    }

    /**
     * Finalisasi / Validasi pengajuan: Setujui (ACC) atau Kembalikan (Revisi)
     */
    public function validasi(Request $request)
    {
        $id = $request->input('id_kehadiran_lembaga');
        $actionType = $request->input('action_type', 'setuju'); // 'setuju' | 'revisi'
        $catatanUmum = $request->input('catatan_umum_pimpinan', '');
        $catatanKhusus = $request->input('catatan_khusus', []); // array [id_penempatan => text]

        if (!$id) {
            return response()->json(['status' => false, 'message' => 'ID periode tidak ditemukan.'], 400);
        }

        $user = Auth::user();
        $jabatan = $user ? $user->jabatan : 'SuperAdmin';
        if (!in_array($jabatan, ['SuperAdmin', 'Evaluasi'])) {
            return response()->json(['status' => false, 'message' => 'Anda tidak memiliki hak akses untuk memvalidasi.'], 403);
        }

        $res = \App\Services\HitungBarokahService::hitungPeriode($id);
        if (!$res['periode']) {
            return response()->json(['status' => false, 'message' => 'Data periode tidak ditemukan.'], 404);
        }

        DB::beginTransaction();
        try {
            $rows = $res['rows'];
            $totals = $res['totals'];

            $statusNew = ($actionType === 'revisi') ? 'Revisi' : 'acc';

            // Bersihkan snapshot total_barokah periode ini jika ada
            DB::table('total_barokah')->where('id_kehadiran', $id)->delete();

            // Jika disetujui (ACC), buat snapshot ke tabel total_barokah
            if ($actionType !== 'revisi') {
                $snapshotData = [];
                foreach ($rows as $r) {
                    $catatanUmana = $catatanKhusus[$r->id_penempatan] ?? '';
                    $snapshotData[] = [
                        'id_penempatan'        => $r->id_penempatan,
                        'id_kehadiran'         => $id,
                        'bulan'                => $r->bulan,
                        'tahun'                => $r->tahun,
                        'tunjab'               => (int)$r->tunjab,
                        'mp'                   => (int)$r->mp,
                        'kehadiran'            => (int)$r->jumlah_hadir,
                        'nominal_kehadiran'    => (int)$r->nominal_kehadiran,
                        'tunkel'               => (int)$r->tunkel,
                        'tunj_anak'            => (int)$r->tunj_anak,
                        'tmp'                  => (int)$r->tmp,
                        'kehormatan'           => (int)$r->nilai_kehormatan,
                        'tbk'                  => (int)$r->tbk,
                        'potongan'             => (int)$r->potongan,
                        'barokah_khusus'       => 0,
                        'diterima'             => (int)$r->diterima,
                        'catatan_khusus_umana' => $catatanUmana,
                        'status'               => 'acc',
                    ];
                }
                if (!empty($snapshotData)) {
                    DB::table('total_barokah')->insert($snapshotData);
                }
            }

            // Update status dan catatan pimpinan di tabel kehadiran_lembaga
            KehadiranLembaga::where('id_kehadiran_lembaga', $id)->update([
                'status'                => $statusNew,
                'jumlah_total'          => (int)$totals['grand_total'],
                'catatan_umum_pimpinan' => $catatanUmum,
            ]);

            // Log aktivitas
            $statusLog = ($actionType === 'revisi') ? 'Revisi' : 'acc';

            \App\Models\LogRiwayatBarokah::create([
                'id_kehadiran_lembaga' => $id,
                'status_aksi'          => $statusLog,
                'waktu_eksekusi'       => now(),
                'id_pengguna'          => Auth::id() ?? 1,
                'catatan_log'          => trim((string)$catatanUmum),
            ]);

            DB::commit();

            $msg = ($actionType === 'revisi')
                ? 'Dokumen berhasil dikembalikan ke Admin Lembaga untuk revisi.'
                : 'Periode kehadiran berhasil disetujui (ACC) dan tersimpan ke rekap total barokah.';

            return response()->json([
                'status'  => true,
                'message' => $msg,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Riwayat status / log barokah
     */
    public function riwayat($id)
    {
        $logs = DB::table('log_riwayat_barokah as l')
            ->leftJoin('pengguna as p', 'p.id_pengguna', '=', 'l.id_pengguna')
            ->select('l.*', 'p.username', 'p.jabatan')
            ->where('l.id_kehadiran_lembaga', $id)
            ->orderBy('l.waktu_eksekusi', 'desc')
            ->get();

        return response()->json(['data' => $logs]);
    }

    /**
     * Reset status periode kehadiran
     */
    public function reset(Request $request)
    {
        $id = $request->input('id_kehadiran_lembaga');
        $mode = $request->input('mode', 'kehadiran'); // 'kehadiran' | 'total'

        $user = Auth::user();
        $jabatan = $user ? $user->jabatan : 'SuperAdmin';
        if (!in_array($jabatan, ['SuperAdmin', 'Evaluasi'])) {
            return response()->json(['status' => false, 'message' => 'Anda tidak memiliki hak akses untuk reset.'], 403);
        }

        $periode = KehadiranLembaga::findOrFail($id);

        DB::beginTransaction();
        try {
            if ($mode === 'kehadiran') {
                \App\Models\Kehadiran::where('id_kehadi', $id)->delete();
                DB::table('total_barokah')->where('id_kehadiran', $id)->delete();
                $periode->update([
                    'status'       => 'Belum',
                    'jumlah_total' => 0,
                ]);
                $catatan = 'Reset total: data input kehadiran dan rekap barokah dihapus, status kembali ke Belum.';
            } else {
                DB::table('total_barokah')->where('id_kehadiran', $id)->delete();
                $periode->update([
                    'status'       => 'Sudah',
                    'jumlah_total' => 0,
                ]);
                $catatan = 'Reset status: rekap total barokah dihapus, status dikembalikan ke Sudah.';
            }

            \App\Models\LogRiwayatBarokah::create([
                'id_kehadiran_lembaga' => $id,
                'status_aksi'          => 'Reset',
                'waktu_eksekusi'       => now(),
                'id_pengguna'          => Auth::id() ?? 1,
                'catatan_log'          => $catatan,
            ]);

            DB::commit();

            return response()->json(['status' => true, 'message' => $catatan]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Gagal melakukan reset: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Cek data validasi silang Struktural & Pengajar untuk memastikan tidak ada tunjangan ganda
     */
    public function cekValidasiUmana($nik)
    {
        $umana = DB::table('umana')->where('nik', $nik)->first();
        if (!$umana) {
            return response()->json(['status' => false, 'message' => 'Data Umana tidak ditemukan.'], 404);
        }

        // 1. Data Penempatan Struktural lintas lembaga
        $struktural = DB::table('penempatan as p')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'p.id_lembaga')
            ->leftJoin('ketentuan_barokah as kb', 'kb.id_ketentuan', '=', 'p.id_ketentuan')
            ->where('p.nik', $nik)
            ->where(function($q) {
                $q->whereNull('p.tgl_selesai')
                  ->orWhere('p.tgl_selesai', '0000-00-00')
                  ->orWhere('p.tgl_selesai', '>=', now()->toDateString());
            })
            ->select(
                'p.id_penempatan',
                'l.nama_lembaga',
                'kb.nama_jabatan',
                'p.tunj_kel',
                'p.tunj_anak',
                'p.kehormatan',
                'p.status as status_penempatan'
            )
            ->get();

        // 2. Data Penugasan Pengajar (Guru / Dosen) lintas lembaga
        $pengajar = DB::table('pengajar as peng')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'peng.id_lembaga')
            ->where('peng.nik', $nik)
            ->where(function($q) {
                $q->whereNull('peng.tgl_selesai')
                  ->orWhere('peng.tgl_selesai', '0000-00-00')
                  ->orWhere('peng.tgl_selesai', '>=', now()->toDateString());
            })
            ->select(
                'peng.id_pengajar',
                'l.nama_lembaga',
                'peng.kategori as jenis_pengajar', // GTY / GTT / DTY / DTT
                'peng.tunj_kel',
                'peng.tunj_anak',
                'peng.kehormatan',
                'peng.status as status_pengajar'
            )
            ->get();

        $namaFull = trim(($umana->gelar_depan ?? '') . ' ' . $umana->nama_lengkap . ' ' . ($umana->gelar_belakang ?? ''));

        return response()->json([
            'status' => true,
            'umana' => [
                'nik'                 => $umana->nik,
                'nama_lengkap'        => $namaFull,
                'status_sertifikasi'  => !empty($umana->status_sertifikasi) ? $umana->status_sertifikasi : 'Belum Sertifikasi',
                'ikatan_khidmah'      => $umana->ikatan_khidmah ?? '-',
            ],
            'struktural' => $struktural,
            'pengajar'   => $pengajar,
        ]);
    }

    /**
     * Cetak Rekap Barokah Struktural (Landscape Legal)
     */
    public function cetak($id)
    {
        // Decode jika terenkripsi base64 seperti di legacy CI3
        $decodedId = is_numeric($id) ? (int)$id : (int)base64_decode($id);
        if (!$decodedId) {
            $decodedId = (int)$id;
        }

        $res = HitungBarokahService::hitungPeriode($decodedId);

        if (empty($res['periode']) || empty($res['rows'])) {
            abort(404, 'Data periode tidak ditemukan atau belum lengkap');
        }

        $pdfContent = PdfBarokahService::cetakRekapBarokah($res);
        $filename = 'Rekap_Barokah_' . str_replace(' ', '_', $res['periode']->nama_lembaga ?? 'Lembaga') . '_' . ($res['periode']->bulan ?? '') . '_' . ($res['periode']->tahun ?? '') . '.pdf';

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }

    /**
     * Cetak Potongan Barokah Struktural (Portrait Legal)
     */
    public function cetakPotongan($id, $id_kehadiran = null)
    {
        $idLembaga = is_numeric($id) ? (int)$id : (int)base64_decode($id);
        if (!$idLembaga) {
            $idLembaga = (int)$id;
        }

        $bulanLaporan = null;
        $tahunLaporan = null;
        $filterTanggal = date('Y-m-01');

        if (!empty($id_kehadiran)) {
            $idKehadiranDecoded = is_numeric($id_kehadiran) ? (int)$id_kehadiran : (int)base64_decode($id_kehadiran);
            $periodeLaporan = DB::table('kehadiran_lembaga')->where('id_kehadiran_lembaga', $idKehadiranDecoded)->first();

            if ($periodeLaporan) {
                $mapBulan = [
                    'januari' => '01', 'februari' => '02', 'maret' => '03', 'april' => '04',
                    'mei' => '05', 'juni' => '06', 'juli' => '07', 'agustus' => '08',
                    'september' => '09', 'oktober' => '10', 'november' => '11', 'desember' => '12'
                ];
                $blnRaw = strtolower(trim((string)$periodeLaporan->bulan));
                $blnNum = is_numeric($blnRaw) ? str_pad((int)$blnRaw, 2, '0', STR_PAD_LEFT) : ($mapBulan[$blnRaw] ?? date('m'));
                $thnStr = (int)$periodeLaporan->tahun;

                $filterTanggal = sprintf('%04d-%02d-01', $thnStr, (int)$blnNum);
                $bulanLaporan = ucfirst($blnRaw);
                $tahunLaporan = $thnStr;
            }
        }

        $query = "
            SELECT 
                u.nama_lengkap, 
                u.gelar_depan, 
                u.gelar_belakang, 
                pot.nama_potongan, 
                pp.nominal_potongan, 
                pp.min_periode_potongan, 
                pp.max_periode_potongan, 
                l.nama_lembaga, 
                l.id_bidang
            FROM umana u
            JOIN penempatan p ON u.nik = p.nik
            JOIN potongan_umana pp ON pp.id_penempatan = p.id_penempatan
            JOIN potongan pot ON pp.jenis_potongan = pot.id_potongan
            JOIN lembaga l ON p.id_lembaga = l.id_lembaga
            WHERE pp.min_periode_potongan <= ?
              AND pp.max_periode_potongan >= ?
              AND p.id_lembaga = ?
            ORDER BY u.nama_lengkap ASC
        ";

        $potonganList = DB::select($query, [$filterTanggal, $filterTanggal, $idLembaga]);
        $lembagaInfo  = DB::table('lembaga')->where('id_lembaga', $idLembaga)->first();

        $pdfContent = PdfBarokahService::cetakPotongan($potonganList, $lembagaInfo, $bulanLaporan, $tahunLaporan);
        $namaLembaga = $lembagaInfo ? str_replace(' ', '_', $lembagaInfo->nama_lembaga) : 'Lembaga';
        $filename = 'Potongan_Struktural_' . $namaLembaga . '.pdf';

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
