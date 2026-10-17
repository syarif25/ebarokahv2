@extends('layouts.app')
@section('title', 'Umana - E-Barokah')
@section('page_title', 'Master Data / Umana')

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
        
        .btn-edit-umana {
            background-color: #0bd649 !important;
            color: #fff !important;
            border: none;
            border-radius: 20px;
            padding: 6px 18px;
            font-weight: 500;
        }
        .btn-delete-umana {
            background-color: #ff3156 !important;
            color: #fff !important;
            border: none;
            border-radius: 20px;
            padding: 6px 15px;
            font-weight: 500;
            margin-top: 5px;
        }
        .nav-tabs .nav-link {
            border-radius: 0;
            font-weight: 500;
            color: #6e6e6e;
        }
        .nav-tabs .nav-link.active {
            color: #0d6efd;
            border-bottom: 2px solid #0d6efd;
        }
        .tab-content {
            padding-top: 15px;
        }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="card-title">Daftar Umana</h4>
                <button type="button" class="btn btn-primary btn-sm btn-tambah-umana" data-bs-toggle="modal" data-bs-target="#modalUmana">
                    <i class="fas fa-plus me-1"></i> Tambah Umana
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-umana" class="display" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th style="width:50px;"><strong>No</strong></th>
                                <th><strong>NIK</strong></th>
                                <th><strong>Nama Lengkap</strong></th>
                                <th><strong>Alamat</strong></th>
                                <th><strong>No HP</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                            <tr class="filters" style="background-color: #f8f9fa;">
                                <th></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter NIK" style="border-radius: 8px; width: 100%; min-width: 100px;"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Nama" style="border-radius: 8px; width: 100%; min-width: 140px;"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Alamat" style="border-radius: 8px; width: 100%; min-width: 120px;"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter HP" style="border-radius: 8px; width: 100%; min-width: 100px;"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data populated via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modalUmana" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUmanaLabel">Form Umana</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="form-umana">
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" name="is_edit" id="is_edit" value="0">
                            <input type="hidden" id="old_nik" value="">

                            <!-- Tabs Header -->
                            <ul class="nav nav-tabs" id="umanaTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="pribadi-tab" data-bs-toggle="tab" data-bs-target="#pribadi" type="button" role="tab">Pribadi</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="akademik-tab" data-bs-toggle="tab" data-bs-target="#akademik" type="button" role="tab">Akademik & Jabatan</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="tmt-tab" data-bs-toggle="tab" data-bs-target="#tmt" type="button" role="tab">TMT</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="rekening-tab" data-bs-toggle="tab" data-bs-target="#rekening" type="button" role="tab">Rekening</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="kredensial-tab" data-bs-toggle="tab" data-bs-target="#kredensial" type="button" role="tab">Kredensial</button>
                                </li>
                            </ul>

                            <!-- Tabs Content -->
                            <div class="tab-content" id="umanaTabsContent">
                                
                                <!-- Tab Pribadi -->
                                <div class="tab-pane fade show active" id="pribadi" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>NIK *</strong></label>
                                            <input type="text" class="form-control" name="nik" id="nik" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>NIY *</strong></label>
                                            <input type="text" class="form-control" name="niy" id="niy" required>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"><strong>Nama Lengkap *</strong></label>
                                            <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Gelar Depan</strong></label>
                                            <input type="text" class="form-control" name="gelar_depan" id="gelar_depan">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Gelar Belakang</strong></label>
                                            <input type="text" class="form-control" name="gelar_belakang" id="gelar_belakang">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Jenis Kelamin *</strong></label>
                                            <select class="form-control" name="jk" id="jk" required>
                                                <option value="">-- Pilih --</option>
                                                <option value="Laki-laki">Laki-laki</option>
                                                <option value="Perempuan">Perempuan</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Tanggal Lahir *</strong></label>
                                            <input type="date" class="form-control" name="tgl_lahir" id="tgl_lahir" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Status Nikah *</strong></label>
                                            <select class="form-control" name="status_nikah" id="status_nikah" required>
                                                <option value="">-- Pilih --</option>
                                                <option value="Menikah">Menikah</option>
                                                <option value="Belum Menikah">Belum Menikah</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>No HP *</strong></label>
                                            <input type="text" class="form-control" name="nomor_hp" id="nomor_hp" required>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"><strong>Alamat Domisili *</strong></label>
                                            <textarea class="form-control" name="alamat_domisili" id="alamat_domisili" rows="2" required></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab Akademik -->
                                <div class="tab-pane fade" id="akademik" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>NUPTK / NIDN</strong></label>
                                            <input type="text" class="form-control" name="nidn" id="nidn">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Bidang Keahlian</strong></label>
                                            <input type="text" class="form-control" name="bidang_keahlian" id="bidang_keahlian">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Ijazah Terakhir *</strong></label>
                                            <select class="form-control" name="ijazah_terakhir" id="ijazah_terakhir" required>
                                                <option value="">-- Pilih --</option>
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
                                            <label class="form-label"><strong>Jabatan Akademik</strong></label>
                                            <select class="form-control" name="jabatan_akademik" id="jabatan_akademik">
                                                <option value="">-- Pilih --</option>
                                                @foreach($jafung as $jf)
                                                    <option value="{{ $jf->id_barokah_jafung }}">{{ $jf->nama_jafung }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Status Sertifikasi</strong></label>
                                            <select class="form-control" name="status_sertifikasi" id="status_sertifikasi">
                                                <option value="">-- Pilih --</option>
                                                <option value="Sudah">Sudah</option>
                                                <option value="Belum">Belum</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Ikatan Khidmah *</strong></label>
                                            <select class="form-control" name="ikatan_khidmah" id="ikatan_khidmah" required>
                                                <option value="">-- Pilih --</option>
                                                <option value="Pegawai Tetap">Pegawai Tetap</option>
                                                <option value="Dosen Tetap">Dosen Tetap</option>
                                                <option value="Guru Tetap">Guru Tetap</option>
                                                <option value="Magang">Magang</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab TMT -->
                                <div class="tab-pane fade" id="tmt" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>TMT Struktural</strong></label>
                                            <input type="date" class="form-control" name="tmt_struktural" id="tmt_struktural">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>TMT Guru</strong></label>
                                            <input type="date" class="form-control" name="tmt_guru" id="tmt_guru">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>TMT Dosen</strong></label>
                                            <input type="date" class="form-control" name="tmt_dosen" id="tmt_dosen">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>TMT Maif</strong></label>
                                            <input type="date" class="form-control" name="tmt_maif" id="tmt_maif">
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab Rekening -->
                                <div class="tab-pane fade" id="rekening" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Nomor Rekening *</strong></label>
                                            <input type="text" class="form-control" name="no_rekening" id="no_rekening" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Atas Nama *</strong></label>
                                            <input type="text" class="form-control" name="atas_nama" id="atas_nama" required>
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label"><strong>Nama Bank *</strong></label>
                                            <select class="form-control" name="nama_bank" id="nama_bank" required>
                                                <option value="">-- Pilih --</option>
                                                <option value="Bank Rakyat Indonesia">Bank Rakyat Indonesia (BRI)</option>
                                                <option value="Bank Syari'ah Indonesia">Bank Syari'ah Indonesia (BSI)</option>
                                                <option value="Bank Mandiri">Bank Mandiri</option>
                                                <option value="Bank Jatim">Bank Jatim</option>
                                                <option value="Bank BNI">Bank BNI</option>
                                                <option value="Bank BCA">Bank BCA</option>
                                                <option value="CIMB">CIMB</option>
                                                <option value="Bank Muamalat">Bank Muamalat</option>
                                                <option value="BTN">BTN</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab Kredensial -->
                                <div class="tab-pane fade" id="kredensial" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Status *</strong></label>
                                            <select class="form-control" name="status_aktif" id="status_aktif" required>
                                                <option value="">-- Pilih --</option>
                                                <option value="Aktif">Aktif</option>
                                                <option value="Tidak Aktif">Tidak Aktif</option>
                                                <option value="Cuti 50%">Cuti 50%</option>
                                                <option value="Cuti 100%">Cuti 100%</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><strong>Password <span class="text-danger" id="pwd_req">*</span></strong></label>
                                            <input type="password" class="form-control" name="password" id="password" placeholder="Kosongkan jika tidak diubah">
                                        </div>
                                    </div>
                                </div>

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
    </div>
</div>

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        var table = $('#table-umana').DataTable({
            orderCellsTop: true,
            processing: true,
            ajax: '{{ url("/master-data/umana") }}',
            columns: [
                {
                    data: null, 
                    searchable: false, 
                    orderable: false, 
                    render: function (data, type, row, meta) {
                        return '<strong>' + (meta.row + meta.settings._iDisplayStart + 1) + '</strong>';
                    }
                },
                { data: 'nik', render: function(data) { return '<strong>' + data + '</strong>'; } },
                { 
                    data: 'nama_lengkap',
                    render: function(data, type, row) {
                        var gelarDepan = row.gelar_depan ? row.gelar_depan.trim() + ' ' : '';
                        var gelarBelakang = row.gelar_belakang ? ', ' + row.gelar_belakang.trim() : '';
                        var nama = data ? data.toLowerCase().replace(/\b\w/g, function(l) { return l.toUpperCase(); }) : '';
                        return gelarDepan + nama + gelarBelakang;
                    }
                },
                { data: 'alamat_domisili', render: function(data) { return data ? data : '-'; } },
                { data: 'nomor_hp', render: function(data) { return data ? data : '0'; } },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return '<div class="d-flex flex-column" style="width: max-content;">' +
                               '<button type="button" class="btn-edit-umana shadow-sm" data-id="' + row.nik + '">Edit</button>' +
                               '<button type="button" class="btn-delete-umana shadow-sm" data-id="' + row.nik + '">Hapus</button>' +
                               '</div>';
                    }
                }
            ],
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
                    $('input', $('#table-umana thead tr.filters th').eq(i)).on('keyup change clear', function () {
                        if (column.search() !== this.value) {
                            column.search(this.value).draw();
                        }
                    });
                });
            }
        });

        $('.btn-tambah-umana').on('click', function() {
            $('#modalUmanaLabel').text('Tambah Umana');
            $('#form-umana')[0].reset();
            $('#is_edit').val(0);
            $('#old_nik').val('');
            $('#pwd_req').show();
            $('#password').prop('required', true);
            $('.nav-tabs a[href="#pribadi"]').tab('show');
        });

        $('#table-umana tbody').on('click', '.btn-edit-umana', function() {
            var id = $(this).data('id');
            
            // Fetch complete data via AJAX
            $.get('{{ url("/master-data/umana/edit") }}/' + id, function(response) {
                if(response.success) {
                    var data = response.data;
                    $('#modalUmanaLabel').text('Edit Umana');
                    $('#form-umana')[0].reset();
                    $('#is_edit').val(1);
                    $('#old_nik').val(data.nik);
                    $('#pwd_req').hide();
                    $('#password').prop('required', false);
                    
                    // Populate fields
                    Object.keys(data).forEach(function(key) {
                        var elem = $('#' + key);
                        if(elem.length > 0 && key !== 'password') {
                            elem.val(data[key]);
                        }
                    });
                    
                    $('.nav-tabs a[href="#pribadi"]').tab('show');
                    $('#modalUmana').modal('show');
                }
            });
        });

        $('#table-umana tbody').on('click', '.btn-delete-umana', function() {
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
                        url: '{{ url("/master-data/umana/delete") }}/' + id,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if(response.success) {
                                Swal.fire('Terhapus!', response.message, 'success').then(() => {
                                    table.ajax.reload(null, false);
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

        $('#form-umana').on('submit', function(e) {
            e.preventDefault();
            var isEdit = $('#is_edit').val() == "1";
            var id = $('#old_nik').val();
            var url = isEdit ? '{{ url("/master-data/umana/update") }}/' + id : '{{ url("/master-data/umana/store") }}';
            
            $.ajax({
                url: url,
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if(response.success) {
                        $('#modalUmana').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            table.ajax.reload(null, false);
                        });
                    }
                },
                error: function(xhr) {
                    var msg = 'Terjadi kesalahan, silakan coba lagi';
                    if(xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });
        });
    });
</script>
@endpush
@endsection
