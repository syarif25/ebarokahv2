@extends('layouts.app')
@section('title', 'Potongan Barokah Satpam - E-Barokah')
@section('page_title')
<span class="text-danger font-weight-bold">Master</span> / Potongan Barokah Satpam
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

        .btn-tambah-potongan {
            background-color: #d848c9 !important; 
            color: white !important; 
            border-radius: 20px; 
            padding: 8px 20px;
            border: none;
            font-weight: 600;
        }
        .btn-tambah-potongan:hover {
            background-color: #c13bb3 !important;
            color: white !important;
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
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap pt-3 px-4">
                    <h4 class="card-title font-weight-bold text-dark mb-0">Daftar Potongan Barokah Satpam</h4>
                    <button type="button" class="btn btn-sm btn-tambah-potongan shadow-sm" onclick="addPotongan()">
                        <i class="fas fa-plus me-1 mr-1"></i> Tambah Potongan
                    </button>
                </div>
                <div class="card-body px-4">
                    <div class="table-responsive">
                        <table id="tablePotonganSatpam" class="display table-hover w-100" style="min-width: 845px;">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50px;">No</th>
                                    <th>Nama Lengkap</th>
                                    <th>Jenis Potongan</th>
                                    <th>Nominal</th>
                                    <th class="text-center">Periode</th>
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

<!-- Modal Form Potongan Barokah Satpam -->
<div class="modal fade" id="modalPotonganSatpam" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <div class="modal-header border-bottom-0 pb-0 mt-3 mx-3">
                <h5 class="modal-title font-weight-bold text-dark" id="modalPotonganTitle">Form Potongan Barokah</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="font-size: 1.5rem; border: none; background: transparent;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formPotonganSatpam">
                @csrf
                <input type="hidden" name="id_potongan_satpam" id="id_potongan_satpam">
                <div class="modal-body px-4 pt-3 pb-2">
                    <!-- Nama Lengkap Satpam -->
                    <div class="form-group mb-3">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <select class="form-control" name="id_satpam" id="id_satpam" style="width: 100%;">
                            <option value=""></option>
                            @foreach($satpams as $s)
                                <option value="{{ $s->id_satpam }}">
                                    {{ $s->umana->nama_lengkap ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-danger error-text error-id_satpam"></small>
                    </div>

                    <!-- Jenis Potongan & Nominal -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Jenis Potongan <span class="text-danger">*</span></label>
                            <select class="form-control" name="jenis_potongan" id="jenis_potongan" style="width: 100%;">
                                <option value=""></option>
                                @foreach($jenisPotongans as $jp)
                                    <option value="{{ $jp->id_potongan }}" data-nominal="{{ $jp->nominal }}">
                                        {{ $jp->nama_potongan }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger error-text error-jenis_potongan"></small>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Nominal (Rp) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-right numeric" name="nominal_potongan" id="nominal_potongan" placeholder="0">
                            <small class="text-danger error-text error-nominal_potongan"></small>
                        </div>
                    </div>

                    <!-- Periode Mulai & Sampai -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Masa Aktif Mulai dari <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="min_periode_potongan" id="min_periode_potongan">
                            <small class="text-danger error-text error-min_periode_potongan"></small>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Sampai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="max_periode_potongan" id="max_periode_potongan">
                            <small class="text-danger error-text error-max_periode_potongan"></small>
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="modal-footer border-top-0 pt-0 pb-4 px-4 d-flex justify-content-end">
                    <button type="button" class="btn btn-light btn-rounded px-4 mr-2" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success btn-rounded px-4 font-weight-bold" id="btnSave">Simpan</button>
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
    let tablePotonganSatpam;
    let saveMethod = 'add';

    function formatRupiahInput(angka) {
        let number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    }

    $(document).on('keyup', '.numeric', function() {
        $(this).val(formatRupiahInput($(this).val()));
    });

    $(document).ready(function() {
        // Init DataTable
        tablePotonganSatpam = $('#tablePotonganSatpam').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ url('/manajemen-satpam/potongan-satpam') }}",
                type: 'GET'
            },
            columns: [
                { data: 'no', className: 'text-center' },
                { data: 'nama_lengkap' },
                { data: 'jenis_potongan' },
                { data: 'nominal' },
                { data: 'periode', className: 'text-center' },
                { data: 'aksi', className: 'text-center', orderable: false }
            ],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                zeroRecords: "Tidak ada data potongan satpam ditemukan",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previ"
                }
            }
        });

        // Init Select2
        $('#id_satpam').select2({
            dropdownParent: $('#modalPotonganSatpam'),
            placeholder: 'Pilih Satpam',
            allowClear: true,
            width: '100%'
        });

        $('#jenis_potongan').select2({
            dropdownParent: $('#modalPotonganSatpam'),
            placeholder: 'Pilih Jenis Potongan',
            allowClear: true,
            width: '100%'
        });

        // Otomatis isi nominal saat jenis potongan dipilih
        let isPopulating = false;
        $('#jenis_potongan').on('change', function() {
            if (!isPopulating) {
                const selectedOption = $(this).find(':selected');
                const nominal = selectedOption.data('nominal');
                if (nominal !== undefined && nominal !== null && nominal !== '') {
                    $('#nominal_potongan').val(formatRupiahInput(nominal.toString()));
                }
            }
        });

        // Submit Form
        $('#formPotonganSatpam').on('submit', function(e) {
            e.preventDefault();
            $('.error-text').text('');

            const btn = $('#btnSave');
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

            const formData = new FormData(this);
            let url = "{{ url('/manajemen-satpam/potongan-satpam/store') }}";
            if (saveMethod === 'edit') {
                const id = $('#id_potongan_satpam').val();
                url = "{{ url('/manajemen-satpam/potongan-satpam/update') }}/" + id;
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
                        $('#modalPotonganSatpam').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 1800,
                            showConfirmButton: false
                        });
                        tablePotonganSatpam.ajax.reload(null, false);
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
                            text: 'Gagal menyimpan data potongan satpam.'
                        });
                    }
                }
            });
        });

        // Tombol Edit
        $(document).on('click', '.btn-edit-potongan', function() {
            const id = $(this).data('id');
            saveMethod = 'edit';
            $('#formPotonganSatpam')[0].reset();
            $('.error-text').text('');

            $.getJSON("{{ url('/manajemen-satpam/potongan-satpam/edit') }}/" + id, function(res) {
                if (res && res.status) {
                    const d = res.data;
                    isPopulating = true;
                    $('#id_potongan_satpam').val(d.id_potongan_satpam);
                    $('#id_satpam').val(d.id_satpam).trigger('change');
                    $('#jenis_potongan').val(d.jenis_potongan).trigger('change');
                    $('#nominal_potongan').val(formatRupiahInput(d.nominal_potongan.toString()));
                    $('#min_periode_potongan').val(d.min_periode_potongan);
                    $('#max_periode_potongan').val(d.max_periode_potongan);
                    isPopulating = false;

                    $('#modalPotonganTitle').text('Form Edit Potongan Barokah');
                    $('#modalPotonganSatpam').modal('show');
                } else {
                    Swal.fire('Error', 'Data potongan tidak ditemukan.', 'error');
                }
            });
        });
    });

    function addPotongan() {
        saveMethod = 'add';
        $('#formPotonganSatpam')[0].reset();
        $('#id_potongan_satpam').val('');
        $('#id_satpam').val(null).trigger('change');
        $('#jenis_potongan').val(null).trigger('change');
        $('#nominal_potongan').val('');
        $('.error-text').text('');
        $('#modalPotonganTitle').text('Form Potongan Barokah');
        $('#modalPotonganSatpam').modal('show');
    }
</script>
@endpush
