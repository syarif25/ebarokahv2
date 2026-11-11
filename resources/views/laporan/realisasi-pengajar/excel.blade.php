<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Realisasi Barokah Pengajar Per Bidang - {{ $periodeStr }}</title>
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
            <td colspan="18" class="header-title">
                PONDOK PESANTREN SALAFIYAH SYAFI'IYAH SUKOREJO
            </td>
        </tr>
        <tr>
            <td colspan="18" class="header-sub">
                LAPORAN REALISASI BAROKAH TENAGA PENGAJAR (GURU & DOSEN) PER BIDANG
            </td>
        </tr>
        <tr><td></td></tr>
        <tr>
            <td colspan="2"><strong>Periode</strong></td>
            <td colspan="5">: {{ $periodeStr }}</td>
            <td colspan="2"><strong>Filter Bidang</strong></td>
            <td colspan="9">: {{ $selectedBidang ?: 'Semua Bidang' }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Tanggal Unduh</strong></td>
            <td colspan="5">: {{ date('d-m-Y H:i:s') }}</td>
            <td colspan="11"></td>
        </tr>
        <tr><td></td></tr>
    </table>

    <!-- TABEL 1: REKAPITULASI BAROKAH PENGAJAR PER BIDANG -->
    <table>
        <tr>
            <td colspan="18" class="section-title"><strong>I. REKAPITULASI REALISASI BAROKAH PENGAJAR PER BIDANG</strong></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 110px;">Periode</th>
                <th style="width: 200px;">Nama Bidang</th>
                <th style="width: 100px;">Mengajar</th>
                <th style="width: 90px;">DTY</th>
                <th style="width: 90px;">Jafung</th>
                <th style="width: 90px;">Transport</th>
                <th style="width: 80px;">Hadir 15</th>
                <th style="width: 80px;">Hadir 10</th>
                <th style="width: 80px;">Piket</th>
                <th style="width: 90px;">Tunkel</th>
                <th style="width: 90px;">Tunj Anak</th>
                <th style="width: 90px;">Kehormatan</th>
                <th style="width: 90px;">Walkes</th>
                <th style="width: 100px;">Tambahan</th>
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
                    <td class="num-format">{{ (float)$row->mengajar }}</td>
                    <td class="num-format">{{ (float)$row->dty }}</td>
                    <td class="num-format">{{ (float)$row->jafung }}</td>
                    <td class="num-format">{{ (float)$row->transport }}</td>
                    <td class="num-format">{{ (float)$row->hadir_15 }}</td>
                    <td class="num-format">{{ (float)$row->hadir_10 }}</td>
                    <td class="num-format">{{ (float)$row->piket }}</td>
                    <td class="num-format">{{ (float)$row->tunkel }}</td>
                    <td class="num-format">{{ (float)$row->tunj_anak }}</td>
                    <td class="num-format">{{ (float)$row->kehormatan }}</td>
                    <td class="num-format">{{ (float)$row->walkes }}</td>
                    <td class="num-format">{{ (float)$row->tambahan }}</td>
                    <td class="num-format text-bold">{{ (float)$row->jumlah }}</td>
                    <td class="num-format">{{ (float)$row->potongan }}</td>
                    <td class="num-format text-bold">{{ (float)$row->diterima }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="18" class="text-center">Tidak ada data realisasi barokah pengajar</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <th colspan="3" class="text-center">TOTAL KESELURUHAN</th>
                <th class="num-format">{{ $totals['total_mengajar'] }}</th>
                <th class="num-format">{{ $totals['total_dty'] }}</th>
                <th class="num-format">{{ $totals['total_jafung'] }}</th>
                <th class="num-format">{{ $totals['total_transport'] }}</th>
                <th class="num-format">{{ $totals['total_hadir_15'] }}</th>
                <th class="num-format">{{ $totals['total_hadir_10'] }}</th>
                <th class="num-format">{{ $totals['total_piket'] }}</th>
                <th class="num-format">{{ $totals['total_tunkel'] }}</th>
                <th class="num-format">{{ $totals['total_tunj_anak'] }}</th>
                <th class="num-format">{{ $totals['total_kehormatan'] }}</th>
                <th class="num-format">{{ $totals['total_walkes'] }}</th>
                <th class="num-format">{{ $totals['total_tambahan'] }}</th>
                <th class="num-format">{{ $totals['total_jumlah'] }}</th>
                <th class="num-format">{{ $totals['total_potongan'] }}</th>
                <th class="num-format">{{ $totals['total_diterima'] }}</th>
            </tr>
        </tfoot>
    </table>

    <br>

    <!-- TABEL 2: RINCIAN REALISASI BAROKAH PENGAJAR PER LEMBAGA -->
    <table>
        <tr>
            <td colspan="18" class="section-title"><strong>II. RINCIAN REALISASI BAROKAH PENGAJAR PER LEMBAGA</strong></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 180px;">Nama Bidang</th>
                <th style="width: 220px;">Nama Lembaga</th>
                <th style="width: 100px;">Mengajar</th>
                <th style="width: 90px;">DTY</th>
                <th style="width: 90px;">Jafung</th>
                <th style="width: 90px;">Transport</th>
                <th style="width: 80px;">Hadir 15</th>
                <th style="width: 80px;">Hadir 10</th>
                <th style="width: 80px;">Piket</th>
                <th style="width: 90px;">Tunkel</th>
                <th style="width: 90px;">Tunj Anak</th>
                <th style="width: 90px;">Kehormatan</th>
                <th style="width: 90px;">Walkes</th>
                <th style="width: 100px;">Tambahan</th>
                <th style="width: 110px;">Jumlah</th>
                <th style="width: 90px;">Potongan</th>
                <th style="width: 110px;">Diterima</th>
            </tr>
        </thead>
        <tbody>
            @php
                $subMengajar = 0; $subDty = 0; $subJafung = 0; $subTransport = 0; $subHadir15 = 0; $subHadir10 = 0; $subPiket = 0;
                $subTunkel = 0; $subTunjAnak = 0; $subKehormatan = 0; $subWalkes = 0; $subTambahan = 0; $subJumlah = 0; $subPotongan = 0; $subDiterima = 0;
            @endphp
            @forelse($dataLembagaRaw as $idx => $lem)
                @php
                    $subMengajar += (float)$lem->mengajar;
                    $subDty += (float)$lem->dty;
                    $subJafung += (float)$lem->jafung;
                    $subTransport += (float)$lem->transport;
                    $subHadir15 += (float)$lem->hadir_15;
                    $subHadir10 += (float)$lem->hadir_10;
                    $subPiket += (float)$lem->piket;
                    $subTunkel += (float)$lem->tunkel;
                    $subTunjAnak += (float)$lem->tunj_anak;
                    $subKehormatan += (float)$lem->kehormatan;
                    $subWalkes += (float)$lem->walkes;
                    $subTambahan += (float)$lem->tambahan;
                    $subJumlah += (float)$lem->jumlah;
                    $subPotongan += (float)$lem->potongan;
                    $subDiterima += (float)$lem->diterima;
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-start">{{ $lem->nama_bidang }}</td>
                    <td class="text-start"><strong>{{ $lem->nama_lembaga }}</strong></td>
                    <td class="num-format">{{ (float)$lem->mengajar }}</td>
                    <td class="num-format">{{ (float)$lem->dty }}</td>
                    <td class="num-format">{{ (float)$lem->jafung }}</td>
                    <td class="num-format">{{ (float)$lem->transport }}</td>
                    <td class="num-format">{{ (float)$lem->hadir_15 }}</td>
                    <td class="num-format">{{ (float)$lem->hadir_10 }}</td>
                    <td class="num-format">{{ (float)$lem->piket }}</td>
                    <td class="num-format">{{ (float)$lem->tunkel }}</td>
                    <td class="num-format">{{ (float)$lem->tunj_anak }}</td>
                    <td class="num-format">{{ (float)$lem->kehormatan }}</td>
                    <td class="num-format">{{ (float)$lem->walkes }}</td>
                    <td class="num-format">{{ (float)$lem->tambahan }}</td>
                    <td class="num-format text-bold">{{ (float)$lem->jumlah }}</td>
                    <td class="num-format">{{ (float)$lem->potongan }}</td>
                    <td class="num-format text-bold">{{ (float)$lem->diterima }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="18" class="text-center">Tidak ada data rincian lembaga pengajar</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <th colspan="3" class="text-center">TOTAL RINCIAN LEMBAGA</th>
                <th class="num-format">{{ $subMengajar }}</th>
                <th class="num-format">{{ $subDty }}</th>
                <th class="num-format">{{ $subJafung }}</th>
                <th class="num-format">{{ $subTransport }}</th>
                <th class="num-format">{{ $subHadir15 }}</th>
                <th class="num-format">{{ $subHadir10 }}</th>
                <th class="num-format">{{ $subPiket }}</th>
                <th class="num-format">{{ $subTunkel }}</th>
                <th class="num-format">{{ $subTunjAnak }}</th>
                <th class="num-format">{{ $subKehormatan }}</th>
                <th class="num-format">{{ $subWalkes }}</th>
                <th class="num-format">{{ $subTambahan }}</th>
                <th class="num-format">{{ $subJumlah }}</th>
                <th class="num-format">{{ $subPotongan }}</th>
                <th class="num-format">{{ $subDiterima }}</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>

