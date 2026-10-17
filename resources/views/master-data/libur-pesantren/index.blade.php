@extends('layouts.app')
@section('title', 'Libur Pesantren - E-Barokah')
@section('page_title')
<span class="text-danger font-weight-bold">Master</span> / Libur Pesantren
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
        
        .btn-tambah-libur {
            background-color: #d848c9 !important; 
            color: white !important; 
            border-radius: 20px; 
            padding: 8px 20px;
            border: none;
        }
        .btn-edit-libur {
            border-radius: 20px;
            padding: 5px 15px;
        }
        .btn-delete-libur {
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
                <h4 class="card-title">Daftar Libur Pesantren</h4>
                <button type="button" class="btn btn-sm btn-tambah-libur shadow-sm" data-bs-toggle="modal" data-bs-target="#modalLibur">
                    <i class="fas fa-plus me-1"></i> Tambah Libur
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-libur" class="display" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Bulan</th>
                                <th>Tahun</th>
                                <th>Jumlah Hari</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                            <tr class="filters" style="background-color: #f8f9fa;">
                                <th></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Bulan" style="border-radius: 8px;"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Tahun" style="border-radius: 8px;"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Hari" style="border-radius: 8px;"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Keterangan" style="border-radius: 8px;"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($libur as $l)
                            <tr>
                                <td><strong>{{ $loop->iteration }}</strong></td>
                                <td><strong>{{ $l->bulan }}</strong></td>
                                <td><strong>{{ $l->tahun }}</strong></td>
                                <td><strong>{{ $l->jumlah_hari }} hari</strong></td>
                                <td><strong>{{ $l->keterangan }}</strong></td>
                                <td>
                                    <div class="d-flex flex-column" style="width: max-content;">
                                        <button type="button" class="btn btn-outline-success btn-sm shadow-sm btn-edit-libur" data-bs-toggle="modal" data-bs-target="#modalLibur"
                                            data-id="{{ $l->id_libur }}"
                                            data-bulan="{{ $l->bulan }}"
                                            data-tahun="{{ $l->tahun }}"
                                            data-hari="{{ $l->jumlah_hari }}"
                                            data-keterangan="{{ $l->keterangan }}">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm shadow-sm btn-delete-libur" data-id="{{ $l->id_libur }}">
                                            <i class="fas fa-trash-alt me-1"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data libur pesantren</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modalLibur">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Form Libur Pesantren</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="formLibur">
                        <input type="hidden" id="inputId">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Bulan <span class="text-danger">*</span></label>
                                <select class="form-control" id="inputBulan" required>
                                    <option value="">-- Pilih Bulan --</option>
                                    @php $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']; @endphp
                                    @foreach($months as $m)
                                        <option value="{{ $m }}">{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tahun <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="inputTahun" placeholder="Contoh: 2026" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jumlah Hari <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="inputHari" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea class="form-control" id="inputKeterangan" rows="3"></textarea>
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
            var table = $('#table-libur').DataTable({
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

            $('.btn-tambah-libur').on('click', function() {
                $('#modalLabel').text('Tambah Libur Pesantren');
                $('#formLibur')[0].reset();
                $('#inputId').val('');
            });

            $('#table-libur tbody').on('click', '.btn-edit-libur', function() {
                $('#modalLabel').text('Edit Libur Pesantren');
                $('#inputId').val($(this).data('id'));
                $('#inputBulan').val($(this).data('bulan'));
                $('#inputTahun').val($(this).data('tahun'));
                $('#inputHari').val($(this).data('hari'));
                $('#inputKeterangan').val($(this).data('keterangan'));
            });

            $('#formLibur').on('submit', function(e) {
                e.preventDefault();
                var id = $('#inputId').val();
                var url = id ? '{{ url("/master-data/libur-pesantren/update") }}/' + id : '{{ url("/master-data/libur-pesantren/store") }}';
                
                var data = {
                    _token: '{{ csrf_token() }}',
                    bulan: $('#inputBulan').val(),
                    tahun: $('#inputTahun').val(),
                    jumlah_hari: $('#inputHari').val(),
                    keterangan: $('#inputKeterangan').val()
                };

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        $('#modalLibur').modal('hide');
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

            $('#table-libur tbody').on('click', '.btn-delete-libur', function() {
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
                            url: '{{ url("/master-data/libur-pesantren/delete") }}/' + id,
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
