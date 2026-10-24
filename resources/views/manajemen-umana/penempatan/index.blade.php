@extends('layouts.app')
@section('title', 'Penempatan Umana - E-Barokah')
@section('page_title')
<span class="text-danger font-weight-bold">Manajemen Umana'</span> / Daftar Penempatan Umana'
@endsection

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
        
        .btn-tambah-penempatan {
            background-color: #d848c9 !important; 
            color: white !important; 
            border-radius: 20px; 
            padding: 8px 20px;
            border: none;
        }

        .select2-container--default .select2-selection--single {
            height: 40px;
            border: 1px solid #d7dae3;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }
        .form-control, .select2-container--default .select2-selection--single {
            border: 1px solid #00a15d !important;
            border-radius: 8px !important;
            background-color: #fff;
            height: 42px;
        }
        .form-control:focus {
            border-color: #00a15d !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 161, 93, 0.25) !important;
        }
        .form-label {
            font-weight: 600;
            color: #333 !important;
            margin-bottom: 8px;
        }
        .form-control[readonly] {
            background-color: #e9ecef !important;
            border-color: #ced4da !important;
        }

        .link-cek-umana {
            transition: color 0.15s ease-in-out;
            cursor: pointer;
        }
        .link-cek-umana:hover {
            color: #00a15d !important;
            text-decoration: underline !important;
        }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="card-title">Daftar Penempatan Umana'</h4>
                <button type="button" class="btn btn-sm btn-tambah-penempatan shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPenempatan">
                    <i class="fas fa-plus me-1"></i> Tambah Penempatan
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-penempatan" class="display" style="min-width: 845px">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Lengkap</th>
                                <th>Lembaga</th>
                                <th>Masa Pengabdian</th>
                                <th>Sertifikasi</th>
                                <th>Tunjangan</th>
                                <th>Aksi</th>
                            </tr>
                            <tr class="filters">
                                <th></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Cari Nama" style="width: 100%; min-width: 120px;" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Cari Lembaga" style="width: 100%; min-width: 120px;" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Cari Masa" style="width: 100%; min-width: 100px;" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Cari Sertifikasi" style="width: 100%; min-width: 100px;" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Cari Tunjangan" style="width: 100%; min-width: 100px;" /></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data akan dimuat melalui AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Penempatan -->
        <div class="modal fade" id="modalPenempatan">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <div class="modal-header border-bottom-0 pb-0 mt-3 mx-3">
                        <h5 class="modal-title font-weight-bold text-dark" id="modalLabel">Form Penempatan Umana</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="formPenempatan" enctype="multipart/form-data">
                        <input type="hidden" id="inputId" name="id_penempatan">
                        <div class="modal-body px-4 pt-4">
                            <!-- BARIS 1 -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">NIK <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="nik" id="nik" required>
                                        <option value="">Pilih NIK</option>
                                        @foreach($umanas as $u)
                                            <option value="{{ $u->nik }}" data-niy="{{ $u->niy }}" data-nama="{{ $u->nama_lengkap }}" data-sertifikasi="{{ $u->status_sertifikasi }}">{{ $u->nik }} - {{ $u->nama_lengkap }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nomor Induk P2S3</label>
                                    <input type="text" class="form-control" id="niy" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama_lengkap" readonly>
                                </div>
                            </div>
                            
                            <!-- BARIS 2 -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Jabatan/Rank Barokah <span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="id_ketentuan" id="id_ketentuan" required>
                                        <option value="">Pilih Jabatan Barokah</option>
                                        @foreach($ketentuans as $k)
                                            <option value="{{ $k->id_ketentuan }}" data-nama="{{ $k->nama_jabatan }}">{{ $k->kategori }} - {{ $k->nama_jabatan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Nama Jabatan</label>
                                    <input type="text" class="form-control" id="nama_jabatan" name="jabatan_lembaga" placeholder="Nama Jabatan Spesifik" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Lembaga</label>
                                    <select class="form-control select2" name="id_lembaga" id="id_lembaga">
                                        <option value="">-- Kosongkan jika tidak ada --</option>
                                        @foreach($lembagas as $l)
                                            <option value="{{ $l->id_lembaga }}">{{ $l->nama_lembaga }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- BARIS 3 -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="tgl_mulai" name="tgl_mulai" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tanggal Selesai</label>
                                    <input type="date" class="form-control" id="tgl_selesai" name="tgl_selesai">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">File SK (PDF/JPG/PNG Max 2MB)</label>
                                    <input type="file" class="form-control" id="file_sk" name="file_sk" accept=".pdf,.jpg,.jpeg,.png" style="padding: 7px 15px;">
                                </div>
                            </div>

                            <!-- BARIS 4 -->
                            <div class="row mb-2">
                                <div class="col-md-2">
                                    <label class="form-label">Tunkel</label>
                                    <select class="form-control" id="tunj_kel" name="tunj_kel">
                                        <option value="Tidak">Tidak</option>
                                        <option value="Ya">Ya</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tunj Anak</label>
                                    <select class="form-control" id="tunj_anak" name="tunj_anak">
                                        <option value="Tidak">Tidak</option>
                                        <option value="Ya">Ya</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Kehormatan</label>
                                    <select class="form-control" id="kehormatan" name="kehormatan">
                                        <option value="Tidak">Tidak</option>
                                        <option value="Ya">Ya</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Transport/Kehadiran</label>
                                    <select class="form-control select2" id="kategori_trans" name="kategori_trans">
                                        <option value="">-- Pilih --</option>
                                        @foreach($transports as $t)
                                            <option value="{{ $t->id_transport }}">{{ $t->nama_transport }} | {{ $t->kategori_transport }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <input type="text" class="form-control" id="status" name="status" value="Aktif" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 px-4 pb-4">
                            <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal" style="border-radius: 10px; padding: 10px 25px;">Batal</button>
                            <button type="submit" class="btn btn-success" style="background-color: #00a15d; border-radius: 10px; padding: 10px 25px;">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Validasi Silang Tunjangan Ganda (Struktural & Pengajar) -->
        <div class="modal fade" id="modalCekValidasiUmana" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
              <div class="modal-header py-3 px-4 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="modal-title font-weight-bold text-dark mb-0" style="font-size: 1rem;">
                  Validasi Tunjangan Umana'
                </h5>
                <button type="button" class="close text-muted border-0 bg-transparent" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" style="font-size: 1.5rem; line-height: 1;"><span>&times;</span></button>
              </div>
              <div class="modal-body p-4">
                <!-- Info Profil Umana' -->
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                  <div>
                    <span class="text-muted small">Nama:</span>
                    <span class="font-weight-bold text-dark ml-1 me-1" id="val-umana-nama">-</span>
                  </div>
                  <div>
                    <span class="badge badge-light text-muted font-weight-normal" style="font-size: 12px; border: 1px solid #e2e8f0; padding: 4px 8px;">Pengecekan Tunjangan Ganda</span>
                  </div>
                </div>

                <!-- TABEL STRUKTURAL -->
                <h6 class="font-weight-bold text-dark mb-2" style="font-size: 0.85rem;">
                  Penempatan Struktural
                </h6>
                <div class="table-responsive mb-4">
                  <table class="table table-sm table-hover align-middle mb-0" style="font-size: 13px;">
                    <thead class="table-light">
                      <tr style="font-size: 11px; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px;">
                        <th style="width: 35px;" class="text-center">No</th>
                        <th>Nama</th>
                        <th>Lembaga</th>
                        <th class="text-center">Sertifikasi</th>
                        <th class="text-center">Tunkel</th>
                        <th class="text-center">Tunj Anak</th>
                        <th class="text-center">Kehormatan</th>
                      </tr>
                    </thead>
                    <tbody id="val-tbody-struktural">
                      <tr><td colspan="7" class="text-center text-muted py-3">Memuat data...</td></tr>
                    </tbody>
                  </table>
                </div>

                <!-- TABEL PENGAJAR -->
                <h6 class="font-weight-bold text-dark mb-2" style="font-size: 0.85rem;">
                  Penugasan Pengajar (Guru / Dosen)
                </h6>
                <div class="table-responsive">
                  <table class="table table-sm table-hover align-middle mb-0" style="font-size: 13px;">
                    <thead class="table-light">
                      <tr style="font-size: 11px; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px;">
                        <th style="width: 35px;" class="text-center">No</th>
                        <th>Nama</th>
                        <th>Lembaga</th>
                        <th class="text-center">Sertifikasi</th>
                        <th class="text-center">GTY/DTY</th>
                        <th class="text-center">Tunkel</th>
                        <th class="text-center">Tun Anak</th>
                        <th class="text-center">Kehormatan</th>
                      </tr>
                    </thead>
                    <tbody id="val-tbody-pengajar">
                      <tr><td colspan="8" class="text-center text-muted py-3">Memuat data...</td></tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="modal-footer py-2 px-4 bg-white border-top">
                <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal" data-dismiss="modal" style="border: 1px solid #cbd5e1; border-radius: 4px; font-weight: 500; color: #475569;">Tutup</button>
              </div>
            </div>
          </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Select2 in Modal fix
            $('.select2').select2({
                dropdownParent: $('#modalPenempatan')
            });

            var table = $('#table-penempatan').DataTable({
                processing: true,
                serverSide: false,
                orderCellsTop: true,
                fixedHeader: true,
                ajax: '{{ url("/manajemen-umana/penempatan") }}',
                initComplete: function () {
                    var api = this.api();
                    api.columns().eq(0).each(function (colIdx) {
                        var cell = $('.filters th').eq($(api.column(colIdx).header()).index());
                        $('input', cell).off('keyup change').on('keyup change', function (e) {
                            if (e.keyCode === 13 || e.type === 'change') {
                                api.column(colIdx).search(this.value).draw();
                            }
                        });
                    });
                },
                columns: [
                    { 
                        data: null, 
                        render: function (data, type, row, meta) {
                            return '<strong>' + (meta.row + 1) + '</strong>';
                        } 
                    },
                    { 
                        data: 'umana',
                        render: function(data, type, row) {
                            if(!data) return '-';
                            var gelarDepan = data.gelar_depan ? data.gelar_depan.trim() + ' ' : '';
                            var gelarBelakang = data.gelar_belakang ? ', ' + data.gelar_belakang.trim() : '';
                            var rawNama = data.nama_lengkap || '';
                            var nama = rawNama.toLowerCase().replace(/\b\w/g, function(l) { return l.toUpperCase(); });
                            var fullNama = (gelarDepan + nama + gelarBelakang).trim();
                            if (type === 'filter' || type === 'sort') {
                                return fullNama + ' ' + rawNama + ' ' + (row.nik || '');
                            }
                            return `
                                <a href="javascript:void(0)" class="text-dark font-weight-bold link-cek-umana text-decoration-none" 
                                   data-nik="${row.nik || ''}" 
                                   data-nama="${fullNama.replace(/"/g, '&quot;')}" 
                                   title="Klik untuk cek validasi tunjangan ganda (Struktural & Pengajar)">
                                   <strong>${fullNama}</strong> <i class="fas fa-info-circle text-primary ms-1" style="font-size: 11px; opacity: 0.7;"></i>
                                </a>
                            `;
                        }
                    },
                    { 
                        data: 'lembaga',
                        render: function(data, type, row) {
                            var lembagaNama = data ? data.nama_lembaga : (row.jabatan_lembaga ? row.jabatan_lembaga : '-');
                            var ketentuanNama = row.ketentuan ? row.ketentuan.nama_jabatan : '-';
                            return '<strong>' + lembagaNama + '</strong><br><small class="text-success badge badge-success light badge-sm mt-1">' + ketentuanNama + '</small>';
                        }
                    },
                    { 
                        data: 'tgl_mulai',
                        render: function(data, type, row) {
                            var mulai = data ? data : '-';
                            var selesai = row.tgl_selesai ? row.tgl_selesai : 'Sekarang';
                            return '<strong>' + mulai + '<br>Sampai<br>' + selesai + '</strong>';
                        }
                    },
                    {
                        data: 'umana',
                        render: function(data, type, row) {
                            var rawStatus = (data && data.status_sertifikasi) ? data.status_sertifikasi.trim().toLowerCase() : '';
                            var status = 'Belum';
                            var badgeClass = 'badge-danger'; // Warna merah/abu untuk Belum

                            if (rawStatus === 'sudah sertifikasi' || rawStatus === 'ya' || rawStatus === 'sudah') {
                                status = 'Sudah';
                                badgeClass = 'badge-success'; // Warna hijau untuk Sudah
                            } else if (rawStatus && rawStatus !== 'belum' && rawStatus !== 'tidak' && rawStatus !== 'tidak ada') {
                                // Jika ada status lain (misal: "Sedang Proses")
                                status = data.status_sertifikasi;
                                badgeClass = 'badge-info';
                            }
                            
                            return '<span class="badge ' + badgeClass + ' rounded-pill">' + status + '</span>';
                        }
                    },
                    { 
                        data: null,
                        render: function(data, type, row) {
                            var badges = '';
                            if(row.tunj_kel === 'Ya' || row.tunj_kel === '1') {
                                badges += '<span class="badge badge-info rounded-pill mb-1 me-1"><i class="fas fa-users me-1"></i> Tunkel</span> ';
                            }
                            if(row.tunj_anak === 'Ya' || row.tunj_anak === '1') {
                                badges += '<span class="badge badge-danger rounded-pill mb-1 me-1"><i class="fas fa-child me-1"></i> Tunj Anak</span> ';
                            }
                            if(row.kehormatan === 'Ya' || row.kehormatan === '1') {
                                badges += '<span class="badge badge-success rounded-pill mb-1 me-1"><i class="fas fa-star me-1"></i> Kehormatan</span> ';
                            }
                            
                            if(badges === '') {
                                badges = '<span class="badge badge-warning rounded-pill mb-1"><i class="fas fa-ban me-1"></i> Tidak Ada</span>';
                            }
                            return badges;
                        }
                    },
                    { 
                        data: null,
                        render: function(data, type, row) {
                            var penempatanStr = encodeURIComponent(JSON.stringify(row));
                            return '<button type="button" class="btn btn-outline-success btn-sm shadow-sm btn-edit-penempatan" style="border-radius: 20px; padding: 5px 15px;" data-penempatan="' + penempatanStr + '">' +
                                   '<i class="fas fa-edit me-1"></i> Edit' +
                                   '</button>';
                        }
                    }
                ],
                language: {
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>' 
                    }
                }
            });

            // Auto-fill logic
            $('#nik').on('change', function() {
                var selected = $(this).find('option:selected');
                $('#niy').val(selected.data('niy') || '');
                $('#nama_lengkap').val(selected.data('nama') || '');
                
                var sert = selected.data('sertifikasi');
                var rawSert = (sert) ? sert.toString().trim().toLowerCase() : '';
                var isSudah = (rawSert !== '' && rawSert !== 'belum' && rawSert !== 'tidak' && rawSert !== 'tidak ada' && rawSert !== '0');

                // Tampilkan pesan hint kecil atau tidak usah dikunci agar admin bisa mengaudit
                if (isSudah) {
                    $('#tunj_anak').css('background-color', '#fff');
                    $('#kehormatan').css('background-color', '#fff');
                }
            });

            $('#id_ketentuan').on('change', function() {
                var selected = $(this).find('option:selected');
                $('#nama_jabatan').val(selected.data('nama') || '');
            });

            // Modal Tambah
            $('.btn-tambah-penempatan').on('click', function() {
                $('#modalLabel').text('Tambah Penempatan Umana');
                $('#formPenempatan')[0].reset();
                $('#inputId').val('');
                $('#nik').val('').trigger('change');
                $('#id_ketentuan').val('').trigger('change');
                $('#id_lembaga').val('').trigger('change');
            });

            // Modal Edit
            $('#table-penempatan tbody').on('click', '.btn-edit-penempatan', function() {
                var data = JSON.parse(decodeURIComponent($(this).data('penempatan')));
                $('#modalLabel').text('Edit Penempatan Umana');
                $('#inputId').val(data.id_penempatan);
                
                $('#nik').val(data.nik).trigger('change');
                $('#id_ketentuan').val(data.id_ketentuan).trigger('change');
                $('#id_lembaga').val(data.id_lembaga).trigger('change');
                
                $('#nama_jabatan').val(data.jabatan_lembaga);
                $('#tgl_mulai').val(data.tgl_mulai);
                $('#tgl_selesai').val(data.tgl_selesai);
                
                // Normal select
                $('#tunj_kel').val(data.tunj_kel);
                $('#tunj_anak').val(data.tunj_anak);
                $('#kehormatan').val(data.kehormatan);
                
                // Select2
                $('#kategori_trans').val(data.kategori_trans).trigger('change');
                
                $('#status').val(data.status);
                
                $('#modalPenempatan').modal('show');
            });

            // Submit Form
            $('#formPenempatan').on('submit', function(e) {
                e.preventDefault();
                var id = $('#inputId').val();
                var url = id ? '{{ url("/manajemen-umana/penempatan/update") }}/' + id : '{{ url("/manajemen-umana/penempatan/store") }}';
                
                var formData = new FormData(this);
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#modalPenempatan').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Tersimpan!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        table.ajax.reload(null, false);
                    },
                    error: function(err) {
                        if (err.status === 422) {
                            var errors = err.responseJSON.errors;
                            var errorMsg = '';
                            for (var key in errors) {
                                errorMsg += errors[key][0] + '<br>';
                            }
                            Swal.fire({
                                title: 'Validasi Gagal',
                                html: errorMsg,
                                icon: 'warning'
                            });
                        } else {
                            Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
                        }
                    }
                });
            });

            // Hapus Data
            $('#table-penempatan tbody').on('click', '.btn-delete-penempatan', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data penempatan ini akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.value || result.isConfirmed) {
                        $.ajax({
                            url: '{{ url("/manajemen-umana/penempatan/delete") }}/' + id,
                            type: 'POST',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(res) {
                                Swal.fire('Terhapus!', res.message, 'success');
                                table.ajax.reload(null, false);
                            },
                            error: function() {
                                Swal.fire('Error', 'Terjadi kesalahan saat menghapus data', 'error');
                            }
                        });
                    }
                });
            });

            // Helper format Title Case
            function toTitleCase(str) {
                if (!str) return '';
                return str.toLowerCase().replace(/\b\w/g, function(l) { return l.toUpperCase(); });
            }

            // Modal Validasi Silang Umana' (Tunjangan Ganda Struktural vs Pengajar)
            $(document).on('click', '.link-cek-umana', function() {
                const nik = $(this).data('nik');
                const namaDefault = $(this).data('nama');

                if (!nik) {
                    Swal.fire('Info', 'NIK Umana tidak ditemukan.', 'warning');
                    return;
                }

                $('#val-umana-nama').text(toTitleCase(namaDefault));
                $('#val-tbody-struktural').html('<tr><td colspan="7" class="text-center text-muted">Memuat data penempatan struktural...</td></tr>');
                $('#val-tbody-pengajar').html('<tr><td colspan="8" class="text-center text-muted">Memuat data penugasan pengajar...</td></tr>');

                $('#modalCekValidasiUmana').modal('show');

                $.getJSON("{{ url('/kehadiran_struktural/cekValidasiUmana') }}/" + nik, function(res) {
                    if(!res || !res.status) {
                        $('#val-tbody-struktural').html('<tr><td colspan="7" class="text-center text-danger">Gagal memuat data.</td></tr>');
                        $('#val-tbody-pengajar').html('<tr><td colspan="8" class="text-center text-danger">Gagal memuat data.</td></tr>');
                        return;
                    }

                    // Update profil
                    let formattedNama = toTitleCase(res.umana.nama_lengkap);
                    $('#val-umana-nama').text(formattedNama);

                    const sertifVal = res.umana.status_sertifikasi || 'Belum Sertifikasi';

                    const yaBadge = '<span class="badge" style="background-color: #e8f5e9; color: #1b5e20; font-weight: 600; font-size: 11px; padding: 2px 7px; border-radius: 4px; border: 1px solid #c8e6c9;"><i class="fa fa-check mr-1 me-1"></i>Ya</span>';
                    const tidakBadge = '<span class="text-muted" style="font-size: 12px;">-</span>';

                    // 1. Render Tabel Struktural
                    let htmlStruk = '';
                    if(res.struktural && res.struktural.length > 0) {
                        res.struktural.forEach((item, idx) => {
                            const tunkelBadge = (item.tunj_kel === 'Ya') ? yaBadge : tidakBadge;
                            const tunjAnakBadge = (item.tunj_anak === 'Ya') ? yaBadge : tidakBadge;
                            const kehormatanBadge = (item.kehormatan === 'Ya') ? yaBadge : tidakBadge;

                            htmlStruk += `<tr>
                                <td class="text-center font-weight-bold text-muted">${idx + 1}</td>
                                <td><strong>${formattedNama}</strong><br><small class="text-muted">${item.nama_jabatan || '-'}</small></td>
                                <td><strong>${item.nama_lembaga || '-'}</strong></td>
                                <td class="text-center">${sertifVal}</td>
                                <td class="text-center">${tunkelBadge}</td>
                                <td class="text-center">${tunjAnakBadge}</td>
                                <td class="text-center">${kehormatanBadge}</td>
                            </tr>`;
                        });
                    } else {
                        htmlStruk = '<tr><td colspan="7" class="text-center text-muted py-3">Tidak memiliki data penempatan struktural lain.</td></tr>';
                    }
                    $('#val-tbody-struktural').html(htmlStruk);

                    // 2. Render Tabel Pengajar
                    let htmlPeng = '';
                    if(res.pengajar && res.pengajar.length > 0) {
                        res.pengajar.forEach((item, idx) => {
                            const tunkelBadge = (item.tunj_kel === 'Ya') ? yaBadge : tidakBadge;
                            const tunjAnakBadge = (item.tunj_anak === 'Ya') ? yaBadge : tidakBadge;
                            const kehormatanBadge = (item.kehormatan === 'Ya') ? yaBadge : tidakBadge;

                            const jenisBadge = item.jenis_pengajar
                                ? `<span class="badge" style="background-color: #f1f5f9; color: #334155; font-weight: 600; font-size: 11px; padding: 2px 7px; border-radius: 4px; border: 1px solid #e2e8f0;">${item.jenis_pengajar}</span>`
                                : '<span class="text-muted">-</span>';

                            htmlPeng += `<tr>
                                <td class="text-center font-weight-bold text-muted">${idx + 1}</td>
                                <td><strong>${formattedNama}</strong></td>
                                <td><strong>${item.nama_lembaga || '-'}</strong></td>
                                <td class="text-center">${sertifVal}</td>
                                <td class="text-center">${jenisBadge}</td>
                                <td class="text-center">${tunkelBadge}</td>
                                <td class="text-center">${tunjAnakBadge}</td>
                                <td class="text-center">${kehormatanBadge}</td>
                            </tr>`;
                        });
                    } else {
                        htmlPeng = '<tr><td colspan="8" class="text-center text-muted py-3">Tidak memiliki data penugasan pengajar lain.</td></tr>';
                    }
                    $('#val-tbody-pengajar').html(htmlPeng);
                }).fail(function() {
                    $('#val-tbody-struktural').html('<tr><td colspan="7" class="text-center text-danger">Gagal memanggil data server.</td></tr>');
                    $('#val-tbody-pengajar').html('<tr><td colspan="8" class="text-center text-danger">Gagal memanggil data server.</td></tr>');
                });
            });
        });
    </script>
@endpush
@endsection
