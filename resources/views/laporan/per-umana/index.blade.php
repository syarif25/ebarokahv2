@extends('layouts.app')

@section('title', 'Daftar Barokah Per Umana - E-Barokah')

@section('page_title')
<span class="text-danger font-weight-bold">Laporan</span> / Per Umana
@endsection

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .filter-input-col {
            padding: 6px 12px;
            font-size: 13px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            width: 100%;
            outline: none;
            transition: all 0.2s;
        }
        .filter-input-col:focus {
            border-color: #00a15d;
            box-shadow: 0 0 0 3px rgba(0, 161, 93, 0.12);
        }
        #tabel_perumana th {
            background-color: #f8fafc !important;
            color: #1e293b !important;
            font-weight: 700 !important;
            font-size: 13px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            vertical-align: middle;
            padding: 12px 14px;
        }
        #tabel_perumana td {
            font-size: 13px !important;
            color: #334155 !important;
            vertical-align: middle;
            padding: 12px 14px;
        }
        .btn-rincian-custom {
            border: 1px solid #ef4444;
            color: #ef4444;
            background-color: transparent;
            font-size: 12px;
            font-weight: 600;
            border-radius: 20px;
            padding: 5px 16px;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .btn-rincian-custom:hover {
            background-color: #ef4444;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.2);
        }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title font-weight-bold mb-1">Daftar Barokah Perumana</h4>
                <p class="text-muted fs-13 mb-0">
                    <i class="fas fa-info-circle text-muted me-1"></i> Data Umana' penerima barokah beserta riwayat detailnya
                </p>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="tabel_perumana" class="table table-bordered table-hover align-middle mb-0" style="width: 100%;">
                        <thead>
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th>Nama Lengkap</th>
                                <th>No HP</th>
                                <th>Alamat</th>
                                <th style="width: 100px;" class="text-center">Aksi</th>
                            </tr>
                            <tr class="filter-row">
                                <th></th>
                                <th>
                                    <input type="text" class="filter-input-col" placeholder="Cari Nama Lengkap" id="col_search_nama">
                                </th>
                                <th>
                                    <input type="text" class="filter-input-col" placeholder="Cari No HP" id="col_search_hp">
                                </th>
                                <th>
                                    <input type="text" class="filter-input-col" placeholder="Cari Alamat" id="col_search_alamat">
                                </th>
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
@endsection

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
    $(document).ready(function() {
        var table = $('#tabel_perumana').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ url('/laporan/per-umana/data') }}",
                type: "POST",
                data: function(d) {
                    d._token = "{{ csrf_token() }}";
                }
            },
            columns: [
                { data: 'no', className: 'text-center font-weight-bold' },
                { 
                    data: 'nama_lengkap',
                    render: function(data) {
                        return '<strong>' + data + '</strong>';
                    }
                },
                { data: 'nomor_hp', className: 'font-weight-bold' },
                { data: 'alamat_domisili' },
                { data: 'aksi', className: 'text-center', orderable: false }
            ],
            order: [[1, 'asc']],
            orderCellsTop: true,
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                zeroRecords: "Data tidak ditemukan",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: '<i class="fas fa-chevron-right"></i>',
                    previous: '<i class="fas fa-chevron-left"></i>'
                }
            }
        });

        // Search per kolom (Nama, No HP, Alamat)
        $('#col_search_nama').on('keyup change', function() {
            table.column(1).search(this.value).draw();
        });
        $('#col_search_hp').on('keyup change', function() {
            table.column(2).search(this.value).draw();
        });
        $('#col_search_alamat').on('keyup change', function() {
            table.column(3).search(this.value).draw();
        });
    });
    </script>
@endpush
