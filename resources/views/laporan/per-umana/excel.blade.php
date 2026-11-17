<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Barokah Per Umana - {{ $periodeStr }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #000;
        }
        .header-title {
            text-align: center;
            font-size: 14pt;
            font-weight: bold;
        }
        .header-sub {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
        }
        .data-table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000000;
            padding: 5px 8px;
            font-size: 10pt;
            vertical-align: middle;
        }
        .data-table th {
            background-color: #d1fae5;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-start { text-align: left; }
        .text-end { text-align: right; }
        .text-bold { font-weight: bold; }
        .num-format {
            mso-number-format: "\#\,\#\#0";
            text-align: right;
        }
        .text-format {
            mso-number-format: "\@";
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="9" class="header-title">PONDOK PESANTREN SALAFIYAH SYAFI'IYAH SUKOREJO</td>
        </tr>
        <tr>
            <td colspan="9" class="header-sub">LAPORAN BAROKAH PER UMANA'</td>
        </tr>
        <tr>
            <td colspan="9" style="text-align: center; font-size: 10pt;">
                Periode: <strong>{{ $periodeStr }}</strong> &nbsp;|&nbsp; Lembaga: <strong>{{ $namaLembagaTerpilih }}</strong>
            </td>
        </tr>
        <tr><td colspan="9"></td></tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 150px;">NIK</th>
                <th style="width: 250px;">Nama Lengkap</th>
                <th style="width: 250px;">Lembaga / Unit Tugas</th>
                <th style="width: 150px;">Kategori</th>
                <th style="width: 130px;">Barokah Struktural</th>
                <th style="width: 130px;">Barokah Pengajar</th>
                <th style="width: 130px;">Barokah Satpam</th>
                <th style="width: 140px;">Grand Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totStruktural = 0;
                $totPengajar = 0;
                $totSatpam = 0;
                $totGrand = 0;
            @endphp
            @forelse($list as $i => $item)
                @php
                    $nama = trim(($item->gelar_depan ? $item->gelar_depan . ' ' : '') . $item->nama_lengkap . ($item->gelar_belakang ? ', ' . $item->gelar_belakang : ''));
                    $totStruktural += (float)$item->total_struktural;
                    $totPengajar += (float)$item->total_pengajar;
                    $totSatpam += (float)$item->total_satpam;
                    $totGrand += (float)$item->grand_total;
                @endphp
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td class="text-format">{{ $item->nik }}</td>
                    <td>{{ $nama }}</td>
                    <td>{{ $item->daftar_lembaga ?: '-' }}</td>
                    <td class="text-center">{{ $item->daftar_kategori ?: '-' }}</td>
                    <td class="num-format">{{ (float)$item->total_struktural }}</td>
                    <td class="num-format">{{ (float)$item->total_pengajar }}</td>
                    <td class="num-format">{{ (float)$item->total_satpam }}</td>
                    <td class="num-format text-bold">{{ (float)$item->grand_total }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-3">Tidak ada data barokah pada periode ini</td>
                </tr>
            @endforelse
        </tbody>
        @if(count($list) > 0)
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="5" class="text-center text-bold">TOTAL KESELURUHAN ({{ count($list) }} Umana')</td>
                <td class="num-format text-bold">{{ $totStruktural }}</td>
                <td class="num-format text-bold">{{ $totPengajar }}</td>
                <td class="num-format text-bold">{{ $totSatpam }}</td>
                <td class="num-format text-bold">{{ $totGrand }}</td>
            </tr>
        </tfoot>
        @endif
    </table>
</body>
</html>
