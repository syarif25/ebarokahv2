@extends('layouts.app')
@section('title', 'Barokah Kepanitiaan - E-Barokah')
@section('page_title')
<span class="text-danger font-weight-bold">Master</span> / Barokah Kepanitiaan
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
            padding: 1.2rem 1rem !important;
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
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                <h4 class="card-title">Daftar Barokah Kepanitiaan</h4>
                <div class="d-flex align-items-center" style="gap: 8px;">
                    <a href="{{ url('/master-data/jenis-kegiatan') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 20px; padding: 7px 18px;">
                        <i class="fas fa-list me-1"></i> Kelola Kegiatan
                    </a>
                    <button type="button" class="btn btn-sm btn-tambah-barokah shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBarokah">
                        <i class="fas fa-plus me-1"></i> Tambah Barokah
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-barokah" class="display" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kegiatan</th>
                                <th>Kategori</th>
                                <th>Nama Komponen</th>
                                <th>Satuan</th>
                                <th>Nominal</th>
                                <th>Ketentuan & Aturan</th>
                                <th>Aksi</th>
                            </tr>
                            <tr class="filters" style="background-color: #f8f9fa;">
                                <th></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Kegiatan" style="border-radius: 8px;"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Kategori" style="border-radius: 8px;"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Komponen" style="border-radius: 8px;"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Satuan" style="border-radius: 8px;"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Nominal" style="border-radius: 8px;"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Aturan" style="border-radius: 8px;"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($barokah as $b)
                            <tr>
                                <td><strong>{{ $loop->iteration }}</strong></td>
                                <td><strong>{{ $b->jenisKegiatan->nama_kegiatan ?? '-' }}</strong></td>
                                <td>
                                    <span class="badge badge-primary text-uppercase" style="border-radius: 12px; padding: 4px 8px; font-size: 11px;">
                                        {{ str_replace('_', ' ', $b->kolom_kategori) }}
                                    </span>
                                </td>
                                <td><strong>{{ $b->nama_komponen }}</strong></td>
                                <td><strong>{{ $b->satuan !== 'kosong' ? 'per ' . $b->satuan : '-' }}</strong></td>
                                <td><strong>Rp {{ number_format($b->nominal, 0, ',', '.') }}</strong></td>
                                <td><small class="text-muted">{{ $b->keterangan_aturan ?: '-' }}</small></td>
                                <td>
                                    <div class="d-flex flex-column" style="width: max-content;">
                                        <button type="button" class="btn btn-outline-success btn-sm shadow-sm btn-edit-barokah" data-bs-toggle="modal" data-bs-target="#modalBarokah"
                                            data-id="{{ $b->id_barokah_kepanitiaan }}"
                                            data-kegiatan="{{ $b->id_jenis_kegiatan }}"
                                            data-pilar="{{ $b->kolom_kategori }}"
                                            data-nama="{{ $b->nama_komponen }}"
                                            data-satuan="{{ $b->satuan }}"
                                            data-nominal="{{ (int) $b->nominal }}"
                                            data-aturan="{{ $b->keterangan_aturan }}">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm shadow-sm btn-delete-barokah" data-id="{{ $b->id_barokah_kepanitiaan }}">
                                            <i class="fas fa-trash-alt me-1"></i> Hapus
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

        <!-- Modal Tambah / Edit -->
        <div class="modal fade" id="modalBarokah">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Form Barokah Kepanitiaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="formBarokah">
                        <input type="hidden" id="inputId">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jenis Kegiatan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="inputKegiatan" required>
                                        <option value="">-- Pilih Kegiatan --</option>
                                        @foreach($jenisKegiatan as $jk)
                                            <option value="{{ $jk->id_jenis_kegiatan }}">{{ $jk->nama_kegiatan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kategori <span class="text-danger">*</span></label>
                                    <select class="form-control" id="inputPilar" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <option value="kepanitiaan">Kepanitiaan (Barokah Pokok)</option>
                                        <option value="kehadiran">Kehadiran Panitia</option>
                                        <option value="konsumsi">Konsumsi Pengawas</option>
                                        <option value="pemb_soal">Pembuatan Soal</option>
                                        <option value="input_soal">Input / Pembuatan Soal</option>
                                        <option value="pengawas">Pengawas Ruang </option>
                                        <option value="penguji">Penguji </option>
                                        <option value="korektor">Korektor </option>
                                        <option value="input_nilai">Input Nilai</option>
                                        <option value="pembimbing">Pembimbing</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="inputNama" placeholder="Contoh: Ketua, Pengawas Ruang, Penguji Skripsi" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Satuan Hitung <span class="text-danger">*</span></label>
                                    <select class="form-control" id="inputSatuan" required>
                                        <option value="kosong">Kosong</option>
                                        <option value="mhs">Siswa/Mahasiswa</option>
                                        <option value="sesi">Sesi</option>
                                        <option value="shift">Shift </option>
                                        <option value="hari">Hari</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Nominal Barokah (Rp) <span class="text-danger">*</span></label>
                                    <input type="text" inputmode="numeric" class="form-control" id="inputNominal" placeholder="Contoh: 350.000" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Ketentuan & Aturan Khusus <span class="text-muted">(Opsional)</span></label>
                                    <input type="text" class="form-control" id="inputAturan" placeholder="Contoh: Maksimal 1 orang, Min 20 mhs, dll.">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="btnSubmitBarokah">Simpan</button>
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
        function formatRibuan(angka) {
            if (angka === null || angka === undefined || angka === '') return '';
            var numberString = angka.toString().replace(/[^,\d]/g, '');
            if (numberString === '') return '';
            return numberString.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        $(document).ready(function() {
            var table = $('#table-barokah').DataTable({
                orderCellsTop: true,
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>' 
                    },
                    emptyTable: "Belum ada data barokah kepanitiaan",
                    zeroRecords: "Tidak ditemukan data yang sesuai"
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

            // Format otomatis input nominal dengan titik ribuan
            $('#inputNominal').on('input keyup', function() {
                $(this).val(formatRibuan($(this).val()));
            });

            // Tombol Tambah Baru
            $('.btn-tambah-barokah').on('click', function() {
                $('#modalLabel').text('Tambah Barokah Kepanitiaan');
                $('#btnSubmitBarokah').prop('disabled', false).html('Simpan');
                $('#formBarokah')[0].reset();
                $('#inputId').val('');
                $('#modalBarokah').modal('show');
            });

            // Tombol Edit
            $('#table-barokah tbody').on('click', '.btn-edit-barokah', function() {
                $('#modalLabel').text('Edit Barokah Kepanitiaan');
                $('#btnSubmitBarokah').prop('disabled', false).html('Perbarui');
                $('#inputId').val($(this).data('id'));
                $('#inputKegiatan').val($(this).data('kegiatan'));
                $('#inputPilar').val($(this).data('pilar'));
                $('#inputNama').val($(this).data('nama'));
                $('#inputSatuan').val($(this).data('satuan'));
                var rawNominal = Math.round(parseFloat($(this).data('nominal')) || 0);
                $('#inputNominal').val(formatRibuan(rawNominal));
                $('#inputAturan').val($(this).data('aturan'));
                $('#modalBarokah').modal('show');
            });

            // Submit Form (Store & Update)
            $('#formBarokah').on('submit', function(e) {
                e.preventDefault();
                var id = $('#inputId').val();
                var url = id ? '{{ url("/master-data/barokah-kepanitiaan/update") }}/' + id : '{{ url("/master-data/barokah-kepanitiaan/store") }}';
                
                var cleanNominal = $('#inputNominal').val().replace(/\./g, '');

                var data = {
                    _token: '{{ csrf_token() }}',
                    id_jenis_kegiatan: $('#inputKegiatan').val(),
                    kolom_kategori: $('#inputPilar').val(),
                    nama_komponen: $('#inputNama').val(),
                    satuan: $('#inputSatuan').val(),
                    nominal: cleanNominal,
                    keterangan_aturan: $('#inputAturan').val()
                };

                $('#btnSubmitBarokah').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
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
                        $('#btnSubmitBarokah').prop('disabled', false).html(id ? 'Perbarui' : 'Simpan');
                        var msg = 'Terjadi kesalahan saat menyimpan data';
                        if (err.responseJSON) {
                            if (err.responseJSON.errors) {
                                msg = Object.values(err.responseJSON.errors).flat().join('<br>');
                            } else if (err.responseJSON.message) {
                                msg = err.responseJSON.message;
                            }
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: msg
                        });
                    }
                });
            });

            // Tombol Hapus (Delete)
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
                            url: '{{ url("/master-data/barokah-kepanitiaan/delete") }}/' + id,
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
