@extends('layouts.app')

@section('title', 'Laporan Barokah Per Umana - E-Barokah')

@section('page_title')
<span class="text-danger font-weight-bold">Laporan</span> / Per Umana
@endsection

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        .summary-card {
            border: none;
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
            border: 1px solid #edf2f7;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.06);
        }
        .summary-icon-box {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .filter-label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
            display: block;
        }
        .filter-control {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 8px 14px;
            font-size: 13px;
            color: #334155;
            transition: all 0.2s;
            height: 40px;
        }
        .filter-control:focus {
            border-color: #00a15d;
            box-shadow: 0 0 0 3px rgba(0, 161, 93, 0.15);
        }
        .btn-filter-custom {
            background-color: #00a15d;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 8px 18px;
            font-weight: 600;
            font-size: 13px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .btn-filter-custom:hover {
            background-color: #00874e;
            color: #ffffff;
            transform: translateY(-1px);
        }
        .btn-reset-custom {
            background-color: #f1f5f9;
            color: #64748b;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 8px 18px;
            font-weight: 600;
            font-size: 13px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .btn-reset-custom:hover {
            background-color: #e2e8f0;
            color: #334155;
        }
        #tabel_perumana th {
            background-color: #f8fafc !important;
            color: #1e293b !important;
            font-weight: 700 !important;
            font-size: 13px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            vertical-align: middle;
            padding: 12px 14px;
        }
        #tabel_perumana td {
            font-size: 13px !important;
            color: #334155 !important;
            vertical-align: middle;
            padding: 12px 14px;
        }
        .bg-success-light {
            background-color: #ecfdf5 !important;
            color: #059669 !important;
            border: 1px solid #a7f3d0;
            font-weight: 600;
        }
        .bg-info-light {
            background-color: #eff6ff !important;
            color: #2563eb !important;
            border: 1px solid #bfdbfe;
            font-weight: 600;
        }
        .bg-warning-light {
            background-color: #fffbeb !important;
            color: #d97706 !important;
            border: 1px solid #fde68a;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
<!-- Header Card & Tombol Aksi -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-header border-0 pb-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h4 class="card-title font-weight-bold mb-1">Daftar Barokah Per Umana'</h4>
                    <p class="text-muted fs-13 mb-0">
                        <i class="fas fa-info-circle text-muted me-1"></i> Rekapitulasi nominal barokah per Umana' berdasarkan periode dan unit penempatan
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm font-weight-bold" onclick="exportExcel()">
                        <i class="fas fa-file-excel me-1"></i> Export XLS
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm font-weight-bold" onclick="refreshTable()">
                        <i class="fas fa-sync-alt me-1"></i> Refresh
                    </button>
                </div>
            </div>

            <div class="card-body pt-3 pb-3">
                <!-- Baris Filter Dropdown -->
                <div class="row align-items-end g-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="filter-label"><i class="fas fa-calendar-alt text-muted me-1"></i> Tahun</label>
                        <select class="form-select filter-control" id="filter_tahun">
                            @foreach($daftarTahun as $th)
                                <option value="{{ $th }}" {{ $selectedTahun == $th ? 'selected' : '' }}>
                                    {{ $th }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="filter-label"><i class="fas fa-calendar-day text-muted me-1"></i> Bulan</label>
                        <select class="form-select filter-control" id="filter_bulan">
                            @foreach($daftarBulan as $bln)
                                <option value="{{ $bln }}" {{ $selectedBulan == $bln ? 'selected' : '' }}>
                                    {{ $bln }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-8">
                        <label class="filter-label"><i class="fas fa-university text-muted me-1"></i> Lembaga / Unit</label>
                        <select class="form-select filter-control" id="filter_lembaga">
                            <option value="">-- Semua Lembaga / Unit --</option>
                            @foreach($daftarLembaga as $lem)
                                <option value="{{ $lem->id_lembaga }}" {{ $selectedLembaga == $lem->id_lembaga ? 'selected' : '' }}>
                                    {{ $lem->nama_lembaga }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-4 d-flex gap-2">
                        <button type="button" class="btn btn-filter-custom flex-grow-1 shadow-sm" onclick="applyFilter()">
                            <i class="fas fa-filter me-1"></i> Terapkan
                        </button>
                        <button type="button" class="btn btn-reset-custom shadow-sm px-3" onclick="resetFilter()" title="Reset Filter">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Executive Summary Cards -->
<div class="row g-3 mb-4">
    <!-- Card 1: Total Penerima -->
    <div class="col-xl-3 col-md-6">
        <div class="card summary-card p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="summary-icon-box bg-primary-subtle text-primary me-3" style="background-color: #e0f2fe; color: #0284c7;">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <span class="text-muted fs-12 fw-semibold text-uppercase">Total Penerima</span>
                    <h4 class="font-weight-bold mb-0 text-dark" id="summary_total_umana">
                        <span class="spinner-border spinner-border-sm text-secondary" role="status"></span>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Grand Total -->
    <div class="col-xl-3 col-md-6">
        <div class="card summary-card p-3 h-100" style="border-left: 4px solid #00a15d !important;">
            <div class="d-flex align-items-center">
                <div class="summary-icon-box text-success me-3" style="background-color: #dcfce7; color: #16a34a;">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div>
                    <span class="text-muted fs-12 fw-semibold text-uppercase">Grand Total Barokah</span>
                    <h4 class="font-weight-bold mb-0 text-success" id="summary_grand_total">
                        <span class="spinner-border spinner-border-sm text-success" role="status"></span>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Struktural -->
    <div class="col-xl-2 col-md-4">
        <div class="card summary-card p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="summary-icon-box text-success me-2" style="background-color: #ecfdf5; color: #059669; width: 38px; height: 38px; font-size: 16px;">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <span class="text-muted fs-11 fw-semibold text-uppercase">Struktural</span>
                    <h6 class="font-weight-bold mb-0 text-dark fs-14" id="summary_struktural">-</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Pengajar -->
    <div class="col-xl-2 col-md-4">
        <div class="card summary-card p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="summary-icon-box text-info me-2" style="background-color: #eff6ff; color: #2563eb; width: 38px; height: 38px; font-size: 16px;">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div>
                    <span class="text-muted fs-11 fw-semibold text-uppercase">Pengajar</span>
                    <h6 class="font-weight-bold mb-0 text-dark fs-14" id="summary_pengajar">-</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 5: Satpam -->
    <div class="col-xl-2 col-md-4">
        <div class="card summary-card p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="summary-icon-box text-warning me-2" style="background-color: #fffbeb; color: #d97706; width: 38px; height: 38px; font-size: 16px;">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div>
                    <span class="text-muted fs-11 fw-semibold text-uppercase">Satpam</span>
                    <h6 class="font-weight-bold mb-0 text-dark fs-14" id="summary_satpam">-</h6>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabel Barokah Per Umana -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabel_perumana" class="table table-bordered table-hover align-middle mb-0 w-100">
                        <thead>
                            <tr>
                                <th style="width: 45px;" class="text-center">No</th>
                                <th>Nama Lengkap</th>
                                <th>Lembaga / Unit Tugas</th>
                                <th style="width: 130px;" class="text-center">Kategori</th>
                                <th style="width: 120px;" class="text-center">Periode</th>
                                <th style="width: 150px;" class="text-end">Total Diterima</th>
                                <th style="width: 90px;" class="text-center">Aksi</th>
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
@endsection

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
    var dataTable;

    $(document).ready(function() {
        if ($.fn.select2) {
            $('#filter_lembaga').select2({
                placeholder: "-- Semua Lembaga / Unit --",
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
                },
                dataSrc: function(json) {
                    // Update Executive Summary Cards
                    if (json && json.summary) {
                        $('#summary_total_umana').text((json.summary.total_umana || 0).toLocaleString('id-ID') + ' Orang');
                        $('#summary_grand_total').text(json.summary.grand_total_rp || 'Rp 0');
                        $('#summary_struktural').text(json.summary.struktural_rp || 'Rp 0');
                        $('#summary_pengajar').text(json.summary.pengajar_rp || 'Rp 0');
                        $('#summary_satpam').text(json.summary.satpam_rp || 'Rp 0');
                    }
                    return json.data || [];
                }
            },
            columns: [
                { data: 'no', className: 'text-center font-weight-bold' },
                { data: 'nama_lengkap' },
                { data: 'lembaga' },
                { data: 'kategori', className: 'text-center' },
                { data: 'periode', className: 'text-center' },
                { data: 'grand_total', className: 'text-end' },
                { data: 'aksi', className: 'text-center', orderable: false }
            ],
            order: [[1, 'asc']],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ Umana'",
                infoEmpty: "Tidak ada data Umana' pada periode ini",
                zeroRecords: "Data tidak ditemukan",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: '<i class="fas fa-chevron-right"></i>',
                    previous: '<i class="fas fa-chevron-left"></i>'
                }
            }
        });
    });

    function applyFilter() {
        if (dataTable) {
            $('#summary_total_umana').html('<span class="spinner-border spinner-border-sm text-secondary"></span>');
            $('#summary_grand_total').html('<span class="spinner-border spinner-border-sm text-success"></span>');
            dataTable.ajax.reload();
        }
    }

    function resetFilter() {
        $('#filter_tahun').val("{{ $selectedTahun }}");
        $('#filter_bulan').val("{{ $selectedBulan }}");
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

        var params = new URLSearchParams();
        if (bulan) params.set('bulan', bulan);
        if (tahun) params.set('tahun', tahun);
        if (idLembaga) params.set('id_lembaga', idLembaga);

        window.location.href = "{{ url('/laporan/per-umana/export-excel') }}?" + params.toString();
    }
    </script>
@endpush
