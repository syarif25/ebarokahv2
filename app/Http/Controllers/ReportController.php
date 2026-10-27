<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;

class ReportController extends Controller
{
    /**
     * Get authenticated NIK for Umana
     */
    private function getAuthenticatedNik(Request $request)
    {
        if (Auth::guard('umana')->check()) {
            return Auth::guard('umana')->user()->nik;
        }
        if ($request->session()->has('nik')) {
            return $request->session()->get('nik');
        }
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user()->username;
        }
        return null;
    }

    private function ensureAuthenticated(Request $request)
    {
        $nik = $this->getAuthenticatedNik($request);
        if (!$nik) {
            if ($request->ajax() || $request->wantsJson()) {
                abort(response()->json(['ok' => false, 'message' => 'Unauthenticated'], 401));
            }
            return redirect('/')->send();
        }
        return $nik;
    }

    private function nama_bulan_id($n)
    {
        $m = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return $m[(int)$n] ?? $n;
    }

    private function tahun_ajaran($bulan, $tahun)
    {
        if ((int)$tahun >= 2026) {
            return (string)$tahun;
        }
        if ((int)$bulan >= 7) {
            return $tahun . '/' . ($tahun + 1);
        } else {
            return ($tahun - 1) . '/' . $tahun;
        }
    }

    private function rupiah($angka)
    {
        return number_format((float)$angka, 0, ',', '.');
    }

    /**
     * PWA Dashboard (Home)
     */
    public function index(Request $request)
    {
        $this->ensureAuthenticated($request);
        return view('pwa.dashboard');
    }

    /**
     * PWA Kehadiran (Category Cards)
     */
    public function kehadiran(Request $request)
    {
        $this->ensureAuthenticated($request);
        return view('pwa.barokah');
    }

    /**
     * PWA Barokah Detail (List Lembaga & Monthly Breakdown)
     */
    public function barokah(Request $request)
    {
        $this->ensureAuthenticated($request);
        $kategori = strtolower($request->get('k') ?: 'struktural');
        return view('pwa.detail_barokah', compact('kategori'));
    }

    /**
     * JSON: Header / Card Barokah on Dashboard
     */
    public function dashboard_barokah(Request $request)
    {
        $nik = $this->ensureAuthenticated($request);

        $start = (new DateTime('first day of -1 month 00:00:00'))->format('Y-m-d H:i:s');
        $end   = (new DateTime('first day of +1 month 00:00:00'))->format('Y-m-d H:i:s');

        $profile       = $this->get_profile_by_nik($nik);
        $lembaga_aktif = $this->count_pengabdian_aktif($nik);
        $buckets       = $this->sum_barokah_last_two_months($nik, $start, $end);
        $rows          = $this->list_barokah_last_two_months($nik, $start, $end);

        $now     = new DateTime('now');
        $keyNow  = $now->format('Y-m');
        $prev    = (clone $now)->modify('first day of last month');
        $keyPrev = $prev->format('Y-m');

        $totalNow  = isset($buckets[$keyNow])  ? (int)$buckets[$keyNow]['total'] : 0;
        $totalPrev = isset($buckets[$keyPrev]) ? (int)$buckets[$keyPrev]['total'] : 0;

        $namaBulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $fallbackNowDate  = (clone $now)->modify('-1 month');
        $fallbackPrevDate = (clone $prev)->modify('-1 month');

        $labelNowBulan  = $buckets[$keyNow]['label_bulan']  ?? $namaBulan[(int)$fallbackNowDate->format('n')]  ?? '';
        $labelNowTahun  = $buckets[$keyNow]['label_tahun']  ?? $fallbackNowDate->format('Y');
        $labelPrevBulan = $buckets[$keyPrev]['label_bulan'] ?? $namaBulan[(int)$fallbackPrevDate->format('n')] ?? '';
        $labelPrevTahun = $buckets[$keyPrev]['label_tahun'] ?? $fallbackPrevDate->format('Y');

        $rincianNow  = array_values(array_filter($rows, fn($r) => sprintf('%04d-%02d', $r['y'], $r['m']) === $keyNow));
        $rincianPrev = array_values(array_filter($rows, fn($r) => sprintf('%04d-%02d', $r['y'], $r['m']) === $keyPrev));

        return response()->json([
            'ok' => true,
            'nama_gelar'                 => $profile['nama_gelar'] ?? '-',
            'lembaga_aktif'              => (int)$lembaga_aktif,
            'label_bulan_ini'            => $labelNowBulan,
            'label_tahun_ini'            => $labelNowTahun,
            'label_bulan_lalu'           => $labelPrevBulan,
            'label_tahun_lalu'           => $labelPrevTahun,
            'total_bulan_ini'            => $totalNow,
            'total_bulan_ini_formatted'  => 'Rp. ' . $this->rupiah($totalNow),
            'total_bulan_lalu'           => $totalPrev,
            'total_bulan_lalu_formatted' => 'Rp. ' . $this->rupiah($totalPrev),
            'rincian_bulan_ini'          => $rincianNow,
            'rincian_bulan_lalu'         => $rincianPrev,
        ]);
    }

    /**
     * JSON: Active SK / Positions list on Dashboard
     */
    public function dashboard_rincian(Request $request)
    {
        $nik = $this->ensureAuthenticated($request);

        $struktural = $this->list_struktural_aktif($nik);
        $pengajar   = $this->list_pengajar_aktif($nik);

        return response()->json([
            'ok'         => true,
            'struktural' => $struktural,
            'pengajar'   => $pengajar,
        ]);
    }

    /**
     * JSON: Monthly series barokah by category (last 12 months)
     */
    public function barokah_data(Request $request)
    {
        $nik   = $this->ensureAuthenticated($request);
        $k     = strtolower($request->get('k') ?: 'struktural');
        $range = (int)($request->get('range') ?: 12);

        $start = (new DateTime('first day of -' . ($range - 1) . ' month 00:00:00'))->format('Y-m-d H:i:s');
        $end   = (new DateTime('first day of +1 month 00:00:00'))->format('Y-m-d H:i:s');

        $series = $this->sum_bulanan_by_kategori($nik, $k, $start, $end);
        return response()->json([
            'ok'       => true,
            'kategori' => $k,
            'series'   => $series,
        ]);
    }

    /**
     * JSON: List per-lembaga for selected month
     */
    public function barokah_detail(Request $request)
    {
        $nik = $this->ensureAuthenticated($request);
        $k   = strtolower($request->get('k') ?: 'struktural');
        $y   = (int)$request->get('y');
        $m   = (int)$request->get('m');

        $start = (new DateTime("$y-$m-01 00:00:00"))->format('Y-m-d H:i:s');
        $end   = (new DateTime("$y-$m-01 00:00:00"))->modify('first day of +1 month')->format('Y-m-d H:i:s');

        $rows = $this->detail_bulanan_by_kategori($nik, $k, $start, $end);
        return response()->json([
            'ok'   => true,
            'rows' => $rows,
        ]);
    }

    /**
     * JSON: Breakdown components modal
     */
    public function barokah_breakdown(Request $request)
    {
        $authNik = $this->ensureAuthenticated($request);
        $isPengelola = Auth::guard('web')->check() && in_array(Auth::guard('web')->user()->jabatan ?? '', ['SuperAdmin', 'Evaluasi']);

        $req_nik = $request->get('nik');
        $nik = (!empty($req_nik) && $isPengelola) ? $req_nik : $authNik;

        $k   = strtolower($request->get('k') ?: 'pengajar');
        $y   = (int)$request->get('y');
        $m   = (int)$request->get('m');
        $lid = $request->get('lid');

        $specM = null;
        $specY = null;
        if (strpos((string)$lid, '-') !== false) {
            $parts = explode('-', (string)$lid);
            if (count($parts) >= 3) {
                $lid   = $parts[0];
                $specM = (int)$parts[1];
                $specY = (int)$parts[2];
            }
        }

        $start = (new DateTime("$y-$m-01 00:00:00"))->format('Y-m-d H:i:s');
        $end   = (new DateTime("$y-$m-01 00:00:00"))->modify('first day of +1 month')->format('Y-m-d H:i:s');

        $fmt = fn($n) => $this->rupiah((int)$n);

        switch ($k) {
            case 'struktural':
                $data  = $this->breakdown_struktural_by_month($nik, $start, $end, $lid, $specM, $specY);
                $items = $this->list_potongan_struktural_by_month($nik, $start, $end, $lid, $specM, $specY);

                $payload = [
                    'barokah_pokok'     => (int)($data['barokah_pokok'] ?? 0),
                    'kehadiran_nominal' => (int)($data['kehadiran_nominal'] ?? 0),
                    'kehadiran'         => (int)($data['kehadiran'] ?? 0),
                    'tunkel'            => (int)($data['tunkel'] ?? 0),
                    'tun_anak'          => (int)($data['tun_anak'] ?? 0),
                    'tmp'               => (int)($data['tmp'] ?? 0),
                    'tbk'               => (int)($data['tbk'] ?? 0),
                    'kehormatan'        => (int)($data['kehormatan'] ?? 0),
                    'potongan'          => (int)($data['potongan'] ?? 0),
                    'total'             => (int)($data['total'] ?? 0),
                    'f' => [
                        'barokah_pokok'     => $fmt($data['barokah_pokok'] ?? 0),
                        'kehadiran_nominal' => $fmt($data['kehadiran_nominal'] ?? 0),
                        'kehadiran'         => (int)($data['kehadiran'] ?? 0),
                        'tunkel'            => $fmt($data['tunkel'] ?? 0),
                        'tun_anak'          => $fmt($data['tun_anak'] ?? 0),
                        'tmp'               => $fmt($data['tmp'] ?? 0),
                        'tbk'               => $fmt($data['tbk'] ?? 0),
                        'kehormatan'        => $fmt($data['kehormatan'] ?? 0),
                        'potongan'          => $fmt($data['potongan'] ?? 0),
                        'total'             => $fmt($data['total'] ?? 0),
                    ],
                    'potongan_items' => array_map(
                        fn($r) => ['nama' => $r['nama'], 'nominal' => (int)$r['nominal']],
                        $items
                    ),
                ];
                break;

            case 'satpam':
                $data = $this->breakdown_satpam_by_month($nik, $start, $end, $lid, $specM, $specY);

                $payload = [
                    'k1_total'   => (int)($data['k1_total'] ?? 0),
                    'k2_total'   => (int)($data['k2_total'] ?? 0),
                    'k3_total'   => (int)($data['k3_total'] ?? 0),
                    'j_hari'     => (int)($data['j_hari'] ?? 0),
                    'j_shift'    => (int)($data['j_shift'] ?? 0),
                    'j_dinihari' => (int)($data['j_dinihari'] ?? 0),
                    'k1_unit'    => (int)($data['k1_unit'] ?? 0),
                    'k2_unit'    => (int)($data['k2_unit'] ?? 0),
                    'k3_unit'    => (int)($data['k3_unit'] ?? 0),
                    'total'      => (int)($data['total'] ?? 0),
                    'f' => [
                        'k1_total' => $fmt($data['k1_total'] ?? 0),
                        'k2_total' => $fmt($data['k2_total'] ?? 0),
                        'k3_total' => $fmt($data['k3_total'] ?? 0),
                        'k1_unit'  => $fmt($data['k1_unit'] ?? 0),
                        'k2_unit'  => $fmt($data['k2_unit'] ?? 0),
                        'k3_unit'  => $fmt($data['k3_unit'] ?? 0),
                        'total'    => $fmt($data['total'] ?? 0),
                    ],
                    'potongan_items' => [],
                ];
                break;

            default: // pengajar
                $data  = $this->breakdown_pengajar_by_month($nik, $start, $end, $lid, $specM, $specY);
                $items = $this->list_potongan_pengajar_by_month($nik, $start, $end, $lid ?? null, $specM, $specY);

                $payload = [
                    'sks'        => (int)($data['sks'] ?? 0),
                    'per_sks'    => (int)($data['per_sks'] ?? 0),
                    'mengajar'   => (int)($data['mengajar'] ?? 0),
                    'gty_dty'    => (int)($data['gty_dty'] ?? 0),
                    'jafung'     => (int)($data['jafung'] ?? 0),
                    'kehadiran'  => (int)($data['kehadiran'] ?? 0),
                    'jhadir'     => (int)($data['jhadir'] ?? 0),
                    'jhadir15'   => (int)($data['jhadir15'] ?? 0),
                    'nhadir15'   => (int)($data['nhadir15'] ?? 0),
                    'jhadir10'   => (int)($data['jhadir10'] ?? 0),
                    'nhadir10'   => (int)($data['nhadir10'] ?? 0),
                    'walkes'     => (int)($data['walkes'] ?? 0),
                    'kehormatan' => (int)($data['kehormatan'] ?? 0),
                    'tambahan'   => (int)($data['tambahan'] ?? 0),
                    'piket'      => (int)($data['piket'] ?? 0),
                    'tun_anak'   => (int)($data['tun_anak'] ?? 0),
                    'tunkel'     => (int)($data['tunkel'] ?? 0),
                    'potongan'   => (int)($data['potongan'] ?? 0),
                    'total'      => (int)($data['total'] ?? 0),
                    'f' => [
                        'per_sks'    => $fmt($data['per_sks'] ?? 0),
                        'mengajar'   => $fmt($data['mengajar'] ?? 0),
                        'gty_dty'    => $fmt($data['gty_dty'] ?? 0),
                        'jafung'     => $fmt($data['jafung'] ?? 0),
                        'kehadiran'  => $fmt($data['kehadiran'] ?? 0),
                        'nhadir15'   => $fmt($data['nhadir15'] ?? 0),
                        'nhadir10'   => $fmt($data['nhadir10'] ?? 0),
                        'walkes'     => $fmt($data['walkes'] ?? 0),
                        'kehormatan' => $fmt($data['kehormatan'] ?? 0),
                        'tambahan'   => $fmt($data['tambahan'] ?? 0),
                        'piket'      => $fmt($data['piket'] ?? 0),
                        'tun_anak'   => $fmt($data['tun_anak'] ?? 0),
                        'tunkel'     => $fmt($data['tunkel'] ?? 0),
                        'potongan'   => $fmt($data['potongan'] ?? 0),
                        'total'      => $fmt($data['total'] ?? 0),
                    ],
                    'potongan_items' => array_map(function ($r) {
                        return ['nama' => $r['nama'], 'nominal' => (int)$r['nominal']];
                    }, $items),
                ];
        }

        return response()->json([
            'ok'   => true,
            'k'    => $k,
            'y'    => $y,
            'm'    => $m,
            'data' => $payload,
        ]);
    }

    // =========================================================================
    // DATABASE QUERY METHODS (Exact replication of old Report_model)
    // =========================================================================

    public function get_profile_by_nik($nik)
    {
        $row = DB::table('umana')
            ->selectRaw("TRIM(CONCAT(COALESCE(gelar_depan,''),' ',nama_lengkap,
                     CASE WHEN COALESCE(gelar_belakang,'')<>'' THEN CONCAT(', ',gelar_belakang) ELSE '' END)) AS nama_gelar")
            ->where('nik', $nik)
            ->first();

        return $row ? (array)$row : ['nama_gelar' => '-'];
    }

    public function count_pengabdian_aktif($nik)
    {
        $today = date('Y-m-d');

        $q1 = DB::table('penempatan')
            ->where('nik', $nik)
            ->where(function ($q) use ($today) {
                $q->whereNull('tgl_mulai')->orWhere('tgl_mulai', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('tgl_selesai')->orWhere('tgl_selesai', '>=', $today);
            })
            ->count();

        $q2 = DB::table('pengajar')
            ->where('nik', $nik)
            ->where(function ($q) use ($today) {
                $q->whereNull('tgl_mulai')->orWhere('tgl_mulai', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('tgl_selesai')->orWhere('tgl_selesai', '>=', $today);
            })
            ->count();

        $q3 = DB::table('satpam')
            ->where('nik', $nik)
            ->where(function ($q) use ($today) {
                $q->whereNull('tgl_mulai')->orWhere('tgl_mulai', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('tgl_selesai')->orWhere('tgl_selesai', '>=', $today);
            })
            ->count();

        return (int)$q1 + (int)$q2 + (int)$q3;
    }

    public function list_struktural_aktif($nik)
    {
        $today = date('Y-m-d');

        $rows = DB::table('penempatan as p')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'p.id_lembaga')
            ->select('l.nama_lembaga', 'p.jabatan_lembaga')
            ->where('p.nik', $nik)
            ->where(function ($q) use ($today) {
                $q->whereNull('p.tgl_mulai')->orWhere('p.tgl_mulai', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('p.tgl_selesai')->orWhere('p.tgl_selesai', '>=', $today);
            })
            ->orderBy('l.nama_lembaga', 'ASC')
            ->get();

        return array_map(fn($r) => (array)$r, $rows->toArray());
    }

    public function list_pengajar_aktif($nik)
    {
        $today = date('Y-m-d');

        $rows = DB::table('pengajar as pg')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'pg.id_lembaga')
            ->select('l.nama_lembaga', 'pg.kategori')
            ->where('pg.nik', $nik)
            ->where(function ($q) use ($today) {
                $q->whereNull('pg.tgl_mulai')->orWhere('pg.tgl_mulai', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('pg.tgl_selesai')->orWhere('pg.tgl_selesai', '>=', $today);
            })
            ->orderBy('l.nama_lembaga', 'ASC')
            ->get();

        return array_map(fn($r) => (array)$r, $rows->toArray());
    }

    public function sum_barokah_last_two_months($nik, $startTs, $endTs)
    {
        $combine = [];
        $acc = function ($rows) use (&$combine) {
            foreach ($rows as $r) {
                $r = (array)$r;
                $key = sprintf('%04d-%02d', (int)$r['y'], (int)$r['m']);
                if (!isset($combine[$key])) {
                    $combine[$key] = [
                        'y'           => (int)$r['y'],
                        'm'           => (int)$r['m'],
                        'label_bulan' => $r['label_bulan'] ?? null,
                        'label_tahun' => $r['label_tahun'] ?? null,
                        'total'       => 0,
                    ];
                }
                $combine[$key]['total'] += (int)$r['total'];
                if (!$combine[$key]['label_bulan'] && !empty($r['label_bulan'])) {
                    $combine[$key]['label_bulan'] = $r['label_bulan'];
                }
                if (!$combine[$key]['label_tahun'] && !empty($r['label_tahun'])) {
                    $combine[$key]['label_tahun'] = $r['label_tahun'];
                }
            }
        };

        // STRUKTURAL (tb.timestamp)
        $r1 = DB::select("
            SELECT YEAR(tb.`timestamp`) AS y,
                MONTH(tb.`timestamp`) AS m,
                MIN(tb.bulan)        AS label_bulan,
                MIN(tb.tahun)        AS label_tahun,
                COALESCE(SUM(tb.diterima),0) AS total
            FROM total_barokah tb
            JOIN penempatan p ON p.id_penempatan = tb.id_penempatan
            WHERE p.nik = ? AND tb.`timestamp` >= ? AND tb.`timestamp` < ?
            GROUP BY y, m
        ", [$nik, $startTs, $endTs]);
        $acc($r1);

        // PENGAJAR (tbp.timestamp)
        $r2 = DB::select("
            SELECT YEAR(tbp.`timestamp`) AS y,
                MONTH(tbp.`timestamp`) AS m,
                MIN(tbp.bulan)         AS label_bulan,
                MIN(tbp.tahun)         AS label_tahun,
                COALESCE(SUM(tbp.diterima),0) AS total
            FROM total_barokah_pengajar tbp
            JOIN pengajar pg ON pg.id_pengajar = tbp.id_pengajar
            WHERE pg.nik = ? AND tbp.`timestamp` >= ? AND tbp.`timestamp` < ?
            GROUP BY y, m
        ", [$nik, $startTs, $endTs]);
        $acc($r2);

        // SATPAM (tbs.tgl_input)
        $r3 = DB::select("
            SELECT YEAR(tbs.`tgl_input`) AS y,
                MONTH(tbs.`tgl_input`) AS m,
                MIN(tbs.bulan)         AS label_bulan,
                MIN(tbs.tahun)         AS label_tahun,
                COALESCE(SUM(tbs.diterima),0) AS total
            FROM total_barokah_satpam tbs
            JOIN satpam sp ON sp.id_satpam = tbs.id_satpam
            WHERE sp.nik = ? AND tbs.`tgl_input` >= ? AND tbs.`tgl_input` < ?
            GROUP BY y, m
        ", [$nik, $startTs, $endTs]);
        $acc($r3);

        return $combine;
    }

    public function list_barokah_last_two_months($nik, $startTs, $endTs)
    {
        $sql = "
        SELECT u.y, u.m, u.label_bulan, u.label_tahun, u.lembaga, u.jenis, u.nominal
        FROM (
            -- Struktural
            SELECT YEAR(tb.`timestamp`) AS y, MONTH(tb.`timestamp`) AS m,
                tb.bulan AS label_bulan, tb.tahun AS label_tahun,
                l.nama_lembaga AS lembaga, 'Struktural' AS jenis, tb.diterima AS nominal
            FROM total_barokah tb
            JOIN penempatan p ON p.id_penempatan = tb.id_penempatan
            JOIN lembaga l    ON l.id_lembaga    = p.id_lembaga
            WHERE p.nik = ? AND tb.`timestamp` >= ? AND tb.`timestamp` < ?

            UNION ALL

            -- Pengajar
            SELECT YEAR(tbp.`timestamp`) AS y, MONTH(tbp.`timestamp`) AS m,
                tbp.bulan AS label_bulan, tbp.tahun AS label_tahun,
                l.nama_lembaga AS lembaga, 'Pengajar' AS jenis, tbp.diterima AS nominal
            FROM total_barokah_pengajar tbp
            JOIN pengajar pg ON pg.id_pengajar = tbp.id_pengajar
            JOIN lembaga l   ON l.id_lembaga   = pg.id_lembaga
            WHERE pg.nik = ? AND tbp.`timestamp` >= ? AND tbp.`timestamp` < ?

            UNION ALL

            -- Satpam (lewat kehadiran_lembaga untuk nama lembaga)
            SELECT YEAR(tbs.`tgl_input`) AS y, MONTH(tbs.`tgl_input`) AS m,
                tbs.bulan AS label_bulan, tbs.tahun AS label_tahun,
                COALESCE(l.nama_lembaga,'(Tidak terkait lembaga)') AS lembaga,
                'Satpam' AS jenis, tbs.diterima AS nominal
            FROM total_barokah_satpam tbs
            JOIN satpam sp ON sp.id_satpam = tbs.id_satpam
            LEFT JOIN kehadiran_lembaga kl ON kl.id_kehadiran_lembaga = tbs.id_kehadiran_lembaga
            LEFT JOIN lembaga l ON l.id_lembaga = kl.id_lembaga
            WHERE sp.nik = ? AND tbs.`tgl_input` >= ? AND tbs.`tgl_input` < ?
        ) u
        ORDER BY u.y DESC, u.m DESC, u.lembaga, u.jenis
        ";

        $results = DB::select($sql, [
            $nik, $startTs, $endTs,  // Struktural
            $nik, $startTs, $endTs,  // Pengajar
            $nik, $startTs, $endTs   // Satpam
        ]);

        return array_map(fn($r) => (array)$r, $results);
    }

    public function sum_bulanan_by_kategori($nik, $k, $start, $end)
    {
        switch (strtolower($k)) {
            case 'pengajar':
                $sql = "
                    SELECT YEAR(t.`timestamp`) AS y,
                        MONTH(t.`timestamp`) AS m,
                        MAX(t.bulan)         AS label_bulan,
                        CASE 
                            WHEN LENGTH(MAX(t.tahun)) = 4 THEN MAX(t.tahun)
                            WHEN MAX(t.bulan) IN ('Januari','Februari','Maret','April','Mei','Juni') 
                            THEN SUBSTRING(MAX(t.tahun), 6, 4)
                            ELSE SUBSTRING(MAX(t.tahun), 1, 4)
                        END AS label_tahun,
                        COALESCE(SUM(t.diterima),0) AS total
                    FROM total_barokah_pengajar t
                    JOIN pengajar pg ON pg.id_pengajar = t.id_pengajar
                    WHERE pg.nik = ? AND t.`timestamp` >= ? AND t.`timestamp` < ?
                    GROUP BY y,m
                    ORDER BY y,m
                ";
                $bind = [$nik, $start, $end];
                break;

            case 'satpam':
                $sql = "
                    SELECT YEAR(t.`tgl_input`) AS y,
                        MONTH(t.`tgl_input`) AS m,
                        MAX(t.bulan)         AS label_bulan,
                        CASE 
                            WHEN LENGTH(MAX(t.tahun)) = 4 THEN MAX(t.tahun)
                            WHEN MAX(t.bulan) IN ('Januari','Februari','Maret','April','Mei','Juni') 
                            THEN SUBSTRING(MAX(t.tahun), 6, 4)
                            ELSE SUBSTRING(MAX(t.tahun), 1, 4)
                        END AS label_tahun,
                        COALESCE(SUM(t.diterima),0) AS total
                    FROM total_barokah_satpam t
                    JOIN satpam sp ON sp.id_satpam = t.id_satpam
                    WHERE sp.nik = ? AND t.`tgl_input` >= ? AND t.`tgl_input` < ?
                    GROUP BY y,m
                    ORDER BY y,m
                ";
                $bind = [$nik, $start, $end];
                break;

            default: // struktural
                $sql = "
                    SELECT YEAR(t.`timestamp`) AS y,
                        MONTH(t.`timestamp`) AS m,
                        MAX(t.bulan)         AS label_bulan,
                        CASE 
                            WHEN LENGTH(MAX(t.tahun)) = 4 THEN MAX(t.tahun)
                            WHEN MAX(t.bulan) IN ('Januari','Februari','Maret','April','Mei','Juni') 
                            THEN SUBSTRING(MAX(t.tahun), 6, 4)
                            ELSE SUBSTRING(MAX(t.tahun), 1, 4)
                        END AS label_tahun,
                        COALESCE(SUM(t.diterima),0) AS total
                    FROM total_barokah t
                    JOIN penempatan p ON p.id_penempatan = t.id_penempatan
                    WHERE p.nik = ? AND t.`timestamp` >= ? AND t.`timestamp` < ?
                    GROUP BY y,m
                    ORDER BY y,m
                ";
                $bind = [$nik, $start, $end];
        }

        $results = DB::select($sql, $bind);
        return array_map(fn($r) => (array)$r, $results);
    }

    public function detail_bulanan_by_kategori($nik, $k, $start, $end)
    {
        switch (strtolower($k)) {
            case 'pengajar':
                $sql = "
                    SELECT l.nama_lembaga, t.bulan, t.tahun,
                        'Pengajar' AS jenis, SUM(t.diterima) AS nominal,
                        pg.id_lembaga
                    FROM total_barokah_pengajar t
                    JOIN pengajar pg ON pg.id_pengajar = t.id_pengajar
                    JOIN lembaga  l  ON l.id_lembaga  = pg.id_lembaga
                    WHERE pg.nik = ? AND t.`timestamp` >= ? AND t.`timestamp` < ?
                    GROUP BY l.nama_lembaga, t.bulan, t.tahun, pg.id_lembaga
                    ORDER BY l.nama_lembaga DESC
                ";
                $bind = [$nik, $start, $end];
                break;

            case 'satpam':
                $sql = "
                    SELECT COALESCE(l.nama_lembaga,'(Satpam)') AS nama_lembaga, t.bulan, t.tahun,
                        'Satpam' AS jenis,
                        SUM(t.diterima) AS nominal,
                        kl.id_lembaga
                    FROM total_barokah_satpam t
                    JOIN satpam sp ON sp.id_satpam = t.id_satpam
                    LEFT JOIN kehadiran_lembaga kl ON kl.id_kehadiran_lembaga = t.id_kehadiran_lembaga
                    LEFT JOIN lembaga l ON l.id_lembaga = kl.id_lembaga
                    WHERE sp.nik = ? AND t.`tgl_input` >= ? AND t.`tgl_input` < ?
                    GROUP BY l.nama_lembaga, t.bulan, t.tahun, kl.id_lembaga
                    ORDER BY l.nama_lembaga DESC
                ";
                $bind = [$nik, $start, $end];
                break;

            default: // struktural
                $sql = "
                    SELECT l.nama_lembaga, t.bulan, t.tahun,
                         'Struktural' AS jenis, SUM(t.diterima) AS nominal,
                        p.id_lembaga
                    FROM total_barokah t
                    JOIN penempatan p ON p.id_penempatan = t.id_penempatan
                    JOIN lembaga   l ON l.id_lembaga    = p.id_lembaga
                    WHERE p.nik = ? AND t.`timestamp` >= ? AND t.`timestamp` < ?
                    GROUP BY l.nama_lembaga, t.bulan, t.tahun, p.id_lembaga
                    ORDER BY l.nama_lembaga DESC
                ";
                $bind = [$nik, $start, $end];
        }

        $res = DB::select($sql, $bind);
        $res = array_map(fn($r) => (array)$r, $res);

        $mapBulan = ['Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4, 'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8, 'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12];

        return array_map(function ($r) use ($mapBulan) {
            $mInt = $mapBulan[$r['bulan']] ?? 0;
            $partsY = explode('/', (string)($r['tahun'] ?? ''));
            $yReal = (isset($partsY[1]) && $mInt < 7) ? (int)$partsY[1] : (int)($partsY[0] ?? date('Y'));

            return [
                'lembaga' => ($r['nama_lembaga'] ?? '') . ' (' . ($r['bulan'] ?? '') . ' ' . ($r['tahun'] ?? '') . ')',
                'jenis'   => $r['jenis'],
                'nominal' => (int)($r['nominal'] ?? 0),
                'ent_id'  => ($r['id_lembaga'] ?? 0) . '-' . $mInt . '-' . $yReal,
            ];
        }, $res);
    }

    public function breakdown_pengajar_by_month($nik, $startTs, $endTs, $entId = null, $specM = null, $specY = null)
    {
        $whereL = '';
        $bind   = [$nik, $startTs, $endTs];

        if (!empty($entId)) {
            $whereL .= ' AND pg.id_lembaga = ? ';
            $bind[] = $entId;
        }
        if (!empty($specM) && !empty($specY)) {
            $nmBulan = $this->nama_bulan_id($specM);
            $nmTahun = $this->tahun_ajaran($specM, $specY);
            $whereL .= ' AND tbp.bulan = ? AND tbp.tahun = ? ';
            $bind[] = $nmBulan;
            $bind[] = $nmTahun;
        }

        $sql = "
            SELECT
                COALESCE(SUM(tbp.jumlah_sks),0)        AS sks,
                COALESCE(SUM(tbp.mengajar),0)          AS mengajar,
                COALESCE(SUM(tbp.dty),0)               AS gty_dty,
                COALESCE(SUM(tbp.jafung),0)            AS jafung,
                COALESCE(SUM(tbp.nominal_kehadiran),0) AS kehadiran,
                COALESCE(SUM(tbp.jumlah_hadir),0)      AS jhadir,
                COALESCE(SUM(tbp.jumlah_hadir_15),0)   AS jhadir15,
                COALESCE(SUM(tbp.nominal_hadir_15),0)  AS nhadir15,
                COALESCE(SUM(tbp.jumlah_hadir_10),0)   AS jhadir10,
                COALESCE(SUM(tbp.nominal_hadir_10),0)  AS nhadir10,
                COALESCE(SUM(tbp.walkes),0)            AS walkes,
                COALESCE(SUM(tbp.kehormatan),0)        AS kehormatan,
                COALESCE(SUM(tbp.khusus),0)            AS tambahan,
                COALESCE(SUM(tbp.barokah_piket),0)     AS piket,
                COALESCE(SUM(tbp.tun_anak),0)          AS tun_anak,
                COALESCE(SUM(tbp.tunkel),0)            AS tunkel,
                COALESCE(SUM(tbp.potongan),0)          AS potongan,
                COALESCE(SUM(tbp.diterima),0)          AS total
            FROM total_barokah_pengajar tbp
            JOIN pengajar pg ON pg.id_pengajar = tbp.id_pengajar
            WHERE pg.nik = ? AND tbp.`timestamp` >= ? AND tbp.`timestamp` < ? {$whereL}
        ";
        $row = DB::selectOne($sql, $bind);
        $row = $row ? (array)$row : [];
        $row['per_sks'] = (!empty($row['sks']) && (int)$row['sks'] > 0)
            ? (int) round(((int)$row['mengajar']) / (int)$row['sks']) : 0;
        return $row;
    }

    public function breakdown_struktural_by_month($nik, $startTs, $endTs, $id_lembaga = null, $specM = null, $specY = null)
    {
        $bind  = [$nik, $startTs, $endTs];
        $extra = '';
        if (!empty($id_lembaga)) {
            $extra .= ' AND p.id_lembaga = ? ';
            $bind[] = $id_lembaga;
        }
        if (!empty($specM) && !empty($specY)) {
            $nmBulan = $this->nama_bulan_id($specM);
            $nmTahun = $this->tahun_ajaran($specM, $specY);
            $extra .= ' AND t.bulan = ? AND t.tahun = ? ';
            $bind[] = $nmBulan;
            $bind[] = $nmTahun;
        }

        $sql = "
            SELECT
                COALESCE(SUM(t.tunjab),0)            AS barokah_pokok,
                COALESCE(SUM(t.nominal_kehadiran),0) AS kehadiran_nominal,
                COALESCE(SUM(t.kehadiran),0)         AS kehadiran,
                COALESCE(SUM(t.tunkel),0)            AS tunkel,
                COALESCE(SUM(t.tunj_anak),0)         AS tun_anak,
                COALESCE(SUM(t.tmp),0)               AS tmp,
                COALESCE(SUM(t.tbk),0)               AS tbk,
                COALESCE(SUM(t.kehormatan),0)        AS kehormatan,
                COALESCE(SUM(t.potongan),0)          AS potongan,
                COALESCE(SUM(t.diterima),0)          AS total
            FROM total_barokah t
            JOIN penempatan p ON p.id_penempatan = t.id_penempatan
            WHERE p.nik = ?
            AND t.`timestamp` >= ?
            AND t.`timestamp` < ?
            {$extra}
        ";
        $row = DB::selectOne($sql, $bind);
        $row = $row ? (array)$row : [];

        $bindH  = [$nik];
        $extraH = '';
        if (!empty($id_lembaga)) {
            $extraH .= ' AND p.id_lembaga = ? ';
            $bindH[] = $id_lembaga;
        }

        if (!empty($specM) && !empty($specY)) {
            $s = (new DateTime("$specY-$specM-01"))->format('Y-m-d H:i:s');
            $e = (new DateTime("$specY-$specM-01"))->modify('+1 month')->format('Y-m-d H:i:s');
            $bindH[] = $s;
            $bindH[] = $e;
        } else {
            $bindH[] = $startTs;
            $bindH[] = $endTs;
        }

        $sqlH = "
            SELECT COALESCE(SUM(k.jumlah_hadir),0) AS jhadir
            FROM kehadiran k
            JOIN penempatan p ON p.id_penempatan = k.id_penempatan
            WHERE p.nik = ?
            AND k.`tgl_input` >= ?
            AND k.`tgl_input` < ?
            {$extraH}
        ";
        $rowH = DB::selectOne($sqlH, $bindH);
        $row['jhadir_normal'] = (int)($rowH->jhadir ?? 0);
        return $row;
    }

    public function breakdown_satpam_by_month($nik, $startTs, $endTs, $id_lembaga = null, $specM = null, $specY = null)
    {
        $whereL = '';
        $bind   = [$nik, $startTs, $endTs];
        if (!empty($id_lembaga)) {
            $whereL .= ' AND kl.id_lembaga = ? ';
            $bind[] = $id_lembaga;
        }
        if (!empty($specM) && !empty($specY)) {
            $nmBulan = $this->nama_bulan_id($specM);
            $nmTahun = $this->tahun_ajaran($specM, $specY);
            $whereL .= ' AND t.bulan = ? AND t.tahun = ? ';
            $bind[] = $nmBulan;
            $bind[] = $nmTahun;
        }

        $sql = "
            SELECT
            COALESCE(SUM(t.jumlah_hari),0)                                 AS j_hari,
            COALESCE(SUM(t.nominal_transport * t.jumlah_hari),0)           AS k1_total,
            CASE WHEN COALESCE(SUM(t.jumlah_hari),0)=0
                THEN 0
                ELSE ROUND(SUM(t.nominal_transport * t.jumlah_hari)
                            / NULLIF(SUM(t.jumlah_hari),0))
            END                                                            AS k1_unit,

            COALESCE(SUM(t.jumlah_shift),0)                                AS j_shift,
            COALESCE(SUM(t.rank * t.jumlah_shift),0)                       AS k2_total,
            CASE WHEN COALESCE(SUM(t.jumlah_shift),0)=0
                THEN 0
                ELSE ROUND(SUM(t.rank * t.jumlah_shift)
                            / NULLIF(SUM(t.jumlah_shift),0))
            END                                                            AS k2_unit,

            COALESCE(SUM(t.jumlah_dinihari),0)                             AS j_dinihari,
            COALESCE(SUM(t.konsumsi * t.jumlah_dinihari),0)                AS k3_total,
            CASE WHEN COALESCE(SUM(t.jumlah_dinihari),0)=0
                THEN 0
                ELSE ROUND(SUM(t.konsumsi * t.jumlah_dinihari)
                            / NULLIF(SUM(t.jumlah_dinihari),0))
            END                                                            AS k3_unit,

            COALESCE(SUM(t.diterima),0)                                    AS total
            FROM total_barokah_satpam t
            JOIN satpam sp ON sp.id_satpam = t.id_satpam
            LEFT JOIN kehadiran_lembaga kl ON kl.id_kehadiran_lembaga = t.id_kehadiran_lembaga
            WHERE sp.nik = ?
            AND t.`tgl_input` >= ?
            AND t.`tgl_input` < ?
            {$whereL}
        ";
        $row = DB::selectOne($sql, $bind);
        return $row ? (array)$row : [];
    }

    public function list_potongan_pengajar_by_month($nik, $startTs, $endTs, $id_lembaga = null, $specM = null, $specY = null)
    {
        // Cari baris slip total_barokah_pengajar yang sesuai untuk mendapatkan tanggal payroll dan id_pengajar presisi
        $whereL = '';
        $bindL = [$nik, $startTs, $endTs];
        if (!empty($id_lembaga)) {
            $whereL .= ' AND pg.id_lembaga = ? ';
            $bindL[] = $id_lembaga;
        }
        if (!empty($specM) && !empty($specY)) {
            $nmBulan = $this->nama_bulan_id($specM);
            $nmTahun = $this->tahun_ajaran($specM, $specY);
            $whereL .= ' AND tbp.bulan = ? AND tbp.tahun = ? ';
            $bindL[] = $nmBulan;
            $bindL[] = $nmTahun;
        }

        $slip = DB::selectOne("
            SELECT tbp.id_pengajar, tbp.timestamp, tbp.potongan
            FROM total_barokah_pengajar tbp
            JOIN pengajar pg ON pg.id_pengajar = tbp.id_pengajar
            WHERE pg.nik = ? AND tbp.`timestamp` >= ? AND tbp.`timestamp` < ? {$whereL}
            ORDER BY tbp.id_total_barokah_pengajar DESC
            LIMIT 1
        ", $bindL);

        if ($slip && !empty($slip->timestamp)) {
            $payrollDate = date('Y-m-d', strtotime($slip->timestamp));
            $sql = "
                SELECT p.nama_potongan AS nama, SUM(pp.nominal_potongan) AS nominal
                FROM potongan_pengajar pp
                JOIN potongan p ON p.id_potongan = pp.jenis_potongan
                WHERE pp.id_pengajar = ?
                  AND (pp.min_periode_potongan IS NULL OR pp.min_periode_potongan <= ?)
                  AND (pp.max_periode_potongan IS NULL OR pp.max_periode_potongan >= ?)
                GROUP BY p.nama_potongan
                ORDER BY p.nama_potongan ASC
            ";
            $results = DB::select($sql, [$slip->id_pengajar, $payrollDate, $payrollDate]);
            return array_map(fn($r) => (array)$r, $results);
        }

        // Fallback jika slip belum tercatat di total_barokah_pengajar
        $bind  = [$nik, $endTs, $startTs];
        $extra = '';
        if (!empty($id_lembaga)) {
            $extra = ' AND pg.id_lembaga = ? ';
            $bind[] = $id_lembaga;
        }

        $sql = "
            SELECT nama, SUM(nominal) AS nominal
            FROM (
                SELECT p.nama_potongan AS nama, pp.nominal_potongan AS nominal
                FROM potongan_pengajar pp
                JOIN potongan p  ON p.id_potongan = pp.jenis_potongan
                JOIN pengajar pg ON pg.id_pengajar = pp.id_pengajar
                WHERE pg.nik = ?
                  AND (pp.min_periode_potongan IS NULL OR pp.min_periode_potongan <  ?)
                  AND (pp.max_periode_potongan IS NULL OR pp.max_periode_potongan >= ?)
                  {$extra}
            ) t
            GROUP BY nama
            ORDER BY nama ASC
        ";

        $results = DB::select($sql, $bind);
        return array_map(fn($r) => (array)$r, $results);
    }

    public function list_potongan_struktural_by_month($nik, $startTs, $endTs, $id_lembaga = null, $specM = null, $specY = null)
    {
        // Cari baris slip total_barokah yang sesuai untuk mendapatkan tanggal payroll dan id_penempatan presisi
        $whereL = '';
        $bindL = [$nik, $startTs, $endTs];
        if (!empty($id_lembaga)) {
            $whereL .= ' AND p.id_lembaga = ? ';
            $bindL[] = $id_lembaga;
        }
        if (!empty($specM) && !empty($specY)) {
            $nmBulan = $this->nama_bulan_id($specM);
            $nmTahun = $this->tahun_ajaran($specM, $specY);
            $whereL .= ' AND t.bulan = ? AND t.tahun = ? ';
            $bindL[] = $nmBulan;
            $bindL[] = $nmTahun;
        }

        $slip = DB::selectOne("
            SELECT t.id_penempatan, t.timestamp, t.potongan
            FROM total_barokah t
            JOIN penempatan p ON p.id_penempatan = t.id_penempatan
            WHERE p.nik = ? AND t.`timestamp` >= ? AND t.`timestamp` < ? {$whereL}
            ORDER BY t.id_total_barokah DESC
            LIMIT 1
        ", $bindL);

        if ($slip && !empty($slip->timestamp)) {
            $payrollDate = date('Y-m-d', strtotime($slip->timestamp));
            $sql = "
                SELECT p.nama_potongan AS nama, SUM(pu.nominal_potongan) AS nominal
                FROM potongan_umana pu
                JOIN potongan p ON p.id_potongan = pu.jenis_potongan
                WHERE pu.id_penempatan = ?
                  AND (pu.min_periode_potongan IS NULL OR pu.min_periode_potongan <= ?)
                  AND (pu.max_periode_potongan IS NULL OR pu.max_periode_potongan >= ?)
                GROUP BY p.nama_potongan
                ORDER BY p.nama_potongan ASC
            ";
            $results = DB::select($sql, [$slip->id_penempatan, $payrollDate, $payrollDate]);
            return array_map(fn($r) => (array)$r, $results);
        }

        // Fallback jika baris total_barokah tidak ditemukan
        $bind  = [$nik, $endTs, $startTs];
        $extra = '';
        if (!empty($id_lembaga)) {
            $extra = ' AND pen.id_lembaga = ? ';
            $bind[] = $id_lembaga;
        }

        $sql = "
            SELECT nama, SUM(nominal) AS nominal
            FROM (
                SELECT p.nama_potongan AS nama, pu.nominal_potongan AS nominal
                FROM potongan_umana pu
                JOIN potongan p    ON p.id_potongan = pu.jenis_potongan
                JOIN penempatan pen ON pen.id_penempatan = pu.id_penempatan
                WHERE pen.nik = ?
                  AND (pu.min_periode_potongan IS NULL OR pu.min_periode_potongan <  ?)
                  AND (pu.max_periode_potongan IS NULL OR pu.max_periode_potongan >= ?)
                  {$extra}
            ) t
            GROUP BY nama
            ORDER BY nama ASC
        ";

        $results = DB::select($sql, $bind);
        return array_map(fn($r) => (array)$r, $results);
    }
}
