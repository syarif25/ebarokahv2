<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KehadiranLembaga;
use App\Models\KehadiranPengajar;
use App\Models\Lembaga;
use App\Models\Pengajar;
use App\Models\LogRiwayatBarokah;
use App\Services\HitungPengajarService;
use App\Services\PdfBarokahService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KehadiranPengajarController extends Controller
{
    /**
     * Halaman Utama: Daftar Kehadiran Pengajar (Guru / Dosen)
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = Auth::user();
            $jabatan = $user ? $user->jabatan : 'SuperAdmin';
            $canManageAll = in_array($jabatan, ['SuperAdmin', 'Evaluasi']);

            $bindings = [];
            $whereClause = "WHERE kl.kategori = 'Pengajar'";

            // RBAC: Jika AdminLembaga, batasi hanya lembaga miliknya
            if (!$canManageAll && $user && $user->lembaga) {
                $whereClause .= " AND kl.id_lembaga = ?";
                $bindings[] = $user->lembaga;
            }

            $sql = "
                SELECT 
                    kl.id_kehadiran_lembaga,
                    l.nama_lembaga,
                    kl.bulan,
                    kl.tahun,
                    kl.status,
                    CASE 
                        WHEN kl.status != 'Belum' AND kp_counts.total_detail IS NOT NULL THEN kp_counts.total_detail
                        ELSE COALESCE(p_counts.total_pengajar, 0)
                    END AS jumlah_pengajar
                FROM kehadiran_lembaga kl
                JOIN lembaga l ON kl.id_lembaga = l.id_lembaga
                LEFT JOIN (
                    SELECT id_kehadiran_lembaga, COUNT(*) AS total_detail
                    FROM kehadiran_pengajar
                    GROUP BY id_kehadiran_lembaga
                ) kp_counts ON kp_counts.id_kehadiran_lembaga = kl.id_kehadiran_lembaga
                LEFT JOIN (
                    SELECT id_lembaga, COUNT(*) AS total_pengajar
                    FROM pengajar
                    WHERE status != 'Tidak Aktif'
                      AND (tgl_selesai IS NULL OR tgl_selesai = '0000-00-00' OR tgl_selesai >= CURDATE())
                    GROUP BY id_lembaga
                ) p_counts ON p_counts.id_lembaga = kl.id_lembaga
                {$whereClause}
                ORDER BY kl.id_kehadiran_lembaga DESC
            ";

            $data = DB::select($sql, $bindings);

            $result = array_map(function ($item) use ($canManageAll) {
                return [
                    'id_kehadiran_lembaga' => $item->id_kehadiran_lembaga,
                    'nama_lembaga'         => $item->nama_lembaga ?? '-',
                    'jumlah_pengajar'      => (int)$item->jumlah_pengajar,
                    'bulan'                => $item->bulan,
                    'tahun'                => $item->tahun,
                    'status'               => $item->status,
                    'can_reset'            => $canManageAll,
                ];
            }, $data);

            return response()->json(['data' => $result]);
        }

        // Ambil daftar lembaga kategori pengajar (madrasah, sekolah, kampus, pesantren)
        $user = Auth::user();
        $jabatan = $user ? $user->jabatan : 'SuperAdmin';
        $canManageAll = in_array($jabatan, ['SuperAdmin', 'Evaluasi']);

        $lembagaQuery = Lembaga::query();
        if (!$canManageAll && $user && $user->lembaga) {
            $lembagaQuery->where('id_lembaga', $user->lembaga);
        } else {
            // Lembaga yang memiliki unit pengajar
            $lembagaQuery->whereExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('pengajar')
                  ->whereColumn('pengajar.id_lembaga', 'lembaga.id_lembaga');
            });
        }
        $lembagas = $lembagaQuery->orderBy('nama_lembaga', 'asc')->get();

        return view('kehadiran-pengajar.index', compact('lembagas'));
    }

    /**
     * Tambah Blanko Baru Kehadiran Pengajar
     */
    public function storeBlanko(Request $request)
    {
        $request->validate([
            'id_lembaga' => 'required',
            'bulan'      => 'required',
            'tahun'      => 'required',
        ]);

        $idLembaga = $request->input('id_lembaga');
        $bulan     = $request->input('bulan');
        $tahun     = $request->input('tahun');

        // Cek duplikasi blanko pengajar
        $exists = KehadiranLembaga::where('id_lembaga', $idLembaga)
            ->where('kategori', 'Pengajar')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->exists();

        if ($exists) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Blanko Kehadiran Pengajar untuk lembaga dan periode ini sudah ada!'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $kehadiran = KehadiranLembaga::create([
                'id_lembaga' => $idLembaga,
                'kategori'   => 'Pengajar',
                'bulan'      => $bulan,
                'tahun'      => $tahun,
                'status'     => 'Belum',
                'file'       => '',
            ]);

            // Catat log audit trail (catatan_log dikosongkan sesuai ketentuan user)
            LogRiwayatBarokah::create([
                'id_kehadiran_lembaga' => $kehadiran->id_kehadiran_lembaga,
                'status_aksi'          => 'Buat Blanko',
                'waktu_eksekusi'       => now(),
                'id_pengguna'          => Auth::id() ?? 1,
                'catatan_log'          => '',
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Blanko Kehadiran Pengajar berhasil ditambahkan!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal membuat blanko: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Halaman Input Rekap Kehadiran Pengajar
     */
    public function rekap($id)
    {
        $periode = KehadiranLembaga::with('lembaga')->where('kategori', 'Pengajar')->findOrFail($id);

        $user = Auth::user();
        if ($user && $user->jabatan === 'AdminLembaga' && $periode->id_lembaga != $user->lembaga) {
            abort(403, 'Anda tidak memiliki hak akses untuk lembaga ini.');
        }

        $pengajar = DB::table('pengajar')
            ->join('umana', 'pengajar.nik', '=', 'umana.nik')
            ->join('lembaga', 'pengajar.id_lembaga', '=', 'lembaga.id_lembaga')
            ->leftJoin('transport', 'pengajar.kategori_trans', '=', 'transport.id_transport')
            ->leftJoin('master_prodi', 'pengajar.id_prodi', '=', 'master_prodi.id_prodi')
            ->where('lembaga.id_lembaga', $periode->id_lembaga)
            ->where('pengajar.tgl_mulai', '<=', now()->toDateString())
            ->where(function ($q) {
                $q->whereNull('pengajar.tgl_selesai')
                  ->orWhere('pengajar.tgl_selesai', '0000-00-00')
                  ->orWhere('pengajar.tgl_selesai', '>=', now()->toDateString());
            })
            ->whereIn('pengajar.status', ['Aktif', 'Cuti 50%', 'Cuti 100%'])
            ->select(
                'pengajar.id_pengajar',
                'pengajar.nik',
                'pengajar.jumlah_sks',
                'pengajar.status',
                'umana.nama_lengkap',
                'umana.gelar_depan',
                'umana.gelar_belakang',
                'master_prodi.nama_prodi',
                'transport.nominal_transport'
            )
            ->orderByRaw("IFNULL(master_prodi.nama_prodi, '~') ASC, umana.nama_lengkap ASC")
            ->get();

        $existingKehadiran = KehadiranPengajar::where('id_kehadiran_lembaga', $periode->id_kehadiran_lembaga)
            ->get()
            ->keyBy('id_pengajar');

        return view('kehadiran-pengajar.rekap', compact('periode', 'pengajar', 'existingKehadiran'));
    }

    /**
     * Simpan Rekap Kehadiran Pengajar
     */
    public function saveRekap(Request $request)
    {
        $request->validate([
            'id_kehadiran_lembaga' => 'required',
            'bulan'                => 'required',
            'tahun'                => 'required',
        ]);

        $periode = KehadiranLembaga::where('kategori', 'Pengajar')->findOrFail($request->id_kehadiran_lembaga);

        $user = Auth::user();
        if ($user && $user->jabatan === 'AdminLembaga' && $periode->id_lembaga != $user->lembaga) {
            return response()->json(['status' => false, 'message' => 'Anda tidak memiliki hak akses.'], 403);
        }

        // Handle File Upload
        $fileName = $periode->file;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $customName = \Illuminate\Support\Str::random(50) . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload'), $customName);
            $fileName = $customName;
        }

        if (empty($fileName)) {
            return response()->json(['status' => false, 'message' => 'File bukti absensi (PDF) wajib diunggah!'], 422);
        }

        DB::beginTransaction();
        try {
            $idPengajars   = $request->input('id_penempatan', []);
            $jumlahHadir   = $request->input('jumlah_kehadiran', []);
            $jumlahHadir15 = $request->input('jumlah_kehadiran_15', []);
            $jumlahHadir10 = $request->input('jumlah_kehadiran_10', []);
            $jumlahPiket   = $request->input('jumlah_kehadiran_piket', []);

            $dataKehadiran = [];
            foreach ($idPengajars as $i => $idp) {
                $hNormal = isset($jumlahHadir[$i]) ? max(0, (int)$jumlahHadir[$i]) : 0;
                $h15     = isset($jumlahHadir15[$i]) ? max(0, (int)$jumlahHadir15[$i]) : 0;
                $h10     = isset($jumlahHadir10[$i]) ? max(0, (int)$jumlahHadir10[$i]) : 0;
                $piket   = isset($jumlahPiket[$i]) ? max(0, (int)$jumlahPiket[$i]) : 0;

                $dataKehadiran[] = [
                    'id_kehadiran_lembaga' => $periode->id_kehadiran_lembaga,
                    'id_pengajar'          => $idp,
                    'bulan'                => $request->bulan,
                    'tahun'                => $request->tahun,
                    'jumlah_hadir'         => $hNormal,
                    'jumlah_hadir_15'      => $h15,
                    'jumlah_hadir_10'      => $h10,
                    'jumlah_hadir_piket'   => $piket,
                    'tgl_input'            => now(),
                ];
            }

            KehadiranPengajar::where('id_kehadiran_lembaga', $periode->id_kehadiran_lembaga)->delete();

            if (!empty($dataKehadiran)) {
                KehadiranPengajar::insert($dataKehadiran);
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
                'message' => 'Rekap kehadiran Guru/Dosen berhasil disimpan!'
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
     * Hitung Data Kalkulasi Lengkap Periode Pengajar
     */
    protected function calculatePeriodeData($id)
    {
        $periode = KehadiranLembaga::with('lembaga')->where('kategori', 'Pengajar')->findOrFail($id);
        $periodeDate = HitungPengajarService::getPeriodeDate($periode->bulan, $periode->tahun);
        $meta = HitungPengajarService::buildMetaPengajar($periodeDate);
        $komponenSebelumnya = HitungPengajarService::getKomponenSebelumnyaPengajar($periode->id_lembaga, $id);

        // 1. Cek Snapshot First di total_barokah_pengajar
        $snapshot = DB::table('total_barokah_pengajar as tbp')
            ->join('kehadiran_lembaga as kl', 'kl.id_kehadiran_lembaga', '=', 'tbp.id_kehadiran_lembaga')
            ->join('pengajar as p', 'p.id_pengajar', '=', 'tbp.id_pengajar')
            ->join('umana as u', 'u.nik', '=', 'p.nik')
            ->join('lembaga as l', 'l.id_lembaga', '=', 'kl.id_lembaga')
            ->leftJoin('master_prodi as mp', 'mp.id_prodi', '=', 'p.id_prodi')
            ->where('kl.id_kehadiran_lembaga', $id)
            ->select(
                'tbp.*',
                'u.nik',
                'u.gelar_depan', 'u.nama_lengkap', 'u.gelar_belakang',
                'p.kategori', 'u.status_aktif', 'u.ijazah_terakhir', 'u.status_sertifikasi',
                'u.tmt_guru', 'u.tmt_dosen', 'u.tmt_maif',
                'kl.bulan', 'kl.tahun', 'l.nama_lembaga', 'kl.status as status_periode', 'kl.file',
                'kl.id_kehadiran_lembaga', 'kl.id_lembaga',
                'tbp.id_total_barokah_pengajar as id_kehadiran_pengajar',
                'kl.status', 'mp.nama_prodi'
            )
            ->orderByRaw("CASE WHEN l.id_bidang = 'Bidang DIKTI' THEN 0 ELSE 1 END ASC, IFNULL(mp.nama_prodi, '~') ASC, u.nama_lengkap ASC, u.nik ASC")
            ->get();

        $rows = [];
        $totals = [
            'total_mengajar'   => 0,
            'total_dty'        => 0,
            'total_jafung'     => 0,
            'total_kehadiran'  => 0,
            'total_hadir_15'   => 0,
            'total_hadir_10'   => 0,
            'total_piket'      => 0,
            'total_tunkel'     => 0,
            'total_tunjanak'   => 0,
            'total_kehormatan' => 0,
            'total_walkes'     => 0,
            'total_tambahan'   => 0,
            'total_jumlah'     => 0,
            'total_potongan'   => 0,
            'grand_total'      => 0,
        ];

        if ($snapshot->isNotEmpty()) {
            foreach ($snapshot as $s) {
                $calc = [
                    'id_pengajar'        => $s->id_pengajar,
                    'id_kehadiran_pengajar' => $s->id_kehadiran_pengajar,
                    'jumlah_sks'         => (int)$s->jumlah_sks,
                    'mp'                 => (int)$s->mp,
                    'rank'               => (int)$s->rank,
                    'mengajar'           => (int)$s->mengajar,
                    'dty'                => (int)$s->dty,
                    'jafung'             => (int)$s->jafung,
                    'jumlah_hadir'       => (int)$s->jumlah_hadir,
                    'nominal_kehadiran'  => (int)$s->nominal_kehadiran,
                    'jumlah_hadir_15'    => (int)$s->jumlah_hadir_15,
                    'nominal_hadir_15'   => (int)$s->nominal_hadir_15,
                    'jumlah_hadir_10'    => (int)$s->jumlah_hadir_10,
                    'nominal_hadir_10'   => (int)$s->nominal_hadir_10,
                    'jumlah_hadir_piket' => (int)$s->jumlah_hadir_piket,
                    'rank_piket'         => (float)$s->rank_piket,
                    'barokah_piket'      => (int)$s->barokah_piket,
                    'tunkel'             => (int)$s->tunkel,
                    'tun_anak'           => (int)$s->tun_anak,
                    'kehormatan'         => (int)$s->kehormatan,
                    'walkes'             => (int)$s->walkes,
                    'khusus'             => (int)$s->khusus,
                    'potongan'           => (int)$s->potongan,
                    'diterima'           => (int)$s->diterima,
                    'jumlah'             => (int)$s->diterima + (int)$s->potongan,
                    'nama_lengkap'       => $s->nama_lengkap,
                    'nik'                => $s->nik,
                    'gelar_depan'        => $s->gelar_depan,
                    'gelar_belakang'     => $s->gelar_belakang,
                    'nama_prodi'         => $s->nama_prodi,
                    'ijazah_terakhir'    => $s->ijazah_terakhir,
                    'kategori'           => $s->kategori,
                    'status_sertifikasi' => $s->status_sertifikasi,
                    'status_aktif'       => $s->status_aktif,
                    'tmt_guru'           => $s->tmt_guru,
                    'tmt_dosen'          => $s->tmt_dosen,
                    'tmt_maif'           => $s->tmt_maif,
                    'catatan_khusus_umana' => $s->catatan_khusus_umana ?? '',
                ];

                $calcObj = (object)$calc;
                $rows[] = $calcObj;

                $totals['total_mengajar']   += $calc['mengajar'];
                $totals['total_dty']        += $calc['dty'];
                $totals['total_jafung']     += $calc['jafung'];
                $totals['total_kehadiran']  += $calc['nominal_kehadiran'];
                $totals['total_hadir_15']   += $calc['nominal_hadir_15'];
                $totals['total_hadir_10']   += $calc['nominal_hadir_10'];
                $totals['total_piket']      += $calc['barokah_piket'];
                $totals['total_tunkel']     += $calc['tunkel'];
                $totals['total_tunjanak']   += $calc['tun_anak'];
                $totals['total_kehormatan'] += $calc['kehormatan'];
                $totals['total_walkes']     += $calc['walkes'];
                $totals['total_tambahan']   += $calc['khusus'];
                $totals['total_jumlah']     += $calc['jumlah'];
                $totals['total_potongan']   += $calc['potongan'];
                $totals['grand_total']      += $calc['diterima'];
            }
        } else {
            // 2. Fetch Live Calculation
            $listLive = DB::table('kehadiran_pengajar as kp')
                ->join('pengajar as p', 'p.id_pengajar', '=', 'kp.id_pengajar')
                ->join('umana as u', 'p.nik', '=', 'u.nik')
                ->join('kehadiran_lembaga as kl', 'kl.id_kehadiran_lembaga', '=', 'kp.id_kehadiran_lembaga')
                ->join('lembaga as l', 'p.id_lembaga', '=', 'l.id_lembaga')
                ->leftJoin('transport as t', 'p.kategori_trans', '=', 't.id_transport')
                ->leftJoin('master_prodi as mp', 'mp.id_prodi', '=', 'p.id_prodi')
                ->where('kl.id_kehadiran_lembaga', $id)
                ->where(function ($q) {
                    $q->whereNull('p.tgl_selesai')
                      ->orWhere('p.tgl_selesai', '0000-00-00')
                      ->orWhere('p.tgl_selesai', '>=', now()->toDateString());
                })
                ->select(
                    'kp.id_kehadiran_pengajar', 'kp.jumlah_hadir', 'kp.jumlah_hadir_15', 'kp.jumlah_hadir_10', 'kp.jumlah_hadir_piket', 'kp.catatan_khusus_umana',
                    'p.id_pengajar', 'p.kategori', 'p.jumlah_sks', 'p.id_lembaga', 'p.tunj_kel', 'p.tunj_anak', 'p.walkes', 'p.kehormatan', 'p.jafung',
                    'u.nik', 'u.nama_lengkap', 'u.gelar_depan', 'u.gelar_belakang', 'u.ijazah_terakhir', 'u.status_aktif',
                    'u.status_sertifikasi', 'u.jabatan_akademik', 'u.tmt_guru', 'u.tmt_dosen', 'u.tmt_maif',
                    'l.nama_lembaga', 'l.id_bidang', 't.nominal_transport', 'mp.nama_prodi',
                    'kl.bulan', 'kl.tahun', 'kl.status', 'kl.file'
                )
                ->orderByRaw("CASE WHEN l.id_bidang = 'Bidang DIKTI' THEN 0 ELSE 1 END ASC, IFNULL(mp.nama_prodi, '~') ASC, u.nama_lengkap ASC, u.nik ASC")
                ->get();

            foreach ($listLive as $item) {
                $calc = HitungPengajarService::hitungRowPengajar($item, $meta);

                // Tambahkan field pelengkap tampilan
                $calc['id_kehadiran_pengajar'] = $item->id_kehadiran_pengajar;
                $calc['nik']             = $item->nik;
                $calc['nama_lengkap']    = $item->nama_lengkap;
                $calc['gelar_depan']     = $item->gelar_depan;
                $calc['gelar_belakang']  = $item->gelar_belakang;
                $calc['nama_prodi']      = $item->nama_prodi;
                $calc['ijazah_terakhir'] = $item->ijazah_terakhir;
                $calc['kategori']        = $item->kategori;
                $calc['status_sertifikasi'] = $item->status_sertifikasi ?? '';
                $calc['status_aktif']    = $item->status_aktif;
                $calc['tmt_guru']        = $item->tmt_guru;
                $calc['tmt_dosen']       = $item->tmt_dosen;
                $calc['tmt_maif']        = $item->tmt_maif;
                $calc['catatan_khusus_umana'] = $item->catatan_khusus_umana ?? '';

                // Cek Warning Perubahan Komponen dari Bulan Sebelumnya
                $changed = [];
                $selisih = 0;
                $isWarning = false;
                $totalTetapNow = $calc['mengajar'] + $calc['dty'] + $calc['jafung'] + $calc['tunkel'] + $calc['tun_anak'] + $calc['kehormatan'] + $calc['walkes'];

                if (isset($komponenSebelumnya[$item->id_pengajar])) {
                    $prev = $komponenSebelumnya[$item->id_pengajar];
                    if ($calc['mengajar'] !== $prev['mengajar'])     $changed[] = 'mengajar';
                    if ($calc['dty'] !== $prev['dty'])               $changed[] = 'dty';
                    if ($calc['jafung'] !== $prev['jafung'])         $changed[] = 'jafung';
                    if ($calc['tunkel'] !== $prev['tunkel'])         $changed[] = 'tunkel';
                    if ($calc['tun_anak'] !== $prev['tun_anak'])     $changed[] = 'tun_anak';
                    if ($calc['kehormatan'] !== $prev['kehormatan']) $changed[] = 'kehormatan';
                    if ($calc['walkes'] !== $prev['walkes'])         $changed[] = 'walkes';

                    if ($totalTetapNow !== $prev['total_tetap_prev']) {
                        $isWarning = true;
                        $selisih = $totalTetapNow - $prev['total_tetap_prev'];
                    }
                }

                $calc['komponen_berubah'] = $changed;
                $calc['is_warning']       = $isWarning;
                $calc['selisih_komponen'] = $selisih;

                $calcObj = (object)$calc;
                $rows[] = $calcObj;

                $totals['total_mengajar']   += $calc['mengajar'];
                $totals['total_dty']        += $calc['dty'];
                $totals['total_jafung']     += $calc['jafung'];
                $totals['total_kehadiran']  += $calc['nominal_kehadiran'];
                $totals['total_hadir_15']   += $calc['nominal_hadir_15'];
                $totals['total_hadir_10']   += $calc['nominal_hadir_10'];
                $totals['total_piket']      += $calc['barokah_piket'];
                $totals['total_tunkel']     += $calc['tunkel'];
                $totals['total_tunjanak']   += $calc['tun_anak'];
                $totals['total_kehormatan'] += $calc['kehormatan'];
                $totals['total_walkes']     += $calc['walkes'];
                $totals['total_tambahan']   += $calc['khusus'];
                $totals['total_jumlah']     += $calc['jumlah'];
                $totals['total_potongan']   += $calc['potongan'];
                $totals['grand_total']      += $calc['diterima'];
            }
        }

        return [
            'periode'             => $periode,
            'rows'                => $rows,
            'totals'              => $totals,
            'periode_date'        => $periodeDate,
            'komponen_sebelumnya' => $komponenSebelumnya,
            'is_snapshot'         => $snapshot->isNotEmpty(),
        ];
    }

    /**
     * Halaman Cek Barokah / Koreksi Pengajar
     */
    public function koreksi($id)
    {
        $res = $this->calculatePeriodeData($id);
        $periode = $res['periode'];

        $user = Auth::user();
        if ($user && $user->jabatan === 'AdminLembaga' && $periode->id_lembaga != $user->lembaga) {
            abort(403, 'Anda tidak memiliki hak akses untuk lembaga ini.');
        }

        return view('kehadiran-pengajar.koreksi', [
            'periode'             => $periode,
            'isilist'             => $res['rows'],
            'totals'              => $res['totals'],
            'periode_date'        => $res['periode_date'],
            'is_snapshot'         => $res['is_snapshot'],
            'komponen_sebelumnya' => $res['komponen_sebelumnya'],
        ]);
    }

    /**
     * Update baris kehadiran pengajar individual via AJAX modal koreksi
     */
    public function updateRow(Request $request)
    {
        $idKehadiranPengajar = $request->input('id_kehadiran_pengajar');
        $hadir               = max(0, (int)$request->input('jumlah_hadir', 0));
        $hadir15             = max(0, (int)$request->input('jumlah_hadir_15', 0));
        $hadir10             = max(0, (int)$request->input('jumlah_hadir_10', 0));
        $piket               = max(0, (int)$request->input('jumlah_hadir_piket', 0));

        if (!$idKehadiranPengajar) {
            return response()->json(['status' => false, 'message' => 'ID tidak ditemukan.'], 400);
        }

        $rowKP = KehadiranPengajar::findOrFail($idKehadiranPengajar);
        $rowKP->update([
            'jumlah_hadir'       => $hadir,
            'jumlah_hadir_15'    => $hadir15,
            'jumlah_hadir_10'    => $hadir10,
            'jumlah_hadir_piket' => $piket,
        ]);

        $res = $this->calculatePeriodeData($rowKP->id_kehadiran_lembaga);
        $updated = null;
        foreach ($res['rows'] as $r) {
            if ((int)$r->id_kehadiran_pengajar === (int)$idKehadiranPengajar) {
                $updated = $r;
                break;
            }
        }

        return response()->json([
            'status'  => true,
            'message' => 'Data kehadiran berhasil diperbarui.',
            'data'    => [
                'jml_kehadiran'    => 'Rp ' . number_format($updated->nominal_kehadiran, 0, ',', '.'),
                'nominal_hadir_15' => 'Rp ' . number_format($updated->nominal_hadir_15, 0, ',', '.'),
                'nominal_hadir_10' => 'Rp ' . number_format($updated->nominal_hadir_10, 0, ',', '.'),
                'rank_piket'       => 'Rp ' . number_format($updated->rank_piket, 0, ',', '.'),
                'barokah_piket'    => 'Rp ' . number_format($updated->barokah_piket, 0, ',', '.'),
                'jumlah'           => 'Rp ' . number_format($updated->jumlah, 0, ',', '.'),
                'diterima'         => 'Rp ' . number_format($updated->diterima, 0, ',', '.'),
            ],
            'totals'  => $res['totals'],
        ]);
    }

    /**
     * Kirim Pengajuan Kehadiran Pengajar (Sudah / Revisi -> Terkirim)
     */
    public function kirim(Request $request)
    {
        $id = $request->input('id_kehadiran_lembaga');
        if (!$id) {
            return response()->json(['status' => false, 'message' => 'ID periode tidak ditemukan.'], 400);
        }

        $periode = KehadiranLembaga::where('kategori', 'Pengajar')->findOrFail($id);
        $periode->update(['status' => 'Terkirim']);

        LogRiwayatBarokah::create([
            'id_kehadiran_lembaga' => $id,
            'status_aksi'          => 'Kirim',
            'waktu_eksekusi'       => now(),
            'id_pengguna'          => Auth::id() ?? 1,
            'catatan_log'          => '',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Validasi barokah pengajar berhasil dikirim ke evaluasi!',
        ]);
    }

    /**
     * Validasi / Setujui (ACC) atau Kembalikan (Revisi)
     */
    public function validasi(Request $request)
    {
        $id         = $request->input('id_kehadiran_lembaga');
        $actionType = $request->input('action_type', 'setuju'); // 'setuju' | 'revisi'
        $catatan    = $request->input('catatan_umum_pimpinan', '');
        $catatanKhusus = $request->input('catatan_khusus', []); // array [id_pengajar => text]

        if (!$id) {
            return response()->json(['status' => false, 'message' => 'ID periode tidak ditemukan.'], 400);
        }

        $user = Auth::user();
        $jabatan = $user ? $user->jabatan : 'SuperAdmin';
        if (!in_array($jabatan, ['SuperAdmin', 'Evaluasi'])) {
            return response()->json(['status' => false, 'message' => 'Anda tidak memiliki hak akses validasi.'], 403);
        }

        $res = $this->calculatePeriodeData($id);
        $periode = $res['periode'];
        $rows = $res['rows'];
        $totals = $res['totals'];

        DB::beginTransaction();
        try {
            $statusNew = ($actionType === 'revisi') ? 'Revisi' : 'acc';

            // Bersihkan snapshot lama di total_barokah_pengajar
            DB::table('total_barokah_pengajar')->where('id_kehadiran_lembaga', $id)->delete();

            // Simpan catatan khusus ke tabel kehadiran_pengajar
            if (!empty($catatanKhusus)) {
                foreach ($catatanKhusus as $idPengajar => $cNote) {
                    DB::table('kehadiran_pengajar')
                        ->where('id_kehadiran_lembaga', $id)
                        ->where('id_pengajar', $idPengajar)
                        ->update(['catatan_khusus_umana' => $cNote]);
                }
            }

            // Jika disetujui (ACC), buat snapshot
            if ($actionType === 'setuju') {
                $snapshotBatch = [];
                foreach ($rows as $r) {
                    $cNote = $catatanKhusus[$r->id_pengajar] ?? ($r->catatan_khusus_umana ?? '');
                    $snapshotBatch[] = [
                        'id_pengajar'          => $r->id_pengajar,
                        'id_kehadiran_lembaga' => $id,
                        'bulan'                => $periode->bulan,
                        'tahun'                => $periode->tahun,
                        'jumlah_sks'           => (int)$r->jumlah_sks,
                        'rank'                 => (int)$r->rank,
                        'mengajar'             => (int)$r->mengajar,
                        'mp'                   => (int)$r->mp,
                        'dty'                  => (int)$r->dty,
                        'jafung'               => (int)$r->jafung,
                        'jumlah_hadir'         => (int)$r->jumlah_hadir,
                        'nominal_kehadiran'    => (int)$r->nominal_kehadiran,
                        'jumlah_hadir_15'      => (int)$r->jumlah_hadir_15,
                        'nominal_hadir_15'     => (int)$r->nominal_hadir_15,
                        'jumlah_hadir_10'      => (int)$r->jumlah_hadir_10,
                        'nominal_hadir_10'     => (int)$r->nominal_hadir_10,
                        'jumlah_hadir_piket'   => (int)$r->jumlah_hadir_piket,
                        'rank_piket'           => (int)$r->rank_piket,
                        'barokah_piket'        => (int)$r->barokah_piket,
                        'tunkel'               => (int)$r->tunkel,
                        'tun_anak'             => (int)($r->tun_anak ?? $r->tunja_anak ?? 0),
                        'kehormatan'           => (int)$r->kehormatan,
                        'walkes'               => (int)($r->walkes ?? $r->tunj_walkes ?? 0),
                        'khusus'               => (int)($r->khusus ?? $r->tambahan ?? 0),
                        'potongan'             => (int)$r->potongan,
                        'diterima'             => (int)$r->diterima,
                        'catatan_khusus_umana' => $cNote,
                        'status'               => 'acc',
                    ];
                }

                if (!empty($snapshotBatch)) {
                    DB::table('total_barokah_pengajar')->insert($snapshotBatch);
                }
            }

            // Update Periode
            $periode->update([
                'status'                => $statusNew,
                'jumlah_total'          => (int)$totals['grand_total'],
                'catatan_umum_pimpinan' => $catatan,
            ]);

            // Catat log
            LogRiwayatBarokah::create([
                'id_kehadiran_lembaga' => $id,
                'status_aksi'          => $statusNew,
                'waktu_eksekusi'       => now(),
                'id_pengguna'          => Auth::id() ?? 1,
                'catatan_log'          => trim((string)$catatan),
            ]);

            DB::commit();

            $msg = ($actionType === 'revisi')
                ? 'Pengajuan kehadiran berhasil dikembalikan untuk revisi.'
                : 'Validasi barokah Guru/Dosen berhasil disetujui (ACC) dan tersimpan ke snapshot!';

            return response()->json(['status' => true, 'message' => $msg]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Gagal validasi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Simpan catatan khusus pengajar individual via AJAX modal
     */
    public function saveCatatan(Request $request)
    {
        $idKehadiranLembaga = $request->input('id_kehadiran_lembaga');
        $idPengajar         = $request->input('id_pengajar');
        $catatan            = $request->input('catatan', '');

        if (!$idKehadiranLembaga || !$idPengajar) {
            return response()->json(['status' => false, 'message' => 'Parameter tidak lengkap.'], 400);
        }

        // Update di kehadiran_pengajar
        DB::table('kehadiran_pengajar')
            ->where('id_kehadiran_lembaga', $idKehadiranLembaga)
            ->where('id_pengajar', $idPengajar)
            ->update(['catatan_khusus_umana' => $catatan]);

        // Update di total_barokah_pengajar jika snapshot sudah ada
        DB::table('total_barokah_pengajar')
            ->where('id_kehadiran_lembaga', $idKehadiranLembaga)
            ->where('id_pengajar', $idPengajar)
            ->update(['catatan_khusus_umana' => $catatan]);

        return response()->json([
            'status'  => true,
            'message' => 'Catatan berhasil disimpan.',
        ]);
    }

    /**
     * Sinkronkan Pengajar Baru ke Periode Berjalan
     */
    public function syncPengajar(Request $request)
    {
        $id = $request->input('id_kehadiran_lembaga');
        if (!$id) {
            return response()->json(['status' => false, 'message' => 'ID periode tidak ditemukan.'], 400);
        }

        $periode = KehadiranLembaga::where('kategori', 'Pengajar')->findOrFail($id);

        $activeTeachers = Pengajar::where('id_lembaga', $periode->id_lembaga)
            ->whereIn('status', ['Aktif', 'Cuti 50%', 'Cuti 100%'])
            ->where('tgl_mulai', '<=', now()->toDateString())
            ->where(function ($q) {
                $q->whereNull('tgl_selesai')
                  ->orWhere('tgl_selesai', '0000-00-00')
                  ->orWhere('tgl_selesai', '>=', now()->toDateString());
            })
            ->pluck('id_pengajar')
            ->toArray();

        $existingIds = KehadiranPengajar::where('id_kehadiran_lembaga', $id)
            ->pluck('id_pengajar')
            ->toArray();

        $missing = array_diff($activeTeachers, $existingIds);
        if (empty($missing)) {
            return response()->json(['status' => true, 'message' => 'Daftar pengajar sudah sinkron. Tidak ada data baru.']);
        }

        $inserts = [];
        foreach ($missing as $mId) {
            $inserts[] = [
                'id_kehadiran_lembaga' => $id,
                'id_pengajar'          => $mId,
                'bulan'                => $periode->bulan,
                'tahun'                => $periode->tahun,
                'jumlah_hadir'         => 0,
                'jumlah_hadir_15'      => 0,
                'jumlah_hadir_10'      => 0,
                'jumlah_hadir_piket'   => 0,
                'tgl_input'            => now(),
            ];
        }

        KehadiranPengajar::insert($inserts);

        return response()->json([
            'status'  => true,
            'message' => count($inserts) . ' pengajar baru berhasil disinkronkan ke periode ini.'
        ]);
    }

    /**
     * Detail Potongan Pengajar
     */
    public function detailPotongan($id_pengajar, $periode_date = null)
    {
        $query = DB::table('potongan_pengajar as pp')
            ->leftJoin('potongan as p', 'p.id_potongan', '=', 'pp.jenis_potongan')
            ->select('pp.id_potongan_pengajar', 'pp.jenis_potongan', 'p.nama_potongan', 'pp.nominal_potongan', 'pp.min_periode_potongan', 'pp.max_periode_potongan')
            ->where('pp.id_pengajar', $id_pengajar);

        if ($periode_date) {
            $query->where(function ($q) use ($periode_date) {
                $q->whereNull('pp.min_periode_potongan')
                  ->orWhere('pp.min_periode_potongan', '<=', DB::raw("LAST_DAY('$periode_date')"));
            })->where(function ($q) use ($periode_date) {
                $q->whereNull('pp.max_periode_potongan')
                  ->orWhere('pp.max_periode_potongan', '>=', $periode_date);
            });
        }

        return response()->json($query->get());
    }

    /**
     * Simpan / Tambah Potongan Pengajar
     */
    public function savePotongan(Request $request)
    {
        $id_pengajar = $request->input('id_pengajar');
        $potonganData = $request->input('potongan', []);

        if ($id_pengajar && is_array($potonganData)) {
            foreach ($potonganData as $item) {
                $jenis      = $item['jenis_potongan'] ?? '';
                $nominal    = isset($item['nominal_potongan']) ? (int)str_replace(['.', ','], '', $item['nominal_potongan']) : 0;
                $minPeriode = !empty($item['min_periode_potongan']) ? $item['min_periode_potongan'] : null;
                $maxPeriode = !empty($item['max_periode_potongan']) ? $item['max_periode_potongan'] : null;
                $idPp       = $item['id_potongan_pengajar'] ?? null;

                if (empty($jenis)) continue;

                $payload = [
                    'id_pengajar'          => $id_pengajar,
                    'jenis_potongan'       => $jenis,
                    'nominal_potongan'     => $nominal,
                    'min_periode_potongan' => $minPeriode,
                    'max_periode_potongan' => $maxPeriode,
                ];

                if (!empty($idPp)) {
                    DB::table('potongan_pengajar')->where('id_potongan_pengajar', $idPp)->update($payload);
                } else {
                    DB::table('potongan_pengajar')->insert($payload);
                }
            }
        }

        return response()->json(['status' => true, 'message' => 'Data Potongan pengajar berhasil disimpan.']);
    }

    /**
     * Riwayat log audit trail
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
     * Reset data periode pengajar (Reset Total / Reset Status)
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

        $periode = KehadiranLembaga::where('kategori', 'Pengajar')->findOrFail($id);

        DB::beginTransaction();
        try {
            if ($mode === 'kehadiran') {
                // Hapus data kehadiran_pengajar dan total_barokah_pengajar
                KehadiranPengajar::where('id_kehadiran_lembaga', $id)->delete();
                DB::table('total_barokah_pengajar')->where('id_kehadiran_lembaga', $id)->delete();
                $periode->update([
                    'status'       => 'Belum',
                    'jumlah_total' => 0,
                ]);
                $pesan = 'Data kehadiran pengajar berhasil dihapus. Status periode dikembalikan ke Belum diisi.';
            } else {
                // Hapus total_barokah_pengajar saja, data input absensi tetap aman
                DB::table('total_barokah_pengajar')->where('id_kehadiran_lembaga', $id)->delete();
                $periode->update([
                    'status'       => 'Sudah',
                    'jumlah_total' => 0,
                ]);
                $pesan = 'Status periode dikembalikan ke Sudah (Belum dikirim). Data input kehadiran tetap aman.';
            }

            LogRiwayatBarokah::create([
                'id_kehadiran_lembaga' => $id,
                'status_aksi'          => 'Reset',
                'waktu_eksekusi'       => now(),
                'id_pengguna'          => Auth::id() ?? 1,
                'catatan_log'          => '',
            ]);

            DB::commit();

            return response()->json(['status' => true, 'message' => $pesan]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Gagal melakukan reset: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Cetak PDF Rekap Pengajar (Landscape Legal)
     */
    public function cetak($id)
    {
        $res = $this->calculatePeriodeData($id);
        if (empty($res['periode']) || empty($res['rows'])) {
            abort(404, 'Data periode pengajar tidak ditemukan atau belum lengkap');
        }

        $pdfContent = PdfBarokahService::cetakRekapPengajar($res);

        $cleanLembaga = preg_replace('/[^A-Za-z0-9\-]/', '_', $res['periode']->nama_lembaga ?? 'Pengajar');
        $fileName = 'Rekap_Barokah_Pengajar_' . $cleanLembaga . '_' . ($res['periode']->bulan ?? '') . '_' . ($res['periode']->tahun ?? '') . '.pdf';

        return response($pdfContent, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Cetak Potongan Pengajar (Portrait Legal)
     */
    public function cetakPotongan($id_lembaga, $id_kehadiran = null)
    {
        $lembaga = Lembaga::findOrFail($id_lembaga);
        $bulanLaporan = null;
        $tahunLaporan = null;
        $filterTanggal = "DATE_FORMAT(NOW(), '%Y-%m-01')";

        if (!empty($id_kehadiran)) {
            $periode = KehadiranLembaga::find($id_kehadiran);
            if ($periode) {
                $periodeDate = HitungPengajarService::getPeriodeDate($periode->bulan, $periode->tahun);
                $filterTanggal = "'{$periodeDate}'";
                $bulanLaporan = ucfirst($periode->bulan);
                $tahunLaporan = $periode->tahun;
            }
        }

        $query = "
            SELECT 
                u.nama_lengkap, u.gelar_depan, u.gelar_belakang,
                pot.nama_potongan, pp.nominal_potongan
            FROM umana u
            JOIN pengajar p ON u.nik = p.nik
            JOIN potongan_pengajar pp ON pp.id_pengajar = p.id_pengajar
            JOIN potongan pot ON pp.jenis_potongan = pot.id_potongan
            JOIN lembaga l ON p.id_lembaga = l.id_lembaga
            WHERE 
                (pp.min_periode_potongan IS NULL OR pp.min_periode_potongan <= {$filterTanggal})
                AND (pp.max_periode_potongan IS NULL OR pp.max_periode_potongan >= {$filterTanggal})
                AND p.id_lembaga = ?
                AND (p.tgl_selesai IS NULL OR p.tgl_selesai = '0000-00-00' OR p.tgl_selesai >= CURDATE())
            ORDER BY u.nama_lengkap ASC
        ";

        $potonganList = DB::select($query, [$id_lembaga]);

        $pdfContent = PdfBarokahService::cetakPotonganPengajar($potonganList, $lembaga, $bulanLaporan, $tahunLaporan);

        $cleanLembaga = preg_replace('/[^A-Za-z0-9\-]/', '_', $lembaga->nama_lembaga);
        $fileName = 'Potongan_Pengajar_' . $cleanLembaga . ($bulanLaporan ? '_' . $bulanLaporan . '_' . $tahunLaporan : '') . '.pdf';

        return response($pdfContent, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }
}
