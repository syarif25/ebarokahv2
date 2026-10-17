@extends('layouts.app')
@section('title', 'Ketentuan Barokah - E-Barokah')
@section('page_title')
<span class="text-danger font-weight-bold">Master</span> / Ketentuan Barokah
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

        .btn-tambah-ketentuan {
            background-color: #d848c9 !important; 
            color: white !important; 
            border-radius: 20px; 
            padding: 8px 20px;
            border: none;
        }
        .btn-edit-ketentuan {
            border-radius: 20px;
            padding: 5px 15px;
        }
        .btn-delete-ketentuan {
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
                <h4 class="card-title">Daftar Ketentuan Barokah</h4>
                <button type="button" class="btn btn-sm btn-tambah-ketentuan shadow-sm" data-bs-toggle="modal" data-bs-target="#modalKetentuan">
                    <i class="fas fa-plus me-1"></i> Tambah Ketentuan
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-ketentuan" class="display" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Jabatan</th>
                                <th>Kategori</th>
                                <th>Jumlah Barokah</th>
                                <th>Wajib Hadir</th>
                                <th>Aksi</th>
                            </tr>
                            <tr class="filters" style="background-color: #f8f9fa;">
                                <th></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Jabatan" style="border-radius: 8px;"></th>
                                <th>
                                    <select class="form-control form-control-sm" style="border-radius: 8px;">
                                        <option value="">Semua Kategori</option>
                                        <option value="Pengurus Pesantren">Pengurus Pesantren</option>
                                        <option value="DIKJAR">DIKJAR</option>
                                        <option value="DIKTI">DIKTI</option>
                                        <option value="UNIB">UNIB</option>
                                        <option value="Kelas Kota">Kelas Kota</option>
                                        <option value="Pengurus Yayasan/Perangkat Khusus">Pengurus Yayasan/Perangkat Khusus</option>
                                    </select>
                                </th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Jumlah" style="border-radius: 8px;"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Hadir" style="border-radius: 8px;"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ketentuan as $k)
                            <tr>
                                <td><strong>{{ $loop->iteration }}</strong></td>
                                <td><strong>{{ $k->nama_jabatan }}</strong></td>
                                <td><strong>{{ $k->kategori }}</strong></td>
                                <td><strong>{{ number_format($k->barokah, 0, ',', '.') }}</strong></td>
                                <td><strong>{{ $k->wajib_hadir }} hari/minggu</strong></td>
                                <td>
                                    <div class="d-flex flex-column" style="width: max-content;">
                                        <button type="button" class="btn btn-outline-success btn-sm shadow-sm btn-edit-ketentuan" data-bs-toggle="modal" data-bs-target="#modalKetentuan"
                                            data-id="{{ $k->id_ketentuan }}"
                                            data-nama="{{ $k->nama_jabatan }}"
                                            data-kategori="{{ $k->kategori }}"
                                            data-barokah="{{ $k->barokah }}"
                                            data-wajib="{{ $k->wajib_hadir }}">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm shadow-sm btn-delete-ketentuan" data-id="{{ $k->id_ketentuan }}">
                                            <i class="fas fa-trash-alt me-1"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data ketentuan barokah</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modalKetentuan">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Form Ketentuan Barokah</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="formKetentuan">
                        <input type="hidden" id="inputId">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="inputNama" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select class="form-control" id="inputKategori" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="Pengurus Pesantren">Pengurus Pesantren</option>
                                    <option value="DIKJAR">DIKJAR</option>
                                    <option value="DIKTI">DIKTI</option>
                                    <option value="UNIB">UNIB</option>
                                    <option value="Kelas Kota">Kelas Kota</option>
                                    <option value="Pengurus Yayasan/Perangkat Khusus">Pengurus Yayasan/Perangkat Khusus</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jumlah Barokah <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="inputBarokah" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Wajib Hadir (hari/minggu) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="inputWajib" required>
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
            var table = $('#table-ketentuan').DataTable({
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
                        $('input, select', $('#table-ketentuan thead tr.filters th').eq(i)).on('keyup change clear', function () {
                            if (column.search() !== this.value) {
                                column.search(this.value).draw();
                            }
                        });
                    });
                }
            });

            $('.btn-tambah-ketentuan').on('click', function() {
                $('#modalLabel').text('Tambah Ketentuan Barokah');
                $('#formKetentuan')[0].reset();
                $('#inputId').val('');
            });

            $('#table-ketentuan tbody').on('click', '.btn-edit-ketentuan', function() {
                $('#modalLabel').text('Edit Ketentuan Barokah');
                $('#inputId').val($(this).data('id'));
                $('#inputNama').val($(this).data('nama'));
                $('#inputKategori').val($(this).data('kategori'));
                $('#inputBarokah').val($(this).data('barokah'));
                $('#inputWajib').val($(this).data('wajib'));
            });

            $('#formKetentuan').on('submit', function(e) {
                e.preventDefault();
                var id = $('#inputId').val();
                var url = id ? '{{ url("/master-data/ketentuan-barokah/update") }}/' + id : '{{ url("/master-data/ketentuan-barokah/store") }}';
                
                var data = {
                    _token: '{{ csrf_token() }}',
                    nama_jabatan: $('#inputNama').val(),
                    kategori: $('#inputKategori').val(),
                    barokah: $('#inputBarokah').val(),
                    wajib_hadir: $('#inputWajib').val()
                };

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        $('#modalKetentuan').modal('hide');
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

            $('#table-ketentuan tbody').on('click', '.btn-delete-ketentuan', function() {
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
                            url: '{{ url("/master-data/ketentuan-barokah/delete") }}/' + id,
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
