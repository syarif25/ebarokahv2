@extends('layouts.app')
@section('title', 'Barokah Kepanitiaan')
@section('page_title')
<span class="text-danger font-weight-bold">Kepanitiaan</span> / Barokah Kepanitiaan
@endsection

@push('styles')
    <!-- DataTables -->
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        #table-kepanitiaan.dataTable tbody tr {
            border-bottom: 1px solid #f1f1f1;
        }
        #table-kepanitiaan.dataTable, #table-kepanitiaan.dataTable th, #table-kepanitiaan.dataTable td {
            border-top: none;
            border-left: none;
            border-right: none;
        }
        #table-kepanitiaan.dataTable thead th {
            border-bottom: 2px solid #f1f1f1;
            background-color: #fff;
            color: #333;
            font-weight: 600;
        }
        .action-pill {
            border-radius: 20px !important;
            padding: 4px 12px !important;
            font-size: 12px !important;
            font-weight: 500 !important;
            white-space: nowrap !important;
            display: inline-flex !important;
            align-items: center !important;
            line-height: 1.5 !important;
        }
        .btn-tambah-blanko {
            background-color: #00a15d !important; 
            color: white !important; 
            border-radius: 30px; 
            padding: 9px 22px;
            border: none;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0, 161, 93, 0.2);
            transition: all 0.3s ease;
        }
        .btn-tambah-blanko:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        .badge-status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        /* Border hijau tipis untuk seluruh inputan di modal */
        #modalTambahBlanko .form-control,
        #modalTambahBlanko select,
        #modalTambahBlanko input {
            border: 1px solid #1ea566 !important;
            border-radius: 8px !important;
        }
        #modalTambahBlanko .form-control:focus,
        #modalTambahBlanko select:focus,
        #modalTambahBlanko input:focus {
            border-color: #00a15d !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 161, 93, 0.2) !important;
        }
        #modalTambahBlanko .select2-container--default .select2-selection--single {
            height: 38px !important;
            border: 1px solid #1ea566 !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
        }

        /* Styling Filter Row agar seragam & jelas */
        .filter-label {
            font-size: 11px;
            font-weight: 700;
            color: #1e293b !important; /* Warna label gelap dan tegas */
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            display: block;
        }
        .filter-select {
            border: 1px solid #cbd5e1 !important;
            border-radius: 12px !important;
            height: 42px !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: #0f172a !important; /* Warna teks gelap pekat */
            background-color: #ffffff !important;
            padding: 8px 14px !important;
            transition: all 0.2s ease !important;
            cursor: pointer;
        }
        .filter-select:focus {
            border-color: #1ea566 !important;
            box-shadow: 0 0 0 3px rgba(30, 165, 102, 0.15) !important;
            outline: none !important;
            color: #0f172a !important;
        }
        .filter-select option {
            color: #0f172a !important;
            font-weight: 500;
            font-size: 13.5px;
            background-color: #ffffff;
        }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 14px;">
            <div class="card-body">
                <!-- Header Card & Tombol Tambah Blanko -->
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-4" style="gap: 12px;">
                    <div>
                        <!-- <h4 class="card-title text-dark font-weight-bold mb-1">Daftar Barokah Kepanitiaan</h4> -->
                        <!-- <p class="text-muted mb-0" style="font-size: 13px;">Pengelolaan barokah kepanitiaan kegiatan</p> -->
                    </div>
                    <div class="d-flex" style="gap: 8px;">
                        <button type="button" class="btn btn-tambah-blanko" id="btnTambahEvent" data-bs-toggle="modal" data-bs-target="#modalTambahBlanko">
                            <i class="fa fa-plus-circle mr-1"></i> Tambah Kegiatan Kepanitiaan
                        </button>
                    </div>
                </div>

                <!-- Filter Row -->
                <div class="row mb-3 align-items-end" style="gap: 10px 0;">
                    <div class="col-md-3 col-sm-6">
                        <label class="filter-label">FILTER LEMBAGA</label>
                        <select id="filterLembaga" class="form-control form-control-sm filter-select">
                            <option value="">Semua</option>
                            @foreach($lembagas as $l)
                                <option value="{{ $l->id_lembaga }}">{{ $l->nama_lembaga }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <label class="filter-label">JENIS KEGIATAN</label>
                        <select id="filterJenis" class="form-control form-control-sm filter-select">
                            <option value="">Semua Jenis Kegiatan</option>
                            @foreach($jenisKegiatan as $jk)
                                <option value="{{ $jk->id_jenis_kegiatan }}">{{ $jk->nama_kegiatan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <label class="filter-label">BULAN</label>
                        <select id="filterBulan" class="form-control form-control-sm filter-select">
                            <option value="">Semua Bulan</option>
                            @php
                                $bulans = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                            @endphp
                            @foreach($bulans as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <label class="filter-label">TAHUN</label>
                        <select id="filterTahun" class="form-control form-control-sm filter-select">
                            <option value="">Semua Tahun</option>
                            <option value="2026" selected>2026</option>
                            <option value="2025">2025</option>
                            <option value="2027">2027</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <label class="filter-label">STATUS</label>
                        <select id="filterStatus" class="form-control form-control-sm filter-select">
                            <option value="">Semua Status</option>
                            <option value="draft">Draft</option>
                            <option value="pengajuan">Pengajuan</option>
                            <option value="disetujui">Disetujui</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table id="table-kepanitiaan" class="table table-hover w-100">
                        <thead>
                            <tr>
                                <th style="width: 30px;">No</th>
                                <th>Nama Kegiatan</th>
                                <th>Jenis Kegiatan</th>
                                <th>Lembaga</th>
                                <th>Periode</th>
                                <th class="text-center">Jml Panitia</th>
                                <th class="text-right">Total Barokah</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH / EDIT EVENT KEPANITIAAN -->
<div class="modal fade" id="modalTambahBlanko" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 14px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold text-dark" id="modalBlankoTitle">Tambah Event Kepanitiaan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTambahBlanko">
                @csrf
                <input type="hidden" id="id_transaksi_edit" value="">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark" style="font-size: 12px;">NAMA EVENT / KEGIATAN <span class="text-danger">*</span></label>
                        <input type="text" name="nama_event" id="input_nama_event" class="form-control" placeholder="Contoh: UAS Semester Gasal 2025/2026" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark" style="font-size: 12px;">JENIS KEGIATAN <span class="text-danger">*</span></label>
                        <select name="id_jenis_kegiatan" id="input_jenis_kegiatan" class="form-control" required>
                            <option value="">-- Pilih Jenis Kegiatan --</option>
                            @foreach($jenisKegiatan as $jk)
                                <option value="{{ $jk->id_jenis_kegiatan }}">{{ $jk->nama_kegiatan }} ({{ strtoupper($jk->tingkat) }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Nominal akan otomatis disesuaikan dengan jenis kegiatan yang dipilih.</small>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark" style="font-size: 12px;">LEMBAGA PELAKSANA</label>
                        <select name="id_lembaga" id="input_lembaga" class="form-control select2-modal">
                            <option value="">-- Pilih --</option>
                            @foreach($lembagas as $l)
                                <option value="{{ $l->id_lembaga }}">{{ $l->nama_lembaga }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Kosongkan jika scope event mencakup lintas fakultas / pusat.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-dark" style="font-size: 12px;">BULAN <span class="text-danger">*</span></label>
                                <select name="bulan" id="input_bulan" class="form-control" required>
                                    @foreach($bulans as $b)
                                        <option value="{{ $b }}" {{ $b == date('F') ? 'selected' : '' }}>{{ $b }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="font-weight-bold text-dark" style="font-size: 12px;">TAHUN ANGGARAN <span class="text-danger">*</span></label>
                                <input type="text" name="tahun" id="input_tahun" class="form-control" value="2026" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnSimpanBlanko"><i class="fa fa-save mr-1"></i> Simpan & Buat</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL VALIDASI PIMPINAN -->
<div class="modal fade" id="modalValidasi" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow" style="border-radius: 14px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold text-dark">Validasi Pengajuan Kepanitiaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formValidasi">
                @csrf
                <input type="hidden" name="id_transaksi_validasi" id="id_transaksi_validasi">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark" style="font-size: 12px;">KEPUTUSAN VALIDASI <span class="text-danger">*</span></label>
                        <select name="status" id="validasiStatus" class="form-control" required>
                            <option value="disetujui">Setujui (ACC Barokah)</option>
                            <option value="ditolak">Tolak (Kembalikan dengan Catatan)</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark" style="font-size: 12px;">CATATAN PIMPINAN (OPSIONAL)</label>
                        <textarea name="catatan_pimpinan" id="validasiCatatan" class="form-control" rows="3" placeholder="Tuliskan catatan arahan atau perbaikan jika ditolak..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnProsesValidasi">Proses Keputusan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <!-- DataTables -->
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({ width: '100%' });
            $('.select2-modal').select2({ dropdownParent: $('#modalTambahBlanko'), width: '100%' });

            const isSuperAdmin = @json(Auth::user() ? (Auth::user()->isSuperAdmin() || Auth::user()->isEvaluasi()) : false);

            const table = $('#table-kepanitiaan').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '{{ url("/kepanitiaan") }}',
                    data: function(d) {
                        d.id_lembaga = $('#filterLembaga').val();
                        d.id_jenis_kegiatan = $('#filterJenis').val();
                        d.bulan = $('#filterBulan').val();
                        d.tahun = $('#filterTahun').val();
                        d.status = $('#filterStatus').val();
                    }
                },
                columns: [
                    { data: null, render: (d, t, r, meta) => meta.row + 1, className: 'text-center' },
                    { 
                        data: 'nama_event',
                        render: function(data, type, row) {
                            return `<div class="font-weight-bold text-dark">${data}</div>`;
                        }
                    },
                    { data: 'jenis_kegiatan' },
                    { data: 'nama_lembaga' },
                    { 
                        data: null, 
                        render: function(data) {
                            return `${data.bulan} ${data.tahun}`;
                        }
                    },
                    { data: 'jumlah_panitia', className: 'text-center' },
                    { 
                        data: 'jumlah_total',
                        className: 'text-right font-weight-bold',
                        render: function(data) {
                            return 'Rp ' + Number(data).toLocaleString('id-ID');
                        }
                    },
                    { 
                        data: 'status',
                        className: 'text-center',
                        render: function(status) {
                            let badgeClass = 'badge-secondary';
                            let label = status.toUpperCase();
                            if (status === 'draft') badgeClass = 'badge-light text-dark border';
                            else if (status === 'pengajuan') badgeClass = 'badge-warning text-white';
                            else if (status === 'disetujui') badgeClass = 'badge-success';
                            else if (status === 'ditolak') badgeClass = 'badge-danger';
                            return `<span class="badge ${badgeClass} badge-status">${label}</span>`;
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        orderable: false,
                        render: function(data) {
                            let btnKoreksi = `<a href="{{ url('/kepanitiaan') }}/${data.id_kepanitiaan_transaksi}/rincian" class="btn btn-outline-primary btn-xs action-pill mr-1" title="Input & Rincian Data Panitia"><i class="fa fa-list-ul mr-1"></i> Rincian</a>`;
                            let btnCetak = `<a href="{{ url('/kepanitiaan/cetak') }}/${data.id_kepanitiaan_transaksi}" target="_blank" class="btn btn-outline-info btn-xs action-pill mr-1" title="Cetak Amprah"><i class="fa fa-print"></i></a>`;
                            let btnEdit = '';
                            let btnHapus = '';
                            let btnKirim = '';
                            let btnValidasi = '';
                            let btnReset = '';

                            if (data.status === 'draft' || data.status === 'ditolak' || isSuperAdmin) {
                                const encoded = encodeURIComponent(JSON.stringify(data));
                                btnEdit = `<button type="button" onclick="editEvent('${encoded}')" class="btn btn-outline-secondary btn-xs action-pill mr-1" title="Edit Event"><i class="fa fa-pencil-alt"></i></button>`;
                                btnHapus = `<button type="button" onclick="hapusEvent(${data.id_kepanitiaan_transaksi}, '${(data.nama_event || '').replace(/'/g, "\\'")}')" class="btn btn-outline-danger btn-xs action-pill mr-1" title="Hapus Event"><i class="fa fa-trash"></i></button>`;
                            }

                            if (data.status === 'draft' || data.status === 'ditolak') {
                                btnKirim = `<button type="button" onclick="kirimPengajuan(${data.id_kepanitiaan_transaksi})" class="btn btn-outline-success btn-xs action-pill mr-1" title="Kirim Pengajuan"><i class="fa fa-paper-plane"></i></button>`;
                            }

                            if (isSuperAdmin && data.status === 'pengajuan') {
                                btnValidasi = `<button type="button" onclick="bukaModalValidasi(${data.id_kepanitiaan_transaksi})" class="btn btn-success btn-xs action-pill mr-1" title="Validasi Pimpinan"><i class="fa fa-check-circle"></i></button>`;
                            }

                            if (isSuperAdmin && data.status !== 'draft') {
                                btnReset = `<button type="button" onclick="resetStatus(${data.id_kepanitiaan_transaksi})" class="btn btn-outline-warning btn-xs action-pill" title="Reset ke Draft"><i class="fa fa-undo"></i></button>`;
                            }

                            return `<div class="d-flex justify-content-center" style="gap: 4px;">${btnKoreksi}${btnEdit}${btnCetak}${btnKirim}${btnValidasi}${btnReset}${btnHapus}</div>`;
                        }
                    }
                ],
                order: [[0, 'desc']],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampil _MENU_",
                    zeroRecords: "Belum ada event kepanitiaan.",
                    emptyTable: "Belum ada event kepanitiaan.",
                    info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ data",
                    paginate: { next: "›", previous: "‹" }
                }
            });

            // Trigger Filter
            $('#filterLembaga, #filterJenis, #filterBulan, #filterTahun, #filterStatus').on('change', function() {
                table.ajax.reload();
            });

            // Buka Modal Tambah Baru
            $('#btnTambahEvent').on('click', function() {
                $('#modalBlankoTitle').text('Tambah Event Kepanitiaan Baru');
                $('#btnSimpanBlanko').prop('disabled', false).html('<i class="fa fa-save mr-1"></i> Simpan & Buat');
                $('#formTambahBlanko')[0].reset();
                $('#id_transaksi_edit').val('');
                $('#input_lembaga').val('').trigger('change');
                $('#input_tahun').val('2026');
                $('#modalTambahBlanko').modal('show');
            });

            // Action: Edit Event
            window.editEvent = function(encodedData) {
                const d = JSON.parse(decodeURIComponent(encodedData));
                $('#modalBlankoTitle').text('Edit Event Kepanitiaan');
                $('#btnSimpanBlanko').prop('disabled', false).html('<i class="fa fa-save mr-1"></i> Perbarui');
                $('#id_transaksi_edit').val(d.id_kepanitiaan_transaksi);
                $('#input_nama_event').val(d.nama_event);
                $('#input_jenis_kegiatan').val(d.id_jenis_kegiatan);
                $('#input_lembaga').val(d.id_lembaga || '').trigger('change');
                $('#input_bulan').val(d.bulan);
                $('#input_tahun').val(d.tahun);
                $('#modalTambahBlanko').modal('show');
            };

            // Action: Hapus Event
            window.hapusEvent = function(id, nama) {
                Swal.fire({
                    title: 'Hapus Event Ini?',
                    text: `Event "${nama}" beserta seluruh rincian panitia di dalamnya akan dihapus permanen!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#aaa',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.value || result.isConfirmed) {
                        $.post(`{{ url('/kepanitiaan/delete') }}/${id}`, { _token: '{{ csrf_token() }}' }, function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            table.ajax.reload();
                        }).fail(function(err) {
                            Swal.fire('Gagal!', err.responseJSON ? err.responseJSON.message : 'Tidak dapat menghapus event.', 'error');
                        });
                    }
                });
            };

            // Submit Blanko Baru / Update
            $('#formTambahBlanko').on('submit', function(e) {
                e.preventDefault();
                const idEdit = $('#id_transaksi_edit').val();
                const url = idEdit ? `{{ url("/kepanitiaan/update") }}/${idEdit}` : '{{ url("/kepanitiaan/store") }}';
                const btn = $('#btnSimpanBlanko');
                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#modalTambahBlanko').modal('hide');
                        $('#formTambahBlanko')[0].reset();
                        btn.prop('disabled', false).html('<i class="fa fa-save mr-1"></i> Simpan & Buat');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        table.ajax.reload();
                        // Langsung arahkan ke halaman rincian input panitia jika buat baru
                        if (!idEdit && res.id) {
                            window.location.href = `{{ url('/kepanitiaan') }}/${res.id}/rincian`;
                        }
                    },
                    error: function(err) {
                        btn.prop('disabled', false).html(idEdit ? '<i class="fa fa-save mr-1"></i> Perbarui' : '<i class="fa fa-save mr-1"></i> Simpan & Buat');
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: err.responseJSON ? err.responseJSON.message : 'Terjadi kesalahan sistem.'
                        });
                    }
                });
            });

            // Action: Kirim Pengajuan
            window.kirimPengajuan = function(id) {
                Swal.fire({
                    title: 'Kirim Pengajuan?',
                    text: 'Event ini akan diajukan ke Pimpinan/Evaluasi untuk divalidasi.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#00a15d',
                    cancelButtonColor: '#aaa',
                    confirmButtonText: 'Ya, Kirim!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.value || result.isConfirmed) {
                        $.post(`{{ url('/kepanitiaan/kirim') }}/${id}`, { _token: '{{ csrf_token() }}' }, function(res) {
                            Swal.fire('Berhasil!', res.message, 'success');
                            table.ajax.reload();
                        }).fail(function() {
                            Swal.fire('Gagal!', 'Tidak dapat memproses pengajuan.', 'error');
                        });
                    }
                });
            };

            // Action: Buka Modal Validasi
            window.bukaModalValidasi = function(id) {
                $('#id_transaksi_validasi').val(id);
                $('#modalValidasi').modal('show');
            };

            // Submit Validasi
            $('#formValidasi').on('submit', function(e) {
                e.preventDefault();
                const id = $('#id_transaksi_validasi').val();
                $.post(`{{ url('/kepanitiaan/validasi') }}/${id}`, $(this).serialize(), function(res) {
                    $('#modalValidasi').modal('hide');
                    $('#formValidasi')[0].reset();
                    Swal.fire('Tervalidasi!', res.message, 'success');
                    table.ajax.reload();
                }).fail(function() {
                    Swal.fire('Gagal!', 'Gagal memproses validasi.', 'error');
                });
            });

            // Action: Reset Status ke Draft
            window.resetStatus = function(id) {
                Swal.fire({
                    title: 'Reset ke Draft?',
                    text: 'Status akan dikembalikan ke Draft agar bisa diedit kembali.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#aaa',
                    confirmButtonText: 'Ya, Reset!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.value || result.isConfirmed) {
                        $.post(`{{ url('/kepanitiaan/reset') }}/${id}`, { _token: '{{ csrf_token() }}' }, function(res) {
                            Swal.fire('Direset!', res.message, 'success');
                            table.ajax.reload();
                        }).fail(function() {
                            Swal.fire('Gagal!', 'Gagal mereset status.', 'error');
                        });
                    }
                });
            };
        });
    </script>
@endpush
