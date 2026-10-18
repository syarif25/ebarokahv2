<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KehadiranLembaga;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PayrollController extends Controller
{
    /**
     * Tampilan utama Menu PayRoll
     */
    public function index()
    {
        $user = Auth::user();
        if ($user && in_array($user->jabatan, ['AdminLembaga', 'umana'])) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengakses menu PayRoll.');
        }

        return view('payroll.index');
    }

    /**
     * Endpoint DataTables JSON untuk tabel Daftar Barokah Perlembaga
     */
    public function dataList(Request $request)
    {
        // Ambil data kehadiran lembaga yang siap ditransfer atau sudah ditransfer
        $query = KehadiranLembaga::with('lembaga')
            ->whereNotIn('status', ['Belum', 'Sudah', 'Terkirim']);

        $list = $query->orderBy('id_kehadiran_lembaga', 'desc')->get();

        // Hitung agregat log WA per id_kehadiran_lembaga
        $waCounts = DB::table('wa_log')
            ->select(
                'id_kehadiran_lembaga',
                DB::raw('SUM(CASE WHEN status = "success" THEN 1 ELSE 0 END) as success_count'),
                DB::raw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_count'),
                DB::raw('SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed_count')
            )
            ->groupBy('id_kehadiran_lembaga')
            ->get()
            ->keyBy('id_kehadiran_lembaga');

        $data = [];
        $no = 1;

        foreach ($list as $item) {
            $id = $item->id_kehadiran_lembaga;
            $namaLembaga = $item->lembaga->nama_lembaga ?? '-';
            $kategori = $item->kategori ?? 'Struktural';
            $periode = ($item->bulan ?? '') . ' ' . ($item->tahun ?? '');
            $jumlahNominal = number_format((float)$item->jumlah_total, 0, ',', '.');

            $wa = $waCounts->get($id);
            $badgeWa = '';
            if ($wa) {
                if ($wa->failed_count > 0) {
                    $badgeWa .= '<span class="badge" style="background-color: #fee2e2; color: #dc2626; border-radius: 12px; padding: 4px 10px; font-weight: 700; margin-right: 4px;" title="Gagal Terkirim">&times; ' . $wa->failed_count . '</span>';
                }
                if ($wa->success_count > 0) {
                    $badgeWa .= '<span class="badge" style="background-color: #dcfce7; color: #16a34a; border-radius: 12px; padding: 4px 10px; font-weight: 700; margin-right: 4px;" title="Berhasil Terkirim"><i class="fa fa-check me-1"></i> ' . $wa->success_count . '</span>';
                }
                if ($wa->pending_count > 0) {
                    $badgeWa .= '<span class="badge" style="background-color: #fef3c7; color: #d97706; border-radius: 12px; padding: 4px 10px; font-weight: 700;" title="Menunggu"><i class="fa fa-clock me-1"></i> ' . $wa->pending_count . '</span>';
                }
            }
            if (empty($badgeWa)) {
                $badgeWa = '<span class="text-muted">-</span>';
            }

            // Status transfer & Tombol Aksi
            if ($item->status === 'selesai') {
                $statusHtml = '<span style="color: #16a34a; font-weight: 600;">Sudah ditransfer</span><br>'
                    . '<button type="button" class="btn btn-xs text-white btn-log-wa" style="background-color: #d848c9; border-radius: 14px; font-size: 11px; padding: 2px 10px; margin-top: 4px;" data-id="' . $id . '" data-bulan="' . e($item->bulan) . '" data-tahun="' . e($item->tahun) . '">'
                    . '<i class="fab fa-whatsapp me-1"></i> Log WA</button>';

                $aksiHtml = '<button type="button" class="btn btn-sm text-white btn-rincian" data-id="' . $id . '" data-mode="selesai" style="background-color: #00a15d; border-radius: 20px; padding: 6px 16px; font-weight: 500;">'
                    . '<i class="fas fa-eye me-1"></i> Lihat Rincian</button>';
            } else {
                $statusHtml = '<span style="color: #dc2626; font-weight: 600;">Belum ditransfer</span>';

                $aksiHtml = '<button type="button" class="btn btn-sm btn-outline-success btn-rincian" data-id="' . $id . '" data-mode="transfer" style="border-radius: 20px; padding: 6px 16px; font-weight: 500;">'
                    . '<i class="fas fa-check me-1"></i> Sudah Transfer</button>';
            }

            $data[] = [
                'no' => $no++,
                'id_kehadiran_lembaga' => $id,
                'nama_lembaga' => $namaLembaga,
                'kategori' => $kategori,
                'periode' => $periode,
                'jumlah_nominal' => $jumlahNominal,
                'status_wa' => $badgeWa,
                'status_html' => $statusHtml,
                'aksi_html' => $aksiHtml,
                'status' => $item->status,
            ];
        }

        return response()->json(['data' => $data]);
    }

    /**
     * Ambil detail rincian penerima transfer per lembaga
     */
    public function getById($id)
    {
        $lembaga = DB::table('kehadiran_lembaga')
            ->join('lembaga', 'kehadiran_lembaga.id_lembaga', '=', 'lembaga.id_lembaga')
            ->where('kehadiran_lembaga.id_kehadiran_lembaga', $id)
            ->select('kehadiran_lembaga.*', 'lembaga.nama_lembaga')
            ->first();

        if (!$lembaga) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        $kategori = $lembaga->kategori ?? 'Struktural';
        $rows = [];

        if ($kategori === 'Struktural') {
            $rows = DB::table('umana')
                ->join('penempatan', 'umana.nik', '=', 'penempatan.nik')
                ->join('total_barokah', 'total_barokah.id_penempatan', '=', 'penempatan.id_penempatan')
                ->join('kehadiran_lembaga', 'kehadiran_lembaga.id_lembaga', '=', 'penempatan.id_lembaga')
                ->join('lembaga', 'lembaga.id_lembaga', '=', 'kehadiran_lembaga.id_lembaga')
                ->where('kehadiran_lembaga.id_kehadiran_lembaga', $id)
                ->where('total_barokah.id_kehadiran', $id)
                ->select(
                    'total_barokah.id_total_barokah as id_total',
                    'kehadiran_lembaga.id_kehadiran_lembaga',
                    'kehadiran_lembaga.kategori',
                    'umana.nama_lengkap',
                    'umana.gelar_depan',
                    'umana.gelar_belakang',
                    'umana.nama_bank',
                    'umana.nomor_hp',
                    'umana.no_rekening',
                    'umana.atas_nama',
                    'lembaga.nama_lembaga',
                    'total_barokah.bulan',
                    'total_barokah.tahun',
                    'total_barokah.diterima'
                )
                ->orderBy('umana.nama_bank', 'asc')
                ->get();
        } elseif ($kategori === 'Satpam') {
            $rows = DB::table('umana')
                ->join('satpam', 'umana.nik', '=', 'satpam.nik')
                ->join('total_barokah_satpam', 'total_barokah_satpam.id_satpam', '=', 'satpam.id_satpam')
                ->join('kehadiran_lembaga', 'kehadiran_lembaga.id_kehadiran_lembaga', '=', 'total_barokah_satpam.id_kehadiran_lembaga')
                ->join('lembaga', 'lembaga.id_lembaga', '=', 'kehadiran_lembaga.id_lembaga')
                ->where('kehadiran_lembaga.id_kehadiran_lembaga', $id)
                ->where('total_barokah_satpam.id_kehadiran_lembaga', $id)
                ->select(
                    'total_barokah_satpam.id_total_barokah_satpam as id_total',
                    'kehadiran_lembaga.id_kehadiran_lembaga',
                    'kehadiran_lembaga.kategori',
                    'umana.nama_lengkap',
                    'umana.gelar_depan',
                    'umana.gelar_belakang',
                    'umana.nama_bank',
                    'umana.nomor_hp',
                    'umana.no_rekening',
                    'umana.atas_nama',
                    'lembaga.nama_lembaga',
                    'total_barokah_satpam.bulan',
                    'total_barokah_satpam.tahun',
                    'total_barokah_satpam.diterima'
                )
                ->orderBy('umana.nama_bank', 'asc')
                ->get();
        } else {
            $rows = DB::table('umana')
                ->join('pengajar', 'umana.nik', '=', 'pengajar.nik')
                ->join('total_barokah_pengajar', 'total_barokah_pengajar.id_pengajar', '=', 'pengajar.id_pengajar')
                ->join('kehadiran_lembaga', 'kehadiran_lembaga.id_lembaga', '=', 'pengajar.id_lembaga')
                ->join('lembaga', 'lembaga.id_lembaga', '=', 'kehadiran_lembaga.id_lembaga')
                ->where('kehadiran_lembaga.id_kehadiran_lembaga', $id)
                ->where('total_barokah_pengajar.id_kehadiran_lembaga', $id)
                ->select(
                    'total_barokah_pengajar.id_total_barokah_pengajar as id_total',
                    'kehadiran_lembaga.id_kehadiran_lembaga',
                    'kehadiran_lembaga.kategori',
                    'umana.nama_lengkap',
                    'umana.gelar_depan',
                    'umana.gelar_belakang',
                    'umana.nama_bank',
                    'umana.nomor_hp',
                    'umana.no_rekening',
                    'umana.atas_nama',
                    'lembaga.nama_lembaga',
                    'total_barokah_pengajar.bulan',
                    'total_barokah_pengajar.tahun',
                    'total_barokah_pengajar.diterima'
                )
                ->orderBy('umana.nama_bank', 'asc')
                ->get();
        }

        // Ambil data bank dari master data bank di database
        $masterBanks = DB::table('bank')->get()->keyBy(function($item) {
            return strtolower(trim($item->nama_bank));
        });
        $defaultBank = DB::table('bank')->where('is_default', 1)->first();
        $defaultAdminFee = $defaultBank ? (int)$defaultBank->biaya_admin : 1000;
        $defaultBiFastCode = $defaultBank ? $defaultBank->bifast_code : 'BRINIDJA';
        $defaultType = $defaultBank ? $defaultBank->tipe_transfer : 'IFT';

        $htmlItem = '';
        $no = 1;

        foreach ($rows as $sow) {
            $nominal = (int) $sow->diterima;
            $bankKey = strtolower(trim($sow->nama_bank ?? ''));
            $bankInfo = $masterBanks->get($bankKey);

            if ($bankInfo) {
                $adminFee = (int) $bankInfo->biaya_admin;
                $biFastCode = $bankInfo->bifast_code;
                $type = $bankInfo->tipe_transfer;
            } else {
                $adminFee = $defaultAdminFee;
                $biFastCode = $defaultBiFastCode;
                $type = $defaultType;
            }

            $ref = ($type === 'IFT' ? 'REFF-IFT' : 'REFF-BIF') . $sow->id_total;
            $netAmount = max(0, $nominal - $adminFee);
            $namaFormatted = trim(($sow->gelar_depan ? $sow->gelar_depan . ' ' : '') . $sow->nama_lengkap . ($sow->gelar_belakang ? ', ' . $sow->gelar_belakang : ''));

            $dataItems[] = [
                'nama_lengkap' => $namaFormatted,
                'nama_lembaga' => $sow->nama_lembaga,
                'nama_bank' => $sow->nama_bank,
                'norek' => $sow->no_rekening,
                'diterima' => $nominal,
                'nomor_hp' => $sow->nomor_hp,
                'kategori' => $sow->kategori,
                'bulan' => $sow->bulan,
                'tahun' => $sow->tahun,
                'id_total' => $sow->id_total,
                'id_kehadiran_lembaga_log' => $sow->id_kehadiran_lembaga,
                'periode' => $sow->bulan . ' ' . $sow->tahun,
                'id_kehadiran_lembaga' => $id,
            ];

            $htmlItem .= '<tr>';
            $htmlItem .= '<td class="text-center font-weight-bold">' . $no++ . '</td>';
            $htmlItem .= '<td class="font-weight-bold">' . e($namaFormatted) . '</td>';
            $htmlItem .= '<td>' . e($sow->nama_bank) . '</td>';
            $htmlItem .= '<td class="font-weight-bold">' . e($sow->no_rekening) . '</td>';
            $htmlItem .= '<td>' . e($sow->atas_nama ?: $namaFormatted) . '</td>';
            $htmlItem .= '<td class="font-weight-bold">' . $nominal . '</td>';
            $htmlItem .= '<td class="font-weight-bold">' . $netAmount . '</td>';
            $htmlItem .= '<td class="font-weight-bold">' . e($biFastCode) . '</td>';
            $htmlItem .= '<td class="font-weight-bold">' . e($ref) . '</td>';
            $htmlItem .= '<td>' . e($type) . '</td>';
            $htmlItem .= '<td>' . e($sow->nama_lembaga . ' - ' . $sow->kategori) . '
                <input type="hidden" name="id_total[]" value="' . $sow->id_total . '">
                <input type="hidden" name="id_kehadiran_lembaga_log[]" value="' . $sow->id_kehadiran_lembaga . '">
                <input type="hidden" name="kategori[]" value="' . $sow->kategori . '">
                <input type="hidden" name="bulan[]" value="' . $sow->bulan . '">
                <input type="hidden" name="tahun[]" value="' . $sow->tahun . '">
                <input type="hidden" name="nama_lengkap[]" value="' . e($namaFormatted) . '">
                <input type="hidden" name="nama_lembaga[]" value="' . e($sow->nama_lembaga) . '">
                <input type="hidden" name="nama_bank[]" value="' . e($sow->nama_bank) . '">
                <input type="hidden" name="norek[]" value="' . e($sow->no_rekening) . '">
                <input type="hidden" name="nomor_hp[]" value="' . e($sow->nomor_hp) . '">
                <input type="hidden" name="diterima[]" value="' . $nominal . '">
            </td>';
            $htmlItem .= '</tr>';
        }

        return response()->json([
            'data_elemen' => [
                'id_kehadiran_lembaga' => $lembaga->id_kehadiran_lembaga,
                'nama_lembaga' => $lembaga->nama_lembaga,
                'bulan' => $lembaga->bulan,
                'tahun' => $lembaga->tahun,
                'periode' => $lembaga->bulan . ' ' . $lembaga->tahun,
                'jumlah_total' => number_format((float)$lembaga->jumlah_total, 0, ',', '.'),
                'jumlah_total_rp' => 'Rp ' . number_format((float)$lembaga->jumlah_total, 0, ',', '.'),
                'jumlah_total_raw' => (int)$lembaga->jumlah_total,
            ],
            'html_item' => $htmlItem,
            'total_rows' => count($rows),
            'data_items' => $dataItems ?? []
        ]);
    }

    /**
     * Proses Simpan Transfer Selesai & Kirim Batch Notifikasi WA
     */
    public function save(Request $request)
    {
        $idKehadiranLembaga = $request->input('id_kehadiran_lembaga');
        $idTotals = $request->input('id_total', []);
        $namaLengkap = $request->input('nama_lengkap', []);
        $namaLembaga = $request->input('nama_lembaga', []);
        $namaBank = $request->input('nama_bank', []);
        $norek = $request->input('norek', []);
        $jumlah = $request->input('diterima', []);
        $nomorHp = $request->input('nomor_hp', []);
        $kategoriList = $request->input('kategori', []);
        $bulanList = $request->input('bulan', []);
        $tahunList = $request->input('tahun', []);
        $periode = $request->input('periode');

        // 1. Update status kehadiran_lembaga menjadi 'selesai'
        DB::table('kehadiran_lembaga')
            ->where('id_kehadiran_lembaga', $idKehadiranLembaga)
            ->update(['status' => 'selesai']);

        // 2. Ambil kategori
        $kl = DB::table('kehadiran_lembaga')->where('id_kehadiran_lembaga', $idKehadiranLembaga)->first();
        $kategori = $kl ? $kl->kategori : 'Struktural';

        // 3. Update status barokah per umana menjadi 'selesai'
        if (!empty($idTotals)) {
            if ($kategori === 'Satpam') {
                DB::table('total_barokah_satpam')->whereIn('id_total_barokah_satpam', $idTotals)->update(['status' => 'selesai']);
            } elseif ($kategori === 'Pengajar') {
                DB::table('total_barokah_pengajar')->whereIn('id_total_barokah_pengajar', $idTotals)->update(['status' => 'selesai']);
            } else {
                DB::table('total_barokah')->whereIn('id_total_barokah', $idTotals)->update(['status' => 'selesai']);
            }
        }

        // 4. Kirim notifikasi WA & catat di wa_log
        $waktu = date('Y-m-d');
        foreach ($namaLengkap as $i => $nama) {
            $hp = isset($nomorHp[$i]) ? $nomorHp[$i] : '';
            $hpClean = preg_replace('/^0/', '62', trim((string)$hp));

            $payload = [
                'title' => 'Transfer Barokah Berhasil',
                'nomor_hp' => $hpClean,
                'periode' => $periode,
                'nama_lengkap' => $nama,
                'lembaga' => $namaLembaga[$i] ?? '',
                'jumlah' => isset($jumlah[$i]) ? number_format((float)$jumlah[$i], 0, ',', '.') : '0',
                'nama_bank' => $namaBank[$i] ?? '',
                'norek' => $norek[$i] ?? '',
                'waktu' => $waktu,
                'bulan' => $bulanList[$i] ?? '',
                'tahun' => $tahunList[$i] ?? '',
                'id_kehadiran_lembaga' => $idKehadiranLembaga,
                'kategori' => $kategoriList[$i] ?? $kategori,
                'id_total_barokah' => $idTotals[$i] ?? null,
            ];

            $this->kirimWa($payload);
        }

        return response()->json(['status' => true, 'message' => 'Transfer berhasil disimpan dan notifikasi WA diproses.']);
    }

    /**
     * Kirim pesan WhatsApp via Gateway Wablas dan catat wa_log
     */
    private function kirimWa($isipesan)
    {
        $pesan = '*' . $isipesan['title'] . "*\n\n"
            . 'Periode : ' . $isipesan['periode'] . "\n"
            . 'Nama Lengkap : ' . $isipesan['nama_lengkap'] . "\n"
            . 'Lembaga : *' . $isipesan['lembaga'] . "*\n"
            . 'Jumlah  : ' . $isipesan['jumlah'] . "\n"
            . 'Bank  : ' . $isipesan['nama_bank'] . "\n"
            . 'No Rek : ' . $isipesan['norek'] . "\n"
            . 'Tanggal Kirim : *' . $isipesan['waktu'] . "*\n\n"
            . "Untuk detail rincian barokah silakan kunjungi ebarokah.p2s3.com\n\n"
            . 'Jazakumullah Khairan';

        $nomorHp = preg_replace('/^0/', '62', trim((string)$isipesan['nomor_hp']));

        // Masukkan draft ke wa_log
        $logId = DB::table('wa_log')->insertGetId([
            'id_total_barokah' => $isipesan['id_total_barokah'],
            'id_kehadiran_lembaga' => $isipesan['id_kehadiran_lembaga'],
            'bulan' => $isipesan['bulan'],
            'tahun' => $isipesan['tahun'],
            'nama_umana' => $isipesan['nama_lengkap'],
            'kategori' => $isipesan['kategori'],
            'nama_lembaga' => $isipesan['lembaga'],
            'nomor_hp' => $nomorHp,
            'nama_bank' => $isipesan['nama_bank'],
            'nomor_rekening' => $isipesan['norek'],
            'nama_penerima' => $isipesan['nama_lengkap'],
            'jumlah' => (int) str_replace('.', '', (string)$isipesan['jumlah']),
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $link = "https://solo.wablas.com/api/send-message";
        $token = "fpi23keYVt27yc4FpVj6crgc199h72PrPARBY0ZZ0NLuqOeROfGPxYdzENOvXQRI.WEKyuXvr";

        try {
            $start = microtime(true);
            $response = Http::withHeaders(['Authorization' => $token])
                ->timeout(10)
                ->asForm()
                ->post($link, [
                    'phone' => $nomorHp,
                    'message' => $pesan,
                ]);
            $duration = round(microtime(true) - $start, 5);

            $resJson = $response->json();
            $status = (isset($resJson['status']) && $resJson['status'] == true) ? 'success' : 'failed';

            DB::table('wa_log')->where('id_wa_log', $logId)->update([
                'status' => $status,
                'response' => $response->body(),
                'http_code' => $response->status(),
                'duration' => $duration,
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            DB::table('wa_log')->where('id_wa_log', $logId)->update([
                'status' => 'failed',
                'response' => $e->getMessage(),
                'curl_error' => $e->getMessage(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Ambil riwayat Log Pengiriman WA
     */
    public function getWaLog(Request $request)
    {
        $idLembaga = $request->input('id_lembaga');

        $logs = DB::table('wa_log')
            ->where('id_kehadiran_lembaga', $idLembaga)
            ->orderBy('id_wa_log', 'desc')
            ->get();

        return response()->json($logs);
    }

    /**
     * Resend 1 pesan WA yang gagal
     */
    public function resendWa($id)
    {
        $log = DB::table('wa_log')->where('id_wa_log', $id)->first();
        if (!$log) {
            return response()->json(['status' => false, 'message' => 'Data log tidak ditemukan.'], 404);
        }

        $payload = [
            'title' => 'Transfer Barokah Berhasil',
            'periode' => $log->bulan . ' ' . $log->tahun,
            'nama_lengkap' => $log->nama_penerima,
            'lembaga' => $log->nama_lembaga,
            'jumlah' => number_format((float)$log->jumlah, 0, ',', '.'),
            'nama_bank' => $log->nama_bank,
            'norek' => $log->nomor_rekening,
            'waktu' => date('Y-m-d'),
            'nomor_hp' => $log->nomor_hp,
            'bulan' => $log->bulan,
            'tahun' => $log->tahun,
            'id_kehadiran_lembaga' => $log->id_kehadiran_lembaga,
            'kategori' => $log->kategori,
            'id_total_barokah' => $log->id_total_barokah,
        ];

        $this->kirimWa($payload);

        return response()->json(['status' => true, 'message' => 'Pesan berhasil dikirim ulang.']);
    }

    /**
     * Resend massal semua pesan WA yang gagal untuk lembaga terkait
     */
    public function bulkResendFailed(Request $request)
    {
        $idLembaga = $request->input('id_lembaga');

        $failedLogs = DB::table('wa_log')
            ->where('id_kehadiran_lembaga', $idLembaga)
            ->where('status', 'failed')
            ->get();

        if ($failedLogs->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Tidak ada pengiriman WA yang gagal.']);
        }

        $successCount = 0;
        $failedCount = 0;

        foreach ($failedLogs as $log) {
            $payload = [
                'title' => 'Transfer Barokah Berhasil',
                'periode' => $log->bulan . ' ' . $log->tahun,
                'nama_lengkap' => $log->nama_penerima,
                'lembaga' => $log->nama_lembaga,
                'jumlah' => number_format((float)$log->jumlah, 0, ',', '.'),
                'nama_bank' => $log->nama_bank,
                'norek' => $log->nomor_rekening,
                'waktu' => date('Y-m-d'),
                'nomor_hp' => $log->nomor_hp,
                'bulan' => $log->bulan,
                'tahun' => $log->tahun,
                'id_kehadiran_lembaga' => $log->id_kehadiran_lembaga,
                'kategori' => $log->kategori,
                'id_total_barokah' => $log->id_total_barokah,
            ];

            $this->kirimWa($payload);
            $successCount++;
        }

        return response()->json([
            'status' => true,
            'message' => "Proses resend selesai: {$successCount} diproses.",
            'success_count' => $successCount,
            'failed_count' => $failedCount
        ]);
    }
}
