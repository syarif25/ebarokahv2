@extends('layouts.app')

@section('title', 'Log Kehadiran Struktural - E-Barokah')

@section('page_title')
<span class="text-danger font-weight-bold">Log</span> / Kehadiran Struktural
@endsection

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <style>
        /* Card Filter Styling */
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

        /* Select2 Styling */
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
            font-size: 13px !important;
            color: #1e293b !important;
            font-weight: 500 !important;
            padding-left: 0 !important;
            padding-right: 20px !important;
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
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
            z-index: 9999 !important;
        }

        /* Badges */
        .badge-id-kehadiran {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            padding: 5px 10px !important;
            border-radius: 6px !important;
            border: 1px solid #cbd5e1 !important;
        }

        .badge-periode {
            background-color: #f8fafc !important;
            color: #1e293b !important;
            border: 1px solid #e2e8f0 !important;
            font-weight: 600 !important;
            font-size: 12px !important;
            padding: 5px 10px !important;
            border-radius: 8px !important;
        }

        .badge-kehadi {
            background-color: #ede9fe !important;
            color: #6d28d9 !important;
            border: 1px solid #ddd6fe !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            padding: 5px 10px !important;
            border-radius: 6px !important;
        }

        .badge-stat-hadir {
            background-color: #10b981 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 11px !important;
            padding: 4px 8px !important;
            border-radius: 6px !important;
        }
        .badge-stat-tugas {
            background-color: #0284c7 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 11px !important;
            padding: 4px 8px !important;
            border-radius: 6px !important;
        }
        .badge-stat-izin {
            background-color: #f59e0b !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 11px !important;
            padding: 4px 8px !important;
            border-radius: 6px !important;
        }
        .badge-stat-sakit {
            background-color: #ef4444 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 11px !important;
            padding: 4px 8px !important;
            border-radius: 6px !important;
        }

        /* Clean Table */
        table.dataTable.display tbody td {
            padding: 0.85rem 1rem !important;
            vertical-align: middle;
        }
        table.dataTable.display thead th {
            border-top: none !important;
            border-bottom: 2px solid #e2e8f0 !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #475569 !important;
            background-color: #f8fafc !important;
        }

        .btn-custom-action {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .btn-success-custom {
            background-color: #00a15d !important;
            border-color: #00a15d !important;
            color: #ffffff !important;
            border-radius: 10px !important;
            padding: 9px 20px !important;
            font-weight: 600 !important;
            transition: all 0.2s ease !important;
        }
        .btn-success-custom:hover {
            background-color: #00874e !important;
            border-color: #00874e !important;
            box-shadow: 0 4px 12px rgba(0, 161, 93, 0.25) !important;
        }

        .input-custom {
            border: 1px solid #cbd5e1 !important;
            border-radius: 10px !important;
            height: 42px !important;
            padding: 8px 14px !important;
            font-size: 13px !important;
        }
        .input-custom:focus {
            border-color: #00a15d !important;
            box-shadow: 0 0 0 3px rgba(0, 161, 93, 0.15) !important;
            outline: none !important;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Filter Card -->
    <div class="row">
        <div class="col-12">
            <div class="filter-card">
                <div class="row align-items-end g-3">
                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="filter-label">
                            <i class="fas fa-university text-primary"></i> Lembaga
                        </label>
                        <select id="filter_lembaga" class="form-control select2-filter">
                            <option value="">Semua Lembaga</option>
                            @foreach($lembagaList as $lem)
                                <option value="{{ $lem->id_lembaga }}">{{ $lem->nama_lembaga }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-4 col-6">
                        <label class="filter-label">
                            <i class="far fa-calendar-alt text-success"></i> Bulan
                        </label>
                        <select id="filter_bulan" class="form-control select2-filter">
                            <option value="">Semua Bulan</option>
                            @foreach($bulanList as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-4 col-6">
                        <label class="filter-label">
                            <i class="far fa-clock text-info"></i> Tahun
                        </label>
                        <select id="filter_tahun" class="form-control select2-filter">
                            <option value="">Semua Tahun</option>
                            @foreach($tahunList as $th)
                                <option value="{{ $th }}">{{ $th }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-4 col-6">
                        <label class="filter-label">
                            <i class="fas fa-hashtag text-warning"></i> ID Blanko
                        </label>
                        <input type="text" id="filter_kehadi" class="form-control input-custom" placeholder="ID Blanko...">
                    </div>

                    <div class="col-lg-3 col-md-8 col-12 d-flex justify-content-end gap-2">
                        <button type="button" id="btn_reset_filter" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1" style="border-radius: 10px; height: 42px;">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                        <button type="button" id="btn_tambah_data" class="btn btn-success-custom d-inline-flex align-items-center gap-2">
                            <i class="fas fa-plus"></i> Tambah Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="row">
        <div class="col-12">
            <div class="card" style="border-radius: 14px; border: 1px solid #eef2f6; box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);">
                <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title text-dark font-weight-bold mb-0">Daftar Log Kehadiran Umana</h5>
                    <button type="button" id="btn_refresh_table" class="btn btn-sm btn-light border text-secondary" title="Segarkan Data">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabel_kehadiran" class="table display table-hover" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width: 40px;">No</th>
                                    <th style="width: 60px;">ID</th>
                                    <th>Nama Umana & NIK</th>
                                    <th>Lembaga</th>
                                    <th>Periode</th>
                                    <th>ID Blanko</th>
                                    <th>Kehadiran</th>
                                    <th>Tgl Input</th>
                                    <th style="width: 80px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables Server-Side Processing -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form Tambah / Edit Kehadiran Struktural -->
<div class="modal fade" id="modal_kehadiran" tabindex="-1" aria-labelledby="modalKehadiranLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header bg-light" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title font-weight-bold text-dark" id="modalKehadiranLabel">Tambah Log Kehadiran Struktural</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_kehadiran">
                <div class="modal-body p-4">
                    <input type="hidden" id="kehadiran_id" name="id_kehadiran">

                    <div class="row g-3">
                        <!-- Select Penempatan / Umana via AJAX -->
                        <div class="col-12">
                            <label class="form-label font-weight-bold text-dark">
                                Umana / Penempatan <span class="text-danger">*</span>
                            </label>
                            <select id="modal_id_penempatan" name="id_penempatan" class="form-control" style="width: 100%;" required>
                                <option value="">Ketik untuk mencari nama Umana atau NIK...</option>
                            </select>
                            <small class="text-muted">Cari berdasarkan nama lengkap, NIK, atau nama lembaga penempatan.</small>
                        </div>

                        <!-- ID Kehadi (ID Blanko Lembaga) -->
                        <div class="col-md-6 col-12">
                            <label class="form-label font-weight-bold text-dark">
                                ID Blanko Kehadiran Lembaga (ID Kehadi)
                            </label>
                            <input type="number" id="modal_id_kehadi" name="id_kehadi" class="form-control input-custom" placeholder="Contoh: 12">
                            <small class="text-muted">Opsional: ID parent blanko pada tabel kehadiran_lembaga.</small>
                        </div>

                        <!-- Bulan & Tahun -->
                        <div class="col-md-3 col-6">
                            <label class="form-label font-weight-bold text-dark">
                                Bulan <span class="text-danger">*</span>
                            </label>
                            <select id="modal_bulan" name="bulan" class="form-control select2-modal" required>
                                <option value="">Pilih Bulan</option>
                                @foreach($bulanList as $b)
                                    <option value="{{ $b }}">{{ $b }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 col-6">
                            <label class="form-label font-weight-bold text-dark">
                                Tahun <span class="text-danger">*</span>
                            </label>
                            <select id="modal_tahun" name="tahun" class="form-control select2-modal" required>
                                <option value="">Pilih Tahun</option>
                                @foreach($tahunList as $th)
                                    <option value="{{ $th }}">{{ $th }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Detail Kehadiran (Hadir, Tugas, Izin, Sakit) -->
                        <div class="col-12 mt-3">
                            <div class="p-3 bg-light rounded-3 border">
                                <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-check-square text-success me-2"></i>Rincian Kehadiran</h6>
                                <div class="row g-2">
                                    <div class="col-md-3 col-6">
                                        <label class="form-label font-weight-bold text-success mb-1">Jumlah Hadir <span class="text-danger">*</span></label>
                                        <input type="number" id="modal_jumlah_hadir" name="jumlah_hadir" class="form-control input-custom" min="0" value="0" required>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <label class="form-label font-weight-bold text-info mb-1">Jumlah Tugas</label>
                                        <input type="number" id="modal_jumlah_tugas" name="jumlah_tugas" class="form-control input-custom" min="0" placeholder="0">
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <label class="form-label font-weight-bold text-warning mb-1">Jumlah Izin</label>
                                        <input type="number" id="modal_jumlah_izin" name="jumlah_izin" class="form-control input-custom" min="0" placeholder="0">
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <label class="form-label font-weight-bold text-danger mb-1">Jumlah Sakit</label>
                                        <input type="number" id="modal_jumlah_sakit" name="jumlah_sakit" class="form-control input-custom" min="0" placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" id="btn_submit_kehadiran" class="btn btn-success-custom px-4">
                        <i class="fas fa-save me-1"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

    <script>
    $(document).ready(function() {
        // Setup CSRF Token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Init Select2 Filter
        $('.select2-filter').select2({
            width: '100%',
            dropdownParent: $('.filter-card')
        });

        // Init Select2 Modal
        $('.select2-modal').select2({
            width: '100%',
            dropdownParent: $('#modal_kehadiran')
        });

        // Init Select2 AJAX search for Penempatan / Umana
        $('#modal_id_penempatan').select2({
            width: '100%',
            dropdownParent: $('#modal_kehadiran'),
            placeholder: 'Ketik untuk mencari nama Umana atau NIK...',
            minimumInputLength: 1,
            ajax: {
                url: '{{ url("/log/kehadiran-struktural/penempatan-options") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });

        // Init DataTable Server-Side
        var table = $('#tabel_kehadiran').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ url("/log/kehadiran-struktural/data") }}',
                type: 'POST',
                data: function(d) {
                    d.filter_lembaga = $('#filter_lembaga').val();
                    d.filter_bulan = $('#filter_bulan').val();
                    d.filter_tahun = $('#filter_tahun').val();
                    d.filter_kehadi = $('#filter_kehadi').val();
                }
            },
            columns: [
                { data: 'no', name: 'no', orderable: false, searchable: false, className: 'text-center' },
                { data: 'id_kehadiran', name: 'k.id_kehadiran', className: 'text-center' },
                { data: 'nama_umana', name: 'u.nama_lengkap' },
                { data: 'lembaga', name: 'l.nama_lembaga' },
                { data: 'periode', name: 'k.tahun' },
                { data: 'id_kehadi', name: 'k.id_kehadi', className: 'text-center' },
                { data: 'kehadiran', name: 'k.jumlah_hadir' },
                { data: 'tgl_input', name: 'k.tgl_input' },
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[1, 'desc']],
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            language: {
                processing: '<div class="d-flex justify-content-center py-3"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div></div>',
                search: "Cari Data:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                zeroRecords: "Tidak ada data yang sesuai",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Selanjutnya",
                    previous: "Sebelumnya"
                }
            }
        });

        // Filter Event Listeners
        $('#filter_lembaga, #filter_bulan, #filter_tahun').on('change', function() {
            table.ajax.reload();
        });

        var searchTimeout;
        $('#filter_kehadi').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                table.ajax.reload();
            }, 400);
        });

        $('#btn_reset_filter').on('click', function() {
            $('#filter_lembaga').val('').trigger('change.select2');
            $('#filter_bulan').val('').trigger('change.select2');
            $('#filter_tahun').val('').trigger('change.select2');
            $('#filter_kehadi').val('');
            table.ajax.reload();
        });

        $('#btn_refresh_table').on('click', function() {
            table.ajax.reload(null, false);
        });

        // Open Modal Tambah
        $('#btn_tambah_data').on('click', function() {
            $('#form_kehadiran')[0].reset();
            $('#kehadiran_id').val('');
            $('#modal_id_penempatan').val(null).trigger('change');
            $('#modal_bulan').val('').trigger('change.select2');
            $('#modal_tahun').val('').trigger('change.select2');
            $('#modalKehadiranLabel').text('Tambah Log Kehadiran Struktural');
            $('#modal_kehadiran').modal('show');
        });

        // Edit Data Click (Event Delegation on document)
        $(document).on('click', '.btn-edit-kehadiran', function(e) {
            e.preventDefault();
            var btn = $(this).closest('.btn-edit-kehadiran');
            var id = btn.attr('data-id');
            if (!id) return;

            // Show loading SweetAlert
            Swal.fire({
                title: 'Memuat Data...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                onBeforeOpen: function() {
                    if (typeof Swal.showLoading === 'function') {
                        Swal.showLoading();
                    }
                },
                didOpen: function() {
                    if (typeof Swal.showLoading === 'function') {
                        Swal.showLoading();
                    }
                }
            });

            $.ajax({
                url: '{{ url("/log/kehadiran-struktural/show") }}/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    Swal.close();
                    if (res.status && res.data) {
                        var d = res.data;
                        $('#kehadiran_id').val(d.id_kehadiran);
                        $('#modal_id_kehadi').val(d.id_kehadi);
                        $('#modal_bulan').val(d.bulan).trigger('change.select2');
                        $('#modal_tahun').val(d.tahun).trigger('change.select2');
                        $('#modal_jumlah_hadir').val(d.jumlah_hadir !== null ? d.jumlah_hadir : 0);
                        $('#modal_jumlah_tugas').val(d.jumlah_tugas);
                        $('#modal_jumlah_izin').val(d.jumlah_izin);
                        $('#modal_jumlah_sakit').val(d.jumlah_sakit);

                        // Set Select2 Penempatan option
                        if (d.id_penempatan) {
                            var textPenempatan = (d.nama_lengkap || 'Umana') + ' (' + (d.nama_lembaga || '-') + (d.jabatan_lembaga ? ' - ' + d.jabatan_lembaga : '') + ') [ID: ' + d.id_penempatan + ']';
                            var newOption = new Option(textPenempatan, d.id_penempatan, true, true);
                            $('#modal_id_penempatan').empty().append(newOption).trigger('change');
                        }

                        $('#modalKehadiranLabel').text('Edit Log Kehadiran #' + d.id_kehadiran);
                        $('#modal_kehadiran').modal('show');
                    } else {
                        Swal.fire('Gagal', res.message || 'Data tidak ditemukan', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    Swal.fire('Error', 'Terjadi kesalahan saat mengambil data: ' + xhr.statusText, 'error');
                }
            });
        });

        // Submit Form Tambah / Update
        $('#form_kehadiran').on('submit', function(e) {
            e.preventDefault();

            var id = $('#kehadiran_id').val();
            var isEdit = id ? true : false;
            var url = isEdit 
                ? '{{ url("/log/kehadiran-struktural/update") }}/' + id 
                : '{{ url("/log/kehadiran-struktural/store") }}';

            var submitBtn = $('#btn_submit_kehadiran');
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

            $.ajax({
                url: url,
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(res) {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan Data');
                    if (res.status) {
                        $('#modal_kehadiran').modal('hide');
                        table.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Gagal', res.message || 'Terjadi kesalahan', 'error');
                    }
                },
                error: function(xhr) {
                    submitBtn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan Data');
                    var msg = 'Terjadi kesalahan pada server';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire('Validasi / Error', msg, 'error');
                }
            });
        });

        function executeDeleteAjax(id) {
            Swal.fire({
                title: 'Menghapus Data...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                onBeforeOpen: function() {
                    if (typeof Swal.showLoading === 'function') {
                        Swal.showLoading();
                    }
                },
                didOpen: function() {
                    if (typeof Swal.showLoading === 'function') {
                        Swal.showLoading();
                    }
                }
            });

            $.ajax({
                url: '{{ url("/log/kehadiran-struktural/delete") }}/' + id,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                dataType: 'json',
                success: function(res) {
                    Swal.close();
                    if (res.status) {
                        table.ajax.reload(null, false);
                        Swal.fire({
                            type: 'success',
                            icon: 'success',
                            title: 'Terhapus!',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            type: 'error',
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Gagal menghapus data'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    var msg = 'Terjadi kesalahan saat menghapus data.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    } else if (xhr.statusText) {
                        msg += ' (' + xhr.status + ': ' + xhr.statusText + ')';
                    }
                    Swal.fire({
                        type: 'error',
                        icon: 'error',
                        title: 'Error',
                        text: msg
                    });
                }
            });
        }

        // Delete Data Click (Event Delegation on document)
        $(document).on('click', '.btn-delete-kehadiran', function(e) {
            e.preventDefault();
            var btn = $(this).closest('.btn-delete-kehadiran');
            var id = btn.attr('data-id');
            var nama = btn.attr('data-nama') || ('ID #' + id);

            if (!id) {
                Swal.fire({
                    type: 'error',
                    icon: 'error',
                    title: 'Error',
                    text: 'ID data kehadiran tidak ditemukan.'
                });
                return;
            }

            Swal.fire({
                title: 'Hapus Kehadiran?',
                text: 'Apakah Anda yakin ingin menghapus data kehadiran untuk ' + nama + '?',
                type: 'warning',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6e7881',
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then(function(result) {
                if (result && (result.value || result.isConfirmed || result === true)) {
                    executeDeleteAjax(id);
                }
            });
        });
    });
    </script>
@endpush
