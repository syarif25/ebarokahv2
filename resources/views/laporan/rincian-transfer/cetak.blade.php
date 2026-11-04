<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan TF - {{ $selectedLembaga->nama_lembaga ?? 'Lembaga' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4 portrait;
            margin: 6mm 14mm 6mm 14mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            line-height: 1.25;
            color: #000;
            background: #fff;
            padding: 8px 25px;
        }

        .doc-paper {
            width: 100%;
            max-width: 680px;
            margin: 0 auto;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* Kop Surat */
        .kop-header {
            display: flex;
            align-items: center;
            border-bottom: 2.5px double #000;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }
        .kop-logo {
            width: 54px;
            height: auto;
            margin-right: 14px;
        }
        .kop-text {
            flex-grow: 1;
            text-align: center;
        }
        .kop-text h3 {
            font-size: 11.5pt;
            font-weight: bold;
            margin: 0 0 1px 0;
            letter-spacing: 0.3px;
            line-height: 1.2;
        }
        .kop-text p {
            margin: 1px 0;
            font-size: 7.5pt;
            line-height: 1.2;
        }

        /* Meta Info */
        .meta-info-table {
            margin-bottom: 12px;
            font-size: 10pt;
            width: 100%;
        }
        .meta-info-table td {
            padding: 1px 0;
        }

        /* Section Layout (Struktural & Pengajar) */
        .section-block {
            display: flex;
            margin-bottom: 12px;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .section-left {
            width: 25%;
            padding-right: 12px;
        }
        .section-title-label {
            font-weight: bold;
            font-size: 10.5pt;
        }
        .transfer-note-label {
            font-style: italic;
            font-size: 8.5pt;
            margin-top: 8px;
            color: #222;
        }
        .transfer-date-val {
            font-size: 8.5pt;
            line-height: 1.2;
            color: #000;
        }

        .section-right {
            width: 75%;
        }

        .table-rincian {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        .table-rincian td {
            padding: 1.5px 0;
            vertical-align: middle;
        }
        .col-colon {
            width: 20px;
            text-align: center;
        }
        .col-name {
            color: #000;
        }
        .col-amount {
            text-align: right;
            font-variant-numeric: tabular-nums;
        }
        .row-border-bottom td {
            border-bottom: 1.5px solid #000;
            padding: 1px 0;
        }
        .row-total-sub td {
            font-weight: bold;
            padding-top: 3px;
            padding-bottom: 3px;
        }

        .potongan-heading {
            font-weight: bold;
            margin-top: 8px;
            margin-bottom: 2px;
            font-size: 9.5pt;
        }

        /* Tanda Tangan */
        .signature-box {
            margin-top: 15px;
            margin-left: auto;
            width: 200px;
            text-align: left;
            font-size: 9.5pt;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .signature-gap {
            height: 42px;
        }

        .action-bar {
            text-align: center;
            margin-bottom: 18px;
        }

        .btn-print {
            background-color: #000;
            color: #fff;
            border: none;
            padding: 7px 22px;
            border-radius: 6px;
            font-size: 10pt;
            font-weight: bold;
            cursor: pointer;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            html, body {
                padding: 0 !important;
                margin: 0 !important;
                background: #fff !important;
                height: auto !important;
            }
            .doc-paper {
                max-width: 100% !important;
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body>
    <div class="no-print action-bar">
        <button onclick="window.print()" class="btn-print">Cetak / Simpan PDF (1 Halaman)</button>
        <button onclick="window.close()" style="margin-left: 10px; padding: 7px 16px; border: 1px solid #ccc; background: #fff; border-radius: 6px; cursor: pointer; font-size: 10pt;">Tutup</button>
    </div>

    <div class="doc-paper">
        <!-- 1. KOP SURAT RESMI -->
        <div class="kop-header">
            <img src="{{ asset('assets/p2s2.png') }}" onerror="this.src='{{ asset('images/p2s2.png') }}'" class="kop-logo" alt="Logo P2S3">
            <div class="kop-text">
                <h3>PONDOK PESANTREN SALAFIYAH SYAFI'IYAH</h3>
                <h3>SUKOREJO SITUBONDO JAWA TIMUR</h3>
                <p>Sumberejo Banyuputih Situbondo Jawa Timur 68374</p>
                <p>Po Box 2 Telp 0388-452666 Fax. 452707</p>
            </div>
        </div>

        <!-- 2. METADATA LEMBAGA & PERIODE -->
        <table class="meta-info-table">
            <tr>
                <td style="width: 120px;">Nama Lembaga</td>
                <td style="width: 15px;">:</td>
                <td><strong>{{ $selectedLembaga->nama_lembaga ?? '-' }}</strong></td>
            </tr>
            <tr>
                <td>Periode</td>
                <td>:</td>
                <td><strong>{{ $bulan }} {{ $tahun }}</strong></td>
            </tr>
        </table>

        <!-- 3. STRUKTURAL -->
        @if($dataRincian['has_struktural'] ?? true)
        <div class="section-block">
            <!-- Sisi Kiri -->
            <div class="section-left">
                <div class="section-title-label">Struktural</div>
                <div class="transfer-note-label">di transfer tanggal</div>
                <div class="transfer-date-val">{{ $dataRincian['struktural']['tgl_transfer'] ?? '-' }}</div>
            </div>

            <!-- Sisi Kanan -->
            <div class="section-right">
                <!-- Tabel Bank Transfer -->
                <table class="table-rincian">
                    @forelse($dataRincian['struktural']['bank'] as $index => $b)
                        <tr>
                            <td class="col-colon">{{ $index === 0 ? ':' : '' }}</td>
                            <td class="col-name">{{ $b->nama_bank }}</td>
                            <td class="col-amount">{{ number_format($b->total_nominal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="col-colon">:</td>
                            <td class="col-name">-</td>
                            <td class="col-amount">0</td>
                        </tr>
                    @endforelse

                    <tr class="row-border-bottom">
                        <td colspan="3"></td>
                    </tr>
                    <tr class="row-total-sub">
                        <td></td>
                        <td class="text-end pe-3" style="text-align: right; padding-right: 15px;">Total</td>
                        <td class="col-amount">{{ number_format($dataRincian['struktural']['total_transfer'], 0, ',', '.') }}</td>
                    </tr>
                </table>

                <!-- Bagian Potongan -->
                <div class="potongan-heading" style="margin-left: 20px;">Potongan</div>
                <table class="table-rincian">
                    @forelse($dataRincian['struktural']['potongan'] as $pot)
                        <tr>
                            <td class="col-colon"></td>
                            <td class="col-name">{{ $pot->nama_potongan }}</td>
                            <td class="col-amount">{{ number_format($pot->nominal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="col-colon"></td>
                            <td class="col-name">-</td>
                            <td class="col-amount">0</td>
                        </tr>
                    @endforelse

                    <tr class="row-border-bottom">
                        <td colspan="3"></td>
                    </tr>
                    <tr class="row-total-sub">
                        <td></td>
                        <td class="text-end pe-3" style="text-align: right; padding-right: 15px;">Total</td>
                        <td class="col-amount">{{ number_format($dataRincian['struktural']['total_potongan'], 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        @endif

        <!-- 4. PENGAJAR -->
        @if($dataRincian['has_pengajar'] ?? false)
        <div class="section-block">
            <!-- Sisi Kiri -->
            <div class="section-left">
                <div class="section-title-label">Pengajar</div>
                <div class="transfer-note-label">di transfer tanggal</div>
                <div class="transfer-date-val">{{ $dataRincian['pengajar']['tgl_transfer'] ?? '-' }}</div>
            </div>

            <!-- Sisi Kanan -->
            <div class="section-right">
                <!-- Tabel Bank Transfer -->
                <table class="table-rincian">
                    @forelse($dataRincian['pengajar']['bank'] as $index => $b)
                        <tr>
                            <td class="col-colon">{{ $index === 0 ? ':' : '' }}</td>
                            <td class="col-name">{{ $b->nama_bank }}</td>
                            <td class="col-amount">{{ number_format($b->total_nominal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="col-colon">:</td>
                            <td class="col-name">-</td>
                            <td class="col-amount">0</td>
                        </tr>
                    @endforelse

                    <tr class="row-border-bottom">
                        <td colspan="3"></td>
                    </tr>
                    <tr class="row-total-sub">
                        <td></td>
                        <td class="text-end pe-3" style="text-align: right; padding-right: 15px;">Total</td>
                        <td class="col-amount">{{ number_format($dataRincian['pengajar']['total_transfer'], 0, ',', '.') }}</td>
                    </tr>
                </table>

                <!-- Bagian Potongan -->
                <div class="potongan-heading" style="margin-left: 20px;">Potongan</div>
                <table class="table-rincian">
                    @forelse($dataRincian['pengajar']['potongan'] as $pot)
                        <tr>
                            <td class="col-colon"></td>
                            <td class="col-name">{{ $pot->nama_potongan }}</td>
                            <td class="col-amount">{{ number_format($pot->nominal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="col-colon"></td>
                            <td class="col-name">-</td>
                            <td class="col-amount">0</td>
                        </tr>
                    @endforelse

                    <tr class="row-border-bottom">
                        <td colspan="3"></td>
                    </tr>
                    <tr class="row-total-sub">
                        <td></td>
                        <td class="text-end pe-3" style="text-align: right; padding-right: 15px;">Total</td>
                        <td class="col-amount">{{ number_format($dataRincian['pengajar']['total_potongan'], 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        @endif

        <!-- 5. REKAP TOTAL (GABUNGAN STRUKTURAL & PENGAJAR) -->
        @if(!empty($dataRincian['show_total']) && isset($dataRincian['total']))
        <div class="section-block" style="border-top: 1.5px solid #000; padding-top: 8px; margin-top: 6px;">
            <!-- Sisi Kiri -->
            <div class="section-left">
                <div class="section-title-label">Total</div>
            </div>

            <!-- Sisi Kanan -->
            <div class="section-right">
                <!-- Tabel Bank Transfer -->
                <table class="table-rincian">
                    @forelse($dataRincian['total']['bank'] as $index => $b)
                        <tr>
                            <td class="col-colon">{{ $index === 0 ? ':' : '' }}</td>
                            <td class="col-name" style="font-weight: bold;">{{ $b->nama_bank }}</td>
                            <td class="col-amount" style="font-weight: bold;">{{ number_format($b->total_nominal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="col-colon">:</td>
                            <td class="col-name">-</td>
                            <td class="col-amount">0</td>
                        </tr>
                    @endforelse

                    <tr class="row-border-bottom">
                        <td colspan="3"></td>
                    </tr>
                    <tr class="row-total-sub">
                        <td></td>
                        <td class="text-end pe-3" style="text-align: right; padding-right: 15px;">Total</td>
                        <td class="col-amount">{{ number_format($dataRincian['total']['total_transfer'], 0, ',', '.') }}</td>
                    </tr>
                </table>

                <!-- Bagian Potongan -->
                <div class="potongan-heading" style="margin-left: 20px;">Potongan</div>
                <table class="table-rincian">
                    @forelse($dataRincian['total']['potongan'] as $pot)
                        <tr>
                            <td class="col-colon"></td>
                            <td class="col-name">{{ $pot->nama_potongan }}</td>
                            <td class="col-amount">{{ number_format($pot->nominal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="col-colon"></td>
                            <td class="col-name">-</td>
                            <td class="col-amount">0</td>
                        </tr>
                    @endforelse

                    <tr class="row-border-bottom">
                        <td colspan="3"></td>
                    </tr>
                    <tr class="row-total-sub">
                        <td></td>
                        <td class="text-end pe-3" style="text-align: right; padding-right: 15px;">Total</td>
                        <td class="col-amount">{{ number_format($dataRincian['total']['total_potongan'], 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        @endif

        <!-- 6. TANDA TANGAN (KANAN BAWAH) -->
        <div class="signature-box">
            <div>Sukorejo, {{ $tglCetak }}</div>
            <div>Staf Ebarokah</div>
            <div class="signature-gap"></div>
            <div style="font-weight: bold;">{{ $staffNama }}</div>
        </div>
    </div>
</body>
</html>
