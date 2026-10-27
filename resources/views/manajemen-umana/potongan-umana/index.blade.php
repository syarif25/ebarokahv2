@extends('layouts.app')
@section('title', 'Potongan Barokah')
@section('page_title')
<span class="text-danger font-weight-bold">Manajemen Umana'</span> / Daftar Potongan Barokah Per Umana
@endsection
@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .btn-tambah-potongan {
            background-color: #d848c9 !important; 
            color: white !important; 
            border-radius: 20px; 
            padding: 8px 20px;
            border: none;
        }
        .btn-edit-potongan, .btn-delete-potongan, .btn-detail {
            border-radius: 20px !important;
            padding: 5px 15px !important;
        }
        
        #modalPotongan .select2-container--default .select2-selection--single {
            height: 40px;
            border: 1px solid #d7dae3;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
        }
        #modalPotongan .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }
        #modalPotongan .form-control, #modalPotongan .select2-container--default .select2-selection--single {
            border: 1px solid #00a15d !important;
            border-radius: 8px !important;
            background-color: #fff;
            height: 42px;
        }
        #modalPotongan .form-control:focus {
            border-color: #00a15d !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 161, 93, 0.25) !important;
        }

        /* Senior-Friendly & High Contrast Table Styling */
        #table-potongan.dataTable tbody tr {
            border-bottom: 1px solid #f1f1f1 !important;
        }
        #table-potongan.dataTable, 
        #table-potongan.dataTable th, 
        #table-potongan.dataTable td {
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
        }
        #table-potongan.dataTable thead th {
            border-bottom: 2px solid #f1f1f1 !important;
            background-color: #ffffff !important;
            color: #333333 !important;
            font-size: 14px !important;
            font-weight: 700 !important;
            padding: 12px 10px !important;
            letter-spacing: normal;
            text-transform: none;
        }
        #table-potongan.dataTable tbody td {
            padding: 14px 10px !important;
            color: #333333 !important;
            font-size: 13.5px !important;
        }
        #table-potongan.dataTable tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* Filter per Kolom (Seperti Menu Kehadiran) */
        .filters th {
            padding: 6px 8px !important;
            border-bottom: 2px solid #eef2f6 !important;
            background-color: #ffffff !important;
        }
        .filters th input.form-control {
            width: 100% !important;
            padding: 6px 10px !important;
            font-size: 12px !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            background-color: #fafafa !important;
            color: #495057 !important;
            font-weight: normal !important;
            height: 36px !important;
            transition: all 0.2s ease !important;
        }
        .filters th input.form-control::placeholder {
            color: #888888 !important;
            font-weight: normal !important;
            opacity: 0.85 !important;
        }
        .filters th input.form-control:focus {
            border-color: #00a15d !important;
            background-color: #ffffff !important;
            outline: none !important;
            box-shadow: 0 0 0 2px rgba(0, 161, 93, 0.15) !important;
        }

        /* DataTables Controls (Show entries, pagination, info) */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            color: #0f172a !important;
            font-size: 14px !important;
            font-weight: 700 !important;
            margin-top: 12px;
            margin-bottom: 12px;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1.5px solid #64748b !important;
            border-radius: 6px !important;
            color: #0f172a !important;
            font-weight: 800 !important;
            padding: 5px 10px !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: #0f172a !important;
            font-weight: 700 !important;
            border-radius: 6px !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #00a15d !important;
            color: #ffffff !important;
            border: 1px solid #00a15d !important;
        }

        /* Clean Modal Detail Styling */
        #table-detail.dataTable.no-footer,
        #table-detail {
            border-bottom: 1px solid #f1f1f1 !important;
        }
        #table-detail th, #table-detail td {
            border: none !important;
            padding: 12px 10px !important;
            vertical-align: middle;
        }
        #table-detail thead tr {
            border-bottom: 2px solid #f1f1f1 !important;
        }
        #table-detail tbody tr {
            border-bottom: 1px solid #f8f9fa !important;
        }
        #modalDetail .dataTables_wrapper .dataTables_info,
        #modalDetail .dataTables_wrapper .dataTables_paginate {
            margin-top: 14px !important;
            font-size: 12px !important;
            color: #6c757d;
        }
        #modalDetail .dataTables_wrapper .dataTables_paginate .paginate_button {
            border: none !important;
            padding: 4px 10px !important;
            border-radius: 6px !important;
        }
        #modalDetail .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #00a15d !important;
            color: #fff !important;
            border-radius: 6px !important;
            border: none !important;
        }
        #modalDetail .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #e9ecef !important;
            color: #333 !important;
            border: none !important;
        }
    </style>
@endpush
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="card-title text-dark font-weight-bold mb-0" style="font-size: 1.2rem;">Daftar Potongan Barokah Per Umana</h4>
                <button type="button" class="btn btn-sm btn-tambah-potongan shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPotongan" id="btnTambah">
                    <i class="fas fa-plus me-1"></i> Tambah Potongan
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="display" id="table-potongan" style="min-width: 845px; width:100%">
                        <thead>
                            <tr>
                                <th class="font-weight-bold pb-3 pt-2">No</th>
                                <th class="font-weight-bold pb-3 pt-2">Nama Lengkap</th>
                                <th class="font-weight-bold pb-3 pt-2">Lembaga</th>
                                <th class="font-weight-bold pb-3 pt-2">Jabatan</th>
                                <th class="font-weight-bold pb-3 pt-2">Jumlah Potongan</th>
                                <th class="font-weight-bold pb-3 pt-2">Periode</th>
                                <th class="font-weight-bold pb-3 pt-2 text-center">Aksi</th>
                            </tr>
                            <tr class="filters">
                                <th></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Nama" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Lembaga" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Jabatan" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Jml" /></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Periode" /></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be populated by DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit -->
<div class="modal fade" id="modalPotongan" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header border-0 bg-white pb-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold text-dark" id="modalTitle" style="font-size: 1.15rem;">Form Potongan Barokah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPotongan">
                <div class="modal-body px-4 pt-3">
                    <input type="hidden" id="inputId" name="id_potongan_umana">
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label font-weight-bold text-dark" style="font-size: 13px;">Penempatan / Umana <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="id_penempatan" id="id_penempatan" required style="width: 100%;">
                                <option value="">-- Pilih Penempatan --</option>
                                @foreach($penempatans as $p)
                                    @php
                                        $jabatanNama = $p->ketentuan->nama_jabatan ?? ($p->jabatan_lembaga ?? '-');
                                    @endphp
                                    <option value="{{ $p->id_penempatan }}">{{ $p->umana->nama_lengkap ?? '' }} - {{ $p->lembaga->nama_lembaga ?? '' }} ({{ $jabatanNama }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold text-dark" style="font-size: 13px;">Jenis Potongan <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="jenis_potongan" id="jenis_potongan" required style="width: 100%;">
                                <option value="">-- Pilih Jenis Potongan --</option>
                                @foreach($jenisPotongans as $jp)
                                    <option value="{{ $jp->id_potongan }}" data-nominal="{{ $jp->nominal }}">{{ $jp->nama_potongan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold text-dark" style="font-size: 13px;">Nominal (Rp) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nominal_potongan" id="nominal_potongan" placeholder="Rp 0" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold text-dark" style="font-size: 13px;">Masa Aktif Mulai dari <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="min_periode_potongan" id="min_periode_potongan" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold text-dark" style="font-size: 13px;">Sampai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="max_periode_potongan" id="max_periode_potongan" required>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer border-0 bg-white pt-2 pb-4 px-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 20px; padding: 7px 25px; font-weight: 500; color: #495057; background-color: #f1f3f5; border: none;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 20px; padding: 7px 28px; font-weight: 600;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header border-0 bg-white pb-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                <h5 class="modal-title font-weight-bold text-dark mb-0" style="font-size: 1.15rem;">Rincian Potongan Barokah</h5>
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <span class="badge" style="background-color: #00a15d; font-size: 12px; padding: 6px 14px; border-radius: 20px; color: white; font-weight: 600;" id="detail-total-potongan-aktif">Potongan Aktif : Rp 0</span>
                    <span class="badge bg-light text-muted border" style="font-size: 11px; padding: 6px 12px; border-radius: 20px;" id="detail-total-potongan-semua">Semua: Rp 0</span>
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body bg-white pt-3 px-4 pb-2">
                <div class="row mb-3 mt-1 pb-3" id="detail-profile-card" style="border-bottom: 1px solid #f1f1f1;">
                    <div class="col-md-4">
                        <span class="text-muted" style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Nama Lengkap</span>
                        <h6 class="font-weight-bold mb-0 mt-1 text-dark" style="font-size: 14px;" id="detail-nama">-</h6>
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted" style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Lembaga</span>
                        <h6 class="font-weight-bold mb-0 mt-1 text-dark" style="font-size: 14px;" id="detail-lembaga">-</h6>
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted" style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Jabatan</span>
                        <h6 class="font-weight-bold mb-0 mt-1 text-success" style="font-size: 14px;" id="detail-jabatan">-</h6>
                    </div>
                </div>

                <div class="table-responsive" style="border: none;">
                    <table class="table table-borderless mb-0" id="table-detail" style="border-collapse: separate; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="border-bottom: 2px solid #f1f1f1;">
                                <th class="font-weight-bold text-dark pb-3 pt-2 text-center" style="width: 40px;">No</th>
                                <th class="font-weight-bold text-dark pb-3 pt-2">Jenis Potongan</th>
                                <th class="font-weight-bold text-dark pb-3 pt-2">Nominal</th>
                                <th class="font-weight-bold text-dark pb-3 pt-2 text-center">Periode</th>
                                <th class="font-weight-bold text-dark pb-3 pt-2 text-center" style="width: 100px;">Status</th>
                                <th class="font-weight-bold text-dark pb-3 pt-2 text-center" style="width: 70px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Detail data via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 bg-white pt-1 pb-4 px-4">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 20px; padding: 7px 28px; font-weight: 500; color: #495057; background-color: #f1f3f5; border: none;">Tutup</button>
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
        $('.select2').select2({
            dropdownParent: $('#modalPotongan'),
            theme: 'bootstrap-5'
        });
        
        // Format Rupiah
        const formatRupiah = (number) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        };
        
        // Format Date
        const formatDate = (dateString) => {
            if(!dateString) return '-';
            const [year, month, day] = dateString.split('-');
            return `${day}-${month}-${year}`;
        };

        var table = $('#table-potongan').DataTable({
            processing: true,
            serverSide: false,
            orderCellsTop: true,
            fixedHeader: true,
            ajax: '{{ url("/manajemen-umana/potongan-umana") }}',
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
                { 
                    data: null, 
                    render: function (data, type, row, meta) { 
                        return `<span style="color: #0f172a; font-weight: 800; font-size: 14.5px;">${meta.row + 1}</span>`; 
                    } 
                },
                { 
                    data: 'umana.nama_lengkap', 
                    render: function(data) { 
                        return data ? `<span style="color: #0f172a; font-weight: 800; font-size: 15px; letter-spacing: 0.2px;">${data}</span>` : '-'; 
                    }
                },
                { 
                    data: 'lembaga.nama_lembaga', 
                    render: function(data) { 
                        return data ? `<span style="color: #1e293b; font-weight: 700; font-size: 14px;">${data}</span>` : '-'; 
                    }
                },
                { 
                    data: 'ketentuan.nama_jabatan', 
                    defaultContent: '-',
                    render: function(data, type, row) { 
                        let jab = data ? data : (row.jabatan_lembaga ? row.jabatan_lembaga : '-');
                        return `<span class="badge" style="background-color: #e2e8f0; color: #0f172a; font-size: 12.5px; font-weight: 800; border: 1.5px solid #cbd5e1; padding: 6px 12px; border-radius: 20px;">${jab}</span>`; 
                    }
                },
                { 
                    data: 'potongans', 
                    render: function(data) { 
                        if(!data || data.length === 0) return '<span style="color: #64748b; font-weight: 600;">-</span>';
                        let todayStr = new Date().toISOString().slice(0, 10);
                        let aktifList = data.filter(p => todayStr >= (p.min_periode_potongan || '').substring(0, 10) && todayStr <= (p.max_periode_potongan || '').substring(0, 10));
                        let totalAll = data.reduce((sum, p) => sum + parseFloat(p.nominal_potongan || 0), 0);

                        if(aktifList.length > 0) {
                            let totalAktif = aktifList.reduce((sum, p) => sum + parseFloat(p.nominal_potongan || 0), 0);
                            return `<div>
                                <span class="badge" style="background-color: #dcfce7; color: #14532d; border: 1.5px solid #86efac; border-radius: 12px; font-size: 12px; font-weight: 700; padding: 4px 10px;"><i class="fa fa-check-circle me-1" style="color: #16a34a;"></i> ${aktifList.length} Aktif</span>
                                <span class="d-block font-weight-bold mt-1" style="color: #047857; font-size: 15px;">${formatRupiah(totalAktif)}</span>
                                <small style="color: #475569; font-weight: 600; font-size: 11.5px;">Total: ${data.length} Potongan</small>
                            </div>`;
                        } else {
                            return `<div>
                                <span class="badge" style="background-color: #f1f5f9; color: #334155; border: 1.5px solid #cbd5e1; border-radius: 12px; font-size: 12px; font-weight: 700; padding: 4px 10px;"><i class="fa fa-history me-1" style="color: #475569;"></i> Selesai (${data.length})</span>
                                <span class="d-block font-weight-bold mt-1" style="color: #0f172a; font-size: 15px;">${formatRupiah(totalAll)}</span>
                                <small style="color: #64748b; font-weight: 600; font-size: 11.5px;">Total: ${data.length} Potongan</small>
                            </div>`;
                        }
                    }
                },
                { 
                    data: 'potongans', 
                    render: function(data) { 
                        if(!data || data.length === 0) return '<span style="color: #64748b; font-weight: 600;">-</span>';
                        let todayStr = new Date().toISOString().slice(0, 10);
                        let aktifList = data.filter(p => todayStr >= (p.min_periode_potongan || '').substring(0, 10) && todayStr <= (p.max_periode_potongan || '').substring(0, 10));

                        let minDate = data.reduce((min, p) => p.min_periode_potongan < min ? p.min_periode_potongan : min, data[0].min_periode_potongan);
                        let maxDate = data.reduce((max, p) => p.max_periode_potongan > max ? p.max_periode_potongan : max, data[0].max_periode_potongan);

                        return `<span style="color: #0f172a; font-weight: 700; font-size: 13.5px; line-height: 1.45;">${formatDate(minDate)}<br><span style="color: #64748b; font-weight: 800; font-size: 11px;">s.d</span><br>${formatDate(maxDate)}</span>`;
                    }
                },
                {
                    data: 'id_penempatan',
                    render: function(data, type, row) {
                        let nama = row.umana ? row.umana.nama_lengkap : '-';
                        let lembaga = row.lembaga ? row.lembaga.nama_lembaga : '-';
                        let jabatan = (row.ketentuan && row.ketentuan.nama_jabatan) ? row.ketentuan.nama_jabatan : (row.jabatan_lembaga || '-');
                        return `<button class="btn btn-outline-success btn-sm btn-detail shadow-sm" style="border-radius: 20px; border: 2px solid #059669; color: #059669; font-weight: 700; padding: 5px 16px; font-size: 13px;" data-id="${data}" data-nama="${nama}" data-lembaga="${lembaga}" data-jabatan="${jabatan}"><i class="fas fa-eye me-1"></i> Detail</button>`;
                    }
                }
            ],
            language: { 
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: '<i class="fa fa-angle-double-right"></i>',
                    previous: '<i class="fa fa-angle-double-left"></i>'
                }
            }
        });

        // Format Rupiah untuk input text
        function formatInputRupiah(angka) {
            if(!angka) return '';
            var number_string = angka.toString().replace(/[^,\d]/g, ''),
            split   = number_string.split(','),
            sisa    = split[0].length % 3,
            rupiah  = split[0].substr(0, sisa),
            ribuan  = split[0].substr(sisa).match(/\d{3}/gi);

            if(ribuan) {
                var separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        }

        $('#nominal_potongan').on('keyup', function() {
            $(this).val(formatInputRupiah($(this).val()));
        });

        // Reset form
        $('#btnTambah').click(function() {
            $('#formPotongan')[0].reset();
            $('#inputId').val('');
            $('.select2').val('').trigger('change');
            $('#modalTitle').text('Form Potongan Barokah');
        });

        // Otomatis isi nominal saat jenis potongan dipilih
        $('#jenis_potongan').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var nominal = selectedOption.data('nominal');
            if (nominal !== undefined && $('#inputId').val() === '') {
                $('#nominal_potongan').val(formatInputRupiah(nominal)).trigger('change');
            }
        });

        // Submit form
        $('#formPotongan').submit(function(e) {
            e.preventDefault();
            var id = $('#inputId').val();
            var url = id ? '{{ url("/manajemen-umana/potongan-umana/update") }}/' + id : '{{ url("/manajemen-umana/potongan-umana/store") }}';
            
            var formData = new FormData(this);
            // Hapus titik ribuan sebelum dikirim
            var cleanNominal = $('#nominal_potongan').val().replace(/\./g, '');
            formData.set('nominal_potongan', cleanNominal);
            
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    $('#modalPotongan').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    table.ajax.reload(null, false);
                    
                    // Kalau lagi buka detail, reload detailnya juga
                    if($('#modalDetail').is(':visible')) {
                        loadDetail($('#id_penempatan_detail').val());
                    }
                },
                error: function(err) {
                    if (err.status === 422) {
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

        // Buka Modal Detail
        $('#table-potongan tbody').on('click', '.btn-detail', function() {
            var id_penempatan = $(this).data('id');
            var nama = $(this).data('nama');
            var lembaga = $(this).data('lembaga');
            var jabatan = $(this).data('jabatan');

            $('#detail-nama').text(nama || '-');
            $('#detail-lembaga').text(lembaga || '-');
            $('#detail-jabatan').text(jabatan || '-');
            $('#detail-profile-card').show();

            // Simpan id_penempatan di elemen dummy buat reload otomatis nanti
            if($('#id_penempatan_detail').length === 0) {
                $('body').append(`<input type="hidden" id="id_penempatan_detail" value="${id_penempatan}">`);
            } else {
                $('#id_penempatan_detail').val(id_penempatan);
            }
            loadDetail(id_penempatan);
            $('#modalDetail').modal('show');
        });

        var detailTable = null;
        function loadDetail(id_penempatan) {
            if (detailTable !== null) {
                detailTable.destroy();
                detailTable = null;
            }
            
            $.get('{{ url("/manajemen-umana/potongan-umana/detail") }}/' + id_penempatan, function(res) {
                var tbody = $('#table-detail tbody');
                tbody.empty();

                let totalAktif = 0;
                let grandTotal = 0;

                let now = new Date();
                let y = now.getFullYear();
                let m = String(now.getMonth() + 1).padStart(2, '0');
                let d = String(now.getDate()).padStart(2, '0');
                let todayStr = `${y}-${m}-${d}`;

                if(!res.data || res.data.length === 0) {
                    tbody.append(`<tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada data potongan</td></tr>`);
                    $('#detail-total-potongan-aktif').text('Potongan Aktif : Rp 0');
                    $('#detail-total-potongan-semua').text('Total Riwayat : Rp 0');
                } else {
                    // Urutkan: Aktif paling atas (1), Akan Datang (2), Selesai (3)
                    let sortedData = [...res.data].sort((a, b) => {
                        let minA = (a.min_periode_potongan || '').substring(0, 10);
                        let maxA = (a.max_periode_potongan || '').substring(0, 10);
                        let minB = (b.min_periode_potongan || '').substring(0, 10);
                        let maxB = (b.max_periode_potongan || '').substring(0, 10);

                        let getRank = (min, max) => {
                            if (todayStr >= min && todayStr <= max) return 1; // Aktif
                            if (todayStr < min) return 2; // Akan Datang
                            return 3; // Selesai
                        };

                        let rankA = getRank(minA, maxA);
                        let rankB = getRank(minB, maxB);

                        if (rankA !== rankB) {
                            return rankA - rankB;
                        }

                        if (rankA === 1) {
                            return (b.id_potongan_umana || 0) - (a.id_potongan_umana || 0);
                        }

                        return maxB.localeCompare(maxA);
                    });

                    sortedData.forEach(function(item, index) {
                        let nominal = parseFloat(item.nominal_potongan || 0);
                        grandTotal += nominal;

                        let minStr = (item.min_periode_potongan || '').substring(0, 10);
                        let maxStr = (item.max_periode_potongan || '').substring(0, 10);

                        let status = 'selesai';
                        if (todayStr >= minStr && todayStr <= maxStr) {
                            status = 'aktif';
                        } else if (todayStr < minStr) {
                            status = 'akan_datang';
                        }

                        let trStyle = '';
                        let statusBadge = '';
                        let nominalHtml = '';
                        let periodeHtml = '';
                        let btnClass = 'btn-outline-secondary';

                        if (status === 'aktif') {
                            totalAktif += nominal;
                            trStyle = 'style="background-color: #f0fdf4;"';
                            btnClass = 'btn-outline-success';
                            statusBadge = `<span class="badge" style="background-color: #dcfce7; color: #14532d; border: 1.5px solid #86efac; border-radius: 20px; font-size: 11.5px; padding: 4px 10px; font-weight: 700;"><i class="fa fa-check-circle me-1" style="color: #16a34a;"></i> Aktif</span>`;
                            nominalHtml = `<span style="color: #047857; font-weight: 800; font-size: 14.5px;">${formatRupiah(nominal)}</span>`;
                            periodeHtml = `<span style="color: #0f172a; font-weight: 700;">${formatDate(item.min_periode_potongan)} <span style="color:#64748b; font-weight: 800; font-size: 11px;">s.d</span> ${formatDate(item.max_periode_potongan)}</span>`;
                        } else if (status === 'akan_datang') {
                            statusBadge = `<span class="badge" style="background-color: #fef3c7; color: #78350f; border: 1.5px solid #fde68a; border-radius: 20px; font-size: 11.5px; padding: 4px 10px; font-weight: 700;"><i class="fa fa-clock-o me-1" style="color: #d97706;"></i> Akan Datang</span>`;
                            nominalHtml = `<span style="color: #0f172a; font-weight: 800; font-size: 14px;">${formatRupiah(nominal)}</span>`;
                            periodeHtml = `<span style="color: #0f172a; font-weight: 700;">${formatDate(item.min_periode_potongan)} <span style="color:#64748b; font-weight: 800; font-size: 11px;">s.d</span> ${formatDate(item.max_periode_potongan)}</span>`;
                        } else {
                            statusBadge = `<span class="badge" style="background-color: #f1f5f9; color: #334155; border: 1.5px solid #cbd5e1; border-radius: 20px; font-size: 11.5px; padding: 4px 10px; font-weight: 700;"><i class="fa fa-history me-1" style="color: #475569;"></i> Selesai</span>`;
                            nominalHtml = `<span style="color: #0f172a; font-weight: 700; font-size: 14px;">${formatRupiah(nominal)}</span>`;
                            periodeHtml = `<span style="color: #334155; font-weight: 600;">${formatDate(item.min_periode_potongan)} <span style="color:#64748b; font-weight: 800; font-size: 11px;">s.d</span> ${formatDate(item.max_periode_potongan)}</span>`;
                        }

                        tbody.append(`
                            <tr ${trStyle}>
                                <td class="text-center font-weight-bold" style="color: #0f172a; font-size: 14px;">${index + 1}</td>
                                <td style="color: #0f172a; font-weight: 700; font-size: 14.5px;">${item.potongan ? item.potongan.nama_potongan : '-'}</td>
                                <td>${nominalHtml}</td>
                                <td class="text-center">${periodeHtml}</td>
                                <td class="text-center">${statusBadge}</td>
                                <td class="text-center">
                                    <button class="btn ${btnClass} btn-sm btn-edit-potongan shadow-sm" style="border-radius: 20px; padding: 4px 14px; font-size: 12.5px; font-weight: 700;" data-item='${JSON.stringify(item)}'>
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        `);
                    });

                    if (totalAktif > 0) {
                        $('#detail-total-potongan-aktif').text('Potongan Aktif : ' + formatRupiah(totalAktif)).show();
                    } else {
                        $('#detail-total-potongan-aktif').text('Potongan Aktif : Rp 0').show();
                    }
                    $('#detail-total-potongan-semua').text('Total Riwayat : ' + formatRupiah(grandTotal)).show();

                    detailTable = $('#table-detail').DataTable({
                        order: [], // Jangan sort otomatis, biarkan dari backend (descending)
                        pageLength: 5,
                        lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "Semua"]],
                        dom: 'rtip', // Clean: tanpa dropdown show entries dan search box bawaan
                        language: {
                            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                            paginate: {
                                previous: "<i class='mdi mdi-chevron-left'></i> Prev",
                                next: "Next <i class='mdi mdi-chevron-right'></i>"
                            }
                        }
                    });
                }
            });
        }

        // Edit dari dalam modal detail
        $('#table-detail').on('click', '.btn-edit-potongan', function() {
            var data = $(this).data('item');
            
            // Kita hide dulu modal detail
            $('#modalDetail').modal('hide');
            
            // Masukkan data ke modal utama
            $('#inputId').val(data.id_potongan_umana);
            $('#id_penempatan').val(data.id_penempatan).trigger('change');
            $('#jenis_potongan').val(data.jenis_potongan).trigger('change');
            $('#nominal_potongan').val(formatInputRupiah(data.nominal_potongan));
            $('#min_periode_potongan').val(data.min_periode_potongan);
            $('#max_periode_potongan').val(data.max_periode_potongan);
            
            $('#modalTitle').text('Edit Potongan Barokah');
            $('#modalPotongan').modal('show');
        });

    });
</script>
@endpush
@endsection
