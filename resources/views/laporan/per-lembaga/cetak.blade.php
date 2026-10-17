<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Barokah - {{ $lembaga->nama_lembaga ?? 'Lembaga' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
            padding: 20px;
        }
        
        .header {
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 3px double #000;
            text-align: center;
        }

        .header h1 {
            font-size: 15pt;
            font-weight: bold;
            margin: 0 0 2px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .header h2 {
            font-size: 12pt;
            font-weight: bold;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        
        .header .address {
            font-size: 8pt;
            margin: 0;
            line-height: 1.3;
        }
        
        .report-title {
            text-align: center;
            margin: 12px 0 8px 0;
        }
        
        .report-title h3 {
            font-size: 12pt;
            font-weight: bold;
            margin: 0;
            text-decoration: underline;
            text-transform: uppercase;
        }
        
        .report-info {
            display: flex;
            justify-content: space-between;
            margin: 10px 0 12px 0;
            font-size: 9.5pt;
        }

        .report-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .report-info td {
            padding: 2px 0;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 8.5pt;
        }
        
        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
        }
        
        table.data-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .text-center { text-align: center; }
        .text-right, .text-end { text-align: right; }
        .text-bold, .font-weight-bold { font-weight: bold; }
        
        .footer-note {
            margin-top: 15px;
            font-size: 8pt;
            display: flex;
            justify-content: space-between;
        }

        .signature-block {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 200px;
            text-align: center;
            font-size: 9.5pt;
        }

        .signature-space {
            height: 60px;
        }

        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="background-color: #00a15d; color: white; border: none; padding: 8px 18px; border-radius: 6px; font-weight: bold; cursor: pointer;">
            Cetak Dokumen
        </button>
    </div>

    <div class="header">
        <h1>PONDOK PESANTREN SALAFIYAH SYAFI'IYAH</h1>
        <h2>SUKOREJO SITUBONDO JAWA TIMUR</h2>
        <p class="address">Sumberejo, Banyuputih, Kabupaten Situbondo, Jawa Timur 68374</p>
    </div>

    <div class="report-title">
        <h3>LAPORAN RINCIAN BAROKAH LEMBAGA</h3>
    </div>

    <div class="report-info">
        <table style="width: 100%;">
            <tr>
                <td style="width: 15%;"><strong>Nama Lembaga</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 43%;">{{ $lembaga->nama_lembaga }} - {{ $kategori }}</td>
                <td style="width: 15%;"><strong>Periode</strong></td>
                <td style="width: 2%;">:</td>
                <td style="width: 23%;">{{ $lembaga->bulan }} {{ $lembaga->tahun }}</td>
            </tr>
            <tr>
                <td><strong>Kategori</strong></td>
                <td>:</td>
                <td>{{ $kategori }}</td>
                <td><strong>Status</strong></td>
                <td>:</td>
                <td>Selesai </td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        @if($kategori === 'Struktural')
            <thead>
                <tr>
                    <th rowspan="2" style="width: 30px;">No</th>
                    <th rowspan="2">Nama Lengkap</th>
                    <th rowspan="2">Jabatan</th>
                    <th rowspan="2">TMT</th>
                    <th rowspan="2">Tunjab</th>
                    <th rowspan="2">MP</th>
                    <th colspan="2">Kehadiran</th>
                    <th rowspan="2">Tunkel</th>
                    <th rowspan="2">Tunj Anak</th>
                    <th rowspan="2">TBK</th>
                    <th rowspan="2">TMP</th>
                    <th rowspan="2">Kehormatan</th>
                    <th rowspan="2">Potongan</th>
                    <th rowspan="2">Diterima</th>
                </tr>
                <tr>
                    <th>Hari</th>
                    <th>Nominal</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totTunjab = 0; $totHadirNom = 0; $totTunkel = 0; $totTunAnak = 0;
                    $totTbk = 0; $totTmp = 0; $totKehormatan = 0; $totPotongan = 0; $totDiterima = 0;
                @endphp
                @foreach($rows as $r)
                    @php
                        $namaLengkap = trim(($r->gelar_depan ? $r->gelar_depan . ' ' : '') . $r->nama_lengkap . ($r->gelar_belakang ? ', ' . $r->gelar_belakang : ''));
                        $totTunjab += (float)$r->barokah;
                        $totHadirNom += (float)$r->nominal_kehadiran;
                        $totTunkel += (float)$r->tunkel;
                        $totTunAnak += (float)$r->tunj_anak;
                        $totTbk += (float)$r->tbk;
                        $totTmp += (float)$r->tmp;
                        $totKehormatan += (float)$r->kehormatan;
                        $totPotongan += (float)$r->potongan;
                        $totDiterima += (float)$r->diterima;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $namaLengkap }}</td>
                        <td>{{ $r->nama_jabatan }}</td>
                        <td class="text-center">{{ substr((string)$r->tmt_struktural, 0, 4) }}</td>
                        <td class="text-end">{{ number_format((float)$r->barokah, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $r->mp }}</td>
                        <td class="text-center">{{ $r->jumlah_hadir }}</td>
                        <td class="text-end">{{ number_format((float)$r->nominal_kehadiran, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format((float)$r->tunkel, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format((float)$r->tunj_anak, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format((float)$r->tbk, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format((float)$r->tmp, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format((float)$r->kehormatan, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format((float)$r->potongan, 0, ',', '.') }}</td>
                        <td class="text-end text-bold">{{ number_format((float)$r->diterima, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f0f0f0; font-weight: bold;">
                    <td colspan="4" class="text-center">TOTAL</td>
                    <td class="text-end">{{ number_format($totTunjab, 0, ',', '.') }}</td>
                    <td></td>
                    <td></td>
                    <td class="text-end">{{ number_format($totHadirNom, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($totTunkel, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($totTunAnak, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($totTbk, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($totTmp, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($totKehormatan, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($totPotongan, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($totDiterima, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @elseif($kategori === 'Pengajar')
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Nama Lengkap</th>
                    <th>Kategori</th>
                    <th>SKS</th>
                    <th>Kehadiran</th>
                    <th>Tunkel</th>
                    <th>Tunj Anak</th>
                    <th>Kehormatan</th>
                    <th>Walkes</th>
                    <th>Barokah Tambahan</th>
                    <th>Potongan</th>
                    <th>Diterima</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totTunkelP = 0; $totTunAnakP = 0; $totKehormatanP = 0;
                    $totWalkesP = 0; $totTambahanP = 0;
                    $totDiterimaP = 0; $totPotonganP = 0;
                @endphp
                @foreach($rows as $r)
                    @php
                        $namaLengkap = trim(($r->gelar_depan ? $r->gelar_depan . ' ' : '') . $r->nama_lengkap . ($r->gelar_belakang ? ', ' . $r->gelar_belakang : ''));
                        $totTunkelP += (float)$r->tunkel;
                        $totTunAnakP += (float)$r->tunj_anak;
                        $totKehormatanP += (float)$r->kehormatan;
                        $totWalkesP += (float)$r->walkes;
                        $totTambahanP += (float)$r->khusus;
                        $totDiterimaP += (float)$r->diterima;
                        $totPotonganP += (float)$r->potongan;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $namaLengkap }}</td>
                        <td class="text-center">{{ $r->nama_jabatan }}</td>
                        <td class="text-center">{{ $r->jumlah_sks }} SKS</td>
                        <td class="text-center">{{ $r->jumlah_hadir }}</td>
                        <td class="text-end">{{ number_format((float)$r->tunkel, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format((float)$r->tunj_anak, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format((float)$r->kehormatan, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format((float)$r->walkes, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format((float)$r->khusus, 0, ',', '.') }}</td>
                        <td class="text-end">{{ number_format((float)$r->potongan, 0, ',', '.') }}</td>
                        <td class="text-end text-bold">{{ number_format((float)$r->diterima, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f0f0f0; font-weight: bold;">
                    <td colspan="5" class="text-center">TOTAL</td>
                    <td class="text-end">{{ number_format($totTunkelP, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($totTunAnakP, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($totKehormatanP, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($totWalkesP, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($totTambahanP, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($totPotonganP, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($totDiterimaP, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @else
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Nama Anggota</th>
                    <th>Kehadiran (Hari)</th>
                    <th>Potongan</th>
                    <th>Diterima</th>
                </tr>
            </thead>
            <tbody>
                @php $totDiterimaS = 0; $totPotonganS = 0; @endphp
                @foreach($rows as $r)
                    @php
                        $namaLengkap = trim(($r->gelar_depan ? $r->gelar_depan . ' ' : '') . $r->nama_lengkap . ($r->gelar_belakang ? ', ' . $r->gelar_belakang : ''));
                        $totDiterimaS += (float)$r->diterima;
                        $totPotonganS += (float)$r->potongan;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $namaLengkap }}</td>
                        <td class="text-center">{{ $r->jumlah_hadir }}</td>
                        <td class="text-end">{{ number_format((float)$r->potongan, 0, ',', '.') }}</td>
                        <td class="text-end text-bold">{{ number_format((float)$r->diterima, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #f0f0f0; font-weight: bold;">
                    <td colspan="3" class="text-center">TOTAL</td>
                    <td class="text-end">{{ number_format($totPotonganS, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($totDiterimaS, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="signature-block">
        <div class="signature-box">
            <p>Mengetahui,</p>
            <p><strong>Pimpinan Lembaga</strong></p>
            <div class="signature-space"></div>
            <p>( ................................................. )</p>
        </div>
        <div class="signature-box">
            <p>Sukorejo, {{ date('d F Y') }}</p>
            <p><strong>Bendahara Pesantren</strong></p>
            <div class="signature-space"></div>
            <p>( ................................................. )</p>
        </div>
    </div>

    <div class="footer-note">
        <span>Dicetak secara otomatis oleh Sistem E-Barokah Payroll</span>
        <span>Halaman 1 dari 1</span>
    </div>
</body>
</html>
