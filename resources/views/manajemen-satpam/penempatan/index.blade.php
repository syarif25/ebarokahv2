@extends('layouts.app')
@section('title', 'Penempatan Satpam - E-Barokah')
@section('page_title')
<span class="text-danger font-weight-bold">Manajemen Satpam</span> / Daftar Penempatan Satpam
@endsection

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Custom Table Styling matching other modules */
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
            border-bottom: 1px solid #eaeaea !important;
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
        table.dataTable {
            border-bottom: none !important;
        }
        table.dataTable.no-footer {
            border-bottom: none !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.2em 0.5em !important;
        }

        /* Stat Cards */
        .stat-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            padding: 15px 20px;
            background: #fff;
            height: 100%;
        }
        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .stat-icon-blue { background: #e8f3ff; color: #0d6efd; }
        .stat-icon-green { background: #e0f8e9; color: #00a15d; }
        .stat-icon-purple { background: #f3e8ff; color: #8b5cf6; }
        .stat-icon-red { background: #fee2e2; color: #ef4444; }

        .stat-text {
            display: flex;
            flex-direction: column;
        }
        .stat-text span { font-size: 11px; color: #888; font-weight: 600; text-transform: uppercase; }
        .stat-text h3 { font-size: 24px; font-weight: 700; margin: 0; color: #333; }

        /* Action & Badges */
        .btn-tambah-penempatan {
            background-color: #d848c9 !important; 
            color: white !important; 
            border-radius: 20px; 
            padding: 8px 20px;
            border: none;
            font-weight: 600;
        }
        .btn-tambah-penempatan:hover {
            background-color: #c13bb3 !important;
            color: white !important;
        }

        .btn-edit-satpam {
            background-color: #00a15d !important;
            color: #fff !important;
            border-radius: 20px !important;
            padding: 5px 20px !important;
            border: none;
            font-size: 12px;
            font-weight: 600;
        }
        .btn-edit-satpam:hover {
            background-color: #00854d !important;
            color: #fff !important;
        }

        .badge-status {
            background-color: #00a15d;
            color: #fff;
            border-radius: 20px;
            padding: 5px 15px;
            font-size: 11px;
            font-weight: 600;
        }

        /* Clean Modal Styling */
        .form-label {
            font-weight: 600;
            color: #333 !important;
            margin-bottom: 6px;
            font-size: 0.85rem;
        }
        .form-control {
            border: 1px solid #d7dae3 !important;
            border-radius: 8px !important;
            height: 42px;
            font-size: 0.875rem;
            background-color: #fff;
        }
        .form-control:focus {
            border-color: #00a15d !important;
            box-shadow: 0 0 0 0.15rem rgba(0, 161, 93, 0.15) !important;
        }
        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid #d7dae3 !important;
            border-radius: 8px !important;
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #00a15d !important;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Stat Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
            <div class="stat-card">
                <div class="stat-icon stat-icon-blue">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="stat-text">
                    <span>TOTAL PERSONIL</span>
                    <h3>{{ $totalSatpam }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
            <div class="stat-card">
                <div class="stat-icon stat-icon-green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-text">
                    <span>SATPAM AKTIF</span>
                    <h3>{{ $totalAktif }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
            <div class="stat-card">
                <div class="stat-icon stat-icon-purple">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-text">
                    <span>KOMANDAN REGU</span>
                    <h3>{{ $totalDanru }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-3 mb-xl-0">
            <div class="stat-card">
                <div class="stat-icon stat-icon-red">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-text">
                    <span>TIDAK AKTIF</span>
                    <h3>{{ $totalTidakAktif }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap pt-3 px-4">
                    <h4 class="card-title font-weight-bold text-dark mb-0">Daftar Penempatan Satpam</h4>
                    <button type="button" class="btn btn-sm btn-tambah-penempatan shadow-sm" onclick="addSatpam()">
                        <i class="fas fa-plus me-1 mr-1"></i> Tambah Penempatan
                    </button>
                </div>
                <div class="card-body px-4">
                    <div class="table-responsive">
                        <table id="tableSatpam" class="display table-hover w-100" style="min-width: 845px;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">No</th>
                                    <th>Nama Lengkap</th>
                                    <th>Alamat</th>
                                    <th class="text-center">Masa Pengabdian</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="width: 100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Clean Modal Penempatan Satpam -->
<div class="modal fade" id="modalSatpam" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <div class="modal-header border-bottom-0 pb-0 mt-3 mx-3">
                <h5 class="modal-title font-weight-bold text-dark" id="modalSatpamTitle">Form Penempatan Satpam</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 1.5rem; border: none; background: transparent;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formSatpam" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id_satpam" id="id_satpam">
                <div class="modal-body px-4 pt-3 pb-2">
                    <!-- BARIS 1: NIK & Transport -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">NIK / Nama Umana <span class="text-danger">*</span></label>
                            <select class="form-control" name="nik" id="nik" style="width: 100%;">
                                <option value=""></option>
                                @foreach($umanas as $u)
                                    <option value="{{ $u->nik }}">
                                        {{ $u->nik }} - {{ $u->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger error-text error-nik"></small>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Transport / Kehadiran <span class="text-danger">*</span></label>
                            <select class="form-control" name="transport" id="transport" style="width: 100%;">
                                <option value=""></option>
                                @foreach($transports as $t)
                                    <option value="{{ $t->id_transport }}">
                                        {{ $t->nama_transport }} ({{ $t->kategori_transport ?? 'Transport' }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger error-text error-transport"></small>
                        </div>
                    </div>

                    <!-- BARIS 2: Tanggal Mulai & Tanggal Selesai -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Mulai Tugas <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tgl_mulai" id="tgl_mulai">
                            <small class="text-danger error-text error-tgl_mulai"></small>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Akhir Tugas <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tgl_selesai" id="tgl_selesai">
                            <small class="text-danger error-text error-tgl_selesai"></small>
                        </div>
                    </div>

                    <!-- BARIS 3: Posisi Danru & Status -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Posisi Danru</label>
                            <select class="form-control" name="is_danru" id="is_danru">
                                <option value="0">Tidak (Anggota)</option>
                                <option value="1">Ya (Komandan Regu)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Status Keaktifan</label>
                            <select class="form-control" name="status" id="status">
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>

                    <!-- BARIS 4: File SK -->
                    <div class="row mb-2">
                        <div class="col-12">
                            <label class="form-label">File SK (PDF Max 10MB)</label>
                            <input type="file" class="form-control" name="file_sk" id="file_sk" accept="application/pdf" style="padding: 7px 15px;">
                            <small class="text-danger error-text error-file_sk"></small>
                            <div id="skPreviewBox" class="mt-2" style="display: none;">
                                <a href="#" id="skPreviewLink" target="_blank" class="small text-primary font-weight-bold">
                                    <i class="fa fa-file-pdf mr-1"></i> Lihat Berkas SK Saat Ini
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="modal-footer border-top-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light btn-rounded px-4" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary btn-rounded px-4 font-weight-bold" id="btnSave">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    let tableSatpam;
    let saveMethod = 'add';

    $(document).ready(function() {
        // Init DataTable
        tableSatpam = $('#tableSatpam').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ url('/manajemen-satpam/penempatan') }}",
                type: 'GET'
            },
            columns: [
                { data: 'no', className: 'text-center' },
                { data: 'nama_lengkap' },
                { data: 'alamat' },
                { data: 'masa_pengabdian', className: 'text-center' },
                { data: 'status', className: 'text-center' },
                { data: 'aksi', className: 'text-center', orderable: false }
            ],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                zeroRecords: "Tidak ada data satpam ditemukan",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ data keseluruhan)",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Lanjut",
                    previous: "Sebelum"
                }
            }
        });

        // Init Select2
        $('#nik').select2({
            dropdownParent: $('#modalSatpam'),
            placeholder: 'Pilih NIK / Nama Umana',
            allowClear: true,
            width: '100%'
        });

        $('#transport').select2({
            dropdownParent: $('#modalSatpam'),
            placeholder: 'Pilih Transport / Kehadiran',
            allowClear: true,
            width: '100%'
        });

        // Submit form (Tambah / Edit)
        $('#formSatpam').on('submit', function(e) {
            e.preventDefault();
            $('.error-text').text('');

            const btn = $('#btnSave');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

            const formData = new FormData(this);
            let url = "{{ url('/manajemen-satpam/penempatan/store') }}";
            if (saveMethod === 'edit') {
                const id = $('#id_satpam').val();
                url = "{{ url('/manajemen-satpam/penempatan/update') }}/" + id;
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'JSON',
                success: function(res) {
                    btn.prop('disabled', false).html('Simpan');
                    if (res.status) {
                        $('#modalSatpam').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 1800,
                            showConfirmButton: false
                        });
                        tableSatpam.ajax.reload(null, false);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('Simpan');
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        $.each(errors, function(prefix, val) {
                            $('.error-' + prefix).text(val[0]);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal menyimpan data penempatan satpam.'
                        });
                    }
                }
            });
        });

        // Tombol Edit Satpam
        $(document).on('click', '.btn-edit-satpam', function() {
            const id = $(this).data('id');
            saveMethod = 'edit';
            $('#formSatpam')[0].reset();
            $('.error-text').text('');
            $('#modalSatpamTitle').text('Edit Penempatan Satpam');

            $.getJSON("{{ url('/manajemen-satpam/penempatan/edit') }}/" + id, function(res) {
                if (res && res.status) {
                    const d = res.data;
                    $('#id_satpam').val(d.id_satpam);
                    $('#nik').val(d.nik).trigger('change');
                    $('#transport').val(d.id_transport).trigger('change');
                    $('#tgl_mulai').val(d.tgl_mulai);
                    $('#tgl_selesai').val(d.tgl_selesai);
                    $('#is_danru').val(d.is_danru);
                    $('#status').val(d.status);

                    if (d.file_url) {
                        $('#skPreviewLink').attr('href', d.file_url);
                        $('#skPreviewBox').show();
                    } else {
                        $('#skPreviewBox').hide();
                    }

                    $('#modalSatpam').modal('show');
                } else {
                    Swal.fire('Error', 'Data satpam tidak ditemukan.', 'error');
                }
            });
        });
    });

    function addSatpam() {
        saveMethod = 'add';
        $('#formSatpam')[0].reset();
        $('#id_satpam').val('');
        $('#nik').val(null).trigger('change');
        $('#transport').val(null).trigger('change');
        $('#is_danru').val('0');
        $('#status').val('Aktif');
        $('#skPreviewBox').hide();
        $('.error-text').text('');
        $('#modalSatpamTitle').text('Form Penempatan Satpam');
        $('#modalSatpam').modal('show');
    }
</script>
@endpush
