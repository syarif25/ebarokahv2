<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Amprah Barokah Kepanitiaan - {{ $transaksi->nama_event }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #111;
            margin: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header h3 {
            margin: 0;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header h4 {
            margin: 3px 0;
            font-size: 13px;
        }
        .header p {
            margin: 0;
            font-size: 11px;
            color: #444;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 11px;
        }
        .meta-table td {
            padding: 2px 4px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #333;
            padding: 5px 4px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            text-align: center;
            font-weight: bold;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .signature-table {
            width: 100%;
            margin-top: 35px;
            page-break-inside: avoid;
        }
        .signature-table td {
            text-align: center;
            width: 33%;
            vertical-align: top;
        }
        @media print {
            body { margin: 0; }
            @page {
                size: A4 landscape;
                margin: 10mm;
            }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 6px 16px; font-weight: bold; cursor: pointer; background: #00a15d; color: white; border: none; border-radius: 4px;">
            Cetak Dokumen (Print / PDF)
        </button>
        <button onclick="window.close()" style="padding: 6px 12px; cursor: pointer; margin-left: 8px;">
            Tutup
        </button>
    </div>

    <!-- KOP RESMI -->
    <div class="header">
        <h3>PONDOK PESANTREN SALAFIYAH SYAFI'IYAH SUKOREJO SITUBONDO</h3>
        <h4>DAFTAR PENERIMAAN BAROKAH KEPANITIAAN (APBM T.A. {{ $transaksi->tahun }})</h4>
        <p>{{ $transaksi->nama_event }}</p>
    </div>

    <!-- META EVENT -->
    <table class="meta-table">
        <tr>
            <td style="width: 15%;"><strong>Jenis Kegiatan</strong></td>
            <td style="width: 35%;">: {{ $transaksi->jenisKegiatan->nama_kegiatan ?? '-' }}</td>
            <td style="width: 15%;"><strong>Bulan / Periode</strong></td>
            <td style="width: 35%;">: {{ $transaksi->bulan }} {{ $transaksi->tahun }}</td>
        </tr>
        <tr>
            <td><strong>Lembaga Pelaksana</strong></td>
            <td>: {{ $transaksi->lembaga ? $transaksi->lembaga->nama_lembaga : 'Lintas Lembaga' }}</td>
            <td><strong>Status Dokumen</strong></td>
            <td>: {{ strtoupper($transaksi->status) }}</td>
        </tr>
    </table>

    <!-- 10-COLUMN DATA TABLE -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="min-width: 130px;">Nama Panitia</th>
                <th>Kepanitiaan</th>
                <th>Kehadiran</th>
                <th>Konsumsi</th>
                <th>Pemb Soal</th>
                <th>Input Soal</th>
                <th>Pengawas</th>
                <th>Penguji</th>
                <th>Korektor</th>
                <th>Input Nilai</th>
                <th>Pembimbing</th>
                <th>Jumlah</th>
                <th>Potongan</th>
                <th>Diterima</th>
                <th style="width: 80px;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($details as $idx => $d)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>
                    <strong>{{ $d->nama_panitia }}</strong>
                    @if($d->nik)<br><span style="color:#555; font-size:9px;">NIK: {{ $d->nik }}</span>@endif
                </td>
                <td class="text-right">
                    {{ $d->nominal_kepanitiaan > 0 ? number_format($d->nominal_kepanitiaan, 0, ',', '.') : '-' }}
                    @if($d->jabatan_panitia)<br><small style="color:#666;">({{ $d->jabatan_panitia }})</small>@endif
                </td>
                <td class="text-right">{{ $d->nominal_kehadiran > 0 ? number_format($d->nominal_kehadiran, 0, ',', '.') : '-' }}</td>
                <td class="text-right">{{ $d->nominal_konsumsi > 0 ? number_format($d->nominal_konsumsi, 0, ',', '.') : '-' }}</td>
                <td class="text-right">{{ $d->nominal_pemb_soal > 0 ? number_format($d->nominal_pemb_soal, 0, ',', '.') : '-' }}</td>
                <td class="text-right">{{ $d->nominal_input_soal > 0 ? number_format($d->nominal_input_soal, 0, ',', '.') : '-' }}</td>
                <td class="text-right">{{ $d->nominal_pengawas > 0 ? number_format($d->nominal_pengawas, 0, ',', '.') : '-' }}</td>
                <td class="text-right">{{ $d->nominal_penguji > 0 ? number_format($d->nominal_penguji, 0, ',', '.') : '-' }}</td>
                <td class="text-right">{{ $d->nominal_korektor > 0 ? number_format($d->nominal_korektor, 0, ',', '.') : '-' }}</td>
                <td class="text-right">{{ $d->nominal_input_nilai > 0 ? number_format($d->nominal_input_nilai, 0, ',', '.') : '-' }}</td>
                <td class="text-right">{{ $d->nominal_pembimbing > 0 ? number_format($d->nominal_pembimbing, 0, ',', '.') : '-' }}</td>
                <td class="text-right font-bold">{{ number_format($d->total_bruto, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #b91c1c;">{{ $d->potongan > 0 ? number_format($d->potongan, 0, ',', '.') : '-' }}</td>
                <td class="text-right font-bold">{{ number_format($d->total_diterima, 0, ',', '.') }}</td>
                <td style="height: 30px; vertical-align: bottom; font-size: 8px; color: #888;">
                    {{ $idx + 1 }}. ............
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="2" class="text-center">TOTAL</td>
                <td class="text-right">{{ number_format($details->sum('nominal_kepanitiaan'), 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($details->sum('nominal_kehadiran'), 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($details->sum('nominal_konsumsi'), 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($details->sum('nominal_pemb_soal'), 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($details->sum('nominal_input_soal'), 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($details->sum('nominal_pengawas'), 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($details->sum('nominal_penguji'), 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($details->sum('nominal_korektor'), 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($details->sum('nominal_input_nilai'), 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($details->sum('nominal_pembimbing'), 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($details->sum('total_bruto'), 0, ',', '.') }}</td>
                <td class="text-right" style="color: #b91c1c;">{{ number_format($details->sum('potongan'), 0, ',', '.') }}</td>
                <td class="text-right" style="font-size: 11px;">Rp {{ number_format($details->sum('total_diterima'), 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- SIGNATURE BLOCKS -->
    <table class="signature-table">
        <tr>
            <td>
                Mengetahui,<br>
                <strong>Penanggung Jawab / Pimpinan</strong><br><br><br><br>
                ( .................................................... )
            </td>
            <td>
                <br>
                <strong>Ketua Panitia</strong><br><br><br><br>
                ( .................................................... )
            </td>
            <td>
                Sukorejo, {{ date('d F Y') }}<br>
                <strong>Bendahara Panitia</strong><br><br><br><br>
                ( .................................................... )
            </td>
        </tr>
    </table>
</body>
</html>
