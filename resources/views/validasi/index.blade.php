@extends('layouts.app')

@section('title', 'Validasi Barokah - E-Barokah')
@section('page_title')
<span class="text-danger font-weight-bold">Validasi</span> / Daftar Lembaga dan Jumlah Kehadiran
@endsection

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <style>
        /* Table Styling identik dengan menu Master Data & Manajemen lainnya */
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

        /* Filter per Kolom Styling */
        .filters th {
            padding: 6px 8px !important;
            border-bottom: 2px solid #eef2f6 !important;
            background-color: #fff !important;
        }
        .filters input {
            width: 100%;
            padding: 6px 10px;
            font-size: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background-color: #fafafa;
            color: #495057;
            font-weight: normal;
            transition: all 0.2s ease;
        }
        .filters input:focus {
            border-color: #00a15d;
            background-color: #fff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 161, 93, 0.15);
        }

        .filter-status-select {
            border-radius: 20px;
            font-size: 13px;
            padding: 6px 16px;
            border: 1px solid #e2e8f0;
            background-color: #fff;
            color: #495057;
            font-weight: 500;
            cursor: pointer;
            outline: none;
            transition: all 0.2s ease;
        }
        .filter-status-select:focus {
            border-color: #00a15d;
            box-shadow: 0 0 0 2px rgba(0, 161, 93, 0.15);
        }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h4 class="card-title">Daftar Lembaga dan Jumlah Kehadiran</h4>
                <div class="d-flex align-items-center gap-2">
                    <select id="filterStatus" class="form-select form-select-sm filter-status-select">
                        <option value="antrian" selected>Antrian Validasi</option>
                        <option value="Terkirim">Belum Dikoreksi</option>
                        <option value="acc">Sudah Dikoreksi</option>
                        <option value="selesai">Sudah Ditransfer</option>
                        <option value="all">Semua Status</option>
                    </select>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabel_validasi" class="display" style="min-width: 845px; width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nama Lembaga</th>
                                <th>Periode</th>
                                <th>Jumlah Umana</th>
                                <th>Koreksi</th>
                                <th>Status</th>
                            </tr>
                            <tr class="filters">
                                <th></th>
                                <th><input type="text" placeholder="Cari Lembaga"></th>
                                <th><input type="text" placeholder="Cari Periode"></th>
                                <th><input type="text" placeholder="Cari Kategori"></th>
                                <th></th>
                                <th><input type="text" placeholder="Cari Status"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data dimuat via AJAX DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Datatable JS -->
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script>
    $(function () {
        var table = $('#tabel_validasi').DataTable({
            processing: true,
            serverSide: false,
            orderCellsTop: true,
            ajax: {
                url: '{{ url("/validasi/data") }}',
                type: 'GET',
                data: function (d) {
                    d.status_filter = $('#filterStatus').val();
                }
            },
            initComplete: function () {
                var api = this.api();
                api.columns().every(function (i) {
                    var column = this;
                    $('input', $('.filters th').eq(i)).on('keyup change clear', function () {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                    });
                });
            },
            columns: [
                { 
                    data: null, 
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    },
                    className: 'font-weight-bold text-muted' 
                },
                { 
                    data: 'nama_lembaga', 
                    render: function (data) {
                        return `<span class="text-dark font-weight-bold">${data}</span>`;
                    }
                },
                { 
                    data: 'periode',
                    render: function (data) {
                        return `<span style="color: #333; font-weight: 500;">${data}</span>`;
                    }
                },
                { 
                    data: 'kategori',
                    render: function (data) {
                        return `<span style="color: #333;">${data}</span>`;
                    }
                },
                { 
                    data: 'koreksi', 
                    orderable: false 
                },
                { 
                    data: 'status_html'
                }
            ],
            order: [[0, 'asc']],
            pageLength: 10,
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                zeroRecords: "Tidak ada data yang cocok",
                emptyTable: "Tidak ada data validasi kehadiran",
                paginate: {
                    previous: '<i class="fas fa-chevron-left"></i>',
                    next: '<i class="fas fa-chevron-right"></i>'
                }
            }
        });

        // Filter status change
        $('#filterStatus').on('change', function () {
            table.ajax.reload();
        });
    });
    </script>
@endpush
