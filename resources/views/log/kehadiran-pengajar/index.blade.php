@extends('layouts.app')

@section('title', 'Log Kehadiran Pengajar - E-Barokah')

@section('page_title')
<span class="text-danger font-weight-bold">Log</span> / Kehadiran Pengajar
@endsection

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <style>
        /* Custom Table Styling matching Master Menu */
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
            font-weight: 800;
            color: #333;
            background-color: transparent !important;
        }
        table.dataTable.display tbody tr {
            border-bottom: 1px solid #f8f8f8 !important;
        }
        table.dataTable.display tbody tr.odd {
            background-color: transparent !important;
        }
        table.dataTable.display tbody tr.even {
            background-color: transparent !important;
        }
        table.dataTable.display tbody tr:hover {
            background-color: #fafafa !important;
        }
        table.dataTable {
            border-bottom: none !important;
        }
        table.dataTable.no-footer {
            border-bottom: none !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.2em 0.5em !important;
        }

        /* Filter Card Styling */
        .filter-card {
            background: #ffffff;
            border-radius: 14px;
            border: 1px solid #eef2f6;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
        }
        .filter-label {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .input-custom {
            height: 42px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.5rem 0.875rem;
            font-size: 0.875rem;
            color: #1e293b;
            transition: all 0.2s ease;
        }
        .input-custom:focus {
            border-color: #00a15d;
            box-shadow: 0 0 0 3px rgba(0, 161, 93, 0.15);
            outline: none;
        }

        /* Select2 Theme Tweaks */
        .select2-container--default .select2-selection--single {
            height: 42px !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 6px 12px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 28px !important;
            color: #1e293b !important;
            font-size: 0.875rem !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
        }
        .select2-dropdown {
            border: 1px solid #cbd5e1 !important;
            border-radius: 10px !important;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1) !important;
            overflow: hidden !important;
        }

        /* Card Header Button */
        .btn-add-record {
            background-color: #00a15d;
            border-color: #00a15d;
            color: #ffffff;
            font-weight: 600;
            border-radius: 10px;
            padding: 0.55rem 1.25rem;
            transition: all 0.2s ease;
        }
        .btn-add-record:hover {
            background-color: #00874e;
            border-color: #00874e;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 161, 93, 0.25);
        }
        .btn-reset-filter {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.55rem 1rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-reset-filter:hover {
            background-color: #e2e8f0;
            color: #1e293b;
        }
    </style>
@endpush

@section('content')
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

                <div class="col-lg-3 col-md-3 col-6">
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

                <div class="col-lg-2 col-md-3 col-6">
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
                    <input type="text" id="filter_blanko" class="form-control input-custom" placeholder="ID Blanko...">
                </div>

                <div class="col-lg-2 col-md-4 col-6 d-flex align-items-end">
                    <button type="button" id="btn_reset_filter" class="btn btn-outline-secondary w-100 d-inline-flex align-items-center justify-content-center gap-1" style="border-radius: 10px; height: 42px;">
                        <i class="fas fa-undo"></i> Reset Filter
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Table Card (Matching Master Menu) -->
<div class="row">
    <div class="col-12">
        <div class="card" style="border-radius: 16px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: transparent; border-bottom: 1px solid #f1f5f9; padding: 1.5rem 1.75rem;">
                <div>
                    <h4 class="card-title font-weight-bold text-dark mb-1">Data Log Kehadiran Pengajar</h4>
                    <p class="text-muted fs-13 mb-0">Kelola riwayat baris detail kehadiran guru / dosen per periode</p>
                </div>
                <div>
                    <button type="button" id="btn_tambah_kehadiran" class="btn btn-add-record d-inline-flex align-items-center gap-2">
                        <i class="fas fa-plus"></i> Tambah Data
                    </button>
                </div>
            </div>

            <div class="card-body px-4 pt-3">
                <div class="table-responsive">
                    <table id="tabel_kehadiran" class="display" style="min-width: 845px; width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 50px;"><strong>No</strong></th>
                                <th><strong>ID</strong></th>
                                <th><strong>Nama Pengajar & NIK</strong></th>
                                <th><strong>Lembaga</strong></th>
                                <th><strong>Periode</strong></th>
                                <th><strong>ID Blanko</strong></th>
                                <th><strong>Kehadiran</strong></th>
                                <th><strong>Tgl Input</strong></th>
                                <th><strong>Aksi</strong></th>
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

<!-- Modal Form Tambah / Edit Kehadiran Pengajar -->
<div class="modal fade" id="modal_kehadiran" tabindex="-1" aria-labelledby="modalKehadiranLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
            <div class="modal-header bg-light" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title font-weight-bold text-dark" id="modalKehadiranLabel">Tambah Log Kehadiran Pengajar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_kehadiran">
                <div class="modal-body p-4">
                    <input type="hidden" id="kehadiran_id" name="id_kehadiran_pengajar">

                    <div class="row g-3">
                        <!-- Select Pengajar via AJAX -->
                        <div class="col-12">
                            <label class="form-label font-weight-bold text-dark">
                                Guru / Dosen (Pengajar) <span class="text-danger">*</span>
                            </label>
                            <select id="modal_id_pengajar" name="id_pengajar" class="form-control" style="width: 100%;" required>
                                <option value="">Ketik untuk mencari nama Pengajar atau NIK...</option>
                            </select>
                            <small class="text-muted">Cari berdasarkan nama lengkap, NIK, atau lembaga asal.</small>
                        </div>

                        <!-- ID Blanko Kehadiran Lembaga -->
                        <div class="col-md-6 col-12">
                            <label class="form-label font-weight-bold text-dark">
                                ID Blanko Kehadiran Lembaga
                            </label>
                            <input type="number" id="modal_id_kehadiran_lembaga" name="id_kehadiran_lembaga" class="form-control input-custom" placeholder="Contoh: 208">
                            <small class="text-muted">Opsional: ID parent blanko pada tabel kehadiran_lembaga.</small>
                        </div>

                        <!-- Periode Bulan & Tahun -->
                        <div class="col-md-3 col-6">
                            <label class="form-label font-weight-bold text-dark">
                                Bulan <span class="text-danger">*</span>
                            </label>
                            <select id="modal_bulan" name="bulan" class="form-control input-custom" required>
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
                            <input type="text" id="modal_tahun" name="tahun" class="form-control input-custom" placeholder="Contoh: 2026 atau 2023/2024" required>
                        </div>

                        <!-- Detail Jumlah Kehadiran -->
                        <div class="col-12 mt-3">
                            <h6 class="font-weight-bold text-primary mb-2">
                                <i class="fas fa-calendar-check me-1"></i> Rincian Jumlah Kehadiran
                            </h6>
                        </div>

                        <div class="col-md-3 col-6">
                            <label class="form-label font-weight-bold text-dark">
                                Hadir Pokok <span class="text-danger">*</span>
                            </label>
                            <input type="number" min="0" id="modal_jumlah_hadir" name="jumlah_hadir" class="form-control input-custom" value="0" required>
                        </div>

                        <div class="col-md-3 col-6">
                            <label class="form-label font-weight-bold text-dark">
                                Hadir 15%
                            </label>
                            <input type="number" min="0" id="modal_jumlah_hadir_15" name="jumlah_hadir_15" class="form-control input-custom" placeholder="0">
                        </div>

                        <div class="col-md-3 col-6">
                            <label class="form-label font-weight-bold text-dark">
                                Hadir 10%
                            </label>
                            <input type="number" min="0" id="modal_jumlah_hadir_10" name="jumlah_hadir_10" class="form-control input-custom" placeholder="0">
                        </div>

                        <div class="col-md-3 col-6">
                            <label class="form-label font-weight-bold text-dark">
                                Hadir Piket
                            </label>
                            <input type="number" min="0" id="modal_jumlah_hadir_piket" name="jumlah_hadir_piket" class="form-control input-custom" value="0">
                        </div>

                        <!-- Catatan Khusus -->
                        <div class="col-12">
                            <label class="form-label font-weight-bold text-dark">
                                Catatan Khusus
                            </label>
                            <textarea id="modal_catatan_khusus_umana" name="catatan_khusus_umana" class="form-control" rows="2" placeholder="Catatan khusus opsional..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" id="btn_save_kehadiran" class="btn btn-add-record">
                        <i class="fas fa-save me-1"></i> Simpan
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
            // Setup CSRF header
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Init Select2 Filter
            $('.select2-filter').select2({
                width: '100%'
            });

            // Init Select2 AJAX Pengajar di modal
            $('#modal_id_pengajar').select2({
                dropdownParent: $('#modal_kehadiran'),
                width: '100%',
                ajax: {
                    url: '{{ url("/log/kehadiran-pengajar/pengajar-options") }}',
                    dataType: 'json',
                    delay: 300,
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
                },
                placeholder: 'Ketik untuk mencari nama Pengajar...',
                minimumInputLength: 1
            });

            // Init DataTables Server-Side
            var table = $('#tabel_kehadiran').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ url("/log/kehadiran-pengajar/data") }}',
                    type: 'POST',
                    data: function(d) {
                        d.filter_lembaga = $('#filter_lembaga').val();
                        d.filter_bulan = $('#filter_bulan').val();
                        d.filter_tahun = $('#filter_tahun').val();
                        d.filter_blanko = $('#filter_blanko').val();
                    }
                },
                columns: [
                    { data: 'no', name: 'no', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'id_kehadiran_pengajar', name: 'kp.id_kehadiran_pengajar', className: 'text-center font-weight-bold text-muted' },
                    { data: 'nama_pengajar', name: 'u.nama_lengkap' },
                    { data: 'lembaga', name: 'l.nama_lembaga' },
                    { data: 'periode', name: 'kp.tahun' },
                    { data: 'id_blanko', name: 'kp.id_kehadiran_lembaga', className: 'text-center' },
                    { data: 'kehadiran', name: 'kp.jumlah_hadir' },
                    { data: 'tgl_input', name: 'kp.tgl_input', className: 'text-center text-muted fs-12' },
                    { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center' }
                ],
                order: [[1, 'desc']],
                pageLength: 10,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    },
                    processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Memuat data...</span></div>',
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    zeroRecords: "Tidak ada data yang cocok"
                }
            });

            // Filter Change Events
            $('#filter_lembaga, #filter_bulan, #filter_tahun').on('change', function() {
                table.draw();
            });

            var searchTimeout;
            $('#filter_blanko').on('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    table.draw();
                }, 400);
            });

            $('#btn_reset_filter').on('click', function() {
                $('#filter_lembaga').val('').trigger('change.select2');
                $('#filter_bulan').val('').trigger('change.select2');
                $('#filter_tahun').val('').trigger('change.select2');
                $('#filter_blanko').val('');
                table.draw();
            });

            // Tombol Tambah Data
            $('#btn_tambah_kehadiran').on('click', function() {
                $('#form_kehadiran')[0].reset();
                $('#kehadiran_id').val('');
                $('#modal_id_pengajar').val(null).trigger('change');
                $('#modalKehadiranLabel').text('Tambah Log Kehadiran Pengajar');
                $('#modal_kehadiran').modal('show');
            });

            // Edit Data
            $(document).on('click', '.btn-edit-kehadiran', function() {
                var id = $(this).data('id');
                
                swal({
                    title: 'Memuat Data...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    onBeforeOpen: function() {
                        swal.showLoading();
                    },
                    didOpen: function() {
                        if (typeof swal.showLoading === 'function') {
                            swal.showLoading();
                        }
                    }
                });

                $.ajax({
                    url: '{{ url("/log/kehadiran-pengajar/show") }}/' + id,
                    type: 'GET',
                    success: function(res) {
                        swal.close();
                        if (res.status && res.data) {
                            var d = res.data;
                            $('#form_kehadiran')[0].reset();
                            $('#kehadiran_id').val(d.id_kehadiran_pengajar);

                            // Set Select2 Pengajar option
                            if (d.id_pengajar) {
                                var label = (d.nama_lengkap || 'Pengajar') + ' (' + (d.nama_lembaga || '-') + (d.kategori_pengajar ? ' • ' + d.kategori_pengajar : '') + ') [ID: ' + d.id_pengajar + ']';
                                var newOption = new Option(label, d.id_pengajar, true, true);
                                $('#modal_id_pengajar').empty().append(newOption).trigger('change');
                            }

                            $('#modal_id_kehadiran_lembaga').val(d.id_kehadiran_lembaga || '');
                            $('#modal_bulan').val(d.bulan);
                            $('#modal_tahun').val(d.tahun);
                            $('#modal_jumlah_hadir').val(d.jumlah_hadir || 0);
                            $('#modal_jumlah_hadir_15').val(d.jumlah_hadir_15 !== null ? d.jumlah_hadir_15 : '');
                            $('#modal_jumlah_hadir_10').val(d.jumlah_hadir_10 !== null ? d.jumlah_hadir_10 : '');
                            $('#modal_jumlah_hadir_piket').val(d.jumlah_hadir_piket || 0);
                            $('#modal_catatan_khusus_umana').val(d.catatan_khusus_umana || '');

                            $('#modalKehadiranLabel').text('Edit Log Kehadiran Pengajar #' + d.id_kehadiran_pengajar);
                            $('#modal_kehadiran').modal('show');
                        } else {
                            swal('Error', res.message || 'Gagal memuat detail data', 'error');
                        }
                    },
                    error: function() {
                        swal.close();
                        swal('Error', 'Terjadi kesalahan sistem saat mengambil data', 'error');
                    }
                });
            });

            // Submit Form Simpan / Update
            $('#form_kehadiran').on('submit', function(e) {
                e.preventDefault();

                var id = $('#kehadiran_id').val();
                var url = id ? '{{ url("/log/kehadiran-pengajar/update") }}/' + id : '{{ url("/log/kehadiran-pengajar/store") }}';

                var $btn = $('#btn_save_kehadiran');
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan');
                        if (res.status) {
                            $('#modal_kehadiran').modal('hide');
                            swal({
                                title: 'Berhasil!',
                                text: res.message,
                                type: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            table.ajax.reload(null, false);
                        } else {
                            swal('Gagal', res.message || 'Gagal menyimpan data', 'error');
                        }
                    },
                    error: function(xhr) {
                        $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan');
                        var msg = 'Terjadi kesalahan saat menyimpan data.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        swal('Validasi Gagal', msg, 'error');
                    }
                });
            });

            // Hapus Data
            $(document).on('click', '.btn-delete-kehadiran', function() {
                var id = $(this).data('id');
                var nama = $(this).data('nama') || 'Data ini';

                swal({
                    title: 'Hapus Log Kehadiran Pengajar?',
                    text: 'Anda akan menghapus data kehadiran untuk ' + nama + ' (ID: #' + id + '). Tindakan ini tidak dapat dibatalkan!',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus Data!',
                    cancelButtonText: 'Batal'
                }).then(function(result) {
                    if (result && (result.value || result.isConfirmed || result === true)) {
                        swal({
                            title: 'Menghapus Data...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            showConfirmButton: false,
                            onBeforeOpen: function() {
                                swal.showLoading();
                            },
                            didOpen: function() {
                                if (typeof swal.showLoading === 'function') {
                                    swal.showLoading();
                                }
                            }
                        });

                        $.ajax({
                            url: '{{ url("/log/kehadiran-pengajar/delete") }}/' + id,
                            type: 'POST',
                            success: function(res) {
                                swal.close();
                                if (res.status) {
                                    swal({
                                        title: 'Terhapus!',
                                        text: res.message,
                                        type: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    table.ajax.reload(null, false);
                                } else {
                                    swal('Gagal', res.message || 'Data tidak dapat dihapus', 'error');
                                }
                            },
                            error: function(xhr) {
                                swal.close();
                                var msg = 'Gagal menghapus data dari server.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    msg = xhr.responseJSON.message;
                                }
                                swal('Error', msg, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
