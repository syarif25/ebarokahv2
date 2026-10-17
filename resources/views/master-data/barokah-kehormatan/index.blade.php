@extends('layouts.app')
@section('title', 'Barokah Kehormatan - E-Barokah')
@section('page_title')
<span class="text-danger font-weight-bold">Master</span> / Barokah Kehormatan
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
            padding: 1.5rem 1rem !important;
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
        
        .btn-tambah-bkehormatan {
            background-color: #d848c9 !important; 
            color: white !important; 
            border-radius: 20px; 
            padding: 8px 20px;
            border: none;
        }
        .btn-edit-bkehormatan {
            border-radius: 20px;
            padding: 5px 15px;
        }
        .btn-delete-bkehormatan {
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
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="card-title">Daftar Barokah Kehormatan</h4>
                <button type="button" class="btn btn-sm btn-tambah-bkehormatan shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBKehormatan">
                    <i class="fas fa-plus me-1"></i> Tambah Barokah
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-bkehormatan" class="display" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Masa Pengabdian (Tahun)</th>
                                <th>Nominal Kehormatan</th>
                                <th>Aksi</th>
                            </tr>
                            <tr class="filters" style="background-color: #f8f9fa;">
                                <th></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Pengabdian" style="border-radius: 8px;"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Nominal" style="border-radius: 8px;"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barokah as $b)
                            <tr>
                                <td><strong>{{ $loop->iteration }}</strong></td>
                                <td><strong>{{ $b->min_masa_pengabdian }} - {{ $b->max_masa_pengabdian }} Tahun</strong></td>
                                <td><strong>{{ number_format($b->nominal_kehormatan, 0, ',', '.') }}</strong></td>
                                <td>
                                    <div class="d-flex flex-column" style="width: max-content;">
                                        <button type="button" class="btn btn-outline-success btn-sm shadow-sm btn-edit-bkehormatan" data-bs-toggle="modal" data-bs-target="#modalBKehormatan"
                                            data-id="{{ $b->id_bkehormatan }}"
                                            data-min="{{ $b->min_masa_pengabdian }}"
                                            data-max="{{ $b->max_masa_pengabdian }}"
                                            data-nominal="{{ $b->nominal_kehormatan }}">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm shadow-sm btn-delete-bkehormatan" data-id="{{ $b->id_bkehormatan }}">
                                            <i class="fas fa-trash-alt me-1"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data barokah kehormatan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modalBKehormatan">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Form Barokah Kehormatan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="formBKehormatan">
                        <input type="hidden" id="inputId">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Minimal Masa Pengabdian (Tahun) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="inputMin" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Maksimal Masa Pengabdian (Tahun) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="inputMax" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nominal (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="inputNominal" required>
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
    </div>
</div>

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            var table = $('#table-bkehormatan').DataTable({
                orderCellsTop: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>' 
                    }
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

            $('.btn-tambah-bkehormatan').on('click', function() {
                $('#modalLabel').text('Tambah Barokah Kehormatan');
                $('#formBKehormatan')[0].reset();
                $('#inputId').val('');
            });

            $('#table-bkehormatan tbody').on('click', '.btn-edit-bkehormatan', function() {
                $('#modalLabel').text('Edit Barokah Kehormatan');
                $('#inputId').val($(this).data('id'));
                $('#inputMin').val($(this).data('min'));
                $('#inputMax').val($(this).data('max'));
                $('#inputNominal').val($(this).data('nominal'));
            });

            $('#formBKehormatan').on('submit', function(e) {
                e.preventDefault();
                var id = $('#inputId').val();
                var url = id ? '{{ url("/master-data/barokah-kehormatan/update") }}/' + id : '{{ url("/master-data/barokah-kehormatan/store") }}';
                
                var data = {
                    _token: '{{ csrf_token() }}',
                    min_masa_pengabdian: $('#inputMin').val(),
                    max_masa_pengabdian: $('#inputMax').val(),
                    nominal_kehormatan: $('#inputNominal').val()
                };

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        $('#modalBKehormatan').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Tersimpan!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(err) {
                        Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
                    }
                });
            });

            $('#table-bkehormatan tbody').on('click', '.btn-delete-bkehormatan', function() {
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
                            url: '{{ url("/master-data/barokah-kehormatan/delete") }}/' + id,
                            type: 'POST',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(res) {
                                Swal.fire('Terhapus!', res.message, 'success').then(() => {
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
        });
    </script>
@endpush
@endsection
