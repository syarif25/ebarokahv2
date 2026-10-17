@extends('layouts.app')
@section('title', 'Tunjangan Beban Kerja')
@section('page_title')
<span class="text-danger font-weight-bold">Manajemen Umana'</span> / Tunjangan Beban Kerja
@endsection
@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .btn-tambah-tbk {
            background-color: #d848c9 !important; 
            color: white !important; 
            border-radius: 20px; 
            padding: 8px 20px;
            border: none;
        }
        .btn-edit-tbk, .btn-delete-tbk {
            border-radius: 20px !important;
            padding: 5px 15px !important;
        }

        #modalTbk .select2-container--default .select2-selection--single {
            height: 40px;
            border: 1px solid #d7dae3;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
        }
        #modalTbk .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }
        #modalTbk .form-control, #modalTbk .select2-container--default .select2-selection--single {
            border: 1px solid #00a15d !important;
            border-radius: 8px !important;
            background-color: #fff;
            height: 42px;
        }
        #modalTbk .form-control:focus {
            border-color: #00a15d !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 161, 93, 0.25) !important;
        }
    </style>
@endpush
@section('content')
<div class="content-wrapper">
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-success text-white me-2">
                <i class="mdi mdi-cash-multiple"></i>
            </span> Manajemen Umana / Tunjangan Beban Kerja
        </h3>
    </div>
    
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title text-success font-weight-bold mb-0">Daftar Tunjangan Beban Kerja</h4>
                        <button type="button" class="btn btn-sm btn-tambah-tbk shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTbk" id="btnTambah">
                            Tambah TBK
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="display" id="table-tbk" style="min-width: 845px; width:100%">
                            <thead>
                                <tr>
                                    <th class="font-weight-bold">No</th>
                                    <th class="font-weight-bold">Nama Lengkap</th>
                                    <th class="font-weight-bold">Lembaga</th>
                                    <th class="font-weight-bold">Jenis</th>
                                    <th class="font-weight-bold">Nominal</th>
                                    <th class="font-weight-bold">Periode</th>
                                    <th class="font-weight-bold">Aksi</th>
                                </tr>
                                <tr class="filters">
                                    <th></th>
                                    <th><input type="text" class="form-control" placeholder="Filter Nama" /></th>
                                    <th><input type="text" class="form-control" placeholder="Filter Lembaga" /></th>
                                    <th><input type="text" class="form-control" placeholder="Filter Jenis" /></th>
                                    <th><input type="text" class="form-control" placeholder="Filter Nominal" /></th>
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
<div class="modal fade" id="modalTbk" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold text-dark" id="modalTitle">Form Tunjangan Beban Kerja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTbk">
                <div class="modal-body pt-4">
                    <input type="hidden" id="inputId" name="id_tbk">
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label text-muted">Penempatan / Umana <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="id_penempatan" id="id_penempatan" required style="width: 100%;">
                                <option value="">-- Pilih Penempatan --</option>
                                @foreach($penempatans as $p)
                                    <option value="{{ $p->id_penempatan }}">{{ $p->umana->nama_lengkap ?? '' }} - {{ $p->lembaga->nama_lembaga ?? '' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Jenis TBK <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="jenis_tbk" id="jenis_tbk" required style="width: 100%;">
                                <option value="">-- Pilih Jenis TBK --</option>
                                <option value="TBK Jumlah Siswa">TBK Jumlah Siswa</option>
                                <option value="TBK Mengajar">TBK Mengajar</option>
                                <option value="TBK Walikelas">TBK Walikelas</option>
                                <option value="TBK Jml Prodi">TBK Jml Prodi</option>
                                <option value="TBK Keahlian">TBK Keahlian</option>
                                <option value="TBK Resiko/Keahlian">TBK Resiko/Keahlian</option>
                                <option value="TBK Wilayah Kerja">TBK Wilayah Kerja</option>
                                <option value="TBK Lain-lain">TBK Lain-lain</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Nominal <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nominal_tbk" id="nominal_tbk" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Masa Aktif Mulai dari <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="min_periode" id="min_periode" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Sampai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="max_periode" id="max_periode" required>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-danger btn-rounded" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success btn-rounded" style="background-color: #00b956; border: none;">Simpan</button>
                </div>
            </form>
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
            dropdownParent: $('#modalTbk'),
            theme: 'bootstrap-5'
        });
        
        const formatRupiah = (number) => {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        };
        
        const formatDate = (dateString) => {
            if(!dateString) return '-';
            const [year, month, day] = dateString.split('-');
            return `${day}-${month}-${year}`;
        };

        var table = $('#table-tbk').DataTable({
            processing: true,
            serverSide: false,
            orderCellsTop: true,
            fixedHeader: true,
            ajax: '{{ url("/manajemen-umana/tunjangan-beban-kerja") }}',
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
                { data: 'penempatan.umana.nama_lengkap', render: function(data) { return data ? `<span class="font-weight-bold text-dark">${data}</span>` : '-'; }},
                { data: 'penempatan.lembaga.nama_lembaga', render: function(data) { return data ? data : '-'; }},
                { data: 'jenis_tbk' },
                { data: 'nominal_tbk', render: function(data) { return formatRupiah(data); }},
                { 
                    data: null, 
                    render: function(data) { 
                        return `<span class="text-dark">${formatDate(data.min_periode)}<br>s.d<br>${formatDate(data.max_periode)}</span>`;
                    }
                },
                {
                    data: null,
                    render: function(data) {
                        return `
                            <div class="d-flex flex-column gap-1" style="width: max-content;">
                                <button class="btn btn-outline-success btn-sm btn-edit-tbk shadow-sm" data-item='${JSON.stringify(data)}'>Edit</button>
                                <button class="btn btn-outline-danger btn-sm btn-delete-tbk shadow-sm" data-id="${data.id_tbk}">Hapus</button>
                            </div>
                        `;
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

        $('#nominal_tbk').on('keyup', function() {
            $(this).val(formatInputRupiah($(this).val()));
        });

        // Reset form
        $('#btnTambah').click(function() {
            $('#formTbk')[0].reset();
            $('#inputId').val('');
            $('.select2').val('').trigger('change');
            $('#modalTitle').text('Form Tunjangan Beban Kerja');
        });

        // Submit form
        $('#formTbk').submit(function(e) {
            e.preventDefault();
            var id = $('#inputId').val();
            var url = id ? '{{ url("/manajemen-umana/tunjangan-beban-kerja/update") }}/' + id : '{{ url("/manajemen-umana/tunjangan-beban-kerja/store") }}';
            
            var formData = new FormData(this);
            // Hapus titik ribuan sebelum dikirim
            var cleanNominal = $('#nominal_tbk').val().replace(/\./g, '');
            formData.set('nominal_tbk', cleanNominal);
            
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    $('#modalTbk').modal('hide');
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

        // Edit
        $('#table-tbk tbody').on('click', '.btn-edit-tbk', function() {
            var data = $(this).data('item');
            
            $('#inputId').val(data.id_tbk);
            $('#id_penempatan').val(data.id_penempatan).trigger('change');
            $('#jenis_tbk').val(data.jenis_tbk).trigger('change');
            $('#nominal_tbk').val(formatInputRupiah(data.nominal_tbk));
            $('#min_periode').val(data.min_periode);
            $('#max_periode').val(data.max_periode);
            
            $('#modalTitle').text('Edit Tunjangan Beban Kerja');
            $('#modalTbk').modal('show');
        });

        // Hapus
        $('#table-tbk tbody').on('click', '.btn-delete-tbk', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data TBK ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("/manajemen-umana/tunjangan-beban-kerja/delete") }}/' + id,
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
    });
</script>
@endpush
@endsection
