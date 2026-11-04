@extends('layouts.app')

@section('title', 'Laporan Rincian Transfer Lembaga - E-Barokah')

@section('page_title')
<span class="text-danger font-weight-bold">Laporan</span> / Rincian Transfer Lembaga
@endsection

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        /* Select2 Custom Design Matching V2 Theme */
        .select2-container--default .select2-selection--single {
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            height: 42px !important;
            padding: 6px 14px !important;
            background-color: #fff !important;
            display: flex !important;
            align-items: center !important;
            transition: all 0.2s ease !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            font-size: 13px !important;
            color: #334155 !important;
            padding-left: 0 !important;
            padding-right: 20px !important;
            line-height: normal !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #64748b !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 10px !important;
        }
        .select2-container--default.select2-container--open .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #00a15d !important;
            box-shadow: 0 0 0 3px rgba(0, 161, 93, 0.12) !important;
            outline: none !important;
        }
        .select2-dropdown {
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            overflow: hidden !important;
            z-index: 9999 !important;
            background-color: #fff !important;
        }
        .select2-container--default .select2-search--dropdown {
            padding: 8px 10px !important;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 7px 12px !important;
            font-size: 13px !important;
            outline: none !important;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #00a15d !important;
            box-shadow: 0 0 0 2px rgba(0, 161, 93, 0.15) !important;
        }
        .select2-results__options {
            max-height: 260px !important;
            overflow-y: auto !important;
            font-size: 13px !important;
            padding: 4px !important;
        }
        .select2-container--default .select2-results__option {
            padding: 8px 12px !important;
            border-radius: 6px !important;
            margin-bottom: 2px !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #00a15d !important;
            color: #fff !important;
        }
        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #f0fdf4 !important;
            color: #166534 !important;
            font-weight: 600 !important;
        }

        /* Table Styling identik dengan standar V2 */
        table.dataTable.display tbody td, 
        table.dataTable.display tbody th, 
        table.dataTable.display thead th {
            border-left: none !important;
            border-right: none !important;
            padding: 1.15rem 1rem !important;
            vertical-align: middle;
        }
        table.dataTable.display thead th {
            border-top: none !important;
            border-bottom: 1px solid #f0f0f0 !important;
            text-transform: capitalize;
            font-weight: 700;
            color: #333;
        }
        table.dataTable.display tbody tr {
            border-bottom: 1px solid #f8f8f8 !important;
        }
        table.dataTable.display tbody tr.odd,
        table.dataTable.display tbody tr.even {
            background-color: transparent !important;
        }
        table.dataTable.display tbody tr:hover {
            background-color: #fafafa !important;
        }
        table.dataTable,
        table.dataTable.no-footer {
            border-bottom: none !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.2em 0.5em !important;
        }

        /* Filter Controls */
        .filter-label {
            font-size: 13px;
            font-weight: 500;
            color: #64748b;
            margin-bottom: 6px;
            display: block;
        }
        .filter-control {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 14px;
            font-size: 13px;
            height: 42px;
            background-color: #fff;
            width: 100%;
            transition: all 0.2s ease;
        }
        .filter-control:focus {
            border-color: #00a15d;
            box-shadow: 0 0 0 3px rgba(0, 161, 93, 0.12);
            outline: none;
        }

        .btn-filter-custom {
            background-color: #c026d3;
            color: #fff;
            border: none;
            border-radius: 20px;
            padding: 9px 20px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s ease;
        }
        .btn-filter-custom:hover {
            background-color: #a21caf;
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-reset-custom {
            background-color: #ea580c;
            color: #fff;
            border: none;
            border-radius: 20px;
            padding: 9px 20px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s ease;
        }
        .btn-reset-custom:hover {
            background-color: #c2410c;
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-refresh-custom {
            background-color: #00a15d;
            color: #fff;
            border: none;
            border-radius: 20px;
            padding: 8px 18px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s ease;
        }
        .btn-refresh-custom:hover {
            background-color: #00874e;
            color: #fff;
        }

        /* Dokumen Lembar KOP Resmi Persis Mockup (Ukuran 1 Kertas A4) */
        .doc-wrapper {
            display: flex;
            justify-content: center;
            padding: 10px 10px 40px;
        }
        .doc-paper {
            background: #ffffff;
            width: 100%;
            max-width: 680px;
            padding: 30px 45px 35px;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            color: #000000;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            line-height: 1.35;
        }

        /* Kop Surat */
        .kop-header {
            display: flex;
            align-items: center;
            border-bottom: 2.5px double #000;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .kop-logo {
            width: 60px;
            height: auto;
            margin-right: 18px;
        }
        .kop-text {
            flex-grow: 1;
            text-align: center;
        }
        .kop-text h3 {
            font-size: 14px;
            font-weight: bold;
            margin: 0 0 1px 0;
            color: #000;
            letter-spacing: 0.3px;
            line-height: 1.25;
        }
        .kop-text p {
            margin: 1px 0;
            font-size: 10px;
            color: #111;
            line-height: 1.2;
        }

        /* Meta Info */
        .meta-info-table {
            margin-bottom: 14px;
            font-size: 13px;
            width: 100%;
        }
        .meta-info-table td {
            padding: 1.5px 0;
        }

        /* Section Layout (Struktural & Pengajar) */
        .section-block {
            display: flex;
            margin-bottom: 12px;
        }
        .section-left {
            width: 25%;
            padding-right: 12px;
        }
        .section-title-label {
            font-weight: bold;
            font-size: 13.5px;
            color: #000;
        }
        .transfer-note-label {
            font-style: italic;
            font-size: 11.5px;
            color: #222;
            margin-top: 10px;
        }
        .transfer-date-val {
            font-size: 11.5px;
            color: #000;
            line-height: 1.25;
        }

        .section-right {
            width: 75%;
        }

        .table-rincian {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .table-rincian td {
            padding: 2px 0;
            vertical-align: middle;
        }
        .col-colon {
            width: 20px;
            text-align: center;
            color: #000;
        }
        .col-name {
            color: #000;
        }
        .col-amount {
            text-align: right;
            font-variant-numeric: tabular-nums;
            color: #000;
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
            margin-top: 10px;
            margin-bottom: 2px;
            font-size: 13px;
            color: #000;
        }

        /* Tanda Tangan */
        .signature-box {
            margin-top: 15px;
            margin-left: auto;
            width: 210px;
            text-align: left;
            font-size: 13px;
            color: #000;
        }
        .signature-gap {
            height: 40px;
        }

        @media (max-width: 768px) {
            .doc-paper {
                padding: 20px 15px;
            }
            .section-block {
                flex-direction: column;
            }
            .section-left, .section-right {
                width: 100%;
            }
            .section-left {
                margin-bottom: 8px;
            }
        }
    </style>
@endpush

@section('content')
<div class="row">
    <!-- ======================================================== -->
    <!-- JIKA TAMPILAN SLIP DETAIL LEMBAGA (id_lembaga DIPILIH)   -->
    <!-- ======================================================== -->
    @if($dataRincian && $selectedLembaga)
        <!-- ACTION BAR DETAIL SLIP -->
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; background: #ffffff;">
                <div class="card-body p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <a href="{{ route('laporan.rincian-transfer', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-reset-custom shadow-sm text-white text-decoration-none">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Lembaga
                    </a>

                    <div class="d-flex align-items-center gap-2">
                        @if($dataRincian['is_demo'])
                            <span class="badge bg-warning text-dark px-3 py-2 font-11" style="border-radius: 20px;">
                                <i class="fas fa-info-circle me-1"></i> Data Simulasi
                            </span>
                        @endif

                        <a href="{{ route('laporan.rincian-transfer.cetak', [
                            'id_lembaga'   => $idLembaga,
                            'bulan'        => $bulan,
                            'tahun'        => $tahun,
                            'staff_nama'   => $staffNama,
                            'demo'         => ($dataRincian['is_demo'] ? 1 : 0)
                        ]) }}" target="_blank" class="btn btn-refresh-custom shadow-sm text-white">
                            <i class="fas fa-print me-1"></i> Cetak / Simpan PDF (1 Halaman)
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- DOKUMEN CETAK KERTAS (PERSIS 1 KERTAS A4) -->
        <div class="col-12 doc-wrapper">
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
                                    <td class="col-name text-muted">-</td>
                                    <td class="col-amount">0</td>
                                </tr>
                            @endforelse

                            <tr class="row-border-bottom">
                                <td colspan="3"></td>
                            </tr>
                            <tr class="row-total-sub">
                                <td></td>
                                <td class="text-end pe-3 font-weight-bold" style="text-align: right; padding-right: 15px;">Total</td>
                                <td class="col-amount font-weight-bold">{{ number_format($dataRincian['struktural']['total_transfer'], 0, ',', '.') }}</td>
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
                                    <td class="col-name text-muted">-</td>
                                    <td class="col-amount">0</td>
                                </tr>
                            @endforelse

                            <tr class="row-border-bottom">
                                <td colspan="3"></td>
                            </tr>
                            <tr class="row-total-sub">
                                <td></td>
                                <td class="text-end pe-3 font-weight-bold" style="text-align: right; padding-right: 15px;">Total</td>
                                <td class="col-amount font-weight-bold">{{ number_format($dataRincian['struktural']['total_potongan'], 0, ',', '.') }}</td>
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
                                    <td class="col-name text-muted">-</td>
                                    <td class="col-amount">0</td>
                                </tr>
                            @endforelse

                            <tr class="row-border-bottom">
                                <td colspan="3"></td>
                            </tr>
                            <tr class="row-total-sub">
                                <td></td>
                                <td class="text-end pe-3 font-weight-bold" style="text-align: right; padding-right: 15px;">Total</td>
                                <td class="col-amount font-weight-bold">{{ number_format($dataRincian['pengajar']['total_transfer'], 0, ',', '.') }}</td>
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
                                    <td class="col-name text-muted">-</td>
                                    <td class="col-amount">0</td>
                                </tr>
                            @endforelse

                            <tr class="row-border-bottom">
                                <td colspan="3"></td>
                            </tr>
                            <tr class="row-total-sub">
                                <td></td>
                                <td class="text-end pe-3 font-weight-bold" style="text-align: right; padding-right: 15px;">Total</td>
                                <td class="col-amount font-weight-bold">{{ number_format($dataRincian['pengajar']['total_potongan'], 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                @endif

                <!-- 5. REKAP TOTAL (GABUNGAN STRUKTURAL & PENGAJAR) -->
                @if(!empty($dataRincian['show_total']) && isset($dataRincian['total']))
                <div class="section-block" style="border-top: 1.5px solid #000; padding-top: 12px; margin-top: 6px;">
                    <!-- Sisi Kiri -->
                    <div class="section-left">
                        <div class="section-title-label font-weight-bold" style="font-size: 14px;">Total</div>
                    </div>

                    <!-- Sisi Kanan -->
                    <div class="section-right">
                        <!-- Tabel Bank Transfer Gabungan -->
                        <table class="table-rincian">
                            @forelse($dataRincian['total']['bank'] as $index => $b)
                                <tr>
                                    <td class="col-colon">{{ $index === 0 ? ':' : '' }}</td>
                                    <td class="col-name font-weight-bold">{{ $b->nama_bank }}</td>
                                    <td class="col-amount font-weight-bold">{{ number_format($b->total_nominal, 0, ',', '.') }}</td>
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
                                <td class="text-end pe-3 font-weight-bold" style="text-align: right; padding-right: 15px;">Total</td>
                                <td class="col-amount font-weight-bold">{{ number_format($dataRincian['total']['total_transfer'], 0, ',', '.') }}</td>
                            </tr>
                        </table>

                        <!-- Bagian Potongan Gabungan -->
                        <div class="potongan-heading font-weight-bold" style="margin-left: 20px;">Potongan</div>
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
                                    <td class="col-name text-muted">-</td>
                                    <td class="col-amount">0</td>
                                </tr>
                            @endforelse

                            <tr class="row-border-bottom">
                                <td colspan="3"></td>
                            </tr>
                            <tr class="row-total-sub">
                                <td></td>
                                <td class="text-end pe-3 font-weight-bold" style="text-align: right; padding-right: 15px;">Total</td>
                                <td class="col-amount font-weight-bold">{{ number_format($dataRincian['total']['total_potongan'], 0, ',', '.') }}</td>
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
        </div>

    <!-- ======================================================== -->
    <!-- TAMPILAN AWAL: TABEL REKAPITULASI SEMUA LEMBAGA         -->
    <!-- (IDENTIK DENGAN MENU LAPORAN / PER LEMBAGA)             -->
    <!-- ======================================================== -->
    @else
        <div class="col-12">
            <div class="card">
                <!-- Header Card identik seperti laporan per lembaga -->
                <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title font-weight-bold mb-1">Laporan Rincian Transfer Lembaga</h4>
                        <p class="text-muted fs-13 mb-0">
                            <i class="fas fa-info-circle text-muted me-1"></i> Menampilkan rincian transfer barokah per lembaga beserta rekening bank dan potongan
                        </p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-refresh-custom shadow-sm" onclick="window.location.href='{{ route('laporan.rincian-transfer') }}'">
                            <i class="fas fa-sync-alt me-1"></i> Refresh
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Section Filter identik seperti laporan per lembaga -->
                    <form method="GET" action="{{ route('laporan.rincian-transfer') }}" class="row align-items-end g-3 mb-4">
                        <div class="col-lg-4 col-md-6">
                            <label class="filter-label">Filter Lembaga</label>
                            <select class="form-select filter-control" name="filter_lembaga" id="filter_lembaga" style="width: 100%;">
                                <option value="">Pilih Lembaga</option>
                                @foreach($lembagaList as $lem)
                                    <option value="{{ $lem->id_lembaga }}" {{ ($filterLembaga == $lem->id_lembaga) ? 'selected' : '' }}>
                                        {{ $lem->nama_lembaga }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-3">
                            <label class="filter-label">Bulan</label>
                            <select class="form-select filter-control" name="bulan" id="filter_bulan">
                                @foreach($bulanList as $b)
                                    <option value="{{ $b }}" {{ $bulan == $b ? 'selected' : '' }}>{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-3">
                            <label class="filter-label">Tahun</label>
                            <select class="form-select filter-control" name="tahun" id="filter_tahun">
                                @foreach($tahunList as $t)
                                    <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-12 d-flex gap-2">
                            <button type="submit" class="btn btn-filter-custom flex-grow-1 shadow-sm">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <a href="{{ route('laporan.rincian-transfer') }}" class="btn btn-reset-custom flex-grow-1 shadow-sm text-center text-white text-decoration-none">
                                <i class="fas fa-redo me-1"></i> Reset
                            </a>
                        </div>
                    </form>

                    <!-- DataTable Laporan Rincian Transfer persis seperti laporan per lembaga -->
                    <div class="table-responsive">
                        <table id="tabel_laporan_transfer" class="display" style="min-width: 980px; width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 45px;" class="text-center">No</th>
                                    <th>Nama Lembaga</th>
                                    <th class="text-end" style="font-weight: 700; color: #0369a1;">BRI</th>
                                    <th class="text-end" style="font-weight: 700; color: #0d9488;">BSI</th>
                                    <th class="text-end" style="font-weight: 700; color: #166534;">Total Barokah</th>
                                    <th class="text-end">BPJS</th>
                                    <th class="text-end">Listrik</th>
                                    <th class="text-end">Kalender</th>
                                    <th class="text-end">Ditunaikan</th>
                                    <th class="text-end" style="font-weight: 700; color: #0f172a;">Jumlah Total</th>
                                    <th style="width: 130px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tableRekap as $index => $row)
                                    <tr>
                                        <td class="text-center font-weight-bold text-secondary">{{ $index + 1 }}</td>
                                        <td>
                                            <span class="font-weight-bold text-dark" style="font-size: 13.5px;">{{ $row->nama_lembaga }}</span>
                                        </td>
                                        <td class="text-end font-weight-bold" style="font-size: 14px; color: #0f172a;">
                                            {{ number_format($row->total_bri, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end font-weight-bold" style="font-size: 14px; color: #0f172a;">
                                            {{ number_format($row->total_bsi, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end font-weight-bold" style="font-size: 14px; color: #00a15d;">
                                            {{ number_format($row->total_barokah, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end font-weight-bold" style="font-size: 13.5px; color: #475569;">{{ number_format($row->bpjs, 0, ',', '.') }}</td>
                                        <td class="text-end font-weight-bold" style="font-size: 13.5px; color: #475569;">{{ number_format($row->listrik, 0, ',', '.') }}</td>
                                        <td class="text-end font-weight-bold" style="font-size: 13.5px; color: #475569;">{{ number_format($row->kalender, 0, ',', '.') }}</td>
                                        <td class="text-end font-weight-bold" style="font-size: 14px; color: #0f172a;">{{ number_format($row->ditunaikan, 0, ',', '.') }}</td>
                                        <td class="text-end font-weight-bold" style="font-size: 14.5px; color: #0f172a;">
                                            {{ number_format($row->jumlah_total ?? ($row->total_barokah + $row->bpjs + $row->listrik + $row->kalender + $row->ditunaikan), 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <div class="d-inline-flex gap-1 align-items-center">
                                                <a href="{{ route('laporan.rincian-transfer', [
                                                    'id_lembaga' => $row->id_lembaga,
                                                    'bulan'      => $bulan,
                                                    'tahun'      => $tahun,
                                                    'view'       => 'slip'
                                                ]) }}" class="btn btn-sm text-white shadow-sm" style="background-color: #00a15d; border-radius: 20px; padding: 6px 14px; font-weight: 600; font-size: 12px;" title="Lihat Rincian Slip">
                                                    <i class="fas fa-list-alt me-1"></i> Detail
                                                </a>
                                                <a href="{{ route('laporan.rincian-transfer.cetak', [
                                                    'id_lembaga' => $row->id_lembaga,
                                                    'bulan'      => $bulan,
                                                    'tahun'      => $tahun,
                                                ]) }}" target="_blank" class="btn btn-sm btn-outline-dark" style="border-radius: 20px; padding: 6px 10px;" title="Cetak PDF">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-5 text-muted">
                                            <i class="fas fa-folder-open fa-2x mb-2 text-secondary d-block"></i>
                                            Tidak ada data transfer barokah pada periode <strong>{{ $bulan }} {{ $tahun }}</strong>.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($tableRekap && $tableRekap->count() > 0)
                                <tfoot>
                                    <tr style="background-color: #f8fafc; font-weight: bold;">
                                        <td colspan="2" class="text-center font-weight-bold text-dark" style="font-size: 13px;">TOTAL KESELURUHAN</td>
                                        <td class="text-end font-weight-bold" style="font-size: 14px; color: #0369a1;">
                                            {{ number_format($tableRekap->sum('total_bri'), 0, ',', '.') }}
                                        </td>
                                        <td class="text-end font-weight-bold" style="font-size: 14px; color: #0d9488;">
                                            {{ number_format($tableRekap->sum('total_bsi'), 0, ',', '.') }}
                                        </td>
                                        <td class="text-end font-weight-bold" style="font-size: 14px; color: #166534;">
                                            {{ number_format($tableRekap->sum('total_barokah'), 0, ',', '.') }}
                                        </td>
                                        <td class="text-end font-weight-bold" style="font-size: 13.5px; color: #475569;">{{ number_format($tableRekap->sum('bpjs'), 0, ',', '.') }}</td>
                                        <td class="text-end font-weight-bold" style="font-size: 13.5px; color: #475569;">{{ number_format($tableRekap->sum('listrik'), 0, ',', '.') }}</td>
                                        <td class="text-end font-weight-bold" style="font-size: 13.5px; color: #475569;">{{ number_format($tableRekap->sum('kalender'), 0, ',', '.') }}</td>
                                        <td class="text-end font-weight-bold" style="font-size: 14px; color: #0f172a;">{{ number_format($tableRekap->sum('ditunaikan'), 0, ',', '.') }}</td>
                                        <td class="text-end font-weight-bold" style="font-size: 14.5px; color: #0f172a;">
                                            {{ number_format($tableRekap->sum('jumlah_total') ?: ($tableRekap->sum('total_barokah') + $tableRekap->sum('bpjs') + $tableRekap->sum('listrik') + $tableRekap->sum('kalender') + $tableRekap->sum('ditunaikan')), 0, ',', '.') }}
                                        </td>
                                        <td class="text-center text-muted font-11">-</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
<script>
    $(document).ready(function() {
        // Select2 Filter Lembaga
        $('#filter_lembaga').select2({
            placeholder: "Pilih Lembaga",
            allowClear: true,
            width: '100%'
        });

        // Inisialisasi DataTable dengan UI identik dengan laporan per lembaga
        if ($('#tabel_laporan_transfer tbody tr').length > 0 && !$('#tabel_laporan_transfer tbody tr td[colspan]').length) {
            $('#tabel_laporan_transfer').DataTable({
                paging: true,
                pageLength: 10,
                responsive: true,
                order: [[0, 'asc']],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        }
    });
</script>
@endpush
