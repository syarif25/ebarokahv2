@extends('layouts.app')

@section('title', 'Audit & Pembersihan Anomali Data SK - E-Barokah')

@section('page_title')
<span class="text-danger font-weight-bold">Log</span> / Anomali Data SK
@endsection

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <style>
        .stat-card {
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .nav-tabs .nav-link {
            font-weight: 600;
            color: #6c757d;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 0.75rem 1.25rem;
        }
        .nav-tabs .nav-link.active {
            color: #0d6efd;
            border-bottom: 3px solid #0d6efd;
            background: transparent;
        }
    </style>
@endpush

@section('content')
<div class="row">
    <!-- METRIC STATS -->
    <div class="col-xl-4 col-sm-6 mb-3">
        <div class="card stat-card bg-light-danger border-danger">
            <div class="card-body d-flex justify-content-between align-items-center py-3">
                <div>
                    <span class="text-muted fs-13 font-w600 text-uppercase">Data Residu Kosong / 0000</span>
                    <h3 class="fs-28 font-w700 mb-0 text-danger" id="stat-residu">{{ $stats['total_residu'] ?? 0 }}</h3>
                    <small class="text-muted">{{ $stats['residu_pengajar'] ?? 0 }} Pengajar, {{ $stats['residu_struktural'] ?? 0 }} Struktural</small>
                </div>
                <div class="avatar avatar-md bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fas fa-trash-alt fs-20"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-sm-6 mb-3">
        <div class="card stat-card bg-light-warning border-warning">
            <div class="card-body d-flex justify-content-between align-items-center py-3">
                <div>
                    <span class="text-muted fs-13 font-w600 text-uppercase">SK Pengajar Kadaluarsa (Aktif)</span>
                    <h3 class="fs-28 font-w700 mb-0 text-warning" id="stat-exp-pengajar">{{ $stats['expired_pengajar'] ?? 0 }}</h3>
                    <small class="text-muted">Tanggal selesai lewat dari hari ini</small>
                </div>
                <div class="avatar avatar-md bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fas fa-chalkboard-teacher fs-20"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-sm-6 mb-3">
        <div class="card stat-card bg-light-info border-info">
            <div class="card-body d-flex justify-content-between align-items-center py-3">
                <div>
                    <span class="text-muted fs-13 font-w600 text-uppercase">SK Struktural Kadaluarsa (Aktif)</span>
                    <h3 class="fs-28 font-w700 mb-0 text-info" id="stat-exp-struktural">{{ $stats['expired_struktural'] ?? 0 }}</h3>
                    <small class="text-muted">Tanggal selesai lewat dari hari ini</small>
                </div>
                <div class="avatar avatar-md bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="fas fa-user-tie fs-20"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center border-0 pb-0">
                <div>
                    <h4 class="card-title font-w700 mb-1">Daftar Anomali Data SK</h4>
                    <p class="text-muted fs-13 mb-0">Deteksi data penugasan tanpa lembaga, tanggal tidak valid, dan SK yang telah lewat masa kontrak namun belum dinonaktifkan.</p>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0">
                    <button type="button" class="btn btn-outline-danger btn-sm" id="btn-clean-residu">
                        <i class="fas fa-broom me-1"></i> Bersihkan Masal Residu Kosong
                    </button>
                    <button type="button" class="btn btn-outline-warning btn-sm" id="btn-deactivate-expired">
                        <i class="fas fa-user-clock me-1"></i> Nonaktifkan Masal SK Kadaluarsa
                    </button>
                </div>
            </div>
            
            <div class="card-body">
                <!-- TABS -->
                <ul class="nav nav-tabs mb-4" id="anomaliTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-kategori="residu" href="javascript:void(0);">
                            <i class="fas fa-exclamation-triangle text-danger me-1"></i> Data Residu / Tanggal 0000
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-kategori="expired_pengajar" href="javascript:void(0);">
                            <i class="fas fa-clock text-warning me-1"></i> Pengajar Kadaluarsa (Aktif)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-kategori="expired_struktural" href="javascript:void(0);">
                            <i class="fas fa-history text-info me-1"></i> Struktural Kadaluarsa (Aktif)
                        </a>
                    </li>
                </ul>

                <div class="table-responsive">
                    <table id="tableAnomali" class="table table-hover table-striped display" style="min-width: 850px; width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Tipe</th>
                                <th>Nama Lengkap & NIK</th>
                                <th>Lembaga</th>
                                <th>Posisi / Jabatan</th>
                                <th>Masa Berlaku SK</th>
                                <th>Hak Tunjangan</th>
                                <th>Status</th>
                                <th style="width: 110px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            let activeKategori = 'residu';

            const table = $('#tableAnomali').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url: '{{ url("/log/anomali-sk/data") }}',
                    type: 'GET',
                    data: function(d) {
                        d.kategori = activeKategori;
                    },
                    dataSrc: function(json) {
                        if (json.stats) {
                            $('#stat-residu').text(json.stats.total_residu);
                            $('#stat-exp-pengajar').text(json.stats.expired_pengajar);
                            $('#stat-exp-struktural').text(json.stats.expired_struktural);
                        }
                        return json.data || [];
                    }
                },
                columns: [
                    {
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'tipe',
                        render: function(data) {
                            if (data === 'Pengajar') {
                                return '<span class="badge badge-warning text-dark"><i class="fas fa-chalkboard-teacher me-1"></i>Pengajar</span>';
                            }
                            return '<span class="badge badge-info"><i class="fas fa-user-tie me-1"></i>Struktural</span>';
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            return `<strong>${data.nama_lengkap || '-'}</strong><br><small class="text-muted">${data.nik || '-'}</small>`;
                        }
                    },
                    {
                        data: 'nama_lembaga',
                        render: function(data) {
                            if (!data || data === '-' || data === '') {
                                return '<span class="badge badge-danger">Tanpa Lembaga (Kosong)</span>';
                            }
                            return data;
                        }
                    },
                    { data: 'posisi', defaultContent: '-' },
                    {
                        data: null,
                        render: function(data) {
                            const mulai = data.tgl_mulai || '0000-00-00';
                            const selesai = data.tgl_selesai || '0000-00-00';
                            let badgeClass = 'text-dark';
                            if (mulai === '0000-00-00' || selesai === '0000-00-00') {
                                badgeClass = 'text-danger font-w700';
                            }
                            return `<span class="${badgeClass}">${mulai} <small class="text-muted">s/d</small> ${selesai}</span>`;
                        }
                    },
                    {
                        data: null,
                        render: function(data) {
                            let badges = [];
                            if (data.tunj_kel === 'Ya') badges.push('<span class="badge badge-xs badge-success">Tunkel</span>');
                            if (data.tunj_anak === 'Ya') badges.push('<span class="badge badge-xs badge-success">Anak</span>');
                            if (data.kehormatan === 'Ya') badges.push('<span class="badge badge-xs badge-success">Kehormatan</span>');
                            return badges.length > 0 ? badges.join(' ') : '<span class="text-muted fs-12">-</span>';
                        }
                    },
                    {
                        data: 'status',
                        render: function(data) {
                            if (data === 'Aktif') {
                                return '<span class="badge badge-success">Aktif</span>';
                            }
                            return '<span class="badge badge-secondary">Tidak Aktif</span>';
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data) {
                            let buttons = '';
                            if (activeKategori === 'residu') {
                                buttons += `<button class="btn btn-danger btn-xs btn-delete-residu" data-tipe="${data.tipe}" data-id="${data.id}" title="Hapus Data Residu">
                                    <i class="fas fa-trash-alt me-1"></i> Hapus
                                </button>`;
                            } else {
                                buttons += `<button class="btn btn-warning btn-xs text-dark btn-deactivate-sk" data-tipe="${data.tipe}" data-id="${data.id}" title="Nonaktifkan Status SK">
                                    <i class="fas fa-power-off me-1"></i> Nonaktifkan
                                </button>`;
                            }
                            return buttons;
                        }
                    }
                ],
                language: {
                    emptyTable: "Tidak ada data anomali ditemukan pada kategori ini."
                }
            });

            // TAB SWITCHING
            $('#anomaliTab .nav-link').on('click', function() {
                $('#anomaliTab .nav-link').removeClass('active');
                $(this).addClass('active');
                activeKategori = $(this).data('kategori');
                table.ajax.reload();
            });

            // ACTION HAPUS DATA RESIDU TUNGGAL
            $(document).on('click', '.btn-delete-residu', function() {
                const tipe = $(this).data('tipe');
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Hapus Data Residu?',
                    text: `Data penugasan ${tipe} ini tidak memiliki lembaga/tanggal valid dan akan dihapus permanen.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus Data!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('/log/anomali-sk/destroy') }}/${tipe}/${id}`,
                            type: 'POST',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(res) {
                                Swal.fire('Berhasil', res.message, 'success');
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan sistem.', 'error');
                            }
                        });
                    }
                });
            });

            // ACTION NONAKTIFKAN SK TUNGGAL
            $(document).on('click', '.btn-deactivate-sk', function() {
                const tipe = $(this).data('tipe');
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Nonaktifkan SK?',
                    text: `Masa berlaku SK ${tipe} ini telah habis. Statusnya akan diubah menjadi 'Tidak Aktif'.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Nonaktifkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('/log/anomali-sk/deactivate') }}/${tipe}/${id}`,
                            type: 'POST',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(res) {
                                Swal.fire('Berhasil', res.message, 'success');
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan sistem.', 'error');
                            }
                        });
                    }
                });
            });

            // ACTION BERSIHKAN MASAL RESIDU KOSONG
            $('#btn-clean-residu').on('click', function() {
                Swal.fire({
                    title: 'Bersihkan Semua Data Residu Kosong?',
                    text: 'Sistem akan menghapus seluruh data penugasan pengajar & struktural yang tidak memiliki lembaga dan bertanggal 0000-00-00.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Bersihkan Masal!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Sedang memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                        $.ajax({
                            url: '{{ url("/log/anomali-sk/clean-residu") }}',
                            type: 'POST',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(res) {
                                Swal.fire('Selesai', res.message, 'success');
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message || 'Gagal memproses pembersihan masal.', 'error');
                            }
                        });
                    }
                });
            });

            // ACTION NONAKTIFKAN MASAL SK KADALUARSA
            $('#btn-deactivate-expired').on('click', function() {
                Swal.fire({
                    title: 'Nonaktifkan Masal SK Kadaluarsa?',
                    text: 'Seluruh SK Pengajar dan Struktural yang masa berlakunya sudah berakhir (tgl_selesai < hari ini) akan otomatis diubah statusnya menjadi "Tidak Aktif".',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Nonaktifkan Masal!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({ title: 'Sedang memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                        $.ajax({
                            url: '{{ url("/log/anomali-sk/deactivate-expired") }}',
                            type: 'POST',
                            data: { _token: '{{ csrf_token() }}', target: 'all' },
                            success: function(res) {
                                Swal.fire('Selesai', res.message, 'success');
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message || 'Gagal memproses penonaktifan masal.', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
