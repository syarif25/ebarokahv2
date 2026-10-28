@extends('layouts.app')

@section('title', 'Lembaga - E-Barokah')
@section('page_title', 'Master Data / Lembaga')

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
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
            border-bottom: 1px solid #f0f0f0 !important;
            text-transform: capitalize;
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
                <h4 class="card-title">Daftar Lembaga</h4>
                <button type="button" class="btn btn-primary btn-sm btn-tambah-lembaga" data-bs-toggle="modal" data-bs-target="#modalLembaga">
                    <i class="fas fa-plus me-1"></i> Tambah Lembaga
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-lembaga" class="display" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th style="width:50px;"><strong>No</strong></th>
                                <th><strong>Nama Lembaga</strong></th>
                                <th><strong>Nama Bidang</strong></th>
                                <th><strong>Tenaga Pengajar</strong></th>
                                <th><strong>Nama Pimpinan</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lembaga as $l)
                            <tr>
                                <td><strong>{{ $loop->iteration }}</strong></td>
                                <td>{{ $l->nama_lembaga }}</td>
                                <td>{{ $l->id_bidang }}</td>
                                <td>{{ $l->tenaga_pengajar }}</td>
                                <td>{{ $l->nama_pimpinan }}</td>
                                <td>
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-outline-danger btn-sm shadow-sm btn-edit-lembaga" style="border-radius: 20px; padding: 5px 15px;" data-bs-toggle="modal" data-bs-target="#modalLembaga"
                                            data-id="{{ $l->id_lembaga }}"
                                            data-nama="{{ $l->nama_lembaga }}"
                                            data-bidang="{{ $l->id_bidang }}"
                                            data-pengajar="{{ $l->tenaga_pengajar }}"
                                            data-pimpinan="{{ $l->nama_pimpinan }}">
                                            <i class="fas fa-pencil-alt me-1"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-outline-warning btn-sm shadow-sm btn-delete-lembaga" style="border-radius: 20px; padding: 5px 15px; margin-left: 5px;" data-id="{{ $l->id_lembaga }}">
                                            <i class="fas fa-trash me-1"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data lembaga</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lembaga -->
<div class="modal fade" id="modalLembaga">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Lembaga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formLembaga">
                <input type="hidden" id="inputIdLembaga">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lembaga <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="inputNamaLembaga" placeholder="Masukkan Nama Lembaga" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Bidang <span class="text-danger">*</span></label>
                        <select class="form-control" id="inputBidang" required>
                            <option value="">-- Pilih Bidang --</option>
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
                        <label class="form-label">Tenaga Pengajar <span class="text-danger">*</span></label>
                        <select class="form-control" id="inputPengajar" required>
                            <option value="">-- Pilih --</option>
                            <option value="Ya">Ya</option>
                            <option value="Tidak">Tidak</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Pimpinan</label>
                        <input type="text" class="form-control" id="inputPimpinan" placeholder="Contoh: Budi Santoso (Opsional)">
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
    <script>
        (function($) {
            "use strict"
            $('#table-lembaga').DataTable({
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>' 
                    }
                }
            });

            $('#formLembaga').on('submit', function(e) {
                e.preventDefault();

                var id = $('#inputIdLembaga').val();
                var url = id ? '{{ url("/master-data/lembaga/update") }}/' + id : '{{ url("/master-data/lembaga/store") }}';
                
                var data = {
                    _token: '{{ csrf_token() }}',
                    nama_lembaga: $('#inputNamaLembaga').val(),
                    id_bidang: $('#inputBidang').val(),
                    tenaga_pengajar: $('#inputPengajar').val(),
                    nama_pimpinan: $('#inputPimpinan').val()
                };

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        Swal.fire(
                            'Tersimpan!',
                            'Data lembaga berhasil disimpan ke database.',
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

            $('#table-lembaga tbody').on('click', '.btn-delete-lembaga', function() {
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
                            url: '{{ url("/master-data/lembaga/delete") }}/' + id,
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

            $('#table-lembaga tbody').on('click', '.btn-edit-lembaga', function() {
                var id = $(this).data('id');
                var nama = $.trim($(this).data('nama'));
                var bidang = $.trim($(this).data('bidang'));
                var pengajar = $.trim($(this).data('pengajar'));
                var pimpinan = $.trim($(this).data('pimpinan'));

                $('.modal-title').text('Edit Lembaga');
                $('#inputIdLembaga').val(id);
                $('#inputNamaLembaga').val(nama);
                
                // Failsafe for Bidang
                if (bidang !== '' && $('#inputBidang option[value="' + bidang + '"]').length === 0) {
                    $('#inputBidang').append(new Option(bidang, bidang));
                }
                
                $('#inputBidang').val(bidang).trigger('change');
                $('#inputPengajar').val(pengajar).trigger('change');
                $('#inputPimpinan').val(pimpinan);
            });

            $('.btn-tambah-lembaga').on('click', function() {
                $('.modal-title').text('Form Lembaga');
                $('#formLembaga')[0].reset();
                $('#inputIdLembaga').val('');
                $('#inputBidang').val('').trigger('change');
                $('#inputPengajar').val('').trigger('change');
            });
        })(jQuery);
    </script>
@endpush
