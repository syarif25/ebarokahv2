@extends('layouts.app')
@section('title', 'Barokah Pengajar - E-Barokah')
@section('page_title')
<span class="text-danger font-weight-bold">Master</span> / Barokah Pengajar
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
        
        .btn-tambah-barokah {
            background-color: #d848c9 !important; 
            color: white !important; 
            border-radius: 20px; 
            padding: 8px 20px;
            border: none;
        }
        .btn-edit-barokah {
            border-radius: 20px;
            padding: 5px 15px;
        }
        .btn-delete-barokah {
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
                <h4 class="card-title">Daftar Barokah Pengajar</h4>
                <button type="button" class="btn btn-sm btn-tambah-barokah shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBarokah">
                    <i class="fas fa-plus me-1"></i> Tambah Barokah
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-barokah" class="display" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kategori</th>
                                <th>Masa Bakti (Tahun)</th>
                                <th>Ijazah</th>
                                <th>Nominal</th>
                                <th>Aksi</th>
                            </tr>
                            <tr class="filters" style="background-color: #f8f9fa;">
                                <th></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Kategori" style="border-radius: 8px;"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Masa Bakti" style="border-radius: 8px;"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Ijazah" style="border-radius: 8px;"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Nominal" style="border-radius: 8px;"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($barokah as $b)
                            <tr>
                                <td><strong>{{ $loop->iteration }}</strong></td>
                                <td>
                                    <strong>{{ $b->kategori }}</strong>
                                    @if($b->golongan) <br><small class="text-muted">{{ $b->golongan }}</small> @endif
                                </td>
                                <td><strong>{{ $b->min_tmp_mengajar }} - {{ $b->max_tmp_mengajar }} Tahun</strong></td>
                                <td><strong>{{ $b->ijazah }}</strong></td>
                                <td><strong>{{ number_format($b->nominal, 0, ',', '.') }}</strong></td>
                                <td>
                                    <div class="d-flex flex-column" style="width: max-content;">
                                        <button type="button" class="btn btn-outline-success btn-sm shadow-sm btn-edit-barokah" data-bs-toggle="modal" data-bs-target="#modalBarokah"
                                            data-id="{{ $b->id_barokah_pengajar }}"
                                            data-kategori="{{ $b->kategori }}"
                                            data-golongan="{{ $b->golongan }}"
                                            data-min="{{ $b->min_tmp_mengajar }}"
                                            data-max="{{ $b->max_tmp_mengajar }}"
                                            data-ijazah="{{ $b->ijazah }}"
                                            data-nominal="{{ $b->nominal }}">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm shadow-sm btn-delete-barokah" data-id="{{ $b->id_barokah_pengajar }}">
                                            <i class="fas fa-trash-alt me-1"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data barokah pengajar</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modalBarokah">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Form Barokah Pengajar</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="formBarokah">
                        <input type="hidden" id="inputId">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kategori (Contoh: Dosen UNIB) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="inputKategori" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Golongan <span class="text-muted">(Opsional)</span></label>
                                    <input type="text" class="form-control" id="inputGolongan">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Minimal Masa Bakti (Tahun) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="inputMin" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Maksimal Masa Bakti (Tahun) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="inputMax" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Ijazah Terakhir <span class="text-danger">*</span></label>
                                    <select class="form-control" id="inputIjazah" required>
                                        <option value="">-- Pilih Ijazah --</option>
                                        <option value="SD">SD</option>
                                        <option value="SMP">SMP</option>
                                        <option value="SLTA/D1">SLTA/D1</option>
                                        <option value="SMK">SMK</option>
                                        <option value="D2/D3">D2/D3</option>
                                        <option value="S1">S1</option>
                                        <option value="S2">S2</option>
                                        <option value="S3">S3</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nominal (Rp) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="inputNominal" required>
                                </div>
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
            var table = $('#table-barokah').DataTable({
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

            $('.btn-tambah-barokah').on('click', function() {
                $('#modalLabel').text('Tambah Barokah Pengajar');
                $('#formBarokah')[0].reset();
                $('#inputId').val('');
            });

            $('#table-barokah tbody').on('click', '.btn-edit-barokah', function() {
                $('#modalLabel').text('Edit Barokah Pengajar');
                $('#inputId').val($(this).data('id'));
                $('#inputKategori').val($(this).data('kategori'));
                $('#inputGolongan').val($(this).data('golongan'));
                $('#inputMin').val($(this).data('min'));
                $('#inputMax').val($(this).data('max'));
                $('#inputIjazah').val($(this).data('ijazah'));
                $('#inputNominal').val($(this).data('nominal'));
            });

            $('#formBarokah').on('submit', function(e) {
                e.preventDefault();
                var id = $('#inputId').val();
                var url = id ? '{{ url("/master-data/barokah-pengajar/update") }}/' + id : '{{ url("/master-data/barokah-pengajar/store") }}';
                
                var data = {
                    _token: '{{ csrf_token() }}',
                    kategori: $('#inputKategori').val(),
                    golongan: $('#inputGolongan').val(),
                    min_tmp_mengajar: $('#inputMin').val(),
                    max_tmp_mengajar: $('#inputMax').val(),
                    ijazah: $('#inputIjazah').val(),
                    nominal: $('#inputNominal').val()
                };

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        $('#modalBarokah').modal('hide');
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

            $('#table-barokah tbody').on('click', '.btn-delete-barokah', function() {
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
                            url: '{{ url("/master-data/barokah-pengajar/delete") }}/' + id,
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
