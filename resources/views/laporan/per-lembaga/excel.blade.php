<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Barokah - {{ $lembaga->nama_lembaga }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
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
        .info-table {
            margin-top: 15px;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 3px 5px;
            font-size: 10pt;
        }
        .data-table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000000;
            padding: 5px 8px;
            font-size: 10pt;
        }
        .data-table th {
            background-color: #e2e8f0;
            font-weight: bold;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .text-bold { font-weight: bold; }
        .num-format {
            mso-number-format: "\#\,\#\#0";
        }
        .text-format {
            mso-number-format: "\@";
        }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="{{ $kategori === 'Struktural' ? 14 : ($kategori === 'Pengajar' ? 12 : 5) }}" class="header-title">
                PONDOK PESANTREN SALAFIYAH SYAFI'IYAH SUKOREJO
            </td>
        </tr>
        <tr>
            <td colspan="{{ $kategori === 'Struktural' ? 14 : ($kategori === 'Pengajar' ? 12 : 5) }}" class="header-sub">
                LAPORAN RINCIAN BAROKAH LEMBAGA
            </td>
        </tr>
        <tr><td></td></tr>
        <tr>
            <td colspan="2"><strong>Nama Lembaga</strong></td>
            <td colspan="4">: {{ $lembaga->nama_lembaga }} - {{ $kategori }}</td>
            <td colspan="2"><strong>Periode</strong></td>
            <td colspan="4">: {{ $lembaga->bulan }} {{ $lembaga->tahun }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Kategori</strong></td>
            <td colspan="4">: {{ $kategori }}</td>
            <td colspan="6"></td>
        </tr>
        <tr><td></td></tr>
    </table>

    <table class="data-table">
        @if($kategori === 'Struktural')
            <thead>
                <tr>
                    <th rowspan="2">No</th>
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
                        <td class="text-center text-format">{{ substr((string)$r->tmt_struktural, 0, 4) }}</td>
                        <td class="text-end num-format">{{ (float)$r->barokah }}</td>
                        <td class="text-center">{{ $r->mp }}</td>
                        <td class="text-center">{{ $r->jumlah_hadir }}</td>
                        <td class="text-end num-format">{{ (float)$r->nominal_kehadiran }}</td>
                        <td class="text-end num-format">{{ (float)$r->tunkel }}</td>
                        <td class="text-end num-format">{{ (float)$r->tunj_anak }}</td>
                        <td class="text-end num-format">{{ (float)$r->tbk }}</td>
                        <td class="text-end num-format">{{ (float)$r->tmp }}</td>
                        <td class="text-end num-format">{{ (float)$r->kehormatan }}</td>
                        <td class="text-end num-format">{{ (float)$r->potongan }}</td>
                        <td class="text-end text-bold num-format">{{ (float)$r->diterima }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #e2e8f0; font-weight: bold;">
                    <td colspan="4" class="text-center">TOTAL</td>
                    <td class="text-end num-format">{{ $totTunjab }}</td>
                    <td></td>
                    <td></td>
                    <td class="text-end num-format">{{ $totHadirNom }}</td>
                    <td class="text-end num-format">{{ $totTunkel }}</td>
                    <td class="text-end num-format">{{ $totTunAnak }}</td>
                    <td class="text-end num-format">{{ $totTbk }}</td>
                    <td class="text-end num-format">{{ $totTmp }}</td>
                    <td class="text-end num-format">{{ $totKehormatan }}</td>
                    <td class="text-end num-format">{{ $totPotongan }}</td>
                    <td class="text-end num-format">{{ $totDiterima }}</td>
                </tr>
            </tfoot>
        @elseif($kategori === 'Pengajar')
            <thead>
                <tr>
                    <th>No</th>
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
                        <td class="text-end num-format">{{ (float)$r->tunkel }}</td>
                        <td class="text-end num-format">{{ (float)$r->tunj_anak }}</td>
                        <td class="text-end num-format">{{ (float)$r->kehormatan }}</td>
                        <td class="text-end num-format">{{ (float)$r->walkes }}</td>
                        <td class="text-end num-format">{{ (float)$r->khusus }}</td>
                        <td class="text-end num-format">{{ (float)$r->potongan }}</td>
                        <td class="text-end text-bold num-format">{{ (float)$r->diterima }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #e2e8f0; font-weight: bold;">
                    <td colspan="5" class="text-center">TOTAL</td>
                    <td class="text-end num-format">{{ $totTunkelP }}</td>
                    <td class="text-end num-format">{{ $totTunAnakP }}</td>
                    <td class="text-end num-format">{{ $totKehormatanP }}</td>
                    <td class="text-end num-format">{{ $totWalkesP }}</td>
                    <td class="text-end num-format">{{ $totTambahanP }}</td>
                    <td class="text-end num-format">{{ $totPotonganP }}</td>
                    <td class="text-end num-format">{{ $totDiterimaP }}</td>
                </tr>
            </tfoot>
        @else
            <thead>
                <tr>
                    <th>No</th>
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
                        <td class="text-end num-format">{{ (float)$r->potongan }}</td>
                        <td class="text-end text-bold num-format">{{ (float)$r->diterima }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #e2e8f0; font-weight: bold;">
                    <td colspan="3" class="text-center">TOTAL</td>
                    <td class="text-end num-format">{{ $totPotonganS }}</td>
                    <td class="text-end num-format">{{ $totDiterimaS }}</td>
                </tr>
            </tfoot>
        @endif
    </table>
</body>
</html>
