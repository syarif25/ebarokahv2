<?php

namespace App\Services;

require_once app_path('Libraries/fpdf/fpdf.php');

use FPDF;

class CustomPdfStruktural extends FPDF
{
    protected $watermarkPath;

    public function __construct($orientation = 'L', $unit = 'mm', $size = 'LEGAL', $watermark = null)
    {
        parent::__construct($orientation, $unit, $size);
        $this->watermarkPath = $watermark ?? public_path('assets/p2s2_watermark.png');
    }

    public function Header()
    {
        // Watermark: Single Centered Logo (opacity ~8% PNG)
        if (file_exists($this->watermarkPath)) {
            $pageW = $this->GetPageWidth();
            $pageH = $this->GetPageHeight();
            $logoW = 100;
            $logoX = ($pageW - $logoW) / 2;
            $logoY = ($pageH - ($logoW * 283 / 753)) / 2;
            $this->Image($this->watermarkPath, $logoX, $logoY, $logoW);
        }

        // Header tabel berulang jika halaman > 1
        if ($this->PageNo() > 1) {
            $this->Cell(1, 7, '', 0, 1);
            $this->SetFont('arial', 'B', 6);

            $this->Cell(6, 7, 'NO', 1, 0, 'C');
            $this->Cell(53, 7, 'NAMA LENGKAP', 1, 0, 'C');
            $this->Cell(32, 7, 'ESELON', 1, 0, 'C');
            $this->Cell(10, 7, 'TMT', 1, 0, 'C');
            $this->Cell(19, 7, 'TUNJAB', 1, 0, 'C');
            $this->Cell(7, 7, 'MP', 1, 0, 'C');
            $this->Cell(19, 7, 'TMP', 1, 0, 'C');

            $this->Cell(6, 7, 'W', 1, 0, 'C');
            $this->Cell(6, 7, 'H', 1, 0, 'C');
            $this->Cell(6, 7, 'T', 1, 0, 'C');
            $this->Cell(5, 7, 'I', 1, 0, 'C');
            $this->Cell(5, 7, 'S', 1, 0, 'C');
            $this->Cell(10, 7, '%', 1, 0, 'C');

            $this->Cell(19, 7, 'Kehadiran', 1, 0, 'C');
            $this->Cell(19, 7, 'Tunkel', 1, 0, 'C');
            $this->Cell(19, 7, 'Tun Anak', 1, 0, 'C');
            $this->Cell(20, 7, 'Kehormatan', 1, 0, 'C');
            $this->Cell(18, 7, 'TBK', 1, 0, 'C');
            $this->Cell(20, 7, 'Jumlah', 1, 0, 'C');
            $this->Cell(20, 7, 'Potongan', 1, 0, 'C');
            $this->Cell(19, 7, 'Diterima', 1, 0, 'C');
            $this->Ln();
            $this->setY(25);
        }
    }
}

class CustomPdfPengajar extends FPDF
{
    protected $watermarkPath;

    public function __construct($orientation = 'L', $unit = 'mm', $size = 'LEGAL', $watermark = null)
    {
        parent::__construct($orientation, $unit, $size);
        $this->watermarkPath = $watermark ?? public_path('assets/p2s2_watermark.png');
    }

    public function Header()
    {
        if (file_exists($this->watermarkPath)) {
            $pageW = $this->GetPageWidth();
            $pageH = $this->GetPageHeight();
            $logoW = 80;
            $logoX = ($pageW - $logoW) / 2;
            $logoY = ($pageH - ($logoW * 283 / 753)) / 2;
            $this->Image($this->watermarkPath, $logoX, $logoY, $logoW);
        }

        if ($this->PageNo() > 1) {
            $this->Cell(1, 7, '', 0, 1);
            $this->SetFont('arial', 'B', 6);
            $this->Cell(5, 7, 'NO', 1, 0, 'C');
            $this->Cell(45, 7, 'NAMA LENGKAP', 1, 0, 'C');
            $this->Cell(8, 7, 'IK', 1, 0, 'C');
            $this->Cell(8, 7, 'IT', 1, 0, 'C');
            $this->Cell(8, 7, 'TMT', 1, 0, 'C');
            $this->Cell(5, 7, 'MP', 1, 0, 'C');
            $this->Cell(12, 7, 'JAM/SKS', 1, 0, 'C');
            $this->Cell(10, 7, 'RANK', 1, 0, 'C');
            $this->SetFont('arial', 'B', 5);
            $this->Cell(12, 7, 'MENGAJAR', 1, 0, 'C');
            $this->SetFont('arial', 'B', 6);
            $this->Cell(10, 7, 'DTY', 1, 0, 'C');
            $this->Cell(12, 7, 'JAFUNG', 1, 0, 'C');
            $this->Cell(17, 7, 'TRANSPORT', 1, 0, 'C');
            $this->Cell(17, 7, 'KEHADIRAN 15', 1, 0, 'C');
            $this->Cell(17, 7, 'KEHADIRAN 10', 1, 0, 'C');
            $this->Cell(24, 7, 'KEHADIRAN PIKET', 1, 0, 'C');
            $this->Cell(12, 7, 'TUNKEL', 1, 0, 'C');
            $this->Cell(12, 7, 'TUNANAK', 1, 0, 'C');
            $this->Cell(15, 7, 'KEHORMATAN', 1, 0, 'C');
            $this->SetFont('arial', 'B', 7);
            $this->Cell(15, 7, 'WALKES', 1, 0, 'C');
            $this->Cell(15, 7, 'TAMBAHAN', 1, 0, 'C');
            $this->Cell(15, 7, 'JUMLAH', 1, 0, 'C');
            $this->Cell(15, 7, 'POTONGAN', 1, 0, 'C');
            $this->Cell(20, 7, 'DITERIMA', 1, 0, 'C');
            $this->Cell(0, 1, '', 0, 1);
            $this->setY(25);
        }
    }
}

class CustomPdfPotongan extends FPDF
{
    protected $watermarkPath;

    public function __construct($orientation = 'P', $unit = 'mm', $size = 'LEGAL', $watermark = null)
    {
        parent::__construct($orientation, $unit, $size);
        $this->watermarkPath = $watermark ?? public_path('assets/p2s2_watermark.png');
    }

    public function Header()
    {
        if (file_exists($this->watermarkPath)) {
            $pageW = $this->GetPageWidth();
            $pageH = $this->GetPageHeight();
            $logoW = 100;
            $logoX = ($pageW - $logoW) / 2;
            $logoY = ($pageH - ($logoW * 283 / 753)) / 2;
            $this->Image($this->watermarkPath, $logoX, $logoY, $logoW);
        }

        if ($this->PageNo() > 1) {
            $this->Cell(1, 7, '', 0, 1);
            $this->SetFont('arial', 'B', 7);
            $this->Cell(10, 7, 'NO', 1, 0, 'C');
            $this->Cell(76, 7, 'NAMA LENGKAP', 1, 0, 'C');
            $this->Cell(60, 7, 'NAMA POTONGAN', 1, 0, 'C');
            $this->Cell(50, 7, 'NOMINAL', 1, 0, 'C');
            $this->Cell(0, 1, '', 0, 1);
            $this->setY(25);
        }
    }
}

class PdfBarokahService
{
    public static function formatRupiah($nominal)
    {
        return number_format((float)$nominal, 0, ',', '.');
    }

    public static function getNamaBulanIndo($bulanAngka)
    {
        $map = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $map[(int)$bulanAngka] ?? 'Desember';
    }

    /**
     * Cetak Rekap Barokah Struktural (Landscape LEGAL)
     */
    public static function cetakRekapBarokah($data)
    {
        $rows    = $data['rows'];
        $periode = $data['periode'];
        $totals  = $data['totals'];

        $pdf = new CustomPdfStruktural('L', 'mm', 'LEGAL');

        $fontDir = app_path('Libraries/fpdf/font/');
        if (file_exists($fontDir . 'tahoma.php')) {
            $pdf->AddFont('tahoma', 'B', 'tahomabd.php');
            $pdf->AddFont('tahoma', '', 'tahoma.php');
        }

        $pdf->SetFont('Times', 'B', 16);
        $pdf->AddPage();

        // Logo Pesantren Kop
        $logoPath = public_path('assets/p2s2.png');
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 10, 11, 30);
        }

        // Kop Surat
        $pdf->Ln(4);
        $fontTitle = file_exists($fontDir . 'tahoma.php') ? 'tahoma' : 'Arial';
        $pdf->SetFont($fontTitle, 'B', 13);
        $pdf->Cell(31, 0, '', 0, 0, 'L', false);
        $pdf->Cell(150, 1, "PONDOK PESANTREN SALAFIYAH SYAFI'IYAH SUKOREJO", 0, 1, 'L', false);

        $pdf->Ln(4);
        $pdf->SetFont($fontTitle, '', 11);
        $pdf->Cell(31, 0, '', 0, 0, 'L', false);
        $pdf->Cell(150, 1, 'SUMBEREJO BANYUPUTIH SITUBONDO JAWA TIMUR', 0, 1, 'L', false);

        $pdf->Ln(5);
        $pdf->SetFont($fontTitle, '', 8);
        $pdf->Cell(40, 0, 'Po Box 2 telp 0388-452666 Fax. 452707 - Situbondo, 68374', 0, 0, 'L', false);

        $pdf->Line(9, 23, 340, 23);
        $pdf->Line(9, 27, 340, 27);

        // Header Sub / Nama Lembaga & Periode
        $pdf->Ln(5);
        $pdf->SetFont('arial', 'B', 10);
        $pdf->Cell(200, 2, strtoupper($periode->nama_lembaga ?? ''), 0, 0, 'L', false);
        $pdf->Cell(50, 2, '', 0, 0, 'L', false);
        $pdf->Cell(50, 2, 'Barokah Umana', 0, 1, 'L', false);

        $pdf->Ln(2);
        $pdf->SetFont('arial', '', 10);
        $pdf->Cell(200, 2, '', 0, 0, 'L', false);
        $pdf->Cell(50, 2, '', 0, 0, 'L', false);
        $pdf->Cell(50, 2, 'Bulan : ' . ($periode->bulan ?? '') . ' ' . ($periode->tahun ?? ''), 0, 1, 'L', false);

        // Header Tabel
        $pdf->Cell(1, 7, '', 0, 1);
        $pdf->SetFont('arial', 'B', 6);

        $pdf->Cell(6, 7, 'NO', 1, 0, 'C');
        $pdf->Cell(53, 7, 'NAMA LENGKAP', 1, 0, 'C');
        $pdf->Cell(32, 7, 'ESELON', 1, 0, 'C');
        $pdf->Cell(10, 7, 'TMT', 1, 0, 'C');
        $pdf->Cell(19, 7, 'TUNJAB', 1, 0, 'C');
        $pdf->Cell(7, 7, 'MP', 1, 0, 'C');
        $pdf->Cell(19, 7, 'TMP', 1, 0, 'C');

        $pdf->Cell(6, 7, 'W', 1, 0, 'C');
        $pdf->Cell(6, 7, 'H', 1, 0, 'C');
        $pdf->Cell(6, 7, 'T', 1, 0, 'C');
        $pdf->Cell(5, 7, 'I', 1, 0, 'C');
        $pdf->Cell(5, 7, 'S', 1, 0, 'C');
        $pdf->Cell(10, 7, '%', 1, 0, 'C');

        $pdf->Cell(19, 7, 'Kehadiran', 1, 0, 'C');
        $pdf->Cell(19, 7, 'Tunkel', 1, 0, 'C');
        $pdf->Cell(19, 7, 'Tun Anak', 1, 0, 'C');
        $pdf->Cell(20, 7, 'Kehormatan', 1, 0, 'C');
        $pdf->Cell(18, 7, 'TBK', 1, 0, 'C');
        $pdf->Cell(20, 7, 'Jumlah', 1, 0, 'C');
        $pdf->Cell(20, 7, 'Potongan', 1, 0, 'C');
        $pdf->Cell(19, 7, 'Diterima', 1, 0, 'C');

        // Isi Data Tabel
        $no = 1;
        foreach ($rows as $key) {
            $pdf->Cell(1, 7, '', 0, 1);
            $pdf->SetFont('arial', 'B', 7);

            $fill = false;
            if (!empty($key->is_warning)) {
                $pdf->SetFillColor(255, 243, 205);
                $fill = true;
            }

            $isSerti = !empty($key->status_sertifikasi) && in_array(strtolower(trim($key->status_sertifikasi)), ['sudah', 'ya', 'sudah sertifikasi']);
            $katPengajar = $key->kategori_pengajar ?? '';
            if (in_array($katPengajar, ['GTY', 'GTT'])) {
                $labelSerti = 'Sergur';
            } elseif (in_array($katPengajar, ['DTY', 'DTT'])) {
                $labelSerti = 'Serdos';
            } elseif (!empty($key->tmt_dosen) && $key->tmt_dosen != '0000-00-00' && $key->tmt_dosen != '0001-01-01') {
                $labelSerti = 'Serdos';
            } elseif (!empty($key->tmt_guru) && $key->tmt_guru != '0000-00-00' && $key->tmt_guru != '0001-01-01') {
                $labelSerti = 'Sergur';
            } else {
                $labelSerti = 'Serdos';
            }

            $pdf->Cell(6, 7, $no++, 1, 0, 'C', $fill);

            $nama_full = strtoupper(implode(' ', array_filter([
                trim($key->gelar_depan ?? ''),
                trim($key->nama_lengkap ?? ''),
                trim($key->gelar_belakang ?? '')
            ])));
            $pdf->Cell(53, 7, $nama_full, 1, 0, 'L', $fill);
            $pdf->Cell(32, 7, $key->nama_jabatan ?? '', 1, 0, 'L', $fill);

            $tmtTahun = !empty($key->tmt_struktural) && $key->tmt_struktural != '0000-00-00'
                ? date('Y', strtotime($key->tmt_struktural))
                : '-';
            $pdf->Cell(10, 7, $tmtTahun, 1, 0, 'C', $fill);
            $pdf->Cell(19, 7, self::formatRupiah($key->tunjab ?? 0), 1, 0, 'C', $fill);
            $pdf->Cell(7, 7, (string)($key->mp ?? 0), 1, 0, 'C', $fill);
            $pdf->Cell(19, 7, self::formatRupiah($key->tmp ?? 0), 1, 0, 'C', $fill);

            // Kehadiran W, H, T, I, S, %
            $pdf->Cell(6, 7, (string)($key->wajib_hadir_bulanan ?? 0), 1, 0, 'C', $fill);
            $pdf->Cell(6, 7, (string)($key->jumlah_hadir ?? 0), 1, 0, 'C', $fill);
            $pdf->Cell(6, 7, (string)($key->jumlah_tugas ?? 0), 1, 0, 'C', $fill);
            $pdf->Cell(5, 7, (string)($key->jumlah_izin ?? 0), 1, 0, 'C', $fill);
            $pdf->Cell(5, 7, (string)($key->jumlah_sakit ?? 0), 1, 0, 'C', $fill);
            $pdf->Cell(10, 7, (string)($key->persentase_kehadiran ?? 0), 1, 0, 'C', $fill);

            // Nominal
            $pdf->Cell(19, 7, self::formatRupiah($key->nominal_kehadiran ?? 0), 1, 0, 'C', $fill);
            $pdf->Cell(19, 7, self::formatRupiah($key->tunkel ?? 0), 1, 0, 'C', $fill);
            $pdf->Cell(19, 7, $isSerti ? $labelSerti : self::formatRupiah($key->tunj_anak ?? 0), 1, 0, 'C', $fill);
            $pdf->Cell(20, 7, $isSerti ? $labelSerti : self::formatRupiah($key->nilai_kehormatan ?? 0), 1, 0, 'C', $fill);
            $pdf->Cell(18, 7, self::formatRupiah($key->tbk ?? 0), 1, 0, 'C', $fill);
            $pdf->Cell(20, 7, self::formatRupiah($key->jumlah_barokah ?? 0), 1, 0, 'C', $fill);
            $pdf->Cell(20, 7, self::formatRupiah($key->potongan ?? 0), 1, 0, 'C', $fill);
            $pdf->Cell(19, 7, self::formatRupiah($key->diterima ?? 0), 1, 0, 'C', $fill);
            $pdf->Cell(0, 0, '', 0, 1);
            $pdf->Cell(0, 0, '', 0, 1);
        }

        $pdf->Ln(8);

        // Baris TOTAL
        $pdf->SetFont('arial', 'B', 7);
        $pdf->Cell(101, 7, 'TOTAL', 1, 0, 'C');
        $pdf->Cell(19, 7, self::formatRupiah($totals['total_tunjab'] ?? 0), 1, 0, 'C');
        $pdf->Cell(7, 7, '', 1, 0, 'C');
        $pdf->Cell(19, 7, self::formatRupiah($totals['total_tmp'] ?? 0), 1, 0, 'C');

        // Spasi Kehadiran W, H, T, I, S, %
        $pdf->Cell(6, 7, '', 1, 0, 'C');
        $pdf->Cell(6, 7, '', 1, 0, 'C');
        $pdf->Cell(6, 7, '', 1, 0, 'C');
        $pdf->Cell(5, 7, '', 1, 0, 'C');
        $pdf->Cell(5, 7, '', 1, 0, 'C');
        $pdf->Cell(10, 7, '', 1, 0, 'C');

        $pdf->Cell(19, 7, self::formatRupiah($totals['total_kehadiran'] ?? 0), 1, 0, 'C');
        $pdf->Cell(19, 7, self::formatRupiah($totals['total_tunkel'] ?? 0), 1, 0, 'C');
        $pdf->Cell(19, 7, self::formatRupiah($totals['total_tunjanak'] ?? 0), 1, 0, 'C');
        $pdf->Cell(20, 7, self::formatRupiah($totals['total_kehormatan'] ?? 0), 1, 0, 'C');
        $pdf->Cell(18, 7, self::formatRupiah($totals['total_tbk'] ?? 0), 1, 0, 'C');
        $pdf->Cell(20, 7, self::formatRupiah($totals['total_barokah'] ?? 0), 1, 0, 'C');
        $pdf->Cell(20, 7, self::formatRupiah($totals['total_potongan'] ?? 0), 1, 0, 'C');
        $pdf->Cell(19, 7, self::formatRupiah($totals['grand_total'] ?? 0), 1, 0, 'C');

        // Tanggal Cetak
        $tgl = date('d');
        $b1  = self::getNamaBulanIndo(date('n'));
        $thn = date('Y');

        $pdf->Ln(40);
        $pdf->SetFont('arial', 'i', 5);
        $pdf->Cell(292, -40, 'dicetak pada: ' . $tgl . ' ' . $b1 . ' ' . $thn, 0, 0, 'R');

        return $pdf->Output('S');
    }

    /**
     * Cetak Potongan Barokah Struktural (Portrait LEGAL)
     */
    public static function cetakPotongan($potonganList, $lembagaInfo, $bulanLaporan = null, $tahunLaporan = null)
    {
        $pdf = new CustomPdfPotongan('P', 'mm', 'LEGAL');

        $fontDir = app_path('Libraries/fpdf/font/');
        if (file_exists($fontDir . 'tahoma.php')) {
            $pdf->AddFont('tahoma', 'B', 'tahomabd.php');
            $pdf->AddFont('tahoma', '', 'tahoma.php');
        }

        $pdf->SetFont('Times', 'B', 16);
        $pdf->AddPage();

        $logoPath = public_path('assets/p2s2.png');
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 10, 11, 30);
        }

        // Kop Surat
        $pdf->Ln(4);
        $fontTitle = file_exists($fontDir . 'tahoma.php') ? 'tahoma' : 'Arial';
        $pdf->SetFont($fontTitle, 'B', 13);
        $pdf->Cell(31, 0, '', 0, 0, 'L', false);
        $pdf->Cell(150, 1, "PONDOK PESANTREN SALAFIYAH SYAFI'IYAH SUKOREJO", 0, 1, 'L', false);

        $pdf->Ln(4);
        $pdf->SetFont($fontTitle, '', 11);
        $pdf->Cell(31, 0, '', 0, 0, 'L', false);
        $pdf->Cell(150, 1, 'SUMBEREJO BANYUPUTIH SITUBONDO JAWA TIMUR', 0, 1, 'L', false);

        $pdf->Ln(5);
        $pdf->SetFont($fontTitle, '', 8);
        $pdf->Cell(40, 0, 'Po Box 2 telp 0388-452666 Fax. 452707 - Situbondo, 68374', 0, 0, 'L', false);

        $pdf->Line(9, 23, 205, 23);
        $pdf->Line(9, 27, 205, 27);

        $pdf->Ln(3);
        $pdf->SetFont('arial', '', 9);
        $pdf->Cell(0, 5, 'RINCIAN POTONGAN BAROKAH UMANA STRUKTURAL', 0, 1, 'L', false);

        $namaLembagaHeader = !empty($lembagaInfo->nama_lembaga) ? strtoupper($lembagaInfo->nama_lembaga) : '';
        $pdf->SetFont('arial', 'B', 10);
        $pdf->Cell(135, 6, $namaLembagaHeader, 0, 0, 'L', false);
        $pdf->SetFont('arial', '', 9);
        $pdf->Cell(0, 6, 'Barokah Struktural', 0, 1, 'R', false);

        if (!empty($bulanLaporan) || !empty($tahunLaporan)) {
            $pdf->SetFont('arial', 'I', 9);
            $pdf->Cell(0, 5, 'Periode : ' . ucfirst($bulanLaporan ?? '') . ' ' . ($tahunLaporan ?? ''), 0, 1, 'L', false);
        }

        $pdf->Ln(3);
        $pdf->SetFont('arial', 'B', 7);
        $pdf->Cell(10, 7, 'NO', 1, 0, 'C');
        $pdf->Cell(76, 7, 'NAMA LENGKAP', 1, 0, 'C');
        $pdf->Cell(60, 7, 'NAMA POTONGAN', 1, 0, 'C');
        $pdf->Cell(50, 7, 'NOMINAL', 1, 0, 'C');
        $pdf->Cell(0, 1, '', 0, 1);

        $no = 1;
        $jumlahTotal = 0;
        $pdf->Ln(7);

        foreach ($potonganList as $key) {
            $namaTampil = strtoupper(implode(' ', array_filter([
                trim($key->gelar_depan ?? ''),
                trim($key->nama_lengkap ?? ''),
                trim($key->gelar_belakang ?? '')
            ])));

            $pdf->Cell(10, 7, $no++, 1, 0, 'C');
            $pdf->Cell(76, 7, $namaTampil, 1, 0, 'L');
            $pdf->Cell(60, 7, $key->nama_potongan ?? '', 1, 0, 'L');
            $pdf->Cell(50, 7, self::formatRupiah($key->nominal_potongan ?? 0), 1, 0, 'C');
            $pdf->Cell(0, 7, '', 0, 1);

            $jumlahTotal += (float)($key->nominal_potongan ?? 0);
        }

        $pdf->Ln(1);
        $pdf->SetFont('arial', 'B', 9);
        $pdf->Cell(86, 7, '', 0, 0, 'C');
        $pdf->Cell(60, 7, 'Total', 1, 0, 'C');
        $pdf->Cell(50, 7, self::formatRupiah($jumlahTotal), 1, 0, 'C');
        $pdf->Cell(0, 1, '', 0, 1);

        // Tanggal Cetak
        $tgl = date('d');
        $b1  = self::getNamaBulanIndo(date('n'));
        $thn = date('Y');

        $pdf->Ln(30);
        $pdf->SetFont('arial', 'i', 5);
        $pdf->Cell(22, -40, 'dicetak pada: ' . $tgl . ' ' . $b1 . ' ' . $thn, 0, 0, 'R');

        return $pdf->Output('S');
    }

    /**
     * Cetak Rekap Barokah Pengajar (Landscape LEGAL)
     */
    public static function cetakRekapPengajar($data)
    {
        $rows    = $data['rows'];
        $periode = $data['periode'];
        $totals  = $data['totals'];

        $pdf = new CustomPdfPengajar('L', 'mm', 'LEGAL');

        $fontDir = app_path('Libraries/fpdf/font/');
        if (file_exists($fontDir . 'tahoma.php')) {
            $pdf->AddFont('tahoma', 'B', 'tahomabd.php');
            $pdf->AddFont('tahoma', '', 'tahoma.php');
        }

        $pdf->SetFont('Times', 'B', 16);
        $pdf->AddPage();

        $logoPath = public_path('assets/p2s2.png');
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 10, 11, 30);
        }

        $fontTitle = file_exists($fontDir . 'tahoma.php') ? 'tahoma' : 'Arial';
        $pdf->Ln(4);
        $pdf->SetFont($fontTitle, 'B', 13);
        $pdf->Cell(31, 0, '', 0, 0, 'L', false);
        $pdf->Cell(150, 1, "PONDOK PESANTREN SALAFIYAH SYAFI'IYAH SUKOREJO", 0, 1, 'L', false);

        $pdf->Ln(4);
        $pdf->SetFont($fontTitle, '', 11);
        $pdf->Cell(31, 0, '', 0, 0, 'L', false);
        $pdf->Cell(150, 1, 'SUMBEREJO BANYUPUTIH SITUBONDO JAWA TIMUR', 0, 1, 'L', false);

        $pdf->Ln(5);
        $pdf->SetFont($fontTitle, '', 8);
        $pdf->Cell(40, 0, 'Po Box 2 telp 0388-452666 Fax. 452707 - Situbondo, 68374', 0, 0, 'L', false);

        $pdf->Line(9, 23, 340, 23);
        $pdf->Line(9, 27, 340, 27);

        // Header Sub
        $pdf->Ln(5);
        $pdf->SetFont('arial', 'B', 10);
        $namaLembaga = $periode->nama_lembaga ?? ($periode->lembaga->nama_lembaga ?? '');
        $pdf->Cell(200, 2, strtoupper($namaLembaga), 0, 0, 'L', false);
        $pdf->Cell(50, 2, '', 0, 0, 'L', false);

        $idBidang = $periode->id_bidang ?? ($periode->lembaga->id_bidang ?? '');
        if ($idBidang === 'Bidang DIKTI') {
            $pdf->Cell(50, 2, 'Barokah Dosen', 0, 1, 'L', false);
        } else {
            $pdf->Cell(50, 2, 'Barokah Guru', 0, 1, 'L', false);
        }

        $pdf->Ln(2);
        $pdf->SetFont('arial', '', 10);
        $pdf->Cell(200, 2, '', 0, 0, 'L', false);
        $pdf->Cell(50, 2, '', 0, 0, 'L', false);
        $pdf->Cell(50, 2, 'Bulan : ' . ($periode->bulan ?? '') . ' ' . ($periode->tahun ?? ''), 0, 1, 'L', false);

        // Header Tabel
        $pdf->Cell(1, 7, '', 0, 1);
        $pdf->SetFont('arial', 'B', 6);
        $pdf->Cell(5, 7, 'NO', 1, 0, 'C');
        $pdf->Cell(45, 7, 'NAMA LENGKAP', 1, 0, 'C');
        $pdf->Cell(8, 7, 'IK', 1, 0, 'C');
        $pdf->Cell(8, 7, 'IT', 1, 0, 'C');
        $pdf->Cell(8, 7, 'TMT', 1, 0, 'C');
        $pdf->Cell(5, 7, 'MP', 1, 0, 'C');
        $pdf->Cell(12, 7, 'JAM/SKS', 1, 0, 'C');
        $pdf->Cell(10, 7, 'RANK', 1, 0, 'C');
        $pdf->SetFont('arial', 'B', 5);
        $pdf->Cell(12, 7, 'MENGAJAR', 1, 0, 'C');
        $pdf->SetFont('arial', 'B', 6);
        $pdf->Cell(10, 7, 'DTY', 1, 0, 'C');
        $pdf->Cell(12, 7, 'JAFUNG', 1, 0, 'C');
        $pdf->Cell(17, 7, 'TRANSPORT', 1, 0, 'C');
        $pdf->Cell(17, 7, 'KEHADIRAN 15', 1, 0, 'C');
        $pdf->Cell(17, 7, 'KEHADIRAN 10', 1, 0, 'C');
        $pdf->Cell(24, 7, 'KEHADIRAN PIKET', 1, 0, 'C');
        $pdf->Cell(12, 7, 'TUNKEL', 1, 0, 'C');
        $pdf->Cell(12, 7, 'TUNANAK', 1, 0, 'C');
        $pdf->Cell(15, 7, 'KEHORMATAN', 1, 0, 'C');
        $pdf->SetFont('arial', 'B', 7);
        $pdf->Cell(15, 7, 'WALKES', 1, 0, 'C');
        $pdf->Cell(15, 7, 'TAMBAHAN', 1, 0, 'C');
        $pdf->Cell(15, 7, 'JUMLAH', 1, 0, 'C');
        $pdf->Cell(15, 7, 'POTONGAN', 1, 0, 'C');
        $pdf->Cell(20, 7, 'DITERIMA', 1, 0, 'C');
        $pdf->Cell(0, 1, '', 0, 1);

        // Isi Data
        $no = 1;
        foreach ($rows as $key) {
            $pdf->Cell(1, 7, '', 0, 1);
            $pdf->SetFont('arial', 'B', 6);

            $fill = false;
            if (!empty($key->is_warning)) {
                $pdf->SetFillColor(255, 243, 205);
                $fill = true;
            }

            $isSerti = !empty($key->status_sertifikasi) && in_array(strtolower(trim($key->status_sertifikasi)), ['sudah', 'ya', 'sudah sertifikasi']);
            $labelSerti = in_array($key->kategori ?? '', ['GTY', 'GTT']) ? 'Sergur' : 'Serdos';

            $pdf->Cell(5, 7, $no++, 1, 0, 'C', $fill);

            $gDepan = !empty($key->gelar_depan) ? trim($key->gelar_depan) . ' ' : '';
            $gBelakang = !empty($key->gelar_belakang) ? ' ' . trim($key->gelar_belakang) : '';
            $namaFormatted = ucwords(strtolower(trim($key->nama_lengkap ?? '')));
            $fullName = $gDepan . $namaFormatted . $gBelakang;

            $pdf->Cell(45, 7, $fullName, 1, 0, 'L', $fill);
            $pdf->Cell(8, 7, (string)($key->kategori ?? ''), 1, 0, 'C', $fill);
            $pdf->Cell(8, 7, (string)($key->ijazah_terakhir ?? ''), 1, 0, 'C', $fill);

            $tmtDisplay = '-';
            if (!empty($key->tmt_dosen) && ($key->kategori === 'DTY' || $key->kategori === 'DTT')) {
                $tmtDisplay = date('Y', strtotime($key->tmt_dosen));
            } elseif (!empty($key->tmt_maif) && isset($key->nama_lembaga) && $key->nama_lembaga === "Ma'had Aly Sukorejo") {
                $tmtDisplay = date('Y', strtotime($key->tmt_maif));
            } elseif (!empty($key->tmt_guru)) {
                $tmtDisplay = date('Y', strtotime($key->tmt_guru));
            }
            $pdf->Cell(8, 7, $tmtDisplay, 1, 0, 'C', $fill);
            $pdf->Cell(5, 7, (string)($key->mp ?? 0), 1, 0, 'C', $fill);
            $pdf->Cell(12, 7, (string)($key->jumlah_sks ?? 0), 1, 0, 'C', $fill);
            $pdf->Cell(10, 7, self::formatRupiah($key->rank ?? 0), 1, 0, 'R', $fill);
            $pdf->Cell(12, 7, self::formatRupiah($key->mengajar ?? 0), 1, 0, 'R', $fill);
            $pdf->Cell(10, 7, $isSerti ? $labelSerti : self::formatRupiah($key->dty ?? 0), 1, 0, $isSerti ? 'C' : 'R', $fill);
            $pdf->Cell(12, 7, self::formatRupiah($key->jafung ?? 0), 1, 0, 'R', $fill);
            $pdf->Cell(17, 7, self::formatRupiah($key->nominal_kehadiran ?? 0), 1, 0, 'R', $fill);
            $pdf->Cell(17, 7, self::formatRupiah($key->nominal_hadir_15 ?? 0), 1, 0, 'R', $fill);
            $pdf->Cell(17, 7, self::formatRupiah($key->nominal_hadir_10 ?? 0), 1, 0, 'R', $fill);
            $pdf->Cell(24, 7, self::formatRupiah($key->barokah_piket ?? 0), 1, 0, 'R', $fill);
            $pdf->Cell(12, 7, self::formatRupiah($key->tunkel ?? 0), 1, 0, 'R', $fill);
            $pdf->Cell(12, 7, $isSerti ? $labelSerti : self::formatRupiah($key->tunja_anak ?? ($key->tun_anak ?? 0)), 1, 0, $isSerti ? 'C' : 'R', $fill);
            $pdf->Cell(15, 7, $isSerti ? $labelSerti : self::formatRupiah($key->kehormatan ?? 0), 1, 0, $isSerti ? 'C' : 'R', $fill);
            $pdf->Cell(15, 7, self::formatRupiah($key->walkes ?? ($key->tunj_walkes ?? 0)), 1, 0, 'R', $fill);
            $pdf->Cell(15, 7, self::formatRupiah($key->khusus ?? ($key->tambahan ?? 0)), 1, 0, 'R', $fill);
            $pdf->Cell(15, 7, self::formatRupiah($key->jumlah ?? 0), 1, 0, 'R', $fill);
            $pdf->Cell(15, 7, self::formatRupiah($key->potongan ?? 0), 1, 0, 'R', $fill);
            $pdf->Cell(20, 7, self::formatRupiah($key->diterima ?? 0), 1, 0, 'R', $fill);
            $pdf->Cell(0, 0, '', 0, 1);
        }

        $pdf->Ln(8);

        // Baris TOTAL
        $pdf->SetFont('arial', 'B', 6);
        $pdf->Cell(79, 7, 'TOTAL', 1, 0, 'C');
        $pdf->Cell(12, 7, self::formatRupiah($totals['total_mengajar'] ?? 0), 1, 0, 'R');
        $pdf->Cell(10, 7, self::formatRupiah($totals['total_dty'] ?? 0), 1, 0, 'R');
        $pdf->Cell(12, 7, self::formatRupiah($totals['total_jafung'] ?? 0), 1, 0, 'R');
        $pdf->Cell(17, 7, self::formatRupiah($totals['total_kehadiran'] ?? 0), 1, 0, 'R');
        $pdf->Cell(17, 7, self::formatRupiah($totals['total_hadir_15'] ?? 0), 1, 0, 'R');
        $pdf->Cell(17, 7, self::formatRupiah($totals['total_hadir_10'] ?? 0), 1, 0, 'R');
        $pdf->Cell(24, 7, self::formatRupiah($totals['total_piket'] ?? 0), 1, 0, 'R');
        $pdf->Cell(12, 7, self::formatRupiah($totals['total_tunkel'] ?? 0), 1, 0, 'R');
        $pdf->Cell(12, 7, self::formatRupiah($totals['total_tunjanak'] ?? 0), 1, 0, 'R');
        $pdf->Cell(15, 7, self::formatRupiah($totals['total_kehormatan'] ?? 0), 1, 0, 'R');
        $pdf->Cell(15, 7, self::formatRupiah($totals['total_walkes'] ?? 0), 1, 0, 'R');
        $pdf->Cell(15, 7, self::formatRupiah($totals['total_tambahan'] ?? 0), 1, 0, 'R');
        $pdf->Cell(15, 7, self::formatRupiah($totals['total_jumlah'] ?? 0), 1, 0, 'R');
        $pdf->Cell(15, 7, self::formatRupiah($totals['total_potongan'] ?? 0), 1, 0, 'R');
        $pdf->Cell(20, 7, self::formatRupiah($totals['grand_total'] ?? 0), 1, 0, 'R');

        // Tanggal Cetak
        $tgl = date('d');
        $b1  = self::getNamaBulanIndo(date('n'));
        $thn = date('Y');

        $pdf->Ln(40);
        $pdf->SetFont('arial', 'i', 5);
        $pdf->Cell(292, -40, 'dicetak pada: ' . $tgl . ' ' . $b1 . ' ' . $thn, 0, 0, 'R');

        return $pdf->Output('S');
    }

    /**
     * Cetak Potongan Pengajar (Portrait LEGAL)
     */
    public static function cetakPotonganPengajar($potonganList, $lembagaInfo, $bulanLaporan = null, $tahunLaporan = null)
    {
        $pdf = new CustomPdfPotongan('P', 'mm', 'LEGAL');

        $fontDir = app_path('Libraries/fpdf/font/');
        if (file_exists($fontDir . 'tahoma.php')) {
            $pdf->AddFont('tahoma', 'B', 'tahomabd.php');
            $pdf->AddFont('tahoma', '', 'tahoma.php');
        }

        $pdf->SetFont('Times', 'B', 16);
        $pdf->AddPage();

        $logoPath = public_path('assets/p2s2.png');
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 10, 11, 30);
        }

        $fontTitle = file_exists($fontDir . 'tahoma.php') ? 'tahoma' : 'Arial';
        $pdf->Ln(4);
        $pdf->SetFont($fontTitle, 'B', 13);
        $pdf->Cell(31, 0, '', 0, 0, 'L', false);
        $pdf->Cell(150, 1, "PONDOK PESANTREN SALAFIYAH SYAFI'IYAH SUKOREJO", 0, 1, 'L', false);

        $pdf->Ln(4);
        $pdf->SetFont($fontTitle, '', 11);
        $pdf->Cell(31, 0, '', 0, 0, 'L', false);
        $pdf->Cell(150, 1, 'SUMBEREJO BANYUPUTIH SITUBONDO JAWA TIMUR', 0, 1, 'L', false);

        $pdf->Ln(5);
        $pdf->SetFont($fontTitle, '', 8);
        $pdf->Cell(40, 0, 'Po Box 2 telp 0388-452666 Fax. 452707 - Situbondo, 68374', 0, 0, 'L', false);

        $pdf->Line(9, 23, 205, 23);
        $pdf->Line(9, 27, 205, 27);

        $pdf->Ln(3);
        $pdf->SetFont('arial', '', 9);
        $pdf->Cell(0, 5, 'RINCIAN POTONGAN BAROKAH GURU / DOSEN', 0, 1, 'L', false);

        $namaLembagaHeader = !empty($lembagaInfo->nama_lembaga) ? strtoupper($lembagaInfo->nama_lembaga) : '';
        $pdf->SetFont('arial', 'B', 10);
        $pdf->Cell(135, 6, $namaLembagaHeader, 0, 0, 'L', false);
        $pdf->SetFont('arial', '', 9);
        $pdf->Cell(0, 6, 'Barokah Pengajar', 0, 1, 'R', false);

        if (!empty($bulanLaporan) || !empty($tahunLaporan)) {
            $pdf->SetFont('arial', 'I', 9);
            $pdf->Cell(0, 5, 'Periode : ' . ucfirst($bulanLaporan ?? '') . ' ' . ($tahunLaporan ?? ''), 0, 1, 'L', false);
        }

        $pdf->Ln(3);
        $pdf->SetFont('arial', 'B', 7);
        $pdf->Cell(10, 7, 'NO', 1, 0, 'C');
        $pdf->Cell(76, 7, 'NAMA LENGKAP', 1, 0, 'C');
        $pdf->Cell(60, 7, 'NAMA POTONGAN', 1, 0, 'C');
        $pdf->Cell(50, 7, 'NOMINAL', 1, 0, 'C');
        $pdf->Cell(0, 1, '', 0, 1);

        $no = 1;
        $jumlahTotal = 0;
        $pdf->Ln(7);

        foreach ($potonganList as $key) {
            $namaTampil = strtoupper(implode(' ', array_filter([
                trim($key->gelar_depan ?? ''),
                trim($key->nama_lengkap ?? ''),
                trim($key->gelar_belakang ?? '')
            ])));

            $pdf->Cell(10, 7, $no++, 1, 0, 'C');
            $pdf->Cell(76, 7, $namaTampil, 1, 0, 'L');
            $pdf->Cell(60, 7, $key->nama_potongan ?? '', 1, 0, 'L');
            $pdf->Cell(50, 7, self::formatRupiah($key->nominal_potongan ?? 0), 1, 0, 'C');
            $pdf->Cell(0, 7, '', 0, 1);

            $jumlahTotal += (float)($key->nominal_potongan ?? 0);
        }

        $pdf->Ln(1);
        $pdf->SetFont('arial', 'B', 9);
        $pdf->Cell(86, 7, '', 0, 0, 'C');
        $pdf->Cell(60, 7, 'Total', 1, 0, 'C');
        $pdf->Cell(50, 7, self::formatRupiah($jumlahTotal), 1, 0, 'C');
        $pdf->Cell(0, 1, '', 0, 1);

        // Tanggal Cetak
        $tgl = date('d');
        $b1  = self::getNamaBulanIndo(date('n'));
        $thn = date('Y');

        $pdf->Ln(30);
        $pdf->SetFont('arial', 'i', 5);
        $pdf->Cell(22, -40, 'dicetak pada: ' . $tgl . ' ' . $b1 . ' ' . $thn, 0, 0, 'R');

        return $pdf->Output('S');
    }

    /**
     * Cetak Rekap Barokah Satpam (Landscape Legal)
     */
    public static function cetakRekapSatpam($periode, $isilist, $honorDanru = 100000)
    {
        $pdf = new FPDF('L', 'mm', 'LEGAL');
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        // KOP SURAT
        $logoPath = public_path('images/p2s2.png');
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 10, 9, 30);
        }
        $pdf->Cell(31, 0, '', 0, 0, 'L', false);
        $pdf->SetFont('arial', 'B', 15);
        $pdf->Cell(0, 1, 'KANTOR BENDAHARA', 0, 1, 'L', false);

        $pdf->Ln(4);
        $pdf->SetFont('arial', 'B', 13);
        $pdf->Cell(31, 0, '', 0, 0, 'L', false);
        $pdf->Cell(150, 1, "PONDOK PESANTREN SALAFIYAH SYAFI'IYAH", 0, 1, 'L', false);

        $pdf->Ln(4);
        $pdf->SetFont('arial', '', 11);
        $pdf->Cell(31, 0, '', 0, 0, 'L', false);
        $pdf->Cell(150, 1, 'SUKOREJO SITUBONDO JAWA TIMUR', 0, 1, 'L', false);

        $pdf->Ln(4);
        $pdf->SetFont('arial', '', 8);
        $pdf->Cell(40, 0, 'Po Box 2 telp 0388-452666 Fax. 452707 - eMail : sentral@salafiyah.net - Situbondo, 68374', 0, 0, 'L', false);

        $pdf->Line(9, 23, 340, 23);
        $pdf->Line(9, 27, 340, 27);

        $pdf->Ln(8);
        $pdf->SetFont('arial', 'B', 12);
        $pdf->Cell(0, 5, 'RINCIAN BAROKAH SATPAM', 0, 1, 'C');
        $pdf->SetFont('arial', '', 11);
        $pdf->Cell(0, 5, 'Bulan : ' . ($periode->bulan ?? '') . ' ' . ($periode->tahun ?? ''), 0, 1, 'C');

        $pdf->Ln(5);

        // Header Table
        $pdf->SetFont('arial', 'B', 8);
        $x = $pdf->GetX();
        $y = $pdf->GetY();

        $pdf->Cell(8, 10, 'No', 1, 0, 'C');
        $pdf->Cell(90, 10, 'Nama Satpam', 1, 0, 'C');

        $x_group = $pdf->GetX();
        $pdf->Cell(45, 5, 'Kehadiran 1 (Siang)', 1, 0, 'C');
        $pdf->Cell(60, 5, 'Kehadiran 2 (Shift)', 1, 0, 'C');
        $pdf->Cell(45, 5, 'Kehadiran 3 (Dinihari)', 1, 0, 'C');

        $pdf->Cell(35, 10, 'Danru', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Total Diterima', 1, 0, 'C');

        $pdf->SetXY($x_group, $y + 5);
        // Group 1
        $pdf->Cell(10, 5, 'H', 1, 0, 'C');
        $pdf->Cell(15, 5, 'Trns', 1, 0, 'C');
        $pdf->Cell(20, 5, 'Jml', 1, 0, 'C');
        // Group 2
        $pdf->Cell(10, 5, 'S', 1, 0, 'C');
        $pdf->Cell(20, 5, 'Rank', 1, 0, 'C');
        $pdf->Cell(30, 5, 'Jml', 1, 0, 'C');
        // Group 3
        $pdf->Cell(10, 5, 'D', 1, 0, 'C');
        $pdf->Cell(15, 5, 'Kons', 1, 0, 'C');
        $pdf->Cell(20, 5, 'Jml', 1, 0, 'C');

        $pdf->SetXY($x, $y + 10);

        // ISI DATA
        $pdf->SetFont('arial', '', 8);
        $no = 1;
        $rank = 17500;
        $konsumsi = 5000;

        $grand_transport = 0;
        $grand_barokah   = 0;
        $grand_dinihari  = 0;
        $grand_danru     = 0;
        $grand_total     = 0;

        foreach ($isilist as $row) {
            $nominal_transport = (int)($row->nominal_transport ?? 0);
            $jHari  = (int)($row->jumlah_hari ?? 0);
            $jShift = (int)($row->jumlah_shift ?? 0);
            $jDini  = (int)($row->jumlah_dinihari ?? 0);

            $jumlah_transport = $jHari * $nominal_transport;
            $jumlah_barokah   = $jShift * $rank;
            $jumlah_konsumsi  = $jDini * $konsumsi;

            $isDanru = (isset($row->is_danru) && (string)$row->is_danru === '1');
            $nominalDanruRow = $isDanru ? $honorDanru : 0;

            $diterima = $jumlah_transport + $jumlah_barokah + $jumlah_konsumsi + $nominalDanruRow;

            $grand_transport += $jumlah_transport;
            $grand_barokah   += $jumlah_barokah;
            $grand_dinihari  += $jumlah_konsumsi;
            $grand_danru     += $nominalDanruRow;
            $grand_total     += $diterima;

            $namaLengkap = trim(($row->gelar_depan ?? '') . ' ' . ($row->nama_lengkap ?? '') . ' ' . ($row->gelar_belakang ?? ''));

            $pdf->Cell(8, 7, $no++, 1, 0, 'C');
            $pdf->Cell(90, 7, $namaLengkap, 1, 0, 'L');

            // Kehadiran 1
            $pdf->Cell(10, 7, $jHari, 1, 0, 'C');
            $pdf->Cell(15, 7, number_format($nominal_transport, 0, ',', '.'), 1, 0, 'R');
            $pdf->Cell(20, 7, number_format($jumlah_transport, 0, ',', '.'), 1, 0, 'R');

            // Kehadiran 2
            $pdf->Cell(10, 7, $jShift, 1, 0, 'C');
            $pdf->Cell(20, 7, number_format($rank, 0, ',', '.'), 1, 0, 'R');
            $pdf->Cell(30, 7, number_format($jumlah_barokah, 0, ',', '.'), 1, 0, 'R');

            // Kehadiran 3
            $pdf->Cell(10, 7, $jDini, 1, 0, 'C');
            $pdf->Cell(15, 7, number_format($konsumsi, 0, ',', '.'), 1, 0, 'R');
            $pdf->Cell(20, 7, number_format($jumlah_konsumsi, 0, ',', '.'), 1, 0, 'R');

            // Danru
            $pdf->Cell(35, 7, number_format($nominalDanruRow, 0, ',', '.'), 1, 0, 'R');

            // Total
            $pdf->SetFont('arial', 'B', 8);
            $pdf->Cell(40, 7, number_format($diterima, 0, ',', '.'), 1, 1, 'R');
            $pdf->SetFont('arial', '', 8);
        }

        // FOOTER TOTAL
        $pdf->SetFont('arial', 'B', 8);
        $pdf->Cell(98, 7, 'TOTAL', 1, 0, 'C');

        // Kehadiran 1
        $pdf->Cell(10, 7, '', 1, 0, 'C');
        $pdf->Cell(15, 7, '', 1, 0, 'R');
        $pdf->Cell(20, 7, number_format($grand_transport, 0, ',', '.'), 1, 0, 'R');

        // Kehadiran 2
        $pdf->Cell(10, 7, '', 1, 0, 'C');
        $pdf->Cell(20, 7, '', 1, 0, 'R');
        $pdf->Cell(30, 7, number_format($grand_barokah, 0, ',', '.'), 1, 0, 'R');

        // Kehadiran 3
        $pdf->Cell(10, 7, '', 1, 0, 'C');
        $pdf->Cell(15, 7, '', 1, 0, 'R');
        $pdf->Cell(20, 7, number_format($grand_dinihari, 0, ',', '.'), 1, 0, 'R');

        // Danru
        $pdf->Cell(35, 7, number_format($grand_danru, 0, ',', '.'), 1, 0, 'R');

        // Grand Total
        $pdf->Cell(40, 7, number_format($grand_total, 0, ',', '.'), 1, 1, 'R');

        // TTD
        $pdf->Ln(10);
        $pdf->SetFont('arial', '', 9);
        $pdf->Cell(310, 5, 'Situbondo, ' . date('d') . ' ' . self::getNamaBulanIndo(date('n')) . ' ' . date('Y'), 0, 1, 'R');
        $pdf->Cell(310, 5, 'Bendahara,', 0, 1, 'R');
        $pdf->Ln(20);
        $pdf->SetFont('arial', 'B', 9);
        $pdf->Cell(310, 5, '( ..................................... )', 0, 1, 'R');

        return $pdf->Output('S');
    }
}

