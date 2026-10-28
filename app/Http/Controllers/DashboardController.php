<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Penempatan;
use App\Models\Pengajar;
use App\Models\KehadiranLembaga;
use App\Models\PotonganUmana;
use App\Models\PotonganPengajar;
use App\Models\Lembaga;
use App\Models\Umana;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $isAdminLembaga = $user && $user->jabatan === 'AdminLembaga' && $user->lembaga;

        if ($isAdminLembaga) {
            $lembagaId = $user->lembaga;
            $lembaga = Lembaga::find($lembagaId);

            // 1. Data Umana Struktural & Pengajar Aktif
            $totalStruktural = Penempatan::where('id_lembaga', $lembagaId)
                ->where('status', 'aktif')
                ->count();

            $totalPengajar = Pengajar::where('id_lembaga', $lembagaId)
                ->where('status', 'aktif')
                ->count();

            // 2. Status Kehadiran Lembaga Terbaru
            $kehadiranStrukturalTerbaru = KehadiranLembaga::where('id_lembaga', $lembagaId)
                ->where('kategori', 'Struktural')
                ->orderBy('id_kehadiran_lembaga', 'desc')
                ->first();

            $kehadiranPengajarTerbaru = KehadiranLembaga::where('id_lembaga', $lembagaId)
                ->where('kategori', 'Pengajar')
                ->orderBy('id_kehadiran_lembaga', 'desc')
                ->first();

            // 3. Potongan Lembaga
            $totalPotonganUmana = PotonganUmana::whereHas('penempatan', function ($q) use ($lembagaId) {
                $q->where('id_lembaga', $lembagaId);
            })->sum('nominal_potongan');

            $totalPotonganPengajar = PotonganPengajar::whereHas('pengajar', function ($q) use ($lembagaId) {
                $q->where('id_lembaga', $lembagaId);
            })->sum('nominal_potongan');

            $totalNominalPotongan = $totalPotonganUmana + $totalPotonganPengajar;

            // 4. Daftar Potongan Terbaru (5 Terakhir)
            $recentPotonganUmana = PotonganUmana::with(['penempatan.umana', 'potongan'])
                ->whereHas('penempatan', function ($q) use ($lembagaId) {
                    $q->where('id_lembaga', $lembagaId);
                })
                ->orderBy('id_potongan_umana', 'desc')
                ->take(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'nama' => $item->penempatan && $item->penempatan->umana ? $item->penempatan->umana->nama_lengkap : '-',
                        'kategori' => 'Struktural',
                        'jenis' => $item->potongan ? $item->potongan->nama_potongan : $item->jenis_potongan,
                        'nominal' => $item->nominal_potongan,
                        'periode' => $item->min_periode_potongan ? date('M Y', strtotime($item->min_periode_potongan)) : '-'
                    ];
                });

            $recentPotonganPengajar = PotonganPengajar::with(['pengajar.umana', 'potongan'])
                ->whereHas('pengajar', function ($q) use ($lembagaId) {
                    $q->where('id_lembaga', $lembagaId);
                })
                ->orderBy('id_potongan_pengajar', 'desc')
                ->take(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'nama' => $item->pengajar && $item->pengajar->umana ? $item->pengajar->umana->nama_lengkap : '-',
                        'kategori' => 'Pengajar',
                        'jenis' => $item->potongan ? $item->potongan->nama_potongan : $item->jenis_potongan,
                        'nominal' => $item->nominal_potongan,
                        'periode' => $item->min_periode_potongan ? date('M Y', strtotime($item->min_periode_potongan)) : '-'
                    ];
                });

            $recentPotongan = collect($recentPotonganUmana)
                ->concat($recentPotonganPengajar)
                ->take(5);

            return view('dashboard.admin_lembaga', compact(
                'lembaga',
                'totalStruktural',
                'totalPengajar',
                'kehadiranStrukturalTerbaru',
                'kehadiranPengajarTerbaru',
                'totalNominalPotongan',
                'recentPotongan'
            ));
        }

        // Default Dashboard (SuperAdmin, Evaluasi, SDM, Kasir)
        $totalUmana = Umana::count();
        $totalLembaga = Lembaga::count();
        $totalPenempatan = Penempatan::where('status', 'aktif')->count();
        $totalPengajar = Pengajar::where('status', 'aktif')->count();

        return view('dashboard.index', compact(
            'totalUmana',
            'totalLembaga',
            'totalPenempatan',
            'totalPengajar'
        ));
    }
}
