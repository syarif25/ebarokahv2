<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KehadiranLembaga;
use App\Models\KehadiranSatpam;
use App\Models\TotalBarokahSatpam;
use App\Models\Lembaga;
use App\Models\Satpam;
use App\Models\LogRiwayatBarokah;
use App\Services\HitungSatpamService;
use App\Services\PdfBarokahService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KehadiranSatpamController extends Controller
{
    const ID_LEMBAGA_SATPAM = 59;

    /**
     * Halaman Utama: Daftar Kehadiran Satpam
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $jabatan = $user ? $user->jabatan : 'SuperAdmin';
            $canManageAll = in_array($jabatan, ['SuperAdmin', 'Evaluasi']);

            $sql = "
                SELECT 
                    kl.id_kehadiran_lembaga,
                    l.nama_lembaga,
                    kl.bulan,
                    kl.tahun,
                    kl.status,
                    CASE 
                        WHEN kl.status != 'Belum' AND ks_counts.total_detail IS NOT NULL THEN ks_counts.total_detail
                        ELSE COALESCE(s_counts.total_satpam, 0)
                    END AS jumlah_satpam
                FROM kehadiran_lembaga kl
                JOIN lembaga l ON kl.id_lembaga = l.id_lembaga
                LEFT JOIN (
                    SELECT id_kehadiran_lembaga, COUNT(*) AS total_detail
                    FROM kehadiran_satpam
                    GROUP BY id_kehadiran_lembaga
                ) ks_counts ON ks_counts.id_kehadiran_lembaga = kl.id_kehadiran_lembaga
                LEFT JOIN (
                    SELECT COUNT(*) AS total_satpam
                    FROM satpam
                    WHERE status = 'Aktif'
                      AND (tgl_selesai IS NULL OR tgl_selesai = '0000-00-00' OR tgl_selesai >= CURDATE())
                ) s_counts ON 1 = 1
                WHERE kl.kategori = 'Satpam'
                ORDER BY kl.id_kehadiran_lembaga DESC
            ";

            $data = DB::select($sql);

            $result = array_map(function ($item) use ($canManageAll) {
                return [
                    'id_kehadiran_lembaga' => $item->id_kehadiran_lembaga,
                    'nama_lembaga'         => $item->nama_lembaga ?? 'Satpam',
                    'jumlah_satpam'        => (int)$item->jumlah_satpam,
                    'bulan'                => $item->bulan,
                    'tahun'                => $item->tahun,
                    'status'               => $item->status,
                    'can_reset'            => $canManageAll,
                ];
            }, $data);

            return response()->json(['data' => $result]);
        }

        $lembagaSatpam = Lembaga::find(self::ID_LEMBAGA_SATPAM);
        if (!$lembagaSatpam) {
            $lembagaSatpam = Lembaga::where('nama_lembaga', 'like', '%Satpam%')->first();
        }

        return view('kehadiran-satpam.index', compact('lembagaSatpam'));
    }

    /**
     * Tambah Blanko Baru Kehadiran Satpam
     */
    public function storeBlanko(Request $request)
    {
        $request->validate([
            'bulan' => 'required',
            'tahun' => 'required',
        ]);

        $idLembaga = $request->input('id_lembaga', self::ID_LEMBAGA_SATPAM);
        $bulan     = $request->input('bulan');
        $tahun     = $request->input('tahun');

        // Cek duplikasi blanko
        $exists = KehadiranLembaga::where('id_lembaga', $idLembaga)
            ->where('kategori', 'Satpam')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => false,
                'message' => 'Blanko Kehadiran Satpam untuk periode ini sudah ada!'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $kehadiran = KehadiranLembaga::create([
                'id_lembaga'   => $idLembaga,
                'kategori'     => 'Satpam',
                'bulan'        => $bulan,
                'tahun'        => $tahun,
                'status'       => 'Belum',
                'jumlah_total' => 0,
                'file'         => '',
            ]);

            // Catat log riwayat
            LogRiwayatBarokah::create([
                'id_kehadiran_lembaga' => $kehadiran->id_kehadiran_lembaga,
                'status_aksi'          => 'Buat Blanko',
                'waktu_eksekusi'       => now(),
                'id_pengguna'          => Auth::id() ?? 1,
                'catatan_log'          => '',
            ]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Blanko Kehadiran Satpam berhasil dibuat!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Gagal membuat blanko: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Halaman Input Rekap Kehadiran Satpam
     */
    public function rekap($id)
    {
        $periode = KehadiranLembaga::with('lembaga')->where('kategori', 'Satpam')->findOrFail($id);

        $satpam = DB::table('satpam')
            ->join('umana', 'satpam.nik', '=', 'umana.nik')
            ->leftJoin('transport', 'satpam.id_transport', '=', 'transport.id_transport')
            ->where('satpam.status', 'Aktif')
            ->where(function ($q) {
                $q->whereNull('satpam.tgl_selesai')
                  ->orWhere('satpam.tgl_selesai', '0000-00-00')
                  ->orWhere('satpam.tgl_selesai', '>=', now()->toDateString());
            })
            ->select(
                'satpam.id_satpam',
                'satpam.nik',
                'satpam.is_danru',
                'satpam.status',
                'umana.nama_lengkap',
                'umana.gelar_depan',
                'umana.gelar_belakang',
                'transport.nominal_transport'
            )
            ->orderBy('umana.nama_lengkap', 'asc')
            ->get();

        $existingKehadiran = KehadiranSatpam::where('id_kehadiran_lembaga', $periode->id_kehadiran_lembaga)
            ->get()
            ->keyBy('id_satpam');

        return view('kehadiran-satpam.rekap', compact('periode', 'satpam', 'existingKehadiran'));
    }

    /**
     * Simpan Rekap Kehadiran Satpam
     */
    public function saveRekap(Request $request)
    {
        $request->validate([
            'id_kehadiran_lembaga' => 'required',
            'bulan'                => 'required',
            'tahun'                => 'required',
        ]);

        $periode = KehadiranLembaga::where('kategori', 'Satpam')->findOrFail($request->id_kehadiran_lembaga);

        // Upload File PDF Absensi
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
            return response()->json([
                'status'  => false,
                'message' => 'File bukti absensi (PDF) wajib diunggah!'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $idSatpams   = $request->input('id_satpam', []);
            $jumlahHari  = $request->input('jumlah_hari', []);
            $jumlahShift = $request->input('jumlah_shift', []);
            $jumlahDini  = $request->input('jumlah_dini', []);

            $dataKehadiran = [];
            foreach ($idSatpams as $i => $ids) {
                $h  = isset($jumlahHari[$i])  && $jumlahHari[$i]  !== '' ? max(0, (int)$jumlahHari[$i])  : 0;
                $s  = isset($jumlahShift[$i]) && $jumlahShift[$i] !== '' ? max(0, (int)$jumlahShift[$i]) : 0;
                $dn = isset($jumlahDini[$i])  && $jumlahDini[$i]  !== '' ? max(0, (int)$jumlahDini[$i])  : 0;

                $dataKehadiran[] = [
                    'id_kehadiran_lembaga' => $periode->id_kehadiran_lembaga,
                    'id_satpam'            => $ids,
                    'bulan'                => $request->bulan,
                    'tahun'                => $request->tahun,
                    'jumlah_hari'          => $h,
                    'jumlah_shift'         => $s,
                    'jumlah_dinihari'      => $dn,
                    'tgl_input'            => now(),
                ];
            }

            // Hapus data lama periode ini
            KehadiranSatpam::where('id_kehadiran_lembaga', $periode->id_kehadiran_lembaga)->delete();

            if (!empty($dataKehadiran)) {
                KehadiranSatpam::insert($dataKehadiran);
            }

            $periode->update([
                'status' => 'Sudah',
                'file'   => $fileName,
            ]);

            LogRiwayatBarokah::create([
                'id_kehadiran_lembaga' => $periode->id_kehadiran_lembaga,
                'status_aksi'          => 'Isi Rekap',
                'waktu_eksekusi'       => now(),
                'id_pengguna'          => Auth::id() ?? 1,
                'catatan_log'          => '',
            ]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Rekap kehadiran Satpam berhasil disimpan!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menyimpan rekap: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Halaman Koreksi / Cek Barokah Satpam
     */
    public function koreksi($id)
    {
        $periode = KehadiranLembaga::with('lembaga')->where('kategori', 'Satpam')->findOrFail($id);

        $tarif = HitungSatpamService::getTarif();

        $sql = "
            SELECT 
                ks.id_kehadiran_satpam,
                ks.id_kehadiran_lembaga,
                ks.id_satpam,
                ks.bulan,
                ks.tahun,
                ks.jumlah_hari,
                ks.jumlah_shift,
                ks.jumlah_dinihari,
                s.is_danru,
                u.gelar_depan,
                u.nama_lengkap,
                u.gelar_belakang,
                COALESCE(t.nominal_transport, 0) AS nominal_transport,
                (SELECT COALESCE(SUM(nominal_potongan), 0) FROM potongan_satpam WHERE id_satpam = ks.id_satpam AND (max_periode_potongan >= CURDATE() OR max_periode_potongan IS NULL OR max_periode_potongan = '0000-00-00')) AS nominal_potongan
            FROM kehadiran_satpam ks
            LEFT JOIN satpam s     ON s.id_satpam    = ks.id_satpam
            LEFT JOIN umana  u     ON u.nik          = s.nik
            LEFT JOIN transport t  ON t.id_transport = s.id_transport
            WHERE ks.id_kehadiran_lembaga = ?
            ORDER BY u.nama_lengkap ASC, ks.id_kehadiran_satpam ASC
        ";

        $isilist = DB::select($sql, [$id]);

        // Hitung total dan data baris
        $sum_transport = 0;
        $sum_barokah   = 0;
        $sum_dini      = 0;
        $sum_danru     = 0;
        $sum_potongan  = 0;
        $sum_grand     = 0;

        foreach ($isilist as $row) {
            $calc = HitungSatpamService::calculateRow($row, $tarif);
            $row->calc = $calc;

            $sum_transport += $calc['jumlah_transport'];
            $sum_barokah   += $calc['jumlah_barokah'];
            $sum_dini      += $calc['jumlah_konsumsi'];
            $sum_danru     += $calc['nominal_danru'];
            $sum_potongan  += $calc['nominal_potongan'];
            $sum_grand     += $calc['diterima'];
        }

        $totals = [
            'total_transport' => $sum_transport,
            'total_barokah'   => $sum_barokah,
            'total_dinihari'  => $sum_dini,
            'total_danru'     => $sum_danru,
            'total_potongan'  => $sum_potongan,
            'grand_total'     => $sum_grand,
        ];

        return view('kehadiran-satpam.koreksi', compact('periode', 'isilist', 'totals', 'tarif'));
    }

    /**
     * Update Koreksi Satpam Per Baris (AJAX)
     */
    public function updateRow(Request $request)
    {
        $idRow = $request->input('id_kehadiran_satpam');
        $idKL  = $request->input('id_kehadiran_lembaga');
        $hari  = (int)$request->input('jumlah_hari', 0);
        $shift = (int)$request->input('jumlah_shift', 0);
        $dini  = (int)$request->input('jumlah_dinihari', 0);

        if (!$idRow || !$idKL) {
            return response()->json(['status' => false, 'message' => 'Parameter tidak lengkap.'], 422);
        }

        if ($hari < 0 || $shift < 0 || $dini < 0) {
            return response()->json(['status' => false, 'message' => 'Nilai kehadiran tidak valid.'], 422);
        }

        DB::beginTransaction();
        try {
            DB::table('kehadiran_satpam')
                ->where('id_kehadiran_satpam', $idRow)
                ->update([
                    'jumlah_hari'     => $hari,
                    'jumlah_shift'    => $shift,
                    'jumlah_dinihari' => $dini,
                ]);

            $sqlRow = "
                SELECT 
                    ks.*, 
                    s.is_danru, 
                    t.nominal_transport,
                    (SELECT COALESCE(SUM(nominal_potongan), 0) FROM potongan_satpam WHERE id_satpam = ks.id_satpam AND (max_periode_potongan >= CURDATE() OR max_periode_potongan IS NULL OR max_periode_potongan = '0000-00-00')) AS nominal_potongan
                FROM kehadiran_satpam ks
                JOIN satpam s ON s.id_satpam = ks.id_satpam
                LEFT JOIN transport t ON t.id_transport = s.id_transport
                WHERE ks.id_kehadiran_satpam = ?
            ";
            $row = DB::selectOne($sqlRow, [$idRow]);

            $tarif = HitungSatpamService::getTarif();
            $calc = HitungSatpamService::calculateRow($row, $tarif);
            $totals = HitungSatpamService::calculateTotals($idKL);

            DB::commit();

            return response()->json([
                'status' => true,
                'row'    => [
                    'id'               => (int)$row->id_kehadiran_satpam,
                    'jumlah_hari'      => $calc['jumlah_hari'],
                    'jumlah_shift'     => $calc['jumlah_shift'],
                    'jumlah_dinihari'  => $calc['jumlah_dinihari'],
                    'jumlah_transport' => $calc['jumlah_transport'],
                    'jumlah_barokah'   => $calc['jumlah_barokah'],
                    'jumlah_konsumsi'  => $calc['jumlah_konsumsi'],
                    'nominal_danru'    => $calc['nominal_danru'],
                    'potongan'         => $calc['nominal_potongan'],
                    'subtotal'         => $calc['diterima'],
                ],
                'totals' => $totals,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Gagal mengupdate baris: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Ambil opsi jenis potongan (JSON)
     */
    public function getPotonganOptions()
    {
        $data = DB::table('potongan')->select('id_potongan', 'nama_potongan')->orderBy('nama_potongan', 'asc')->get();
        return response()->json($data);
    }

    /**
     * Ambil rincian potongan per satpam (JSON)
     */
    public function detailPotongan($id_satpam)
    {
        $data = DB::table('potongan_satpam as ps')
            ->leftJoin('potongan as p', 'p.id_potongan', '=', 'ps.jenis_potongan')
            ->where('ps.id_satpam', $id_satpam)
            ->select('ps.*', 'p.nama_potongan')
            ->get();

        return response()->json($data);
    }

    /**
     * Simpan rincian potongan per satpam
     */
    public function savePotongan(Request $request)
    {
        $id_satpam = $request->input('id_satpam');
        $potongan_data = $request->input('potongan', []);

        if ($potongan_data && is_array($potongan_data)) {
            foreach ($potongan_data as $pot) {
                $jenis = $pot['jenis_potongan'] ?? '';
                $nominal = isset($pot['nominal_potongan']) ? (int)str_replace(['.', ','], '', $pot['nominal_potongan']) : 0;
                $min_periode = !empty($pot['min_periode_potongan']) ? $pot['min_periode_potongan'] : null;
                $max_periode = !empty($pot['max_periode_potongan']) ? $pot['max_periode_potongan'] : null;
                $id_potongan_satpam = $pot['id_potongan_satpam'] ?? '';

                if (empty($jenis)) continue;

                $data = [
                    'id_satpam'            => $id_satpam,
                    'jenis_potongan'       => $jenis,
                    'nominal_potongan'     => $nominal,
                    'min_periode_potongan' => $min_periode,
                    'max_periode_potongan' => $max_periode
                ];

                if (!empty($id_potongan_satpam)) {
                    DB::table('potongan_satpam')->where('id_potongan_satpam', $id_potongan_satpam)->update($data);
                } else {
                    DB::table('potongan_satpam')->insert($data);
                }
            }
        }

        return response()->json(['status' => true, 'message' => 'Data Potongan berhasil disimpan.']);
    }


    /**
     * Kirim Koreksi (Status: Sudah -> Terkirim)
     */
    public function kirim(Request $request)
    {
        $id = $request->input('id_kehadiran_lembaga');
        $periode = KehadiranLembaga::where('kategori', 'Satpam')->findOrFail($id);

        $periode->update(['status' => 'Terkirim']);

        LogRiwayatBarokah::create([
            'id_kehadiran_lembaga' => $periode->id_kehadiran_lembaga,
            'status_aksi'          => 'Kirim',
            'waktu_eksekusi'       => now(),
            'id_pengguna'          => Auth::id() ?? 1,
            'catatan_log'          => '',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Periode barokah Satpam berhasil dikirim!'
        ]);
    }

    /**
     * Setujui / Finalisasi Barokah Satpam (Status -> acc)
     */
    public function validasi(Request $request)
    {
        $id = $request->input('id_kehadiran_lembaga');
        $periode = KehadiranLembaga::where('kategori', 'Satpam')->findOrFail($id);

        try {
            $res = HitungSatpamService::generateSnapshot($id);

            LogRiwayatBarokah::create([
                'id_kehadiran_lembaga' => $periode->id_kehadiran_lembaga,
                'status_aksi'          => 'Setujui',
                'waktu_eksekusi'       => now(),
                'id_pengguna'          => Auth::id() ?? 1,
                'catatan_log'          => '',
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Periode barokah Satpam berhasil disetujui & disimpan!',
                'total'   => $res['grand_total'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Gagal menyetujui: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset Kehadiran Satpam (Total saja atau Kehadiran + Total)
     */
    public function reset(Request $request)
    {
        $user = Auth::user();
        $jabatan = $user ? $user->jabatan : 'SuperAdmin';
        if (!in_array($jabatan, ['SuperAdmin', 'Evaluasi'])) {
            return response()->json(['status' => false, 'message' => 'Hanya SuperAdmin dan Evaluasi yang dapat mereset data.'], 403);
        }

        $idKL  = $request->input('id_kehadiran_lembaga');
        $scope = $request->input('scope', 'total'); // 'total' atau 'all'

        if (!$idKL) {
            return response()->json(['status' => false, 'message' => 'ID kehadiran lembaga tidak dikirim.'], 422);
        }

        $periode = KehadiranLembaga::where('kategori', 'Satpam')->findOrFail($idKL);

        DB::beginTransaction();
        try {
            // 1. Selalu hapus snapshot total
            DB::table('total_barokah_satpam')->where('id_kehadiran_lembaga', $idKL)->delete();

            // 2. Opsional hapus rekap kehadiran harian
            if ($scope === 'all') {
                DB::table('kehadiran_satpam')->where('id_kehadiran_lembaga', $idKL)->delete();
            }

            // 3. Kembalikan status ke Belum
            $periode->update([
                'status'       => 'Belum',
                'jumlah_total' => 0,
            ]);

            LogRiwayatBarokah::create([
                'id_kehadiran_lembaga' => $periode->id_kehadiran_lembaga,
                'status_aksi'          => 'Reset',
                'waktu_eksekusi'       => now(),
                'id_pengguna'          => Auth::id() ?? 1,
                'catatan_log'          => $scope === 'all' ? 'Reset Kehadiran + Total' : 'Reset Total Saja',
            ]);

            DB::commit();

            $msg = ($scope === 'all')
                ? 'Input Kehadiran + Total berhasil direset. Status dikembalikan ke Belum.'
                : 'Total berhasil direset. Status dikembalikan ke Belum.';

            return response()->json(['status' => true, 'message' => $msg]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Reset gagal: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Riwayat Audit Trail Log Barokah
     */
    public function riwayat($id)
    {
        $logs = DB::table('log_riwayat_barokah as lrb')
            ->leftJoin('pengguna as p', 'p.id_pengguna', '=', 'lrb.id_pengguna')
            ->where('lrb.id_kehadiran_lembaga', $id)
            ->orderBy('lrb.waktu_eksekusi', 'asc')
            ->select('lrb.*', 'p.username', 'p.nama_lengkap', 'p.jabatan')
            ->get();

        return response()->json([
            'status' => true,
            'logs'   => $logs,
        ]);
    }

    /**
     * Cetak PDF Rekap Barokah Satpam
     */
    public function cetak($id)
    {
        $periode = KehadiranLembaga::with('lembaga')->where('kategori', 'Satpam')->findOrFail($id);

        $tarif = HitungSatpamService::getTarif();

        // Jika sudah acc/selesai dan ada di total_barokah_satpam, ambil dari total_barokah_satpam
        $snapshot = DB::table('total_barokah_satpam as tbs')
            ->join('satpam as s', 's.id_satpam', '=', 'tbs.id_satpam')
            ->join('umana as u', 'u.nik', '=', 's.nik')
            ->leftJoin('transport as t', 't.id_transport', '=', 's.id_transport')
            ->where('tbs.id_kehadiran_lembaga', $id)
            ->select(
                'tbs.*',
                's.is_danru',
                'u.gelar_depan',
                'u.nama_lengkap',
                'u.gelar_belakang',
                't.nominal_transport'
            )
            ->orderBy('u.nama_lengkap', 'asc')
            ->get();

        if ($snapshot->isNotEmpty()) {
            $isilist = $snapshot;
        } else {
            $sql = "
                SELECT 
                    ks.id_kehadiran_satpam,
                    ks.id_kehadiran_lembaga,
                    ks.id_satpam,
                    ks.bulan,
                    ks.tahun,
                    ks.jumlah_hari,
                    ks.jumlah_shift,
                    ks.jumlah_dinihari,
                    s.is_danru,
                    u.gelar_depan,
                    u.nama_lengkap,
                    u.gelar_belakang,
                    COALESCE(t.nominal_transport, 0) AS nominal_transport,
                    (SELECT COALESCE(SUM(nominal_potongan), 0) FROM potongan_satpam WHERE id_satpam = ks.id_satpam AND (max_periode_potongan >= CURDATE() OR max_periode_potongan IS NULL OR max_periode_potongan = '0000-00-00')) AS nominal_potongan
                FROM kehadiran_satpam ks
                LEFT JOIN satpam s     ON s.id_satpam    = ks.id_satpam
                LEFT JOIN umana  u     ON u.nik          = s.nik
                LEFT JOIN transport t  ON t.id_transport = s.id_transport
                WHERE ks.id_kehadiran_lembaga = ?
                ORDER BY u.nama_lengkap ASC, ks.id_kehadiran_satpam ASC
            ";
            $isilist = DB::select($sql, [$id]);
        }

        $pdfContent = PdfBarokahService::cetakRekapSatpam($periode, $isilist, $tarif['honorDanru']);

        return response($pdfContent, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Rekap_Barokah_Satpam_' . $periode->bulan . '_' . $periode->tahun . '.pdf"');
    }

    /**
     * Lihat Laporan Final (Read-Only Snapshot)
     */
    public function rincian($id)
    {
        $periode = KehadiranLembaga::with('lembaga')->where('kategori', 'Satpam')->findOrFail($id);

        $isilist = DB::table('total_barokah_satpam as tb')
            ->join('satpam as s', 'tb.id_satpam', '=', 's.id_satpam')
            ->join('umana as u', 's.nik', '=', 'u.nik')
            ->where('tb.id_kehadiran_lembaga', $id)
            ->select(
                'tb.*',
                's.is_danru',
                'u.nama_lengkap',
                'u.gelar_depan',
                'u.gelar_belakang'
            )
            ->orderBy('u.nama_lengkap', 'asc')
            ->get();

        return view('kehadiran-satpam.rincian', compact('periode', 'isilist'));
    }
}
