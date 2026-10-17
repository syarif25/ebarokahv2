<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KehadiranLembaga;
use Illuminate\Support\Facades\Auth;

class ValidasiController extends Controller
{
    /**
     * Tampilan utama Menu Validasi Barokah
     */
    public function index(Request $request)
    {
        // RBAC: Hanya SuperAdmin, Evaluasi, Bendahara / SDM yang boleh akses
        $user = Auth::user();
        if ($user && in_array($user->jabatan, ['AdminLembaga', 'umana'])) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengakses menu Validasi Barokah.');
        }

        if ($request->ajax()) {
            return $this->dataList($request);
        }

        return view('validasi.index');
    }

    /**
     * Endpoint DataTables AJAX untuk daftar validasi kehadiran
     */
    public function dataList(Request $request)
    {
        $statusFilter = $request->input('status_filter', 'antrian'); // default: antrian (Terkirim & acc)

        $query = KehadiranLembaga::with('lembaga');

        // Filter status
        if ($statusFilter === 'antrian') {
            // Sesuai alur lama: status != 'Belum' and status != 'Sudah' and status != 'selesai'
            $query->whereIn('status', ['Terkirim', 'acc', 'Revisi']);
        } elseif ($statusFilter === 'Terkirim') {
            $query->where('status', 'Terkirim');
        } elseif ($statusFilter === 'acc') {
            $query->where('status', 'acc');
        } elseif ($statusFilter === 'selesai') {
            $query->where('status', 'selesai');
        } elseif ($statusFilter === 'all') {
            $query->whereNotIn('status', ['Belum', 'Sudah']);
        }

        $list = $query->orderBy('id_kehadiran_lembaga', 'desc')->get();

        $data = [];
        $no = 1;

        foreach ($list as $item) {
            $id = $item->id_kehadiran_lembaga;
            $namaLembaga = $item->lembaga->nama_lembaga ?? '-';
            $periode = ($item->bulan ?? '') . ' ' . ($item->tahun ?? '');
            $kategori = $item->kategori ?? 'Struktural';
            $status = $item->status ?? 'Terkirim';

            // Tentukan URL Koreksi berdasarkan kategori
            $urlKoreksi = '#';
            if ($kategori === 'Struktural') {
                $urlKoreksi = url('/kehadiran_struktural/' . $id . '/koreksi');
            } elseif ($kategori === 'Pengajar') {
                $urlKoreksi = url('/kehadiran_pengajar/' . $id . '/koreksi');
            } elseif ($kategori === 'Satpam') {
                $urlKoreksi = url('/validasi_satpam/koreksi/' . $id);
            }

            // Kolom Koreksi & Kolom Status sesuai tampilan referensi
            $btnColor = '#d848c9';
            $btnIcon = 'fa-clipboard';
            $btnText = 'Jml Kehadiran';
            $statusHtml = '';

            if ($status === 'acc') {
                $btnColor = '#00a15d';
                $btnIcon = 'fa-check';
                $statusHtml = '<span style="color: #16a34a; font-weight: 600;">Sudah dikoreksi</span><br><small class="text-muted">(Menunggu ditransfer)</small>';
            } elseif ($status === 'Terkirim') {
                $btnColor = '#d848c9';
                $btnIcon = 'fa-clipboard';
                $statusHtml = '<span style="color: #dc2626; font-weight: 600;">Belum dikoreksi</span>';
            } elseif ($status === 'selesai') {
                $btnColor = '#0284c7';
                $btnIcon = 'fa-check-double';
                $statusHtml = '<span style="color: #0284c7; font-weight: 600;">Sudah Ditransfer</span>';
            } elseif ($status === 'Revisi') {
                $btnColor = '#f59e0b';
                $btnIcon = 'fa-undo';
                $statusHtml = '<span style="color: #d97706; font-weight: 600;">Revisi</span>';
            } else {
                $btnColor = '#6c757d';
                $btnIcon = 'fa-clipboard';
                $statusHtml = '<span class="text-muted font-weight-bold">' . e($status) . '</span>';
            }

            $btnKoreksi = '<a href="' . $urlKoreksi . '" class="btn btn-sm text-white font-weight-bold d-inline-flex align-items-center" style="background-color: ' . $btnColor . '; border-radius: 20px; padding: 6px 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">'
                . '<i class="fa ' . $btnIcon . ' me-1" style="font-size: 13px;"></i> ' . $btnText . '</a>';

            $row = [
                'no' => $no++,
                'id_kehadiran_lembaga' => $id,
                'nama_lembaga' => $namaLembaga,
                'periode' => $periode,
                'kategori' => $kategori,
                'koreksi' => $btnKoreksi,
                'status' => $status,
                'status_html' => $statusHtml,
                'url_koreksi' => $urlKoreksi,
            ];

            $data[] = $row;
        }

        return response()->json(['data' => $data]);
    }
}
