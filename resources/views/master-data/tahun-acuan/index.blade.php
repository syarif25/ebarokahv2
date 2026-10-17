@extends('layouts.app')
@section('title', 'Tahun Acuan - E-Barokah')
@section('page_title')
    <span class="text-danger font-weight-bold">Master</span> / Tahun Acuan
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        @push('styles')
            <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
            <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
            <style>
                table.dataTable.display tbody td, 
                table.dataTable.display tbody th {
                    border-bottom: 1px solid #f0f0f0;
                    border-right: none;
                    border-left: none;
                    padding: 15px 18px;
                }
                table.dataTable.display thead th {
                    border-bottom: 2px solid #e0e0e0;
                    font-weight: 600;
                    color: #333;
                    text-transform: uppercase;
                }
                table.dataTable.display tbody tr:hover {
                    background-color: #f9f9f9;
                }
                .dataTables_wrapper .dataTables_paginate .paginate_button.current {
                    background: var(--primary);
                    color: #fff !important;
                    border: none;
                    border-radius: 5px;
                }
            </style>
        @endpush

        <!-- Page Heading -->
        <div class="mb-4">
            <h4 class="text-primary font-w600">Master Data / <span class="text-muted">Tahun Acuan</span></h4>
        </div>

        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Daftar Tahun Acuan</h4>
                <button type="button" class="btn btn-sm btn-tambah-tahun shadow-sm" style="background-color: #d848c9; color: white; border-radius: 20px; padding: 8px 20px;" data-bs-toggle="modal" data-bs-target="#modalTahun">
                    <i class="fas fa-plus me-1"></i> Tambah Data
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-tahun" class="display" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Bidang</th>
                                <th>Tahun Acuan</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tahun_acuan as $t)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $t->id_bidang }}</td>
                                <td>{{ $t->tahun_acuan }}</td>
                                <td>{{ $t->keterangan }}</td>
                                <td>
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-outline-success btn-sm btn-edit-tahun" style="border-radius: 20px; padding: 5px 20px;" data-bs-toggle="modal" data-bs-target="#modalTahun"
                                            data-id="{{ $t->id }}"
                                            data-bidang="{{ $t->id_bidang }}"
                                            data-tahun="{{ $t->tahun_acuan }}"
                                            data-keterangan="{{ $t->keterangan }}">
                                            Ubah
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm btn-delete-tahun" style="border-radius: 20px; padding: 5px 15px; margin-left: 10px;" data-id="{{ $t->id }}">
                                            Hapus
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

        <!-- Modal -->
        <div class="modal fade" id="modalTahun">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Form Tahun Acuan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="formTahun">
                        <input type="hidden" id="inputIdTahun">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Bidang <span class="text-danger">*</span></label>
                                <select class="form-control" id="inputBidang" required>
                                    <option value="">-- Pilih Bidang --</option>
                                    <option value="Pengurus">Pengurus</option>
                                    <option value="Bidang DIKTI">Bidang DIKTI</option>
                                    <option value="Bidang DIKJAR">Bidang DIKJAR</option>
                                    <option value="Bidang DIKJAR-M">Bidang DIKJAR-M</option>
                                    <option value="Bidang Kepesantrenan dan PU">Bidang Kepesantrenan dan PU</option>
                                    <option value="Bidang KAMTIB">Bidang KAMTIB</option>
                                    <option value="Bidang Usaha">Bidang Usaha</option>
                                    <option value="Sekretariat">Sekretariat</option>
                                    <option value="BPK2M">BPK2M</option>
                                    <option value="Bendahara">Bendahara</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tahun Acuan <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="inputTahun" placeholder="Contoh: 2026" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="inputKeterangan" placeholder="Contoh: Tahun Ajaran Sekolah" required>
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
        (function($) {
            "use strict"
            $('#table-tahun').DataTable({
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>' 
                    }
                }
            });

            $('#formTahun').on('submit', function(e) {
                e.preventDefault();

                var id = $('#inputIdTahun').val();
                var url = id ? '{{ url("/master-data/tahun-acuan/update") }}/' + id : '{{ url("/master-data/tahun-acuan/store") }}';
                
                var data = {
                    _token: '{{ csrf_token() }}',
                    id_bidang: $('#inputBidang').val(),
                    tahun_acuan: $('#inputTahun').val(),
                    keterangan: $('#inputKeterangan').val()
                };

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        Swal.fire(
                            'Tersimpan!',
                            'Data tahun acuan berhasil disimpan ke database.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    },
                    error: function(err) {
                        Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
                    }
                });
            });

            $('#table-tahun tbody').on('click', '.btn-delete-tahun', function() {
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
                            url: '{{ url("/master-data/tahun-acuan/delete") }}/' + id,
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

            $('#table-tahun tbody').on('click', '.btn-edit-tahun', function() {
                var id = $(this).data('id');
                var bidang = $.trim($(this).data('bidang'));
                var tahun = $(this).data('tahun');
                var keterangan = $(this).data('keterangan');

                $('.modal-title').text('Edit Tahun Acuan');
                $('#inputIdTahun').val(id);
                
                // Failsafe for Bidang
                if (bidang !== '' && $('#inputBidang option[value="' + bidang + '"]').length === 0) {
                    $('#inputBidang').append(new Option(bidang, bidang));
                }
                
                $('#inputBidang').val(bidang).trigger('change');
                $('#inputTahun').val(tahun);
                $('#inputKeterangan').val(keterangan);
            });

            $('.btn-tambah-tahun').on('click', function() {
                $('.modal-title').text('Form Tahun Acuan');
                $('#formTahun')[0].reset();
                $('#inputIdTahun').val('');
                $('#inputBidang').val('').trigger('change');
            });
        })(jQuery);
    </script>
@endpush
@endsection
