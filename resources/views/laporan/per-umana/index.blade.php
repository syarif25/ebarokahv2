@extends('layouts.app')

@section('title', 'Laporan Barokah Per Umana - E-Barokah')

@section('page_title')
<span class="text-danger font-weight-bold">Laporan</span> / Per Umana
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
            padding: 1.25rem 1rem !important;
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
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <!-- Header Card identik seperti bawaan template -->
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title font-weight-bold mb-1">Daftar Barokah Per Umana'</h4>
                    <p class="text-muted fs-13 mb-0">
                        <i class="fas fa-info-circle text-muted me-1"></i> Data Umana' penerima barokah beserta riwayat detailnya
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm font-weight-bold" onclick="exportExcel()">
                        <i class="fas fa-file-excel me-1"></i> Export XLS
                    </button>
                    <button type="button" class="btn btn-refresh-custom shadow-sm" onclick="refreshTable()">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                </div>
            </div>

            <div class="card-body">
                <!-- Section Filter identik bawaan template -->
                <div class="row align-items-end g-3 mb-4">
                    <div class="col-lg-3 col-md-6">
                        <label class="filter-label">Filter Lembaga</label>
                        <select class="form-select filter-control" id="filter_lembaga" style="width: 100%;">
                            <option value="">Semua Lembaga</option>
                            @foreach($daftarLembaga as $lem)
                                <option value="{{ $lem->id_lembaga }}" {{ $selectedLembaga == $lem->id_lembaga ? 'selected' : '' }}>
                                    {{ $lem->nama_lembaga }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="filter-label">Kategori</label>
                        <select class="form-select filter-control" id="filter_kategori">
                            <option value="">Semua Kategori</option>
                            <option value="Struktural">Struktural</option>
                            <option value="Pengajar">Pengajar</option>
                            <option value="Satpam">Satpam</option>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label class="filter-label">Bulan</label>
                        <select class="form-select filter-control" id="filter_bulan">
                            @foreach($daftarBulan as $bln)
                                <option value="{{ $bln }}" {{ $selectedBulan == $bln ? 'selected' : '' }}>
                                    {{ $bln }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <label class="filter-label">Tahun</label>
                        <select class="form-select filter-control" id="filter_tahun">
                            @foreach($daftarTahun as $th)
                                <option value="{{ $th }}" {{ $selectedTahun == $th ? 'selected' : '' }}>
                                    {{ $th }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6 d-flex gap-2">
                        <button type="button" class="btn btn-filter-custom flex-grow-1 shadow-sm" onclick="applyFilter()">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <button type="button" class="btn btn-reset-custom flex-grow-1 shadow-sm" onclick="resetFilter()">
                            <i class="fas fa-redo me-1"></i> Reset
                        </button>
                    </div>
                </div>

                <!-- DataTable Laporan Per Umana -->
                <div class="table-responsive">
                    <table id="tabel_perumana" class="display" style="min-width: 845px; width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Nama Lengkap</th>
                                <th>Lembaga</th>
                                <th class="text-center">Bulan</th>
                                <th class="text-center">Tahun</th>
                                <th class="text-center">Kategori</th>
                                <th class="text-end">Jumlah Nominal</th>
                                <th style="width: 110px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data dimuat via AJAX DataTables -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-center font-weight-bold">TOTAL</th>
                                <th class="text-end font-weight-bold" id="foot_total_nominal" style="color: #16a34a; font-size: 14px;">Rp 0</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
    var dataTable;

    $(document).ready(function() {
        if ($.fn.select2) {
            $('#filter_lembaga').select2({
                placeholder: "Semua Lembaga",
                allowClear: true,
                width: '100%'
            });
        }

        dataTable = $('#tabel_perumana').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ url('/laporan/per-umana/data') }}",
                type: "POST",
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                    d.tahun = $('#filter_tahun').val();
                    d.bulan = $('#filter_bulan').val();
                    d.id_lembaga = $('#filter_lembaga').val();
                    d.kategori = $('#filter_kategori').val();
                },
                dataSrc: function(json) {
                    if (json && json.summary) {
                        $('#foot_total_nominal').text(json.summary.grand_total_rp || 'Rp 0');
                    }
                    return json.data || [];
                }
            },
            columns: [
                { 
                    data: 'no', 
                    className: 'text-center font-weight-bold text-muted' 
                },
                { 
                    data: 'nama_html',
                    render: function(data) {
                        return data;
                    }
                },
                { 
                    data: 'lembaga',
                    render: function(data) {
                        return `<span class="text-dark">${data}</span>`;
                    }
                },
                { 
                    data: 'bulan', 
                    className: 'text-center' 
                },
                { 
                    data: 'tahun', 
                    className: 'text-center' 
                },
                { 
                    data: 'kategori_html', 
                    className: 'text-center' 
                },
                { 
                    data: 'nominal', 
                    className: 'text-end',
                    render: function(data) {
                        return `<span class="font-weight-bold" style="color: #16a34a; font-size: 14px;">Rp ${data}</span>`;
                    }
                },
                { 
                    data: 'aksi', 
                    className: 'text-center', 
                    orderable: false 
                }
            ],
            order: [[1, 'asc']],
            pageLength: 10,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                zeroRecords: "Data tidak ditemukan",
                emptyTable: "Tidak ada data laporan",
                paginate: {
                    previous: '<i class="fas fa-chevron-left"></i>',
                    next: '<i class="fas fa-chevron-right"></i>'
                }
            }
        });
    });

    function applyFilter() {
        if (dataTable) {
            dataTable.ajax.reload();
        }
    }

    function resetFilter() {
        $('#filter_tahun').val("{{ $selectedTahun }}");
        $('#filter_bulan').val("{{ $selectedBulan }}");
        $('#filter_kategori').val("");
        if ($.fn.select2) {
            $('#filter_lembaga').val("").trigger('change');
        } else {
            $('#filter_lembaga').val("");
        }
        applyFilter();
    }

    function refreshTable() {
        if (dataTable) {
            dataTable.ajax.reload(null, false);
        }
    }

    function exportExcel() {
        var bulan = $('#filter_bulan').val();
        var tahun = $('#filter_tahun').val();
        var idLembaga = $('#filter_lembaga').val();
        var kategori = $('#filter_kategori').val();

        var params = new URLSearchParams();
        if (bulan) params.set('bulan', bulan);
        if (tahun) params.set('tahun', tahun);
        if (idLembaga) params.set('id_lembaga', idLembaga);
        if (kategori) params.set('kategori', kategori);

        window.location.href = "{{ url('/laporan/per-umana/export-excel') }}?" + params.toString();
    }
    </script>
@endpush
