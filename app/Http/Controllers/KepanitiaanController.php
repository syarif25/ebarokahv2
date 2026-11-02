<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\KepanitiaanTransaksi;
use App\Models\KepanitiaanDetail;
use App\Models\MasterJenisKegiatan;
use App\Models\BarokahKepanitiaan;
use App\Models\Lembaga;
use App\Models\Umana;

class KepanitiaanController extends Controller
{
    /**
     * Halaman Utama: Daftar Transaksi Event Kepanitiaan
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($request->ajax()) {
            $query = KepanitiaanTransaksi::with(['jenisKegiatan', 'lembaga'])
                ->orderBy('id_kepanitiaan_transaksi', 'desc');

            // Filter Lembaga (jika Admin Lembaga)
            if ($user && $user->isAdminLembaga() && $user->id_lembaga) {
                $query->where(function($q) use ($user) {
                    $q->where('id_lembaga', $user->id_lembaga)
                      ->orWhereNull('id_lembaga');
                });
            }

            if ($request->filled('id_lembaga')) {
                $query->where('id_lembaga', $request->id_lembaga);
            }

            if ($request->filled('id_jenis_kegiatan')) {
                $query->where('id_jenis_kegiatan', $request->id_jenis_kegiatan);
            }

            if ($request->filled('bulan')) {
                $query->where('bulan', $request->bulan);
            }

            if ($request->filled('tahun')) {
                $query->where('tahun', $request->tahun);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $data = $query->get()->map(function ($item) {
                return [
                    'id_kepanitiaan_transaksi' => $item->id_kepanitiaan_transaksi,
                    'nama_event'               => $item->nama_event,
                    'id_jenis_kegiatan'        => $item->id_jenis_kegiatan,
                    'jenis_kegiatan'           => $item->jenisKegiatan ? $item->jenisKegiatan->nama_kegiatan : '-',
                    'id_lembaga'               => $item->id_lembaga,
                    'nama_lembaga'             => $item->lembaga ? $item->lembaga->nama_lembaga : 'Lintas Lembaga',
                    'bulan'                    => $item->bulan,
                    'tahun'                    => $item->tahun,
                    'jumlah_panitia'           => $item->details()->count(),
                    'jumlah_total'             => (float) $item->jumlah_total,
                    'status'                   => $item->status,
                    'catatan_pimpinan'         => $item->catatan_pimpinan,
                ];
            });

            return response()->json(['data' => $data]);
        }

        $lembagas = Lembaga::orderBy('nama_lembaga', 'asc')->get();
        $jenisKegiatan = MasterJenisKegiatan::where('is_active', 1)->orderBy('nama_kegiatan', 'asc')->get();

        return view('kepanitiaan.index', compact('lembagas', 'jenisKegiatan'));
    }

    /**
     * Buat Transaksi Baru (Blanko Event)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_event'        => 'required|string|max:200',
            'id_jenis_kegiatan' => 'required|exists:master_jenis_kegiatan,id_jenis_kegiatan',
            'bulan'             => 'required',
            'tahun'             => 'required',
        ]);

        try {
            $transaksi = KepanitiaanTransaksi::create([
                'nama_event'        => $request->nama_event,
                'id_jenis_kegiatan' => $request->id_jenis_kegiatan,
                'id_lembaga'        => $request->id_lembaga ?: null,
                'bulan'             => $request->bulan,
                'tahun'             => $request->tahun,
                'status'            => 'draft',
                'jumlah_total'      => 0,
                'id_petugas_input'  => Auth::id() ?: 'admin',
                'tgl_input'         => now(),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Event kepanitiaan berhasil dibuat.',
                'id'      => $transaksi->id_kepanitiaan_transaksi,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update Transaksi Event Kepanitiaan
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_event'        => 'required|string|max:200',
            'id_jenis_kegiatan' => 'required|exists:master_jenis_kegiatan,id_jenis_kegiatan',
            'bulan'             => 'required',
            'tahun'             => 'required',
        ]);

        try {
            $transaksi = KepanitiaanTransaksi::findOrFail($id);
            $transaksi->update([
                'nama_event'        => $request->nama_event,
                'id_jenis_kegiatan' => $request->id_jenis_kegiatan,
                'id_lembaga'        => $request->id_lembaga ?: null,
                'bulan'             => $request->bulan,
                'tahun'             => $request->tahun,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Event kepanitiaan berhasil diperbarui.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Hapus Transaksi Event Kepanitiaan beserta rinciannya
     */
    public function destroy($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $transaksi = KepanitiaanTransaksi::findOrFail($id);
                KepanitiaanDetail::where('id_kepanitiaan_transaksi', $id)->delete();
                $transaksi->delete();

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Event kepanitiaan berhasil dihapus.',
                ]);
            });
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Halaman Rincian / Input Panitia
     */
    public function rincian($id)
    {
        $transaksi = KepanitiaanTransaksi::with(['jenisKegiatan', 'lembaga', 'details'])->findOrFail($id);

        // Ambil master tarif khusus jenis kegiatan ini
        $tarifs = BarokahKepanitiaan::where('id_jenis_kegiatan', $transaksi->id_jenis_kegiatan)
            ->get();

        // Kelompokkan tarif per kolom kategori untuk memudahkan JavaScript
        $tarifMap = [];
        foreach ($tarifs as $t) {
            $tarifMap[$t->kolom_kategori][] = [
                'id'        => $t->id_barokah_kepanitiaan,
                'nama'      => $t->nama_komponen,
                'satuan'    => $t->satuan,
                'nominal'   => (float)$t->nominal,
                'keterangan'=> $t->keterangan_aturan,
            ];
        }

        $details = $transaksi->details()->orderBy('id_kepanitiaan_detail', 'asc')->get();

        return view('kepanitiaan.koreksi', compact('transaksi', 'tarifs', 'tarifMap', 'details'));
    }

    public function koreksi($id)
    {
        return $this->rincian($id);
    }

    /**
     * Simpan / Update Baris Rincian Panitia
     */
    public function saveRow(Request $request)
    {
        $request->validate([
            'id_kepanitiaan_transaksi' => 'required|exists:kepanitiaan_transaksi,id_kepanitiaan_transaksi',
            'nama_panitia'             => 'required|string|max:150',
        ]);

        try {
            DB::beginTransaction();

            $id_detail = $request->id_kepanitiaan_detail;
            $cleanNum = fn($val) => (float) str_replace('.', '', (string) ($val ?: 0));

            $data = [
                'id_kepanitiaan_transaksi' => $request->id_kepanitiaan_transaksi,
                'nik'                      => $request->nik ?: null,
                'nama_panitia'             => $request->nama_panitia,
                'lembaga_asal'             => $request->lembaga_asal ?: null,
                
                // 1. Kepanitiaan (Jabatan)
                'jabatan_panitia'          => $request->jabatan_panitia ?: null,
                'nominal_kepanitiaan'      => $cleanNum($request->nominal_kepanitiaan),
                
                // 2. Kehadiran
                'qty_kehadiran'            => (float)($request->qty_kehadiran ?: 0),
                'nominal_kehadiran'        => $cleanNum($request->nominal_kehadiran),
                
                // 3. Konsumsi
                'qty_konsumsi'             => (float)($request->qty_konsumsi ?: 0),
                'nominal_konsumsi'         => $cleanNum($request->nominal_konsumsi),
                
                // 4. Pemb Soal
                'qty_pemb_soal'            => (float)($request->qty_pemb_soal ?: 0),
                'nominal_pemb_soal'        => $cleanNum($request->nominal_pemb_soal),
                
                // 5. Input Soal
                'qty_input_soal'           => (float)($request->qty_input_soal ?: 0),
                'nominal_input_soal'       => $cleanNum($request->nominal_input_soal),
                
                // 6. Pengawas
                'qty_pengawas'             => (float)($request->qty_pengawas ?: 0),
                'nominal_pengawas'         => $cleanNum($request->nominal_pengawas),
                
                // 7. Penguji
                'qty_penguji'              => (float)($request->qty_penguji ?: 0),
                'nominal_penguji'          => $cleanNum($request->nominal_penguji),
                
                // 8. Korektor
                'qty_korektor'             => (float)($request->qty_korektor ?: 0),
                'nominal_korektor'         => $cleanNum($request->nominal_korektor),
                
                // 9. Input Nilai
                'qty_input_nilai'          => (float)($request->qty_input_nilai ?: 0),
                'nominal_input_nilai'      => $cleanNum($request->nominal_input_nilai),
                
                // 10. Pembimbing
                'qty_pembimbing'           => (float)($request->qty_pembimbing ?: 0),
                'nominal_pembimbing'       => $cleanNum($request->nominal_pembimbing),
                
                'potongan'                 => $cleanNum($request->potongan),
                'catatan'                  => $request->catatan ?: null,
            ];

            // Hitung total bruto
            $totalBruto = $data['nominal_kepanitiaan']
                + $data['nominal_kehadiran']
                + $data['nominal_konsumsi']
                + $data['nominal_pemb_soal']
                + $data['nominal_input_soal']
                + $data['nominal_pengawas']
                + $data['nominal_penguji']
                + $data['nominal_korektor']
                + $data['nominal_input_nilai']
                + $data['nominal_pembimbing'];

            $data['total_bruto']    = $totalBruto;
            $data['total_diterima'] = max(0, $totalBruto - $data['potongan']);

            if ($id_detail) {
                $detail = KepanitiaanDetail::findOrFail($id_detail);
                $detail->update($data);
            } else {
                $detail = KepanitiaanDetail::create($data);
            }

            // Update total di Header Transaksi
            $transaksi = KepanitiaanTransaksi::findOrFail($request->id_kepanitiaan_transaksi);
            $grandTotal = KepanitiaanDetail::where('id_kepanitiaan_transaksi', $transaksi->id_kepanitiaan_transaksi)->sum('total_diterima');
            $transaksi->update(['jumlah_total' => $grandTotal]);

            DB::commit();

            return response()->json([
                'status'     => 'success',
                'message'    => 'Data baris panitia berhasil disimpan.',
                'row'        => $detail,
                'grand_total'=> $grandTotal,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Hapus Baris Panitia
     */
    public function deleteRow($id)
    {
        try {
            DB::beginTransaction();

            $detail = KepanitiaanDetail::findOrFail($id);
            $idTransaksi = $detail->id_kepanitiaan_transaksi;
            $detail->delete();

            $transaksi = KepanitiaanTransaksi::findOrFail($idTransaksi);
            $grandTotal = KepanitiaanDetail::where('id_kepanitiaan_transaksi', $idTransaksi)->sum('total_diterima');
            $transaksi->update(['jumlah_total' => $grandTotal]);

            DB::commit();

            return response()->json([
                'status'     => 'success',
                'message'    => 'Baris panitia berhasil dihapus.',
                'grand_total'=> $grandTotal,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Cari Data Pegawai / Umana (AJAX Auto-complete)
     */
    public function searchUmana(Request $request)
    {
        $term = trim($request->get('q', ''));
        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $today = now()->toDateString();
        $words = array_values(array_filter(preg_split('/\s+/', $term)));

        $activePenempatanScope = function ($q) use ($today) {
            $q->where('status', 'Aktif')
              ->whereNotNull('id_lembaga')
              ->where('id_lembaga', '!=', '')
              ->where('id_lembaga', '!=', 0)
              ->whereNotNull('id_ketentuan')
              ->whereNotIn('id_ketentuan', [0, 97, 103, 105])
              ->where(function ($sub) use ($today) {
                  $sub->whereNull('tgl_mulai')
                      ->orWhere('tgl_mulai', '0000-00-00')
                      ->orWhere('tgl_mulai', '<=', $today);
              })
              ->where(function ($sub) use ($today) {
                  $sub->whereNull('tgl_selesai')
                      ->orWhere('tgl_selesai', '0000-00-00')
                      ->orWhere('tgl_selesai', '>=', $today);
              })
              ->whereHas('ketentuan', function ($qk) {
                  $qk->where('nama_jabatan', 'not like', '%Berhenti%')
                     ->where('nama_jabatan', 'not like', '%Kosong%');
              })
              ->whereHas('lembaga');
        };

        $activePengajarScope = function ($q) use ($today) {
            $q->where('status', 'Aktif')
              ->whereNotNull('id_lembaga')
              ->where('id_lembaga', '!=', '')
              ->where('id_lembaga', '!=', 0)
              ->whereNotNull('kategori')
              ->where('kategori', '!=', '')
              ->where(function ($sub) use ($today) {
                  $sub->whereNull('tgl_mulai')
                      ->orWhere('tgl_mulai', '0000-00-00')
                      ->orWhere('tgl_mulai', '<=', $today);
              })
              ->where(function ($sub) use ($today) {
                  $sub->whereNull('tgl_selesai')
                      ->orWhere('tgl_selesai', '0000-00-00')
                      ->orWhere('tgl_selesai', '>=', $today);
              })
              ->whereHas('lembaga');
        };

        $query = Umana::where('status_aktif', 'Aktif')
            ->where(function ($q) use ($activePenempatanScope, $activePengajarScope) {
                $q->whereHas('penempatans', $activePenempatanScope)
                  ->orWhereHas('pengajars', $activePengajarScope);
            })
            ->with([
                'penempatans' => function ($q) use ($activePenempatanScope) {
                    $activePenempatanScope($q);
                    $q->orderByRaw('CASE WHEN id_lembaga IN (47, 48) THEN 1 ELSE 0 END ASC')
                      ->orderBy('id_penempatan', 'desc')
                      ->with(['lembaga', 'ketentuan']);
                },
                'pengajars' => function ($q) use ($activePengajarScope) {
                    $activePengajarScope($q);
                    $q->orderByRaw('CASE WHEN id_lembaga IN (47, 48) THEN 1 ELSE 0 END ASC')
                      ->orderBy('id_pengajar', 'desc')
                      ->with(['lembaga']);
                },
            ]);

        foreach ($words as $word) {
            $query->where(function ($q) use ($word, $activePenempatanScope, $activePengajarScope) {
                $q->where('nama_lengkap', 'like', "%{$word}%")
                  ->orWhere('niy', 'like', "%{$word}%")
                  ->orWhereHas('penempatans', function ($qp) use ($word, $activePenempatanScope) {
                      $activePenempatanScope($qp);
                      $qp->where(function ($sub) use ($word) {
                          $sub->whereHas('lembaga', fn($ql) => $ql->where('nama_lembaga', 'like', "%{$word}%"))
                              ->orWhereHas('ketentuan', fn($qk) => $qk->where('nama_jabatan', 'like', "%{$word}%"));
                      });
                  })
                  ->orWhereHas('pengajars', function ($qpg) use ($word, $activePengajarScope) {
                      $activePengajarScope($qpg);
                      $qpg->whereHas('lembaga', fn($qpl) => $qpl->where('nama_lembaga', 'like', "%{$word}%"));
                  });
            });
        }

        $results = $query->limit(25)
            ->get()
            ->map(function ($u) {
                $unitItems = [];
                $lembagaDefault = '';

                // Daftar lembaga non-Situbondo yang sudah dimiliki pada penempatan struktural
                $mainStrukturalLembagas = $u->penempatans
                    ->filter(fn($p) => !in_array((int) $p->id_lembaga, [47, 48]))
                    ->map(fn($p) => trim($p->lembaga->nama_lembaga ?? ''))
                    ->filter()
                    ->values()
                    ->all();

                foreach ($u->penempatans as $p) {
                    $jab = trim($p->ketentuan->nama_jabatan ?? '');
                    $lem = trim($p->lembaga->nama_lembaga ?? '');
                    if (!$jab && !$lem) {
                        continue;
                    }

                    // Jika ini kelas Situbondo dan yang bersangkutan sudah aktif di fakultas induknya, lewati duplikasinya
                    if (in_array((int) $p->id_lembaga, [47, 48])) {
                        $indukName = trim(str_ireplace('Situbondo', '', $lem));
                        if (in_array($indukName, $mainStrukturalLembagas)) {
                            continue;
                        }
                    }

                    if (!$lembagaDefault && $lem) {
                        $lembagaDefault = $lem;
                    }
                    $unitItems[] = ($jab && $lem) ? "{$jab} - {$lem}" : ($lem ?: $jab);
                }

                // Daftar lembaga non-Situbondo yang sudah dimiliki pada pengajar
                $mainPengajarLembagas = $u->pengajars
                    ->filter(fn($pg) => !in_array((int) $pg->id_lembaga, [47, 48]))
                    ->map(fn($pg) => trim($pg->lembaga->nama_lembaga ?? ''))
                    ->filter()
                    ->values()
                    ->all();

                foreach ($u->pengajars as $pg) {
                    $kat = trim($pg->kategori ?: 'Pengajar');
                    $lem = trim($pg->lembaga->nama_lembaga ?? '');
                    if (!$lem) {
                        continue;
                    }

                    if (in_array((int) $pg->id_lembaga, [47, 48])) {
                        $indukName = trim(str_ireplace('Situbondo', '', $lem));
                        if (in_array($indukName, $mainPengajarLembagas)) {
                            continue;
                        }
                    }

                    if (!$lembagaDefault) {
                        $lembagaDefault = $lem;
                    }
                    $unitItems[] = "{$kat} - {$lem}";
                }

                $unitItems = array_values(array_unique($unitItems));
                $infoJabatan = count($unitItems) > 0 ? implode(' • ', $unitItems) : '';

                return [
                    'nik'             => $u->nik,
                    'nama_lengkap'    => $u->nama_lengkap,
                    'nama_gelar'      => $u->nama_gelar,
                    'info_jabatan'    => $infoJabatan,
                    'lembaga_default' => $lembagaDefault,
                ];
            });

        return response()->json($results);
    }

    /**
     * Kirim Pengajuan ke Pimpinan
     */
    public function kirim($id)
    {
        $transaksi = KepanitiaanTransaksi::findOrFail($id);
        $transaksi->update([
            'status' => 'pengajuan'
        ]);

        return response()->json(['status' => 'success', 'message' => 'Pengajuan kepanitiaan berhasil dikirim.']);
    }

    /**
     * Validasi Pengajuan (Setujui / Tolak)
     */
    public function validasi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
        ]);

        $transaksi = KepanitiaanTransaksi::findOrFail($id);
        $transaksi->update([
            'status'           => $request->status,
            'catatan_pimpinan' => $request->catatan_pimpinan ?: null,
            'tgl_validasi'     => now(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Status transaksi kepanitiaan berhasil diperbarui.']);
    }

    /**
     * Reset Status ke Draft
     */
    public function reset($id)
    {
        $transaksi = KepanitiaanTransaksi::findOrFail($id);
        $transaksi->update([
            'status' => 'draft',
        ]);

        return response()->json(['status' => 'success', 'message' => 'Status berhasil direset ke Draft.']);
    }

    /**
     * Cetak Amprah Kepanitiaan (Format PDF / Print Layout)
     */
    public function cetak($id)
    {
        $transaksi = KepanitiaanTransaksi::with(['jenisKegiatan', 'lembaga', 'details'])->findOrFail($id);
        $details = $transaksi->details()->orderBy('id_kepanitiaan_detail', 'asc')->get();

        return view('kepanitiaan.cetak', compact('transaksi', 'details'));
    }
}
