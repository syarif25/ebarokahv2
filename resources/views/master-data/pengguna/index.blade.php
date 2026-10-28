@extends('layouts.app')

@section('title', 'Pengguna - E-Barokah')
@section('page_title', 'Master Data / Pengguna')

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet">
    <style>
        /* Custom Table Styling as requested */
        table.dataTable.display tbody td, 
        table.dataTable.display tbody th, 
        table.dataTable.display thead th {
            border-left: none !important;
            border-right: none !important;
            padding: 1.25rem 1rem !important; /* more padding like image */
            vertical-align: middle;
        }
        table.dataTable.display thead th {
            border-top: none !important;
            border-bottom: 1px solid #f0f0f0 !important;
            text-transform: uppercase;
            font-weight: 800;
            color: #333;
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
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Daftar Pengguna</h4>
                <button type="button" class="btn btn-primary btn-sm btn-tambah-pengguna" data-bs-toggle="modal" data-bs-target="#modalPengguna">
                    <i class="fas fa-plus me-1"></i> Tambah Pengguna
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-pengguna" class="display" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th style="width:50px;"><strong>#</strong></th>
                                <th><strong>USERNAME</strong></th>
                                <th><strong>JABATAN</strong></th>
                                <th><strong>LEMBAGA</strong></th>
                                <th><strong>NO HP</strong></th>
                                <th><strong>AKSI</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengguna as $p)
                            <tr>
                                <td><strong>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</strong></td>
                                <td>{{ $p->username }}</td>
                                <td>{{ $p->jabatan }}</td>
                                <td>{{ $p->lembagaData ? $p->lembagaData->nama_lembaga : '-' }}</td>
                                <td>{{ $p->no_hp }}</td>
                                <td>
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-outline-danger btn-sm shadow-sm btn-edit-pengguna" style="border-radius: 20px; padding: 5px 15px;" data-bs-toggle="modal" data-bs-target="#modalPengguna"
                                            data-id="{{ $p->id_pengguna }}"
                                            data-username="{{ $p->username }}"
                                            data-jabatan="{{ $p->jabatan }}"
                                            data-lembaga="{{ $p->lembaga }}"
                                            data-nohp="{{ $p->no_hp }}">
                                            <i class="fas fa-pencil-alt me-1"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-outline-warning btn-sm shadow-sm btn-delete-pengguna" style="border-radius: 20px; padding: 5px 15px; margin-left: 5px;" data-id="{{ $p->id_pengguna }}">
                                            <i class="fas fa-trash me-1"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data pengguna</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pengguna -->
<div class="modal fade" id="modalPengguna">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPengguna">
                <input type="hidden" id="inputIdPengguna">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="inputUsername" placeholder="Masukkan Username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                        <select class="form-control" id="inputJabatan" required>
                            <option value="">-- Pilih Jabatan --</option>
                            <option value="SuperAdmin">Super Admin</option>
                            <option value="SDM">SDM</option>
                            <option value="Evaluasi">Evaluasi</option>
                            <option value="Kasir">Kasir</option>
                            <option value="AdminLembaga">Admin Lembaga</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lembaga <span class="text-danger">*</span></label>
                        <select class="form-control" id="inputLembaga" style="width: 100%;" required>
                            <option value="">-- Cari Lembaga --</option>
                            @foreach($lembaga_list as $l)
                                <option value="{{ $l->id_lembaga }}">{{ $l->nama_lembaga }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No HP <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="inputNoHp" placeholder="Contoh: 08123456789" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" id="inputPassword" placeholder="Isi jika ingin mengubah/membuat password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
    <script>
        (function($) {
            "use strict"
            $('#table-pengguna').DataTable({
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>' 
                    }
                }
            });

            // Init Select2 for Lembaga
            $('#inputLembaga').select2({
                dropdownParent: $('#modalPengguna'),
                placeholder: "-- Cari Lembaga --",
                allowClear: true
            });

            $('#formPengguna').on('submit', function(e) {
                e.preventDefault();
                
                var id = $('#inputIdPengguna').val();
                var url = id ? '{{ url("/master-data/pengguna/update") }}/' + id : '{{ url("/master-data/pengguna/store") }}';
                
                var data = {
                    _token: '{{ csrf_token() }}',
                    username: $('#inputUsername').val(),
                    jabatan: $('#inputJabatan').val(),
                    lembaga: $('#inputLembaga').val(),
                    no_hp: $('#inputNoHp').val(),
                    password: $('#inputPassword').val()
                };

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        Swal.fire(
                            'Tersimpan!',
                            'Data pengguna berhasil disimpan ke database.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    },
                    error: function(err) {
                        var msg = 'Terjadi kesalahan saat menyimpan data';
                        if (err.responseJSON && err.responseJSON.errors) {
                            var errors = err.responseJSON.errors;
                            var errList = [];
                            for (var k in errors) {
                                errList.push(errors[k].join(', '));
                            }
                            msg = errList.join('<br>');
                        } else if (err.responseJSON && err.responseJSON.message) {
                            msg = err.responseJSON.message;
                        }
                        Swal.fire('Error', msg, 'error');
                    }
                });
            });

            $('#table-pengguna tbody').on('click', '.btn-delete-pengguna', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.value || result.isConfirmed) {
                        $.ajax({
                            url: '{{ url("/master-data/pengguna/delete") }}/' + id,
                            type: 'POST',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(res) {
                                Swal.fire('Terhapus!', 'Data berhasil dihapus dari database.', 'success').then(() => {
                                    location.reload();
                                });
                            },
                            error: function() {
                                Swal.fire('Error', 'Terjadi kesalahan saat menghapus data', 'error');
                            }
                        });
                    }
                });
            });

            $('#table-pengguna tbody').on('click', '.btn-edit-pengguna', function() {
                var id = $(this).data('id');
                var username = $(this).data('username');
                var jabatan = $(this).data('jabatan');
                var lembaga = $(this).data('lembaga');
                var nohp = $(this).data('nohp');

                $('.modal-title').text('Edit Pengguna');
                $('#inputIdPengguna').val(id);
                $('#inputUsername').val(username);
                
                // Normalisasi string jabatan
                jabatan = $.trim(jabatan);
                var cleanJab = jabatan.replace(/\s+/g, '');
                
                // Map ke opsi yang sesuai
                var matchedVal = '';
                $('#inputJabatan option').each(function() {
                    var optVal = $(this).val();
                    if (optVal && (optVal === jabatan || optVal.toLowerCase() === cleanJab.toLowerCase())) {
                        matchedVal = optVal;
                        return false;
                    }
                });

                if (matchedVal) {
                    $('#inputJabatan').val(matchedVal).trigger('change');
                } else if (jabatan !== '') {
                    $('#inputJabatan').append(new Option(jabatan, cleanJab)).val(cleanJab).trigger('change');
                }
                
                $('#inputLembaga').val(lembaga).trigger('change');
                $('#inputNoHp').val(nohp);
                $('#inputPassword').val('');
            });

            $('.btn-tambah-pengguna').on('click', function() {
                $('.modal-title').text('Form Pengguna');
                $('#formPengguna')[0].reset();
                $('#inputIdPengguna').val('');
                $('#inputLembaga').val('').trigger('change');
                $('#inputJabatan').val('').trigger('change');
            });
        })(jQuery);
    </script>
@endpush
