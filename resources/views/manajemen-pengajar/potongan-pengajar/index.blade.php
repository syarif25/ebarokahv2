@extends('layouts.app')
@section('title', 'Potongan Barokah')
@section('page_title')
<span class="text-danger font-weight-bold">Manajemen Umana'</span> / Daftar Potongan Barokah Per Pengajar
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
            border-bottom: 1px solid #cbd5e1 !important;
        }
        #table-potongan.dataTable, 
        #table-potongan.dataTable th, 
        #table-potongan.dataTable td {
            border-top: none !important;
            border-left: none !important;
            border-right: none !important;
            color: #0f172a !important;
            font-size: 14.5px !important;
            vertical-align: middle !important;
        }
        #table-potongan.dataTable thead th {
            border-bottom: 2.5px solid #94a3b8 !important;
            background-color: #f8fafc !important;
            color: #0f172a !important;
            font-size: 14px !important;
            font-weight: 800 !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 12px !important;
        }
        #table-potongan.dataTable tbody td {
            padding: 15px 12px !important;
            color: #0f172a !important;
            font-weight: 600 !important;
        }
        #table-potongan.dataTable tbody tr:hover {
            background-color: #f1f5f9 !important;
        }

        /* High Contrast Filter Row Inputs */
        .filters th input.form-control {
            border: 1.5px solid #64748b !important;
            border-radius: 8px !important;
            color: #0f172a !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            background-color: #ffffff !important;
            height: 38px !important;
            padding: 6px 12px !important;
        }
        .filters th input.form-control::placeholder {
            color: #475569 !important;
            font-weight: 500 !important;
            opacity: 1 !important;
        }
        .filters th input.form-control:focus {
            border-color: #00a15d !important;
            box-shadow: 0 0 0 2px rgba(0, 161, 93, 0.25) !important;
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
        
        /* Light Pink Button */
        .btn-light-pink {
            background-color: #fdeef3 !important;
            color: #d81b60 !important;
            border-radius: 20px !important;
            padding: 8px 25px !important;
            font-weight: 600;
            border: none;
        }
        
        .btn-tambah-potongan {
            background-color: #d848c9 !important; 
            color: white !important; 
            border-radius: 30px; 
            padding: 10px 25px;
            border: none;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 4px 6px rgba(216, 72, 201, 0.2);
        }
        
        /* Fix Select2 Scroll Issue */
        .select2-results__options {
            max-height: 250px !important;
            overflow-y: auto !important;
        }
        .select2-container .select2-dropdown {
            z-index: 9999 !important;
        }
    </style>
@endpush
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="card-title text-dark font-weight-bold mb-0" style="font-size: 1.2rem;">Daftar Potongan Barokah Per Pengajar</h4>
                <button type="button" class="btn btn-sm btn-tambah-potongan" data-bs-toggle="modal" data-bs-target="#modalPotongan" id="btnTambah">
                    <i class="fas fa-plus me-1"></i> Tambah Potongan
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="display" id="table-potongan" style="min-width: 845px; width:100%">
                        <thead>
                            <tr>
                                <th class="font-weight-bold">No</th>
                                <th class="font-weight-bold">Nama Lengkap</th>
                                <th class="font-weight-bold">Lembaga</th>
                                <th class="font-weight-bold">Jumlah Potongan</th>
                                <th class="font-weight-bold">Periode</th>
                                <th class="font-weight-bold">Aksi</th>
                            </tr>
                            <tr class="filters">
                                <th></th>
                                <th><input type="text" class="form-control" placeholder="Filter Nama" /></th>
                                <th><input type="text" class="form-control" placeholder="Filter Lembaga" /></th>
                                <th><input type="text" class="form-control" placeholder="Filter Jml" /></th>
                                <th><input type="text" class="form-control" placeholder="Filter Periode" /></th>
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
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Form Potongan Barokah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPotongan">
                <div class="modal-body">
                    <input type="hidden" id="inputId" name="id_potongan_pengajar">
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="id_pengajar" id="id_pengajar" required style="width: 100%;">
                                <option value="">-- Pilih Nama Lengkap --</option>
                                @foreach($pengajars as $p)
                                    <option value="{{ $p->id_pengajar }}" data-lembaga="{{ $p->lembaga->nama_lembaga ?? '-' }}">{{ $p->umana->nama_lengkap ?? '' }} - {{ $p->lembaga->nama_lembaga ?? '-' }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Lembaga Pengabdian</label>
                            <input type="text" class="form-control" id="lembaga_pengabdian" readonly style="background-color: #f8f9fa;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Potongan <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="jenis_potongan" id="jenis_potongan" required style="width: 100%;">
                                <option value="">-- Pilih Jenis Potongan --</option>
                                @foreach($jenisPotongans as $jp)
                                    <option value="{{ $jp->id_potongan }}" data-nominal="{{ $jp->nominal }}">{{ $jp->nama_potongan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nominal (Rp) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nominal_potongan" id="nominal_potongan" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Masa Aktif Mulai dari <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="min_periode_potongan" id="min_periode_potongan" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sampai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="max_periode_potongan" id="max_periode_potongan" required>
                        </div>
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

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 bg-white pb-0 mt-2 px-4 d-flex justify-content-between align-items-center">
                <h5 class="modal-title font-weight-bold text-dark mb-0">Rincian Potongan Barokah</h5>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge" style="background-color: #4ade80; font-size: 13px; padding: 7px 15px; border-radius: 20px; color: white; font-weight: 600;" id="detail-total-potongan">Total Potongan : Rp. 0</span>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body bg-white pt-2 px-4">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <span class="text-muted" style="font-size: 11px; font-weight: 600; text-transform: uppercase;">Nama Lengkap</span>
                        <h6 class="font-weight-bold mb-0 mt-1" style="color: #00a15d; font-size: 15px;" id="detail-nama-lengkap">...</h6>
                    </div>
                    <div class="col-md-6">
                        <span class="text-muted" style="font-size: 11px; font-weight: 600; text-transform: uppercase;">Lembaga Pengabdian</span>
                        <h6 class="font-weight-bold mb-0 mt-1" style="color: #00a15d; font-size: 15px;" id="detail-lembaga">...</h6>
                    </div>
                </div>

                <div class="table-responsive" style="border: none;">
                    <table class="table table-borderless" id="table-detail" style="border-collapse: separate; border-spacing: 0;">
                        <thead>
                            <tr style="border-bottom: 2px solid #f1f1f1;">
                                <th class="font-weight-bold text-dark pb-3 pt-2">No</th>
                                <th class="font-weight-bold text-dark pb-3 pt-2">Jenis Potongan</th>
                                <th class="font-weight-bold text-dark pb-3 pt-2">Nominal</th>
                                <th class="font-weight-bold text-dark pb-3 pt-2 text-center">Periode</th>
                                <th class="font-weight-bold text-dark pb-3 pt-2 text-center">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Detail data via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 pb-4 pr-4">
                <button type="button" class="btn btn-light-pink" data-bs-dismiss="modal">Close</button>
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
            dropdownParent: $('#modalPotongan')
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
            ajax: '{{ url("/Potongan_pengajar") }}',
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
                    data: 'potongans', 
                    render: function(data) { 
                        if(!data || data.length === 0) return '<span style="color: #64748b; font-weight: 600;">-</span>';
                        let count = data.length;
                        let total = data.reduce((sum, p) => sum + parseFloat(p.nominal_potongan || 0), 0);
                        return `<div>
                            <span class="badge" style="background-color: #f1f5f9; color: #0f172a; border: 1.5px solid #cbd5e1; border-radius: 12px; font-size: 12px; font-weight: 700; padding: 4px 10px;">${count} Potongan</span>
                            <span class="d-block font-weight-bold mt-1" style="color: #047857; font-size: 15px;">${formatRupiah(total)}</span>
                        </div>`;
                    }
                },
                { 
                    data: 'potongans', 
                    render: function(data) { 
                        if(!data || data.length === 0) return '<span style="color: #64748b; font-weight: 600;">-</span>';
                        let minDate = data.reduce((min, p) => p.min_periode_potongan < min ? p.min_periode_potongan : min, data[0].min_periode_potongan);
                        let maxDate = data.reduce((max, p) => p.max_periode_potongan > max ? p.max_periode_potongan : max, data[0].max_periode_potongan);
                        return `<span style="color: #0f172a; font-weight: 700; font-size: 13.5px; line-height: 1.45;">${formatDate(minDate)}<br><span style="color: #64748b; font-weight: 800; font-size: 11px;">s.d</span><br>${formatDate(maxDate)}</span>`;
                    }
                },
                {
                    data: 'id_pengajar',
                    render: function(data) {
                        return `<button class="btn btn-outline-success btn-sm btn-detail" style="border-radius: 20px; border: 2px solid #059669; color: #059669; font-weight: 700; padding: 5px 16px; font-size: 13px;" data-id="${data}"><i class="fas fa-eye me-1"></i> Detail</button>`;
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
            $('#lembaga_pengabdian').val('');
            $('#id_pengajar').prop('disabled', false);
            $('.select2').val('').trigger('change');
            $('#modalTitle').text('Form Potongan Barokah');
        });

        // Set lembaga pengabdian ketika pilih nama pengajar
        $('#id_pengajar').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var lembaga = selectedOption.data('lembaga');
            if(lembaga) {
                $('#lembaga_pengabdian').val(lembaga);
            } else {
                $('#lembaga_pengabdian').val('');
            }
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
            
            // Enable dulu biar datanya ikut ke-submit
            $('#id_pengajar').prop('disabled', false);
            
            var id = $('#inputId').val();
            var url = id ? '{{ url("/Potongan_pengajar/update") }}/' + id : '{{ url("/Potongan_pengajar/store") }}';
            
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
                        loadDetail($('#id_pengajar_detail').val());
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
            var tr = $(this).closest('tr');
            var rowData = table.row(tr).data();
            var id_pengajar = rowData.id_pengajar;
            
            // Set header info
            $('#detail-nama-lengkap').text(rowData.umana ? rowData.umana.nama_lengkap : '-');
            $('#detail-lembaga').text(rowData.lembaga ? rowData.lembaga.nama_lembaga : '-');
            
            // Simpan id_pengajar di elemen dummy buat reload otomatis nanti
            if($('#id_pengajar_detail').length === 0) {
                $('body').append(`<input type="hidden" id="id_pengajar_detail" value="${id_pengajar}">`);
            } else {
                $('#id_pengajar_detail').val(id_pengajar);
            }
            loadDetail(id_pengajar);
            $('#modalDetail').modal('show');
        });

        var detailTable = null;
        function loadDetail(id_pengajar) {
            if (detailTable !== null) {
                detailTable.destroy();
                detailTable = null;
            }
            
            $.get('{{ url("/Potongan_pengajar/detail") }}/' + id_pengajar, function(res) {
                var tbody = $('#table-detail tbody');
                tbody.empty();
                
                let grandTotal = 0;
                let today = new Date();
                today.setHours(0,0,0,0);

                if(res.data.length === 0) {
                    tbody.append(`<tr><td colspan="5" class="text-center">Tidak ada data potongan</td></tr>`);
                    $('#detail-total-potongan').text('Total Potongan : Rp. 0');
                } else {
                    res.data.forEach(function(item, index) {
                        grandTotal += parseFloat(item.nominal_potongan);
                        
                        let startDate = new Date(item.min_periode_potongan);
                        let endDate = new Date(item.max_periode_potongan);
                        let isActive = (today >= startDate && today <= endDate);
                        let badgeHtml = isActive ? `<br><span class="badge mt-2" style="background-color: #e0f8e9; color: #00a15d; border-radius: 12px; font-size: 10px;">Berlaku Saat Ini</span>` : '';

                        tbody.append(`
                            <tr>
                                <td style="color: #0f172a; font-weight: 800; font-size: 14px;">${index + 1}</td>
                                <td style="color: #0f172a; font-weight: 800; font-size: 14.5px;">${item.potongan ? item.potongan.nama_potongan : '-'}</td>
                                <td style="color: #047857; font-weight: 800; font-size: 15px;">${formatRupiah(item.nominal_potongan)}</td>
                                <td class="text-center" style="color: #0f172a; font-weight: 700; font-size: 13.5px;">
                                    ${formatDate(item.min_periode_potongan)} <span style="color:#64748b; font-weight: 800; font-size: 11px;">s.d</span> ${formatDate(item.max_periode_potongan)}
                                    ${badgeHtml}
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-outline-danger btn-sm btn-edit-potongan" style="border-radius: 20px; color: #d848c9; border: 2px solid #d848c9; background: white; font-weight: 700; padding: 4px 14px;" data-item='${JSON.stringify(item)}'><i class="far fa-edit me-1"></i> Edit</button>
                                </td>
                            </tr>
                        `);
                    });
                    
                    $('#detail-total-potongan').text('Total Potongan : ' + formatRupiah(grandTotal));

                    detailTable = $('#table-detail').DataTable({
                        order: [], // Jangan sort otomatis, biarkan dari backend (descending)
                        pageLength: 5,
                        lengthMenu: [[5, 10, 25, -1], [5, 10, 25, "Semua"]],
                        language: { search: "Cari Data:" },
                        dom: 'rtip' // Hide default search box to match screenshot
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
            $('#inputId').val(data.id_potongan_pengajar);
            $('#id_pengajar').val(data.id_pengajar).trigger('change');
            $('#id_pengajar').prop('disabled', true); // Kunci dropdown nama
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
