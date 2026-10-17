@extends('layouts.app')
@section('title', 'Penempatan Guru/Dosen')
@section('page_title')
<span class="text-danger font-weight-bold">Manajemen Pengajar</span> / Daftar Guru Dan Dosen
@endsection
@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .stat-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            padding: 15px 20px;
            background: #fff;
            height: 100%;
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-right: 15px;
        }
        .stat-icon-green { background: #e0f8e9; color: #20c997; }
        .stat-icon-green-solid { background: #20c997; color: #fff; }
        .stat-icon-pink { background: #fce8f3; color: #d848c9; }
        .stat-icon-pink-solid { background: #e083db; color: #fff; }
        .stat-icon-yellow { background: #fef5e5; color: #f6c000; }
        .stat-text {
            display: flex;
            flex-direction: column;
        }
        .stat-text span { font-size: 12px; color: #888; font-weight: 600; text-transform: uppercase; }
        .stat-text h3 { font-size: 24px; font-weight: 700; margin: 0; color: #333; }
        
        .btn-tambah-pengajar {
            background-color: #d848c9 !important; 
            color: white !important; 
            border-radius: 20px; 
            padding: 8px 20px;
            border: none;
        }
        
        .badge-legend {
            font-size: 11px;
            color: #888;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .icon-badge {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
        }
        .icon-badge-keluarga.active { background-color: #88d4c2; color: #fff; }
        .icon-badge-anak.active { background-color: #4ade80; color: #fff; }
        .icon-badge-kehormatan.active { background-color: #fef08a; color: #fff; }
        .icon-badge-jafung.active { background-color: #f87171; color: #fff; }
        .icon-badge-walkes.active { background-color: #f472b6; color: #fff; }
        .icon-badge.inactive { background-color: #d1d5db; color: #fff; }

        .btn-edit-pengajar {
            background-color: #00a15d !important;
            color: #fff !important;
            border-radius: 20px !important;
            padding: 5px 20px !important;
            border: none;
            font-size: 12px;
        }
        
        .badge-kategori {
            background-color: #e0f8e9;
            color: #00a15d;
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 10px;
            font-weight: 600;
            display: inline-block;
            margin-top: 4px;
        }

        .badge-status {
            background-color: #00a15d;
            color: #fff;
            border-radius: 20px;
            padding: 5px 15px;
            font-size: 11px;
        }

        table.dataTable tbody tr {
            border-bottom: 1px solid #f0f0f0;
        }
        table.dataTable tbody tr.odd {
            background-color: #f9fafb !important;
        }
        table.dataTable tbody tr.even {
            background-color: #ffffff !important;
        }
        table.dataTable thead th {
            border-bottom: 1px solid #eee;
            color: #333;
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

        .link-cek-umana {
            transition: color 0.15s ease-in-out;
            cursor: pointer;
        }
        .link-cek-umana:hover {
            color: #00a15d !important;
            text-decoration: underline !important;
        }
        
        #modalPengajar .select2-container--default .select2-selection--single {
            height: 40px;
            border: 1px solid #d7dae3;
            border-radius: 0.375rem;
            display: flex;
            align-items: center;
        }
        #modalPengajar .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }
        #modalPengajar .form-control, #modalPengajar .select2-container--default .select2-selection--single {
            border: 1px solid #00a15d !important;
            border-radius: 8px !important;
            background-color: #fff;
            height: 42px;
        }
        #modalPengajar .form-control:focus {
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
                <i class="fas fa-chalkboard-teacher"></i>
            </span>
        </h3>
    </div>
    
    <!-- STATS CARDS -->
    <div class="row mb-4">
        <div class="col-md-2 mb-3">
            <div class="stat-card">
                <div class="stat-icon stat-icon-green"><i class="fas fa-users"></i></div>
                <div class="stat-text">
                    <span>TOTAL GURU</span>
                    <h3>{{ $totalGuru }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="stat-card">
                <div class="stat-icon stat-icon-pink"><i class="far fa-user"></i></div>
                <div class="stat-text">
                    <span>GTY</span>
                    <h3>{{ $gty }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="stat-card">
                <div class="stat-icon stat-icon-yellow"><i class="far fa-user"></i></div>
                <div class="stat-text">
                    <span>GTT</span>
                    <h3>{{ $gtt }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="stat-card">
                <div class="stat-icon stat-icon-green-solid"><i class="fas fa-graduation-cap"></i></div>
                <div class="stat-text">
                    <span>TOTAL DOSEN</span>
                    <h3>{{ $totalDosen }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="stat-card">
                <div class="stat-icon stat-icon-pink-solid"><i class="far fa-user"></i></div>
                <div class="stat-text">
                    <span>DTY</span>
                    <h3>{{ $dty }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2 mb-3">
            <div class="stat-card">
                <div class="stat-icon stat-icon-yellow"><i class="far fa-user"></i></div>
                <div class="stat-text">
                    <span>DTT</span>
                    <h3>{{ $dtt }}</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title text-dark font-weight-bold mb-0">Daftar Guru Dan Dosen</h4>
                        
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex gap-2 align-items-center">
                                <span class="badge-legend text-muted"><i class="fas fa-info-circle"></i></span>
                                <span class="badge-legend"><span class="icon-badge icon-badge-keluarga active"><i class="fas fa-users"></i></span> Keluarga</span>
                                <span class="badge-legend"><span class="icon-badge icon-badge-anak active"><i class="fas fa-child"></i></span> Anak</span>
                                <span class="badge-legend"><span class="icon-badge icon-badge-kehormatan active"><i class="fas fa-star"></i></span> Kehormatan</span>
                                <span class="badge-legend"><span class="icon-badge icon-badge-jafung active"><i class="fas fa-certificate"></i></span> Jafung</span>
                                <span class="badge-legend"><span class="icon-badge icon-badge-walkes active"><i class="fas fa-chalkboard-teacher"></i></span> Wali Kelas</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-tambah-pengajar shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalPengajar" id="btnTambah">
                                <span style="background: white; color: #d848c9; border-radius: 50%; width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center;"><i class="fas fa-plus" style="font-size: 10px;"></i></span> Tambah Pengajar
                            </button>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="display table-borderless table-striped" id="table-pengajar" style="min-width: 845px; width:100%">
                            <thead>
                                <tr>
                                    <th class="font-weight-bold">No</th>
                                    <th class="font-weight-bold">Nama Lengkap</th>
                                    <th class="font-weight-bold">Lembaga</th>
                                    <th class="font-weight-bold">SKS/Jam</th>
                                    <th class="font-weight-bold">Prodi</th>
                                    <th class="font-weight-bold text-center">Tunjangan</th>
                                    <th class="font-weight-bold text-center">Status</th>
                                    <th class="font-weight-bold text-center">Aksi</th>
                                </tr>
                                <tr class="filters">
                                    <th></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Filter Nama" /></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Filter Lembaga" /></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Filter SKS" /></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Filter Prodi" /></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Filter Tunjangan" /></th>
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

<!-- Modal Tambah/Edit -->
<div class="modal fade" id="modalPengajar" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Form Pengajar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formPengajar">
                <div class="modal-body">
                    <input type="hidden" id="inputId" name="id_pengajar">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Umana' / NIK <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="nik" id="nik" required style="width: 100%;">
                                <option value="">-- Pilih Umana --</option>
                                @foreach($umanas as $u)
                                    <option value="{{ $u->nik }}">{{ ucwords(strtolower($u->nama_lengkap)) }} ({{ $u->nik }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Kategori <span class="text-danger">*</span></label>
                            <select class="form-control" name="kategori" id="kategori" required>
                                <option value="">-- Kategori --</option>
                                <option value="GTY">GTY</option>
                                <option value="GTT">GTT</option>
                                <option value="DTY">DTY</option>
                                <option value="DTT">DTT</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-control" name="status" id="status" required>
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                                <option value="Cuti 50%">Cuti 50%</option>
                                <option value="Cuti 100%">Cuti 100%</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Lembaga <span class="text-danger">*</span></label>
                            <select class="form-control select2" name="id_lembaga" id="id_lembaga" required style="width: 100%;">
                                <option value="">-- Pilih Lembaga --</option>
                                @foreach($lembagas as $l)
                                    <option value="{{ $l->id_lembaga }}">{{ $l->nama_lembaga }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prodi</label>
                            <select class="form-control select2" name="id_prodi" id="id_prodi" style="width: 100%;">
                                <option value="">-- Pilih Prodi --</option>
                                @foreach($prodis as $p)
                                    <option value="{{ $p->id_prodi }}">{{ $p->nama_prodi }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">SKS / Jam <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="jumlah_sks" id="jumlah_sks" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tgl_mulai" id="tgl_mulai" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tgl_selesai" id="tgl_selesai" required>
                        </div>
                    </div>
                    
                    <h5 class="mt-4 mb-3 text-success border-bottom pb-2">Pengaturan Tunjangan</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tunjangan Keluarga</label>
                            <select class="form-control" name="tunj_kel" id="tunj_kel">
                                <option value="Ya">Ya</option>
                                <option value="Tidak">Tidak</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tunjangan Anak</label>
                            <select class="form-control" name="tunj_anak" id="tunj_anak">
                                <option value="Ya">Ya</option>
                                <option value="Tidak">Tidak</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tunjangan Kehormatan</label>
                            <select class="form-control" name="kehormatan" id="kehormatan">
                                <option value="Ya">Ya</option>
                                <option value="Tidak">Tidak</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jabatan Fungsional (Jafung)</label>
                            <select class="form-control" name="jafung" id="jafung">
                                <option value="Ya">Ya</option>
                                <option value="Tidak">Tidak</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Wali Kelas</label>
                            <select class="form-control" name="walkes" id="walkes">
                                <option value="Ya">Ya</option>
                                <option value="Tidak">Tidak</option>
                                <option value="walkes_sklh">Wali Kelas Sekolah</option>
                                <option value="walkes_amsilati">Wali Kelas Amsilati</option>
                            </select>
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

<!-- Modal Validasi Silang Tunjangan Ganda (Struktural & Pengajar) -->
<div class="modal fade" id="modalCekValidasiUmana" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" style="border-radius: 15px; border: none; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
      <div class="modal-header bg-primary text-white py-2 px-3">
        <h5 class="modal-title font-weight-bold text-white mb-0" style="font-size: 1.05rem;">
          <i class="fa fa-id-card mr-2 me-2"></i> Validasi Tunjangan Umana'
        </h5>
        <button type="button" class="close text-white border-0 bg-transparent" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" style="font-size: 1.5rem;"><span>&times;</span></button>
      </div>
      <div class="modal-body p-3">
        <!-- Info Profil Umana' -->
        <div class="card bg-light border-0 mb-3 shadow-none">
          <div class="card-body py-2 px-3">
            <span class="text-muted small">Nama Umana':</span>
            <h6 class="font-weight-bold text-dark mb-0" id="val-umana-nama">-</h6>
          </div>
        </div>

        <div class="alert alert-warning py-2 px-3 small mb-3 border-0" style="background-color: #fff8e1; color: #856404;">
          <i class="fa fa-shield-alt mr-1 me-1"></i>
          <strong>Petunjuk:</strong> Tabel di bawah menampilkan seluruh SK penempatan aktif Umana' ini di unit <strong>Struktural</strong> dan <strong>Pengajar</strong>. Pastikan tidak ada duplikasi penerimaan <em>Tunkel</em>, <em>Tunjangan Anak</em>, atau <em>Kehormatan</em> di lebih dari satu penempatan.
        </div>

        <!-- 1. TABEL STRUKTURAL -->
        <h6 class="font-weight-bold text-primary mb-2" style="font-size: 0.9rem;">
          <i class="fa fa-sitemap mr-1 me-1"></i> 1. Penempatan Struktural
        </h6>
        <div class="table-responsive mb-3">
          <table class="table table-sm table-bordered table-striped">
            <thead class="thead-light">
              <tr>
                <th style="width: 35px;" class="text-center">No</th>
                <th>Nama</th>
                <th>Lembaga</th>
                <th class="text-center">Sertifikasi</th>
                <th class="text-center">Tunkel</th>
                <th class="text-center">Tunj Anak</th>
                <th class="text-center">Kehormatan</th>
              </tr>
            </thead>
            <tbody id="val-tbody-struktural">
              <tr><td colspan="7" class="text-center text-muted">Memuat data...</td></tr>
            </tbody>
          </table>
        </div>

        <!-- 2. TABEL PENGAJAR -->
        <h6 class="font-weight-bold text-success mb-2" style="font-size: 0.9rem;">
          <i class="fa fa-graduation-cap mr-1 me-1"></i> 2. Penugasan Pengajar (Guru / Dosen)
        </h6>
        <div class="table-responsive">
          <table class="table table-sm table-bordered table-striped">
            <thead class="thead-light">
              <tr>
                <th style="width: 35px;" class="text-center">No</th>
                <th>Nama</th>
                <th>Lembaga</th>
                <th class="text-center">Sertifikasi</th>
                <th class="text-center">GTY/DTY</th>
                <th class="text-center">Tunkel</th>
                <th class="text-center">Tun Anak</th>
                <th class="text-center">Kehormatan</th>
              </tr>
            </thead>
            <tbody id="val-tbody-pengajar">
              <tr><td colspan="8" class="text-center text-muted">Memuat data...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer py-2 px-3 border-top-0">
        <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill" data-bs-dismiss="modal" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    function toTitleCase(str) {
        if (!str) return '';
        return str.toLowerCase().replace(/\b\w/g, function(l) { return l.toUpperCase(); });
    }

    $(document).ready(function() {
        $('.select2').select2({
            dropdownParent: $('#modalPengajar'),
            theme: 'bootstrap-5'
        });

        var table = $('#table-pengajar').DataTable({
            processing: true,
            serverSide: false,
            orderCellsTop: true,
            fixedHeader: true,
            ajax: '{{ url("/Penempatan/pengajar") }}',
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
                { data: null, render: function (data, type, row, meta) { return `<span class="font-weight-bold text-dark">${meta.row + 1}</span>`; } },
                { 
                    data: 'umana', 
                    render: function(data, type, row) { 
                        let rawNama = data ? data.nama_lengkap : (row.nik || '-');
                        let nama = toTitleCase(rawNama);
                        if (type === 'filter' || type === 'sort') {
                            return rawNama + ' ' + nama + ' ' + (row.nik || '');
                        }
                        if (!data) return '-';
                        return `
                            <a href="javascript:void(0)" class="text-dark font-weight-bold link-cek-umana text-decoration-none" 
                               data-nik="${row.nik || ''}" 
                               data-nama="${nama.replace(/"/g, '&quot;')}" 
                               title="Klik untuk cek validasi tunjangan ganda (Struktural & Pengajar)">
                               ${nama} <i class="fas fa-info-circle text-primary ms-1" style="font-size: 11px; opacity: 0.7;"></i>
                            </a>
                        `; 
                    }
                },
                { 
                    data: 'lembaga', 
                    render: function(data, type, row) { 
                        let lembaga = data ? data.nama_lembaga : '-';
                        if (type === 'filter' || type === 'sort') {
                            return lembaga + ' ' + (row.kategori || '');
                        }
                        let kategori = row.kategori ? `<br><span class="badge-kategori">${row.kategori}</span>` : '';
                        return `<span class="font-weight-bold text-dark">${lembaga}</span>${kategori}`;
                    }
                },
                { 
                    data: 'jumlah_sks', 
                    render: function(data, type, row) { 
                        if (type === 'filter' || type === 'sort') return data != null ? data.toString() : '';
                        return `<span class="font-weight-bold text-dark">${data}</span>`; 
                    } 
                },
                { 
                    data: 'prodi', 
                    render: function(data, type, row) { 
                        let prodiName = data ? data.nama_prodi : '-';
                        if (type === 'filter' || type === 'sort') return prodiName;
                        return data ? `<span class="font-weight-bold text-dark">${data.nama_prodi}</span>` : '<span class="font-weight-bold text-dark">-</span>'; 
                    }
                },
                { 
                    data: null, 
                    className: 'text-center',
                    render: function(data, type, row) { 
                        function isActive(val) {
                            if (!val) return false;
                            val = val.toString().trim().toLowerCase();
                            return val !== '0' && val !== 'tidak' && val !== 'tidak ada' && val !== 'null' && val !== '-';
                        }
                        
                        if (type === 'filter') {
                            let tList = [];
                            if (isActive(data.tunj_kel)) tList.push('Keluarga');
                            if (isActive(data.tunj_anak)) tList.push('Anak');
                            if (isActive(data.kehormatan)) tList.push('Kehormatan');
                            if (isActive(data.jafung)) tList.push('Jafung');
                            if (isActive(data.walkes)) tList.push('Wali Kelas');
                            return tList.join(' ');
                        }

                        let badges = '<div class="d-flex flex-wrap justify-content-center" style="max-width: 140px; gap: 4px; margin: 0 auto;">';
                        badges += `<span class="icon-badge icon-badge-keluarga ${isActive(data.tunj_kel) ? 'active' : 'inactive'}" title="Keluarga"><i class="fas fa-users"></i></span>`;
                        badges += `<span class="icon-badge icon-badge-anak ${isActive(data.tunj_anak) ? 'active' : 'inactive'}" title="Anak"><i class="fas fa-child"></i></span>`;
                        badges += `<span class="icon-badge icon-badge-kehormatan ${isActive(data.kehormatan) ? 'active' : 'inactive'}" title="Kehormatan"><i class="fas fa-star"></i></span>`;
                        badges += `<span class="icon-badge icon-badge-jafung ${isActive(data.jafung) ? 'active' : 'inactive'}" title="Jafung"><i class="fas fa-certificate"></i></span>`;
                        badges += `<span class="icon-badge icon-badge-walkes ${isActive(data.walkes) ? 'active' : 'inactive'}" title="Wali Kelas"><i class="fas fa-chalkboard-teacher"></i></span>`;
                        badges += '</div>';
                        return badges;
                    }
                },
                { 
                    data: 'status',
                    className: 'text-center',
                    render: function(data, type, row) {
                        if (type === 'filter' || type === 'sort') return data || '';
                        if (data === 'Aktif') {
                            return '<span class="badge-status">Aktif</span>';
                        } else if (data && data.indexOf('Cuti') !== -1) {
                            return `<span class="badge badge-warning rounded-pill px-3">${data}</span>`;
                        } else {
                            return '<span class="badge badge-danger rounded-pill px-3">Tidak Aktif</span>';
                        }
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    render: function(data) {
                        return `
                            <button class="btn btn-edit-pengajar shadow-sm" data-item='${JSON.stringify(data)}'>Edit</button>
                        `;
                    }
                }
            ],
            language: { search: "Search:" }
        });

        $('#btnTambah').click(function() {
            $('#formPengajar')[0].reset();
            $('#inputId').val('');
            $('.select2').val('').trigger('change');
            $('#status').val('Aktif');
            $('#tunj_kel').val('Tidak');
            $('#tunj_anak').val('Tidak');
            $('#kehormatan').val('Tidak');
            $('#jafung').val('Tidak');
            $('#walkes').val('Tidak');
            $('#modalTitle').text('Form Tambah Pengajar');
        });

        $('#formPengajar').submit(function(e) {
            e.preventDefault();
            var id = $('#inputId').val();
            var url = id ? '{{ url("/Penempatan/pengajar/update") }}/' + id : '{{ url("/Penempatan/pengajar/store") }}';
            var formData = new FormData(this);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    $('#modalPengajar').modal('hide');
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

        $('#table-pengajar tbody').on('click', '.btn-edit-pengajar', function() {
            var data = $(this).data('item');
            
            $('#inputId').val(data.id_pengajar);
            $('#nik').val(data.nik).trigger('change');
            $('#kategori').val(data.kategori);
            $('#status').val(data.status || 'Aktif');
            $('#id_lembaga').val(data.id_lembaga).trigger('change');
            $('#id_prodi').val(data.id_prodi).trigger('change');
            $('#jumlah_sks').val(data.jumlah_sks);
            $('#tgl_mulai').val(data.tgl_mulai);
            $('#tgl_selesai').val(data.tgl_selesai);
            
            $('#tunj_kel').val(data.tunj_kel == 'Ya' ? 'Ya' : 'Tidak');
            $('#tunj_anak').val(data.tunj_anak == 'Ya' ? 'Ya' : 'Tidak');
            $('#kehormatan').val(data.kehormatan == 'Ya' ? 'Ya' : 'Tidak');
            $('#jafung').val(data.jafung == 'Ya' ? 'Ya' : 'Tidak');
            $('#walkes').val(data.walkes || 'Tidak');
            
            $('#modalTitle').text('Edit Data Pengajar');
            $('#modalPengajar').modal('show');
        });

        $('#table-pengajar tbody').on('click', '.btn-delete-pengajar', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data pengajar ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url("/Penempatan/pengajar/delete") }}/' + id,
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

        // Modal Validasi Silang Umana' (Tunjangan Ganda Struktural vs Pengajar)
        $(document).on('click', '.link-cek-umana', function() {
            const nik = $(this).data('nik');
            const namaDefault = $(this).data('nama');

            if (!nik) {
                Swal.fire('Info', 'NIK Umana tidak ditemukan.', 'warning');
                return;
            }

            $('#val-umana-nama').text(toTitleCase(namaDefault));
            $('#val-tbody-struktural').html('<tr><td colspan="7" class="text-center text-muted">Memuat data penempatan struktural...</td></tr>');
            $('#val-tbody-pengajar').html('<tr><td colspan="8" class="text-center text-muted">Memuat data penugasan pengajar...</td></tr>');

            $('#modalCekValidasiUmana').modal('show');

            $.getJSON("{{ url('/Penempatan/pengajar/cekValidasiUmana') }}/" + nik, function(res) {
                if(!res || !res.status) {
                    $('#val-tbody-struktural').html('<tr><td colspan="7" class="text-center text-danger">Gagal memuat data.</td></tr>');
                    $('#val-tbody-pengajar').html('<tr><td colspan="8" class="text-center text-danger">Gagal memuat data.</td></tr>');
                    return;
                }

                // Update profil
                let formattedNama = toTitleCase(res.umana.nama_lengkap);
                $('#val-umana-nama').text(formattedNama);

                const sertifVal = res.umana.status_sertifikasi || 'Belum Sertifikasi';

                // 1. Render Tabel Struktural
                let htmlStruk = '';
                if(res.struktural && res.struktural.length > 0) {
                    res.struktural.forEach((item, idx) => {
                        const tunkelBadge = (item.tunj_kel === 'Ya')
                            ? '<span class="badge badge-success px-2 py-1"><i class="fa fa-check mr-1 me-1"></i>Ya</span>'
                            : '<span class="badge badge-secondary px-2 py-1">Tidak</span>';

                        const tunjAnakBadge = (item.tunj_anak === 'Ya')
                            ? '<span class="badge badge-success px-2 py-1"><i class="fa fa-check mr-1 me-1"></i>Ya</span>'
                            : '<span class="badge badge-secondary px-2 py-1">Tidak</span>';

                        const kehormatanBadge = (item.kehormatan === 'Ya')
                            ? '<span class="badge badge-success px-2 py-1"><i class="fa fa-check mr-1 me-1"></i>Ya</span>'
                            : '<span class="badge badge-secondary px-2 py-1">Tidak</span>';

                        htmlStruk += `<tr>
                            <td class="text-center font-weight-bold">${idx + 1}</td>
                            <td><strong>${formattedNama}</strong><br><small class="text-muted">${item.nama_jabatan || '-'}</small></td>
                            <td><strong>${item.nama_lembaga || '-'}</strong></td>
                            <td class="text-center">${sertifVal}</td>
                            <td class="text-center">${tunkelBadge}</td>
                            <td class="text-center">${tunjAnakBadge}</td>
                            <td class="text-center">${kehormatanBadge}</td>
                        </tr>`;
                    });
                } else {
                    htmlStruk = '<tr><td colspan="7" class="text-center text-muted">Tidak memiliki data penempatan struktural lain.</td></tr>';
                }
                $('#val-tbody-struktural').html(htmlStruk);

                // 2. Render Tabel Pengajar
                let htmlPeng = '';
                if(res.pengajar && res.pengajar.length > 0) {
                    res.pengajar.forEach((item, idx) => {
                        const tunkelBadge = (item.tunj_kel === 'Ya')
                            ? '<span class="badge badge-success px-2 py-1"><i class="fa fa-check mr-1 me-1"></i>Ya</span>'
                            : '<span class="badge badge-secondary px-2 py-1">Tidak</span>';

                        const tunjAnakBadge = (item.tunj_anak === 'Ya')
                            ? '<span class="badge badge-success px-2 py-1"><i class="fa fa-check mr-1 me-1"></i>Ya</span>'
                            : '<span class="badge badge-secondary px-2 py-1">Tidak</span>';

                        const kehormatanBadge = (item.kehormatan === 'Ya')
                            ? '<span class="badge badge-success px-2 py-1"><i class="fa fa-check mr-1 me-1"></i>Ya</span>'
                            : '<span class="badge badge-secondary px-2 py-1">Tidak</span>';

                        const jenisBadge = item.jenis_pengajar
                            ? `<span class="badge badge-primary px-2 py-1">${item.jenis_pengajar}</span>`
                            : '-';

                        htmlPeng += `<tr>
                            <td class="text-center font-weight-bold">${idx + 1}</td>
                            <td><strong>${formattedNama}</strong></td>
                            <td><strong>${item.nama_lembaga || '-'}</strong></td>
                            <td class="text-center">${sertifVal}</td>
                            <td class="text-center">${jenisBadge}</td>
                            <td class="text-center">${tunkelBadge}</td>
                            <td class="text-center">${tunjAnakBadge}</td>
                            <td class="text-center">${kehormatanBadge}</td>
                        </tr>`;
                    });
                } else {
                    htmlPeng = '<tr><td colspan="8" class="text-center text-muted">Tidak memiliki data penugasan pengajar lain.</td></tr>';
                }
                $('#val-tbody-pengajar').html(htmlPeng);
            }).fail(function() {
                $('#val-tbody-struktural').html('<tr><td colspan="7" class="text-center text-danger">Gagal memanggil data server.</td></tr>');
                $('#val-tbody-pengajar').html('<tr><td colspan="8" class="text-center text-danger">Gagal memanggil data server.</td></tr>');
            });
        });
    });
</script>
@endpush
@endsection
