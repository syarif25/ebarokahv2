<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanWaktuPengajuanController extends Controller
{
    public function index()
    {
        $bulanList = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        // Ambil daftar tahun dari data kehadiran_lembaga
        $tahunList = DB::table('kehadiran_lembaga')
            ->select('tahun')
            ->whereNotNull('tahun')
            ->where('tahun', '!=', '')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        return view('laporan.waktu-pengajuan.index', compact('bulanList', 'tahunList'));
    }

    public function data(Request $request)
    {
        $filterBulan = $request->input('filter_bulan');
        $filterTahun = $request->input('filter_tahun');

        $query = DB::table('kehadiran_lembaga as kl')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'kl.id_lembaga')
            ->select(
                'kl.id_kehadiran_lembaga',
                'l.nama_lembaga',
                'kl.kategori',
                'kl.bulan',
                'kl.tahun',
                DB::raw("COALESCE(
                    (SELECT waktu_eksekusi FROM log_riwayat_barokah WHERE id_kehadiran_lembaga = kl.id_kehadiran_lembaga AND status_aksi IN ('Terkirim', 'Sudah', 'Kirim', 'Buat Blanko') ORDER BY id_log ASC LIMIT 1),
                    kl.tgl_input
                ) as waktu_upload"),
                DB::raw("(SELECT waktu_eksekusi FROM log_riwayat_barokah WHERE id_kehadiran_lembaga = kl.id_kehadiran_lembaga AND LOWER(status_aksi) = 'acc' ORDER BY id_log DESC LIMIT 1) as waktu_acc"),
                DB::raw("COALESCE(
                    (SELECT waktu_eksekusi FROM log_riwayat_barokah WHERE id_kehadiran_lembaga = kl.id_kehadiran_lembaga AND LOWER(status_aksi) IN ('selesai', 'transfer', 'sudah transfer') ORDER BY id_log DESC LIMIT 1),
                    (SELECT created_at FROM wa_log WHERE id_kehadiran_lembaga = kl.id_kehadiran_lembaga ORDER BY id_wa_log DESC LIMIT 1)
                ) as waktu_transfer")
            );

        if (!empty($filterBulan)) {
            $query->where('kl.bulan', $filterBulan);
        }

        if (!empty($filterTahun)) {
            $query->where('kl.tahun', $filterTahun);
        }

        // Search global DataTables
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('l.nama_lembaga', 'like', "%{$search}%")
                  ->orWhere('kl.kategori', 'like', "%{$search}%")
                  ->orWhere('kl.bulan', 'like', "%{$search}%")
                  ->orWhere('kl.tahun', 'like', "%{$search}%");
            });
        }

        $totalRecords = DB::table('kehadiran_lembaga')->count();
        $filteredRecords = (clone $query)->count();

        // Ordering
        $orderColIndex = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'desc');
        $columns = [
            0 => 'kl.id_kehadiran_lembaga',
            1 => 'l.nama_lembaga',
            2 => 'kl.kategori',
            3 => 'kl.bulan',
            4 => 'waktu_upload',
            5 => 'waktu_acc',
            6 => 'waktu_transfer',
            7 => 'kl.id_kehadiran_lembaga',
        ];

        if (isset($columns[$orderColIndex])) {
            $query->orderBy($columns[$orderColIndex], $orderDir);
        } else {
            $query->orderBy('kl.id_kehadiran_lembaga', 'desc');
        }

        // Pagination
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $list = $query->get();

        $data = [];
        $no = $start + 1;
        foreach ($list as $item) {
            $wupload = $item->waktu_upload
                ? '<span class="badge badge-waktu-upload">' . e($item->waktu_upload) . '</span>'
                : '<span class="text-muted fw-bold">-</span>';

            $wacc = $item->waktu_acc
                ? '<span class="badge badge-waktu-acc">' . e($item->waktu_acc) . '</span>'
                : '<span class="text-muted fw-bold">-</span>';

            $wtransfer = $item->waktu_transfer
                ? '<span class="badge badge-waktu-transfer">' . e($item->waktu_transfer) . '</span>'
                : '<span class="text-muted fw-bold">-</span>';

            // Hitung durasi jika ada waktu upload dan transfer
            $durasi = '<span class="text-muted fw-bold">-</span>';
            if ($item->waktu_upload && $item->waktu_transfer) {
                $t1 = strtotime($item->waktu_upload);
                $t2 = strtotime($item->waktu_transfer);
                $diff = $t2 - $t1;

                if ($diff >= 0) {
                    $days = floor($diff / (60 * 60 * 24));
                    $hours = floor(($diff - ($days * 60 * 60 * 24)) / (60 * 60));
                    $minutes = floor(($diff - ($days * 60 * 60 * 24) - ($hours * 60 * 60)) / 60);

                    if ($days > 0) {
                        $durasi = '<span class="badge badge-durasi-days"><i class="far fa-clock me-1"></i>' . $days . ' Hari ' . $hours . ' Jam</span>';
                    } elseif ($hours > 0) {
                        $durasi = '<span class="badge badge-durasi-hours"><i class="far fa-clock me-1"></i>' . $hours . ' Jam ' . $minutes . ' Menit</span>';
                    } else {
                        $durasi = '<span class="badge badge-durasi-minutes"><i class="far fa-clock me-1"></i>' . $minutes . ' Menit</span>';
                    }
                }
            }

            // Badge kategori dengan warna tegas & kontras
            $katBadgeClass = 'badge badge-kategori-default';
            if ($item->kategori === 'Struktural') {
                $katBadgeClass = 'badge badge-kategori-struktural';
            } elseif ($item->kategori === 'Pengajar') {
                $katBadgeClass = 'badge badge-kategori-pengajar';
            } elseif ($item->kategori === 'Satpam') {
                $katBadgeClass = 'badge badge-kategori-satpam';
            }

            $namaLembaga = $item->nama_lembaga ?: ($item->kategori === 'Satpam' ? 'Satpam' : '-');
            $bulanTahun = trim(($item->bulan ?? '') . ' ' . ($item->tahun ?? ''));
            if ($item->bulan && $item->tahun) {
                $bulanTahun = $item->bulan . ' - ' . $item->tahun;
            }

            $data[] = [
                'no' => $no++,
                'nama_lembaga' => '<span class="fw-bold text-dark">' . e($namaLembaga) . '</span>',
                'kategori' => '<span class="' . $katBadgeClass . '">' . e($item->kategori) . '</span>',
                'bulan_tahun' => '<span class="fw-bold text-dark">' . e($bulanTahun ?: '-') . '</span>',
                'periode' => '<span class="fw-bold text-dark">' . e($bulanTahun ?: '-') . '</span>',
                'waktu_upload' => $wupload,
                'waktu_acc' => $wacc,
                'waktu_transfer' => $wtransfer,
                'durasi' => $durasi,
            ];
        }

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $filterBulan = $request->query('bulan');
        $filterTahun = $request->query('tahun');

        $query = DB::table('kehadiran_lembaga as kl')
            ->leftJoin('lembaga as l', 'l.id_lembaga', '=', 'kl.id_lembaga')
            ->select(
                'kl.id_kehadiran_lembaga',
                'l.nama_lembaga',
                'kl.kategori',
                'kl.bulan',
                'kl.tahun',
                DB::raw("COALESCE(
                    (SELECT waktu_eksekusi FROM log_riwayat_barokah WHERE id_kehadiran_lembaga = kl.id_kehadiran_lembaga AND status_aksi IN ('Terkirim', 'Sudah', 'Kirim', 'Buat Blanko') ORDER BY id_log ASC LIMIT 1),
                    kl.tgl_input
                ) as waktu_upload"),
                DB::raw("(SELECT waktu_eksekusi FROM log_riwayat_barokah WHERE id_kehadiran_lembaga = kl.id_kehadiran_lembaga AND LOWER(status_aksi) = 'acc' ORDER BY id_log DESC LIMIT 1) as waktu_acc"),
                DB::raw("COALESCE(
                    (SELECT waktu_eksekusi FROM log_riwayat_barokah WHERE id_kehadiran_lembaga = kl.id_kehadiran_lembaga AND LOWER(status_aksi) IN ('selesai', 'transfer', 'sudah transfer') ORDER BY id_log DESC LIMIT 1),
                    (SELECT created_at FROM wa_log WHERE id_kehadiran_lembaga = kl.id_kehadiran_lembaga ORDER BY id_wa_log DESC LIMIT 1)
                ) as waktu_transfer")
            );

        if (!empty($filterBulan)) {
            $query->where('kl.bulan', $filterBulan);
        }

        if (!empty($filterTahun)) {
            $query->where('kl.tahun', $filterTahun);
        }

        $list = $query->orderBy('kl.id_kehadiran_lembaga', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Waktu Pengajuan');

        // Judul Laporan
        $sheet->setCellValue('A1', 'LAPORAN DURASI WAKTU PENGAJUAN BAROKAH');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $periodeInfo = 'Periode: ' . ($filterBulan ?: 'Semua Bulan') . ' ' . ($filterTahun ?: 'Semua Tahun');
        $sheet->setCellValue('A2', $periodeInfo);
        $sheet->mergeCells('A2:H2');
        $sheet->getStyle('A2')->getFont()->setSize(11)->setItalic(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header Tabel
        $headers = [
            'A4' => 'No',
            'B4' => 'Nama Lembaga',
            'C4' => 'Kategori',
            'D4' => 'Bulan - Tahun',
            'E4' => 'Waktu Upload',
            'F4' => 'Waktu ACC',
            'G4' => 'Waktu Transfer',
            'H4' => 'Durasi Total',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $sheet->getStyle('A4:H4')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE));
        $sheet->getStyle('A4:H4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF00A15D');
        $sheet->getStyle('A4:H4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

        $rowNum = 5;
        $no = 1;
        foreach ($list as $item) {
            $durasiText = '-';
            if ($item->waktu_upload && $item->waktu_transfer) {
                $t1 = strtotime($item->waktu_upload);
                $t2 = strtotime($item->waktu_transfer);
                $diff = $t2 - $t1;
                if ($diff >= 0) {
                    $days = floor($diff / (60 * 60 * 24));
                    $hours = floor(($diff - ($days * 60 * 60 * 24)) / (60 * 60));
                    $minutes = floor(($diff - ($days * 60 * 60 * 24) - ($hours * 60 * 60)) / 60);

                    if ($days > 0) {
                        $durasiText = $days . ' Hari ' . $hours . ' Jam';
                    } elseif ($hours > 0) {
                        $durasiText = $hours . ' Jam ' . $minutes . ' Menit';
                    } else {
                        $durasiText = $minutes . ' Menit';
                    }
                }
            }

            $namaLembaga = $item->nama_lembaga ?: ($item->kategori === 'Satpam' ? 'Satpam' : '-');

            $sheet->setCellValue('A' . $rowNum, $no++);
            $sheet->setCellValue('B' . $rowNum, $namaLembaga);
            $sheet->setCellValue('C' . $rowNum, $item->kategori);
            $sheet->setCellValue('D' . $rowNum, $item->bulan . ' - ' . $item->tahun);
            $sheet->setCellValue('E' . $rowNum, $item->waktu_upload ?: '-');
            $sheet->setCellValue('F' . $rowNum, $item->waktu_acc ?: '-');
            $sheet->setCellValue('G' . $rowNum, $item->waktu_transfer ?: '-');
            $sheet->setCellValue('H' . $rowNum, $durasiText);

            $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $rowNum++;
        }

        // Border
        $lastRow = $rowNum - 1;
        if ($lastRow >= 4) {
            $sheet->getStyle('A4:H' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        // Auto width
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Laporan_Waktu_Pengajuan_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
