@extends('layouts.app')

@section('title', 'Laporan Realisasi Barokah Pengajar Per Bidang - E-Barokah')

@section('page_title')
<span class="text-danger font-weight-bold">Laporan</span> / Realisasi Barokah Pengajar
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
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 7px 12px !important;
            font-size: 13px !important;
            outline: none !important;
        }

        /* Table Styling identik dengan standar V2 */
        table.dataTable.display tbody td, 
        table.dataTable.display tbody th, 
        table.dataTable.display thead th {
            border-left: none !important;
            border-right: none !important;
            padding: 1rem 0.75rem !important;
            vertical-align: middle;
            font-size: 12.5px;
        }
        table.dataTable.display thead th {
            border-top: none !important;
            border-bottom: 1px solid #f0f0f0 !important;
            text-transform: capitalize;
            font-weight: 700;
            color: #333;
            white-space: nowrap;
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
        table.dataTable tfoot th {
            border-top: 2px solid #e2e8f0 !important;
            border-bottom: 2px solid #e2e8f0 !important;
            font-weight: 700;
            padding: 1rem 0.75rem !important;
            font-size: 12.5px;
            white-space: nowrap;
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
            <!-- Header Card identik seperti menu laporan lainnya -->
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title font-weight-bold mb-1">Laporan Realisasi Barokah Pengajar Per Bidang</h4>
                    <p class="text-muted fs-13 mb-0">
                        <i class="fas fa-info-circle text-muted me-1"></i> Rekapitulasi realisasi barokah tenaga pengajar (guru & dosen) per bidang
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm" onclick="exportExcel()">
                        <i class="fas fa-file-excel me-1"></i> Export XLS
                    </button>
                    <button type="button" class="btn btn-refresh-custom shadow-sm" onclick="refreshTable()">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                </div>
            </div>

            <div class="card-body">
                <!-- Section Filter -->
                <div class="row align-items-end g-3 mb-4">
                    <div class="col-lg-4 col-md-6">
                        <label class="filter-label">Filter Bidang</label>
                        <select class="form-select filter-control" id="filter_bidang" style="width: 100%;">
                            <option value="">Semua Bidang</option>
                            @foreach($bidangList as $b)
                                <option value="{{ $b }}" {{ $selectedBidang == $b ? 'selected' : '' }}>{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="filter-label">Filter Periode</label>
                        <input type="text" class="form-control filter-control" id="filter_periode" value="{{ $periodeStr }}" placeholder="Pilih Bulan & Tahun" autocomplete="off" readonly style="background-color: #fff; cursor: pointer;">
                        <input type="hidden" id="filter_bulan" value="{{ $selectedBulan }}">
                        <input type="hidden" id="filter_tahun" value="{{ $selectedTahun }}">
                    </div>

                    <div class="col-lg-4 col-md-12 d-flex gap-2">
                        <button type="button" class="btn btn-filter-custom flex-grow-1 shadow-sm" onclick="applyFilter()">
                            <i class="fas fa-filter me-1"></i> Filter
                        </button>
                        <button type="button" class="btn btn-reset-custom flex-grow-1 shadow-sm" onclick="resetFilter()">
                            <i class="fas fa-redo me-1"></i> Reset
                        </button>
                    </div>
                </div>

                <!-- DataTable Realisasi Barokah Pengajar Per Bidang -->
                <div class="table-responsive">
                    <table id="tabel_realisasi_pengajar" class="display" style="min-width: 1100px; width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 40px;" class="text-center">No</th>
                                <th class="text-center" style="width: 110px;">Periode</th>
                                <th>Nama Bidang</th>
                                <th class="text-end">Mengajar</th>
                                <th class="text-end">DTY</th>
                                <th class="text-end">Jafung</th>
                                <th class="text-end">Transport</th>
                                <th class="text-end">Hadir 15</th>
                                <th class="text-end">Hadir 10</th>
                                <th class="text-end">Piket</th>
                                <th class="text-end">Tunkel</th>
                                <th class="text-end">Tunj Anak</th>
                                <th class="text-end">Kehormatan</th>
                                <th class="text-end">Walkes</th>
                                <th class="text-end">Tambahan</th>
                                <th class="text-end">Jumlah</th>
                                <th class="text-end">Potongan</th>
                                <th class="text-end">Diterima</th>
                                <th style="width: 80px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dataBidang as $idx => $row)
                                <tr>
                                    <td class="text-center text-muted font-weight-bold">{{ $idx + 1 }}</td>
                                    <td class="text-center">{{ $row['periode'] }}</td>
                                    <td>
                                        <span class="text-dark font-weight-bold">{{ $row['nama_bidang'] }}</span>
                                    </td>
                                    <td class="text-end">{{ number_format($row['mengajar'], 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($row['dty'], 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($row['jafung'], 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($row['transport'], 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($row['hadir_15'], 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($row['hadir_10'], 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($row['piket'], 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($row['tunkel'], 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($row['tunj_anak'], 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($row['kehormatan'], 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($row['walkes'], 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($row['tambahan'], 0, ',', '.') }}</td>
                                    <td class="text-end font-weight-bold">{{ number_format($row['jumlah'], 0, ',', '.') }}</td>
                                    <td class="text-end text-danger">{{ number_format($row['potongan'], 0, ',', '.') }}</td>
                                    <td class="text-end font-weight-bold text-success" style="font-size: 13.5px;">{{ number_format($row['diterima'], 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-outline-primary btn-xs rounded-pill px-3 shadow-sm btn-lihat-rincian" data-rincian='@json($row)'>
                                            Rincian
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="19" class="text-center text-muted py-4">
                                        Tidak ada data realisasi barokah pengajar untuk periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-center">Total Keseluruhan</th>
                                <th class="text-end">{{ number_format($totals['total_mengajar'], 0, ',', '.') }}</th>
                                <th class="text-end">{{ number_format($totals['total_dty'], 0, ',', '.') }}</th>
                                <th class="text-end">{{ number_format($totals['total_jafung'], 0, ',', '.') }}</th>
                                <th class="text-end">{{ number_format($totals['total_transport'], 0, ',', '.') }}</th>
                                <th class="text-end">{{ number_format($totals['total_hadir_15'], 0, ',', '.') }}</th>
                                <th class="text-end">{{ number_format($totals['total_hadir_10'], 0, ',', '.') }}</th>
                                <th class="text-end">{{ number_format($totals['total_piket'], 0, ',', '.') }}</th>
                                <th class="text-end">{{ number_format($totals['total_tunkel'], 0, ',', '.') }}</th>
                                <th class="text-end">{{ number_format($totals['total_tunj_anak'], 0, ',', '.') }}</th>
                                <th class="text-end">{{ number_format($totals['total_kehormatan'], 0, ',', '.') }}</th>
                                <th class="text-end">{{ number_format($totals['total_walkes'], 0, ',', '.') }}</th>
                                <th class="text-end">{{ number_format($totals['total_tambahan'], 0, ',', '.') }}</th>
                                <th class="text-end font-weight-bold">{{ number_format($totals['total_jumlah'], 0, ',', '.') }}</th>
                                <th class="text-end text-danger">{{ number_format($totals['total_potongan'], 0, ',', '.') }}</th>
                                <th class="text-end text-success font-weight-bold">{{ number_format($totals['total_diterima'], 0, ',', '.') }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rincian Lembaga Pengajar -->
<div class="modal fade" id="modalRincianBidang" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width: 95%;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <div class="modal-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="modal-title font-weight-bold text-dark mb-0" id="modalTitle">
                        Rincian Barokah Pengajar Per Lembaga
                    </h5>
                    <small class="text-muted" id="modalSubtitle">-</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="display table-bordered" id="tabel_modal_lembaga" style="width: 100%; font-size: 12px;">
                        <thead>
                            <tr class="bg-light">
                                <th style="width: 40px;" class="text-center">No</th>
                                <th>Nama Lembaga</th>
                                <th class="text-end">Mengajar</th>
                                <th class="text-end">DTY</th>
                                <th class="text-end">Jafung</th>
                                <th class="text-end">Transport</th>
                                <th class="text-end">Hadir 15</th>
                                <th class="text-end">Hadir 10</th>
                                <th class="text-end">Piket</th>
                                <th class="text-end">Tunkel</th>
                                <th class="text-end">Tunj Anak</th>
                                <th class="text-end">Kehormatan</th>
                                <th class="text-end">Walkes</th>
                                <th class="text-end">Tambahan</th>
                                <th class="text-end">Jumlah</th>
                                <th class="text-end">Potongan</th>
                                <th class="text-end">Diterima</th>
                            </tr>
                        </thead>
                        <tbody id="modalTableBody">
                            <!-- Dimuat dinamis via JavaScript -->
                        </tbody>
                        <tfoot id="modalTableFoot" class="bg-light font-weight-bold">
                            <!-- Total rincian modal -->
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top py-2 px-4">
                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
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
    var dataTable;

    $(document).ready(function () {
        // Registrasi Locale Indonesia untuk Bootstrap Datepicker
        if ($.fn.datepicker) {
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
                    var monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                    $('#filter_bulan').val(monthNames[e.date.getMonth()]);
                    $('#filter_tahun').val(e.date.getFullYear());
                } else {
                    $('#filter_bulan').val('');
                    $('#filter_tahun').val('');
                }
            });
        }

        if ($.fn.select2) {
            $('#filter_bidang').select2({
                placeholder: 'Semua Bidang',
                allowClear: true,
                width: '100%'
            });
        }

        // Inisialisasi DataTable Standar Bawaan
        dataTable = $('#tabel_realisasi_pengajar').DataTable({
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                zeroRecords: "No matching records found",
                paginate: {
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>'
                }
            },
            ordering: true,
            order: [[0, 'asc']],
            pageLength: 10,
            columnDefs: [
                { orderable: false, targets: [18] }
            ]
        });

        // Event listener klik tombol Rincian
        $(document).on('click', '.btn-lihat-rincian', function(e) {
            e.preventDefault();
            var data = $(this).data('rincian');
            lihatRincian(data);
        });
    });

    function applyFilter() {
        var bidang = $('#filter_bidang').val();
        var bulan = $('#filter_bulan').val();
        var tahun = $('#filter_tahun').val();

        var params = new URLSearchParams();
        if (bulan) params.set('bulan', bulan);
        if (tahun) params.set('tahun', tahun);
        if (bidang) params.set('bidang', bidang);

        window.location.href = "{{ url('/laporan/realisasi-pengajar') }}?" + params.toString();
    }

    function resetFilter() {
        window.location.href = "{{ url('/laporan/realisasi-pengajar') }}";
    }

    function refreshTable() {
        window.location.reload();
    }

    // Modal Rincian Lembaga Pengajar
    function lihatRincian(data) {
        if (!data) return;

        $('#modalTitle').text('Rincian Realisasi Barokah Pengajar - ' + data.nama_bidang);
        $('#modalSubtitle').text('Periode: ' + data.periode + ' | Total Diterima: Rp ' + parseInt(data.diterima || 0).toLocaleString('id-ID'));

        var html = '';
        var totMengajar = 0, totDty = 0, totJafung = 0, totTransport = 0, totHadir15 = 0, totHadir10 = 0, totPiket = 0;
        var totTunkel = 0, totTunjAnak = 0, totKehormatan = 0, totWalkes = 0, totTambahan = 0, totJumlah = 0, totPotongan = 0, totDiterima = 0;

        if (data.lembaga && data.lembaga.length > 0) {
            data.lembaga.forEach(function(lem, i) {
                totMengajar += parseFloat(lem.mengajar || 0);
                totDty += parseFloat(lem.dty || 0);
                totJafung += parseFloat(lem.jafung || 0);
                totTransport += parseFloat(lem.transport || 0);
                totHadir15 += parseFloat(lem.hadir_15 || 0);
                totHadir10 += parseFloat(lem.hadir_10 || 0);
                totPiket += parseFloat(lem.piket || 0);
                totTunkel += parseFloat(lem.tunkel || 0);
                totTunjAnak += parseFloat(lem.tunj_anak || 0);
                totKehormatan += parseFloat(lem.kehormatan || 0);
                totWalkes += parseFloat(lem.walkes || 0);
                totTambahan += parseFloat(lem.tambahan || 0);
                totJumlah += parseFloat(lem.jumlah || 0);
                totPotongan += parseFloat(lem.potongan || 0);
                totDiterima += parseFloat(lem.diterima || 0);

                html += '<tr>' +
                    '<td class="text-center">' + (i + 1) + '</td>' +
                    '<td><strong>' + lem.nama_lembaga + '</strong></td>' +
                    '<td class="text-end">' + parseInt(lem.mengajar || 0).toLocaleString('id-ID') + '</td>' +
                    '<td class="text-end">' + parseInt(lem.dty || 0).toLocaleString('id-ID') + '</td>' +
                    '<td class="text-end">' + parseInt(lem.jafung || 0).toLocaleString('id-ID') + '</td>' +
                    '<td class="text-end">' + parseInt(lem.transport || 0).toLocaleString('id-ID') + '</td>' +
                    '<td class="text-end">' + parseInt(lem.hadir_15 || 0).toLocaleString('id-ID') + '</td>' +
                    '<td class="text-end">' + parseInt(lem.hadir_10 || 0).toLocaleString('id-ID') + '</td>' +
                    '<td class="text-end">' + parseInt(lem.piket || 0).toLocaleString('id-ID') + '</td>' +
                    '<td class="text-end">' + parseInt(lem.tunkel || 0).toLocaleString('id-ID') + '</td>' +
                    '<td class="text-end">' + parseInt(lem.tunj_anak || 0).toLocaleString('id-ID') + '</td>' +
                    '<td class="text-end">' + parseInt(lem.kehormatan || 0).toLocaleString('id-ID') + '</td>' +
                    '<td class="text-end">' + parseInt(lem.walkes || 0).toLocaleString('id-ID') + '</td>' +
                    '<td class="text-end">' + parseInt(lem.tambahan || 0).toLocaleString('id-ID') + '</td>' +
                    '<td class="text-end font-weight-bold">' + parseInt(lem.jumlah || 0).toLocaleString('id-ID') + '</td>' +
                    '<td class="text-end text-danger">' + parseInt(lem.potongan || 0).toLocaleString('id-ID') + '</td>' +
                    '<td class="text-end text-success font-weight-bold">' + parseInt(lem.diterima || 0).toLocaleString('id-ID') + '</td>' +
                '</tr>';
            });
        } else {
            html = '<tr><td colspan="17" class="text-center text-muted py-3">Tidak ada data rincian lembaga</td></tr>';
        }

        $('#modalTableBody').html(html);

        var footHtml = '<tr>' +
            '<th colspan="2" class="text-center">Total Bidang</th>' +
            '<th class="text-end">' + Math.round(totMengajar).toLocaleString('id-ID') + '</th>' +
            '<th class="text-end">' + Math.round(totDty).toLocaleString('id-ID') + '</th>' +
            '<th class="text-end">' + Math.round(totJafung).toLocaleString('id-ID') + '</th>' +
            '<th class="text-end">' + Math.round(totTransport).toLocaleString('id-ID') + '</th>' +
            '<th class="text-end">' + Math.round(totHadir15).toLocaleString('id-ID') + '</th>' +
            '<th class="text-end">' + Math.round(totHadir10).toLocaleString('id-ID') + '</th>' +
            '<th class="text-end">' + Math.round(totPiket).toLocaleString('id-ID') + '</th>' +
            '<th class="text-end">' + Math.round(totTunkel).toLocaleString('id-ID') + '</th>' +
            '<th class="text-end">' + Math.round(totTunjAnak).toLocaleString('id-ID') + '</th>' +
            '<th class="text-end">' + Math.round(totKehormatan).toLocaleString('id-ID') + '</th>' +
            '<th class="text-end">' + Math.round(totWalkes).toLocaleString('id-ID') + '</th>' +
            '<th class="text-end">' + Math.round(totTambahan).toLocaleString('id-ID') + '</th>' +
            '<th class="text-end font-weight-bold">' + Math.round(totJumlah).toLocaleString('id-ID') + '</th>' +
            '<th class="text-end text-danger">' + Math.round(totPotongan).toLocaleString('id-ID') + '</th>' +
            '<th class="text-end text-success font-weight-bold">' + Math.round(totDiterima).toLocaleString('id-ID') + '</th>' +
        '</tr>';

        $('#modalTableFoot').html(footHtml);

        var modal = new bootstrap.Modal(document.getElementById('modalRincianBidang'));
        modal.show();
    }

    function exportExcel() {
        var bidang = $('#filter_bidang').val();
        var bulan = $('#filter_bulan').val();
        var tahun = $('#filter_tahun').val();

        var params = new URLSearchParams();
        if (bulan) params.set('bulan', bulan);
        if (tahun) params.set('tahun', tahun);
        if (bidang) params.set('bidang', bidang);

        window.location.href = "{{ url('/laporan/realisasi-pengajar/export-excel') }}?" + params.toString();
    }
    </script>
@endpush

