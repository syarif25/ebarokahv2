@extends('layouts.app')

@section('title', 'Audit Tunjangan Ganda - E-Barokah')

@section('page_title')
<span class="text-danger font-weight-bold">Validasi</span> / Audit Tunjangan Ganda
@endsection

@push('styles')
    <!-- DataTables CSS bawaan template -->
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <style>
        /* Card Filter Interaktif bergaya Template Bawaan */
        .card-stat-filter {
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            border: 2px solid transparent;
        }
        .card-stat-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }
        .card-stat-filter.active {
            border: 2px solid #00a15d !important;
            box-shadow: 0 4px 15px rgba(0, 161, 93, 0.15);
        }

        /* Clean Table Style identik dengan menu Validasi & Kehadiran */
        #table-audit.dataTable tbody tr {
            border-bottom: 1px solid #f1f1f1;
        }
        #table-audit.dataTable, #table-audit.dataTable th, #table-audit.dataTable td {
            border-top: none;
            border-left: none;
            border-right: none;
            vertical-align: middle;
        }
        #table-audit.dataTable thead th {
            border-bottom: 2px solid #f1f1f1;
            background-color: #fff;
            color: #333;
            font-weight: 600;
        }

        /* Filter Kolom Styling bawaan */
        .filters th {
            padding: 6px 8px !important;
            border-bottom: 2px solid #eef2f6 !important;
            background-color: #fff !important;
        }
        .filters input, .filters select {
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
        .filters input:focus, .filters select:focus {
            border-color: #00a15d;
            background-color: #fff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 161, 93, 0.15);
        }

        .action-pill {
            border-radius: 20px !important;
            padding: 5px 16px !important;
            font-size: 12px !important;
            font-weight: 500 !important;
            white-space: nowrap !important;
            display: inline-flex !important;
            align-items: center;
        }
    </style>
@endpush

@section('content')
<!-- 4 KARTU STATISTIK NATIVE TEMPLATE (Dapat diklik untuk filter cepat) -->
<div class="row">
    <!-- Total Temuan -->
    <div class="col-xl-3 col-sm-6">
        <div class="card card-stat-filter active" data-kategori="all" title="Klik untuk memfilter semua temuan">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-2 text-nowrap">Total Temuan</h4>
                    <h2 class="fs-32 font-w700 mb-0 text-danger" id="stat-total-ganda">{{ $stats['total_ganda'] }}</h2>
                    <small class="text-muted">Semua Temuan Ganda</small>
                </div>
                <i class="fas fa-exclamation-triangle fa-3x text-danger" style="opacity: 0.35;"></i>
            </div>
        </div>
    </div>

    <!-- Tunkel Ganda -->
    <div class="col-xl-3 col-sm-6">
        <div class="card card-stat-filter" data-kategori="tunkel" title="Klik untuk memfilter duplikasi Tunkel">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-2 text-nowrap">Tunkel Ganda</h4>
                    <h2 class="fs-32 font-w700 mb-0 text-primary" id="stat-total-tunkel">{{ $stats['total_tunkel'] }}</h2>
                    <small class="text-muted">Tunjangan Keluarga</small>
                </div>
                <i class="fas fa-users fa-3x text-primary" style="opacity: 0.35;"></i>
            </div>
        </div>
    </div>

    <!-- Tunj. Anak Ganda -->
    <div class="col-xl-3 col-sm-6">
        <div class="card card-stat-filter" data-kategori="anak" title="Klik untuk memfilter duplikasi Tunjangan Anak">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-2 text-nowrap">Tunj. Anak Ganda</h4>
                    <h2 class="fs-32 font-w700 mb-0 text-info" id="stat-total-anak">{{ $stats['total_anak'] }}</h2>
                    <small class="text-muted">Tunjangan Anak</small>
                </div>
                <i class="fas fa-child fa-3x text-info" style="opacity: 0.35;"></i>
            </div>
        </div>
    </div>

    <!-- Kehormatan Ganda -->
    <div class="col-xl-3 col-sm-6">
        <div class="card card-stat-filter" data-kategori="kehormatan" title="Klik untuk memfilter duplikasi Kehormatan">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-2 text-nowrap">Kehormatan Ganda</h4>
                    <h2 class="fs-32 font-w700 mb-0 text-warning" id="stat-total-kehormatan">{{ $stats['total_kehormatan'] }}</h2>
                    <small class="text-muted">Tunjangan Kehormatan</small>
                </div>
                <i class="fas fa-award fa-3x text-warning" style="opacity: 0.35;"></i>
            </div>
        </div>
    </div>
</div>

<!-- DAFTAR TEMUAN AUDIT -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center">
                    <h4 class="card-title text-dark font-weight-bold mb-0">Daftar Temuan Tunjangan Ganda</h4>
                </div>
                <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
                    <!-- Export Excel Button -->
                    <a href="{{ url('/validasi/audit-tunjangan/export-excel') }}?scope=semua_aktif" id="btn-export-excel" class="btn btn-sm btn-success text-white action-pill" style="background-color: #00a15d; border-color: #00a15d;">
                        <i class="fas fa-file-excel me-1"></i> Export Excel
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-audit" class="display" style="min-width: 845px; width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 45px;" class="text-center font-weight-bold pb-3 pt-2">No</th>
                                <th style="width: 240px;" class="font-weight-bold pb-3 pt-2">Nama Umana & NIK</th>
                                <th style="width: 100px;" class="text-center font-weight-bold pb-3 pt-2">Sertifikasi</th>
                                <th style="width: 180px;" class="font-weight-bold pb-3 pt-2">Jenis Duplikasi</th>
                                <th class="font-weight-bold pb-3 pt-2">Rincian Unit / Penugasan</th>
                                <th style="width: 90px;" class="text-center font-weight-bold pb-3 pt-2">Aksi</th>
                            </tr>
                            <tr class="filters">
                                <th></th>
                                <th><input type="text" placeholder="Filter Nama / NIK"></th>
                                <th>
                                    <select>
                                        <option value="">Semua</option>
                                        <option value="Sudah">Sudah</option>
                                        <option value="Belum">Belum</option>
                                    </select>
                                </th>
                                <th><input type="text" placeholder="Filter Duplikasi"></th>
                                <th><input type="text" placeholder="Filter Lembaga"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- AJAX DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL VALIDASI SILANG TUNJANGAN GANDA -->
<div class="modal fade" id="modalAuditDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold">Rincian Penugasan & Hak Tunjangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Info Profil Umana -->
                <div class="card bg-light border-0 mb-3 shadow-none">
                    <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted fs-12 d-block">Nama Lengkap & NIK:</span>
                            <h6 class="font-weight-bold text-dark mb-0" id="detail-nama">-</h6>
                            <span class="text-muted fs-12 font-monospace" id="detail-nik">-</span>
                        </div>
                        <div class="text-end text-right">
                            <span class="text-muted fs-12 d-block">Status Sertifikasi:</span>
                            <span class="badge badge-info rounded-pill" id="detail-sertifikasi">-</span>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info py-2 px-3 fs-13 mb-3 border-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Setiap Umana hanya berhak mendapatkan status <code>Ya</code> untuk <em>Tunkel</em>, <em>Tunjangan Anak</em>, atau <em>Kehormatan</em> pada <strong>satu SK penugasan saja</strong>.
                </div>

                <!-- 1. TABEL STRUKTURAL -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="font-weight-bold text-primary mb-0">
                        <i class="fas fa-sitemap me-1"></i> 1. Penempatan Struktural
                    </h6>
                    <a href="{{ url('/manajemen-umana/penempatan') }}" target="_blank" class="fs-12 text-primary font-weight-bold">
                        Buka Penempatan <i class="fas fa-external-link-alt ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered table-striped" style="font-size: 12px;">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 35px;" class="text-center">No</th>
                                <th>Lembaga</th>
                                <th>Jabatan</th>
                                <th>Masa SK</th>
                                <th class="text-center">Tunkel</th>
                                <th class="text-center">Tunj Anak</th>
                                <th class="text-center">Kehormatan</th>
                            </tr>
                        </thead>
                        <tbody id="detail-tbody-struktural">
                            <tr><td colspan="7" class="text-center text-muted py-3">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- 2. TABEL PENGAJAR -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="font-weight-bold text-success mb-0">
                        <i class="fas fa-graduation-cap me-1"></i> 2. Penugasan Pengajar (Guru / Dosen)
                    </h6>
                    <a href="{{ url('/Penempatan/pengajar') }}" target="_blank" class="fs-12 text-success font-weight-bold">
                        Buka Pengajar <i class="fas fa-external-link-alt ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped" style="font-size: 12px;">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 35px;" class="text-center">No</th>
                                <th>Lembaga</th>
                                <th>Kategori</th>
                                <th>Masa SK</th>
                                <th class="text-center">Tunkel</th>
                                <th class="text-center">Tunj Anak</th>
                                <th class="text-center">Kehormatan</th>
                            </tr>
                        </thead>
                        <tbody id="detail-tbody-pengajar">
                            <tr><td colspan="7" class="text-center text-muted py-3">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary btn-sm action-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- Datatable & SweetAlert JS bawaan template -->
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            let currentScope = 'semua_aktif';
            let currentKategori = 'all';

            // Inisialisasi DataTable
            var table = $('#table-audit').DataTable({
                orderCellsTop: true,
                processing: true,
                ajax: {
                    url: '{{ url("/validasi/audit-tunjangan/data") }}',
                    data: function(d) {
                        d.scope = currentScope;
                        d.kategori_ganda = currentKategori;
                    },
                    dataSrc: function(json) {
                        // Update angka di 4 kartu statistik
                        if(json.stats) {
                            $('#stat-total-ganda').text(json.stats.total_ganda);
                            $('#stat-total-tunkel').text(json.stats.total_tunkel);
                            $('#stat-total-anak').text(json.stats.total_anak);
                            $('#stat-total-kehormatan').text(json.stats.total_kehormatan);
                        }
                        return json.data;
                    }
                },
                columns: [
                    {
                        data: null,
                        searchable: false,
                        orderable: false,
                        className: 'text-center font-weight-bold text-muted',
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            if (type === 'filter' || type === 'sort') {
                                return (row.nama_lengkap || '') + ' ' + (row.nik || '');
                            }
                            return `<div class="py-1">
                                <span class="font-w600 text-dark fs-14 d-block" style="line-height: 1.3;">${row.nama_lengkap}</span>
                                <small class="text-muted font-monospace">${row.nik}</small>
                            </div>`;
                        }
                    },
                    {
                        data: 'status_sertifikasi',
                        className: 'text-center',
                        render: function(data) {
                            var rawStatus = data ? data.toString().trim().toLowerCase() : '';
                            var isSudah = (rawStatus === 'sudah' || rawStatus === 'ya' || rawStatus === 'sudah sertifikasi');
                            var badgeClass = isSudah ? 'badge-success' : 'badge-danger';
                            var label = isSudah ? 'Sudah' : 'Belum';
                            return '<span class="badge ' + badgeClass + ' rounded-pill">' + label + '</span>';
                        }
                    },
                    {
                        data: 'pelanggaran',
                        render: function(data) {
                            let html = '';
                            data.forEach(item => {
                                let badgeClass = 'badge-danger';
                                let icon = 'fas fa-users';
                                if (item.includes('Anak')) {
                                    badgeClass = 'badge-info';
                                    icon = 'fas fa-child';
                                } else if (item.includes('Kehormatan')) {
                                    badgeClass = 'badge-warning';
                                    icon = 'fas fa-award';
                                }
                                html += `<span class="badge ${badgeClass} rounded-pill mb-1 d-inline-block text-nowrap" style="font-size: 11px;"><i class="${icon} me-1"></i>${item}</span><br>`;
                            });
                            return html;
                        }
                    },
                    {
                        data: 'details',
                        render: function(data) {
                            if (!data || data.length === 0) return '-';
                            let html = '<table class="table table-sm table-borderless mb-0 fs-12 w-100"><tbody>';
                            data.forEach((item, idx) => {
                                const isStruk = item.tipe === 'Struktural';
                                const tipeBadge = isStruk 
                                    ? '<span class="badge badge-primary light badge-xs">Struktural</span>'
                                    : '<span class="badge badge-success light badge-xs">Pengajar</span>';

                                const badges = [];
                                if (item.tunkel === 'Ya') badges.push('<span class="badge badge-danger rounded-pill px-2 py-0 me-1" style="font-size: 10px;">Tunkel</span>');
                                if (item.tunj_anak === 'Ya') badges.push('<span class="badge badge-info rounded-pill px-2 py-0 me-1" style="font-size: 10px;">Anak</span>');
                                if (item.kehormatan === 'Ya') badges.push('<span class="badge badge-warning rounded-pill px-2 py-0 me-1" style="font-size: 10px;">Kehormatan</span>');

                                const lembagaNama = (item.lembaga && item.lembaga !== '-') ? item.lembaga : 'Kantor Pusat / Yayasan';
                                const borderStyle = (idx > 0) ? 'border-top: 1px dashed #e2e8f0;' : '';

                                html += `<tr style="${borderStyle}">
                                    <td class="py-1 px-1 text-nowrap align-middle" style="width: 1%;">
                                        ${tipeBadge}
                                    </td>
                                    <td class="py-1 px-2 align-middle">
                                        <span class="font-w600 text-dark">${lembagaNama}</span>
                                        <small class="text-muted d-block" style="font-size: 11px;">Jabatan: ${item.posisi}</small>
                                    </td>
                                    <td class="py-1 px-1 text-nowrap text-end text-right align-middle" style="width: 1%;">
                                        ${badges.join('') || '<span class="badge badge-light border text-muted px-2 py-0" style="font-size: 10px;">Tanpa Tunjangan</span>'}
                                    </td>
                                </tr>`;
                            });
                            html += '</tbody></table>';
                            return html;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `<button type="button" class="btn btn-outline-success btn-sm shadow-sm action-pill btn-buka-modal" data-nik="${row.nik}">
                                        <i class="fas fa-search me-1"></i> Rincian
                                    </button>`;
                        }
                    }
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ temuan",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 temuan",
                    zeroRecords: "Tidak ditemukan Umana dengan tunjangan ganda pada filter ini.",
                    paginate: {
                        next: '<i class="fa fa-angle-double-right"></i>',
                        previous: '<i class="fa fa-angle-double-left"></i>' 
                    }
                },
                initComplete: function () {
                    var api = this.api();
                    api.columns().every(function (i) {
                        var column = this;
                        $('input, select', $('#table-audit thead tr.filters th').eq(i)).on('keyup change clear', function () {
                            if (column.search() !== this.value) {
                                column.search(this.value).draw();
                            }
                        });
                    });
                }
            });

            // Klik Kartu Statistik untuk Filter Cepat Kategori Ganda
            $('.card-stat-filter').on('click', function() {
                $('.card-stat-filter').removeClass('active');
                $(this).addClass('active');
                currentKategori = $(this).data('kategori');

                // Update link export excel
                $('#btn-export-excel').attr('href', '{{ url("/validasi/audit-tunjangan/export-excel") }}?scope=' + currentScope + '&kategori_ganda=' + currentKategori);

                table.ajax.reload();
            });

            // Klik Rincian / Modal Validasi Silang
            $(document).on('click', '.btn-buka-modal', function() {
                const nik = $(this).data('nik');
                if(!nik) return;

                $('#detail-nama').text('Memuat...');
                $('#detail-nik').text(nik);
                $('#detail-sertifikasi').text('-');
                $('#detail-tbody-struktural').html('<tr><td colspan="7" class="text-center text-muted py-3">Memuat data...</td></tr>');
                $('#detail-tbody-pengajar').html('<tr><td colspan="7" class="text-center text-muted py-3">Memuat data...</td></tr>');

                $('#modalAuditDetail').modal('show');

                $.getJSON('{{ url("/validasi/audit-tunjangan/detail") }}/' + nik + '?scope=' + currentScope, function(res) {
                    if(!res || !res.status) {
                        Swal.fire('Error', 'Gagal memuat detail data.', 'error');
                        return;
                    }

                    $('#detail-nama').text(res.umana.nama_lengkap);
                    $('#detail-sertifikasi').text(res.umana.status_sertifikasi);

                    // 1. Render Struktural
                    let htmlStruk = '';
                    if(res.struktural && res.struktural.length > 0) {
                        res.struktural.forEach((item, idx) => {
                            const tunkelBadge = (item.tunj_kel === 'Ya')
                                ? '<span class="badge badge-danger rounded-pill"><i class="fas fa-check me-1"></i>Ya</span>'
                                : '<span class="badge badge-secondary rounded-pill">Tidak</span>';

                            const tunjAnakBadge = (item.tunj_anak === 'Ya')
                                ? '<span class="badge badge-danger rounded-pill"><i class="fas fa-check me-1"></i>Ya</span>'
                                : '<span class="badge badge-secondary rounded-pill">Tidak</span>';

                            const kehormatanBadge = (item.kehormatan === 'Ya')
                                ? '<span class="badge badge-danger rounded-pill"><i class="fas fa-check me-1"></i>Ya</span>'
                                : '<span class="badge badge-secondary rounded-pill">Tidak</span>';

                            const masa = (item.tgl_mulai ? item.tgl_mulai : '-') + ' s/d ' + (item.tgl_selesai ? item.tgl_selesai : 'Sekarang');

                            htmlStruk += `<tr>
                                <td class="text-center font-weight-bold">${idx + 1}</td>
                                <td><strong>${item.nama_lembaga || '-'}</strong></td>
                                <td>${item.nama_jabatan || item.jabatan_lembaga || '-'}</td>
                                <td><small class="text-muted">${masa}</small></td>
                                <td class="text-center">${tunkelBadge}</td>
                                <td class="text-center">${tunjAnakBadge}</td>
                                <td class="text-center">${kehormatanBadge}</td>
                            </tr>`;
                        });
                    } else {
                        htmlStruk = '<tr><td colspan="7" class="text-center text-muted py-3">Tidak ada SK struktural aktif.</td></tr>';
                    }
                    $('#detail-tbody-struktural').html(htmlStruk);

                    // 2. Render Pengajar
                    let htmlPeng = '';
                    if(res.pengajar && res.pengajar.length > 0) {
                        res.pengajar.forEach((item, idx) => {
                            const tunkelBadge = (item.tunj_kel === 'Ya')
                                ? '<span class="badge badge-danger rounded-pill"><i class="fas fa-check me-1"></i>Ya</span>'
                                : '<span class="badge badge-secondary rounded-pill">Tidak</span>';

                            const tunjAnakBadge = (item.tunj_anak === 'Ya')
                                ? '<span class="badge badge-danger rounded-pill"><i class="fas fa-check me-1"></i>Ya</span>'
                                : '<span class="badge badge-secondary rounded-pill">Tidak</span>';

                            const kehormatanBadge = (item.kehormatan === 'Ya')
                                ? '<span class="badge badge-danger rounded-pill"><i class="fas fa-check me-1"></i>Ya</span>'
                                : '<span class="badge badge-secondary rounded-pill">Tidak</span>';

                            const masa = (item.tgl_mulai ? item.tgl_mulai : '-') + ' s/d ' + (item.tgl_selesai ? item.tgl_selesai : 'Sekarang');

                            htmlPeng += `<tr>
                                <td class="text-center font-weight-bold">${idx + 1}</td>
                                <td><strong>${item.nama_lembaga || '-'}</strong></td>
                                <td><span class="badge badge-primary rounded-pill">${item.jenis_pengajar || 'Pengajar'}</span></td>
                                <td><small class="text-muted">${masa}</small></td>
                                <td class="text-center">${tunkelBadge}</td>
                                <td class="text-center">${tunjAnakBadge}</td>
                                <td class="text-center">${kehormatanBadge}</td>
                            </tr>`;
                        });
                    } else {
                        htmlPeng = '<tr><td colspan="7" class="text-center text-muted py-3">Tidak ada SK pengajar aktif.</td></tr>';
                    }
                    $('#detail-tbody-pengajar').html(htmlPeng);
                });
            });
        });
    </script>
@endpush
