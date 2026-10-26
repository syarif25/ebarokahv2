@extends('layouts.app')

@section('title', 'Laporan Waktu Pengajuan - E-Barokah')

@section('page_title')
<span class="text-danger font-weight-bold">Laporan</span> / Laporan Waktu Pengajuan
@endsection

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        /* Form Label & Filter Card */
        .filter-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #eef2f6;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .filter-label {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Modern Select2 Styling Matching V2 Theme */
        .select2-container--default .select2-selection--single {
            border: 1px solid #cbd5e1 !important;
            border-radius: 10px !important;
            height: 42px !important;
            padding: 6px 14px !important;
            background-color: #ffffff !important;
            display: flex !important;
            align-items: center !important;
            transition: all 0.2s ease !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            font-size: 13.5px !important;
            color: #1e293b !important;
            font-weight: 500 !important;
            padding-left: 0 !important;
            padding-right: 20px !important;
            line-height: normal !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 10px !important;
        }
        .select2-container--default.select2-container--open .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #00a15d !important;
            box-shadow: 0 0 0 3px rgba(0, 161, 93, 0.15) !important;
            outline: none !important;
        }
        .select2-dropdown {
            border: 1px solid #cbd5e1 !important;
            border-radius: 10px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            overflow: hidden !important;
            z-index: 9999 !important;
            background-color: #fff !important;
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
            color: #334155 !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #00a15d !important;
            color: #fff !important;
        }
        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #ecfdf5 !important;
            color: #065f46 !important;
            font-weight: 600 !important;
        }

        /* Clean Table Styling */
        table.dataTable.display tbody td, 
        table.dataTable.display tbody th, 
        table.dataTable.display thead th {
            border-left: none !important;
            border-right: none !important;
            padding: 1rem 1rem !important;
            vertical-align: middle;
        }
        table.dataTable.display thead th {
            border-top: none !important;
            border-bottom: 2px solid #e2e8f0 !important;
            font-weight: 700 !important;
            font-size: 0.8rem !important;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #334155 !important;
            background-color: #f8fafc;
        }
        table.dataTable.display tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* Badge Pewarnaan Tegas & Kontras */
        .badge-kategori-struktural {
            background-color: #7c3aed !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            padding: 6px 12px !important;
            border-radius: 8px !important;
            letter-spacing: 0.3px;
        }
        .badge-kategori-pengajar {
            background-color: #0284c7 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            padding: 6px 12px !important;
            border-radius: 8px !important;
            letter-spacing: 0.3px;
        }
        .badge-kategori-satpam {
            background-color: #d97706 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            padding: 6px 12px !important;
            border-radius: 8px !important;
            letter-spacing: 0.3px;
        }
        .badge-kategori-default {
            background-color: #64748b !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            padding: 6px 12px !important;
            border-radius: 8px !important;
        }

        /* Waktu Badges */
        .badge-waktu-upload {
            background-color: #eff6ff !important;
            color: #1d4ed8 !important;
            border: 1px solid #bfdbfe !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            padding: 6px 10px !important;
            border-radius: 8px !important;
            display: inline-block;
        }
        .badge-waktu-acc {
            background-color: #fdf4ff !important;
            color: #9333ea !important;
            border: 1px solid #f0abfc !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            padding: 6px 10px !important;
            border-radius: 8px !important;
            display: inline-block;
        }
        .badge-waktu-transfer {
            background-color: #f0fdf4 !important;
            color: #15803d !important;
            border: 1px solid #bbf7d0 !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            padding: 6px 10px !important;
            border-radius: 8px !important;
            display: inline-block;
        }

        /* Durasi Badges */
        .badge-durasi-days {
            background-color: #fef2f2 !important;
            color: #dc2626 !important;
            border: 1px solid #fecaca !important;
            font-size: 12.5px !important;
            font-weight: 700 !important;
            padding: 6px 12px !important;
            border-radius: 8px !important;
            display: inline-flex;
            align-items: center;
        }
        .badge-durasi-hours {
            background-color: #fffbeb !important;
            color: #b45309 !important;
            border: 1px solid #fde68a !important;
            font-size: 12.5px !important;
            font-weight: 700 !important;
            padding: 6px 12px !important;
            border-radius: 8px !important;
            display: inline-flex;
            align-items: center;
        }
        .badge-durasi-minutes {
            background-color: #f0fdf4 !important;
            color: #166534 !important;
            border: 1px solid #bbf7d0 !important;
            font-size: 12.5px !important;
            font-weight: 700 !important;
            padding: 6px 12px !important;
            border-radius: 8px !important;
            display: inline-flex;
            align-items: center;
        }

        /* Button Styling */
        .btn-export-excel {
            background-color: #00a15d !important;
            border-color: #00a15d !important;
            color: #ffffff !important;
            font-weight: 600;
            border-radius: 10px;
            padding: 9px 20px;
            transition: all 0.2s ease;
        }
        .btn-export-excel:hover {
            background-color: #00874e !important;
            border-color: #00874e !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 161, 93, 0.25);
        }
        .btn-reset-filter {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #475569;
            font-weight: 600;
            border-radius: 10px;
            padding: 9px 16px;
            transition: all 0.2s ease;
        }
        .btn-reset-filter:hover {
            background-color: #e2e8f0;
            color: #1e293b;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row page-titles mx-0 mb-4">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="text-primary font-weight-bold" style="color: #00a15d !important;">Laporan Waktu Pengajuan</h4>
                <p class="mb-0 text-muted">Memantau durasi dari upload kehadiran hingga ditransfer</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)" class="text-muted">Laporan</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)" style="color: #ff5e5e !important; font-weight: 600;">Laporan Waktu Pengajuan</a></li>
            </ol>
        </div>
    </div>

    <!-- Kotak Filter Terstruktur Rapi -->
    <div class="filter-card">
        <div class="row align-items-end g-3">
            <div class="col-lg-3 col-md-5">
                <label class="filter-label">
                    <i class="far fa-calendar-alt text-primary"></i> Filter Bulan
                </label>
                <select class="form-control select2-filter" id="filter_bulan">
                    <option value="">-- Semua Bulan --</option>
                    @foreach($bulanList as $bln)
                        <option value="{{ $bln }}">{{ $bln }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 col-md-4">
                <label class="filter-label">
                    <i class="far fa-calendar text-primary"></i> Filter Tahun
                </label>
                <select class="form-control select2-filter" id="filter_tahun">
                    <option value="">-- Semua Tahun --</option>
                    @foreach($tahunList as $thn)
                        <option value="{{ $thn }}">{{ $thn }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-3">
                <button type="button" id="btn_reset_filter" class="btn btn-reset-filter w-100 d-inline-flex align-items-center justify-content-center gap-2">
                    <i class="fas fa-undo"></i> Reset Filter
                </button>
            </div>
            <div class="col-lg-4 col-md-12 text-md-end mt-3 mt-lg-0">
                <button type="button" id="btn_export_excel" class="btn btn-export-excel d-inline-flex align-items-center gap-2">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            </div>
        </div>
    </div>

    <!-- Card Tabel Data -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 16px;">
                <div class="card-header border-0 pb-0 pt-4 px-4 bg-transparent d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title font-weight-bold text-dark mb-1">Daftar Durasi Waktu Pengajuan Barokah</h4>
                        <p class="text-muted fs-13 mb-0">Rincian log waktu upload pengajuan, ACC, hingga proses transfer selesai.</p>
                    </div>
                </div>
                <div class="card-body px-4 pt-3">
                    <div class="table-responsive">
                        <table id="tabel_waktu" class="table display responsive nowrap w-100" style="min-width: 950px">
                            <thead>
                                <tr>
                                    <th style="width: 50px;" class="text-center">No</th>
                                    <th>Nama Lembaga</th>
                                    <th class="text-center">Kategori</th>
                                    <th class="text-nowrap text-center">Bulan - Tahun</th>
                                    <th class="text-nowrap text-center">Waktu Upload</th>
                                    <th class="text-nowrap text-center">Waktu ACC</th>
                                    <th class="text-nowrap text-center">Waktu Transfer</th>
                                    <th class="text-nowrap text-center">Durasi Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            // Hilangkan alert dialog bawaan DataTables jika ada error jaringan/parser, alihkan ke console
            $.fn.dataTable.ext.errMode = 'none';

            // Inisialisasi Select2
            $('.select2-filter').select2({
                minimumResultsForSearch: 10,
                width: '100%'
            });

            // Inisialisasi DataTable Server-Side
            var table = $('#tabel_waktu').DataTable({ 
                processing: true, 
                serverSide: true, 
                order: [[0, 'desc']], 
                ajax: {
                    url: "{{ url('/laporan/waktu-pengajuan/data') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function ( d ) {
                        d.filter_bulan = $('#filter_bulan').val();
                        d.filter_tahun = $('#filter_tahun').val();
                    },
                    error: function(xhr, error, code) {
                        console.error('DataTables Ajax error:', xhr, error, code);
                    }
                },
                columns: [
                    { data: 'no', name: 'no', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'nama_lembaga', name: 'l.nama_lembaga' },
                    { data: 'kategori', name: 'kl.kategori', className: 'text-center' },
                    { data: 'bulan_tahun', name: 'kl.bulan', className: 'text-center' },
                    { data: 'waktu_upload', name: 'waktu_upload', className: 'text-center' },
                    { data: 'waktu_acc', name: 'waktu_acc', className: 'text-center' },
                    { data: 'waktu_transfer', name: 'waktu_transfer', className: 'text-center' },
                    { data: 'durasi', name: 'durasi', orderable: false, searchable: false, className: 'text-center' }
                ],
                columnDefs: [
                    { 
                        targets: [ 0, 7 ], 
                        orderable: false 
                    }
                ],
                language: {
                    search: "Search:",
                    searchPlaceholder: "Cari lembaga / kategori...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    emptyTable: "Tidak ada data di tabel",
                    processing: '<div class="spinner-border text-success" role="status"><span class="sr-only">Loading...</span></div>',
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>' 
                    }
                }
            });

            // Reload saat filter berubah
            $('#filter_bulan, #filter_tahun').on('change', function() {
                table.ajax.reload();
            });

            // Tombol Reset Filter
            $('#btn_reset_filter').on('click', function() {
                $('#filter_bulan').val('').trigger('change.select2');
                $('#filter_tahun').val('').trigger('change.select2');
                table.search('').draw();
                table.ajax.reload();
            });

            // Action Export Excel
            $('#btn_export_excel').on('click', function(e) {
                e.preventDefault();
                var bulan = $('#filter_bulan').val();
                var tahun = $('#filter_tahun').val();
                var search = table.search();

                var exportUrl = "{{ url('/laporan/waktu-pengajuan/export-excel') }}" + 
                    "?bulan=" + encodeURIComponent(bulan) + 
                    "&tahun=" + encodeURIComponent(tahun) + 
                    "&search=" + encodeURIComponent(search);

                window.location.href = exportUrl;
            });
        });
    </script>
@endpush
