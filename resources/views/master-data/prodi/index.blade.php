@extends('layouts.app')

@section('title', 'Master Data Prodi')
@section('page_title', 'Master / Master Prodi')

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
            padding: 1.25rem 1rem !important;
            vertical-align: middle;
        }
        table.dataTable.display thead th {
            border-top: none !important;
            border-bottom: 1px solid #eaeaea !important;
            font-weight: 700;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border: none !important;
            padding: 0.5rem 1rem !important;
            margin-left: 2px;
            border-radius: 8px !important;
            background: #f8f9fa !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary) !important;
            color: #fff !important;
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
                <h4 class="card-title">Daftar Program Studi</h4>
                <button type="button" class="btn btn-primary btn-sm" id="btn-tambah-prodi" data-bs-toggle="modal" data-bs-target="#modalProdi">
                    <i class="fas fa-plus me-1"></i> Tambah Prodi
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-prodi" class="display" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th style="width:50px;"><strong>No</strong></th>
                                <th><strong>Fakultas / Lembaga</strong></th>
                                <th><strong>Program Studi</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($prodi as $p)
                            <tr>
                                <td><strong>{{ $loop->iteration }}</strong></td>
                                <td>{{ $p->lembagaData ? $p->lembagaData->nama_lembaga : '-' }}</td>
                                <td>{{ $p->nama_prodi }}</td>
                                <td>
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-outline-danger btn-sm shadow-sm btn-edit-prodi" style="border-radius: 20px; padding: 5px 15px;" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalProdi"
                                            data-id="{{ $p->id_prodi }}"
                                            data-lembaga="{{ $p->id_lembaga }}"
                                            data-nama="{{ $p->nama_prodi }}">
                                            <i class="fas fa-pencil-alt me-1"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-outline-warning btn-sm shadow-sm btn-delete-prodi" style="border-radius: 20px; padding: 5px 15px; margin-left: 5px;" data-id="{{ $p->id_prodi }}">
                                            <i class="fas fa-trash me-1"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data prodi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalProdi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProdiLabel">Tambah Prodi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-prodi">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="id_prodi" id="id_prodi">
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Fakultas / Lembaga *</strong></label>
                        <select class="form-select form-control" name="id_lembaga" id="id_lembaga" required>
                            <option value="">Pilih Lembaga</option>
                            @foreach($lembaga_list as $lembaga)
                                <option value="{{ $lembaga->id_lembaga }}">{{ $lembaga->nama_lembaga }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Nama Prodi *</strong></label>
                        <input type="text" class="form-control" name="nama_prodi" id="nama_prodi" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-simpan">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}"></script>
<script>
    $(document).ready(function() {
        var table = $('#table-prodi').DataTable({
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>' 
                }
            }
        });

        $('#id_lembaga').select2({
            dropdownParent: $('#modalProdi'),
            width: '100%'
        });

        $('#btn-tambah-prodi').on('click', function() {
            $('#modalProdiLabel').text('Tambah Prodi');
            $('#form-prodi')[0].reset();
            $('#id_prodi').val('');
            $('#id_lembaga').val('').trigger('change');
        });

        $('#table-prodi tbody').on('click', '.btn-edit-prodi', function() {
            var id = $(this).data('id');
            var lembaga = $(this).data('lembaga');
            var nama = $(this).data('nama');

            $('#modalProdiLabel').text('Edit Prodi');
            $('#id_prodi').val(id);
            $('#id_lembaga').val(lembaga).trigger('change');
            $('#nama_prodi').val(nama);
        });

        $('#table-prodi tbody').on('click', '.btn-delete-prodi', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value || result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("/master-data/prodi/delete") }}/' + id,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if(response.success) {
                                Swal.fire('Terhapus!', response.message, 'success').then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', 'Terjadi kesalahan saat menghapus data: ' + xhr.responseText, 'error');
                        }
                    });
                }
            });
        });

        $('#form-prodi').on('submit', function(e) {
            e.preventDefault();
            var id = $('#id_prodi').val();
            var url = id ? '{{ url("/master-data/prodi/update") }}/' + id : '{{ url("/master-data/prodi/store") }}';
            
            $.ajax({
                url: url,
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if(response.success) {
                        $('#modalProdi').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Terjadi kesalahan, silakan coba lagi', 'error');
                }
            });
        });
    });
</script>
@endpush
@endsection
