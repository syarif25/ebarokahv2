@extends('layouts.app')

@section('title', 'Rekap Kehadiran Pengajar - E-Barokah')

@section('page_title')
<span class="text-danger font-weight-bold">Laporan</span> / Rekap Kehadiran Pengajar
@endsection

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
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

        .filter-label {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
        }
        .filter-control {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            height: 42px;
            font-size: 13px;
            color: #334155;
            padding: 8px 14px;
            transition: all 0.2s;
        }
        .filter-control:focus {
            border-color: #00a15d;
            box-shadow: 0 0 0 3px rgba(0, 161, 93, 0.12);
        }

        /* Tombol Cari Style Hijau V2 */
        .btn-cari-custom {
            background-color: #00a15d;
            border-color: #00a15d;
            color: #fff;
            border-radius: 10px;
            height: 42px;
            padding: 0 24px;
            font-weight: 600;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .btn-cari-custom:hover {
            background-color: #00874e;
            border-color: #00874e;
            color: #fff;
            transform: translateY(-1px);
        }

        /* Tabel Pivot */
        #table_pivot {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        #table_pivot thead th {
            background-color: #f8fafc !important;
            color: #1e293b !important;
            font-weight: 700 !important;
            font-size: 13px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            text-align: center;
            vertical-align: middle;
            padding: 12px 14px;
        }
        #table_pivot tbody td {
            font-size: 13px !important;
            color: #334155 !important;
            vertical-align: middle;
            padding: 10px 14px;
        }

        /* Bulatan badge angka kehadiran */
        .badge-hadir {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            height: 28px;
            border-radius: 50%;
            font-size: 12px;
            font-weight: 700;
        }
        .badge-hadir-green {
            background-color: #86efac;
            color: #14532d;
        }
        .badge-hadir-zero {
            background-color: #fee2e2;
            color: #ef4444;
        }

        /* Styling buttons DataTables */
        .dt-buttons .btn {
            font-size: 12px !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            padding: 6px 14px !important;
        }
        .dt-button.btn-excel {
            background-color: #f1f5f9 !important;
            color: #334155 !important;
            border: 1px solid #cbd5e1 !important;
        }
        .dt-button.btn-excel:hover {
            background-color: #e2e8f0 !important;
        }
        .dt-button.btn-print {
            background-color: #f1f5f9 !important;
            color: #334155 !important;
            border: 1px solid #cbd5e1 !important;
        }
        .dt-button.btn-print:hover {
            background-color: #e2e8f0 !important;
        }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title font-weight-bold mb-1">Rekap Kehadiran Pengajar</h4>
                <p class="text-muted fs-13 mb-0">
                    <i class="fas fa-info-circle text-muted me-1"></i> Filter laporan berdasarkan lembaga dan rentang waktu (Mulai tahun 2025).
                </p>
            </div>

            <div class="card-body">
                <!-- Form Filter -->
                <div class="row align-items-end g-3 mb-3">
                    <div class="col-lg-4 col-md-12">
                        <label class="filter-label">Lembaga <span class="text-danger">*</span></label>
                        <select class="form-select filter-control select2" id="filter_lembaga" style="width: 100%;">
                            <option value="">-- Pilih Lembaga --</option>
                            @foreach($lembagaList as $l)
                                <option value="{{ $l->id_lembaga }}">{{ $l->nama_lembaga }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6 col-6">
                        <label class="filter-label">Dari Bulan</label>
                        <select class="form-select filter-control" id="start_month">
                            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $m)
                                <option value="{{ $m }}" {{ $m === 'Februari' ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6 col-6">
                        <label class="filter-label">Dari Tahun</label>
                        <input type="number" class="form-control filter-control" id="start_year" value="2026" min="2025">
                    </div>

                    <div class="col-lg-2 col-md-6 col-6">
                        <label class="filter-label">Sampai Bulan</label>
                        <select class="form-select filter-control" id="end_month">
                            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $m)
                                <option value="{{ $m }}" {{ $m === 'Juni' ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6 col-6">
                        <label class="filter-label">Sampai Tahun</label>
                        <input type="number" class="form-control filter-control" id="end_year" value="2026" min="2025">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-cari-custom shadow-sm" id="btn_filter">
                            <i class="fas fa-search me-1"></i> Cari
                        </button>
                    </div>
                </div>

                <!-- Tabel Pivot -->
                <div class="table-responsive">
                    <table id="table_pivot" class="table table-bordered table-hover align-middle mb-0" style="width: 100%;">
                        <thead id="table_pivot_head">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nama Pengajar</th>
                                <th>Lembaga</th>
                            </tr>
                        </thead>
                        <tbody id="table_pivot_body">
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
    <!-- DataTables Buttons & Export Dependencies -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>

    <script>
    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2').select2({
                placeholder: "-- Pilih Lembaga --",
                allowClear: true
            });
        }

        // Inisialisasi awal tabel dalam kondisi kosong
        var pivotTable = $('#table_pivot').DataTable({
            searching: false,
            paging: false,
            info: false,
            ordering: false,
            language: {
                emptyTable: "Silakan pilih filter lembaga dan rentang waktu lalu klik Cari"
            }
        });

        $('#btn_filter').click(function() {
            var id_lembaga = $('#filter_lembaga').val();
            var start_month = $('#start_month').val();
            var start_year = $('#start_year').val();
            var end_month = $('#end_month').val();
            var end_year = $('#end_year').val();

            if (!id_lembaga) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Pilih lembaga terlebih dahulu',
                    confirmButtonColor: '#00a15d'
                });
                return;
            }

            if (!start_year || !end_year) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian',
                    text: 'Tahun tidak boleh kosong',
                    confirmButtonColor: '#00a15d'
                });
                return;
            }

            var oldBtnHtml = $('#btn_filter').html();
            $('#btn_filter').html('<i class="fas fa-spinner fa-spin me-1"></i> Memuat...').prop('disabled', true);

            $.ajax({
                url: "{{ url('/laporan/rekap-kehadiran-pengajar/get-data') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id_lembaga: id_lembaga,
                    start_month: start_month,
                    start_year: start_year,
                    end_month: end_month,
                    end_year: end_year
                },
                dataType: "json",
                success: function(response) {
                    $('#btn_filter').html(oldBtnHtml).prop('disabled', false);

                    if (response.status === 'error') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message,
                            confirmButtonColor: '#00a15d'
                        });
                        return;
                    }

                    // Hancurkan instance DataTable sebelumnya
                    if ($.fn.DataTable.isDataTable('#table_pivot')) {
                        $('#table_pivot').DataTable().destroy();
                    }

                    // Susun ulang Header Kolom Dinamis
                    var thead = '<tr><th style="width: 50px;">No</th><th>Nama Pengajar</th><th>Lembaga</th>';
                    $.each(response.columns, function(index, col) {
                        thead += '<th class="text-center">' + col + '</th>';
                    });
                    thead += '</tr>';
                    $('#table_pivot_head').html(thead);

                    // Susun ulang Body Baris Data
                    var tbody = '';
                    if (response.data.length === 0) {
                        var colspan = 3 + response.columns.length;
                        tbody = '<tr><td colspan="' + colspan + '" class="text-center py-4 text-muted">Tidak ada data ditemukan untuk periode yang dipilih</td></tr>';
                    } else {
                        $.each(response.data, function(index, row) {
                            tbody += '<tr>';
                            tbody += '<td class="text-center font-weight-bold">' + (index + 1) + '</td>';
                            tbody += '<td><strong>' + row.nama + '</strong></td>';
                            tbody += '<td>' + row.lembaga + '</td>';

                            $.each(response.columns, function(i, col) {
                                var val = row.bulan_data[col] !== undefined ? row.bulan_data[col] : 0;
                                var badgeCls = val === 0 ? 'badge-hadir-zero' : 'badge-hadir-green';
                                tbody += '<td class="text-center"><span class="badge-hadir ' + badgeCls + '">' + val + '</span></td>';
                            });

                            tbody += '</tr>';
                        });
                    }
                    $('#table_pivot_body').html(tbody);

                    // Inisialisasi ulang DataTable dengan Fitur Search, Pagination, dan Export Buttons
                    if (response.data.length > 0) {
                        $('#table_pivot').DataTable({
                            paging: true,
                            pageLength: 25,
                            lengthChange: true,
                            searching: true,
                            ordering: true,
                            info: true,
                            autoWidth: false,
                            scrollX: true,
                            dom: '<"d-flex flex-wrap justify-content-between align-items-center mb-3"<"d-flex align-items-center gap-2"B><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
                            buttons: [
                                {
                                    extend: 'excelHtml5',
                                    text: '<i class="fas fa-file-excel me-1 text-success"></i> Excel',
                                    className: 'btn btn-excel',
                                    title: 'Rekap Kehadiran Pengajar (Pivot)',
                                    messageTop: function() {
                                        return 'Lembaga: ' + $('#filter_lembaga option:selected').text() + ' | Periode: ' + $('#start_month').val() + ' ' + $('#start_year').val() + ' s.d. ' + $('#end_month').val() + ' ' + $('#end_year').val();
                                    }
                                },
                                {
                                    extend: 'print',
                                    text: '<i class="fas fa-print me-1 text-primary"></i> Cetak / PDF',
                                    className: 'btn btn-print',
                                    title: '',
                                    customize: function(win) {
                                        $(win.document.body)
                                            .css('font-size', '10pt')
                                            .css('color', '#000')
                                            .prepend(
                                                '<div style="display:flex; align-items:center; margin-bottom:15px; border-bottom:3px double black; padding-bottom:10px;">' +
                                                '<img src="{{ asset("images/logo-pesantren.png") }}" onerror="this.src=\'{{ asset("assets/p2s2.png") }}\'" style="width:70px; margin-right:20px;" />' +
                                                '<div style="text-align: center; flex-grow: 1;">' +
                                                '<h3 style="margin:0; font-size:15pt; font-family:Arial, sans-serif; font-weight:bold;">PONDOK PESANTREN SALAFIYAH SYAFI\'IYAH SUKOREJO</h3>' +
                                                '<p style="margin:2px 0 0 0; font-size:11pt; font-family:Arial, sans-serif;">SUMBEREJO BANYUPUTIH SITUBONDO JAWA TIMUR</p>' +
                                                '<p style="margin:2px 0 0 0; font-size:9pt; font-family:Arial, sans-serif;">Po Box 2 telp 0388-452666 Fax. 452707 - Situbondo, 68374</p>' +
                                                '</div>' +
                                                '</div>' +
                                                '<h4 style="text-align:center; margin-top:10px; margin-bottom: 5px; font-weight: bold; text-decoration: underline;">LAPORAN REKAP KEHADIRAN PENGAJAR (PIVOT)</h4>' +
                                                '<p style="text-align:center; margin-bottom:15px; font-size: 10pt;"><b>Lembaga:</b> ' + $('#filter_lembaga option:selected').text() + ' &nbsp;|&nbsp; <b>Periode:</b> ' + $('#start_month').val() + ' ' + $('#start_year').val() + ' s.d. ' + $('#end_month').val() + ' ' + $('#end_year').val() + '</p>'
                                            );

                                        $(win.document.body).find('table')
                                            .addClass('compact')
                                            .css('font-size', '9pt')
                                            .css('border-collapse', 'collapse')
                                            .css('width', '100%');

                                        $(win.document.body).find('table th, table td')
                                            .css('border', '1px solid #000')
                                            .css('padding', '6px 8px');

                                        // Bersihkan badge bulat pada versi cetak agar angka bersih
                                        $(win.document.body).find('table tbody td span.badge-hadir').each(function() {
                                            var val = $(this).text();
                                            $(this).parent().text(val);
                                        });
                                    }
                                }
                            ],
                            language: {
                                search: "Search:",
                                lengthMenu: "Tampilkan _MENU_ data",
                                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ pengajar",
                                infoEmpty: "Tidak ada data",
                                zeroRecords: "Data pengajar tidak ditemukan",
                                paginate: {
                                    first: "Awal",
                                    last: "Akhir",
                                    next: '<i class="fas fa-chevron-right"></i>',
                                    previous: '<i class="fas fa-chevron-left"></i>'
                                }
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    $('#btn_filter').html(oldBtnHtml).prop('disabled', false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan sistem saat mengambil data',
                        confirmButtonColor: '#00a15d'
                    });
                }
            });
        });
    });
    </script>
@endpush
