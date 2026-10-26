@extends('layouts.app')

@section('title', 'Log Kehadiran Lembaga - E-Barokah')

@section('page_title')
<span class="text-danger font-weight-bold">Log</span> / Kehadiran Lembaga
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

        /* Badges Kategori */
        .badge-kategori-struktural {
            background-color: #7c3aed !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            padding: 6px 12px !important;
            border-radius: 8px !important;
        }
        .badge-kategori-pengajar {
            background-color: #0284c7 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            padding: 6px 12px !important;
            border-radius: 8px !important;
        }
        .badge-kategori-satpam {
            background-color: #d97706 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            padding: 6px 12px !important;
            border-radius: 8px !important;
        }
        .badge-kategori-default {
            background-color: #64748b !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 12px !important;
            padding: 6px 12px !important;
            border-radius: 8px !important;
        }

        /* Badges Status */
        .badge-status-selesai {
            background-color: #dcfce7 !important;
            color: #15803d !important;
            border: 1px solid #86efac !important;
            font-weight: 700 !important;
            padding: 6px 10px !important;
            border-radius: 8px !important;
        }
        .badge-status-acc {
            background-color: #eff6ff !important;
            color: #1d4ed8 !important;
            border: 1px solid #93c5fd !important;
            font-weight: 700 !important;
            padding: 6px 10px !important;
            border-radius: 8px !important;
        }
        .badge-status-terkirim {
            background-color: #fef3c7 !important;
            color: #b45309 !important;
            border: 1px solid #fde68a !important;
            font-weight: 700 !important;
            padding: 6px 10px !important;
            border-radius: 8px !important;
        }
        .badge-status-belum {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #cbd5e1 !important;
            font-weight: 700 !important;
            padding: 6px 10px !important;
            border-radius: 8px !important;
        }

        /* Clean Table */
        table.dataTable.display tbody td {
            padding: 0.9rem 1rem !important;
            vertical-align: middle;
        }
        table.dataTable.display thead th {
            border-top: none !important;
            border-bottom: 2px solid #e2e8f0 !important;
            font-weight: 700 !important;
            font-size: 0.8rem !important;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #334155 !important;
            background-color: #f8fafc;
        }
        table.dataTable.display tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* Action Buttons */
        .btn-add-record {
            background-color: #00a15d !important;
            border-color: #00a15d !important;
            color: #ffffff !important;
            font-weight: 600;
            border-radius: 10px;
            padding: 9px 20px;
            transition: all 0.2s ease;
        }
        .btn-add-record:hover {
            background-color: #00874e !important;
            border-color: #00874e !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 161, 93, 0.25);
        }
        .btn-reset-filter {
            background-color: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #475569;
            font-weight: 600;
            border-radius: 10px;
            padding: 9px 16px;
        }
        .btn-reset-filter:hover {
            background-color: #e2e8f0;
            color: #1e293b;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row page-titles mx-0 mb-4">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="text-primary font-weight-bold" style="color: #00a15d !important;">Log Kehadiran Lembaga</h4>
                <p class="mb-0 text-muted">Manajemen & CRUD data tabel riwayat kehadiran lembaga</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)" class="text-muted">Log</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)" style="color: #ff5e5e !important; font-weight: 600;">Kehadiran Lembaga</a></li>
            </ol>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="filter-card">
        <div class="row align-items-end g-3">
            <div class="col-lg-3 col-md-6">
                <label class="filter-label">
                    <i class="fas fa-building text-primary"></i> Lembaga
                </label>
                <select class="form-control select2-filter" id="filter_lembaga">
                    <option value="">-- Semua Lembaga --</option>
                    @foreach($lembagaList as $lem)
                        <option value="{{ $lem->id_lembaga }}">{{ $lem->nama_lembaga }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-3">
                <label class="filter-label">
                    <i class="fas fa-tags text-primary"></i> Kategori
                </label>
                <select class="form-control select2-filter" id="filter_kategori">
                    <option value="">-- Semua --</option>
                    <option value="Struktural">Struktural</option>
                    <option value="Pengajar">Pengajar</option>
                    <option value="Satpam">Satpam</option>
                </select>
            </div>
            <div class="col-lg-2 col-md-3">
                <label class="filter-label">
                    <i class="far fa-calendar-alt text-primary"></i> Bulan
                </label>
                <select class="form-control select2-filter" id="filter_bulan">
                    <option value="">-- Semua Bulan --</option>
                    @foreach($bulanList as $bln)
                        <option value="{{ $bln }}">{{ $bln }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-4">
                <label class="filter-label">
                    <i class="far fa-calendar text-primary"></i> Tahun
                </label>
                <select class="form-control select2-filter" id="filter_tahun">
                    <option value="">-- Semua Tahun --</option>
                    @foreach($tahunList as $thn)
                        <option value="{{ $thn }}">{{ $thn }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-3 col-md-8 text-md-end d-flex gap-2 justify-content-md-end mt-3 mt-lg-0">
                <button type="button" id="btn_reset_filter" class="btn btn-reset-filter flex-fill flex-md-grow-0 d-inline-flex align-items-center justify-content-center gap-1">
                    <i class="fas fa-undo"></i> Reset
                </button>
                <button type="button" class="btn btn-add-record flex-fill flex-md-grow-0 d-inline-flex align-items-center justify-content-center gap-2" onclick="addData()">
                    <i class="fas fa-plus"></i> Tambah Data
                </button>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 16px;">
                <div class="card-header border-0 pb-0 pt-4 px-4 bg-transparent d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title font-weight-bold text-dark mb-1">Daftar Kehadiran Lembaga</h4>
                        <p class="text-muted fs-13 mb-0">Kelola baris data kehadiran lembaga, status pengajuan, nominal, dan berkas lampiran.</p>
                    </div>
                </div>
                <div class="card-body px-4 pt-3">
                    <div class="table-responsive">
                        <table id="tabel_kehadiran_lembaga" class="table display responsive nowrap w-100" style="min-width: 1000px">
                            <thead>
                                <tr>
                                    <th style="width: 45px;" class="text-center">No</th>
                                    <th style="width: 65px;" class="text-center">ID</th>
                                    <th>Nama Lembaga</th>
                                    <th class="text-center">Kategori</th>
                                    <th class="text-center">Bulan</th>
                                    <th class="text-center">Tahun</th>
                                    <th class="text-end">Jumlah Total</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="width: 70px;">File</th>
                                    <th class="text-center" style="width: 90px;">Aksi</th>
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
</div>

<!-- Modal Form CRUD -->
<div class="modal fade" id="modal_form" tabindex="-1" aria-labelledby="modalFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-dark" id="modalFormLabel">Form Kehadiran Lembaga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form_kehadiran_lembaga" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id_kehadiran_lembaga" id="form_id_kehadiran_lembaga">
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold fs-13">Lembaga</label>
                            <select name="id_lembaga" id="form_id_lembaga" class="form-control select2-modal" style="width: 100%;">
                                <option value="">-- Pilih Lembaga (Opsional jika Satpam) --</option>
                                @foreach($lembagaList as $lem)
                                    <option value="{{ $lem->id_lembaga }}">{{ $lem->nama_lembaga }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold fs-13">Kategori <span class="text-danger">*</span></label>
                            <select name="kategori" id="form_kategori" class="form-control" required style="border-radius: 10px; height: 42px;">
                                <option value="Struktural">Struktural</option>
                                <option value="Pengajar">Pengajar</option>
                                <option value="Satpam">Satpam</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold fs-13">Bulan <span class="text-danger">*</span></label>
                            <select name="bulan" id="form_bulan" class="form-control select2-modal" required style="width: 100%;">
                                @foreach($bulanList as $bln)
                                    <option value="{{ $bln }}">{{ $bln }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold fs-13">Tahun <span class="text-danger">*</span></label>
                            <input type="text" name="tahun" id="form_tahun" class="form-control" placeholder="Contoh: 2026 atau 2025/2026" required style="border-radius: 10px; height: 42px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold fs-13">Status <span class="text-danger">*</span></label>
                            <select name="status" id="form_status" class="form-control" required style="border-radius: 10px; height: 42px;">
                                <option value="Belum">Belum</option>
                                <option value="Sudah">Sudah</option>
                                <option value="Terkirim">Terkirim</option>
                                <option value="acc">acc</option>
                                <option value="selesai">selesai</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold fs-13">Jumlah Total (Rp)</label>
                            <input type="text" name="jumlah_total" id="form_jumlah_total" class="form-control rupiah-input" placeholder="0" style="border-radius: 10px; height: 42px;">
                        </div>
                        <div class="col-12">
                            <label class="form-label font-weight-bold fs-13">Catatan Umum Pimpinan</label>
                            <textarea name="catatan_umum_pimpinan" id="form_catatan_umum_pimpinan" class="form-control" rows="2" placeholder="Catatan opsional..." style="border-radius: 10px;"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label font-weight-bold fs-13">Upload Berkas Lampiran (PDF / Gambar)</label>
                            <input type="file" name="file_upload" id="form_file_upload" class="form-control" accept=".pdf,image/*" style="border-radius: 10px;">
                            <small class="text-muted" id="file_current_preview"></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                    <button type="submit" id="btn_submit_form" class="btn btn-success d-inline-flex align-items-center gap-2" style="border-radius: 10px; background-color: #00a15d; border-color: #00a15d;">
                        <i class="fas fa-save"></i> Simpan Data
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

    <script type="text/javascript">
        var table;
        var isEdit = false;

        $(document).ready(function() {
            $.fn.dataTable.ext.errMode = 'none';

            // Select2 Filter
            $('.select2-filter').select2({
                minimumResultsForSearch: 10,
                width: '100%'
            });

            // Select2 Modal
            $('.select2-modal').select2({
                dropdownParent: $('#modal_form'),
                width: '100%'
            });

            // Format Input Rupiah
            $('.rupiah-input').on('keyup', function() {
                var val = $(this).val().replace(/[^0-9]/g, '');
                $(this).val(val ? parseInt(val, 10).toLocaleString('id-ID') : '');
            });

            // Inisialisasi DataTable
            table = $('#tabel_kehadiran_lembaga').DataTable({
                processing: true,
                serverSide: true,
                order: [[1, 'desc']],
                ajax: {
                    url: "{{ url('/log/kehadiran-lembaga/data') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        d.filter_lembaga = $('#filter_lembaga').val();
                        d.filter_kategori = $('#filter_kategori').val();
                        d.filter_bulan = $('#filter_bulan').val();
                        d.filter_tahun = $('#filter_tahun').val();
                    }
                },
                columns: [
                    { data: 'no', name: 'no', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'id_kehadiran_lembaga', name: 'kl.id_kehadiran_lembaga', className: 'text-center font-weight-bold' },
                    { data: 'nama_lembaga', name: 'l.nama_lembaga' },
                    { data: 'kategori', name: 'kl.kategori', className: 'text-center' },
                    { data: 'bulan', name: 'kl.bulan', className: 'text-center' },
                    { data: 'tahun', name: 'kl.tahun', className: 'text-center' },
                    { data: 'jumlah_total', name: 'kl.jumlah_total', className: 'text-end' },
                    { data: 'status', name: 'kl.status', className: 'text-center' },
                    { data: 'file', name: 'file', orderable: false, searchable: false, className: 'text-center' },
                    { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center' }
                ],
                language: {
                    search: "Search:",
                    searchPlaceholder: "Cari ID, lembaga, status...",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "Showing 0 to 0 of 0 entries",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    emptyTable: "Tidak ada data di tabel",
                    processing: '<div class="spinner-border text-success" role="status"><span class="sr-only">Loading...</span></div>',
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>' 
                    }
                }
            });

            // Filter on change
            $('#filter_lembaga, #filter_kategori, #filter_bulan, #filter_tahun').on('change', function() {
                table.ajax.reload();
            });

            // Reset Filter
            $('#btn_reset_filter').on('click', function() {
                $('#filter_lembaga').val('').trigger('change.select2');
                $('#filter_kategori').val('').trigger('change.select2');
                $('#filter_bulan').val('').trigger('change.select2');
                $('#filter_tahun').val('').trigger('change.select2');
                table.search('').draw();
                table.ajax.reload();
            });

            // Submit Form CRUD
            $('#form_kehadiran_lembaga').on('submit', function(e) {
                e.preventDefault();
                var id = $('#form_id_kehadiran_lembaga').val();
                var url = isEdit 
                    ? "{{ url('/log/kehadiran-lembaga/update') }}/" + id 
                    : "{{ url('/log/kehadiran-lembaga/store') }}";

                var formData = new FormData(this);
                $('#btn_submit_form').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');

                $.ajax({
                    url: url,
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        $('#btn_submit_form').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Data');
                        if (res.status) {
                            $('#modal_form').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: res.message || 'Terjadi kesalahan sistem'
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#btn_submit_form').prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Data');
                        var msg = 'Terjadi kesalahan pada server!';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: msg
                        });
                    }
                });
            });
        });

        function addData() {
            isEdit = false;
            $('#modalFormLabel').text('Tambah Data Kehadiran Lembaga');
            $('#form_kehadiran_lembaga')[0].reset();
            $('#form_id_kehadiran_lembaga').val('');
            $('#form_id_lembaga').val('').trigger('change.select2');
            $('#form_bulan').val('{{ date("F") }}').trigger('change.select2');
            $('#form_tahun').val('{{ date("Y") }}');
            $('#form_kategori').val('Struktural');
            $('#form_status').val('Belum');
            $('#file_current_preview').html('');
            $('#modal_form').modal('show');
        }

        function editData(id) {
            isEdit = true;
            $('#modalFormLabel').text('Edit Data Kehadiran Lembaga #' + id);
            $('#form_kehadiran_lembaga')[0].reset();
            $('#file_current_preview').html('');

            $.ajax({
                url: "{{ url('/log/kehadiran-lembaga/show') }}/" + id,
                type: "GET",
                success: function(res) {
                    if (res.status && res.data) {
                        var d = res.data;
                        $('#form_id_kehadiran_lembaga').val(d.id_kehadiran_lembaga);
                        $('#form_id_lembaga').val(d.id_lembaga || '').trigger('change.select2');
                        $('#form_kategori').val(d.kategori);
                        $('#form_bulan').val(d.bulan).trigger('change.select2');
                        $('#form_tahun').val(d.tahun);
                        $('#form_status').val(d.status);
                        $('#form_jumlah_total').val(d.jumlah_total ? parseInt(d.jumlah_total, 10).toLocaleString('id-ID') : '0');
                        $('#form_catatan_umum_pimpinan').val(d.catatan_umum_pimpinan || '');

                        if (d.file) {
                            $('#file_current_preview').html('File saat ini: <a href="{{ asset("upload") }}/' + d.file + '" target="_blank" class="fw-bold text-primary">' + d.file + '</a>');
                        } else {
                            $('#file_current_preview').html('Belum ada file lampiran.');
                        }

                        $('#modal_form').modal('show');
                    } else {
                        Swal.fire('Error', 'Gagal memuat data!', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Gagal mengambil data dari server!', 'error');
                }
            });
        }

        function deleteData(id, nama) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Hapus data kehadiran #' + id + ' (' + nama + ')? Tindakan ini tidak dapat dibatalkan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/log/kehadiran-lembaga/delete') }}/" + id,
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            if (res.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire('Gagal!', res.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            var errMsg = 'Gagal menghapus data dari server.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errMsg = xhr.responseJSON.message;
                            }
                            Swal.fire('Error!', errMsg, 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush
