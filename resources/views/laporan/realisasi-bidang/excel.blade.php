<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Realisasi Barokah Per Bidang - {{ $periodeStr }}</title>
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
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        .data-table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 5px;
            margin-bottom: 20px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000000;
            padding: 5px 8px;
            font-size: 10pt;
            vertical-align: middle;
        }
        .data-table th {
            background-color: #e2e8f0;
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
            <td colspan="13" class="header-title">
                PONDOK PESANTREN SALAFIYAH SYAFI'IYAH SUKOREJO
            </td>
        </tr>
        <tr>
            <td colspan="13" class="header-sub">
                LAPORAN REALISASI BAROKAH PER BIDANG (STRUKTURAL)
            </td>
        </tr>
        <tr><td></td></tr>
        <tr>
            <td colspan="2"><strong>Periode</strong></td>
            <td colspan="4">: {{ $periodeStr }}</td>
            <td colspan="2"><strong>Filter Bidang</strong></td>
            <td colspan="5">: {{ $selectedBidang ?: 'Semua Bidang' }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Tanggal Unduh</strong></td>
            <td colspan="4">: {{ date('d-m-Y H:i:s') }}</td>
            <td colspan="7"></td>
        </tr>
        <tr><td></td></tr>
    </table>

    <!-- TABEL 1: REKAPITULASI BAROKAH PER BIDANG -->
    <table>
        <tr>
            <td colspan="13" class="section-title"><strong>I. REKAPITULASI REALISASI BAROKAH PER BIDANG</strong></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 110px;">Periode</th>
                <th style="width: 220px;">Nama Bidang</th>
                <th style="width: 100px;">Tunjab</th>
                <th style="width: 80px;">TMP</th>
                <th style="width: 100px;">Trans / Khdr</th>
                <th style="width: 90px;">Tunkel</th>
                <th style="width: 90px;">Tunj Anak</th>
                <th style="width: 90px;">Kehormatan</th>
                <th style="width: 100px;">TBK</th>
                <th style="width: 110px;">Jumlah</th>
                <th style="width: 90px;">Potongan</th>
                <th style="width: 110px;">Diterima</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataBidangRaw as $idx => $row)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center">{{ $periodeStr }}</td>
                    <td class="text-start"><strong>{{ $row->nama_bidang }}</strong></td>
                    <td class="num-format">{{ (float)$row->tunjab }}</td>
                    <td class="num-format">{{ (float)$row->tmp }}</td>
                    <td class="num-format">{{ (float)$row->kehadiran }}</td>
                    <td class="num-format">{{ (float)$row->tunkel }}</td>
                    <td class="num-format">{{ (float)$row->tunj_anak }}</td>
                    <td class="num-format">{{ (float)$row->kehormatan }}</td>
                    <td class="num-format">{{ (float)$row->tbk }}</td>
                    <td class="num-format text-bold">{{ (float)$row->jumlah }}</td>
                    <td class="num-format">{{ (float)$row->potongan }}</td>
                    <td class="num-format text-bold">{{ (float)$row->diterima }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center">Tidak ada data realisasi barokah</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <th colspan="3" class="text-center">TOTAL KESELURUHAN</th>
                <th class="num-format">{{ $totals['total_tunjab'] }}</th>
                <th class="num-format">{{ $totals['total_tmp'] }}</th>
                <th class="num-format">{{ $totals['total_kehadiran'] }}</th>
                <th class="num-format">{{ $totals['total_tunkel'] }}</th>
                <th class="num-format">{{ $totals['total_tunj_anak'] }}</th>
                <th class="num-format">{{ $totals['total_kehormatan'] }}</th>
                <th class="num-format">{{ $totals['total_tbk'] }}</th>
                <th class="num-format">{{ $totals['total_jumlah'] }}</th>
                <th class="num-format">{{ $totals['total_potongan'] }}</th>
                <th class="num-format">{{ $totals['total_diterima'] }}</th>
            </tr>
        </tfoot>
    </table>

    <br>

    <!-- TABEL 2: RINCIAN REALISASI BAROKAH PER LEMBAGA -->
    <table>
        <tr>
            <td colspan="13" class="section-title"><strong>II. RINCIAN REALISASI BAROKAH PER LEMBAGA</strong></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 180px;">Nama Bidang</th>
                <th style="width: 250px;">Nama Lembaga</th>
                <th style="width: 100px;">Tunjab</th>
                <th style="width: 80px;">TMP</th>
                <th style="width: 100px;">Kehadiran</th>
                <th style="width: 90px;">Tunkel</th>
                <th style="width: 90px;">Tunj Anak</th>
                <th style="width: 90px;">Kehormatan</th>
                <th style="width: 100px;">TBK</th>
                <th style="width: 110px;">Jumlah</th>
                <th style="width: 90px;">Potongan</th>
                <th style="width: 110px;">Diterima</th>
            </tr>
        </thead>
        <tbody>
            @php
                $subTunjab = 0; $subTmp = 0; $subKehadiran = 0; $subTunkel = 0; $subTunjAnak = 0;
                $subKehormatan = 0; $subTbk = 0; $subJumlah = 0; $subPotongan = 0; $subDiterima = 0;
            @endphp
            @forelse($dataLembagaRaw as $idx => $lem)
                @php
                    $subTunjab += (float)$lem->tunjab;
                    $subTmp += (float)$lem->tmp;
                    $subKehadiran += (float)$lem->kehadiran;
                    $subTunkel += (float)$lem->tunkel;
                    $subTunjAnak += (float)$lem->tunj_anak;
                    $subKehormatan += (float)$lem->kehormatan;
                    $subTbk += (float)$lem->tbk;
                    $subJumlah += (float)$lem->jumlah;
                    $subPotongan += (float)$lem->potongan;
                    $subDiterima += (float)$lem->diterima;
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-start">{{ $lem->nama_bidang }}</td>
                    <td class="text-start"><strong>{{ $lem->nama_lembaga }}</strong></td>
                    <td class="num-format">{{ (float)$lem->tunjab }}</td>
                    <td class="num-format">{{ (float)$lem->tmp }}</td>
                    <td class="num-format">{{ (float)$lem->kehadiran }}</td>
                    <td class="num-format">{{ (float)$lem->tunkel }}</td>
                    <td class="num-format">{{ (float)$lem->tunj_anak }}</td>
                    <td class="num-format">{{ (float)$lem->kehormatan }}</td>
                    <td class="num-format">{{ (float)$lem->tbk }}</td>
                    <td class="num-format text-bold">{{ (float)$lem->jumlah }}</td>
                    <td class="num-format">{{ (float)$lem->potongan }}</td>
                    <td class="num-format text-bold">{{ (float)$lem->diterima }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center">Tidak ada data rincian lembaga</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <th colspan="3" class="text-center">TOTAL RINCIAN LEMBAGA</th>
                <th class="num-format">{{ $subTunjab }}</th>
                <th class="num-format">{{ $subTmp }}</th>
                <th class="num-format">{{ $subKehadiran }}</th>
                <th class="num-format">{{ $subTunkel }}</th>
                <th class="num-format">{{ $subTunjAnak }}</th>
                <th class="num-format">{{ $subKehormatan }}</th>
                <th class="num-format">{{ $subTbk }}</th>
                <th class="num-format">{{ $subJumlah }}</th>
                <th class="num-format">{{ $subPotongan }}</th>
                <th class="num-format">{{ $subDiterima }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>

