<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rincian Kepanitiaan - {{ $transaksi->nama_event }}</title>

    <link rel="shortcut icon" type="image/png" href="{{ asset('images/p2s2_fav.png') }}?v={{ filemtime(public_path('images/p2s2_fav.png')) }}" />
    <link rel="icon" type="image/png" href="{{ asset('images/p2s2_fav.png') }}?v={{ filemtime(public_path('images/p2s2_fav.png')) }}" />

    <!-- FontAwesome & Theme Style (Bootstrap 5) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        body {
            background-color: #f8fafc;
            color: #1e293b;
            font-size: 13px;
            margin: 0;
            padding: 0;
        }

        /* Top Sticky Toolbar */
        .top-navbar-kepanitiaan {
            position: sticky;
            top: 0;
            z-index: 1020;
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 10px 24px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
        }

        /* Summary Badges in Top Bar */
        .summary-badge-item {
            display: inline-flex;
            align-items: center;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 12px;
            color: #475569;
        }
        .summary-badge-item strong {
            color: #0f172a;
            margin-left: 4px;
        }
        .summary-badge-item.highlight-total {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #047857;
        }
        .summary-badge-item.highlight-total strong {
            color: #059669;
        }

        /* Action Buttons */
        .btn-tambah-panitia {
            background-color: #d848c9 !important; 
            color: white !important; 
            border-radius: 20px; 
            padding: 7px 18px;
            border: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .btn-tambah-panitia:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        /* Table Styling */
        table.dataTable.display {
            border-collapse: collapse !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }
        table.dataTable.display tbody td {
            border-top: 1px solid #f1f5f9 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 0.75rem 0.65rem !important;
            vertical-align: middle;
            font-size: 12.5px;
            color: #334155;
        }
        table.dataTable.display thead th {
            background-color: #f8fafc !important;
            color: #334155 !important;
            border-top: none !important;
            border-bottom: 2px solid #cbd5e1 !important;
            font-weight: 700;
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 0.8rem 0.65rem !important;
            white-space: nowrap;
        }
        table.dataTable.display tfoot th {
            border-top: 2px solid #cbd5e1 !important;
            border-bottom: none !important;
            padding: 0.85rem 0.65rem !important;
            font-weight: 700;
            font-size: 12.5px;
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            white-space: nowrap;
        }
        table.dataTable.display tbody tr:hover {
            background-color: #f8fafc !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.2em 0.5em !important;
        }

        .sub-qty {
            font-size: 11px;
            color: #64748b;
            display: block;
            margin-top: 1px;
        }

        /* Border hijau tipis untuk seluruh inputan di form modal */
        #modalTambahPanitia .form-control,
        #modalTambahPanitia select,
        #modalTambahPanitia input {
            border: 1px solid #1ea566 !important;
            border-radius: 8px !important;
        }
        #modalTambahPanitia .form-control:focus,
        #modalTambahPanitia select:focus,
        #modalTambahPanitia input:focus {
            border-color: #00a15d !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 161, 93, 0.2) !important;
        }
        #modalTambahPanitia .select2-container .select2-selection--single {
            height: 38px !important;
            border: 1px solid #1ea566 !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
        }
        #modalTambahPanitia .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
        #modalTambahPanitia .select2-container--default.select2-container--open .select2-selection--single,
        #modalTambahPanitia .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #00a15d !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 161, 93, 0.2) !important;
        }
        #modalTambahPanitia .select2-dropdown {
            border: 1px solid #1ea566 !important;
            border-radius: 8px !important;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08) !important;
        }
        #modalTambahPanitia .select2-search--dropdown {
            padding: 8px !important;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        #modalTambahPanitia .select2-search--dropdown .select2-search__field {
            border: 1px solid #1ea566 !important;
            border-radius: 6px !important;
            padding: 6px 10px !important;
            font-size: 13px !important;
            outline: none !important;
        }
        #modalTambahPanitia .select2-results__option {
            padding: 8px 12px !important;
            border-bottom: 1px solid #f1f5f9;
        }
        #modalTambahPanitia .select2-results__option:last-child {
            border-bottom: none;
        }
        #modalTambahPanitia .umana-opt-name {
            font-weight: 600;
            font-size: 13.5px;
            color: #1e293b;
            line-height: 1.3;
        }
        #modalTambahPanitia .umana-opt-pos {
            font-size: 12px;
            color: #64748b;
            line-height: 1.35;
            margin-top: 2px;
        }
        #modalTambahPanitia .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #1ea566 !important;
            color: #ffffff !important;
        }
        #modalTambahPanitia .select2-container--default .select2-results__option--highlighted[aria-selected] .umana-opt-name {
            color: #ffffff !important;
        }
        #modalTambahPanitia .select2-container--default .select2-results__option--highlighted[aria-selected] .umana-opt-pos {
            color: rgba(255, 255, 255, 0.9) !important;
        }
    </style>
</head>
<body>

@php
    $sumKepanitiaan = $details->sum('nominal_kepanitiaan');
    $sumKehadiran   = $details->sum('nominal_kehadiran');
    $sumKonsumsi    = $details->sum('nominal_konsumsi');
    $sumPembSoal    = $details->sum('nominal_pemb_soal');
    $sumInputSoal   = $details->sum('nominal_input_soal');
    $sumPengawas    = $details->sum('nominal_pengawas');
    $sumPenguji     = $details->sum('nominal_penguji');
    $sumKorektor    = $details->sum('nominal_korektor');
    $sumInputNilai  = $details->sum('nominal_input_nilai');
    $sumPembimbing  = $details->sum('nominal_pembimbing');
    $sumBruto       = $details->sum('total_bruto');
    $sumPotongan    = $details->sum('potongan');
    $sumGrandTotal  = $details->sum('total_diterima');
@endphp

<!-- Top Sticky Toolbar -->
<header class="top-navbar-kepanitiaan d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px;">
    <!-- Bagian Kiri: Tombol Kembali + Info Kegiatan -->
    <div class="d-flex align-items-center flex-wrap" style="gap: 14px;">
        <a href="{{ url('/kepanitiaan') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 font-weight-bold" title="Kembali ke Daftar Kepanitiaan">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
        <div>
            <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                <h5 class="mb-0 font-weight-bold text-dark">{{ $transaksi->nama_event }}</h5>
                @php
                    $status = strtolower($transaksi->status ?? 'draft');
                    $badgeBg = 'bg-secondary';
                    if ($status === 'disetujui') $badgeBg = 'bg-success';
                    elseif ($status === 'ditolak') $badgeBg = 'bg-danger';
                    elseif ($status === 'pengajuan') $badgeBg = 'bg-warning text-dark';
                @endphp
                <span class="badge {{ $badgeBg }} rounded-pill text-uppercase px-2 py-1" style="font-size: 10.5px;">{{ $transaksi->status }}</span>
            </div>
            <div class="text-muted" style="font-size: 12px; margin-top: 2px;">
                {{ $transaksi->jenisKegiatan->nama_kegiatan ?? '-' }} &bull;
                {{ $transaksi->lembaga ? $transaksi->lembaga->nama_lembaga : 'Pusat / Lintas Lembaga' }} &bull;
                Periode {{ $transaksi->bulan }} {{ $transaksi->tahun }}
            </div>
        </div>
    </div>

    <!-- Bagian Kanan: Quick Summary + Tombol Aksi -->
    <div class="d-flex align-items-center flex-wrap" style="gap: 10px;">
        <div class="summary-badge-item">
            <i class="fas fa-users me-1 text-muted"></i> Total Panitia: <strong>{{ $details->count() }} orang</strong>
        </div>
        <div class="summary-badge-item highlight-total">
            <i class="fas fa-wallet me-1 text-success"></i> Total Diterima: <strong>Rp {{ number_format($sumGrandTotal, 0, ',', '.') }}</strong>
        </div>
        <button type="button" id="btnTambahPanitia" class="btn btn-sm btn-tambah-panitia shadow-sm" style="font-size: 12.5px;">
            <i class="fas fa-plus me-1"></i> Tambah Panitia
        </button>
    </div>
</header>

<!-- Main Container Fullscreen -->
<main class="container-fluid px-4 py-3">
    <div class="card border-0 shadow-sm" style="border-radius: 14px;">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="tblKepanitiaanDetail" class="display table table-hover" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 35px;" class="text-center">No</th>
                            <th>Nama Panitia</th>
                            <th class="text-end">Kepanitiaan</th>
                            <th class="text-end">Kehadiran</th>
                            <th class="text-end">Konsumsi</th>
                            <th class="text-end">Pemb. Soal</th>
                            <th class="text-end">Input Soal</th>
                            <th class="text-end">Pengawas</th>
                            <th class="text-end">Penguji</th>
                            <th class="text-end">Korektor</th>
                            <th class="text-end">Input Nilai</th>
                            <th class="text-end">Pembimbing</th>
                            <th class="text-end">Potongan</th>
                            <th class="text-end">Diterima</th>
                            <th class="text-center" style="width: 75px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($details as $idx => $d)
                        <tr>
                            <td class="text-center"><strong>{{ $idx + 1 }}</strong></td>
                            <td>
                                <strong class="text-dark">{{ $d->nama_panitia }}</strong>
                                @if($d->jabatan_panitia || $d->lembaga_asal)
                                    <span class="sub-qty">{{ $d->jabatan_panitia ?: $d->lembaga_asal }}</span>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($d->nominal_kepanitiaan, 0, ',', '.') }}</td>
                            <td class="text-end">
                                {{ number_format($d->nominal_kehadiran, 0, ',', '.') }}
                                @if($d->qty_kehadiran > 0)
                                    <span class="sub-qty">{{ (float)$d->qty_kehadiran }} shift/hr</span>
                                @endif
                            </td>
                            <td class="text-end">
                                {{ number_format($d->nominal_konsumsi, 0, ',', '.') }}
                                @if($d->qty_konsumsi > 0)
                                    <span class="sub-qty">{{ (float)$d->qty_konsumsi }} hr</span>
                                @endif
                            </td>
                            <td class="text-end">
                                {{ number_format($d->nominal_pemb_soal, 0, ',', '.') }}
                                @if($d->qty_pemb_soal > 0)
                                    <span class="sub-qty">{{ (float)$d->qty_pemb_soal }} pkt</span>
                                @endif
                            </td>
                            <td class="text-end">
                                {{ number_format($d->nominal_input_soal, 0, ',', '.') }}
                                @if($d->qty_input_soal > 0)
                                    <span class="sub-qty">{{ (float)$d->qty_input_soal }} pkt</span>
                                @endif
                            </td>
                            <td class="text-end">
                                {{ number_format($d->nominal_pengawas, 0, ',', '.') }}
                                @if($d->qty_pengawas > 0)
                                    <span class="sub-qty">{{ (float)$d->qty_pengawas }} sesi</span>
                                @endif
                            </td>
                            <td class="text-end">
                                {{ number_format($d->nominal_penguji, 0, ',', '.') }}
                                @if($d->qty_penguji > 0)
                                    <span class="sub-qty">{{ (float)$d->qty_penguji }} mhs</span>
                                @endif
                            </td>
                            <td class="text-end">
                                {{ number_format($d->nominal_korektor, 0, ',', '.') }}
                                @if($d->qty_korektor > 0)
                                    <span class="sub-qty">{{ (float)$d->qty_korektor }} mhs</span>
                                @endif
                            </td>
                            <td class="text-end">
                                {{ number_format($d->nominal_input_nilai, 0, ',', '.') }}
                                @if($d->qty_input_nilai > 0)
                                    <span class="sub-qty">{{ (float)$d->qty_input_nilai }} mhs</span>
                                @endif
                            </td>
                            <td class="text-end">
                                {{ number_format($d->nominal_pembimbing, 0, ',', '.') }}
                                @if($d->qty_pembimbing > 0)
                                    <span class="sub-qty">{{ (float)$d->qty_pembimbing }} mhs</span>
                                @endif
                            </td>
                            <td class="text-end text-danger">{{ number_format($d->potongan, 0, ',', '.') }}</td>
                            <td class="text-end font-weight-bold text-success">Rp {{ number_format($d->total_diterima, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center" style="gap: 5px;">
                                    <button type="button" class="btn btn-outline-success btn-xs" style="border-radius: 15px; padding: 3px 9px;" onclick='editPanitia(@json($d))' title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-xs" style="border-radius: 15px; padding: 3px 9px;" onclick="hapusRow({{ $d->id_kepanitiaan_detail }})" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    @if($details->count() > 0)
                    <tfoot>
                        <tr>
                            <th colspan="2" class="text-end">Total Keseluruhan</th>
                            <th class="text-end">{{ number_format($sumKepanitiaan, 0, ',', '.') }}</th>
                            <th class="text-end">{{ number_format($sumKehadiran, 0, ',', '.') }}</th>
                            <th class="text-end">{{ number_format($sumKonsumsi, 0, ',', '.') }}</th>
                            <th class="text-end">{{ number_format($sumPembSoal, 0, ',', '.') }}</th>
                            <th class="text-end">{{ number_format($sumInputSoal, 0, ',', '.') }}</th>
                            <th class="text-end">{{ number_format($sumPengawas, 0, ',', '.') }}</th>
                            <th class="text-end">{{ number_format($sumPenguji, 0, ',', '.') }}</th>
                            <th class="text-end">{{ number_format($sumKorektor, 0, ',', '.') }}</th>
                            <th class="text-end">{{ number_format($sumInputNilai, 0, ',', '.') }}</th>
                            <th class="text-end">{{ number_format($sumPembimbing, 0, ',', '.') }}</th>
                            <th class="text-end text-danger">{{ number_format($sumPotongan, 0, ',', '.') }}</th>
                            <th class="text-end text-success">Rp {{ number_format($sumGrandTotal, 0, ',', '.') }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal Tambah / Edit Panitia -->
<div class="modal fade" id="modalTambahPanitia" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 14px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold text-dark" id="modalPanitiaTitle">Form Panitia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPanitia">
                @csrf
                <input type="hidden" name="id_kepanitiaan_transaksi" value="{{ $transaksi->id_kepanitiaan_transaksi }}">
                <input type="hidden" name="id_kepanitiaan_detail" id="f_id_detail">

                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label font-weight-bold text-dark" style="font-size: 12px;">Nama Umana <span class="text-danger">*</span></label>
                            <select id="selectUmana" class="form-control" style="width: 100%;">
                                <option value="">-- Ketik Nama atau Lembaga --</option>
                            </select>
                            <input type="hidden" name="nik" id="f_nik">
                            <input type="hidden" name="nama_panitia" id="f_nama_panitia">
                            <input type="hidden" name="lembaga_asal" id="f_lembaga_asal">
                        </div>
                    </div>

                    <hr class="my-2">

                    <div class="row">
                        <!-- 1. Jabatan Kepanitiaan -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold text-dark" style="font-size: 12px;">Jabatan Kepanitiaan</label>
                            <div class="d-flex" style="gap: 8px;">
                                <select id="sel_jabatan" class="form-control">
                                    <option value="">-- Pilih Jabatan --</option>
                                    @foreach($tarifs->where('kolom_kategori', 'kepanitiaan') as $tk)
                                        <option value="{{ $tk->nama_komponen }}" data-nominal="{{ (int)$tk->nominal }}">{{ $tk->nama_komponen }} (Rp {{ number_format($tk->nominal, 0, ',', '.') }})</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="jabatan_panitia" id="f_jabatan_panitia">
                                <input type="text" inputmode="numeric" name="nominal_kepanitiaan" id="f_nominal_kepanitiaan" class="form-control text-end calc-source input-ribuan" style="max-width: 130px;" value="0">
                            </div>
                        </div>

                        <!-- 2. Kehadiran -->
                        <div class="col-md-6 mb-3">
                            @php $tHadir = $tarifs->where('kolom_kategori', 'kehadiran')->first(); $rateHadir = $tHadir ? (int)$tHadir->nominal : 0; @endphp
                            <label class="form-label font-weight-bold text-dark" style="font-size: 12px;">Kehadiran Panitia <small class="text-muted">(Tarif: Rp {{ number_format($rateHadir, 0, ',', '.') }})</small></label>
                            <div class="d-flex" style="gap: 8px;">
                                <input type="number" step="any" name="qty_kehadiran" id="f_qty_kehadiran" class="form-control auto-calc" data-rate="{{ $rateHadir }}" data-target="#f_nominal_kehadiran" placeholder="Jml shift/hari" style="max-width: 120px;">
                                <input type="text" inputmode="numeric" name="nominal_kehadiran" id="f_nominal_kehadiran" class="form-control text-end calc-source input-ribuan" value="0">
                            </div>
                        </div>

                        <!-- 3. Konsumsi -->
                        <div class="col-md-6 mb-3">
                            @php $tKonsumsi = $tarifs->where('kolom_kategori', 'konsumsi')->first(); $rateKonsumsi = $tKonsumsi ? (int)$tKonsumsi->nominal : 0; @endphp
                            <label class="form-label font-weight-bold text-dark" style="font-size: 12px;">Konsumsi <small class="text-muted">(Tarif: Rp {{ number_format($rateKonsumsi, 0, ',', '.') }})</small></label>
                            <div class="d-flex" style="gap: 8px;">
                                <input type="number" step="any" name="qty_konsumsi" id="f_qty_konsumsi" class="form-control auto-calc" data-rate="{{ $rateKonsumsi }}" data-target="#f_nominal_konsumsi" placeholder="Jml hari" style="max-width: 120px;">
                                <input type="text" inputmode="numeric" name="nominal_konsumsi" id="f_nominal_konsumsi" class="form-control text-end calc-source input-ribuan" value="0">
                            </div>
                        </div>

                        <!-- 6. Pengawas -->
                        <div class="col-md-6 mb-3">
                            @php $tPengawas = $tarifs->where('kolom_kategori', 'pengawas')->first(); $ratePengawas = $tPengawas ? (int)$tPengawas->nominal : 0; @endphp
                            <label class="form-label font-weight-bold text-dark" style="font-size: 12px;">Pengawas Ruang <small class="text-muted">(Tarif: Rp {{ number_format($ratePengawas, 0, ',', '.') }})</small></label>
                            <div class="d-flex" style="gap: 8px;">
                                <input type="number" step="any" name="qty_pengawas" id="f_qty_pengawas" class="form-control auto-calc" data-rate="{{ $ratePengawas }}" data-target="#f_nominal_pengawas" placeholder="Jml sesi" style="max-width: 120px;">
                                <input type="text" inputmode="numeric" name="nominal_pengawas" id="f_nominal_pengawas" class="form-control text-end calc-source input-ribuan" value="0">
                            </div>
                        </div>

                        <!-- 4. Pembuatan Soal -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold text-dark" style="font-size: 12px;">Pembuatan Soal</label>
                            <div class="d-flex" style="gap: 8px;">
                                <select id="sel_pemb_soal" class="form-control">
                                    <option value="0">-- Pilih Tarif --</option>
                                    @foreach($tarifs->where('kolom_kategori', 'pemb_soal') as $ts)
                                        <option value="{{ (int)$ts->nominal }}">{{ $ts->nama_komponen }} ({{ number_format($ts->nominal, 0, ',', '.') }})</option>
                                    @endforeach
                                </select>
                                <input type="number" step="any" name="qty_pemb_soal" id="f_qty_pemb_soal" class="form-control" placeholder="Jml" style="width: 85px; min-width: 80px;">
                                <input type="text" inputmode="numeric" name="nominal_pemb_soal" id="f_nominal_pemb_soal" class="form-control text-end calc-source input-ribuan" style="max-width: 120px;" value="0">
                            </div>
                        </div>

                        <!-- 5. Input Soal -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold text-dark" style="font-size: 12px;">Input / Pengetikan Soal</label>
                            <div class="d-flex" style="gap: 8px;">
                                <select id="sel_input_soal" class="form-control">
                                    <option value="0">-- Pilih Tarif --</option>
                                    @foreach($tarifs->where('kolom_kategori', 'input_soal') as $ti)
                                        <option value="{{ (int)$ti->nominal }}">{{ $ti->nama_komponen }} ({{ number_format($ti->nominal, 0, ',', '.') }})</option>
                                    @endforeach
                                </select>
                                <input type="number" step="any" name="qty_input_soal" id="f_qty_input_soal" class="form-control" placeholder="Jml" style="width: 85px; min-width: 80px;">
                                <input type="text" inputmode="numeric" name="nominal_input_soal" id="f_nominal_input_soal" class="form-control text-end calc-source input-ribuan" style="max-width: 120px;" value="0">
                            </div>
                        </div>

                        <!-- 7. Penguji -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold text-dark" style="font-size: 12px;">Penguji</label>
                            <div class="d-flex" style="gap: 8px;">
                                <select id="sel_penguji" class="form-control">
                                    <option value="0">-- Pilih Tarif --</option>
                                    @foreach($tarifs->where('kolom_kategori', 'penguji') as $tuji)
                                        <option value="{{ (int)$tuji->nominal }}">{{ $tuji->nama_komponen }} ({{ number_format($tuji->nominal, 0, ',', '.') }})</option>
                                    @endforeach
                                </select>
                                <input type="number" step="any" name="qty_penguji" id="f_qty_penguji" class="form-control" placeholder="Jml" style="width: 85px; min-width: 80px;">
                                <input type="text" inputmode="numeric" name="nominal_penguji" id="f_nominal_penguji" class="form-control text-end calc-source input-ribuan" style="max-width: 120px;" value="0">
                            </div>
                        </div>

                        <!-- 8. Korektor -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold text-dark" style="font-size: 12px;">Korektor</label>
                            <div class="d-flex" style="gap: 8px;">
                                <select id="sel_korektor" class="form-control">
                                    <option value="0">-- Pilih Tarif --</option>
                                    @foreach($tarifs->where('kolom_kategori', 'korektor') as $tkor)
                                        <option value="{{ (int)$tkor->nominal }}">{{ $tkor->nama_komponen }} ({{ number_format($tkor->nominal, 0, ',', '.') }})</option>
                                    @endforeach
                                </select>
                                <input type="number" step="any" name="qty_korektor" id="f_qty_korektor" class="form-control" placeholder="Jml" style="width: 85px; min-width: 80px;">
                                <input type="text" inputmode="numeric" name="nominal_korektor" id="f_nominal_korektor" class="form-control text-end calc-source input-ribuan" style="max-width: 120px;" value="0">
                            </div>
                        </div>

                        <!-- 9. Input Nilai -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold text-dark" style="font-size: 12px;">Input Nilai</label>
                            <div class="d-flex" style="gap: 8px;">
                                <select id="sel_input_nilai" class="form-control">
                                    <option value="0">-- Pilih Tarif --</option>
                                    @foreach($tarifs->where('kolom_kategori', 'input_nilai') as $tin)
                                        <option value="{{ (int)$tin->nominal }}">{{ $tin->nama_komponen }} ({{ number_format($tin->nominal, 0, ',', '.') }})</option>
                                    @endforeach
                                </select>
                                <input type="number" step="any" name="qty_input_nilai" id="f_qty_input_nilai" class="form-control" placeholder="Jml" style="width: 85px; min-width: 80px;">
                                <input type="text" inputmode="numeric" name="nominal_input_nilai" id="f_nominal_input_nilai" class="form-control text-end calc-source input-ribuan" style="max-width: 120px;" value="0">
                            </div>
                        </div>

                        <!-- 10. Pembimbing -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold text-dark" style="font-size: 12px;">Pembimbing</label>
                            <div class="d-flex" style="gap: 8px;">
                                <select id="sel_pembimbing" class="form-control">
                                    <option value="0">-- Pilih Tarif --</option>
                                    @foreach($tarifs->where('kolom_kategori', 'pembimbing') as $tp)
                                        <option value="{{ (int)$tp->nominal }}">{{ $tp->nama_komponen }} ({{ number_format($tp->nominal, 0, ',', '.') }})</option>
                                    @endforeach
                                </select>
                                <input type="number" step="any" name="qty_pembimbing" id="f_qty_pembimbing" class="form-control" placeholder="Jml" style="width: 85px; min-width: 80px;">
                                <input type="text" inputmode="numeric" name="nominal_pembimbing" id="f_nominal_pembimbing" class="form-control text-end calc-source input-ribuan" style="max-width: 120px;" value="0">
                            </div>
                        </div>
                    </div>

                    <hr class="my-2">

                    <div class="row align-items-end">
                        <div class="col-md-4 mb-3">
                            <label class="form-label font-weight-bold text-dark" style="font-size: 12px;">Potongan (Rp)</label>
                            <input type="text" inputmode="numeric" name="potongan" id="f_potongan" class="form-control text-end calc-source input-ribuan" value="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label font-weight-bold text-dark" style="font-size: 12px;">Catatan <span class="text-muted">(Opsional)</span></label>
                            <input type="text" name="catatan" id="f_catatan" class="form-control" placeholder="Catatan">
                        </div>
                        <div class="col-md-4 mb-3 text-end">
                            <small class="text-muted d-block" style="font-size: 12px;">Total Diterima:</small>
                            <h4 class="font-weight-bold text-success mb-0" id="previewDiterima">Rp 0</h4>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanPanitia">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('vendor/global/global.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    function formatRibuan(angka) {
        if (angka === null || angka === undefined || angka === '') return '0';
        var num = Math.round(parseFloat(angka.toString().replace(/\./g, '')) || 0);
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function parseNominal(str) {
        if (!str) return 0;
        return parseFloat(str.toString().replace(/\./g, '')) || 0;
    }

    $(document).ready(function() {
        $('#tblKepanitiaanDetail').DataTable({
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                },
                search: "Cari:",
                lengthMenu: "Tampil _MENU_",
                emptyTable: "Belum ada data panitia pada kegiatan ini",
                zeroRecords: "Tidak ditemukan data yang sesuai",
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data"
            }
        });

        // Format otomatis input nominal dengan titik ribuan
        $('.input-ribuan').on('input keyup', function() {
            var raw = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(raw ? formatRibuan(raw) : '0');
            hitungTotalModal();
        });

        // Select2 Live Search Umana
        $('#selectUmana').select2({
            dropdownParent: $('#modalTambahPanitia'),
            placeholder: 'Ketik nama atau lembaga...',
            minimumInputLength: 2,
            ajax: {
                url: '{{ url("/kepanitiaan/searchUmana") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return { q: params.term };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                id: item.nik,
                                text: item.info_jabatan ? `${item.nama_gelar} — ${item.info_jabatan}` : item.nama_gelar,
                                nama: item.nama_gelar,
                                nik: item.nik,
                                info_jabatan: item.info_jabatan,
                                lembaga_default: item.lembaga_default
                            };
                        })
                    };
                }
            },
            templateResult: function(item) {
                if (item.loading) return item.text;
                if (!item.nama) return item.text;
                const $el = $(`
                    <div>
                        <div class="umana-opt-name"></div>
                        ${item.info_jabatan ? '<div class="umana-opt-pos"></div>' : ''}
                    </div>
                `);
                $el.find('.umana-opt-name').text(item.nama);
                if (item.info_jabatan) {
                    $el.find('.umana-opt-pos').text(item.info_jabatan);
                }
                return $el;
            },
            templateSelection: function(item) {
                if (!item.id) return item.text;
                const nama = item.nama || item.text || '';
                if (item.info_jabatan) {
                    return `${nama} — ${item.info_jabatan}`;
                }
                return nama;
            }
        }).on('select2:select', function(e) {
            const data = e.params.data;
            $('#f_nik').val(data.nik);
            $('#f_nama_panitia').val(data.nama);
            $('#f_lembaga_asal').val(data.lembaga_default || '');
        });

        // 1. Pilih Jabatan Panitia -> Auto-fill nominal kepanitiaan
        $('#sel_jabatan').on('change', function() {
            const selected = $(this).find('option:selected');
            const val = selected.val();
            const nominal = selected.data('nominal') || 0;
            $('#f_jabatan_panitia').val(val);
            $('#f_nominal_kepanitiaan').val(formatRibuan(nominal));
            hitungTotalModal();
        });

        // 2. Auto-calc kolom volumetrik sederhana (Qty × Rate)
        $('.auto-calc').on('input', function() {
            const qty = parseFloat($(this).val()) || 0;
            const rate = parseFloat($(this).data('rate')) || 0;
            const target = $($(this).data('target'));
            target.val(formatRibuan(Math.round(qty * rate)));
            hitungTotalModal();
        });

        // 3. Multi-option Dropdown calculators
        function bindDropdownCalc(selId, qtyId, targetNominalId) {
            $(`#${selId}, #${qtyId}`).on('change input', function() {
                const rate = parseFloat($(`#${selId}`).val()) || 0;
                const qty = parseFloat($(`#${qtyId}`).val()) || 0;
                $(`#${targetNominalId}`).val(formatRibuan(Math.round(rate * qty)));
                hitungTotalModal();
            });
        }

        bindDropdownCalc('sel_pemb_soal', 'f_qty_pemb_soal', 'f_nominal_pemb_soal');
        bindDropdownCalc('sel_input_soal', 'f_qty_input_soal', 'f_nominal_input_soal');
        bindDropdownCalc('sel_penguji', 'f_qty_penguji', 'f_nominal_penguji');
        bindDropdownCalc('sel_korektor', 'f_qty_korektor', 'f_nominal_korektor');
        bindDropdownCalc('sel_input_nilai', 'f_qty_input_nilai', 'f_nominal_input_nilai');
        bindDropdownCalc('sel_pembimbing', 'f_qty_pembimbing', 'f_nominal_pembimbing');

        function hitungTotalModal() {
            let bruto = 0;
            const sources = [
                '#f_nominal_kepanitiaan', '#f_nominal_kehadiran', '#f_nominal_konsumsi',
                '#f_nominal_pemb_soal', '#f_nominal_input_soal', '#f_nominal_pengawas',
                '#f_nominal_penguji', '#f_nominal_korektor', '#f_nominal_input_nilai',
                '#f_nominal_pembimbing'
            ];
            sources.forEach(selector => {
                bruto += parseNominal($(selector).val());
            });

            const potongan = parseNominal($('#f_potongan').val());
            const netto = Math.max(0, bruto - potongan);

            $('#previewDiterima').text('Rp ' + formatRibuan(netto));
        }

        // Buka modal saat klik Tambah Panitia
        $('#btnTambahPanitia').on('click', function() {
            $('#f_id_detail').val('');
            $('#modalPanitiaTitle').text('Tambah Panitia');
            $('#btnSimpanPanitia').prop('disabled', false).html('Simpan');
            $('#formPanitia')[0].reset();
            $('#selectUmana').val(null).trigger('change');
            $('.input-ribuan').val('0');
            hitungTotalModal();
            $('#modalTambahPanitia').modal('show');
        });

        // Submit Form Panitia
        $('#formPanitia').on('submit', function(e) {
            e.preventDefault();
            if (!$('#f_nama_panitia').val()) {
                Swal.fire('Perhatian', 'Silakan pilih nama umana terlebih dahulu!', 'warning');
                return;
            }

            const btn = $('#btnSimpanPanitia');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

            $.ajax({
                url: '{{ url("/kepanitiaan/saveRow") }}',
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    $('#modalTambahPanitia').modal('hide');
                    btn.prop('disabled', false).html('Simpan');
                    Swal.fire({
                        icon: 'success',
                        title: 'Tersimpan!',
                        text: res.message,
                        timer: 1200,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(err) {
                    btn.prop('disabled', false).html('Simpan');
                    Swal.fire('Gagal', err.responseJSON ? err.responseJSON.message : 'Gagal menyimpan data.', 'error');
                }
            });
        });

        // Action: Edit Panitia
        window.editPanitia = function(d) {
            $('#f_id_detail').val(d.id_kepanitiaan_detail);
            $('#modalPanitiaTitle').text('Edit Panitia: ' + d.nama_panitia);
            $('#btnSimpanPanitia').prop('disabled', false).html('Perbarui');

            $('#f_nik').val(d.nik);
            $('#f_nama_panitia').val(d.nama_panitia);
            $('#f_lembaga_asal').val(d.lembaga_asal);

            const labelOpt = d.lembaga_asal ? `${d.nama_panitia} (${d.lembaga_asal})` : d.nama_panitia;
            const opt = new Option(labelOpt, d.nik || d.nama_panitia, true, true);
            $('#selectUmana').append(opt).trigger('change');

            $('#sel_jabatan').val(d.jabatan_panitia);
            $('#f_jabatan_panitia').val(d.jabatan_panitia);
            $('#f_nominal_kepanitiaan').val(formatRibuan(d.nominal_kepanitiaan));

            $('#f_qty_kehadiran').val(d.qty_kehadiran > 0 ? parseFloat(d.qty_kehadiran) : '');
            $('#f_nominal_kehadiran').val(formatRibuan(d.nominal_kehadiran));

            $('#f_qty_konsumsi').val(d.qty_konsumsi > 0 ? parseFloat(d.qty_konsumsi) : '');
            $('#f_nominal_konsumsi').val(formatRibuan(d.nominal_konsumsi));

            $('#f_qty_pemb_soal').val(d.qty_pemb_soal > 0 ? parseFloat(d.qty_pemb_soal) : '');
            $('#f_nominal_pemb_soal').val(formatRibuan(d.nominal_pemb_soal));

            $('#f_qty_input_soal').val(d.qty_input_soal > 0 ? parseFloat(d.qty_input_soal) : '');
            $('#f_nominal_input_soal').val(formatRibuan(d.nominal_input_soal));

            $('#f_qty_pengawas').val(d.qty_pengawas > 0 ? parseFloat(d.qty_pengawas) : '');
            $('#f_nominal_pengawas').val(formatRibuan(d.nominal_pengawas));

            $('#f_qty_penguji').val(d.qty_penguji > 0 ? parseFloat(d.qty_penguji) : '');
            $('#f_nominal_penguji').val(formatRibuan(d.nominal_penguji));

            $('#f_qty_korektor').val(d.qty_korektor > 0 ? parseFloat(d.qty_korektor) : '');
            $('#f_nominal_korektor').val(formatRibuan(d.nominal_korektor));

            $('#f_qty_input_nilai').val(d.qty_input_nilai > 0 ? parseFloat(d.qty_input_nilai) : '');
            $('#f_nominal_input_nilai').val(formatRibuan(d.nominal_input_nilai));

            $('#f_qty_pembimbing').val(d.qty_pembimbing > 0 ? parseFloat(d.qty_pembimbing) : '');
            $('#f_nominal_pembimbing').val(formatRibuan(d.nominal_pembimbing));

            $('#f_potongan').val(formatRibuan(d.potongan));
            $('#f_catatan').val(d.catatan);

            hitungTotalModal();
            $('#modalTambahPanitia').modal('show');
        };

        // Action: Hapus Row
        window.hapusRow = function(id) {
            Swal.fire({
                title: 'Hapus Panitia Ini?',
                text: 'Data rincian panitia ini akan dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((res) => {
                if (res.value || res.isConfirmed) {
                    $.ajax({
                        url: `{{ url('/kepanitiaan/deleteRow') }}/${id}`,
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(resp) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: resp.message,
                                timer: 1200,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function() {
                            Swal.fire('Gagal', 'Tidak dapat menghapus data.', 'error');
                        }
                    });
                }
            });
        };
    });
</script>
</body>
</html>
