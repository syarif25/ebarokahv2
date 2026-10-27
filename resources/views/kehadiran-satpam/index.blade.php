@extends('layouts.app')
@section('title', 'Kehadiran Satpam')
@section('page_title')
<span class="text-danger font-weight-bold">Kehadiran</span> / Kehadiran Satpam
@endsection

@push('styles')
    <!-- DataTables -->
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
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
            padding: 5px 14px !important;
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

        /* Timeline Audit Trail Riwayat */
        .timeline-vertical { position: relative; padding-left: 2rem !important; margin: 0.5rem 0; list-style: none; }
        .timeline-vertical::before { content: ''; position: absolute; top: 0; bottom: 0; left: 16px; width: 2px; background-color: #e2e8f0; }
        .timeline-item { position: relative; margin-bottom: 1.25rem; }
        .timeline-indicator { position: absolute; left: -2rem; width: 14px; height: 14px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 0 2px #cbd5e1; top: 6px; z-index: 1;}
        .timeline-indicator.status-Belum { background-color: #94a3b8; }
        .timeline-indicator.status-Sudah { background-color: #0284c7; }
        .timeline-indicator.status-Terkirim { background-color: #f59e0b; }
        .timeline-indicator.status-acc { background-color: #10b981; }
        .timeline-indicator.status-Reset { background-color: #dc2626; }
        .timeline-content { background: #fff; padding: 0.85rem 1rem; border-radius: 0.75rem; border: 1px solid #e2e8f0; }
        .timeline-date { font-size: 0.75rem; color: #64748b; font-weight: 500; }
        .timeline-title { font-weight: 600; margin-bottom: 0.15rem; font-size: 0.88rem; }
        .timeline-user { font-size: 0.78rem; font-weight: 500; color: #475569; }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="card-title text-dark font-weight-bold mb-0" style="font-size: 1.2rem;">Daftar Kehadiran Satpam</h4>
                <button type="button" class="btn btn-sm btn-tambah-blanko shadow-sm mt-2 mt-sm-0" data-bs-toggle="modal" data-bs-target="#modalBlanko" id="btnTambah">
                    <i class="fas fa-plus me-1"></i> Tambah Blanko Kehadiran
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="display" id="table-kehadiran" style="min-width: 845px; width:100%">
                        <thead>
                            <tr>
                                <th class="font-weight-bold pb-3 pt-2">No</th>
                                <th class="font-weight-bold pb-3 pt-2">Nama Lembaga</th>
                                <th class="font-weight-bold pb-3 pt-2">Jumlah Satpam</th>
                                <th class="font-weight-bold pb-3 pt-2">Bulan</th>
                                <th class="font-weight-bold pb-3 pt-2">Tahun</th>
                                <th class="font-weight-bold pb-3 pt-2 text-center">Status</th>
                                <th class="font-weight-bold pb-3 pt-2 text-center">Aksi</th>
                            </tr>
                            <tr class="filters">
                                <th></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Nama lembaga" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Jumlah Satpam" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Bulan" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Tahun" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Status" /></th>
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

<!-- Modal Tambah Blanko -->
<div class="modal fade" id="modalBlanko" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 bg-white pb-0 mt-2 px-4">
                <h5 class="modal-title font-weight-bold text-dark" id="modalTitle">Tambah Blanko Input Barokah Satpam</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formBlanko">
                <div class="modal-body bg-white pt-3 px-4">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label" style="font-size: 13px; font-weight: 500;">Nama Lembaga <span class="text-danger">*</span></label>
                            <select class="form-control" name="id_lembaga" id="id_lembaga" required style="border: 1px solid #00a15d; border-radius: 8px;">
                                <option value="{{ $lembagaSatpam ? $lembagaSatpam->id_lembaga : 59 }}">
                                    {{ $lembagaSatpam ? $lembagaSatpam->nama_lembaga : 'Satpam' }}
                                </option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label" style="font-size: 13px; font-weight: 500;">Bulan <span class="text-danger">*</span></label>
                            <select class="form-control" name="bulan" id="bulan" required style="border: 1px solid #00a15d; border-radius: 8px;">
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
                            <input type="text" class="form-control" name="tahun" id="tahun" required value="2026" readonly style="background-color: #f8f9fa; cursor: not-allowed; font-weight: 600; color: #495057; border: 1px solid #00a15d; border-radius: 8px;">
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
@endsection

@push('scripts')
<!-- DataTables JS -->
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

<script>
    $(document).ready(function() {
        var table = $('#table-kehadiran').DataTable({
            processing: true,
            serverSide: false,
            orderCellsTop: true,
            fixedHeader: true,
            ajax: {
                url: '{{ url("/kehadiran-satpam") }}',
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
                { data: 'jumlah_satpam', render: function(data) { return `<span class="font-weight-bold text-dark">${data} org</span>`; }},
                { data: 'bulan', render: function(data) { return `<span class="font-weight-bold text-dark">${data}</span>`; }},
                { data: 'tahun', render: function(data) { return `<span class="font-weight-bold text-dark">${data}</span>`; }},
                { 
                    data: 'status', 
                    render: function(data) { 
                        if(data === 'Belum') {
                            return `<span class="badge" style="background-color: #6c757d; color: #fff; font-size: 12px; border-radius: 12px; padding: 6px 12px;">Belum diisi <i class="fas fa-times ms-1"></i></span>`;
                        } else if(data === 'Sudah' || data === 'Terkirim' || data === 'acc' || data === 'Sedang dikoreksi') {
                            return `<span class="badge" style="background-color: #f6c23e; color: #212529; border-radius: 12px; font-size: 12px; padding: 6px 12px;">Sedang dikoreksi <i class="fas fa-redo ms-1"></i></span>`;
                        } else {
                            return `<span class="badge" style="background-color: #1cc88a; color: #fff; font-size: 12px; border-radius: 12px; padding: 6px 12px;">Sudah transfer <i class="fas fa-check ms-1"></i></span>`;
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
                            html += `<a href="{{ url('/kehadiran-satpam') }}/${id}/rekap" class="btn btn-outline-secondary btn-sm action-pill">
                                        <i class="fas fa-file-alt me-1"></i> Isi Rekap Kehadiran
                                     </a>`;
                        } else if (status === 'Sudah' || status === 'Terkirim' || status === 'acc' || status === 'Sedang dikoreksi') {
                            html += `<a href="{{ url('/kehadiran-satpam') }}/${id}/koreksi" class="btn btn-success btn-xs action-pill text-white" style="background-color: #00a15d; border-color: #00a15d;">
                                        <i class="fas fa-check-circle me-1"></i> Cek Barokah
                                     </a>`;
                        } else {
                            html += `<a href="{{ url('/kehadiran-satpam') }}/${id}/rincian" class="btn btn-sm action-pill text-white" style="background-color: #d81b60; border-color: #d81b60;">
                                        <i class="fas fa-file-invoice-dollar me-1"></i> Lihat Laporan Final
                                     </a>`;
                        }

                        // Tombol Riwayat & Reset
                        if (canReset && status !== 'Belum') {
                            html += `<button type="button" class="btn btn-outline-info btn-xs action-pill btn-riwayat" style="color: #0284c7; border-color: #0284c7;" data-id="${id}" title="Riwayat">
                                        <i class="fas fa-history"></i>
                                     </button>`;
                            html += `<button type="button" class="btn btn-outline-danger btn-sm action-pill btn-reset" data-id="${id}">
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
                emptyTable: "Belum ada data kehadiran satpam",
                zeroRecords: "Tidak ada data yang cocok"
            }
        });

        // Tombol Tambah Blanko click
        $('#btnTambah').click(function() {
            $('#formBlanko')[0].reset();
            $('#tahun').val('2026');
            $('#modalTitle').text('Tambah Blanko Input Barokah Satpam');
        });

        // Submit Form Blanko
        $('#formBlanko').submit(function(e) {
            e.preventDefault();
            var url = '{{ url("/kehadiran-satpam/blanko") }}';
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
                    var msg = 'Terjadi kesalahan pada server';
                    if (err.responseJSON && err.responseJSON.message) {
                        msg = err.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: msg
                    });
                }
            });
        });

        // Handler Reset Data
        $(document).on('click', '.btn-reset', function() {
            const idKL = $(this).data('id');

            Swal.fire({
                title: 'Reset data?',
                text: 'Pilih jenis reset yang kamu inginkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Reset total_barokah saja',
                cancelButtonText: 'Batal',
                showDenyButton: true,
                denyButtonText: 'Reset Kehadiran + Total',
                confirmButtonColor: '#3085d6',
                denyButtonColor: '#d33',
                cancelButtonColor: '#6c757d'
            }).then((r) => {
                // Support SweetAlert2 v7 and v11
                if (r.value || r.isConfirmed) {
                    doReset(idKL, 'total');
                } else if (r.dismiss === Swal.DismissReason.deny || r.isDenied) {
                    doReset(idKL, 'all');
                }
            });
        });

        function doReset(idKL, scope) {
            Swal.fire({
                title: 'Memproses...', 
                allowOutsideClick: false, 
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: '{{ url("/kehadiran-satpam/reset") }}',
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: '{{ csrf_token() }}',
                    id_kehadiran_lembaga: idKL,
                    scope: scope
                },
                success: function(res) {
                    if (res && res.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message || 'Reset selesai.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            table.ajax.reload(null, false);
                        });
                    } else {
                        Swal.fire('Gagal', (res && res.message) ? res.message : 'Reset gagal.', 'error');
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    var msg = 'Tidak dapat menghubungi server.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });
        }

        // Handler Riwayat Audit Trail
        $(document).on('click', '.btn-riwayat', function() {
            const id = $(this).data('id');
            const $container = $('#riwayat_timeline_container');
            
            $container.html('<li class="text-center text-muted py-4"><i class="fas fa-spinner fa-spin fa-2x mb-2 d-block"></i> Memuat Riwayat...</li>');
            $('#modal_riwayat').modal('show');

            $.ajax({
                url: '{{ url("/kehadiran-satpam/riwayat") }}/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (!res.status || !res.logs || res.logs.length === 0) {
                        $container.html('<li class="text-center text-muted py-4">Belum ada riwayat aktivitas pada periode ini.</li>');
                        return;
                    }

                    let html = '';
                    res.logs.forEach(function(item) {
                        let badgeClass = 'status-' + (item.status_aksi || 'Belum');
                        let username = item.username || 'System';
                        let jabatan = item.jabatan ? `(${item.jabatan})` : '';

                        html += `
                        <li class="timeline-item">
                            <span class="timeline-indicator ${badgeClass}"></span>
                            <div class="timeline-content shadow-none">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="timeline-title text-dark font-weight-bold mb-0">
                                        <i class="fas fa-circle me-1 text-secondary" style="font-size: 8px;"></i> ${item.status_aksi}
                                    </h6>
                                    <span class="timeline-date"><i class="far fa-clock me-1"></i> ${item.waktu_eksekusi}</span>
                                </div>
                                <div class="timeline-user">
                                    <i class="far fa-user me-1"></i> dieksekusi oleh: <strong>${username}</strong> ${jabatan}
                                </div>
                                ${item.catatan_log ? `<div class="mt-2 p-2 bg-light rounded text-muted small border">${item.catatan_log}</div>` : ''}
                            </div>
                        </li>
                        `;
                    });

                    $container.html(html);
                },
                error: function() {
                    $container.html('<li class="text-center text-danger py-4">Gagal memuat log riwayat.</li>');
                }
            });
        });
    });
</script>
@endpush
