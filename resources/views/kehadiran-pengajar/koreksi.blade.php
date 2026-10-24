<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Validasi Pengajar - Koreksi Barokah Guru / Dosen</title>

  <link rel="shortcut icon" type="image/png" href="{{ asset('images/p2s2_fav.png') }}?v={{ filemtime(public_path('images/p2s2_fav.png')) }}" />
  <link rel="icon" type="image/png" href="{{ asset('images/p2s2_fav.png') }}?v={{ filemtime(public_path('images/p2s2_fav.png')) }}" />

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.4.1/css/rowGroup.bootstrap4.min.css">
  <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}">

  <style>
    body { background:#f4f6f8; font-size:13px; color: #2d3748; }
    .table-custom thead th { vertical-align:middle!important; text-align:center; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; }
    .cell-right{ text-align:right }
    .cell-center{ text-align:center }
    .badge-summary{ 
      font-size:.78rem; 
      font-weight: 500;
      padding: 0.32rem 0.55rem; 
      border-radius: 6px;
      display: inline-flex;
      align-items: center;
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      color: #475569;
      box-shadow: 0 1px 2px rgba(0,0,0,0.02);
      transition: all 0.15s ease;
    }
    .badge-summary:hover {
      background: #f1f5f9;
    }
    .badge-summary span {
      font-weight: 700;
      color: #0f172a;
      margin-left: 4px;
    }
    .badge-summary.badge-danger {
      background: #fef2f2 !important;
      border-color: #fecaca !important;
      color: #b91c1c !important;
    }
    .badge-summary.badge-danger span {
      color: #dc2626 !important;
    }
    .badge-summary.badge-success {
      background: #ecfdf5 !important;
      border-color: #a7f3d0 !important;
      color: #047857 !important;
    }
    .badge-summary.badge-success span {
      color: #059669 !important;
    }
    .dataTables_filter {
      display: none !important;
    }
    .sticky-actions{ position:sticky; bottom:0; background:#fff; z-index:100; border-top:1px solid #e2e8f0; padding:.75rem }
    .swal2-container { z-index: 99999 !important; }

    /* Custom Column Highlights */
    .header-group-input { background-color: #fffae0 !important; color: #975a16; }
    .header-group-calc { background-color: #e6fffa !important; color: #285e61; }
    .header-group-result { background-color: #f0fff4 !important; color: #22543d; font-weight: 700; }
    .col-input { background-color: #fffbf0; }
    .col-result { background-color: #f0fff4; color: #2f855a; font-weight: 700; }

    #tblValidasi {
      width: auto !important;
      min-width: 100% !important;
      white-space: nowrap;
    }
    #tblValidasi th,
    #tblValidasi td {
      white-space: nowrap;
      padding: 0.35rem 0.55rem !important;
      vertical-align: middle !important;
      font-size: 12px;
    }
    #tblValidasi thead th {
      padding: 0.4rem 0.5rem !important;
      font-size: 11px;
      letter-spacing: 0.2px;
      vertical-align: middle !important;
      text-align: center;
    }
    #tblValidasi td:nth-child(2) {
      white-space: normal;
      min-width: 180px;
      max-width: 250px;
    }
    .table-container-sticky {
      max-height: 72vh;
      overflow: auto;
      border-radius: 8px;
    }
    #tblValidasi thead th {
      position: sticky;
      background-clip: padding-box;
      box-shadow: inset 0 1px 0 #dee2e6, inset 0 -1px 0 #dee2e6;
    }
    #tblValidasi thead tr:first-child th {
      top: 0;
      z-index: 25;
      height: 32px;
    }
    #tblValidasi thead tr:first-child th[rowspan="2"] {
      z-index: 26;
    }
    #tblValidasi thead tr:nth-child(2) th {
      top: 32px;
      z-index: 24;
      height: 28px;
    }
  </style>
</head>
<body>

<div class="container-fluid my-3">
  <!-- TOP TOOLBAR & INFO CARD -->
  <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; background: #fff;">
    <div class="card-body p-3">
      <!-- Baris 1: Judul Periode & Tombol Aksi Dokumen -->
      <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
        <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
          <h4 class="mb-0 font-weight-bold text-dark mr-1">
            Kehadiran Bulan {{ $periode->bulan ?? '' }} {{ $periode->tahun ?? '' }}
          </h4>
          @php
            $st = $periode->status ?? 'Belum';
            $badgeClass = 'badge-secondary';
            $statusLabel = 'Belum diisi';
            if ($st === 'acc') { $badgeClass = 'badge-success'; $statusLabel = 'Disetujui (ACC)'; }
            elseif ($st === 'Terkirim') { $badgeClass = 'badge-warning text-dark'; $statusLabel = 'Sedang dikoreksi'; }
            elseif ($st === 'Sudah') { $badgeClass = 'badge-danger text-white'; $statusLabel = 'Belum dikirim'; }
            elseif ($st === 'Revisi') { $badgeClass = 'badge-danger'; $statusLabel = 'Terdapat Revisi'; }
            elseif ($st === 'selesai') { $badgeClass = 'badge-primary'; $statusLabel = 'Sudah transfer'; }
          @endphp
          <span class="badge {{ $badgeClass }}" style="font-size: 0.8rem; padding: 0.45em 0.8em; border-radius: 6px;">
            Status: {{ $statusLabel }}
          </span>
          <span class="badge badge-light border text-dark font-weight-bold" style="font-size: 0.8rem; padding: 0.45em 0.8em; border-radius: 6px;">
           {{ $periode->lembaga->nama_lembaga ?? '-' }}
          </span>
        </div>

        @php
          $user = Auth::user();
          $jabatanUser = $user ? $user->jabatan : 'SuperAdmin';
          $isApprover = in_array($jabatanUser, ['SuperAdmin', 'Evaluasi']);
          $canEdit = false;
          if ($isApprover) {
              $canEdit = ($periode->status !== 'acc' && $periode->status !== 'selesai');
          } else {
              $canEdit = ($periode->status === 'Sudah' || $periode->status === 'Revisi');
          }
        @endphp

        <div class="d-flex align-items-center flex-wrap" style="gap: 6px;">
          @if(!empty($periode->file))
            <a class="btn btn-info btn-sm text-white font-weight-bold shadow-sm" target="_blank" href="{{ asset('upload/' . $periode->file) }}">
              <i class="fa fa-file-text mr-1"></i> File Absensi
            </a>
          @endif

          @if(!empty($periode->id_kehadiran_lembaga))
            <a class="btn btn-warning btn-sm text-dark font-weight-bold shadow-sm" target="_blank" href="{{ url('/kehadiran_pengajar/cetak/' . $periode->id_kehadiran_lembaga) }}">
              <i class="fa fa-print mr-1"></i> Cetak
            </a>
          @endif

          @if(!empty($periode->id_lembaga))
            <a class="btn btn-danger btn-sm text-white font-weight-bold shadow-sm" target="_blank" href="{{ url('/kehadiran_pengajar/cetak_potongan/' . $periode->id_lembaga . '/' . ($periode->id_kehadiran_lembaga ?? '')) }}">
              <i class="fa fa-scissors mr-1"></i> Potongan
            </a>
          @endif

          @if($canEdit)
            <button type="button" class="btn btn-primary btn-sm font-weight-bold shadow-sm" onclick="syncTeachers()">
              <i class="fa fa-refresh mr-1"></i> Update Daftar Pengajar
            </button>
          @endif
        </div>
      </div>

      <hr class="my-2" style="border-top: 1px solid #edf2f7;">

      <!-- Baris 2: Ringkasan Badges (Kiri) & Filter Pencarian / Kolom (Kanan) -->
      <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px;">
        <div class="d-flex flex-wrap align-items-center" style="gap: 4px;">
          <span class="badge-summary">Mengajar:<span id="sumMengajar">Rp {{ number_format($totals['total_mengajar'] ?? 0, 0, ',', '.') }}</span></span>
          <span class="badge-summary">DTY:<span id="sumDty">Rp {{ number_format($totals['total_dty'] ?? 0, 0, ',', '.') }}</span></span>
          <span class="badge-summary">Jafung:<span id="sumJafung">Rp {{ number_format($totals['total_jafung'] ?? 0, 0, ',', '.') }}</span></span>
          <span class="badge-summary">Transport:<span id="sumKehadiran">Rp {{ number_format($totals['total_kehadiran'] ?? 0, 0, ',', '.') }}</span></span>
          <span class="badge-summary">Hadir 15rb:<span id="sumHadir15">Rp {{ number_format($totals['total_hadir_15'] ?? 0, 0, ',', '.') }}</span></span>
          <span class="badge-summary">Hadir 10rb:<span id="sumHadir10">Rp {{ number_format($totals['total_hadir_10'] ?? 0, 0, ',', '.') }}</span></span>
          <span class="badge-summary">Piket:<span id="sumPiket">Rp {{ number_format($totals['total_piket'] ?? 0, 0, ',', '.') }}</span></span>
          <span class="badge-summary">Tunkel:<span id="sumTunkel">Rp {{ number_format($totals['total_tunkel'] ?? 0, 0, ',', '.') }}</span></span>
          <span class="badge-summary">Anak:<span id="sumTunjanak">Rp {{ number_format($totals['total_tunjanak'] ?? 0, 0, ',', '.') }}</span></span>
          <span class="badge-summary">Kehormatan:<span id="sumKehormatan">Rp {{ number_format($totals['total_kehormatan'] ?? 0, 0, ',', '.') }}</span></span>
          <span class="badge-summary">Walkes:<span id="sumWalkes">Rp {{ number_format($totals['total_walkes'] ?? 0, 0, ',', '.') }}</span></span>
          <span class="badge-summary">Tambahan:<span id="sumTambahan">Rp {{ number_format($totals['total_tambahan'] ?? 0, 0, ',', '.') }}</span></span>
          <span class="badge-summary badge-danger">Potongan:<span id="sumPotongan">Rp {{ number_format($totals['total_potongan'] ?? 0, 0, ',', '.') }}</span></span>
          <span class="badge-summary badge-success font-weight-bold">Grand Total:<span id="sumGrand">Rp {{ number_format($totals['grand_total'] ?? 0, 0, ',', '.') }}</span></span>
        </div>

        <div class="d-flex align-items-center ml-auto" style="gap: 6px;">
          <div class="input-group input-group-sm shadow-sm" style="width: 200px;">
            <div class="input-group-prepend">
              <span class="input-group-text bg-white border-right-0" style="font-size: 0.8rem;"><i class="fa fa-search text-muted"></i></span>
            </div>
            <input type="text" id="customSearchPengajar" class="form-control border-left-0 pl-1" placeholder="Cari nama pengajar..." style="font-size: 0.8rem; height: 31px;">
          </div>

          <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle font-weight-bold bg-white shadow-sm" type="button" id="btnTampilanKolom" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="height: 31px; font-size: 0.8rem;">
              <i class="fa fa-columns mr-1 text-primary"></i> Kolom
            </button>
            <div class="dropdown-menu dropdown-menu-right shadow-sm" aria-labelledby="btnTampilanKolom" style="min-width: 220px;">
              <h6 class="dropdown-header text-uppercase font-weight-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Tampilkan / Sembunyikan</h6>
              <div class="dropdown-item px-3 py-2">
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input toggle-vis" id="colMaster" data-columns="3,4,5,6,7,8,9,10" checked>
                  <label class="custom-control-label" style="cursor:pointer;" for="colMaster">Grup Data Master</label>
                </div>
              </div>
              <div class="dropdown-item px-3 py-2">
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input toggle-vis" id="colKehadiran" data-columns="11,12,13,14,15,16,17,18,19" checked>
                  <label class="custom-control-label" style="cursor:pointer;" for="colKehadiran">Grup Kehadiran</label>
                </div>
              </div>
              <div class="dropdown-item px-3 py-2">
                <div class="custom-control custom-switch">
                  <input type="checkbox" class="custom-control-input toggle-vis" id="colTunjangan" data-columns="20,21,22,23,24" checked>
                  <label class="custom-control-label" style="cursor:pointer;" for="colTunjangan">Grup Tunjangan</label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MAIN TABLE -->
  <div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-0">
      <div class="table-responsive table-container-sticky">
        <form id="form">
          @csrf
          <input type="hidden" name="id_kehadiran_lembaga" value="{{ $periode->id_kehadiran_lembaga }}">

          <table id="tblValidasi" class="table table-bordered table-striped mb-0">
            <thead>
              <tr>
                <th rowspan="2" class="text-center bg-white" style="width: 35px;">No</th>
                <th rowspan="2" class="bg-white" style="min-width: 170px;">Nama Pengajar</th>
                <th rowspan="2" class="bg-white">Prodi</th>
                <th rowspan="2" class="text-center header-group-input">IT</th>
                <th rowspan="2" class="text-center header-group-input">TMT</th>
                <th rowspan="2" class="text-center header-group-input">MP</th>
                <th rowspan="2" class="text-center header-group-input">SKS</th>
                <th rowspan="2" class="text-center header-group-calc text-muted">Rank</th>
                <th rowspan="2" class="text-center header-group-calc">Mengajar</th>
                <th rowspan="2" class="text-center header-group-calc">DTY</th>
                <th rowspan="2" class="text-center header-group-calc">Jafung</th>

                <!-- Kehadiran Breakdown -->
                <th colspan="2" class="text-center header-group-input">Hadir Normal</th>
                <th colspan="2" class="text-center header-group-input">Hadir 15rb</th>
                <th colspan="2" class="text-center header-group-input">Hadir 10rb</th>
                <th colspan="3" class="text-center header-group-input">Piket</th>

                <!-- Tunjangan -->
                <th colspan="5" class="text-center header-group-calc">Tunjangan Lain</th>

                <th rowspan="2" class="text-center header-group-calc text-muted">Jumlah</th>
                <th rowspan="2" class="text-center text-danger">Potongan</th>
                <th rowspan="2" class="text-right header-group-result">Diterima</th>
                <th rowspan="2" class="text-center bg-white" style="width: 85px;">Aksi</th>
              </tr>
            <tr>
              <!-- Subheaders Kehadiran -->
              <th class="text-center header-group-input" style="font-size: 11px;">Jml</th>
              <th class="text-right header-group-input" style="font-size: 11px;">Nominal</th>
              <th class="text-center header-group-input" style="font-size: 11px;">Jml</th>
              <th class="text-right header-group-input" style="font-size: 11px;">Nominal</th>
              <th class="text-center header-group-input" style="font-size: 11px;">Jml</th>
              <th class="text-right header-group-input" style="font-size: 11px;">Nominal</th>
              <th class="text-center header-group-input" style="font-size: 11px;">Jml</th>
              <th class="text-right header-group-input" style="font-size: 11px;">Rate</th>
              <th class="text-right header-group-input" style="font-size: 11px;">Nominal</th>

              <!-- Subheaders Tunjangan -->
              <th class="text-right header-group-calc" style="font-size: 11px;">Tunkel</th>
              <th class="text-right header-group-calc" style="font-size: 11px;">Anak</th>
              <th class="text-right header-group-calc" style="font-size: 11px;">Kehormatan</th>
              <th class="text-right header-group-calc" style="font-size: 11px;">Walkes</th>
              <th class="text-right header-group-calc" style="font-size: 11px;">Tambahan</th>
            </tr>
          </thead>
          <tbody>
            @foreach($isilist as $idx => $key)
              @php
                $fullName = trim(($key->gelar_depan ?? '') . ' ' . ucwords(strtolower($key->nama_lengkap ?? '')) . ' ' . ($key->gelar_belakang ?? ''));
                $tmtDisplay = '-';
                if (!empty($key->tmt_dosen) && ($key->kategori === 'DTY' || $key->kategori === 'DTT')) {
                    $tmtDisplay = date('Y', strtotime($key->tmt_dosen));
                } elseif (!empty($key->tmt_maif) && isset($key->nama_lembaga) && $key->nama_lembaga === "Ma'had Aly Sukorejo") {
                    $tmtDisplay = date('Y', strtotime($key->tmt_maif));
                } elseif (!empty($key->tmt_guru)) {
                    $tmtDisplay = date('Y', strtotime($key->tmt_guru));
                }

                $changed = $key->komponen_berubah ?? [];
                $isWarning = !empty($key->is_warning);
                $selisih = $key->selisih_komponen ?? 0;
                $isSerti = !empty($key->status_sertifikasi) && in_array(strtolower(trim($key->status_sertifikasi)), ['sudah', 'ya', 'sudah sertifikasi']);
                $labelSerti = in_array($key->kategori, ['GTY', 'GTT']) ? 'Sergur' : 'Serdos';
              @endphp
              <tr id="row-{{ $key->id_kehadiran_pengajar }}" class="{{ $isWarning ? 'bg-warning' : '' }}">
                <td class="cell-center {{ $isWarning ? 'text-dark font-weight-bold' : 'text-muted' }}">{{ $idx + 1 }}</td>
                <td>
                  <div class="font-weight-bold" style="font-size: 0.9rem; line-height: 1.2;">
                    <a href="javascript:void(0)" class="text-dark link-cek-umana text-decoration-none" data-nik="{{ $key->nik ?? '' }}" data-nama="{{ htmlspecialchars($fullName, ENT_QUOTES) }}" title="Klik untuk cek validasi tunjangan ganda (Struktural & Pengajar)">
                      {{ $fullName }}
                      <i class="fa fa-info-circle text-primary ml-1" style="font-size: 0.8rem;"></i>
                    </a>
                  </div>
                  <span class="badge badge-light border mt-1" style="font-size: 0.7rem;">{{ $key->kategori }}</span>
                  @if(!empty($key->status_aktif) && ($key->status_aktif === 'Cuti 50%' || $key->status_aktif === 'Cuti 100%'))
                    <span class="badge badge-danger ml-1">{{ $key->status_aktif }}</span>
                  @endif
                  @if($isWarning)
                    <span class="badge badge-danger shadow-sm mt-1 px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.3px;">
                      <i class="fas fa-exclamation-triangle mr-1"></i> PERUBAHAN KOMPONEN ({{ ($selisih > 0 ? '+' : '') . number_format($selisih, 0, ',', '.') }})
                    </span>
                  @endif
                </td>
                <td class="text-muted align-middle" style="font-size: 0.8rem;">
                  {{ !empty($key->nama_prodi) ? $key->nama_prodi : '-' }}
                </td>
                <td class="cell-center align-middle">{{ $key->ijazah_terakhir ?? '-' }}</td>
                <td class="cell-center align-middle">{{ $tmtDisplay }}</td>
                <td class="cell-center align-middle">{{ $key->mp ?? 0 }} Thn</td>
                <td class="cell-center align-middle font-weight-bold">{{ $key->jumlah_sks ?? 0 }}</td>
                <td class="cell-right text-muted align-middle">Rp {{ number_format($key->rank ?? 0, 0, ',', '.') }}</td>
                <td class="cell-right align-middle {{ in_array('mengajar', $changed) ? 'text-danger font-weight-bold' : 'font-weight-bold text-dark' }}">
                  Rp {{ number_format($key->mengajar ?? 0, 0, ',', '.') }}
                </td>
                <td class="align-middle {{ in_array('dty', $changed) ? 'text-danger font-weight-bold' : '' }} {{ $isSerti ? 'text-center' : 'cell-right' }}">
                  @if($isSerti)
                    <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 0.72rem; letter-spacing: 0.3px;">{{ $labelSerti }}</span>
                  @else
                    Rp {{ number_format($key->dty ?? 0, 0, ',', '.') }}
                  @endif
                </td>
                <td class="cell-right align-middle {{ in_array('jafung', $changed) ? 'text-danger font-weight-bold' : '' }}">
                  Rp {{ number_format($key->jafung ?? 0, 0, ',', '.') }}
                </td>

                <!-- Kehadiran Breakdown -->
                <td class="cell-center col-input js-val-hadir">{{ $key->jumlah_hadir ?? 0 }}</td>
                <td class="cell-right text-muted align-middle js-nominal-hadir">Rp {{ number_format($key->nominal_kehadiran ?? 0, 0, ',', '.') }}</td>

                <td class="cell-center col-input js-val-hadir15">{{ $key->jumlah_hadir_15 ?? 0 }}</td>
                <td class="cell-right text-muted align-middle js-nominal-15">Rp {{ number_format($key->nominal_hadir_15 ?? 0, 0, ',', '.') }}</td>

                <td class="cell-center col-input js-val-hadir10">{{ $key->jumlah_hadir_10 ?? 0 }}</td>
                <td class="cell-right text-muted align-middle js-nominal-10">Rp {{ number_format($key->nominal_hadir_10 ?? 0, 0, ',', '.') }}</td>

                <td class="cell-center col-input js-val-piket">{{ $key->jumlah_hadir_piket ?? 0 }}</td>
                <td class="cell-right text-muted align-middle js-nominal-rankpiket" style="font-size: 11px;">Rp {{ number_format($key->rank_piket ?? 0, 0, ',', '.') }}</td>
                <td class="cell-right text-muted align-middle js-nominal-piket">Rp {{ number_format($key->barokah_piket ?? 0, 0, ',', '.') }}</td>

                <!-- Tunjangan breakdown -->
                <td class="cell-right align-middle {{ in_array('tunkel', $changed) ? 'text-danger font-weight-bold' : '' }}">
                  Rp {{ number_format($key->tunkel ?? 0, 0, ',', '.') }}
                </td>
                <td class="align-middle {{ in_array('tun_anak', $changed) ? 'text-danger font-weight-bold' : '' }} {{ $isSerti ? 'text-center' : 'cell-right' }}">
                  @if($isSerti)
                    <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 0.72rem; letter-spacing: 0.3px;">{{ $labelSerti }}</span>
                  @else
                    Rp {{ number_format($key->tun_anak ?? ($key->tunja_anak ?? 0), 0, ',', '.') }}
                  @endif
                </td>
                <td class="align-middle {{ in_array('kehormatan', $changed) ? 'text-danger font-weight-bold' : '' }} {{ $isSerti ? 'text-center' : 'cell-right' }}">
                  @if($isSerti)
                    <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 0.72rem; letter-spacing: 0.3px;">{{ $labelSerti }}</span>
                  @else
                    Rp {{ number_format($key->kehormatan ?? 0, 0, ',', '.') }}
                  @endif
                </td>
                <td class="cell-right align-middle {{ in_array('walkes', $changed) ? 'text-danger font-weight-bold' : '' }}">
                  Rp {{ number_format($key->walkes ?? ($key->tunj_walkes ?? 0), 0, ',', '.') }}
                </td>
                <td class="cell-right align-middle">
                  Rp {{ number_format($key->khusus ?? ($key->tambahan ?? 0), 0, ',', '.') }}
                </td>

                <!-- Potongan & Result -->
                <td class="cell-right align-middle text-muted js-jumlah">
                  Rp {{ number_format($key->jumlah ?? 0, 0, ',', '.') }}
                </td>
                <td class="cell-right text-danger align-middle js-row-potongan">
                  <a href="javascript:void(0)" class="text-danger link-potongan" data-id="{{ $key->id_pengajar }}" data-nama="{{ htmlspecialchars($fullName, ENT_QUOTES) }}">
                    Rp {{ number_format($key->potongan ?? 0, 0, ',', '.') }}
                  </a>
                </td>
                <td class="cell-right col-result align-middle js-diterima">
                  Rp {{ number_format($key->diterima ?? 0, 0, ',', '.') }}
                </td>
                <td class="cell-center align-middle">
                  <div class="d-flex align-items-center justify-content-center">
                    @if($canEdit)
                      <button type="button" class="btn btn-sm btn-outline-primary btn-edit mr-1"
                        data-toggle="tooltip"
                        title="Koreksi jumlah kehadiran"
                        data-id="{{ $key->id_kehadiran_pengajar }}"
                        data-hadir="{{ $key->jumlah_hadir ?? 0 }}"
                        data-hadir15="{{ $key->jumlah_hadir_15 ?? 0 }}"
                        data-hadir10="{{ $key->jumlah_hadir_10 ?? 0 }}"
                        data-piket="{{ $key->jumlah_hadir_piket ?? 0 }}"
                        data-nama="{{ htmlspecialchars($fullName, ENT_QUOTES) }}">
                        Edit
                      </button>

                      <input type="hidden" name="catatan_khusus[{{ $key->id_pengajar }}]" id="catatan_val_{{ $key->id_pengajar }}" value="{{ $key->catatan_khusus_umana ?? '' }}">

                      @php
                        $has_note = !empty(trim($key->catatan_khusus_umana ?? ''));
                        $btn_class = $has_note ? 'btn-info shadow-sm' : 'btn-outline-info';
                        $icon_class = $has_note ? 'fa-commenting' : 'fa-comment-o';
                      @endphp

                      <button type="button" class="btn btn-sm {{ $btn_class }} btn-catatan position-relative"
                              title="Catatan Evaluasi"
                              data-id="{{ $key->id_pengajar }}"
                              data-id-kehadiran="{{ $key->id_kehadiran_pengajar }}"
                              data-nama="{{ htmlspecialchars($fullName, ENT_QUOTES) }}">
                        <i class="fa {{ $icon_class }} icon-catatan-{{ $key->id_pengajar }}"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $has_note ? '' : 'd-none' }} badge-catatan-{{ $key->id_pengajar }}" style="font-size: 0.5rem; transform: translate(-30%, -30%);">★</span>
                      </button>
                    @else
                      <button type="button" class="btn btn-sm btn-outline-secondary mr-1" disabled title="Data sudah disetujui">Edit</button>
                      @if(!empty($key->catatan_khusus_umana))
                        <button type="button" class="btn btn-sm btn-info" onclick="Swal.fire('Catatan', '{{ htmlspecialchars($key->catatan_khusus_umana, ENT_QUOTES) }}', 'info')"><i class="fa fa-comment"></i></button>
                      @endif
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr class="font-weight-bold bg-light">
              <td colspan="8" class="text-right text-uppercase px-3">Total Seluruh:</td>
              <td class="text-right" id="ft-mengajar">Rp {{ number_format($totals['total_mengajar'] ?? 0, 0, ',', '.') }}</td>
              <td class="text-right" id="ft-dty">Rp {{ number_format($totals['total_dty'] ?? 0, 0, ',', '.') }}</td>
              <td class="text-right" id="ft-jafung">Rp {{ number_format($totals['total_jafung'] ?? 0, 0, ',', '.') }}</td>
              <td></td>
              <td class="text-right" id="ft-kehadiran">Rp {{ number_format($totals['total_kehadiran'] ?? 0, 0, ',', '.') }}</td>
              <td></td>
              <td class="text-right" id="ft-hadir15">Rp {{ number_format($totals['total_hadir_15'] ?? 0, 0, ',', '.') }}</td>
              <td></td>
              <td class="text-right" id="ft-hadir10">Rp {{ number_format($totals['total_hadir_10'] ?? 0, 0, ',', '.') }}</td>
              <td></td>
              <td></td>
              <td class="text-right" id="ft-piket">Rp {{ number_format($totals['total_piket'] ?? 0, 0, ',', '.') }}</td>
              <td class="text-right" id="ft-tunkel">Rp {{ number_format($totals['total_tunkel'] ?? 0, 0, ',', '.') }}</td>
              <td class="text-right" id="ft-tunjanak">Rp {{ number_format($totals['total_tunjanak'] ?? 0, 0, ',', '.') }}</td>
              <td class="text-right" id="ft-kehormatan">Rp {{ number_format($totals['total_kehormatan'] ?? 0, 0, ',', '.') }}</td>
              <td class="text-right" id="ft-walkes">Rp {{ number_format($totals['total_walkes'] ?? 0, 0, ',', '.') }}</td>
              <td class="text-right" id="ft-tambahan">Rp {{ number_format($totals['total_tambahan'] ?? 0, 0, ',', '.') }}</td>
              <td class="text-right text-muted" id="ft-jumlah">Rp {{ number_format($totals['total_jumlah'] ?? 0, 0, ',', '.') }}</td>
              <td class="text-right text-danger" id="ft-potongan">Rp {{ number_format($totals['total_potongan'] ?? 0, 0, ',', '.') }}</td>
              <td class="text-right text-success" id="ft-grand">Rp {{ number_format($totals['grand_total'] ?? 0, 0, ',', '.') }}</td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </form>
      </div>
    </div>
  </div>

  <!-- Catatan Evaluasi / Pimpinan -->
  @if($periode->status !== 'acc' && $periode->status !== 'selesai')
    <div class="card mt-3 border-left-primary shadow-sm border-0">
      <div class="card-body bg-light p-3">
        <label class="font-weight-bold text-primary"><i class="fas fa-comment-dots mr-1"></i> Catatan Evaluasi</label>
        <textarea name="catatan_umum_pimpinan" id="catatan_umum_pimpinan" class="form-control" rows="2" placeholder="" {{ $isApprover ? '' : 'readonly' }}>{{ $periode->catatan_umum_pimpinan ?? '' }}</textarea>
      </div>
    </div>
  @else
    @if(!empty($periode->catatan_umum_pimpinan))
      <div class="alert alert-warning mt-3">
        <h6 class="font-weight-bold"><i class="fas fa-comment-dots mr-1"></i> Catatan :</h6>
        <p class="mb-0">{!! nl2br(e($periode->catatan_umum_pimpinan)) !!}</p>
      </div>
    @endif
  @endif

  <!-- Sticky Actions -->
  <div class="sticky-actions d-flex justify-content-end align-items-center mt-3">
    @if(($periode->status === 'Sudah' || $periode->status === 'Revisi') && $jabatanUser !== 'SDM')
      @php $btnTeks = ($periode->status === 'Revisi') ? 'Kirim Hasil Revisi' : 'Kirim Validasi'; @endphp
      <button type="button" id="btnKirim" onclick="kirimValidasi()" class="btn btn-primary font-weight-bold px-4 shadow-sm">
        <i class="fas fa-paper-plane mr-1"></i> {{ $btnTeks }}
      </button>
    @elseif($periode->status === 'Terkirim' && $isApprover)
      <button type="button" id="btnKembalikan" onclick="validasiAksi('revisi')" class="btn btn-warning mr-2 text-dark font-weight-bold">
        <i class="fas fa-undo mr-1"></i> Kembalikan (Revisi)
      </button>
      <button type="button" id="btnSetujui" onclick="validasiAksi('setuju')" class="btn btn-success font-weight-bold shadow-sm px-4">
        <i class="fas fa-check mr-1"></i> Setujui (ACC)
      </button>
    @endif
  </div>
</div>

<!-- Modal Catatan Spesifik Pengajar -->
<div class="modal fade" id="modalCatatan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Catatan</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <p><strong>Pengajar:</strong> <span id="cat-nama"></span></p>
        <input type="hidden" id="cat-id">
        <div class="form-group">
          <label class="font-weight-bold">Catatan</label>
          @php
            $can_fill_catatan = false;
            if (in_array($jabatanUser, ['SuperAdmin', 'Evaluasi'])) {
                $can_fill_catatan = true;
                $placeholder_teks = 'Tuliskan catatan disini...';
            } else {
                if ($periode->status === 'Sudah' || $periode->status === 'Revisi' || $periode->status === 'Belum') {
                    $can_fill_catatan = true;
                }
                $placeholder_teks = 'Tuliskan catatan khusus dari lembaga...';
            }
          @endphp
          <textarea id="cat-val" class="form-control" rows="4" placeholder="{{ $placeholder_teks }}" {{ $can_fill_catatan ? '' : 'readonly' }}></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Tutup</button>
        @if($can_fill_catatan)
          <button type="button" id="btnSimpanCatatan" class="btn btn-primary">Simpan Catatan</button>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Koreksi Kehadiran -->
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <h6 class="modal-title font-weight-bold text-dark">Koreksi: <span id="edit-nama" class="text-primary"></span></h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edit-id">
        <div class="form-group mb-2">
          <label class="small font-weight-bold mb-1">Hadir Normal</label>
          <input type="number" class="form-control form-control-sm" id="edit-hadir" min="0">
        </div>
        <div class="form-group mb-2">
          <label class="small font-weight-bold mb-1">Hadir 15.000</label>
          <input type="number" class="form-control form-control-sm" id="edit-hadir15" min="0">
        </div>
        <div class="form-group mb-2">
          <label class="small font-weight-bold mb-1">Hadir 10.000</label>
          <input type="number" class="form-control form-control-sm" id="edit-hadir10" min="0">
        </div>
        <div class="form-group mb-2">
          <label class="small font-weight-bold mb-1">Jam Piket</label>
          <input type="number" class="form-control form-control-sm" id="edit-piket" min="0">
        </div>
      </div>
      <div class="modal-footer bg-light py-2">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary btn-sm" id="btnSimpanEdit">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Potongan Pengajar -->
<div class="modal fade" id="modalPotongan" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-light">
        <h6 class="modal-title font-weight-bold text-dark">Potongan: <span id="potonganPegawaiName" class="text-danger"></span></h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="formPotongan">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="id_pengajar" id="potongan_id_pengajar">
          <div class="table-responsive">
            <table class="table table-sm table-bordered">
              <thead class="thead-light">
                <tr>
                  <th style="width: 35%;">Jenis Potongan</th>
                  <th style="width: 25%;">Nominal (Rp)</th>
                  <th style="width: 20%;">Min Periode</th>
                  <th style="width: 20%;">Max Periode</th>
                </tr>
              </thead>
              <tbody id="potonganBody">
                <tr><td colspan="4" class="text-center text-muted">Memuat...</td></tr>
              </tbody>
            </table>
          </div>
          @if($canEdit)
            <button type="button" class="btn btn-outline-success btn-sm mt-2" id="btnTambahPotongan">
              <i class="fas fa-plus mr-1"></i> Tambah Baris Potongan
            </button>
          @endif
        </div>
        <div class="modal-footer bg-light py-2">
          <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
          @if($canEdit)
            <button type="submit" class="btn btn-danger btn-sm" id="btnSimpanPotongan">Simpan Potongan</button>
          @endif
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Validasi Silang Tunjangan Ganda (Struktural & Pengajar) -->
<div class="modal fade" id="modalCekValidasiUmana" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white py-2">
        <h5 class="modal-title font-weight-bold" style="font-size: 1.05rem;">
          <i class="fa fa-id-card mr-2"></i> Validasi Tunjangan Umana'
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <!-- Info Profil Umana' -->
        <div class="card bg-light border-0 mb-3">
          <div class="card-body py-2 px-3">
            <span class="text-muted small">Nama Umana':</span>
            <h6 class="font-weight-bold text-dark mb-0" id="val-umana-nama">-</h6>
          </div>
        </div>

        <div class="alert alert-warning py-2 px-3 small mb-3">
          <i class="fa fa-shield mr-1"></i>
          <strong>Petunjuk:</strong> Tabel di bawah menampilkan seluruh SK penempatan aktif Umana' ini di unit <strong>Struktural</strong> dan <strong>Pengajar</strong>. Pastikan tidak ada duplikasi penerimaan <em>Tunkel</em>, <em>Tunjangan Anak</em>, atau <em>Kehormatan</em> di lebih dari satu penempatan.
        </div>

        <!-- 1. TABEL STRUKTURAL -->
        <h6 class="font-weight-bold text-primary mb-2" style="font-size: 0.9rem;">
          <i class="fa fa-sitemap mr-1"></i> 1. Penempatan Struktural
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
          <i class="fa fa-graduation-cap mr-1"></i> 2. Penugasan Pengajar (Guru / Dosen)
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
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/rowgroup/1.4.1/js/dataTables.rowGroup.min.js"></script>
<script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

<script>
  let table = null;
  const id_leb_storage = "{{ $periode->id_kehadiran_lembaga }}";

  $(document).ready(function() {
    // Sinkronisasi catatan pengajar dari LocalStorage
    $('.btn-catatan').each(function() {
      let id = $(this).data('id');
      let savedKhusus = localStorage.getItem('catatan_pengajar_' + id_leb_storage + '_' + id);
      
      if (savedKhusus !== null) {
        $('#catatan_val_' + id).val(savedKhusus);
        let btn = $(this);
        let icon = $('.icon-catatan-' + id);
        let badge = $('.badge-catatan-' + id);
        
        if (savedKhusus.trim() !== '') {
          btn.removeClass('btn-outline-info').addClass('btn-info shadow-sm');
          icon.removeClass('fa-comment-o').addClass('fa-commenting');
          badge.removeClass('d-none');
        } else {
          btn.removeClass('btn-info shadow-sm').addClass('btn-outline-info');
          icon.removeClass('fa-commenting').addClass('fa-comment-o');
          badge.addClass('d-none');
        }
      }
    });

    table = $('#tblValidasi').DataTable({
      paging: false,
      searching: true,
      info: false,
      ordering: false,
      dom: 't',
      rowGroup: {
        dataSrc: 2,
        startRender: function (rows, group) {
          var groupName = (group === '-' || group === '' || !group) ? 'LAINNYA / TIDAK ADA PRODI' : group;
          return $('<tr class="bg-light font-weight-bold text-dark"/>')
            .append('<td colspan="29" class="text-left pl-3 text-uppercase">PRODI: ' + groupName + '</td>');
        }
      }
    });

    // Custom Search
    $('#customSearchPengajar').on('keyup change', function() {
      table.search($(this).val()).draw();
    });

    // Toggle Kolom
    $('.dropdown-menu').on('click', function(e) { e.stopPropagation(); });
    $('.toggle-vis').on('change', function () {
      var columnsStr = $(this).attr('data-columns');
      var columns = columnsStr.split(',').map(Number);
      var isVisible = $(this).is(':checked');
      table.columns(columns).visible(isVisible);
    });

    // Modal Validasi Silang Umana' (Tunjangan Ganda Struktural vs Pengajar)
    $(document).on('click', '.link-cek-umana', function() {
      const nik = $(this).data('nik');
      const namaDefault = $(this).data('nama');

      if (!nik) {
        Swal.fire('Info', 'NIK Umana tidak ditemukan.', 'warning');
        return;
      }

      $('#val-umana-nama').text(namaDefault);
      $('#val-tbody-struktural').html('<tr><td colspan="7" class="text-center text-muted">Memuat data penempatan struktural...</td></tr>');
      $('#val-tbody-pengajar').html('<tr><td colspan="8" class="text-center text-muted">Memuat data penugasan pengajar...</td></tr>');

      $('#modalCekValidasiUmana').modal('show');

      $.getJSON("{{ url('/kehadiran_pengajar/cekValidasiUmana') }}/" + nik, function(res) {
        if(!res || !res.status) {
          $('#val-tbody-struktural').html('<tr><td colspan="7" class="text-center text-danger">Gagal memuat data.</td></tr>');
          $('#val-tbody-pengajar').html('<tr><td colspan="8" class="text-center text-danger">Gagal memuat data.</td></tr>');
          return;
        }

        // Update profil
        $('#val-umana-nama').text(res.umana.nama_lengkap);

        const sertifVal = res.umana.status_sertifikasi || 'Belum Sertifikasi';

        // 1. Render Tabel Struktural
        let htmlStruk = '';
        if(res.struktural && res.struktural.length > 0) {
          res.struktural.forEach((item, idx) => {
            const tunkelBadge = (item.tunj_kel === 'Ya')
              ? '<span class="badge badge-success px-2 py-1"><i class="fa fa-check mr-1"></i>Ya</span>'
              : '<span class="badge badge-secondary px-2 py-1">Tidak</span>';

            const tunjAnakBadge = (item.tunj_anak === 'Ya')
              ? '<span class="badge badge-success px-2 py-1"><i class="fa fa-check mr-1"></i>Ya</span>'
              : '<span class="badge badge-secondary px-2 py-1">Tidak</span>';

            const kehormatanBadge = (item.kehormatan === 'Ya')
              ? '<span class="badge badge-success px-2 py-1"><i class="fa fa-check mr-1"></i>Ya</span>'
              : '<span class="badge badge-secondary px-2 py-1">Tidak</span>';

            htmlStruk += `<tr>
              <td class="text-center font-weight-bold">${idx + 1}</td>
              <td><strong>${res.umana.nama_lengkap}</strong><br><small class="text-muted">${item.nama_jabatan || '-'}</small></td>
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
              ? '<span class="badge badge-success px-2 py-1"><i class="fa fa-check mr-1"></i>Ya</span>'
              : '<span class="badge badge-secondary px-2 py-1">Tidak</span>';

            const tunjAnakBadge = (item.tunj_anak === 'Ya')
              ? '<span class="badge badge-success px-2 py-1"><i class="fa fa-check mr-1"></i>Ya</span>'
              : '<span class="badge badge-secondary px-2 py-1">Tidak</span>';

            const kehormatanBadge = (item.kehormatan === 'Ya')
              ? '<span class="badge badge-success px-2 py-1"><i class="fa fa-check mr-1"></i>Ya</span>'
              : '<span class="badge badge-secondary px-2 py-1">Tidak</span>';

            const jenisBadge = item.jenis_pengajar
              ? `<span class="badge badge-primary px-2 py-1">${item.jenis_pengajar}</span>`
              : '-';

            htmlPeng += `<tr>
              <td class="text-center font-weight-bold">${idx + 1}</td>
              <td><strong>${res.umana.nama_lengkap}</strong></td>
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

    // Edit Modal Trigger
    $('.btn-edit').click(function() {
      const id = $(this).data('id');
      const nama = $(this).data('nama');
      const hadir = $(this).data('hadir');
      const hadir15 = $(this).data('hadir15');
      const hadir10 = $(this).data('hadir10');
      const piket = $(this).data('piket');

      $('#edit-nama').text(nama);
      $('#edit-id').val(id);
      $('#edit-hadir').val(hadir);
      $('#edit-hadir15').val(hadir15);
      $('#edit-hadir10').val(hadir10);
      $('#edit-piket').val(piket);

      $('#modalEdit').modal('show');
    });

    // Simpan Perubahan Edit Row
    $('#btnSimpanEdit').click(function() {
      const id = $('#edit-id').val();
      const hadir = parseInt($('#edit-hadir').val()) || 0;
      const hadir15 = parseInt($('#edit-hadir15').val()) || 0;
      const hadir10 = parseInt($('#edit-hadir10').val()) || 0;
      const piket = parseInt($('#edit-piket').val()) || 0;
      const btn = $(this);

      btn.prop('disabled', true).text('Menyimpan...');

      $.ajax({
        url: '{{ url("/kehadiran_pengajar/updateRow") }}',
        type: 'POST',
        dataType: 'json',
        data: {
          _token: '{{ csrf_token() }}',
          id_kehadiran_pengajar: id,
          jumlah_hadir: hadir,
          jumlah_hadir_15: hadir15,
          jumlah_hadir_10: hadir10,
          jumlah_hadir_piket: piket
        },
        success: function(res) {
          btn.prop('disabled', false).text('Simpan');
          if (res.status) {
            const row = $('#row-' + id);
            row.find('.js-val-hadir').text(hadir);
            row.find('.js-val-hadir15').text(hadir15);
            row.find('.js-val-hadir10').text(hadir10);
            row.find('.js-val-piket').text(piket);

            row.find('.js-nominal-hadir').text(res.data.jml_kehadiran);
            row.find('.js-nominal-15').text(res.data.nominal_hadir_15);
            row.find('.js-nominal-10').text(res.data.nominal_hadir_10);
            row.find('.js-nominal-rankpiket').text(res.data.rank_piket);
            row.find('.js-nominal-piket').text(res.data.barokah_piket);
            row.find('.js-jumlah').text(res.data.jumlah);
            row.find('.js-diterima').text(res.data.diterima);

            const btnEdit = row.find('.btn-edit');
            btnEdit.data('hadir', hadir);
            btnEdit.data('hadir15', hadir15);
            btnEdit.data('hadir10', hadir10);
            btnEdit.data('piket', piket);

            // Update footer totals
            updateFooter(res.totals);

            $('#modalEdit').modal('hide');
            Swal.fire({
              icon: 'success',
              title: 'Berhasil',
              text: 'Data kehadiran berhasil diperbarui!',
              timer: 1500,
              showConfirmButton: false
            });
          } else {
            Swal.fire('Gagal', res.message || 'Gagal update data', 'error');
          }
        },
        error: function(xhr) {
          btn.prop('disabled', false).text('Simpan');
          Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
        }
      });
    });
  });

  function updateFooter(totals) {
    if (!totals) return;
    $('#sumMengajar, #ft-mengajar').text('Rp ' + parseInt(totals.total_mengajar || 0).toLocaleString('id-ID'));
    $('#sumDty, #ft-dty').text('Rp ' + parseInt(totals.total_dty || 0).toLocaleString('id-ID'));
    $('#sumJafung, #ft-jafung').text('Rp ' + parseInt(totals.total_jafung || 0).toLocaleString('id-ID'));
    $('#sumKehadiran, #ft-kehadiran').text('Rp ' + parseInt(totals.total_kehadiran || 0).toLocaleString('id-ID'));
    $('#sumHadir15, #ft-hadir15').text('Rp ' + parseInt(totals.total_hadir_15 || 0).toLocaleString('id-ID'));
    $('#sumHadir10, #ft-hadir10').text('Rp ' + parseInt(totals.total_hadir_10 || 0).toLocaleString('id-ID'));
    $('#sumPiket, #ft-piket').text('Rp ' + parseInt(totals.total_piket || 0).toLocaleString('id-ID'));
    $('#sumTunkel, #ft-tunkel').text('Rp ' + parseInt(totals.total_tunkel || 0).toLocaleString('id-ID'));
    $('#sumTunjanak, #ft-tunjanak').text('Rp ' + parseInt(totals.total_tunjanak || 0).toLocaleString('id-ID'));
    $('#sumKehormatan, #ft-kehormatan').text('Rp ' + parseInt(totals.total_kehormatan || 0).toLocaleString('id-ID'));
    $('#sumWalkes, #ft-walkes').text('Rp ' + parseInt(totals.total_walkes || 0).toLocaleString('id-ID'));
    $('#sumTambahan, #ft-tambahan').text('Rp ' + parseInt(totals.total_tambahan || 0).toLocaleString('id-ID'));
    $('#sumJumlah, #ft-jumlah').text('Rp ' + parseInt(totals.total_jumlah || 0).toLocaleString('id-ID'));
    $('#sumPotongan, #ft-potongan').text('Rp ' + parseInt(totals.total_potongan || 0).toLocaleString('id-ID'));
    $('#sumGrand, #ft-grand').text('Rp ' + parseInt(totals.grand_total || 0).toLocaleString('id-ID'));
  }

  // Kirim Validasi
  function kirimValidasi() {
    Swal.fire({
      title: 'Kirim Validasi Pengajar?',
      text: 'Pastikan data kehadiran sudah sesuai sebelum dikirim ke evaluasi.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Ya, Kirim Sekarang',
      cancelButtonText: 'Batal'
    }).then((res) => {
      if (res.value || res.isConfirmed) {
        Swal.fire({
          title: 'Mengirim...',
          text: 'Mohon tunggu sebentar...',
          allowOutsideClick: false,
          showConfirmButton: false,
          onOpen: () => { Swal.showLoading(); },
          didOpen: () => { Swal.showLoading(); }
        });
        $.ajax({
          url: '{{ url("/kehadiran_pengajar/kirim") }}',
          type: 'POST',
          dataType: 'json',
          data: {
            _token: '{{ csrf_token() }}',
            id_kehadiran_lembaga: '{{ $periode->id_kehadiran_lembaga }}'
          },
          success: function(resp) {
            if (resp.status) {
              Swal.fire('Berhasil', resp.message, 'success').then(() => {
                window.location.href = '{{ url("/kehadiran_pengajar") }}';
              });
            } else {
              Swal.fire('Gagal', resp.message, 'error');
            }
          },
          error: function() {
            Swal.fire('Error', 'Terjadi kesalahan sistem saat mengirim validasi.', 'error');
          }
        });
      }
    });
  }

  // MODAL CATATAN KHUSUS PENGAJAR
  $(document).on('click', '.btn-catatan', function() {
    let id = $(this).data('id');
    let nama = $(this).data('nama');
    let currentVal = $('#catatan_val_' + id).val();
    
    $('#cat-id').val(id);
    $('#cat-nama').text(nama);
    $('#cat-val').val(currentVal);
    $('#modalCatatan').modal('show');
  });

  $('#btnSimpanCatatan').on('click', function() {
    let id = $('#cat-id').val();
    let val = $('#cat-val').val();
    
    $('#catatan_val_' + id).val(val);
    
    if (id_leb_storage) {
      localStorage.setItem('catatan_pengajar_' + id_leb_storage + '_' + id, val);
    }

    let btn = $('.btn-catatan[data-id="'+id+'"]');
    let icon = $('.icon-catatan-'+id);
    let badge = $('.badge-catatan-'+id);
    
    if (val.trim() !== '') {
      btn.removeClass('btn-outline-info').addClass('btn-info shadow-sm');
      icon.removeClass('fa-comment-o').addClass('fa-commenting');
      badge.removeClass('d-none');
    } else {
      btn.removeClass('btn-info shadow-sm').addClass('btn-outline-info');
      icon.removeClass('fa-commenting').addClass('fa-comment-o');
      badge.addClass('d-none');
    }
    
    $.ajax({
      url: '{{ url("/kehadiran_pengajar/saveCatatan") }}',
      type: 'POST',
      dataType: 'json',
      data: {
        _token: '{{ csrf_token() }}',
        id_kehadiran_lembaga: id_leb_storage,
        id_pengajar: id,
        catatan: val
      }
    });

    $('#modalCatatan').modal('hide');
  });

  // Setujui atau Revisi
  function validasiAksi(type) {
    const isSetuju = (type === 'setuju');
    const title = isSetuju ? 'Setujui Pengajuan (ACC)?' : 'Kembalikan untuk Revisi?';
    const text = isSetuju 
      ? 'Data akan dikunci ke rekap total barokah pengajar (snapshot).' 
      : 'Pengajuan akan dikembalikan ke status Revisi agar dapat diperbaiki operator.';

    Swal.fire({
      title: title,
      text: text,
      icon: isSetuju ? 'question' : 'warning',
      showCancelButton: true,
      confirmButtonText: isSetuju ? 'Ya, Setujui (ACC)' : 'Ya, Kembalikan',
      confirmButtonColor: isSetuju ? '#28a745' : '#ffc107',
      cancelButtonText: 'Batal'
    }).then((res) => {
      if (res.value || res.isConfirmed) {
        Swal.fire({
          title: 'Memproses...',
          allowOutsideClick: false,
          showConfirmButton: false,
          onOpen: () => { Swal.showLoading(); },
          didOpen: () => { Swal.showLoading(); }
        });
        let postData = $('#form').serialize() + '&action_type=' + type + '&catatan_umum_pimpinan=' + encodeURIComponent($('#catatan_umum_pimpinan').val());
        $.ajax({
          url: '{{ url("/kehadiran_pengajar/validasi") }}',
          type: 'POST',
          dataType: 'json',
          data: postData,
          success: function(resp) {
            if (resp.status) {
              if (id_leb_storage) {
                $('.btn-catatan').each(function() {
                  localStorage.removeItem('catatan_pengajar_' + id_leb_storage + '_' + $(this).data('id'));
                });
              }
              Swal.fire('Berhasil', resp.message, 'success').then(() => {
                location.reload();
              });
            } else {
              Swal.fire('Gagal', resp.message, 'error');
            }
          },
          error: function() {
            Swal.fire('Error', 'Terjadi kesalahan sistem saat validasi.', 'error');
          }
        });
      }
    });
  }

  // Sinkronisasi Pengajar Baru
  function syncTeachers() {
    Swal.fire({
      title: 'Update Daftar Pengajar?',
      text: 'Sistem akan memeriksa jika terdapat penugasan Guru/Dosen baru di lembaga ini yang belum masuk periode.',
      icon: 'info',
      showCancelButton: true,
      confirmButtonText: 'Ya, Sinkronkan',
      cancelButtonText: 'Batal'
    }).then((res) => {
      if (res.value || res.isConfirmed) {
        Swal.fire({
          title: 'Memproses...',
          allowOutsideClick: false,
          showConfirmButton: false,
          onOpen: () => { Swal.showLoading(); },
          didOpen: () => { Swal.showLoading(); }
        });
        $.ajax({
          url: '{{ url("/kehadiran_pengajar/syncPengajar") }}',
          type: 'POST',
          dataType: 'json',
          data: {
            _token: '{{ csrf_token() }}',
            id_kehadiran_lembaga: '{{ $periode->id_kehadiran_lembaga }}'
          },
          success: function(resp) {
            if (resp.status) {
              Swal.fire('Berhasil', resp.message, 'success').then(() => {
                location.reload();
              });
            } else {
              Swal.fire('Gagal', resp.message, 'error');
            }
          },
          error: function() {
            Swal.fire('Error', 'Terjadi kesalahan sistem saat sinkronisasi pengajar.', 'error');
          }
        });
      }
    });
  }

  // Logic Modal Potongan Pengajar
  let potonganOptions = [];
  let potIndex = 0;

  $.getJSON('{{ url("/kehadiran_struktural/getPotonganOptions") }}', function(data) {
    potonganOptions = data;
  });

  $(document).on('click', '.link-potongan', function() {
    const id = $(this).data('id');
    const nama = $(this).data('nama');
    const periodeDate = '{{ $periode_date }}';

    $('#potonganPegawaiName').text(nama);
    $('#potongan_id_pengajar').val(id);
    $('#potonganBody').html('<tr><td colspan="4" class="text-center text-muted">Memuat data potongan...</td></tr>');
    $('#modalPotongan').modal('show');
    potIndex = 0;

    $.getJSON('{{ url("/kehadiran_pengajar/detailPotongan") }}/' + id + '/' + periodeDate, function(data) {
      let rows = '';
      if (!data || data.length === 0) {
        rows = '<tr><td colspan="4" class="text-center text-muted">Belum ada potongan untuk pengajar ini.</td></tr>';
      } else {
        data.forEach(item => {
          let opts = '<option value="">Pilih Potongan...</option>';
          potonganOptions.forEach(opt => {
            let sel = (opt.id_potongan == item.jenis_potongan) ? 'selected' : '';
            opts += `<option value="${opt.id_potongan}" ${sel}>${opt.nama_potongan}</option>`;
          });

          rows += `
            <tr>
              <td>
                <input type="hidden" name="potongan[${potIndex}][id_potongan_pengajar]" value="${item.id_potongan_pengajar}">
                <select class="form-control form-control-sm" name="potongan[${potIndex}][jenis_potongan]" required>
                  ${opts}
                </select>
              </td>
              <td>
                <input type="text" class="form-control form-control-sm text-right" name="potongan[${potIndex}][nominal_potongan]" value="${parseInt(item.nominal_potongan || 0).toLocaleString('id-ID')}" required>
              </td>
              <td>
                <input type="date" class="form-control form-control-sm" name="potongan[${potIndex}][min_periode_potongan]" value="${item.min_periode_potongan || ''}">
              </td>
              <td>
                <input type="date" class="form-control form-control-sm" name="potongan[${potIndex}][max_periode_potongan]" value="${item.max_periode_potongan || ''}">
              </td>
            </tr>
          `;
          potIndex++;
        });
      }
      $('#potonganBody').html(rows);
    }).fail(function() {
      $('#potonganBody').html('<tr><td colspan="4" class="text-center text-danger">Gagal memuat data potongan.</td></tr>');
    });
  });

  $('#btnTambahPotongan').click(function() {
    let opts = '<option value="">Pilih Potongan...</option>';
    potonganOptions.forEach(opt => {
      opts += `<option value="${opt.id_potongan}">${opt.nama_potongan}</option>`;
    });

    let newRow = `
      <tr>
        <td>
          <input type="hidden" name="potongan[${potIndex}][id_potongan_pengajar]" value="">
          <select class="form-control form-control-sm" name="potongan[${potIndex}][jenis_potongan]" required>
            ${opts}
          </select>
        </td>
        <td>
          <input type="text" class="form-control form-control-sm text-right" name="potongan[${potIndex}][nominal_potongan]" placeholder="0" required>
        </td>
        <td>
          <input type="date" class="form-control form-control-sm" name="potongan[${potIndex}][min_periode_potongan]">
        </td>
        <td>
          <input type="date" class="form-control form-control-sm" name="potongan[${potIndex}][max_periode_potongan]">
        </td>
      </tr>
    `;

    if ($('#potonganBody tr td').hasClass('text-muted')) {
      $('#potonganBody').html('');
    }
    $('#potonganBody').append(newRow);
    potIndex++;
  });

  $('#formPotongan').submit(function(e) {
    e.preventDefault();
    $('#btnSimpanPotongan').prop('disabled', true).text('Menyimpan...');

    $.ajax({
      url: '{{ url("/kehadiran_pengajar/savePotongan") }}',
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function(res) {
        if (res.status) {
          Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false })
            .then(() => location.reload());
        } else {
          Swal.fire('Gagal', res.message || 'Gagal menyimpan potongan', 'error');
          $('#btnSimpanPotongan').prop('disabled', false).text('Simpan Potongan');
        }
      },
      error: function() {
        Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
        $('#btnSimpanPotongan').prop('disabled', false).text('Simpan Potongan');
      }
    });
  });
</script>
</body>
</html>
