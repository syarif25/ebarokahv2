@extends('layouts.app')

@section('title', 'PayRoll - E-Barokah')
@section('page_title')
<div class="d-flex align-items-center flex-wrap gap-2">
    <span class="text-danger font-weight-bold">Payroll</span> 
    <span class="text-muted">/</span>
    <a href="{{ asset('upload/bsi.xlsb') }}" class="btn btn-xs text-white px-3" style="background-color: #00a15d; border-radius: 20px; font-weight: 600;" download><i class="fas fa-download me-1"></i> BSI</a>
    <a href="{{ asset('upload/BRI.xls') }}" class="btn btn-xs text-white px-3" style="background-color: #0284c7; border-radius: 20px; font-weight: 600;" download><i class="fas fa-download me-1"></i> BRI</a>
    <a href="{{ asset('upload/BNI.xlsm') }}" class="btn btn-xs text-white px-3" style="background-color: #ea580c; border-radius: 20px; font-weight: 600;" download><i class="fas fa-download me-1"></i> BNI</a>
</div>
@endsection

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <style>
        /* Table Styling konsisten dengan menu lainnya */
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

        /* Modal Rincian & DataTables Buttons Styling */
        #modal_sudah_trans .modal-header {
            border-bottom: 1px solid #f1f5f9;
        }
        #modal_sudah_trans .modal-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1e293b;
        }
        .modal-detail-label {
            font-size: 0.82rem;
            color: #64748b;
            margin-bottom: 2px;
        }
        .modal-detail-val {
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
        }
        .dt-buttons {
            display: inline-flex !important;
            gap: 6px !important;
            margin-bottom: 14px !important;
        }
        .dt-buttons .dt-button {
            background: #ffffff !important;
            border: 1px solid #d1d5db !important;
            border-radius: 6px !important;
            color: #374151 !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            padding: 5px 14px !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
            background-image: none !important;
        }
        .dt-buttons .dt-button:hover {
            background: #f9fafb !important;
            border-color: #9ca3af !important;
            color: #111827 !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08) !important;
        }
        .dt-buttons .dt-button:active {
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1) !important;
        }
        #tabel2 {
            border-collapse: separate !important;
            border-spacing: 0 !important;
        }
        #tabel2 thead th {
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            background-color: #ffffff !important;
            border-top: none !important;
            border-bottom: 2px solid #e2e8f0 !important;
            padding: 10px 12px !important;
            white-space: nowrap;
        }
        #tabel2 tbody td {
            font-size: 0.875rem;
            color: #1e293b;
            padding: 12px 12px !important;
            border-bottom: 1px solid #f1f5f9 !important;
            border-top: none !important;
            vertical-align: middle;
            white-space: nowrap;
        }
        #tabel2 tbody tr:hover td {
            background-color: #f8fafc !important;
        }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="card-title">Daftar Barokah Perlembaga</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabel_payroll" class="display" style="min-width: 845px; width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>Nama Lembaga</th>
                                <th>Kategori</th>
                                <th>Periode</th>
                                <th>Jumlah Nominal</th>
                                <th>Status WA</th>
                                <th>Status</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                            <tr class="filters">
                                <th></th>
                                <th><input type="text" placeholder="Cari lembaga..."></th>
                                <th>
                                    <select>
                                        <option value="">Semua Kategori</option>
                                        <option value="Struktural">Struktural</option>
                                        <option value="Pengajar">Pengajar</option>
                                        <option value="Satpam">Satpam</option>
                                    </select>
                                </th>
                                <th><input type="text" placeholder="Cari periode..."></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
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

<!-- Modal Rincian Nominal Transfer -->
<div class="modal fade" id="modal_sudah_trans" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="modal-title font-weight-bold text-dark mb-0">
                    Form Detail Nominal Transfer
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formTransfer">
                    <!-- Detail Lembaga, Periode, Jumlah persis seperti di gambar -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <span class="modal-detail-label d-block">Nama Lembaga</span>
                            <div class="modal-detail-val" id="detail_nama_lembaga">-</div>
                            <input type="hidden" id="form_id_kehadiran_lembaga" name="id_kehadiran_lembaga">
                            <input type="hidden" id="form_periode" name="periode">
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <span class="modal-detail-label d-block">Periode</span>
                            <div class="modal-detail-val" id="detail_periode">-</div>
                        </div>
                        <div class="col-md-4">
                            <span class="modal-detail-label d-block">Jumlah</span>
                            <div class="modal-detail-val" id="detail_jumlah">0</div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tabel2" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 40px;">No</th>
                                    <th>Nama Lengkap</th>
                                    <th>Nama Bank</th>
                                    <th>Nomor Rekening</th>
                                    <th>Atas Nama</th>
                                    <th>Nominal</th>
                                    <th>+ Admin</th>
                                    <th>Bifast Kode</th>
                                    <th>Kode</th>
                                    <th>Ref</th>
                                    <th>Lembaga</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_detail_transfer">
                                <!-- Data baris rincian dimasukkan via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-white border-top py-3 px-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn text-white rounded-pill px-4" id="btnSaveTransfer" style="background-color: #00a15d;" onclick="submitTransfer()">
                    <i class="fas fa-check me-1"></i> Sudah ditransfer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Log Pengiriman WhatsApp -->
<div class="modal fade" id="waLogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <h5 class="modal-title font-weight-bold text-dark mb-0">
                    <i class="fab fa-whatsapp text-success me-2"></i> Log Pengiriman WhatsApp
                </h5>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-warning btn-sm text-white rounded-pill px-3" id="bulkResendBtn" onclick="bulkResendFailed()" style="display:none;">
                        <i class="fas fa-redo-alt me-1"></i> Resend Semua Failed (<span id="failedCountBadge">0</span>)
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0" id="tabel_wa_log">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;" class="text-center">#</th>
                                <th>Nama Umana</th>
                                <th>No. HP</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">HTTP Code</th>
                                <th>Response</th>
                                <th>Waktu Kirim</th>
                                <th class="text-center" style="width: 90px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="waLogTableBody">
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Memuat data...</td>
                            </tr>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>

    <script>
    // Polyfill if Swal.fire is missing
    if (typeof Swal !== 'undefined' && !Swal.fire) {
        Swal.fire = function() {
            return Swal.apply(this, arguments);
        };
    }

    function openModal(modalId) {
        var el = document.getElementById(modalId);
        if (!el) return;
        try {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var modal = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
                modal.show();
                return;
            }
        } catch (e) {
            console.warn('Bootstrap modal open fallback:', e);
        }
        if ($.fn && $.fn.modal) {
            $('#' + modalId).modal('show');
        }
    }

    function closeModal(modalId) {
        var el = document.getElementById(modalId);
        if (!el) return;
        try {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                var modal = bootstrap.Modal.getInstance(el);
                if (modal) {
                    modal.hide();
                    return;
                }
            }
        } catch (e) {
            console.warn('Bootstrap modal hide fallback:', e);
        }
        if ($.fn && $.fn.modal) {
            $('#' + modalId).modal('hide');
        }
    }

    var table;
    var table_item = null;
    var currentWaLembagaId = null;
    var currentTransferItems = [];

    $(function () {
        table = $('#tabel_payroll').DataTable({
            processing: true,
            serverSide: false,
            orderCellsTop: true,
            ajax: {
                url: '{{ url("/payroll/data") }}',
                type: 'GET'
            },
            initComplete: function () {
                var api = this.api();
                api.columns().every(function (i) {
                    var column = this;
                    $('input, select', $('.filters th').eq(i)).on('keyup change clear', function () {
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
                    className: 'font-weight-bold text-muted text-center' 
                },
                { 
                    data: 'nama_lembaga', 
                    render: function (data) {
                        return `<span class="text-dark font-weight-bold">${data}</span>`;
                    }
                },
                { 
                    data: 'kategori',
                    render: function (data) {
                        return `<span style="color: #333;">${data}</span>`;
                    }
                },
                { 
                    data: 'periode',
                    render: function (data) {
                        return `<span style="color: #333; font-weight: 500;">${data}</span>`;
                    }
                },
                { 
                    data: 'jumlah_nominal',
                    render: function (data) {
                        return `<span style="color: #16a34a; font-weight: 700;">${data}</span>`;
                    }
                },
                { 
                    data: 'status_wa',
                    className: 'text-center'
                },
                { 
                    data: 'status_html'
                },
                { 
                    data: 'aksi_html',
                    orderable: false,
                    className: 'text-nowrap'
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
                emptyTable: "Tidak ada data payroll",
                paginate: {
                    previous: '<i class="fas fa-chevron-left"></i>',
                    next: '<i class="fas fa-chevron-right"></i>'
                }
            }
        });

        // Event handler klik Lihat Rincian / Sudah Transfer
        $(document).on('click', '.btn-rincian', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var mode = $(this).data('mode'); // 'transfer' or 'selesai'
            bukaDetailTransfer(id, mode);
        });

        // Event handler klik Log WA
        $(document).on('click', '.btn-log-wa', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            showWaLog(id);
        });
    });

    function initTableItem() {
        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tabel2')) {
            $('#tabel2').DataTable().destroy();
        }

        var namaLembaga = $('#detail_nama_lembaga').text().trim() || 'Payroll';
        var periode = $('#detail_periode').text().trim() || '';

        try {
            var dtConfig = {
                paging: false,
                searching: false,
                ordering: false,
                info: true,
                destroy: true,
                deferRender: true,
                language: {
                    info: 'Menampilkan _TOTAL_ data penerima transfer',
                    infoEmpty: 'Tidak ada data'
                }
            };

            if ($.fn.DataTable.Buttons) {
                dtConfig.dom = '<"d-flex align-items-center mb-2"B>rt<"d-flex align-items-center justify-content-between mt-3 text-muted fs-12"i>';
                dtConfig.buttons = [
                    {
                        extend: 'copyHtml5',
                        text: 'Copy',
                        title: 'Form Detail Nominal Transfer - ' + namaLembaga + ' - ' + periode
                    },
                    {
                        extend: 'excelHtml5',
                        text: 'Excel',
                        filename: 'Payroll_' + namaLembaga.replace(/[^a-zA-Z0-9_-]/g, '_') + '_' + periode.replace(/[^a-zA-Z0-9_-]/g, '_'),
                        title: 'Form Detail Nominal Transfer - ' + namaLembaga + ' (' + periode + ')'
                    },
                    {
                        extend: 'csvHtml5',
                        text: 'CSV',
                        filename: 'Payroll_' + namaLembaga.replace(/[^a-zA-Z0-9_-]/g, '_') + '_' + periode.replace(/[^a-zA-Z0-9_-]/g, '_'),
                        title: 'Form Detail Nominal Transfer - ' + namaLembaga + ' (' + periode + ')'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        filename: 'Payroll_' + namaLembaga.replace(/[^a-zA-Z0-9_-]/g, '_') + '_' + periode.replace(/[^a-zA-Z0-9_-]/g, '_'),
                        title: 'Form Detail Nominal Transfer - ' + namaLembaga + ' (' + periode + ')'
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        title: 'Form Detail Nominal Transfer - ' + namaLembaga + ' (' + periode + ')'
                    }
                ];
            } else {
                dtConfig.dom = 'rt<"d-flex align-items-center justify-content-between mt-3 text-muted fs-12"i>';
            }

            table_item = $('#tabel2').DataTable(dtConfig);
        } catch (err) {
            console.error('Error init DataTable tabel2:', err);
        }
    }

    function bukaDetailTransfer(id, mode) {
        if (mode === 'selesai') {
            $('#btnSaveTransfer').hide();
        } else {
            $('#btnSaveTransfer').show();
        }

        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tabel2')) {
            $('#tabel2').DataTable().destroy();
        }

        currentTransferItems = [];
        $('#tbody_detail_transfer').html('<tr><td colspan="11" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-2"></i> Memuat detail transfer...</td></tr>');
        openModal('modal_sudah_trans');

        $.ajax({
            url: '{{ url("/payroll/detail") }}/' + id,
            type: 'GET',
            dataType: 'JSON',
            success: function (res) {
                if (!res || !res.data_elemen) {
                    Swal.fire('Error', 'Data detail transfer tidak ditemukan.', 'error');
                    closeModal('modal_sudah_trans');
                    return;
                }

                var el = res.data_elemen;
                $('#detail_nama_lembaga').text(el.nama_lembaga);
                var formattedJumlah = el.jumlah_total || (el.jumlah_total_raw ? new Intl.NumberFormat('id-ID').format(el.jumlah_total_raw) : '0');
                $('#detail_jumlah').text(formattedJumlah);
                $('#form_id_kehadiran_lembaga').val(el.id_kehadiran_lembaga);
                $('#form_periode').val(el.periode);

                $('#tbody_detail_transfer').html(res.html_item);

                currentTransferItems = res.data_items || [];

                initTableItem();
            },
            error: function () {
                Swal.fire('Error', 'Gagal memuat detail data.', 'error');
                closeModal('modal_sudah_trans');
            }
        });
    }

    function submitTransfer() {
        const BATCH_SIZE = 10;
        let dataArray = [];

        if (currentTransferItems && currentTransferItems.length > 0) {
            dataArray = currentTransferItems;
        } else {
            const form = document.getElementById('formTransfer');
            const formData = new FormData(form);

            const namaLengkap = formData.getAll('nama_lengkap[]');
            const namaLembaga = formData.getAll('nama_lembaga[]');
            const namaBank    = formData.getAll('nama_bank[]');
            const norek       = formData.getAll('norek[]');
            const jumlah      = formData.getAll('diterima[]');
            const nomorHp     = formData.getAll('nomor_hp[]');
            const kategori    = formData.getAll('kategori[]');
            const bulan       = formData.getAll('bulan[]');
            const tahun       = formData.getAll('tahun[]');
            const idTotal     = formData.getAll('id_total[]');
            const idKehadiranLog = formData.getAll('id_kehadiran_lembaga_log[]');

            const periode     = formData.get('periode') || $('#detail_periode').text().trim();
            const idKehadiranLembaga = formData.get('id_kehadiran_lembaga') || $('#form_id_kehadiran_lembaga').val();

            if (namaLengkap && namaLengkap.length > 0) {
                dataArray = namaLengkap.map((_, i) => ({
                    nama_lengkap: namaLengkap[i],
                    nama_lembaga: namaLembaga[i],
                    nama_bank: namaBank[i],
                    norek: norek[i],
                    diterima: jumlah[i],
                    nomor_hp: nomorHp[i],
                    kategori: kategori[i],
                    bulan: bulan[i],
                    tahun: tahun[i],
                    id_total: idTotal[i],
                    id_kehadiran_lembaga_log: idKehadiranLog[i],
                    periode: periode,
                    id_kehadiran_lembaga: idKehadiranLembaga
                }));
            }
        }

        if (!dataArray || !dataArray.length) {
            Swal.fire('Info', 'Tidak ada data penerima transfer untuk diproses.', 'info');
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Transfer',
            text: 'Apakah Anda yakin transfer untuk ' + dataArray.length + ' penerima telah selesai dan ingin memproses notifikasi WhatsApp?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#00a15d',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Selesai Transfer',
            cancelButtonText: 'Batal'
        }).then((result) => {
            const isConfirmed = result && (result.isConfirmed || result.value === true);
            if (!isConfirmed) return;

            const batches = [];
            for (let i = 0; i < dataArray.length; i += BATCH_SIZE) {
                batches.push(dataArray.slice(i, i + BATCH_SIZE));
            }

            const totalBatches = batches.length;
            let currentBatch = 0;

            Swal.fire({
                title: 'Memproses Transfer & Notifikasi WA...',
                html: `
                    <div style="margin-top:10px;">
                        <b>Batch: <span id="batch-count">0</span> / ${totalBatches}</b>
                        <div class="progress" style="height: 22px; margin-top:10px; border-radius: 10px; background-color: #f1f5f9;">
                            <div id="swal-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                                role="progressbar" style="width: 0%; font-weight: 600;">0%</div>
                        </div>
                    </div>
                `,
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    sendNextBatch();
                },
                onOpen: () => {
                    sendNextBatch();
                }
            });

            function updateProgress() {
                const percent = Math.round((currentBatch / totalBatches) * 100);
                const bar = document.getElementById('swal-progress-bar');
                if (bar) {
                    bar.style.width = percent + '%';
                    bar.textContent = percent + '%';
                }
                const countEl = document.getElementById('batch-count');
                if (countEl) countEl.textContent = currentBatch;
            }

            function sendNextBatch() {
                if (currentBatch >= totalBatches) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Transfer Selesai',
                        text: `${dataArray.length} data transfer dan notifikasi WA berhasil diproses.`,
                        confirmButtonColor: '#00a15d',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        closeModal('modal_sudah_trans');
                        if (table && table.ajax) {
                            table.ajax.reload(null, false);
                        }
                    });
                    return;
                }

                const batch = batches[currentBatch];
                const batchForm = new FormData();

                batch.forEach(item => {
                    batchForm.append('nama_lengkap[]', item.nama_lengkap);
                    batchForm.append('nama_lembaga[]', item.nama_lembaga);
                    batchForm.append('nama_bank[]', item.nama_bank);
                    batchForm.append('norek[]', item.norek);
                    batchForm.append('diterima[]', item.diterima);
                    batchForm.append('nomor_hp[]', item.nomor_hp);
                    batchForm.append('kategori[]', item.kategori);
                    batchForm.append('bulan[]', item.bulan);
                    batchForm.append('tahun[]', item.tahun);
                    batchForm.append('id_total[]', item.id_total);
                    batchForm.append('id_kehadiran_lembaga_log[]', item.id_kehadiran_lembaga_log);
                });

                batchForm.append('periode', batch[0].periode);
                batchForm.append('id_kehadiran_lembaga', batch[0].id_kehadiran_lembaga);

                $.ajax({
                    url: '{{ url("/payroll/save") }}',
                    type: "POST",
                    data: batchForm,
                    contentType: false,
                    processData: false,
                    dataType: "JSON",
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function (data) {
                        if (data && data.status) {
                            currentBatch++;
                            updateProgress();
                            setTimeout(sendNextBatch, 800);
                        } else {
                            Swal.fire('Gagal', (data && data.message) ? data.message : `Gagal memproses batch ke-${currentBatch + 1}`, 'error');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire('Terjadi Error', `Batch ke-${currentBatch + 1} gagal diproses.`, 'error');
                    }
                });
            }
        });
    }

    function showWaLog(id) {
        currentWaLembagaId = id;
        $('#waLogTableBody').html('<tr><td colspan="8" class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-2"></i> Memuat log WA...</td></tr>');
        $('#bulkResendBtn').hide();
        openModal('waLogModal');

        $.ajax({
            url: '{{ url("/payroll/wa-log") }}',
            type: 'GET',
            data: { id_lembaga: id },
            dataType: 'JSON',
            success: function (logs) {
                if (!logs || logs.length === 0) {
                    $('#waLogTableBody').html('<tr><td colspan="8" class="text-center py-4 text-muted">Belum ada riwayat pengiriman WA untuk lembaga ini.</td></tr>');
                    return;
                }

                var html = '';
                var failedCount = 0;

                logs.forEach(function (log, idx) {
                    if (log.status === 'failed') failedCount++;

                    var badgeStatus = '<span class="badge bg-warning text-dark">Pending</span>';
                    if (log.status === 'success') {
                        badgeStatus = '<span class="badge bg-success text-white">Success</span>';
                    } else if (log.status === 'failed') {
                        badgeStatus = '<span class="badge bg-danger text-white">Failed</span>';
                    }

                    var btnResend = '';
                    if (log.status === 'failed') {
                        btnResend = `<button type="button" class="btn btn-xs btn-outline-warning rounded-pill" onclick="resendWa(${log.id_wa_log})"><i class="fas fa-redo-alt me-1"></i> Resend</button>`;
                    }

                    html += `<tr>
                        <td class="text-center">${idx + 1}</td>
                        <td class="font-weight-bold">${log.nama_umana || log.nama_penerima || '-'}</td>
                        <td>${log.nomor_hp || '-'}</td>
                        <td class="text-center">${badgeStatus}</td>
                        <td class="text-center">${log.http_code || '-'}</td>
                        <td style="max-width: 200px; word-break: break-all; font-size: 11px;">${log.response || '-'}</td>
                        <td style="font-size: 12px;">${log.created_at || '-'}</td>
                        <td class="text-center">${btnResend}</td>
                    </tr>`;
                });

                $('#waLogTableBody').html(html);

                if (failedCount > 0) {
                    $('#failedCountBadge').text(failedCount);
                    $('#bulkResendBtn').show();
                } else {
                    $('#bulkResendBtn').hide();
                }
            },
            error: function () {
                $('#waLogTableBody').html('<tr><td colspan="8" class="text-center py-4 text-danger">Gagal memuat log WA.</td></tr>');
            }
        });
    }

    function resendWa(id) {
        Swal.fire({
            title: 'Kirim Ulang?',
            text: 'Pesan notifikasi WA ini akan dikirim ulang sekarang.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Kirim',
            cancelButtonText: 'Batal'
        }).then((result) => {
            const isConfirmed = result && (result.isConfirmed || result.value === true);
            if (isConfirmed) {
                $.ajax({
                    url: '{{ url("/payroll/resend-wa") }}/' + id,
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    dataType: 'JSON',
                    success: function (res) {
                        Swal.fire('Terkirim', res.message, 'success');
                        if (currentWaLembagaId) showWaLog(currentWaLembagaId);
                        if (table && table.ajax) {
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function () {
                        Swal.fire('Gagal', 'Terjadi kesalahan saat kirim ulang.', 'error');
                    }
                });
            }
        });
    }

    function bulkResendFailed() {
        if (!currentWaLembagaId) return;

        Swal.fire({
            title: 'Resend Semua Gagal?',
            text: 'Semua pesan WA yang berstatus failed akan dikirim ulang.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim Semua',
            cancelButtonText: 'Batal'
        }).then((result) => {
            const isConfirmed = result && (result.isConfirmed || result.value === true);
            if (isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); },
                    onOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: '{{ url("/payroll/bulk-resend") }}',
                    type: 'POST',
                    data: { id_lembaga: currentWaLembagaId },
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    dataType: 'JSON',
                    success: function (res) {
                        Swal.fire('Selesai', res.message, 'success');
                        showWaLog(currentWaLembagaId);
                        if (table && table.ajax) {
                            table.ajax.reload(null, false);
                        }
                    },
                    error: function () {
                        Swal.fire('Gagal', 'Terjadi kesalahan saat memproses resend.', 'error');
                    }
                });
            }
        });
    }
    </script>
@endpush
