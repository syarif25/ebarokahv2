@extends('layouts.app')
@section('title', 'Master Data Bank & Admin - E-Barokah')
@section('page_title')
<span class="text-danger font-weight-bold">Master</span> / Biaya Admin Bank
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
            padding: 1.25rem 1rem !important;
            vertical-align: middle;
        }
        table.dataTable.display thead th {
            border-top: none !important;
            border-bottom: 1px solid #eaeaea !important;
            font-weight: 700;
        }
        table.dataTable,
        table.dataTable.no-footer {
            border-bottom: none !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.2em 0.5em !important;
        }
        
        .btn-tambah-bank {
            background-color: #00a15d !important; 
            color: white !important; 
            border-radius: 20px; 
            padding: 8px 20px;
            border: none;
            font-weight: 600;
        }
        .btn-edit-bank {
            border-radius: 20px;
            padding: 5px 15px;
        }
        .btn-delete-bank {
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
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-1">Daftar Bank & Potongan Admin</h4>
                    <p class="text-muted fs-13 mb-0">Pengaturan kode BI-Fast, jenis transfer, dan biaya admin untuk otomatisasi menu Payroll</p>
                </div>
                <button type="button" class="btn btn-sm btn-tambah-bank shadow-sm" onclick="showAddModal()">
                    <i class="fas fa-plus me-1"></i> Tambah Bank
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table-bank" class="display" style="min-width: 845px; width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nama Bank</th>
                                <th>Kode Bank</th>
                                <th>Kode BI-Fast</th>
                                <th>Tipe Transfer</th>
                                <th>Potongan Admin</th>
                                <th style="width: 120px;">Aksi</th>
                            </tr>
                            <tr class="filters" style="background-color: #f8f9fa;">
                                <th></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Bank" style="border-radius: 8px;"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Kode" style="border-radius: 8px;"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter BI-Fast" style="border-radius: 8px;"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Tipe" style="border-radius: 8px;"></th>
                                <th><input type="text" class="form-control form-control-sm" placeholder="Filter Admin" style="border-radius: 8px;"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($banks as $b)
                            <tr>
                                <td><strong>{{ $loop->iteration }}</strong></td>
                                <td>
                                    <strong>{{ $b->nama_bank }}</strong>
                                    @if($b->is_default)
                                        <span class="badge badge-xs bg-success text-white ms-1" style="font-size: 10px; border-radius: 10px;">Utama</span>
                                    @endif
                                </td>
                                <td>{{ $b->kode_bank ?: '-' }}</td>
                                <td><code class="text-primary fw-bold">{{ $b->bifast_code }}</code></td>
                                <td>
                                    @if($b->tipe_transfer === 'IFT')
                                        <span class="badge bg-light text-dark border">IFT (In-House)</span>
                                    @else
                                        <span class="badge bg-light text-primary border">BIF (BI-Fast)</span>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-success">Rp {{ number_format($b->biaya_admin, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex flex-column" style="width: max-content;">
                                        <button type="button" class="btn btn-outline-success btn-sm shadow-sm btn-edit-bank"
                                            onclick="showEditModal({{ json_encode($b) }})">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm shadow-sm btn-delete-bank" 
                                            onclick="deleteBank({{ $b->id_bank }}, '{{ e($b->nama_bank) }}')">
                                            <i class="fas fa-trash-alt me-1"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah / Edit Bank -->
<div class="modal fade" id="modalBank" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title font-weight-bold" id="modalBankTitle">Tambah Bank & Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formBank">
                @csrf
                <input type="hidden" id="bank_id" name="id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Nama Bank <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_bank" name="nama_bank" placeholder="Contoh: Bank Rakyat Indonesia" required style="border-radius: 8px;">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Kode Bank</label>
                            <input type="text" class="form-control" id="kode_bank" name="kode_bank" placeholder="Contoh: 002" style="border-radius: 8px;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Kode BI-Fast <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="bifast_code" name="bifast_code" placeholder="Contoh: BRINIDJA" required style="border-radius: 8px; text-transform: uppercase;">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Tipe Transfer <span class="text-danger">*</span></label>
                            <select class="form-select form-control" id="tipe_transfer" name="tipe_transfer" required style="border-radius: 8px;">
                                <option value="BIF">BIF (BI-Fast)</option>
                                <option value="IFT">IFT (In-House / Sesama)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Potongan Admin (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="biaya_admin" name="biaya_admin" placeholder="Contoh: 2500" min="0" required style="border-radius: 8px;">
                        </div>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1">
                        <label class="form-check-label" for="is_default">
                            Jadikan Bank Utama Payroll (In-House Transfer)
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn text-white rounded-pill px-4" id="btnSubmitBank" style="background-color: #00a15d;">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            var table = $('#table-bank').DataTable({
                orderCellsTop: true,
                pageLength: 10,
                language: {
                    search: "Search:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                    zeroRecords: "Tidak ada data yang cocok",
                    paginate: {
                        previous: '<i class="fas fa-chevron-left"></i>',
                        next: '<i class="fas fa-chevron-right"></i>'
                    }
                }
            });

            // Column filters
            $('#table-bank thead tr.filters th').each(function(i) {
                var input = $(this).find('input');
                if (input.length) {
                    input.on('keyup change', function() {
                        if (table.column(i).search() !== this.value) {
                            table.column(i).search(this.value).draw();
                        }
                    });
                }
            });

            // Form Submit (Add / Edit)
            $('#formBank').on('submit', function(e) {
                e.preventDefault();
                var id = $('#bank_id').val();
                var url = id ? '{{ url("/master-data/bank/update") }}/' + id : '{{ url("/master-data/bank/store") }}';
                
                $('#btnSubmitBank').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'JSON',
                    success: function(res) {
                        $('#modalBank').modal('hide');
                        Swal.fire({
                            title: 'Berhasil',
                            text: res.message,
                            icon: 'success',
                            confirmButtonColor: '#00a15d'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(err) {
                        var msg = 'Terjadi kesalahan saat menyimpan data.';
                        if (err.responseJSON && err.responseJSON.message) {
                            msg = err.responseJSON.message;
                        }
                        Swal.fire('Gagal', msg, 'error');
                        $('#btnSubmitBank').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan');
                    }
                });
            });
        });

        function showAddModal() {
            $('#formBank')[0].reset();
            $('#bank_id').val('');
            $('#modalBankTitle').text('Tambah Bank & Admin');
            $('#btnSubmitBank').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Simpan');
            $('#modalBank').modal('show');
        }

        function showEditModal(bank) {
            $('#formBank')[0].reset();
            $('#bank_id').val(bank.id_bank);
            $('#nama_bank').val(bank.nama_bank);
            $('#kode_bank').val(bank.kode_bank);
            $('#bifast_code').val(bank.bifast_code);
            $('#tipe_transfer').val(bank.tipe_transfer);
            $('#biaya_admin').val(bank.biaya_admin);
            $('#is_default').prop('checked', bank.is_default == 1);

            $('#modalBankTitle').text('Edit Bank & Admin');
            $('#btnSubmitBank').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Perbarui');
            $('#modalBank').modal('show');
        }

        function deleteBank(id, nama) {
            Swal.fire({
                title: 'Hapus Bank?',
                text: 'Apakah Anda yakin ingin menghapus ' + nama + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("/master-data/bank/delete") }}/' + id,
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        dataType: 'JSON',
                        success: function(res) {
                            Swal.fire({
                                title: 'Berhasil',
                                text: res.message,
                                icon: 'success',
                                confirmButtonColor: '#00a15d'
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function() {
                            Swal.fire('Gagal', 'Terjadi kesalahan saat menghapus data.', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush
