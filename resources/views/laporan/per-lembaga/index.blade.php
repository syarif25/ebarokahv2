@extends('layouts.app')

@section('title', 'Laporan Barokah Per Lembaga - E-Barokah')

@section('page_title')
<span class="text-danger font-weight-bold">Laporan</span> / Per Lembaga
@endsection

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/bootstrap-datepicker-master/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
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
            <!-- Header Card persis seperti di gambar -->
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title font-weight-bold mb-1">Laporan Barokah Per Lembaga</h4>
                    <p class="text-muted fs-13 mb-0">
                        <i class="fas fa-info-circle text-muted me-1"></i> Menampilkan daftar barokah yang telah dibayarkan per lembaga
                    </p>
                </div>
                <div>
                    <button type="button" class="btn btn-refresh-custom shadow-sm" onclick="refreshTable()">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                </div>
            </div>

            <div class="card-body">
                <!-- Section Filter persis seperti di gambar -->
                <div class="row align-items-end g-3 mb-4">
                    <div class="col-lg-3 col-md-6">
                        <label class="filter-label">Filter Lembaga</label>
                        <select class="form-select filter-control" id="filter_lembaga" style="width: 100%;">
                            <option value="">Pilih Lembaga</option>
                            @foreach($lembagaList as $lem)
                                <option value="{{ $lem->id_lembaga }}__{{ $lem->kategori }}" data-kategori="{{ $lem->kategori }}">
                                    {{ $lem->nama_lembaga }} - {{ $lem->kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="filter-label">Kategori</label>
                        <select class="form-select filter-control" id="filter_kategori">
                            <option value="Semua Kategori">Semua Kategori</option>
                            <option value="Struktural">Struktural</option>
                            <option value="Pengajar">Pengajar</option>
                            <option value="Satpam">Satpam</option>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="filter-label">Filter Periode</label>
                        <input type="text" class="form-control filter-control" id="filter_periode" placeholder="Pilih Bulan & Tahun" autocomplete="off" readonly style="background-color: #fff; cursor: pointer;">
                        <input type="hidden" id="filter_bulan">
                        <input type="hidden" id="filter_tahun">
                    </div>

                    <div class="col-lg-3 col-md-6 d-flex gap-2">
                        <button type="button" class="btn btn-filter-custom flex-grow-1 shadow-sm" onclick="applyFilter()">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <button type="button" class="btn btn-reset-custom flex-grow-1 shadow-sm" onclick="resetFilter()">
                            <i class="fas fa-redo me-1"></i> Reset
                        </button>
                    </div>
                </div>

                <!-- DataTable Laporan Per Lembaga -->
                <div class="table-responsive">
                    <table id="tabel_laporan_lembaga" class="display" style="min-width: 845px; width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Nama Lembaga</th>
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
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap-datepicker-master/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>

    <script>
    var table;
    var allLembaga = [
        @foreach($lembagaList as $lem)
            {
                id: "{{ $lem->id_lembaga }}__{{ $lem->kategori }}",
                text: "{{ $lem->nama_lembaga }} - {{ $lem->kategori }}",
                kategori: "{{ $lem->kategori }}"
            },
        @endforeach
    ];

    $(document).ready(function () {
        // Registrasi Locale Indonesia untuk Bootstrap Datepicker
        $.fn.datepicker.dates['id'] = {
            days: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"],
            daysShort: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
            daysMin: ["Mg", "Sn", "Sl", "Rb", "Km", "Jm", "Sb"],
            months: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
            monthsShort: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"],
            today: "Hari Ini",
            clear: "Kosongkan",
            format: "MM yyyy",
            titleFormat: "MM yyyy",
            weekStart: 1
        };

        // Inisialisasi Select2 dengan Search Box pada Filter Lembaga
        $('#filter_lembaga').select2({
            placeholder: "Pilih Lembaga",
            allowClear: true,
            width: '100%',
            language: {
                noResults: function () {
                    return "Lembaga tidak ditemukan";
                }
            }
        });

        // Inisialisasi Month Picker untuk Filter Periode
        $('#filter_periode').datepicker({
            format: "MM yyyy",
            startView: "months",
            minViewMode: "months",
            language: 'id',
            autoclose: true,
            todayHighlight: true,
            clearBtn: true,
            orientation: "bottom auto"
        }).on('changeDate', function(e) {
            if (e.date) {
                var monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                                  "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                $('#filter_bulan').val(monthNames[e.date.getMonth()]);
                $('#filter_tahun').val(e.date.getFullYear());
            } else {
                $('#filter_bulan').val('');
                $('#filter_tahun').val('');
            }
        }).on('clearDate change', function() {
            if (!$(this).val()) {
                $('#filter_bulan').val('');
                $('#filter_tahun').val('');
            }
        });

        // Filter dropdown Lembaga otomatis sesuai Kategori yang dipilih
        $('#filter_kategori').on('change', function() {
            renderLembagaOptions($(this).val());
        });

        // Inisialisasi DataTable
        table = $('#tabel_laporan_lembaga').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: '{{ url("/laporan/per-lembaga/data") }}',
                type: 'POST',
                data: function (d) {
                    d._token = '{{ csrf_token() }}';
                    var selectedLembaga = $('#filter_lembaga').val() || '';
                    var lemId = '';
                    var lemKat = '';
                    if (selectedLembaga) {
                        var parts = selectedLembaga.split('__');
                        lemId = parts[0];
                        lemKat = parts[1] || '';
                    }
                    d.filter_lembaga = lemId;
                    d.filter_lembaga_kategori = lemKat;
                    d.filter_kategori = $('#filter_kategori').val();
                    d.filter_bulan = $('#filter_bulan').val();
                    d.filter_tahun = $('#filter_tahun').val();
                    d.filter_periode = $('#filter_periode').val();
                }
            },
            columns: [
                { 
                    data: 'no',
                    className: 'text-center font-weight-bold text-muted'
                },
                { 
                    data: 'nama_lembaga',
                    render: function (data) {
                        return `<span class="text-dark font-weight-bold">${data}</span>`;
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
                    render: function (data) {
                        return `<span class="font-weight-bold" style="color: #16a34a; font-size: 14px;">${data}</span>`;
                    }
                },
                { 
                    data: 'aksi',
                    orderable: false,
                    className: 'text-center'
                }
            ],
            order: [[0, 'asc']],
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

    function renderLembagaOptions(selectedKategori) {
        var currentVal = $('#filter_lembaga').val();
        $('#filter_lembaga').empty();
        $('#filter_lembaga').append(new Option('Pilih Lembaga', '', false, false));

        var keepVal = false;
        allLembaga.forEach(function(item) {
            if (!selectedKategori || selectedKategori === 'Semua Kategori' || item.kategori === selectedKategori) {
                var isSelected = (item.id === currentVal);
                if (isSelected) keepVal = true;
                var opt = new Option(item.text, item.id, isSelected, isSelected);
                $(opt).attr('data-kategori', item.kategori);
                $('#filter_lembaga').append(opt);
            }
        });

        if (!keepVal) {
            $('#filter_lembaga').val('').trigger('change.select2');
        } else {
            $('#filter_lembaga').trigger('change.select2');
        }
    }

    function refreshTable() {
        table.ajax.reload(null, false);
    }

    function applyFilter() {
        table.ajax.reload();
    }

    function resetFilter() {
        $('#filter_kategori').val('Semua Kategori');
        renderLembagaOptions('Semua Kategori');
        $('#filter_lembaga').val('').trigger('change.select2');
        $('#filter_periode').val('').datepicker('clearDates');
        $('#filter_bulan').val('');
        $('#filter_tahun').val('');
        table.ajax.reload();
    }
    </script>
@endpush
