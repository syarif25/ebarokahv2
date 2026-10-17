@extends('layouts.app')

@section('title', 'Audit Tunjangan Ganda - E-Barokah')

@section('page_title')
<span class="text-danger font-weight-bold">Evaluasi & Validasi</span> / Audit Tunjangan Ganda (Struktural & Pengajar)
@endsection

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <style>
        .stat-card {
            border-radius: 14px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }
        
        table.dataTable.display tbody td, 
        table.dataTable.display tbody th, 
        table.dataTable.display thead th {
            border-left: none !important;
            border-right: none !important;
            padding: 1.1rem 0.85rem !important;
            vertical-align: middle;
        }
        table.dataTable.display thead th {
            border-top: none !important;
            border-bottom: 1px solid #f0f0f0 !important;
            text-transform: capitalize;
            font-weight: 700;
            color: #333;
            font-size: 13px;
        }
        table.dataTable.display tbody tr {
            border-bottom: 1px solid #f8f8f8 !important;
        }
        table.dataTable.display tbody tr:hover {
            background-color: #fafbfc !important;
        }
        table.dataTable,
        table.dataTable.no-footer {
            border-bottom: none !important;
        }

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
        }
        .filters input:focus, .filters select:focus {
            border-color: #00a15d;
            background-color: #fff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 161, 93, 0.15);
        }

        .badge-duplikasi {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin: 2px 2px 2px 0;
        }
        .badge-tunkel {
            background-color: #fff1f2;
            color: #e11d48;
            border: 1px solid #fecdd3;
        }
        .badge-anak {
            background-color: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
        }
        .badge-kehormatan {
            background-color: #f5f3ff;
            color: #7c3aed;
            border: 1px solid #ddd6fe;
        }

        .unit-item {
            font-size: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 4px 8px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .unit-item:last-child {
            margin-bottom: 0;
        }

        .btn-cek-detail {
            background-color: #00a15d;
            color: #fff !important;
            border-radius: 20px;
            padding: 5px 14px;
            font-size: 12px;
            font-weight: 500;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-cek-detail:hover {
            background-color: #00844c;
            box-shadow: 0 2px 8px rgba(0, 161, 93, 0.3);
        }

        .filter-btn-group .btn {
            font-size: 12px;
            font-weight: 600;
            padding: 6px 16px;
            border-radius: 8px;
        }
    </style>
@endpush

@section('content')
<div class="row">
    <!-- STATISTIK RINGKASAN -->
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card stat-card">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="stat-icon bg-danger-light text-danger mr-3 me-3">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold d-block text-uppercase">Total Umana Dobel</span>
                    <h3 class="font-weight-bold text-danger mb-0" id="stat-total-ganda">{{ $stats['total_ganda'] }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card stat-card">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="stat-icon text-white mr-3 me-3" style="background-color: #e11d48;">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold d-block text-uppercase">Dobel Tunkel</span>
                    <h3 class="font-weight-bold text-dark mb-0" id="stat-total-tunkel">{{ $stats['total_tunkel'] }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card stat-card">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="stat-icon text-white mr-3 me-3" style="background-color: #2563eb;">
                    <i class="fas fa-child"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold d-block text-uppercase">Dobel Tunj. Anak</span>
                    <h3 class="font-weight-bold text-dark mb-0" id="stat-total-anak">{{ $stats['total_anak'] }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card stat-card">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="stat-icon text-white mr-3 me-3" style="background-color: #7c3aed;">
                    <i class="fas fa-award"></i>
                </div>
                <div>
                    <span class="text-muted small font-weight-bold d-block text-uppercase">Dobel Kehormatan</span>
                    <h3 class="font-weight-bold text-dark mb-0" id="stat-total-kehormatan">{{ $stats['total_kehormatan'] }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h4 class="card-title font-weight-bold text-dark mb-1">
                        <i class="fas fa-shield-alt text-primary mr-1 me-1"></i> Daftar Temuan Tunjangan Ganda
                    </h4>
                    <p class="text-muted small mb-0">
                        Mendeteksi silang data penugasan Umana di <strong>Penempatan Struktural</strong> dan <strong>Penugasan Pengajar (Guru / Dosen)</strong>.
                    </p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <!-- Scope Filter Toggle -->
                    <div class="btn-group filter-btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary active btn-scope" data-scope="berlaku">
                            <i class="fas fa-calendar-check mr-1 me-1"></i> SK Berlaku Saat Ini
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-scope" data-scope="semua_aktif">
                            <i class="fas fa-layer-group mr-1 me-1"></i> Semua SK Status Aktif
                        </button>
                    </div>

                    <!-- Export Excel Button -->
                    <a href="{{ url('/validasi/audit-tunjangan/export-excel') }}?scope=berlaku" id="btn-export-excel" class="btn btn-success btn-sm font-weight-bold text-white shadow-sm" style="border-radius: 8px; background-color: #00a15d; border-color: #00a15d;">
                        <i class="fas fa-file-excel mr-1 me-1"></i> Export Excel (CSV)
                    </a>
                </div>
            </div>

            <div class="card-body pt-3">
                <!-- Filter Kategori Ganda Quick Pills -->
                <div class="d-flex flex-wrap align-items-center mb-3 gap-2">
                    <span class="small font-weight-bold text-muted mr-2 me-2">Filter Jenis Anomali:</span>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill active btn-pill-filter" data-kategori="all">Semua Duplikasi</button>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill btn-pill-filter" data-kategori="tunkel">
                        <i class="fas fa-circle mr-1 me-1" style="font-size: 8px;"></i> Tunkel Ganda
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill btn-pill-filter" data-kategori="anak">
                        <i class="fas fa-circle mr-1 me-1" style="font-size: 8px;"></i> Tunj. Anak Ganda
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-info rounded-pill btn-pill-filter" data-kategori="kehormatan">
                        <i class="fas fa-circle mr-1 me-1" style="font-size: 8px;"></i> Kehormatan Ganda
                    </button>
                </div>

                <div class="table-responsive">
                    <table id="table-audit" class="display" style="min-width: 900px; width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 40px;" class="text-center">No</th>
                                <th style="width: 140px;">NIK</th>
                                <th style="width: 220px;">Nama Umana</th>
                                <th style="width: 100px;" class="text-center">Sertifikasi</th>
                                <th style="width: 180px;">Jenis Duplikasi</th>
                                <th>Rincian Unit / Lembaga Penerima</th>
                                <th style="width: 110px;" class="text-center">Aksi</th>
                            </tr>
                            <tr class="filters">
                                <th></th>
                                <th><input type="text" placeholder="Filter NIK"></th>
                                <th><input type="text" placeholder="Filter Nama"></th>
                                <th>
                                    <select>
                                        <option value="">Semua</option>
                                        <option value="Sudah">Sudah</option>
                                        <option value="Belum">Belum</option>
                                    </select>
                                </th>
                                <th><input type="text" placeholder="Filter Duplikasi"></th>
                                <th><input type="text" placeholder="Filter Lembaga / Posisi"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- AJAX Load Data -->
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
        <div class="modal-content" style="border-radius: 15px; border: none; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header bg-primary text-white py-2 px-3">
                <h5 class="modal-title font-weight-bold text-white mb-0" style="font-size: 1.05rem;">
                    <i class="fa fa-id-card mr-2 me-2"></i> Rincian Penempatan & Tunjangan Umana'
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3">
                <!-- Info Profil Umana' -->
                <div class="card bg-light border-0 mb-3 shadow-none">
                    <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small d-block">Nama Lengkap & NIK:</span>
                            <h6 class="font-weight-bold text-dark mb-0" id="detail-nama">-</h6>
                            <span class="text-muted small" id="detail-nik">-</span>
                        </div>
                        <div class="text-end text-right">
                            <span class="text-muted small d-block">Status Sertifikasi:</span>
                            <span class="badge badge-info" id="detail-sertifikasi">-</span>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning py-2 px-3 small mb-3 border-0" style="background-color: #fff8e1; color: #856404;">
                    <i class="fa fa-exclamation-circle mr-1 me-1"></i>
                    <strong>Petunjuk Evaluasi:</strong> Di bawah adalah seluruh SK aktif Umana ini. Pastikan hanya ada <strong>satu SK</strong> yang mendapatkan status <code>Ya</code> untuk <em>Tunkel</em>, <em>Tunjangan Anak</em>, atau <em>Kehormatan</em>.
                </div>

                <!-- 1. TABEL STRUKTURAL -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="font-weight-bold text-primary mb-0" style="font-size: 0.9rem;">
                        <i class="fa fa-sitemap mr-1 me-1"></i> 1. Penempatan Struktural
                    </h6>
                    <a href="{{ url('/manajemen-umana/penempatan') }}" target="_blank" class="small text-primary font-weight-bold">
                        Buka Menu Penempatan <i class="fas fa-external-link-alt ml-1 ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive mb-3">
                    <table class="table table-sm table-bordered table-striped" style="font-size: 12px;">
                        <thead class="thead-light">
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
                            <tr><td colspan="7" class="text-center text-muted">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- 2. TABEL PENGAJAR -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="font-weight-bold text-success mb-0" style="font-size: 0.9rem;">
                        <i class="fa fa-graduation-cap mr-1 me-1"></i> 2. Penugasan Pengajar (Guru / Dosen)
                    </h6>
                    <a href="{{ url('/Penempatan/pengajar') }}" target="_blank" class="small text-success font-weight-bold">
                        Buka Menu Pengajar <i class="fas fa-external-link-alt ml-1 ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped" style="font-size: 12px;">
                        <thead class="thead-light">
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
                            <tr><td colspan="7" class="text-center text-muted">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer py-2 px-3 border-top-0">
                <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            let currentScope = 'berlaku';
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
                        // Update angka di cards statistik
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
                        data: 'nik',
                        render: function(data) {
                            return '<strong>' + data + '</strong>';
                        }
                    },
                    {
                        data: 'nama_lengkap',
                        render: function(data, type, row) {
                            return `<div class="d-flex align-items-center">
                                        <div class="mr-2 me-2">
                                            <i class="fas fa-user-circle text-primary" style="font-size: 26px;"></i>
                                        </div>
                                        <div>
                                            <span class="font-weight-bold text-dark d-block">${data}</span>
                                            <small class="text-muted"><i class="fas fa-phone-alt mr-1 me-1"></i>${row.nomor_hp}</small>
                                        </div>
                                    </div>`;
                        }
                    },
                    {
                        data: 'status_sertifikasi',
                        className: 'text-center',
                        render: function(data) {
                            if (data && data.toLowerCase() === 'sudah') {
                                return '<span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1 me-1"></i>Sudah</span>';
                            }
                            return '<span class="badge badge-light px-2 py-1 text-muted">Belum</span>';
                        }
                    },
                    {
                        data: 'pelanggaran',
                        render: function(data) {
                            let html = '';
                            data.forEach(item => {
                                if (item.includes('Tunkel')) {
                                    html += `<span class="badge-duplikasi badge-tunkel"><i class="fas fa-exclamation-circle"></i> ${item}</span><br>`;
                                } else if (item.includes('Anak')) {
                                    html += `<span class="badge-duplikasi badge-anak"><i class="fas fa-exclamation-circle"></i> ${item}</span><br>`;
                                } else {
                                    html += `<span class="badge-duplikasi badge-kehormatan"><i class="fas fa-exclamation-circle"></i> ${item}</span><br>`;
                                }
                            });
                            return html;
                        }
                    },
                    {
                        data: 'details',
                        render: function(data) {
                            if (!data || data.length === 0) return '-';
                            let html = '<div class="d-flex flex-column">';
                            data.forEach(item => {
                                const tipeClass = item.tipe === 'Struktural' ? 'text-primary' : 'text-success';
                                const badges = [];
                                if (item.tunkel === 'Ya') badges.push('<span class="text-danger font-weight-bold">Tunkel: Ya</span>');
                                if (item.tunj_anak === 'Ya') badges.push('<span class="text-primary font-weight-bold">Anak: Ya</span>');
                                if (item.kehormatan === 'Ya') badges.push('<span class="text-purple font-weight-bold" style="color: #7c3aed;">Kehormatan: Ya</span>');

                                html += `<div class="unit-item">
                                            <div>
                                                <strong class="${tipeClass}">[${item.tipe}]</strong> 
                                                <span class="text-dark font-weight-bold">${item.lembaga}</span>
                                                <small class="text-muted d-block">(${item.posisi})</small>
                                            </div>
                                            <div class="small text-right text-end">
                                                ${badges.join(' | ') || '<span class="text-muted">Tanpa Tunjangan</span>'}
                                            </div>
                                         </div>`;
                            });
                            html += '</div>';
                            return html;
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `<button type="button" class="btn-cek-detail shadow-sm btn-buka-modal" data-nik="${row.nik}">
                                        <i class="fas fa-search-plus mr-1 me-1"></i> Rincian
                                    </button>`;
                        }
                    }
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ temuan",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 temuan",
                    zeroRecords: "Alhamdulillah! Tidak ditemukan Umana dengan tunjangan ganda pada filter ini.",
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

            // Ganti Filter Scope (SK Berlaku Saat Ini vs Semua SK Status Aktif)
            $('.btn-scope').on('click', function() {
                $('.btn-scope').removeClass('active');
                $(this).addClass('active');
                currentScope = $(this).data('scope');

                // Update link export excel
                $('#btn-export-excel').attr('href', '{{ url("/validasi/audit-tunjangan/export-excel") }}?scope=' + currentScope + '&kategori_ganda=' + currentKategori);
                
                table.ajax.reload();
            });

            // Ganti Filter Kategori Ganda (Pill Filters)
            $('.btn-pill-filter').on('click', function() {
                $('.btn-pill-filter').removeClass('active');
                $(this).addClass('active');
                currentKategori = $(this).data('kategori');

                // Update link export excel
                $('#btn-export-excel').attr('href', '{{ url("/validasi/audit-tunjangan/export-excel") }}?scope=' + currentScope + '&kategori_ganda=' + currentKategori);

                table.ajax.reload();
            });

            // Klik Rincian / Modal Validasi
            $(document).on('click', '.btn-buka-modal', function() {
                const nik = $(this).data('nik');
                if(!nik) return;

                $('#detail-nama').text('Memuat...');
                $('#detail-nik').text(nik);
                $('#detail-sertifikasi').text('-');
                $('#detail-tbody-struktural').html('<tr><td colspan="7" class="text-center text-muted">Memuat data...</td></tr>');
                $('#detail-tbody-pengajar').html('<tr><td colspan="7" class="text-center text-muted">Memuat data...</td></tr>');

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
                                ? '<span class="badge badge-danger px-2 py-1"><i class="fa fa-check mr-1 me-1"></i>Ya</span>'
                                : '<span class="badge badge-secondary px-2 py-1">Tidak</span>';

                            const tunjAnakBadge = (item.tunj_anak === 'Ya')
                                ? '<span class="badge badge-danger px-2 py-1"><i class="fa fa-check mr-1 me-1"></i>Ya</span>'
                                : '<span class="badge badge-secondary px-2 py-1">Tidak</span>';

                            const kehormatanBadge = (item.kehormatan === 'Ya')
                                ? '<span class="badge badge-danger px-2 py-1"><i class="fa fa-check mr-1 me-1"></i>Ya</span>'
                                : '<span class="badge badge-secondary px-2 py-1">Tidak</span>';

                            const masa = (item.tgl_mulai ? item.tgl_mulai : '-') + ' s/d ' + (item.tgl_selesai ? item.tgl_selesai : 'Sekarang');

                            htmlStruk += `<tr>
                                <td class="text-center font-weight-bold">${idx + 1}</td>
                                <td><strong>${item.nama_lembaga || '-'}</strong></td>
                                <td>${item.nama_jabatan || item.jabatan_lembaga || '-'}</td>
                                <td><small>${masa}</small></td>
                                <td class="text-center">${tunkelBadge}</td>
                                <td class="text-center">${tunjAnakBadge}</td>
                                <td class="text-center">${kehormatanBadge}</td>
                            </tr>`;
                        });
                    } else {
                        htmlStruk = '<tr><td colspan="7" class="text-center text-muted">Tidak ada SK struktural aktif.</td></tr>';
                    }
                    $('#detail-tbody-struktural').html(htmlStruk);

                    // 2. Render Pengajar
                    let htmlPeng = '';
                    if(res.pengajar && res.pengajar.length > 0) {
                        res.pengajar.forEach((item, idx) => {
                            const tunkelBadge = (item.tunj_kel === 'Ya')
                                ? '<span class="badge badge-danger px-2 py-1"><i class="fa fa-check mr-1 me-1"></i>Ya</span>'
                                : '<span class="badge badge-secondary px-2 py-1">Tidak</span>';

                            const tunjAnakBadge = (item.tunj_anak === 'Ya')
                                ? '<span class="badge badge-danger px-2 py-1"><i class="fa fa-check mr-1 me-1"></i>Ya</span>'
                                : '<span class="badge badge-secondary px-2 py-1">Tidak</span>';

                            const kehormatanBadge = (item.kehormatan === 'Ya')
                                ? '<span class="badge badge-danger px-2 py-1"><i class="fa fa-check mr-1 me-1"></i>Ya</span>'
                                : '<span class="badge badge-secondary px-2 py-1">Tidak</span>';

                            const masa = (item.tgl_mulai ? item.tgl_mulai : '-') + ' s/d ' + (item.tgl_selesai ? item.tgl_selesai : 'Sekarang');

                            htmlPeng += `<tr>
                                <td class="text-center font-weight-bold">${idx + 1}</td>
                                <td><strong>${item.nama_lembaga || '-'}</strong></td>
                                <td><span class="badge badge-primary">${item.jenis_pengajar || 'Pengajar'}</span></td>
                                <td><small>${masa}</small></td>
                                <td class="text-center">${tunkelBadge}</td>
                                <td class="text-center">${tunjAnakBadge}</td>
                                <td class="text-center">${kehormatanBadge}</td>
                            </tr>`;
                        });
                    } else {
                        htmlPeng = '<tr><td colspan="7" class="text-center text-muted">Tidak ada SK pengajar aktif.</td></tr>';
                    }
                    $('#detail-tbody-pengajar').html(htmlPeng);
                });
            });
        });
    </script>
@endpush
