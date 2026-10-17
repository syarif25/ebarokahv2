@extends('layouts.app')
@section('title', 'Kehadiran Struktural')
@section('page_title')
<span class="text-danger font-weight-bold">Kehadiran</span> / Kehadiran Struktural
@endsection

@push('styles')
    <!-- DataTables -->
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Clean Table Style */
        #table-kehadiran.dataTable tbody tr {
            border-bottom: 1px solid #f1f1f1;
        }
        #table-kehadiran.dataTable, #table-kehadiran.dataTable th, #table-kehadiran.dataTable td {
            border-top: none;
            border-left: none;
            border-right: none;
        }
        #table-kehadiran.dataTable thead th {
            border-bottom: 2px solid #f1f1f1;
            background-color: #fff;
            color: #333;
            font-weight: 600;
        }

        /* Filter per Kolom */
        .filters th {
            padding: 6px 8px !important;
            border-bottom: 2px solid #eef2f6 !important;
            background-color: #fff !important;
        }
        .filters input {
            width: 100%;
            padding: 6px 10px;
            font-size: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background-color: #fafafa;
            color: #495057;
            font-weight: normal;
            transition: all 0.2s ease;
        }
        .filters input:focus {
            border-color: #00a15d;
            background-color: #fff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 161, 93, 0.15);
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
            padding: 10px 25px;
            border: none;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0, 161, 93, 0.2);
            transition: all 0.3s ease;
        }
        .btn-tambah-blanko:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-light-pink {
            background-color: #fdeef3 !important;
            color: #d81b60 !important;
            border-radius: 20px !important;
            padding: 8px 25px !important;
            font-weight: 600;
            border: none;
        }
        
        /* Fix Select2 Scroll Issue */
        .select2-results__options {
            max-height: 250px !important;
            overflow-y: auto !important;
        }
        .select2-container .select2-dropdown {
            z-index: 9999 !important;
        }

        #modalBlanko .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid #00a15d !important;
            border-radius: 8px !important;
            display: flex;
            align-items: center;
        }
        #modalBlanko .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
        #modalBlanko .form-control {
            border: 1px solid #00a15d !important;
            border-radius: 8px !important;
            height: 42px;
        }
        #modalBlanko .form-control:focus {
            border-color: #00a15d !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 161, 93, 0.25) !important;
        }

        /* Timeline Audit Trail Riwayat */
        .timeline-vertical { position: relative; padding-left: 2rem !important; margin: 0.5rem 0; list-style: none; }
        .timeline-vertical::before { content: ''; position: absolute; top: 0; bottom: 0; left: 16px; width: 2px; background-color: #e2e8f0; }
        .timeline-item { position: relative; margin-bottom: 1.25rem; }
        .timeline-indicator { position: absolute; left: -2rem; width: 14px; height: 14px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 0 2px #cbd5e1; top: 6px; z-index: 1;}
        .timeline-indicator.status-Belum { background-color: #94a3b8; }
        .timeline-indicator.status-Sudah { background-color: #0284c7; }
        .timeline-indicator.status-Terkirim { background-color: #f59e0b; }
        .timeline-indicator.status-Revisi { background-color: #ef4444; }
        .timeline-indicator.status-acc { background-color: #10b981; }
        .timeline-indicator.status-Reset { background-color: #dc2626; }
        .timeline-content { background: #fff; padding: 0.85rem 1rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; }
        .timeline-date { font-size: 0.75rem; color: #64748b; font-weight: 500; }
        .timeline-title { font-weight: 600; margin-bottom: 0.15rem; font-size: 0.88rem; }
        .timeline-user { font-size: 0.78rem; font-weight: 500; color: #475569; }
    </style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 15px; margin-top: 10px;">
        <div class="card-body py-3">
            <h5 class="mb-0 font-weight-normal"><strong class="text-danger">Kehadiran</strong> <span class="text-muted" style="color: #999 !important;">/ Kehadiran Struktural</span></h5>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                        <h4 class="card-title text-dark font-weight-bold mb-0" style="font-size: 1.2rem;">Daftar Kehadiran Struktural</h4>
                        <button type="button" class="btn btn-sm btn-tambah-blanko" data-bs-toggle="modal" data-bs-target="#modalBlanko" id="btnTambah">
                            <i class="fas fa-plus"></i> Tambah Blanko Kehadiran
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="display" id="table-kehadiran" style="min-width: 845px; width:100%">
                            <thead>
                                <tr>
                                    <th class="font-weight-bold pb-3 pt-2">No</th>
                                    <th class="font-weight-bold pb-3 pt-2">Nama Lembaga</th>
                                    <th class="font-weight-bold pb-3 pt-2">Jumlah Umana'</th>
                                    <th class="font-weight-bold pb-3 pt-2">Bulan</th>
                                    <th class="font-weight-bold pb-3 pt-2">Tahun</th>
                                    <th class="font-weight-bold pb-3 pt-2 text-center">Status</th>
                                    <th class="font-weight-bold pb-3 pt-2 text-center">Aksi</th>
                                </tr>
                                <tr class="filters">
                                    <th></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Filter Lembaga" /></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Filter Jml" /></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Filter Bulan" /></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Filter Tahun" /></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Filter Status" /></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Blanko -->
<div class="modal fade" id="modalBlanko" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 bg-white pb-0 mt-2 px-4">
                <h5 class="modal-title font-weight-bold text-dark" id="modalTitle">Tambah Blanko Input Barokah Lembaga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formBlanko">
                <div class="modal-body bg-white pt-3 px-4">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label" style="font-size: 13px; font-weight: 500;">Nama Lembaga <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="id_lembaga" id="id_lembaga" required style="width: 100%;">
                                <option value="">-- Pilih Lembaga --</option>
                                @foreach($lembagas as $l)
                                    <option value="{{ $l->id_lembaga }}">{{ $l->nama_lembaga }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-size: 13px; font-weight: 500;">Bulan <span class="text-danger">*</span></label>
                            <select class="form-control" name="bulan" id="bulan" required>
                                <option value="">-- Pilih Bulan --</option>
                                <option value="Januari">Januari</option>
                                <option value="Februari">Februari</option>
                                <option value="Maret">Maret</option>
                                <option value="April">April</option>
                                <option value="Mei">Mei</option>
                                <option value="Juni">Juni</option>
                                <option value="Juli">Juli</option>
                                <option value="Agustus">Agustus</option>
                                <option value="September">September</option>
                                <option value="Oktober">Oktober</option>
                                <option value="November">November</option>
                                <option value="Desember">Desember</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-size: 13px; font-weight: 500;">Tahun Anggaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="tahun" id="tahun" required value="2026" readonly style="background-color: #f8f9fa; cursor: not-allowed; font-weight: 600; color: #495057;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light-pink" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn text-white" style="background-color: #00a15d; border-radius: 20px; padding: 8px 25px; font-weight: 600;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Timeline Riwayat Barokah -->
<div class="modal fade" id="modal_riwayat" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 shadow" style="border-radius: 16px; overflow: hidden;">
      <div class="modal-header bg-white border-bottom py-3">
        <h5 class="modal-title font-weight-bold text-dark mb-0 fs-6">
          <i class="fas fa-history text-info me-2"></i> Sinkronisasi Riwayat Barokah
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4" style="background-color: #f8fafc; max-height: 480px;">
         <ul class="timeline-vertical p-0 m-0" id="riwayat_timeline_container">
            <li class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i> Memuat Riwayat...</li>
         </ul>
      </div>
      <div class="modal-footer bg-white border-top py-2">
        <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Reset Status Kehadiran -->
<div class="modal fade" id="modal_reset" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow" style="border-radius: 16px; overflow: hidden;">
      <div class="modal-header bg-white border-bottom py-3">
        <h5 class="modal-title font-weight-bold text-danger mb-0 fs-6">
          <i class="fas fa-undo text-danger me-2"></i> Reset Data Periode Kehadiran
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4" style="background-color: #f8fafc;">
        <input type="hidden" id="reset_target_id">
        <p class="text-muted small mb-3">Pilih jenis tindakan reset yang ingin diterapkan pada periode ini:</p>
        
        <div class="card border mb-3 shadow-none p-3" style="border-radius: 12px; background: #fff; cursor: pointer;" onclick="$('#resetKehadiranRadio').prop('checked', true);">
          <div class="form-check">
            <input class="form-check-input" type="radio" name="modal_reset_mode" id="resetKehadiranRadio" value="kehadiran" checked>
            <label class="form-check-label text-dark fw-bold ms-1" for="resetKehadiranRadio" style="cursor: pointer;">
              Reset Total (Hapus Kehadiran & Kembalikan ke "Belum")
            </label>
          </div>
          <div class="text-muted small ms-4 mt-1">
            Menghapus seluruh isian absensi Umana' dan rekap barokah. Periode kembali menjadi blanko kosong ("Belum diisi").
          </div>
        </div>

        <div class="card border mb-1 shadow-none p-3" style="border-radius: 12px; background: #fff; cursor: pointer;" onclick="$('#resetTotalRadio').prop('checked', true);">
          <div class="form-check">
            <input class="form-check-input" type="radio" name="modal_reset_mode" id="resetTotalRadio" value="total">
            <label class="form-check-label text-dark fw-bold ms-1" for="resetTotalRadio" style="cursor: pointer;">
              Reset Status Saja (Kembalikan ke "Sudah / Belum Terkirim")
            </label>
          </div>
          <div class="text-muted small ms-4 mt-1">
            Data absensi Umana' tetap tersimpan aman. Hanya membatalkan status kirim/validasi sehingga bisa dicek/diedit ulang.
          </div>
        </div>
      </div>
      <div class="modal-footer bg-white border-top py-2 d-flex justify-content-between">
        <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-danger btn-sm px-4 rounded-pill text-white font-weight-bold" id="btnKonfirmasiEksekusiReset">
          <i class="fas fa-undo me-1"></i> Ya, Reset Sekarang
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<!-- DataTables JS -->
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            dropdownParent: $('#modalBlanko')
        });

        var table = $('#table-kehadiran').DataTable({
            processing: true,
            serverSide: false,
            orderCellsTop: true,
            fixedHeader: true,
            ajax: {
                url: '{{ url("/kehadiran_struktural") }}',
                type: 'GET'
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
            },
            columns: [
                { data: null, render: function (data, type, row, meta) { return meta.row + 1; } },
                { data: 'nama_lembaga', render: function(data) { return `<span class="font-weight-bold text-dark">${data}</span>`; }},
                { data: 'jumlah_umana', render: function(data) { return `<span class="font-weight-bold text-dark">${data} org</span>`; }},
                { data: 'bulan', render: function(data) { return `<span class="font-weight-bold text-dark">${data}</span>`; }},
                { data: 'tahun', render: function(data) { return `<span class="font-weight-bold text-dark">${data}</span>`; }},
                { 
                    data: 'status', 
                    render: function(data) { 
                        if(data === 'Belum') {
                            return `<span class="badge" style="background-color: #f1f1f1; color: #666; font-size: 12px; border-radius: 12px; padding: 6px 12px;">Belum diisi <i class="fas fa-times ms-1"></i></span>`;
                        } else if(data === 'Sudah') {
                            return `<span class="badge bg-danger" style="font-size: 12px; border-radius: 12px; padding: 6px 12px;">Belum dikirim <i class="fas fa-exclamation-circle ms-1"></i></span>`;
                        } else if(data === 'Revisi') {
                            return `<span class="badge" style="background-color: #ffe5d0; color: #d97706; border-radius: 12px; font-size: 12px; padding: 6px 12px;"><i class="fas fa-sync-alt me-1"></i> Terdapat Revisi</span>`;
                        } else if(data === 'acc') {
                            return `<span class="badge bg-success text-white" style="font-size: 12px; border-radius: 12px; padding: 6px 12px;"><i class="fas fa-check-circle me-1"></i> Disetujui (ACC)</span>`;
                        } else if(data === 'Terkirim' || data === 'Sedang dikoreksi') {
                            return `<span class="badge" style="background-color: #ffecd6; color: #ff9800; border-radius: 12px; font-size: 12px; padding: 6px 12px;"><i class="fas fa-clock me-1"></i> Sedang dikoreksi</span>`;
                        } else {
                            return `<span class="badge bg-primary text-white" style="font-size: 12px; border-radius: 12px; padding: 6px 12px;"><i class="fas fa-money-check-alt me-1"></i> Sudah transfer</span>`;
                        }
                    },
                    className: "text-center"
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        let id = row.id_kehadiran_lembaga;
                        let status = row.status;
                        let canReset = row.can_reset;
                        let html = '<div class="d-inline-flex align-items-center justify-content-center gap-1 text-nowrap">';

                        if (status === 'Belum') {
                            html += `<a href="{{ url('/kehadiran_struktural') }}/${id}/rekap" class="btn btn-outline-secondary btn-xs action-pill">
                                        <i class="fas fa-file-alt me-1"></i> Isi Rekap Kehadiran
                                     </a>`;
                        } else if (status === 'Sudah' || status === 'Terkirim' || status === 'acc' || status === 'Revisi' || status === 'Sedang dikoreksi') {
                            html += `<a href="{{ url('/kehadiran_struktural') }}/${id}/koreksi" class="btn btn-success btn-xs action-pill text-white" style="background-color: #00a15d; border-color: #00a15d;">
                                        <i class="fas fa-check-circle me-1"></i> Cek Barokah
                                     </a>`;
                        } else {
                            html += `<a href="{{ url('/kehadiran_struktural') }}/${id}/koreksi" class="btn btn-info btn-xs action-pill text-white" style="background-color: #0284c7; border-color: #0284c7;">
                                        <i class="fas fa-file-invoice-dollar me-1"></i> Lihat Laporan
                                     </a>`;
                        }

                        // Tombol Riwayat & Reset rapi sejajar ke samping
                        if (canReset && status !== 'Belum') {
                            html += `<button type="button" class="btn btn-outline-info btn-xs action-pill btn-riwayat" style="color: #0284c7; border-color: #0284c7;" data-id="${id}">
                                        <i class="fas fa-history me-1"></i> Riwayat
                                     </button>`;
                            html += `<button type="button" class="btn btn-outline-danger btn-xs action-pill btn-reset" data-id="${id}" data-status="${status}">
                                        <i class="fas fa-undo me-1"></i> Reset
                                     </button>`;
                        }

                        html += '</div>';
                        return html;
                    },
                    className: "text-center text-nowrap"
                }
            ],
            language: { 
                search: "Search:",
                emptyTable: "Belum ada data kehadiran struktural",
                zeroRecords: "Tidak ada data yang cocok"
            }
        });

        // Tombol Tambah Blanko click
        $('#btnTambah').click(function() {
            $('#formBlanko')[0].reset();
            $('#tahun').val('2026');
            $('#id_lembaga').val('').trigger('change');
            $('#modalTitle').text('Tambah Blanko Input Barokah Lembaga');
        });

        // Submit form
        $('#formBlanko').submit(function(e) {
            e.preventDefault();
            var url = '{{ url("/kehadiran_struktural/storeBlanko") }}';
            var formData = new FormData(this);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    $('#modalBlanko').modal('hide');
                    $('#formBlanko')[0].reset();
                    $('#tahun').val('2026');
                    $('#id_lembaga').val('').trigger('change');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    table.ajax.reload(null, false);
                },
                error: function(err) {
                    if (err.status === 400) {
                        Swal.fire('Peringatan', err.responseJSON.message, 'warning');
                    } else if (err.status === 422) {
                        var errors = err.responseJSON.errors;
                        var errorMsg = '';
                        for (var key in errors) {
                            errorMsg += errors[key][0] + '<br>';
                        }
                        Swal.fire({ title: 'Validasi Gagal', html: errorMsg, icon: 'warning' });
                    } else {
                        Swal.fire('Error', 'Terjadi kesalahan saat menyimpan data', 'error');
                    }
                }
            });
        });

        // Handler Riwayat Log
        $(document).on('click', '.btn-riwayat', function() {
            var id = $(this).data('id');
            $('#riwayat_timeline_container').html('<li class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin fa-2x mb-2 d-block text-primary"></i> Memuat Riwayat...</li>');
            $('#modal_riwayat').modal('show');

            $.get('{{ url("/kehadiran_struktural/riwayat") }}/' + id, function(res) {
                var logs = res.data || [];
                var html = '';
                if(logs.length === 0) {
                    html = '<li class="text-center text-muted py-4"><i class="fas fa-info-circle fa-2x mb-2 d-block text-secondary"></i>Belum ada riwayat tercatat.</li>';
                } else {
                    logs.forEach(function(l) {
                        var statusClass = 'status-Belum';
                        var icon = 'fa-circle';
                        var title = l.status_aksi;
                        var textColor = 'text-dark';

                        if(l.status_aksi === 'Belum') {
                            statusClass = 'status-Belum'; icon = 'fa-file-alt'; title = 'Blanko Dibuat'; textColor = 'text-secondary';
                        } else if(l.status_aksi === 'Sudah') {
                            statusClass = 'status-Sudah'; icon = 'fa-save'; title = 'Disimpan (Draf)'; textColor = 'text-info';
                        } else if(l.status_aksi === 'Terkirim') {
                            statusClass = 'status-Terkirim'; icon = 'fa-paper-plane'; title = 'Terkirim ke Pimpinan / Evaluator'; textColor = 'text-warning';
                        } else if(l.status_aksi === 'Revisi') {
                            statusClass = 'status-Revisi'; icon = 'fa-undo'; title = 'Dikembalikan / Revisi (Ditolak)'; textColor = 'text-danger';
                        } else if(l.status_aksi === 'acc') {
                            statusClass = 'status-acc'; icon = 'fa-check-circle'; title = 'Disetujui (ACC)'; textColor = 'text-success';
                        } else if(l.status_aksi === 'Reset') {
                            statusClass = 'status-Reset'; icon = 'fa-trash'; title = 'Data Direset'; textColor = 'text-danger';
                        }

                        html += `<li class="timeline-item">
                            <div class="timeline-indicator ${statusClass}"></div>
                            <div class="timeline-content shadow-sm">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="timeline-title ${textColor}"><i class="fas ${icon} me-1"></i> ${title}</span>
                                    <span class="timeline-date"><i class="far fa-clock me-1"></i> ${l.waktu_eksekusi}</span>
                                </div>`;
                        
                        if(l.username) {
                            html += `<div class="timeline-user text-muted"><i class="far fa-user me-1"></i> dieksekusi oleh: <strong>${l.username}</strong> (${l.jabatan || 'User'})</div>`;
                        } else if (l.id_pengguna) {
                            html += `<div class="timeline-user text-muted"><i class="far fa-user me-1"></i> dieksekusi oleh ID: ${l.id_pengguna}</div>`;
                        }

                        if(l.catatan_log && l.catatan_log.trim() !== '') {
                            html += `<p class="mb-0 small text-dark border-top pt-2 mt-2" style="background: #f8fafc; padding: 6px 10px; border-radius: 6px;"><em>"${l.catatan_log}"</em></p>`;
                        }
                        html += `</div></li>`;
                    });
                }
                $('#riwayat_timeline_container').html(html);
            }).fail(function() {
                $('#riwayat_timeline_container').html('<li class="text-center text-danger py-4"><i class="fas fa-exclamation-triangle fa-2x mb-2 d-block"></i>Gagal memuat riwayat barokah.</li>');
            });
        });

        // Handler Buka Modal Reset
        $(document).on('click', '.btn-reset', function() {
            var id = $(this).data('id');
            if (!id) {
                alert('ID periode tidak ditemukan.');
                return;
            }
            $('#reset_target_id').val(id);
            $('#resetKehadiranRadio').prop('checked', true);
            $('#modal_reset').modal('show');
        });

        // Handler Konfirmasi Eksekusi Reset
        $('#btnKonfirmasiEksekusiReset').click(function() {
            var id = $('#reset_target_id').val();
            var mode = $('input[name="modal_reset_mode"]:checked').val() || 'kehadiran';
            var $btn = $(this);

            if (!id) return;

            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Memproses...');

            $.ajax({
                url: '{{ url("/kehadiran_struktural/reset") }}',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: '{{ csrf_token() }}',
                    id_kehadiran_lembaga: id,
                    mode: mode
                },
                success: function(rsp) {
                    $btn.prop('disabled', false).html('<i class="fas fa-undo me-1"></i> Ya, Reset Sekarang');
                    $('#modal_reset').modal('hide');

                    if (rsp && rsp.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil di-Reset',
                            text: rsp.message,
                            timer: 1600,
                            showConfirmButton: false
                        });
                        table.ajax.reload(null, false);
                    } else {
                        Swal.fire('Gagal', (rsp && rsp.message) ? rsp.message : 'Reset gagal.', 'error');
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="fas fa-undo me-1"></i> Ya, Reset Sekarang');
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Terjadi kesalahan saat memproses reset.';
                    Swal.fire('Error', msg, 'error');
                }
            });
        });
    });
</script>
@endpush
