<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pengajar;
use App\Models\Penempatan;

class LogAnomaliSkController extends Controller
{
    /**
     * Tampilkan Halaman Utama Log Anomali Data SK
     */
    public function index(Request $request)
    {
        $stats = $this->getStatistics();

        return view('log.anomali-sk', compact('stats'));
    }

    /**
     * Hitung Statistik Anomali Data SK
     */
    private function getStatistics()
    {
        // 1. Residu Pengajar (Lembaga kosong atau tanggal 0000-00-00)
        $residuPengajar = DB::table('pengajar')
            ->where(function ($q) {
                $q->whereNull('id_lembaga')
                  ->orWhere('id_lembaga', '')
                  ->orWhere('id_lembaga', '0')
                  ->orWhere('tgl_mulai', '0000-00-00')
                  ->orWhere('tgl_selesai', '0000-00-00');
            })
            ->count();

        // 2. Residu Struktural (Lembaga kosong atau tanggal 0000-00-00)
        $residuStruktural = DB::table('penempatan')
            ->where(function ($q) {
                $q->whereNull('id_lembaga')
                  ->orWhere('id_lembaga', '')
                  ->orWhere('id_lembaga', '0')
                  ->orWhere('tgl_mulai', '0000-00-00')
                  ->orWhere('tgl_selesai', '0000-00-00');
            })
            ->count();

        // 3. SK Pengajar Kadaluarsa tapi Status Masih 'Aktif'
        $expiredPengajar = DB::table('pengajar')
            ->where('status', 'Aktif')
            ->whereNotNull('tgl_selesai')
            ->where('tgl_selesai', '!=', '0000-00-00')
            ->where('tgl_selesai', '<', DB::raw('CURDATE()'))
            ->count();

        // 4. SK Struktural Kadaluarsa tapi Status Masih 'Aktif'
        $expiredStruktural = DB::table('penempatan')
            ->where('status', 'Aktif')
            ->whereNotNull('tgl_selesai')
            ->where('tgl_selesai', '!=', '0000-00-00')
            ->where('tgl_selesai', '<', DB::raw('CURDATE()'))
            ->count();

        return [
            'residu_pengajar'    => $residuPengajar,
            'residu_struktural'  => $residuStruktural,
            'total_residu'       => $residuPengajar + $residuStruktural,
            'expired_pengajar'   => $expiredPengajar,
            'expired_struktural' => $expiredStruktural,
            'total_expired'      => $expiredPengajar + $expiredStruktural,
        ];
    }

    /**
     * AJAX Endpoint untuk DataTables Anomali SK
     */
    public function data(Request $request)
    {
        $kategori = $request->get('kategori', 'residu');

        $data = [];

        if ($kategori === 'residu') {
            // Ambil Pengajar Cacat / Residu
            $pengajarResidu = DB::table('pengajar as peng')
                ->leftJoin('umana as u', 'u.nik', '=', 'peng.nik')
                ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'peng.id_lembaga')
                ->where(function ($q) {
                    $q->whereNull('peng.id_lembaga')
                      ->orWhere('peng.id_lembaga', '')
                      ->orWhere('peng.id_lembaga', '0')
                      ->orWhere('peng.tgl_mulai', '0000-00-00')
                      ->orWhere('peng.tgl_selesai', '0000-00-00');
                })
                ->select(
                    'peng.id_pengajar as id',
                    DB::raw("'Pengajar' as tipe"),
                    'peng.nik',
                    DB::raw("COALESCE(u.nama_lengkap, '-') as nama_lengkap"),
                    DB::raw("COALESCE(l.nama_lembaga, '-') as nama_lembaga"),
                    'peng.kategori as posisi',
                    'peng.tgl_mulai',
                    'peng.tgl_selesai',
                    'peng.status',
                    'peng.tunj_kel',
                    'peng.tunj_anak',
                    'peng.kehormatan'
                )
                ->get();

            // Ambil Struktural Cacat / Residu
            $strukturalResidu = DB::table('penempatan as p')
                ->leftJoin('umana as u', 'u.nik', '=', 'p.nik')
                ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'p.id_lembaga')
                ->leftJoin('ketentuan_barokah as kb', 'kb.id_ketentuan', '=', 'p.id_ketentuan')
                ->where(function ($q) {
                    $q->whereNull('p.id_lembaga')
                      ->orWhere('p.id_lembaga', '')
                      ->orWhere('p.id_lembaga', '0')
                      ->orWhere('p.tgl_mulai', '0000-00-00')
                      ->orWhere('p.tgl_selesai', '0000-00-00');
                })
                ->select(
                    'p.id_penempatan as id',
                    DB::raw("'Struktural' as tipe"),
                    'p.nik',
                    DB::raw("COALESCE(u.nama_lengkap, '-') as nama_lengkap"),
                    DB::raw("COALESCE(l.nama_lembaga, '-') as nama_lembaga"),
                    DB::raw("COALESCE(kb.nama_jabatan, p.jabatan_lembaga, 'Struktural') as posisi"),
                    'p.tgl_mulai',
                    'p.tgl_selesai',
                    'p.status',
                    'p.tunj_kel',
                    'p.tunj_anak',
                    'p.kehormatan'
                )
                ->get();

            $data = $pengajarResidu->concat($strukturalResidu);
        } elseif ($kategori === 'expired_pengajar') {
            $data = DB::table('pengajar as peng')
                ->leftJoin('umana as u', 'u.nik', '=', 'peng.nik')
                ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'peng.id_lembaga')
                ->where('peng.status', 'Aktif')
                ->whereNotNull('peng.tgl_selesai')
                ->where('peng.tgl_selesai', '!=', '0000-00-00')
                ->where('peng.tgl_selesai', '<', DB::raw('CURDATE()'))
                ->select(
                    'peng.id_pengajar as id',
                    DB::raw("'Pengajar' as tipe"),
                    'peng.nik',
                    DB::raw("COALESCE(u.nama_lengkap, '-') as nama_lengkap"),
                    DB::raw("COALESCE(l.nama_lembaga, '-') as nama_lembaga"),
                    'peng.kategori as posisi',
                    'peng.tgl_mulai',
                    'peng.tgl_selesai',
                    'peng.status',
                    'peng.tunj_kel',
                    'peng.tunj_anak',
                    'peng.kehormatan'
                )
                ->get();
        } elseif ($kategori === 'expired_struktural') {
            $data = DB::table('penempatan as p')
                ->leftJoin('umana as u', 'u.nik', '=', 'p.nik')
                ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'p.id_lembaga')
                ->leftJoin('ketentuan_barokah as kb', 'kb.id_ketentuan', '=', 'p.id_ketentuan')
                ->where('p.status', 'Aktif')
                ->whereNotNull('p.tgl_selesai')
                ->where('p.tgl_selesai', '!=', '0000-00-00')
                ->where('p.tgl_selesai', '<', DB::raw('CURDATE()'))
                ->select(
                    'p.id_penempatan as id',
                    DB::raw("'Struktural' as tipe"),
                    'p.nik',
                    DB::raw("COALESCE(u.nama_lengkap, '-') as nama_lengkap"),
                    DB::raw("COALESCE(l.nama_lembaga, '-') as nama_lembaga"),
                    DB::raw("COALESCE(kb.nama_jabatan, p.jabatan_lembaga, 'Struktural') as posisi"),
                    'p.tgl_mulai',
                    'p.tgl_selesai',
                    'p.status',
                    'p.tunj_kel',
                    'p.tunj_anak',
                    'p.kehormatan'
                )
                ->get();
        }

        return response()->json([
            'success' => true,
            'data'    => $data,
            'stats'   => $this->getStatistics()
        ]);
    }

    /**
     * Hapus Data Residu Anomali Tunggal
     */
    public function destroy(Request $request, $tipe, $id)
    {
        if ($tipe === 'Pengajar' || $tipe === 'pengajar') {
            DB::table('pengajar')->where('id_pengajar', $id)->delete();
        } elseif ($tipe === 'Struktural' || $tipe === 'struktural') {
            DB::table('penempatan')->where('id_penempatan', $id)->delete();
        } else {
            return response()->json(['success' => false, 'message' => 'Tipe data tidak valid.'], 400);
        }

        return response()->json([
            'success' => true,
            'message' => "Data residu {$tipe} berhasil dihapus dari sistem.",
            'stats'   => $this->getStatistics()
        ]);
    }

    /**
     * Nonaktifkan Status SK Tunggal
     */
    public function deactivate(Request $request, $tipe, $id)
    {
        if ($tipe === 'Pengajar' || $tipe === 'pengajar') {
            DB::table('pengajar')->where('id_pengajar', $id)->update(['status' => 'Tidak Aktif']);
        } elseif ($tipe === 'Struktural' || $tipe === 'struktural') {
            DB::table('penempatan')->where('id_penempatan', $id)->update(['status' => 'Tidak Aktif']);
        } else {
            return response()->json(['success' => false, 'message' => 'Tipe data tidak valid.'], 400);
        }

        return response()->json([
            'success' => true,
            'message' => "Status SK {$tipe} berhasil diubah menjadi Tidak Aktif.",
            'stats'   => $this->getStatistics()
        ]);
    }

    /**
     * Pembersihan Masal Data Residu Kosong / 0000-00-00
     */
    public function cleanAllResidu(Request $request)
    {
        $delPengajar = DB::table('pengajar')
            ->where(function ($q) {
                $q->whereNull('id_lembaga')
                  ->orWhere('id_lembaga', '')
                  ->orWhere('id_lembaga', '0');
            })
            ->where(function ($q) {
                $q->where('tgl_mulai', '0000-00-00')
                  ->orWhere('tgl_selesai', '0000-00-00')
                  ->orWhereNull('tgl_mulai')
                  ->orWhereNull('tgl_selesai');
            })
            ->delete();

        $delStruktural = DB::table('penempatan')
            ->where(function ($q) {
                $q->whereNull('id_lembaga')
                  ->orWhere('id_lembaga', '')
                  ->orWhere('id_lembaga', '0');
            })
            ->where(function ($q) {
                $q->where('tgl_mulai', '0000-00-00')
                  ->orWhere('tgl_selesai', '0000-00-00')
                  ->orWhereNull('tgl_mulai')
                  ->orWhereNull('tgl_selesai');
            })
            ->delete();

        $totalDeleted = $delPengajar + $delStruktural;

        return response()->json([
            'success' => true,
            'message' => "Berhasil membersihkan {$totalDeleted} baris data residu kosong ({$delPengajar} Pengajar, {$delStruktural} Struktural).",
            'stats'   => $this->getStatistics()
        ]);
    }

    /**
     * Nonaktifkan Masal SK yang Telah Kadaluarsa
     */
    public function deactivateExpiredAll(Request $request)
    {
        $target = $request->input('target', 'all'); // 'all', 'pengajar', 'struktural'

        $countPengajar = 0;
        $countStruktural = 0;

        if ($target === 'all' || $target === 'pengajar') {
            $countPengajar = DB::table('pengajar')
                ->where('status', 'Aktif')
                ->whereNotNull('tgl_selesai')
                ->where('tgl_selesai', '!=', '0000-00-00')
                ->where('tgl_selesai', '<', DB::raw('CURDATE()'))
                ->update(['status' => 'Tidak Aktif']);
        }

        if ($target === 'all' || $target === 'struktural') {
            $countStruktural = DB::table('penempatan')
                ->where('status', 'Aktif')
                ->whereNotNull('tgl_selesai')
                ->where('tgl_selesai', '!=', '0000-00-00')
                ->where('tgl_selesai', '<', DB::raw('CURDATE()'))
                ->update(['status' => 'Tidak Aktif']);
        }

        $totalUpdated = $countPengajar + $countStruktural;

        return response()->json([
            'success' => true,
            'message' => "Berhasil menonaktifkan {$totalUpdated} SK kedaluwarsa ({$countPengajar} Pengajar, {$countStruktural} Struktural).",
            'stats'   => $this->getStatistics()
        ]);
    }
}
