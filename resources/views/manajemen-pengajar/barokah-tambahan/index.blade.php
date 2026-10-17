@extends('layouts.app')
@section('title', 'Barokah Tambahan')
@section('page_title')
<span class="text-danger font-weight-bold">Manajemen Umana'</span> / Daftar Barokah Tambahan Per Pengajar
@endsection
@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .btn-tambah-barokah {
            background-color: #d848c9 !important; 
            color: white !important; 
            border-radius: 20px; 
            padding: 8px 20px;
            border: none;
        }
        .btn-edit-barokah, .btn-delete-barokah, .btn-detail {
            border-radius: 20px !important;
            padding: 5px 15px !important;
        }
        
        #modalBarokah .select2-container--default .select2-selection--single {
            height: 40px;
            border: 1px solid #d7dae3;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
        }
        #modalBarokah .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }
        #modalBarokah .form-control, #modalBarokah .select2-container--default .select2-selection--single {
            border: 1px solid #00a15d !important;
            border-radius: 8px !important;
            background-color: #fff;
            height: 42px;
        }
        #modalBarokah .form-control:focus {
            border-color: #00a15d !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 161, 93, 0.25) !important;
        }
        
        /* Clean Table Style */
        #table-barokah.dataTable tbody tr {
            border-bottom: 1px solid #f1f1f1;
        }
        #table-barokah.dataTable, #table-barokah.dataTable th, #table-barokah.dataTable td {
            border-top: none;
            border-left: none;
            border-right: none;
        }
        #table-barokah.dataTable thead th {
            border-bottom: 2px solid #f1f1f1;
            background-color: #fff;
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
        
        .btn-tambah-barokah {
            background-color: #d848c9 !important; 
            color: white !important; 
            border-radius: 30px; 
            padding: 10px 25px;
            border: none;
            font-weight: 600;
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
<div class="content-wrapper">
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 15px; margin-top: 10px;">
        <div class="card-body py-3">
            <h5 class="mb-0 font-weight-normal"><strong class="text-danger">Manajemen Pengajar</strong> <span class="text-muted" style="color: #999 !important;">/ Barokah Tambahan Pengajar</span></h5>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                        <h4 class="card-title text-dark font-weight-bold mb-0" style="font-size: 1.2rem;">Daftar Barokah Tambahan Per Pengajar</h4>
                        <button type="button" class="btn btn-sm btn-tambah-barokah" data-bs-toggle="modal" data-bs-target="#modalBarokah" id="btnTambah">
                            <i class="fas fa-plus"></i> Tambah Barokah
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="display" id="table-barokah" style="min-width: 845px; width:100%">
                            <thead>
                                <tr>
                                    <th class="font-weight-bold">No</th>
                                    <th class="font-weight-bold">Nama Lengkap</th>
                                    <th class="font-weight-bold">Lembaga</th>
                                    <th class="font-weight-bold">Jumlah Barokah</th>
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
</div>

<!-- Modal Tambah/Edit -->
<div class="modal fade" id="modalBarokah" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Form Barokah Tambahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formBarokah">
                <div class="modal-body">
                    <input type="hidden" id="inputId" name="id_barokah_tambahan">
                    
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
                            <label class="form-label">Nama Barokah Tambahan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_barokah" id="nama_barokah" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nominal (Rp) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nominal_tambahan" id="nominal_tambahan" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Masa Aktif Mulai dari <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="min_periode_tambahan" id="min_periode_tambahan" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sampai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="max_periode_tambahan" id="max_periode_tambahan" required>
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
                <h5 class="modal-title font-weight-bold text-dark mb-0">Rincian Barokah Tambahan</h5>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge" style="background-color: #4ade80; font-size: 13px; padding: 7px 15px; border-radius: 20px; color: white; font-weight: 600;" id="detail-total-barokah">Total Barokah : Rp. 0</span>
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
                                <th class="font-weight-bold text-dark pb-3 pt-2">Nama Barokah Tambahan</th>
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
            dropdownParent: $('#modalBarokah')
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

        var table = $('#table-barokah').DataTable({
            processing: true,
            serverSide: false,
            orderCellsTop: true,
            fixedHeader: true,
            ajax: '{{ url("/Barokah_tambahan") }}',
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
                { data: 'umana.nama_lengkap', render: function(data) { return data ? `<span class="font-weight-bold text-dark">${data}</span>` : '-'; }},
                { data: 'lembaga.nama_lembaga', render: function(data) { return data ? data : '-'; }},
                { 
                    data: 'barokah_tambahans', 
                    render: function(data) { 
                        if(!data || data.length === 0) return '-';
                        let count = data.length;
                        let total = data.reduce((sum, p) => sum + parseFloat(p.nominal_tambahan || 0), 0);
                        return `<div><span class="d-block font-weight-bold text-dark">${count} Barokah</span><span class="text-success font-weight-bold">${formatRupiah(total)}</span></div>`;
                    }
                },
                { 
                    data: 'barokah_tambahans', 
                    render: function(data) { 
                        if(!data || data.length === 0) return '-';
                        let minDate = data.reduce((min, p) => p.min_periode_tambahan < min ? p.min_periode_tambahan : min, data[0].min_periode_tambahan);
                        let maxDate = data.reduce((max, p) => p.max_periode_tambahan > max ? p.max_periode_tambahan : max, data[0].max_periode_tambahan);
                        return `<span class="text-success" style="font-size: 13px;">${formatDate(minDate)}<br>s.d<br>${formatDate(maxDate)}</span>`;
                    }
                },
                {
                    data: 'id_pengajar',
                    render: function(data) {
                        return `<button class="btn btn-outline-success btn-sm btn-detail" style="border-radius: 20px; border-color: #4ade80; color: #4ade80; font-weight: 600;" data-id="${data}"><i class="fas fa-eye"></i> Detail</button>`;
                    }
                }
            ],
            language: { search: "Search:" }
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

        $('#nominal_tambahan').on('keyup', function() {
            $(this).val(formatInputRupiah($(this).val()));
        });

        // Reset form
        $('#btnTambah').click(function() {
            $('#formBarokah')[0].reset();
            $('#inputId').val('');
            $('#lembaga_pengabdian').val('');
            $('#id_pengajar').prop('disabled', false);
            $('.select2').val('').trigger('change');
            $('#modalTitle').text('Form Barokah Tambahan');
        });
        $('#id_pengajar').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var lembaga = selectedOption.data('lembaga');
            if(lembaga) {
                $('#lembaga_pengabdian').val(lembaga);
            } else {
                $('#lembaga_pengabdian').val('');
            }
        });

        // Submit form
        $('#formBarokah').submit(function(e) {
            e.preventDefault();
            
            // Enable dulu biar datanya ikut ke-submit
            $('#id_pengajar').prop('disabled', false);
            
            var id = $('#inputId').val();
            var url = id ? '{{ url("/Barokah_tambahan/update") }}/' + id : '{{ url("/Barokah_tambahan/store") }}';
            
            var formData = new FormData(this);
            // Hapus titik ribuan sebelum dikirim
            var cleanNominal = $('#nominal_tambahan').val().replace(/\./g, '');
            formData.set('nominal_tambahan', cleanNominal);
            
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    $('#modalBarokah').modal('hide');
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
        $('#table-barokah tbody').on('click', '.btn-detail', function() {
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
            
            $.get('{{ url("/Barokah_tambahan/detail") }}/' + id_pengajar, function(res) {
                var tbody = $('#table-detail tbody');
                tbody.empty();
                
                let grandTotal = 0;
                let today = new Date();
                today.setHours(0,0,0,0);

                if(res.data.length === 0) {
                    tbody.append(`<tr><td colspan="5" class="text-center">Tidak ada data barokah</td></tr>`);
                    $('#detail-total-barokah').text('Total Barokah : Rp. 0');
                } else {
                    res.data.forEach(function(item, index) {
                        grandTotal += parseFloat(item.nominal_tambahan);
                        
                        let startDate = new Date(item.min_periode_tambahan);
                        let endDate = new Date(item.max_periode_tambahan);
                        let isActive = (today >= startDate && today <= endDate);
                        let badgeHtml = isActive ? `<br><span class="badge mt-2" style="background-color: #e0f8e9; color: #00a15d; border-radius: 12px; font-size: 10px;">Berlaku Saat Ini</span>` : '';

                        tbody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.nama_barokah ? item.nama_barokah : '-'}</td>
                                <td class="text-success font-weight-bold">${formatRupiah(item.nominal_tambahan)}</td>
                                <td class="text-center text-success">
                                    ${formatDate(item.min_periode_tambahan)}<br>s.d<br>${formatDate(item.max_periode_tambahan)}
                                    ${badgeHtml}
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-outline-danger btn-sm btn-edit-barokah" style="border-radius: 20px; color: #d848c9; border-color: #d848c9; background: white;" data-item='${JSON.stringify(item)}'><i class="far fa-edit"></i> Edit</button>
                                </td>
                            </tr>
                        `);
                    });
                    
                    $('#detail-total-barokah').text('Total Barokah : ' + formatRupiah(grandTotal));

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
        $('#table-detail').on('click', '.btn-edit-barokah', function() {
            var data = $(this).data('item');
            
            // Kita hide dulu modal detail
            $('#modalDetail').modal('hide');
            
            // Masukkan data ke modal utama
            $('#inputId').val(data.id_barokah_tambahan);
            $('#id_pengajar').val(data.id_pengajar).trigger('change');
            $('#id_pengajar').prop('disabled', true); // Kunci dropdown nama
            $('#nama_barokah').val(data.nama_barokah);
            $('#nominal_tambahan').val(formatInputRupiah(data.nominal_tambahan));
            $('#min_periode_tambahan').val(data.min_periode_tambahan);
            $('#max_periode_tambahan').val(data.max_periode_tambahan);
            
            $('#modalTitle').text('Edit Barokah Tambahan');
            $('#modalBarokah').modal('show');
        });

    });
</script>
@endpush
@endsection
