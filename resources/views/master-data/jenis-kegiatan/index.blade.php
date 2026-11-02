@extends('layouts.app')
@section('title', 'Jenis Kegiatan Kepanitiaan - E-Barokah')
@section('page_title')
<span class="text-danger font-weight-bold">Master</span> / Jenis Kegiatan Kepanitiaan
@endsection

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <style>
        table.dataTable.display tbody td, 
        table.dataTable.display tbody th, 
        table.dataTable.display thead th {
            border-left: none !important;
            border-right: none !important;
            padding: 1.2rem 1rem !important;
            vertical-align: middle;
        }
        table.dataTable.display thead th {
            border-top: none !important;
            border-bottom: 1px solid #eaeaea !important;
            font-weight: 700;
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
        
        .btn-tambah-kegiatan {
            background-color: #d848c9 !important; 
            color: white !important; 
            border-radius: 20px; 
            padding: 8px 20px;
            border: none;
        }
        .btn-edit-kegiatan {
            border-radius: 20px;
            padding: 5px 15px;
        }
        .btn-delete-kegiatan {
            border-radius: 20px;
            padding: 5px 15px;
            margin-top: 5px;
        }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                <h4 class="card-title">Daftar Jenis Kegiatan Kepanitiaan</h4>
                <div class="d-flex align-items-center" style="gap: 8px;">
                    <a href="{{ url('/master-data/barokah-kepanitiaan') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 20px; padding: 7px 18px;">
                        <i class="fas fa-coins me-1"></i> Kelola Barokah Panitia
                    </a>
                    <button type="button" class="btn btn-sm btn-tambah-kegiatan shadow-sm" data-bs-toggle="modal" data-bs-target="#modalKegiatan">
                        <i class="fas fa-plus me-1"></i> Tambah Kegiatan
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-kegiatan" class="display" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kegiatan</th>
                                <th>Tingkat / Jenjang</th>
                                <th class="text-center">Nominal</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                            <tr class="filters" style="background-color: #f8f9fa;">
                                <th></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Kegiatan" style="border-radius: 8px;"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Tingkat" style="border-radius: 8px;"></th>
                                <th></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Keterangan" style="border-radius: 8px;"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kegiatan as $k)
                            <tr>
                                <td><strong>{{ $loop->iteration }}</strong></td>
                                <td>
                                    <strong class="text-dark">{{ $k->nama_kegiatan }}</strong>
                                </td>
                                <td>
                                    @php
                                        $badgeTingkat = 'badge-info';
                                        $labelTingkat = 'PESANTREN';
                                        if ($k->tingkat === 'dikdasmen') {
                                            $badgeTingkat = 'badge-success';
                                            $labelTingkat = 'DIKDASMEN';
                                        } elseif ($k->tingkat === 'dikti') {
                                            $badgeTingkat = 'badge-primary';
                                            $labelTingkat = 'DIKTI';
                                        } elseif ($k->tingkat === 'pesantren') {
                                            $badgeTingkat = 'badge-info';
                                            $labelTingkat = 'PESANTREN';
                                        } else {
                                            $labelTingkat = strtoupper($k->tingkat);
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeTingkat }}" style="border-radius: 12px; padding: 4px 10px; font-size: 11px;">
                                        {{ $labelTingkat }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light border text-dark font-weight-bold" style="font-size: 12px;">
                                        {{ $k->tarif_count }} Komponen
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ $k->keterangan ?: '-' }}</small></td>
                                <td>
                                    <div class="d-flex flex-column" style="width: max-content;">
                                        <button type="button" class="btn btn-outline-success btn-sm shadow-sm btn-edit-kegiatan"
                                            data-id="{{ $k->id_jenis_kegiatan }}"
                                            data-nama="{{ $k->nama_kegiatan }}"
                                            data-tingkat="{{ $k->tingkat }}"
                                            data-keterangan="{{ $k->keterangan }}">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm shadow-sm btn-delete-kegiatan" data-id="{{ $k->id_jenis_kegiatan }}" data-nama="{{ $k->nama_kegiatan }}">
                                            <i class="fas fa-trash-alt me-1"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Tambah / Edit Kegiatan -->
        <div class="modal fade" id="modalKegiatan">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Form Jenis Kegiatan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="formKegiatan">
                        <input type="hidden" id="inputId">
                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label class="form-label">Nama Kegiatan Kepanitiaan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="inputNama" placeholder="Contoh: Wisuda Bersama, Ujian Semester, dll." required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Tingkat / Jenjang <span class="text-danger">*</span></label>
                                <select class="form-control" id="inputTingkat" required>
                                    <option value="dikdasmen">DIKDASMEN</option>
                                    <option value="dikti">DIKTI</option>
                                    <option value="pesantren">PESANTREN</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label">Keterangan <span class="text-muted">(Opsional)</span></label>
                                <textarea class="form-control" id="inputKeterangan" rows="3" placeholder="Deskripsi singkat jenis kepanitiaan ini..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="btnSubmitKegiatan">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            var table = $('#table-kegiatan').DataTable({
                orderCellsTop: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>' 
                    },
                    emptyTable: "Belum ada data jenis kegiatan kepanitiaan",
                    zeroRecords: "Tidak ditemukan data yang sesuai"
                },
                initComplete: function () {
                    var api = this.api();
                    api.columns().every(function (i) {
                        var column = this;
                        $('input', $('.filters th').eq(i)).on('keyup change clear', function () {
                            if (column.search() !== this.value) {
                                column.search(this.value).draw();
                            }
                        });
                    });
                }
            });

            // Tombol Tambah
            $('.btn-tambah-kegiatan').on('click', function() {
                $('#modalLabel').text('Tambah Jenis Kegiatan');
                $('#btnSubmitKegiatan').prop('disabled', false).html('Simpan');
                $('#formKegiatan')[0].reset();
                $('#inputId').val('');
                $('#inputTingkat').val('dikdasmen');
                $('#modalKegiatan').modal('show');
            });

            // Tombol Edit
            $('#table-kegiatan tbody').on('click', '.btn-edit-kegiatan', function() {
                $('#modalLabel').text('Edit Jenis Kegiatan');
                $('#btnSubmitKegiatan').prop('disabled', false).html('Perbarui');
                $('#inputId').val($(this).data('id'));
                $('#inputNama').val($(this).data('nama'));
                $('#inputTingkat').val($(this).data('tingkat'));
                $('#inputKeterangan').val($(this).data('keterangan'));
                $('#modalKegiatan').modal('show');
            });

            // Submit Form
            $('#formKegiatan').on('submit', function(e) {
                e.preventDefault();
                var id = $('#inputId').val();
                var url = id ? '{{ url("/master-data/jenis-kegiatan/update") }}/' + id : '{{ url("/master-data/jenis-kegiatan/store") }}';
                
                var data = {
                    _token: '{{ csrf_token() }}',
                    nama_kegiatan: $('#inputNama').val(),
                    tingkat: $('#inputTingkat').val(),
                    keterangan: $('#inputKeterangan').val()
                };

                $('#btnSubmitKegiatan').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#modalKegiatan').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(err) {
                        $('#btnSubmitKegiatan').prop('disabled', false).html(id ? 'Perbarui' : 'Simpan');
                        var msg = 'Terjadi kesalahan saat menyimpan data';
                        if (err.responseJSON) {
                            if (err.responseJSON.errors) {
                                msg = Object.values(err.responseJSON.errors).flat().join('<br>');
                            } else if (err.responseJSON.message) {
                                msg = err.responseJSON.message;
                            }
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: msg
                        });
                    }
                });
            });

            // Tombol Hapus
            $('#table-kegiatan tbody').on('click', '.btn-delete-kegiatan', function() {
                var id = $(this).data('id');
                var nama = $(this).data('nama');
                Swal.fire({
                    title: 'Hapus Kegiatan Ini?',
                    text: `Jenis kegiatan "${nama}" dan seluruh master barokah di bawahnya akan dihapus!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.value || result.isConfirmed) {
                        $.ajax({
                            url: '{{ url("/master-data/jenis-kegiatan/delete") }}/' + id,
                            type: 'POST',
                            data: { _token: '{{ csrf_token() }}' },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Terhapus!',
                                    text: res.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                var msg = 'Terjadi kesalahan saat menghapus data';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    msg = xhr.responseJSON.message;
                                }
                                Swal.fire('Gagal!', msg, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
@endsection
