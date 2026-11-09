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
        
        table.dataTable.display tbody td, 
        table.dataTable.display tbody th, 
        table.dataTable.display thead th {
            border-left: none !important;
            border-right: none !important;
            padding: 1.5rem 1rem !important;
            vertical-align: middle;
        }
        table.dataTable.display thead th {
            border-top: none !important;
            border-bottom: 1px solid #eaeaea !important;
            font-weight: 700;
        }
        table.dataTable {
            border-bottom: none !important;
        }
        table.dataTable.no-footer {
            border-bottom: none !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.2em 0.5em !important;
        }
        
        .btn-tambah-pengajar {
            background-color: #d848c9 !important; 
            color: white !important; 
            border-radius: 20px; 
            padding: 8px 20px;
            border: none;
        }

        .filters th {
            padding: 8px 10px !important;
            border-bottom: 1px solid #eaeaea !important;
        }
        .filters th input.form-control {
            border: 1px solid #00a15d !important;
            border-radius: 8px !important;
            background-color: #fff;
            height: 38px;
            font-size: 13px;
        }
        .filters th input.form-control:focus {
            border-color: #00a15d !important;
            box-shadow: 0 0 0 0.2rem rgba(0, 161, 93, 0.25) !important;
        }

        .link-cek-umana {
            transition: color 0.15s ease-in-out;
            cursor: pointer;
        }
        .link-cek-umana:hover {
            color: #00a15d !important;
            text-decoration: underline !important;
        }
        
        /* Custom Modern Modal Penempatan Pengajar (Screenshot Design) */
        #modalPengajar .modal-content {
            border-radius: 20px !important;
            border: none !important;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12) !important;
            padding: 10px 15px 15px 15px !important;
            background-color: #ffffff !important;
        }
        #modalPengajar .modal-header {
            border-bottom: none !important;
            padding: 15px 20px 5px 20px !important;
            align-items: center !important;
        }
        #modalPengajar .modal-title {
            font-size: 1.15rem !important;
            font-weight: 800 !important;
            color: #2b3445 !important;
            display: flex;
            align-items: center;
        }
        #modalPengajar .modal-title .modal-id-badge {
            color: #8c98a9 !important;
            font-size: 0.85rem !important;
            font-weight: 500 !important;
            margin-left: 8px;
        }
        #modalPengajar .btn-close-modal {
            background: none !important;
            border: none !important;
            font-size: 26px !important;
            line-height: 1 !important;
            color: #64748b !important;
            cursor: pointer;
            padding: 0 5px !important;
            transition: color 0.15s ease;
        }
        #modalPengajar .btn-close-modal:hover {
            color: #1e293b !important;
        }
        #modalPengajar .modal-body {
            padding: 15px 20px !important;
        }
        #modalPengajar .modal-footer-custom {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 10px 20px 10px 20px;
        }

        /* Label style */
        #modalPengajar .form-label-custom {
            font-size: 12.5px !important;
            font-weight: 600 !important;
            color: #555e6d !important;
            margin-bottom: 4px !important;
            display: block;
        }

        /* Underline / Plain input style for static/auto-populated fields */
        #modalPengajar .input-underline {
            border: none !important;
            border-bottom: 1.5px solid #d1d5db !important;
            border-radius: 0 !important;
            background-color: transparent !important;
            font-weight: 700 !important;
            font-size: 13.5px !important;
            color: #1e293b !important;
            padding: 6px 2px !important;
            height: 38px !important;
            box-shadow: none !important;
            width: 100%;
        }
        #modalPengajar .input-underline:focus {
            border-bottom-color: #00a15d !important;
            outline: none !important;
        }
        #modalPengajar .input-underline:disabled,
        #modalPengajar .input-underline[readonly],
        #modalPengajar select.input-underline:disabled {
            background-color: transparent !important;
            color: #1e293b !important;
            cursor: not-allowed !important;
            opacity: 1 !important;
            -webkit-text-fill-color: #1e293b !important;
        }

        /* Rounded pill input style with green border */
        #modalPengajar .input-pill-green {
            border: 1.5px solid #22c55e !important;
            border-radius: 12px !important;
            background-color: #ffffff !important;
            font-weight: 600 !important;
            font-size: 13.5px !important;
            color: #1e293b !important;
            padding: 7px 14px !important;
            height: 42px !important;
            box-shadow: none !important;
            width: 100%;
            transition: all 0.2s ease;
        }
        #modalPengajar .input-pill-green:focus {
            border-color: #16a34a !important;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15) !important;
            outline: none !important;
        }

        /* Select2 override inside modal for underline style */
        #modalPengajar .select2-underline + .select2-container--bootstrap-5 .select2-selection,
        #modalPengajar .select2-underline + .select2-container--default .select2-selection--single {
            border: none !important;
            border-bottom: 1.5px solid #d1d5db !important;
            border-radius: 0 !important;
            background-color: transparent !important;
            height: 38px !important;
            display: flex;
            align-items: center;
            padding-left: 2px !important;
        }
        #modalPengajar .select2-underline + .select2-container--bootstrap-5 .select2-selection .select2-selection__rendered,
        #modalPengajar .select2-underline + .select2-container--default .select2-selection--single .select2-selection__rendered {
            font-weight: 700 !important;
            font-size: 13.5px !important;
            color: #1e293b !important;
            padding-left: 0 !important;
            line-height: normal !important;
        }
        #modalPengajar .select2-underline + .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        #modalPengajar .select2-underline + .select2-container--default.select2-container--open .select2-selection--single {
            border-bottom-color: #00a15d !important;
            box-shadow: none !important;
        }
        #modalPengajar .select2-underline:disabled + .select2-container--bootstrap-5 .select2-selection,
        #modalPengajar .select2-underline:disabled + .select2-container--default .select2-selection--single {
            background-color: transparent !important;
            cursor: not-allowed !important;
        }
        #modalPengajar .select2-underline:disabled + .select2-container--bootstrap-5 .select2-selection .select2-selection__rendered,
        #modalPengajar .select2-underline:disabled + .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1e293b !important;
        }

        /* Select2 override for pill green */
        #modalPengajar .select2-pill-green + .select2-container--bootstrap-5 .select2-selection,
        #modalPengajar .select2-pill-green + .select2-container--default .select2-selection--single {
            border: 1.5px solid #22c55e !important;
            border-radius: 12px !important;
            background-color: #ffffff !important;
            height: 42px !important;
            display: flex;
            align-items: center;
            padding: 0 8px !important;
        }
        #modalPengajar .select2-pill-green + .select2-container--bootstrap-5 .select2-selection .select2-selection__rendered,
        #modalPengajar .select2-pill-green + .select2-container--default .select2-selection--single .select2-selection__rendered {
            font-weight: 600 !important;
            font-size: 13px !important;
            color: #64748b !important;
        }

        /* Save Button Green Pill */
        #modalPengajar .btn-simpan-modal {
            background-color: #00a15d !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 15px !important;
            border-radius: 14px !important;
            padding: 10px 48px !important;
            border: none !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 4px 12px rgba(0, 161, 93, 0.25) !important;
        }
        #modalPengajar .btn-simpan-modal:hover {
            background-color: #008f52 !important;
            box-shadow: 0 6px 16px rgba(0, 161, 93, 0.35) !important;
            transform: translateY(-1px);
        }
    </style>
@endpush
@section('content')
    
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
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Daftar Guru Dan Dosen</h4>
                    <button type="button" class="btn btn-sm btn-tambah-pengajar shadow-sm" data-bs-toggle="modal" data-bs-target="#modalPengajar" id="btnTambah">
                        <i class="fas fa-plus me-1"></i> Tambah Pengajar
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table-pengajar" class="display" style="min-width: 845px; width: 100%;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Lengkap</th>
                                    <th>Lembaga</th>
                                    <th>Masa Pengabdian</th>
                                    <th>Sertifikasi</th>
                                    <th>Tunjangan</th>
                                    <th>Aksi</th>
                                </tr>
                                <tr class="filters">
                                    <th></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Cari Nama" style="width: 100%; min-width: 120px;" /></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Cari Lembaga" style="width: 100%; min-width: 120px;" /></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Cari Masa" style="width: 100%; min-width: 100px;" /></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Cari Sertifikasi" style="width: 100%; min-width: 100px;" /></th>
                                    <th><input type="text" class="form-control form-control-sm" placeholder="Cari Tunjangan" style="width: 100%; min-width: 100px;" /></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan dimuat melalui AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Modal Tambah/Edit Pengajar (Sesuai Desain Screenshot) -->
<div class="modal fade" id="modalPengajar" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <div class="modal-title">
                    Penempatan Pengajar <span class="modal-id-badge">ID: #<span id="modalIdLabel">-</span></span>
                </div>
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <form id="formPengajar">
                <div class="modal-body">
                    <input type="hidden" id="inputId" name="id_pengajar">
                    
                    <!-- Row 1: NIK, Nama Lengkap, Ijazah Terakhir, TMT Guru, TMT Dosen -->
                    <div class="row gx-3 gy-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label-custom">NIK</label>
                            <select class="form-control select2 select2-underline" name="nik" id="nik" required style="width: 100%;">
                                <option value="">-- Pilih NIK / Umana --</option>
                                @foreach($umanas as $u)
                                    <option value="{{ $u->nik }}" 
                                            data-nama="{{ ucwords(strtolower($u->nama_lengkap)) }}"
                                            data-ijazah="{{ $u->ijazah_terakhir ?: '-' }}"
                                            data-tmtguru="{{ $u->tmt_guru ?: '-' }}"
                                            data-tmtdosen="{{ $u->tmt_dosen ?: '-' }}">
                                        {{ $u->nik }} {{ ucwords(strtolower($u->nama_lengkap)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-custom">Nama Lengkap</label>
                            <input type="text" class="input-underline" id="view_nama_lengkap" readonly placeholder="Nama Lengkap">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-custom">Ijazah Terakhir</label>
                            <input type="text" class="input-underline" id="view_ijazah_terakhir" readonly placeholder="S1/S2/dll">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-custom">TMT Guru</label>
                            <input type="text" class="input-underline" id="view_tmt_guru" readonly placeholder="0000-00-00">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-custom">TMT Dosen</label>
                            <input type="text" class="input-underline" id="view_tmt_dosen" readonly placeholder="0000-00-00">
                        </div>
                    </div>

                    <!-- Row 2: Lembaga, Program Studi, Kategori, Jumlah SKS/JAM -->
                    <div class="row gx-3 gy-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label-custom">Lembaga</label>
                            <select class="form-control select2 select2-underline" name="id_lembaga" id="id_lembaga" required style="width: 100%;">
                                <option value="">-- Pilih Lembaga --</option>
                                @foreach($lembagas as $l)
                                    <option value="{{ $l->id_lembaga }}">{{ $l->nama_lembaga }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-custom">Program Studi</label>
                            <select class="form-control select2 select2-pill-green" name="id_prodi" id="id_prodi" style="width: 100%;">
                                <option value="">-- Pilih Prodi --</option>
                                @foreach($prodis as $p)
                                    <option value="{{ $p->id_prodi }}">{{ $p->nama_prodi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-custom">Kategori</label>
                            <select class="form-control input-pill-green" name="kategori" id="kategori" required>
                                <option value="">-- Kategori --</option>
                                <option value="GTY">GTY (Guru Tetap Yayasan)</option>
                                <option value="GTT">GTT (Guru Tidak Tetap)</option>
                                <option value="DTY">DTY (Dosen Tetap Yayasan)</option>
                                <option value="DTT">DTT (Dosen Tidak Tetap)</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-custom">Jumlah SKS/JAM</label>
                            <input type="number" class="input-pill-green text-center" name="jumlah_sks" id="jumlah_sks" value="0" required min="0">
                        </div>
                    </div>

                    <!-- Row 3: Tunkel, Tunj Anak, Kehormatan, Wali Kelas, Jafung, Status -->
                    <div class="row gx-3 gy-3 mb-3">
                        <div class="col-md-2">
                            <label class="form-label-custom">Tunkel</label>
                            <select class="form-control input-pill-green text-center" name="tunj_kel" id="tunj_kel">
                                <option value="Tidak">Tidak</option>
                                <option value="Ya">Ya</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-custom">Tunj Anak</label>
                            <select class="form-control input-pill-green text-center" name="tunj_anak" id="tunj_anak">
                                <option value="Tidak">Tidak</option>
                                <option value="Ya">Ya</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-custom">Kehormatan</label>
                            <select class="form-control input-pill-green text-center" name="kehormatan" id="kehormatan">
                                <option value="Tidak">Tidak</option>
                                <option value="Ya">Ya</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-custom">Wali Kelas</label>
                            <select class="form-control input-pill-green" name="walkes" id="walkes">
                                <option value="Tidak">Tidak</option>
                                <option value="Ya">Ya</option>
                                <option value="walkes_sklh">Wali Kelas Sekolah</option>
                                <option value="walkes_amsilati">Wali Kelas Amsilati</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-custom">Jafung</label>
                            <select class="form-control input-pill-green text-center" name="jafung" id="jafung">
                                <option value="Tidak">Tidak</option>
                                <option value="Ya">Ya</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label-custom">Status</label>
                            <select class="form-control input-pill-green text-center" name="status" id="status" required>
                                <option value="Aktif">Aktif</option>
                                <option value="Tidak Aktif">Tidak Aktif</option>
                                <option value="Cuti 50%">Cuti 50%</option>
                                <option value="Cuti 100%">Cuti 100%</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 4: Transport/Kehadiran & Tombol Simpan -->
                    <div class="row gx-3 gy-3 align-items-end mt-1">
                        <div class="col-md-9">
                            <label class="form-label-custom">Transport/Kehadiran</label>
                            <select class="form-control select2 select2-pill-green" name="kategori_trans" id="kategori_trans" style="width: 100%;">
                                <option value="">-- Tanpa Transport / Pilih Rute Transport --</option>
                                @foreach($transports as $t)
                                    <option value="{{ $t->id_transport }}">
                                        {{ $t->kategori_transport }} | {{ $t->nama_transport }} | Rp {{ number_format($t->nominal_transport, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 text-end">
                            <button type="submit" class="btn btn-simpan-modal w-100">Simpan</button>
                        </div>
                    </div>

                    <!-- Hidden inputs for fallback / defaults if required -->
                    <input type="hidden" name="tgl_mulai" id="tgl_mulai" value="{{ date('Y-01-01') }}">
                    <input type="hidden" name="tgl_selesai" id="tgl_selesai" value="{{ date('2040-01-01') }}">

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
                <th class="text-center">IK</th>
                <th class="text-center">Sertifikasi</th>
                <th class="text-center">GTY / DTY</th>
                <th class="text-center">Tunkel</th>
                <th class="text-center">Tun Anak</th>
                <th class="text-center">Kehormatan</th>
              </tr>
            </thead>
            <tbody id="val-tbody-pengajar">
              <tr><td colspan="9" class="text-center text-muted">Memuat data...</td></tr>
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
                { 
                    data: null, 
                    render: function (data, type, row, meta) {
                        return '<strong>' + (meta.row + 1) + '</strong>';
                    } 
                },
                { 
                    data: 'umana',
                    render: function(data, type, row) {
                        if(!data) return '-';
                        var gelarDepan = data.gelar_depan ? data.gelar_depan.trim() + ' ' : '';
                        var gelarBelakang = data.gelar_belakang ? ', ' + data.gelar_belakang.trim() : '';
                        var rawNama = data.nama_lengkap || '';
                        var nama = rawNama.toLowerCase().replace(/\b\w/g, function(l) { return l.toUpperCase(); });
                        var fullNama = (gelarDepan + nama + gelarBelakang).trim();
                        if (type === 'filter' || type === 'sort') {
                            return fullNama + ' ' + rawNama + ' ' + (row.nik || '');
                        }
                        return `
                            <a href="javascript:void(0)" class="text-dark font-weight-bold link-cek-umana text-decoration-none" 
                               data-nik="${row.nik || ''}" 
                               data-nama="${fullNama.replace(/"/g, '&quot;')}" 
                               title="Klik untuk cek validasi tunjangan ganda (Struktural & Pengajar)">
                               <strong>${fullNama}</strong> <i class="fas fa-info-circle text-primary ms-1" style="font-size: 11px; opacity: 0.7;"></i>
                            </a>
                        `;
                    }
                },
                { 
                    data: 'lembaga',
                    render: function(data, type, row) {
                        var lembagaNama = data ? data.nama_lembaga : '-';
                        var kategori = row.kategori ? row.kategori : '-';
                        var prodiInfo = '';
                        if (row.prodi && row.prodi.nama_prodi) {
                            prodiInfo += '<br><small class="text-muted fw-bold">' + row.prodi.nama_prodi + (row.jumlah_sks ? ' (' + row.jumlah_sks + ' SKS/Jam)' : '') + '</small>';
                        } else if (row.jumlah_sks) {
                            prodiInfo += ' <small class="text-muted">(' + row.jumlah_sks + ' SKS/Jam)</small>';
                        }
                        if (type === 'filter' || type === 'sort') {
                            return lembagaNama + ' ' + kategori + ' ' + (row.prodi ? row.prodi.nama_prodi : '') + ' ' + (row.jumlah_sks || '');
                        }
                        return '<strong>' + lembagaNama + '</strong><br><small class="text-success badge badge-success light badge-sm mt-1">' + kategori + '</small>' + prodiInfo;
                    }
                },
                { 
                    data: 'tgl_mulai',
                    render: function(data, type, row) {
                        var mulai = (data && data !== '0000-00-00') ? data : '-';
                        var selesai = (row.tgl_selesai && row.tgl_selesai !== '0000-00-00') ? row.tgl_selesai : 'Sekarang';
                        if (type === 'filter' || type === 'sort') {
                            return mulai + ' ' + selesai;
                        }
                        return '<strong>' + mulai + '<br>Sampai<br>' + selesai + '</strong>';
                    }
                },
                {
                    data: 'umana',
                    render: function(data, type, row) {
                        var rawStatus = (data && data.status_sertifikasi) ? data.status_sertifikasi.trim().toLowerCase() : '';
                        var status = 'Belum';
                        var badgeClass = 'badge-danger';

                        if (rawStatus === 'sudah sertifikasi' || rawStatus === 'ya' || rawStatus === 'sudah') {
                            status = 'Sudah';
                            badgeClass = 'badge-success';
                        } else if (rawStatus && rawStatus !== 'belum' && rawStatus !== 'tidak' && rawStatus !== 'tidak ada') {
                            status = data.status_sertifikasi;
                            badgeClass = 'badge-info';
                        }
                        if (type === 'filter' || type === 'sort') {
                            return status + ' ' + rawStatus;
                        }
                        return '<span class="badge ' + badgeClass + ' rounded-pill">' + status + '</span>';
                    }
                },
                { 
                    data: null,
                    render: function(data, type, row) {
                        function isActive(val) {
                            if (!val) return false;
                            val = val.toString().trim().toLowerCase();
                            return val !== '0' && val !== 'tidak' && val !== 'tidak ada' && val !== 'null' && val !== '-';
                        }

                        var badges = '';
                        var filterList = [];
                        if (isActive(row.tunj_kel)) {
                            badges += '<span class="badge badge-info rounded-pill mb-1 me-1"><i class="fas fa-users me-1"></i> Tunkel</span> ';
                            filterList.push('Tunkel', 'Keluarga');
                        }
                        if (isActive(row.tunj_anak)) {
                            badges += '<span class="badge badge-danger rounded-pill mb-1 me-1"><i class="fas fa-child me-1"></i> Tunj Anak</span> ';
                            filterList.push('Tunj Anak', 'Anak');
                        }
                        if (isActive(row.kehormatan)) {
                            badges += '<span class="badge badge-success rounded-pill mb-1 me-1"><i class="fas fa-star me-1"></i> Kehormatan</span> ';
                            filterList.push('Kehormatan');
                        }
                        if (isActive(row.jafung)) {
                            badges += '<span class="badge badge-primary rounded-pill mb-1 me-1"><i class="fas fa-certificate me-1"></i> Jafung</span> ';
                            filterList.push('Jafung');
                        }
                        if (isActive(row.walkes)) {
                            badges += '<span class="badge rounded-pill mb-1 me-1 text-white" style="background-color: #d848c9;"><i class="fas fa-chalkboard-teacher me-1"></i> Walkes</span> ';
                            filterList.push('Walkes', 'Wali Kelas');
                        }
                        
                        if (badges === '') {
                            badges = '<span class="badge badge-warning rounded-pill mb-1"><i class="fas fa-ban me-1"></i> Tidak Ada</span>';
                            filterList.push('Tidak Ada');
                        }
                        if (type === 'filter' || type === 'sort') {
                            return filterList.join(' ');
                        }
                        return badges;
                    }
                },
                { 
                    data: null,
                    render: function(data, type, row) {
                        var itemStr = encodeURIComponent(JSON.stringify(row));
                        return '<button type="button" class="btn btn-outline-success btn-sm shadow-sm btn-edit-pengajar" style="border-radius: 20px; padding: 5px 15px;" data-item="' + itemStr + '">' +
                               '<i class="fas fa-edit me-1"></i> Edit' +
                               '</button>';
                    }
                }
            ],
            language: {
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>' 
                }
            }
        });

        // Auto populate Umana info when NIK changes
        $('#nik').on('change', function() {
            var selectedOpt = $(this).find('option:selected');
            if (selectedOpt.val()) {
                $('#view_nama_lengkap').val(selectedOpt.data('nama') || '');
                $('#view_ijazah_terakhir').val(selectedOpt.data('ijazah') || '-');
                $('#view_tmt_guru').val(selectedOpt.data('tmtguru') || '-');
                $('#view_tmt_dosen').val(selectedOpt.data('tmtdosen') || '-');
            } else {
                $('#view_nama_lengkap').val('');
                $('#view_ijazah_terakhir').val('');
                $('#view_tmt_guru').val('');
                $('#view_tmt_dosen').val('');
            }
        });

        $('#btnTambah').click(function() {
            $('#formPengajar')[0].reset();
            $('#inputId').val('');
            $('#modalIdLabel').text('Baru');
            $('#view_nama_lengkap').val('');
            $('#view_ijazah_terakhir').val('');
            $('#view_tmt_guru').val('');
            $('#view_tmt_dosen').val('');
            
            // Enable semua field saat Tambah Pengajar baru
            $('#nik').prop('disabled', false);
            $('#id_lembaga').prop('disabled', false);
            $('#kategori').prop('disabled', false);
            $('#tunj_kel').prop('disabled', false);
            $('#tunj_anak').prop('disabled', false);
            $('#kehormatan').prop('disabled', false);
            $('#walkes').prop('disabled', false);
            $('#jafung').prop('disabled', false);
            $('#kategori_trans').prop('disabled', false);

            $('.select2').val('').trigger('change');
            $('#jumlah_sks').val(0);
            $('#status').val('Aktif');
            $('#tunj_kel').val('Tidak');
            $('#tunj_anak').val('Tidak');
            $('#kehormatan').val('Tidak');
            $('#jafung').val('Tidak');
            $('#walkes').val('Tidak');
            $('#modalPengajar').modal('show');
        });

        $('#formPengajar').submit(function(e) {
            e.preventDefault();
            var id = $('#inputId').val();
            var url = id ? '{{ url("/Penempatan/pengajar/update") }}/' + id : '{{ url("/Penempatan/pengajar/store") }}';
            
            // Sertakan juga field yang disabled agar data tetap lengkap jika diperlukan
            var disabled = $(this).find(':disabled').prop('disabled', false);
            var formData = new FormData(this);
            disabled.prop('disabled', true);

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
            var rawItem = $(this).attr('data-item') || $(this).data('item');
            var data;
            try {
                data = (typeof rawItem === 'string') ? JSON.parse(decodeURIComponent(rawItem)) : rawItem;
            } catch(e) {
                try {
                    data = JSON.parse(rawItem);
                } catch(err) {
                    console.error("Gagal parse data pengajar:", err);
                    return;
                }
            }
            
            if (!data) return;

            $('#inputId').val(data.id_pengajar);
            $('#modalIdLabel').text(data.id_pengajar || '-');

            $('#nik').val(data.nik).trigger('change');
            
            // In case humana relation exists directly on data
            if (data.umana) {
                $('#view_nama_lengkap').val(toTitleCase(data.umana.nama_lengkap || ''));
                $('#view_ijazah_terakhir').val(data.umana.ijazah_terakhir || '-');
                $('#view_tmt_guru').val(data.umana.tmt_guru || '-');
                $('#view_tmt_dosen').val(data.umana.tmt_dosen || '-');
            }

            $('#kategori').val(data.kategori || '');
            $('#status').val(data.status || 'Aktif');
            $('#id_lembaga').val(data.id_lembaga).trigger('change');
            $('#id_prodi').val(data.id_prodi || '').trigger('change');
            $('#jumlah_sks').val(data.jumlah_sks != null ? data.jumlah_sks : 0);
            $('#tgl_mulai').val(data.tgl_mulai || '{{ date("Y-01-01") }}');
            $('#tgl_selesai').val(data.tgl_selesai || '{{ date("2040-01-01") }}');
            
            $('#tunj_kel').val(data.tunj_kel == 'Ya' ? 'Ya' : 'Tidak');
            $('#tunj_anak').val(data.tunj_anak == 'Ya' ? 'Ya' : 'Tidak');
            $('#kehormatan').val(data.kehormatan == 'Ya' ? 'Ya' : 'Tidak');
            $('#jafung').val(data.jafung == 'Ya' ? 'Ya' : 'Tidak');
            $('#walkes').val(data.walkes || 'Tidak');
            
            $('#kategori_trans').val(data.kategori_trans || '').trigger('change');
            
            // Kunci NIK agar identitas Umana tidak tertukar saat edit
            $('#nik').prop('disabled', true);
            $('#id_lembaga').prop('disabled', false);
            $('#kategori').prop('disabled', false);
            $('#tunj_kel').prop('disabled', false);
            $('#tunj_anak').prop('disabled', false);
            $('#kehormatan').prop('disabled', false);
            $('#walkes').prop('disabled', false);
            $('#jafung').prop('disabled', false);
            $('#kategori_trans').prop('disabled', false);
            
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
                            ? `<span class="badge ${['DTY', 'GTY'].includes(item.jenis_pengajar) ? 'badge-primary' : 'badge-secondary'} px-2 py-1">${item.jenis_pengajar}</span>`
                            : '-';

                        const isTetap = ['DTY', 'GTY'].includes(item.jenis_pengajar);
                        const isSertif = (sertifVal !== 'Belum' && sertifVal !== 'Belum Sertifikasi');
                        const isGuru = ['GTY', 'GTT'].includes(item.jenis_pengajar);
                        const labelSertif = isGuru ? 'Sergur' : 'Serdos';

                        const gtyDtyBadge = isTetap
                            ? (isSertif
                                ? `<span class="badge badge-info px-2 py-1">${labelSertif}</span>`
                                : '<span class="badge badge-success px-2 py-1"><i class="fa fa-check mr-1 me-1"></i>Ya</span>')
                            : '<span class="badge badge-secondary px-2 py-1">Tidak</span>';

                        htmlPeng += `<tr>
                            <td class="text-center font-weight-bold">${idx + 1}</td>
                            <td><strong>${formattedNama}</strong></td>
                            <td><strong>${item.nama_lembaga || '-'}</strong></td>
                            <td class="text-center font-weight-bold">${jenisBadge}</td>
                            <td class="text-center">${sertifVal}</td>
                            <td class="text-center">${gtyDtyBadge}</td>
                            <td class="text-center">${tunkelBadge}</td>
                            <td class="text-center">${tunjAnakBadge}</td>
                            <td class="text-center">${kehormatanBadge}</td>
                        </tr>`;
                    });
                } else {
                    htmlPeng = '<tr><td colspan="9" class="text-center text-muted">Tidak memiliki data penugasan pengajar lain.</td></tr>';
                }
                $('#val-tbody-pengajar').html(htmlPeng);
            }).fail(function() {
                $('#val-tbody-struktural').html('<tr><td colspan="7" class="text-center text-danger">Gagal memanggil data server.</td></tr>');
                $('#val-tbody-pengajar').html('<tr><td colspan="9" class="text-center text-danger">Gagal memanggil data server.</td></tr>');
            });
        });
    });
</script>
@endpush
@endsection
