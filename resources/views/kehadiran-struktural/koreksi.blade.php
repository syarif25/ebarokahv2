<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Validasi Struktural - Koreksi Kehadiran</title>

  <link rel="shortcut icon" type="image/png" href="{{ asset('images/p2s2_fav.png') }}?v={{ filemtime(public_path('images/p2s2_fav.png')) }}" />
  <link rel="icon" type="image/png" href="{{ asset('images/p2s2_fav.png') }}?v={{ filemtime(public_path('images/p2s2_fav.png')) }}" />

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.bootstrap4.min.css">
  <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}">

  <style>
    body { background:#f8f9fa; font-size:14px; color: #212529; }
    .table thead th { vertical-align:middle!important; text-align:center; }
    .cell-right{ text-align:right }
    .cell-center{ text-align:center }
    .badge-summary{ font-size:.9rem; margin-right:.5rem; padding: 0.4em 0.6em; }
    .sticky-actions{ position:sticky; bottom:0; background:#fff; z-index:100; border-top:1px solid #e9ecef; padding:.75rem }
    .swal2-container { z-index: 99999 !important; }

    /* Agar tabel bisa mengecil/menyempit mengikuti kolom yang aktif */
    #tabel_validasi {
      width: auto !important;
      min-width: 0 !important;
      max-width: 100% !important;
    }
    #tabel_validasi th, #tabel_validasi td {
      white-space: nowrap;
    }
    #tabel_validasi td:nth-child(2) {
      white-space: normal;
      min-width: 180px;
    }
    .dtfh-floatingparenthead, .dtfh-floatingparent {
      overflow: hidden !important;
      z-index: 1020 !important;
    }
  </style>
</head>
<body>

<div class="container-fluid my-3">
  <!-- HEADER -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center">
      <h4 class="mb-0 font-weight-bold mr-3">
        Kehadiran Bulan {{ $periode->bulan ?? '' }} {{ $periode->tahun ?? '' }}
      </h4>
      @php
        $st = $periode->status ?? 'Belum';
        $badgeClass = 'badge-secondary';
        if ($st === 'acc') $badgeClass = 'badge-success';
        elseif ($st === 'Terkirim') $badgeClass = 'badge-warning text-dark';
        elseif ($st === 'Sudah') $badgeClass = 'badge-secondary';
        elseif ($st === 'Revisi') $badgeClass = 'badge-danger';
      @endphp
      <span class="badge {{ $badgeClass }}" style="font-size: 0.85rem; padding: 0.4em 0.7em;">
        Status: {{ ucfirst($st === 'Sudah' ? 'Belum dikirim' : $st) }}
      </span>
      <span class="badge badge-light border text-dark font-weight-bold ml-2" style="font-size: 0.85rem; padding: 0.4em 0.7em;">
        {{ $periode->nama_lembaga ?? ($periode->lembaga->nama_lembaga ?? '-') }}
      </span>
    </div>

    <div class="d-flex align-items-center">
      @if(!empty($periode->file))
        <a class="btn btn-info text-white mr-2" target="_blank" href="{{ asset('upload/' . $periode->file) }}">
          <i class="fa fa-file mr-1"></i> Lihat File Absensi
        </a>
      @endif

      @if(!empty($periode->id_kehadiran_lembaga))
        <a class="btn btn-warning text-dark mr-2" target="_blank" href="{{ url('/kehadiran_struktural/cetak/' . $periode->id_kehadiran_lembaga) }}">
          <i class="fa fa-print mr-1"></i> Cetak
        </a>
      @endif

      @if(!empty($periode->id_lembaga))
        <a class="btn btn-danger text-white mr-2" target="_blank" href="{{ url('/kehadiran_struktural/cetak_potongan/' . $periode->id_lembaga . '/' . ($periode->id_kehadiran_lembaga ?? '')) }}">
          <i class="fa fa-scissors mr-1"></i> Potongan
        </a>
      @endif

      <!-- Search & Tampilan Kolom -->
      <div class="d-inline-flex align-items-center">
        <div class="input-group shadow-sm mr-2" style="width: 250px;">
          <div class="input-group-prepend">
            <span class="input-group-text bg-white border-right-0" style="font-size: 0.85rem;"><i class="fa fa-search text-muted"></i></span>
          </div>
          <input type="text" id="customSearchStruk" class="form-control border-left-0 pl-1" placeholder="Cari nama..." style="font-size: 0.85rem; height: 38px;">
        </div>

        <div class="dropdown">
          <button class="btn btn-primary dropdown-toggle font-weight-bold" type="button" id="btnTampilanKolomStruk" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-columns mr-1"></i> Kolom
          </button>
          <div class="dropdown-menu dropdown-menu-right shadow-sm" aria-labelledby="btnTampilanKolomStruk" style="min-width: 220px;">
            <h6 class="dropdown-header text-uppercase font-weight-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Tampilkan / Sembunyikan</h6>
            <div class="dropdown-item px-3 py-2">
              <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input toggle-vis-struk" id="colMasterStruk" data-columns="2,3,4,5,6" checked>
                <label class="custom-control-label" style="cursor:pointer;" for="colMasterStruk">Grup Data Master</label>
              </div>
            </div>
            <div class="dropdown-item px-3 py-2">
              <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input toggle-vis-struk" id="colKehadiranStruk" data-columns="7,8,9,10,11,12,13" checked>
                <label class="custom-control-label" style="cursor:pointer;" for="colKehadiranStruk">Grup Kehadiran</label>
              </div>
            </div>
            <div class="dropdown-item px-3 py-2">
              <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input toggle-vis-struk" id="colTunjanganStruk" data-columns="14,15,16,17" checked>
                <label class="custom-control-label" style="cursor:pointer;" for="colTunjanganStruk">Grup Tunjangan</label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- BADGE RINGKASAN -->
  <div class="mb-3">
    <span class="badge badge-secondary badge-summary">Total Tunjab: <span id="sumTunjab">{{ number_format($totals['total_tunjab'] ?? 0, 0, ',', '.') }}</span></span>
    <span class="badge badge-secondary badge-summary">Total TMP: <span id="sumTMP">{{ number_format($totals['total_tmp'] ?? 0, 0, ',', '.') }}</span></span>
    <span class="badge badge-secondary badge-summary">Total Kehadiran: <span id="sumKehadiran">{{ number_format($totals['total_kehadiran'] ?? 0, 0, ',', '.') }}</span></span>
    <span class="badge badge-secondary badge-summary">Total Tunkel: <span id="sumTunkel">{{ number_format($totals['total_tunkel'] ?? 0, 0, ',', '.') }}</span></span>
    <span class="badge badge-secondary badge-summary">Total Tunj Anak: <span id="sumTunjAnak">{{ number_format($totals['total_tunjanak'] ?? 0, 0, ',', '.') }}</span></span>
    <span class="badge badge-secondary badge-summary">Total Kehormatan: <span id="sumKehormatan">{{ number_format($totals['total_kehormatan'] ?? 0, 0, ',', '.') }}</span></span>
    <span class="badge badge-secondary badge-summary">Total TBK: <span id="sumTBK">{{ number_format($totals['total_tbk'] ?? 0, 0, ',', '.') }}</span></span>
    <span class="badge badge-primary badge-summary">Grand Total: <span id="sumGrand">{{ number_format($totals['grand_total'] ?? 0, 0, ',', '.') }}</span></span>
  </div>

  <!-- INFO PERIODE -->
  @php
    $bulan_nama = $periode->bulan ?? '';
    $map_b = ['januari'=>1, 'februari'=>2, 'maret'=>3, 'april'=>4, 'mei'=>5, 'juni'=>6, 'juli'=>7, 'agustus'=>8, 'september'=>9, 'oktober'=>10, 'november'=>11, 'desember'=>12];
    $bulan_int = $map_b[strtolower(trim((string)$bulan_nama))] ?? (int)date('m');
    $thn_int = (int)substr($periode->tahun ?? date('Y'), 0, 4);
    $total_hari = (int)date('t', mktime(0, 0, 0, $bulan_int, 1, $thn_int));

    $jumlah_jumat = 0;
    for($i=1; $i<=$total_hari; $i++){
      if(date('N', mktime(0,0,0, $bulan_int, $i, $thn_int)) == 5) $jumlah_jumat++;
    }

    $libur_pesantren = isset($periode->libur_pesantren) ? (int)$periode->libur_pesantren : 0;
    $hari_kerja = $total_hari - $jumlah_jumat - $libur_pesantren;
    if ($hari_kerja < 0) $hari_kerja = 0;
  @endphp

  <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center">
    <i class="fa fa-info-circle mr-2" style="font-size: 1.2rem;"></i>
    <div>
      <strong><i class="fa fa-calendar mr-1"></i> Info Periode: {{ $bulan_nama }} {{ $thn_int }}</strong>
      <span class="ml-3">
        <i class="fa fa-calendar-o mr-1"></i> Total Hari: <strong>{{ $total_hari }}</strong>
      </span>
      <span class="ml-2">|</span>
      <span class="ml-2">
        <i class="fa fa-ban mr-1 text-danger"></i> Jumat (Libur): <strong>{{ $jumlah_jumat }}</strong>
      </span>
      @if($libur_pesantren > 0)
      <span class="ml-2">|</span>
      <span class="ml-2">
        <i class="fa fa-calendar-minus-o mr-1 text-warning"></i> Libur Pesantren: <strong>{{ $libur_pesantren }}</strong>
      </span>
      @endif
      <span class="ml-2">|</span>
      <span class="ml-2">
        <i class="fa fa-check-circle mr-1 text-success"></i> Hari Kerja: <strong>{{ $hari_kerja }}</strong> (Sabtu-Kamis)
      </span>
    </div>
  </div>

  <!-- TABEL -->
  <form id="form">
    @csrf
    <input type="hidden" name="id_kehadiran_lembaga" value="{{ $periode->id_kehadiran_lembaga }}">

    <div class="table-responsive">
      <table class="table table-bordered table-striped" id="tabel_validasi">
        <thead class="thead-light">
          <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Nama Lengkap</th>
            <th rowspan="2">Eselon</th>
            <th rowspan="2">TMT</th>
            <th rowspan="2">Tunjab</th>
            <th rowspan="2">MP</th>
            <th rowspan="2">TMP</th>
            <th colspan="7">Kehadiran</th>
            <th rowspan="2">Tunkel</th>
            <th rowspan="2">Tunj Anak</th>
            <th rowspan="2">Kehormatan</th>
            <th rowspan="2">TBK</th>
            <th rowspan="2">Jumlah</th>
            <th rowspan="2">Potongan</th>
            <th rowspan="2">Diterima</th>
            <th rowspan="2" style="width:70px">Aksi</th>
          </tr>
          <tr>
            <th>Wajib</th>
            <th>Hadir</th>
            <th>TGS</th>
            <th>Izin</th>
            <th>Sakit</th>
            <th>%</th>
            <th>Nominal</th>
          </tr>
        </thead>
        <tbody>
          @if(!empty($isilist))
            @foreach($isilist as $idx => $row)
              @php
                $changed = $row->komponen_berubah ?? [];
                $nama_full = strtoupper(implode(' ', array_filter([
                    trim($row->gelar_depan ?? ''),
                    trim($row->nama_lengkap ?? ''),
                    trim($row->gelar_belakang ?? '')
                ])));
                $is_punish = !empty($row->is_punishment_tunjab);
                $tunjab_asli = (int)($row->tunjab_asli ?? $row->tunjab);
                $tunjab_class = $is_punish
                    ? 'text-danger font-weight-bold'
                    : (in_array('tunjab', $changed) ? 'text-warning font-weight-bold' : '');
                $tunjab_title = $is_punish
                    ? 'title="⚠️ Punishment Kehadiran < 50%. Tunjab Asli: ' . number_format($tunjab_asli, 0, ',', '.') . '"'
                    : (in_array('tunjab', $changed) ? 'title="Nominal berubah dari bulan sebelumnya"' : '');
                $isSerti = !empty($row->status_sertifikasi) && in_array(strtolower(trim($row->status_sertifikasi)), ['sudah', 'ya', 'sudah sertifikasi']);
                $katPengajar = $row->kategori_pengajar ?? '';
                if (in_array($katPengajar, ['GTY', 'GTT'])) {
                    $labelSerti = 'Sergur';
                } elseif (in_array($katPengajar, ['DTY', 'DTT'])) {
                    $labelSerti = 'Serdos';
                } elseif (!empty($row->tmt_dosen) && $row->tmt_dosen != '0000-00-00' && $row->tmt_dosen != '0001-01-01') {
                    $labelSerti = 'Serdos';
                } elseif (!empty($row->tmt_guru) && $row->tmt_guru != '0000-00-00' && $row->tmt_guru != '0001-01-01') {
                    $labelSerti = 'Sergur';
                } else {
                    $labelSerti = 'Serdos';
                }
              @endphp
              <tr id="row-{{ $row->id_kehadiran }}" class="{{ !empty($row->is_warning) ? 'bg-warning' : '' }}">
                <td class="cell-center {{ !empty($row->is_warning) ? 'text-dark font-weight-bold' : '' }}">{{ $idx + 1 }}</td>
                <td class="{{ !empty($row->is_warning) ? 'text-dark' : '' }}">
                  <div class="font-weight-bold" style="font-size: 0.9rem; line-height: 1.2;">
                    <a href="javascript:void(0)" class="text-dark link-cek-umana text-decoration-none" data-nik="{{ $row->nik }}" data-nama="{{ htmlspecialchars($nama_full, ENT_QUOTES) }}" title="Klik untuk cek validasi tunjangan ganda (Struktural & Pengajar)">
                      {{ $nama_full }}
                      <i class="fa fa-info-circle text-primary ml-1" style="font-size: 0.8rem;"></i>
                    </a>
                  </div>
                  @if(!empty($row->is_warning))
                    <span class="badge badge-danger shadow-sm mt-1 px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.3px;">
                      <i class="fa fa-exclamation-triangle mr-1"></i> 
                      PERUBAHAN KOMPONEN ({{ ($row->selisih_komponen > 0 ? '+' : '').number_format($row->selisih_komponen,0,',','.') }})
                    </span>
                  @endif
                </td>
                <td>{{ $row->nama_jabatan ?? '-' }}</td>
                <td class="cell-center">
                  {{ !empty($row->tmt_struktural) ? date('Y', strtotime($row->tmt_struktural)) : '-' }}
                </td>

                <td class="cell-right {{ $tunjab_class }}">
                  @if($is_punish)
                    <a href="javascript:void(0)" class="text-danger link-punishment text-decoration-none"
                       data-nama="{{ htmlspecialchars($nama_full, ENT_QUOTES) }}"
                       data-jabatan="{{ htmlspecialchars($row->nama_jabatan ?? '-', ENT_QUOTES) }}"
                       data-wajib="{{ (int)($row->wajib_hadir_bulanan ?? 0) }}"
                       data-hadir="{{ (int)$row->jumlah_hadir }}"
                       data-tugas="{{ (int)($row->jumlah_tugas ?? 0) }}"
                       data-izin="{{ (int)($row->jumlah_izin ?? 0) }}"
                       data-sakit="{{ (int)($row->jumlah_sakit ?? 0) }}"
                       data-persen="{{ $row->persentase_kehadiran ?? 0 }}"
                       data-tunjab-asli="{{ $tunjab_asli }}"
                       data-tunjab-diterima="{{ (int)$row->tunjab }}"
                       title="Klik untuk melihat rincian pemotongan punishment">
                      <i class="fa fa-exclamation-triangle text-danger mr-1"></i>
                      {{ number_format((int)$row->tunjab, 0, ',', '.') }}
                    </a>
                  @else
                    <span {!! $tunjab_title !!}>{{ number_format((int)$row->tunjab, 0, ',', '.') }}</span>
                  @endif
                </td>
                <td class="cell-center">{{ (int)$row->mp }}</td>
                <td class="cell-right {{ in_array('tmp', $changed) ? 'text-danger font-weight-bold' : '' }}" {{ in_array('tmp', $changed) ? 'title="Nominal berubah dari bulan sebelumnya"' : '' }}>
                  {{ number_format((int)$row->tmp, 0, ',', '.') }}
                </td>

                <td class="cell-center text-success font-weight-bold">{{ (int)($row->wajib_hadir_bulanan ?? 0) }}</td>
                <td class="cell-center" id="hari-{{ $row->id_kehadiran }}">{{ (int)$row->jumlah_hadir }}</td>
                <td class="cell-center" id="tugas-{{ $row->id_kehadiran }}">{{ (int)($row->jumlah_tugas ?? 0) }}</td>
                <td class="cell-center" id="izin-{{ $row->id_kehadiran }}">{{ (int)($row->jumlah_izin ?? 0) }}</td>
                <td class="cell-center" id="sakit-{{ $row->id_kehadiran }}">{{ (int)($row->jumlah_sakit ?? 0) }}</td>
                <td class="cell-center @php
                  $persen = $row->persentase_kehadiran ?? 0;
                  if ($persen >= 80) echo 'text-success font-weight-bold';
                  elseif ($persen >= 60) echo 'text-warning font-weight-bold';
                  else echo 'text-danger font-weight-bold';
                @endphp">
                  {{ number_format((float)$persen, 2, '.', '') }}%
                </td>
                <td class="cell-right" id="jmlhadir-{{ $row->id_kehadiran }}">{{ number_format((int)$row->nominal_kehadiran, 0, ',', '.') }}</td>

                <td class="cell-right {{ in_array('tunkel', $changed) ? 'text-danger font-weight-bold' : '' }}">{{ number_format((int)$row->tunkel, 0, ',', '.') }}</td>
                <td class="align-middle {{ in_array('tunj_anak', $changed) ? 'text-danger font-weight-bold' : '' }} {{ $isSerti ? 'cell-center' : 'cell-right' }}">
                  @if($isSerti)
                    <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 0.72rem; letter-spacing: 0.3px;">{{ $labelSerti }}</span>
                  @else
                    {{ number_format((int)$row->tunj_anak, 0, ',', '.') }}
                  @endif
                </td>
                <td class="align-middle {{ in_array('kehormatan', $changed) ? 'text-danger font-weight-bold' : '' }} {{ $isSerti ? 'cell-center' : 'cell-right' }}">
                  @if($isSerti)
                    <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 0.72rem; letter-spacing: 0.3px;">{{ $labelSerti }}</span>
                  @else
                    {{ number_format((int)$row->nilai_kehormatan, 0, ',', '.') }}
                  @endif
                </td>
                <td class="cell-right">
                  <a href="javascript:void(0)" class="text-primary link-tbk" data-id="{{ $row->id_penempatan }}">
                    {{ number_format((int)$row->tbk, 0, ',', '.') }}
                  </a>
                </td>

                <td class="cell-right font-weight-bold" id="jumlahbarokah-{{ $row->id_kehadiran }}">{{ number_format((int)$row->jumlah_barokah, 0, ',', '.') }}</td>
                <td class="cell-right">
                  <a href="javascript:void(0)" class="text-danger link-potongan" data-id="{{ $row->id_penempatan }}" data-nama="{{ htmlspecialchars($nama_full, ENT_QUOTES) }}">
                    {{ number_format((int)$row->potongan, 0, ',', '.') }}
                  </a>
                </td>
                <td class="cell-right font-weight-bold" id="diterima-{{ $row->id_kehadiran }}">{{ number_format((int)$row->diterima, 0, ',', '.') }}</td>

                <td class="cell-center">
                  <div class="d-flex justify-content-center">
                    @php
                      $user = Auth::user();
                      $jabatanUser = $user ? $user->jabatan : 'SuperAdmin';
                      $can_edit = false;
                      if (in_array($jabatanUser, ['SuperAdmin', 'Evaluasi'])) {
                        if ($periode->status !== 'acc' && $periode->status !== 'selesai') $can_edit = true;
                      } else {
                        if ($periode->status === 'Sudah' || $periode->status === 'Revisi') $can_edit = true;
                      }
                    @endphp

                    @if($can_edit)
                      <button type="button" class="btn btn-sm btn-outline-primary btn-edit mr-1"
                              data-toggle="tooltip"
                              title="Koreksi jumlah kehadiran"
                              data-id="{{ $row->id_kehadiran }}"
                              data-lembaga="{{ $periode->id_kehadiran_lembaga }}"
                              data-hari="{{ (int)$row->jumlah_hadir }}"
                              data-tugas="{{ (int)($row->jumlah_tugas ?? 0) }}"
                              data-izin="{{ (int)($row->jumlah_izin ?? 0) }}"
                              data-sakit="{{ (int)($row->jumlah_sakit ?? 0) }}">
                        Edit
                      </button>

                      <input type="hidden" name="catatan_khusus[{{ $row->id_penempatan }}]" id="catatan_val_{{ $row->id_penempatan }}" value="{{ $row->catatan_khusus_umana ?? '' }}">

                      @php
                        $has_note = !empty(trim($row->catatan_khusus_umana ?? ''));
                        $btn_class = $has_note ? 'btn-info shadow-sm' : 'btn-outline-info';
                        $icon_class = $has_note ? 'fa-commenting' : 'fa-comment-o';
                      @endphp

                      <button type="button" class="btn btn-sm {{ $btn_class }} btn-catatan position-relative"
                              title="Catatan Evaluasi"
                              data-id="{{ $row->id_penempatan }}"
                              data-nama="{{ htmlspecialchars($nama_full, ENT_QUOTES) }}">
                        <i class="fa {{ $icon_class }} icon-catatan-{{ $row->id_penempatan }}"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $has_note ? '' : 'd-none' }} badge-catatan-{{ $row->id_penempatan }}" style="font-size: 0.5rem; transform: translate(-30%, -30%);">★</span>
                      </button>
                    @else
                      <button type="button" class="btn btn-sm btn-outline-secondary mr-1" disabled title="Data sudah disetujui">Edit</button>
                      @if(!empty($row->catatan_khusus_umana))
                        <button type="button" class="btn btn-sm btn-info" onclick="Swal.fire('Catatan', '{{ htmlspecialchars($row->catatan_khusus_umana, ENT_QUOTES) }}', 'info')"><i class="fa fa-comment"></i></button>
                      @endif
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
          @endif
        </tbody>

        <tfoot>
          <tr>
            <th></th> <!-- No -->
            <th></th> <!-- Nama Lengkap -->
            <th></th> <!-- Eselon -->
            <th></th> <!-- TMT -->
            <th class="cell-right" id="ft-tunjab">{{ number_format($totals['total_tunjab'] ?? 0, 0, ',', '.') }}</th>
            <th></th> <!-- MP -->
            <th class="cell-right" id="ft-tmp">{{ number_format($totals['total_tmp'] ?? 0, 0, ',', '.') }}</th>
            <th></th> <!-- Wajib -->
            <th></th> <!-- Hadir -->
            <th></th> <!-- TGS -->
            <th></th> <!-- Izin -->
            <th></th> <!-- Sakit -->
            <th></th> <!-- % -->
            <th class="cell-right" id="ft-kehadiran">{{ number_format($totals['total_kehadiran'] ?? 0, 0, ',', '.') }}</th>
            <th class="cell-right" id="ft-tunkel">{{ number_format($totals['total_tunkel'] ?? 0, 0, ',', '.') }}</th>
            <th class="cell-right" id="ft-tunjanak">{{ number_format($totals['total_tunjanak'] ?? 0, 0, ',', '.') }}</th>
            <th class="cell-right" id="ft-kehormatan">{{ number_format($totals['total_kehormatan'] ?? 0, 0, ',', '.') }}</th>
            <th class="cell-right" id="ft-tbk">{{ number_format($totals['total_tbk'] ?? 0, 0, ',', '.') }}</th>
            <th class="cell-right" id="ft-barokah">{{ number_format($totals['total_barokah'] ?? 0, 0, ',', '.') }}</th>
            <th class="cell-right" id="ft-potongan">{{ number_format($totals['total_potongan'] ?? 0, 0, ',', '.') }}</th>
            <th class="cell-right font-weight-bold" id="ft-grand">{{ number_format($totals['grand_total'] ?? 0, 0, ',', '.') }}</th>
            <th></th> <!-- Aksi -->
          </tr>
        </tfoot>
      </table>
    </div>

    <!-- Catatan Pimpinan (Keseluruhan) -->
    @php
      $user = Auth::user();
      $jabatanUser = $user ? $user->jabatan : 'SuperAdmin';
      $isEvaluasiOrSuper = in_array($jabatanUser, ['SuperAdmin', 'Evaluasi']);
    @endphp

    @if($periode->status !== 'acc' && $periode->status !== 'selesai')
      <div class="card mt-3 border-left-primary shadow-sm border-0">
        <div class="card-body bg-light p-3">
          <label class="font-weight-bold text-primary"><i class="fa fa-commenting"></i> Catatan Evaluasi</label>
          <textarea name="catatan_umum_pimpinan" class="form-control" rows="2" placeholder="" {{ $isEvaluasiOrSuper ? '' : 'readonly' }}>{{ $periode->catatan_umum_pimpinan ?? '' }}</textarea>
        </div>
      </div>
    @else
      @if(!empty($periode->catatan_umum_pimpinan))
        <div class="alert alert-warning mt-3">
          <h6 class="font-weight-bold"><i class="fa fa-commenting"></i> Catatan :</h6>
          <p class="mb-0">{!! nl2br(e($periode->catatan_umum_pimpinan)) !!}</p>
        </div>
      @endif
    @endif
  </form>

    <!-- Info untuk operator -->
    <div class="alert alert-info py-2 px-3 small d-flex align-items-center mb-2 mt-3">
      <i class="fa fa-info-circle mr-2"></i>
      Jika ada koreksi pada <strong>Jumlah Kehadiran</strong>, silakan klik tombol &nbsp;<strong> Edit </strong> &nbsp;di kolom paling kanan.
    </div>

    <!-- Sticky Actions -->
    <div class="sticky-actions d-flex justify-content-end">
      @if(($periode->status === 'Sudah' || $periode->status === 'Revisi') && $jabatanUser !== 'SDM')
        @php $btn_teks = ($periode->status === 'Revisi') ? 'Kirim Hasil Revisi' : 'Kirim'; @endphp
        <button type="button" id="btnKirim" class="btn btn-primary font-weight-bold shadow-sm">
          <i class="fa fa-send mr-1"></i> {{ $btn_teks }}
        </button>
      @elseif($jabatanUser === 'SDM')
        <!-- SDM: read-only -->
      @elseif($periode->status === 'Terkirim' && $jabatanUser !== 'AdminLembaga')
        <button type="button" id="btnKembalikan" class="btn btn-warning mr-2 text-dark font-weight-bold">
          <i class="fa fa-undo mr-1"></i> Kembalikan (Revisi)
        </button>
        <button type="button" id="btnSetujui" class="btn btn-success font-weight-bold shadow-sm">
          <i class="fa fa-check mr-1"></i> Setujui
        </button>
      @else
        <!-- kondisi lain -->
      @endif
    </div>

  </div>
</div>

<!-- Modal Detail TBK -->
<div class="modal fade" id="modalTBK" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <form id="formTBK">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Detail Tugas Beban Kerja</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_penempatan" id="tbk_id_penempatan" value="">
        <table class="table table-sm table-bordered">
          <thead class="thead-light">
            <tr>
              <th>Nama Tunjangan</th>
              <th class="text-right" style="width: 200px;">Nominal</th>
              <th class="text-center" style="width: 150px;">Periode Maksimum</th>
            </tr>
          </thead>
          <tbody id="tbkBody">
            <tr><td colspan="3" class="text-center text-muted">Memuat...</td></tr>
          </tbody>
        </table>
        <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="btnTambahTBK"><i class="fa fa-plus"></i> Tambah Baris</button>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Tutup</button>
        <button type="submit" class="btn btn-primary" id="btnSimpanTBK">Simpan TBK</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Detail Potongan -->
<div class="modal fade" id="modalPotongan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <form id="formPotongan">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title text-danger">Detail Potongan</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <h6 id="potonganPegawaiName" class="mb-3 text-primary font-weight-bold"></h6>
        <input type="hidden" name="id_penempatan" id="potongan_id_penempatan" value="">
        <table class="table table-sm table-bordered">
          <thead class="thead-light">
            <tr>
              <th>Nama Potongan</th>
              <th class="text-right" style="width: 150px;">Nominal</th>
              <th class="text-center" style="width: 130px;">Tgl Mulai</th>
              <th class="text-center" style="width: 130px;">Tgl Selesai</th>
            </tr>
          </thead>
          <tbody id="potonganBody">
            <tr><td colspan="4" class="text-center text-muted">Memuat...</td></tr>
          </tbody>
        </table>
        <button type="button" class="btn btn-sm btn-outline-danger mt-2" id="btnTambahPotongan"><i class="fa fa-plus"></i> Tambah Baris</button>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Tutup</button>
        <button type="submit" class="btn btn-danger" id="btnSimpanPotongan">Simpan Potongan</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Catatan Spesifik Umana -->
<div class="modal fade" id="modalCatatan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Catatan </h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <p><strong>Umana:</strong> <span id="cat-nama"></span></p>
        <input type="hidden" id="cat-id">
        <div class="form-group">
          <label class="font-weight-bold">Catatan</label>
          @php
            $can_fill_catatan = false;
            if (in_array($jabatanUser, ['SuperAdmin', 'Evaluasi'])) {
                $can_fill_catatan = true;
                $placeholder_teks = 'Tuliskan catatan disini...';
            } else {
                if ($periode->status === 'Sudah' || $periode->status === 'Revisi') {
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

<!-- Modal Koreksi Kehadiran -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Koreksi Kehadiran</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edit-id">
        <input type="hidden" id="edit-lembaga">

        <div class="form-group">
          <label>Jumlah Hadir (Hari)</label>
          <input type="number" min="0" step="1" id="edit-hari" class="form-control" placeholder="Masukkan jumlah hari">
          <small class="form-text text-muted">Saat ini: <span id="cur-hari">0</span></small>
        </div>

        <div class="form-group">
          <label>Jumlah Tugas (Hari)</label>
          <input type="number" min="0" step="1" id="edit-tugas" class="form-control" placeholder="Masukkan jumlah tugas">
          <small class="form-text text-muted">Saat ini: <span id="cur-tugas">0</span></small>
        </div>

        <div class="form-group">
          <label>Jumlah Izin (Hari)</label>
          <input type="number" min="0" step="1" id="edit-izin" class="form-control" placeholder="Masukkan jumlah izin">
          <small class="form-text text-muted">Saat ini: <span id="cur-izin">0</span></small>
        </div>

        <div class="form-group">
          <label>Jumlah Sakit (Hari)</label>
          <input type="number" min="0" step="1" id="edit-sakit" class="form-control" placeholder="Masukkan jumlah sakit">
          <small class="form-text text-muted">Saat ini: <span id="cur-sakit">0</span></small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
        <button type="button" id="btnSimpanEdit" class="btn btn-primary">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Keterangan Pengurangan Tunjab (Tunjangan Jabatan) -->
<div class="modal fade" id="modalPunishment" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Pengurangan Tunjab</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <p class="text-muted small mb-3">
          Tunjangan Jabatan (Tunjab) dikurangi secara proporsional karena kehadiran efektif kurang dari 50% target wajib hadir bulanan.
        </p>

        <table class="table table-sm table-borderless mb-3">
          <tbody>
            <tr>
              <th style="width: 150px;" class="pl-0">Nama Umana'</th>
              <td class="font-weight-bold text-dark">: <span id="punish-nama">-</span></td>
            </tr>
            <tr>
              <th class="pl-0">Jabatan</th>
              <td>: <span id="punish-jabatan">-</span></td>
            </tr>
            <tr>
              <th class="pl-0">Wajib Hadir</th>
              <td>: <span id="punish-wajib">0</span> Hari</td>
            </tr>
            <tr>
              <th class="pl-0">Kehadiran Efektif</th>
              <td>: <span id="punish-efektif">0</span> Hari <small class="text-muted">(Hadir: <span id="punish-hadir">0</span>, Tugas: <span id="punish-tugas">0</span>, Izin: <span id="punish-izin">0</span>, Sakit: <span id="punish-sakit">0</span>)</small></td>
            </tr>
            <tr>
              <th class="pl-0">Persentase</th>
              <td>: <strong class="text-danger" id="punish-persen">0%</strong></td>
            </tr>
          </tbody>
        </table>

        <table class="table table-sm table-bordered mb-2">
          <tbody>
            <tr>
              <td class="bg-light">Tunjab Normal / Asli</td>
              <td class="text-right font-weight-bold" id="punish-tunjab-asli">Rp 0</td>
            </tr>
            <tr>
              <td class="bg-light">Rasio Kehadiran (<span id="punish-rasio-text">0 / 0</span>)</td>
              <td class="text-right text-danger font-weight-bold" id="punish-rasio-persen">0%</td>
            </tr>
            <tr class="table-active">
              <th class="font-weight-bold">Tunjab Diterima</th>
              <th class="text-right font-weight-bold text-danger fs-15" id="punish-tunjab-diterima">Rp 0</th>
            </tr>
          </tbody>
        </table>
        <small class="text-muted d-block text-right">
          <em>Perhitungan: Tunjab Asli &times; (Kehadiran Efektif &divide; Wajib Hadir)</em>
        </small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Validasi Silang Tunjangan Ganda (Struktural & Pengajar) -->
<div class="modal fade" id="modalCekValidasiUmana" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Validasi Tunjangan Umana'</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
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

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>
<script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

<script>
  function formatRupiahInput(value) {
    let number_string = value.replace(/[^,\d]/g, "").toString(),
        split = number_string.split(","),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
      let separator = sisa ? "." : "";
      rupiah += separator + ribuan.join(".");
    }
    return split[1] != undefined ? rupiah + "," + split[1] : rupiah;
  }

  $(document).on("keyup", ".numeric", function() {
    $(this).val(formatRupiahInput($(this).val()));
  });

  const dtTblStruk = $('#tabel_validasi').DataTable({
    paging: false,
    searching: true,
    ordering: false,
    info: false,
    fixedHeader: true,
    dom: 't',
  });

  // Sinkronisasi FixedHeader dengan scroll horizontal .table-responsive
  function adjustFloatingHeader() {
    const $resp = $('.table-responsive');
    if (!$resp.length) return;

    const $fp = $('.dtfh-floatingparenthead, .dtfh-floatingparent');
    if (!$fp.length || !$fp.is(':visible')) return;

    const respOffset = $resp.offset();
    const respWidth = $resp.outerWidth();
    const sl = $resp.scrollLeft();
    const winLeft = $(window).scrollLeft();

    // 1. Kunci posisi dan lebar container floating header agar sejajar dengan .table-responsive
    $fp.each(function() {
      this.style.setProperty('left', (respOffset.left - winLeft) + 'px', 'important');
      this.style.setProperty('width', respWidth + 'px', 'important');
      this.style.setProperty('max-width', respWidth + 'px', 'important');
      this.style.setProperty('overflow', 'hidden', 'important');
    });

    // 2. Samakan lebar tabel floating dan geser ke kiri sebesar scrollLeft
    const $origTbl = $('#tabel_validasi');
    const origWidth = $origTbl.outerWidth();
    const $floatingTbl = $fp.find('table');

    $floatingTbl.each(function() {
      if (origWidth > 0) {
        this.style.setProperty('width', origWidth + 'px', 'important');
        this.style.setProperty('min-width', origWidth + 'px', 'important');
      }
      this.style.setProperty('margin-left', (-sl) + 'px', 'important');
    });
  }

  // Listener scroll horizontal tabel
  $('.table-responsive').on('scroll', function() {
    adjustFloatingHeader();
  });

  // Listener scroll window (vertikal dan horizontal)
  $(window).on('scroll', function() {
    adjustFloatingHeader();
  });

  // Listener resize layar
  $(window).on('resize', function() {
    adjustFloatingHeader();
  });


  // Modal Detail Punishment
  $(document).on('click', '.link-punishment', function() {
    const nama = $(this).data('nama');
    const jabatan = $(this).data('jabatan');
    const wajib = parseFloat($(this).data('wajib')) || 0;
    const hadir = parseFloat($(this).data('hadir')) || 0;
    const tugas = parseFloat($(this).data('tugas')) || 0;
    const izin = parseFloat($(this).data('izin')) || 0;
    const sakit = parseFloat($(this).data('sakit')) || 0;
    const persen = parseFloat($(this).data('persen')) || 0;
    const tunjabAsli = parseInt($(this).data('tunjab-asli'), 10) || 0;
    const tunjabDiterima = parseInt($(this).data('tunjab-diterima'), 10) || 0;

    // Kehadiran efektif = (Hadir * 1) + (Tugas * 1) + (Izin * 0.25) + (Sakit * 1.0)
    const efektif = (hadir * 1) + (tugas * 1) + (izin * 0.25) + (sakit * 1.0);
    const rasioPersen = wajib > 0 ? ((efektif / wajib) * 100).toFixed(1) : '0';

    $('#punish-nama').text(nama);
    $('#punish-jabatan').text(jabatan);
    $('#punish-wajib').text(wajib);
    $('#punish-efektif').text(efektif % 1 === 0 ? efektif : efektif.toFixed(2));
    $('#punish-persen').text(persen.toFixed(2) + '%');

    $('#punish-hadir').text(hadir);
    $('#punish-tugas').text(tugas);
    $('#punish-izin').text(izin);
    $('#punish-sakit').text(sakit);

    $('#punish-tunjab-asli').text('Rp ' + tunjabAsli.toLocaleString('id-ID'));
    $('#punish-rasio-text').text((efektif % 1 === 0 ? efektif : efektif.toFixed(2)) + ' / ' + wajib);
    $('#punish-rasio-persen').text(rasioPersen + '%');
    $('#punish-tunjab-diterima').text('Rp ' + tunjabDiterima.toLocaleString('id-ID'));

    $('#modalPunishment').modal('show');
  });

  // Modal Validasi Silang Umana' (Tunjangan Ganda Struktural vs Pengajar)
  $(document).on('click', '.link-cek-umana', function() {
    const nik = $(this).data('nik');
    const namaDefault = $(this).data('nama');

    $('#val-umana-nama').text(namaDefault);
    $('#val-umana-nik').text(nik);
    $('#val-tbody-struktural').html('<tr><td colspan="7" class="text-center text-muted">Memuat data penempatan struktural...</td></tr>');
    $('#val-tbody-pengajar').html('<tr><td colspan="8" class="text-center text-muted">Memuat data penugasan pengajar...</td></tr>');

    $('#modalCekValidasiUmana').modal('show');

    $.getJSON("{{ url('/kehadiran_struktural/cekValidasiUmana') }}/" + nik, function(res) {
      if(!res || !res.status) {
        $('#val-tbody-struktural').html('<tr><td colspan="7" class="text-center text-danger">Gagal memuat data.</td></tr>');
        $('#val-tbody-pengajar').html('<tr><td colspan="8" class="text-center text-danger">Gagal memuat data.</td></tr>');
        return;
      }

      // Update profil
      $('#val-umana-nama').text(res.umana.nama_lengkap);

      const sertifVal = res.umana.status_sertifikasi || 'Belum';

      // 1. Render Tabel Struktural
      // Kolom: No, Nama, Lembaga, Sertifikasi, Tunkel, Tunj Anak, Kehormatan
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
      // Kolom: No, Nama, Lembaga, Sertifikasi, GTY/DTY, Tunkel, Tun Anak, Kehormatan
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

          const jenisPengajar = item.jenis_pengajar ? `<span class="badge badge-primary px-2 py-1">${item.jenis_pengajar}</span>` : '-';

          htmlPeng += `<tr>
            <td class="text-center font-weight-bold">${idx + 1}</td>
            <td><strong>${res.umana.nama_lengkap}</strong></td>
            <td><strong>${item.nama_lembaga || '-'}</strong></td>
            <td class="text-center">${sertifVal}</td>
            <td class="text-center">${jenisPengajar}</td>
            <td class="text-center">${tunkelBadge}</td>
            <td class="text-center">${tunjAnakBadge}</td>
            <td class="text-center">${kehormatanBadge}</td>
          </tr>`;
        });
      } else {
        htmlPeng = '<tr><td colspan="8" class="text-center text-muted">Umana ini tidak terdaftar memiliki penugasan aktif sebagai Pengajar (Guru / Dosen).</td></tr>';
      }
      $('#val-tbody-pengajar').html(htmlPeng);

    }).fail(function() {
      $('#val-tbody-struktural').html('<tr><td colspan="7" class="text-center text-danger">Gagal menghubungi server.</td></tr>');
      $('#val-tbody-pengajar').html('<tr><td colspan="8" class="text-center text-danger">Gagal menghubungi server.</td></tr>');
    });
  });

  $('#customSearchStruk').on('keyup', function() {
    dtTblStruk.search(this.value).draw();
  });

  $('.dropdown-menu').on('click', function(e) {
    e.stopPropagation();
  });

  $('.toggle-vis-struk').on('change', function (e) {
    var columnsStr = $(this).attr('data-columns');
    var columns = columnsStr.split(',').map(Number);
    var isVisible = $(this).is(':checked');
    dtTblStruk.columns(columns).visible(isVisible);
    dtTblStruk.columns.adjust().draw();
    setTimeout(adjustFloatingHeader, 50);
  });

  $(function () {
    $('[data-toggle="tooltip"]').tooltip();
  });

  const idL = '{{ $periode->id_kehadiran_lembaga ?? "" }}';

  // KIRIM
  $(document).on('click', '#btnKirim', function(){
    const $btn = $(this);
    if(!idL){ Swal.fire('Error','ID periode tidak ditemukan.','error'); return; }

    $btn.prop('disabled', true).text('Mengirim...');
    $.ajax({
      url: "{{ url('/kehadiran_struktural/kirim') }}",
      type: "POST",
      dataType: "json",
      data: {
        _token: '{{ csrf_token() }}',
        id_kehadiran_lembaga: idL
      }
    })
    .done(function(res){
      if(res && res.status){
        Swal.fire({icon:'success', title:'Terkirim', text: res.message || 'Periode berhasil dikirim.', timer:1400, showConfirmButton:false})
        .then(()=>{ 
          window.location.href = "{{ url('/kehadiran_struktural') }}";
        });
      } else {
        Swal.fire('Gagal', (res && res.message) ? res.message : 'Gagal mengirim periode.','error');
      }
    })
    .fail(function(xhr){
      Swal.fire('Error','Gagal menghubungi server.','error');
    })
    .always(function(){
      $btn.prop('disabled', false).html('<i class="fa fa-send mr-1"></i> Kirim');
    });
  });

  // LocalStorage Auto-save
  const id_leb_storage = "{{ $periode->id_kehadiran_lembaga ?? '' }}";
  $(function() {
    if (!id_leb_storage) return;

    let savedUmum = localStorage.getItem('catatan_umum_' + id_leb_storage);
    if (savedUmum) {
      $('textarea[name="catatan_umum_pimpinan"]').val(savedUmum);
    }

    $('textarea[name="catatan_umum_pimpinan"]').on('keyup change', function() {
      localStorage.setItem('catatan_umum_' + id_leb_storage, $(this).val());
    });

    $('.btn-catatan').each(function() {
      let id = $(this).data('id');
      let savedKhusus = localStorage.getItem('catatan_pegawai_' + id_leb_storage + '_' + id);
      
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
  });

  // SETUJUI
  $(document).on('click', '#btnSetujui', function(){
    const $btn = $(this);
    if(!idL){ Swal.fire('Error','ID periode tidak ditemukan.','error'); return; }

    $btn.prop('disabled', true).text('Menyetujui...');
    $.ajax({
      url: "{{ url('/kehadiran_struktural/validasi') }}",
      type: "POST",
      dataType: "json",
      data: $('#form').serialize() + '&id_kehadiran_lembaga=' + idL + '&action_type=setuju'
    })
    .done(function(res){
      if(res && res.status){
        if (id_leb_storage) {
          localStorage.removeItem('catatan_umum_' + id_leb_storage);
          $('.btn-catatan').each(function() {
            localStorage.removeItem('catatan_pegawai_' + id_leb_storage + '_' + $(this).data('id'));
          });
        }
        
        Swal.fire({icon:'success', title:'Berhasil!', text: res.message || 'Periode disetujui & disimpan.', timer:1500, showConfirmButton:false})
        .then(()=>{ 
          window.location.href = "{{ url('/kehadiran_struktural') }}"; 
        });
      } else {
        Swal.fire('Gagal', (res && res.message) ? res.message : 'Validasi gagal.','error');
      }
    })
    .fail(function(xhr){
      Swal.fire('Error','Gagal menghubungi server.','error');
    })
    .always(function(){
      $btn.prop('disabled', false).html('<i class="fa fa-check mr-1"></i> Setujui');
    });
  });

  // KEMBALIKAN (REVISI)
  $(document).on('click', '#btnKembalikan', function(){
    const $btn = $(this);
    if(!idL){ Swal.fire('Error','ID periode tidak ditemukan.','error'); return; }

    Swal.fire({
      title: 'Kembalikan untuk Revisi?',
      text: "Pastikan sudah menulis catatan pada kolom evaluasi agar Admin Lembaga mengerti apa yang harus diperbaiki.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ffc107',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Kembalikan!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.value || result.isConfirmed) {
        $btn.prop('disabled', true).text('Memproses...');
        $.ajax({
          url: "{{ url('/kehadiran_struktural/validasi') }}",
          type: "POST",
          dataType: "json",
          data: $('#form').serialize() + '&id_kehadiran_lembaga=' + idL + '&action_type=revisi'
        })
        .done(function(res){
          if(res && res.status){
            if (id_leb_storage) {
              localStorage.removeItem('catatan_umum_' + id_leb_storage);
              $('.btn-catatan').each(function() {
                localStorage.removeItem('catatan_pegawai_' + id_leb_storage + '_' + $(this).data('id'));
              });
            }
            
            Swal.fire({icon:'success', title:'Dikembalikan!', text: res.message || 'Dokumen dikembalikan ke Admin Lembaga.', timer:1500, showConfirmButton:false})
            .then(()=>{ 
              window.location.href = "{{ url('/kehadiran_struktural') }}"; 
            });
          } else {
            Swal.fire('Gagal', (res && res.message) ? res.message : 'Proses gagal.','error');
          }
        })
        .fail(function(xhr){
          Swal.fire('Error','Gagal menghubungi server.','error');
        })
        .always(function(){
          $btn.prop('disabled', false).html('<i class="fa fa-undo mr-1"></i> Kembalikan (Revisi)');
        });
      }
    });
  });

  // DETAIL TBK
  let tbkIndex = 0;
  const tbkOptions = [
    "TBK Jumlah Siswa", "TBK Mengajar", "TBK Walikelas", 
    "TBK Jml Prodi", "TBK Keahlian", "TBK Resiko/Keahlian", 
    "TBK Wilayah Kerja", "TBK Lain-lain"
  ];

  function getTbkSelect(index, selectedValue) {
    let optionsHtml = '<option value="">-- Pilih TBK --</option>';
    tbkOptions.forEach(opt => {
      let sel = (opt === selectedValue) ? 'selected' : '';
      optionsHtml += `<option value="${opt}" ${sel}>${opt}</option>`;
    });
    if (selectedValue && !tbkOptions.includes(selectedValue)) {
      optionsHtml += `<option value="${selectedValue}" selected>${selectedValue}</option>`;
    }
    return `<select class="form-control form-control-sm" name="tbk[${index}][jenis_tbk]" required>${optionsHtml}</select>`;
  }

  $(document).on('click', '.link-tbk', function(){
    const id = $(this).data('id');
    $('#tbk_id_penempatan').val(id);
    $('#tbkBody').html('<tr><td colspan="3" class="text-center text-muted">Memuat...</td></tr>');
    $('#modalTBK').modal('show');

    $.getJSON("{{ url('/kehadiran_struktural/detailTbk') }}/" + id, function(data){
      tbkIndex = 0;
      let rows = '';
      if(data && data.length > 0){
        data.forEach(item => {
          rows += `
            <tr>
              <td>
                <input type="hidden" name="tbk[${tbkIndex}][id_tbk]" value="${item.id_tbk}">
                ${getTbkSelect(tbkIndex, item.jenis_tbk)}
              </td>
              <td>
                <input type="text" class="form-control form-control-sm text-right numeric" name="tbk[${tbkIndex}][nominal_tbk]" value="${formatRupiahInput((item.nominal_tbk ?? 0).toString())}" required>
              </td>
              <td>
                <input type="date" class="form-control form-control-sm text-center" name="tbk[${tbkIndex}][max_periode]" value="${item.max_periode ?? ''}">
              </td>
            </tr>
          `;
          tbkIndex++;
        });
      }
      if (rows === '') {
        rows = '<tr><td colspan="3" class="text-center text-muted" id="emptyTBK">Tidak ada data TBK aktif. Silakan klik Tambah Baris.</td></tr>';
      }
      $('#tbkBody').html(rows);
    }).fail(function(){
      $('#tbkBody').html('<tr><td colspan="3" class="text-center text-danger">Gagal memuat data TBK.</td></tr>');
    });
  });

  $('#btnTambahTBK').click(function(){
    $('#emptyTBK').remove();
    let optionsHtml = '<option value="">-- Pilih TBK --</option>';
    tbkOptions.forEach(opt => {
      optionsHtml += `<option value="${opt}">${opt}</option>`;
    });

    let newRow = `
      <tr>
        <td>
          <input type="hidden" name="tbk[${tbkIndex}][id_tbk]" value="">
          <select class="form-control form-control-sm" name="tbk[${tbkIndex}][jenis_tbk]" required>${optionsHtml}</select>
        </td>
        <td>
          <input type="text" class="form-control form-control-sm text-right numeric" name="tbk[${tbkIndex}][nominal_tbk]" placeholder="Nominal" required>
        </td>
        <td>
          <input type="date" class="form-control form-control-sm text-center" name="tbk[${tbkIndex}][max_periode]">
        </td>
      </tr>
    `;
    $('#tbkBody').append(newRow);
    tbkIndex++;
  });

  $('#formTBK').submit(function(e){
    e.preventDefault();
    $('#btnSimpanTBK').prop('disabled', true).html('Menyimpan...');
    $.ajax({
      url: "{{ url('/kehadiran_struktural/saveTbk') }}",
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function(res){
        if(res.status){
          Swal.fire({icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false})
          .then(() => location.reload());
        } else {
          Swal.fire('Gagal', 'Gagal menyimpan TBK', 'error');
          $('#btnSimpanTBK').prop('disabled', false).html('Simpan TBK');
        }
      },
      error: function(){
        Swal.fire('Error', 'Terjadi kesalahan pada server.', 'error');
        $('#btnSimpanTBK').prop('disabled', false).html('Simpan TBK');
      }
    });
  });

  // DETAIL POTONGAN
  let potonganOptions = [];
  let potonganIndex = 0;
  $.getJSON("{{ url('/kehadiran_struktural/getPotonganOptions') }}", function(data){
    potonganOptions = data;
  });

  $(document).on('click', '.link-potongan', function(){
    const id = $(this).data('id');
    const nama = $(this).data('nama');
    const periodeDate = "{{ $periode->tahun }}-{{ sprintf('%02d', $bulan_int) }}-01";
    
    $('#potonganPegawaiName').text(nama);
    $('#potongan_id_penempatan').val(id);
    $('#potonganBody').html('<tr><td colspan="4" class="text-center text-muted">Memuat...</td></tr>');
    $('#modalPotongan').modal('show');
    potonganIndex = 0;

    $.getJSON("{{ url('/kehadiran_struktural/detailPotongan') }}/" + id + '/' + periodeDate, function(data){
      let rows = '';
      if(data && data.length > 0){
        data.forEach(item => {
          let optionsHtml = '<option value="">Pilih Potongan...</option>';
          potonganOptions.forEach(opt => {
            let selected = (opt.id_potongan == item.jenis_potongan) ? 'selected' : '';
            optionsHtml += `<option value="${opt.id_potongan}" ${selected}>${opt.nama_potongan}</option>`;
          });
          if (item.jenis_potongan && !potonganOptions.find(o => o.id_potongan == item.jenis_potongan)) {
            optionsHtml += `<option value="${item.jenis_potongan}" selected>${item.nama_potongan ?? item.jenis_potongan}</option>`;
          }

          rows += `
            <tr>
              <td>
                <input type="hidden" name="potongan[${potonganIndex}][id_potongan_umana]" value="${item.id_potongan_umana}">
                <select class="form-control form-control-sm" name="potongan[${potonganIndex}][jenis_potongan]" required>${optionsHtml}</select>
              </td>
              <td>
                <input type="text" class="form-control form-control-sm text-right numeric" name="potongan[${potonganIndex}][nominal_potongan]" value="${formatRupiahInput((item.nominal_potongan ?? 0).toString())}" placeholder="Nominal" required>
              </td>
              <td>
                <input type="date" class="form-control form-control-sm text-center" name="potongan[${potonganIndex}][min_periode_potongan]" value="${item.min_periode_potongan ?? ''}">
              </td>
              <td>
                <input type="date" class="form-control form-control-sm text-center" name="potongan[${potonganIndex}][max_periode_potongan]" value="${item.max_periode_potongan ?? ''}">
              </td>
            </tr>
          `;
          potonganIndex++;
        });
      }
      if (rows === '') {
        rows = '<tr><td colspan="4" class="text-center text-muted" id="emptyPotongan">Tidak ada data potongan aktif. Silakan klik Tambah Baris.</td></tr>';
      }
      $('#potonganBody').html(rows);
    }).fail(function(){
      $('#potonganBody').html('<tr><td colspan="4" class="text-center text-danger">Gagal memuat data potongan.</td></tr>');
    });
  });

  $('#btnTambahPotongan').click(function(){
    $('#emptyPotongan').remove();
    let optionsHtml = '<option value="">Pilih Potongan...</option>';
    potonganOptions.forEach(opt => {
      optionsHtml += `<option value="${opt.id_potongan}">${opt.nama_potongan}</option>`;
    });
    
    let newRow = `
      <tr>
        <td>
          <input type="hidden" name="potongan[${potonganIndex}][id_potongan_umana]" value="">
          <select class="form-control form-control-sm" name="potongan[${potonganIndex}][jenis_potongan]" required>${optionsHtml}</select>
        </td>
        <td>
          <input type="text" class="form-control form-control-sm text-right numeric" name="potongan[${potonganIndex}][nominal_potongan]" placeholder="Nominal" required>
        </td>
        <td>
          <input type="date" class="form-control form-control-sm text-center" name="potongan[${potonganIndex}][min_periode_potongan]">
        </td>
        <td>
          <input type="date" class="form-control form-control-sm text-center" name="potongan[${potonganIndex}][max_periode_potongan]">
        </td>
      </tr>
    `;
    $('#potonganBody').append(newRow);
    potonganIndex++;
  });

  $('#formPotongan').submit(function(e){
    e.preventDefault();
    $('#btnSimpanPotongan').prop('disabled', true).html('Menyimpan...');
    $.ajax({
      url: "{{ url('/kehadiran_struktural/savePotongan') }}",
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function(res){
        if(res.status){
          Swal.fire({icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false})
          .then(() => location.reload());
        } else {
          Swal.fire('Gagal', 'Gagal menyimpan Potongan', 'error');
          $('#btnSimpanPotongan').prop('disabled', false).html('Simpan Potongan');
        }
      },
      error: function(){
        Swal.fire('Error', 'Terjadi kesalahan pada server.', 'error');
        $('#btnSimpanPotongan').prop('disabled', false).html('Simpan Potongan');
      }
    });
  });

  // MODAL CATATAN KHUSUS
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
      localStorage.setItem('catatan_pegawai_' + id_leb_storage + '_' + id, val);
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
    
    $('#modalCatatan').modal('hide');
  });

  // KOREKSI KEHADIRAN (ROW EDIT)
  $(document).on('click', '.btn-edit', function() {
    const id = $(this).data('id');
    const idLembaga = $(this).data('lembaga');
    const hari = $(this).data('hari') || 0;
    const tugas = $(this).data('tugas') || 0;
    const izin = $(this).data('izin') || 0;
    const sakit = $(this).data('sakit') || 0;

    $('#edit-id').val(id);
    $('#edit-lembaga').val(idLembaga);
    
    $('#cur-hari').text(hari);
    $('#edit-hari').val('').attr('placeholder', `Saat ini: ${hari}`);
    
    $('#cur-tugas').text(tugas);
    $('#edit-tugas').val('').attr('placeholder', `Saat ini: ${tugas}`);
    
    $('#cur-izin').text(izin);
    $('#edit-izin').val('').attr('placeholder', `Saat ini: ${izin}`);
    
    $('#cur-sakit').text(sakit);
    $('#edit-sakit').val('').attr('placeholder', `Saat ini: ${sakit}`);

    $('#modalEdit').modal('show');
  });

  $('#modalEdit').on('hidden.bs.modal', function(){
    $('#edit-id, #edit-lembaga').val('');
    $('#edit-hari, #edit-tugas, #edit-izin, #edit-sakit').val('');
    $('#cur-hari, #cur-tugas, #cur-izin, #cur-sakit').text('0');
  });

  $('#btnSimpanEdit').on('click', function(){
    const id = $('#edit-id').val();
    const idLembaga = $('#edit-lembaga').val();
    
    let hari = $('#edit-hari').val() === '' ? parseInt($('#cur-hari').text(), 10) : parseInt($('#edit-hari').val(), 10);
    let tugas = $('#edit-tugas').val() === '' ? parseInt($('#cur-tugas').text(), 10) : parseInt($('#edit-tugas').val(), 10);
    let izin = $('#edit-izin').val() === '' ? parseInt($('#cur-izin').text(), 10) : parseInt($('#edit-izin').val(), 10);
    let sakit = $('#edit-sakit').val() === '' ? parseInt($('#cur-sakit').text(), 10) : parseInt($('#edit-sakit').val(), 10);

    if(hari < 0 || tugas < 0 || izin < 0 || sakit < 0){ 
      Swal.fire('Periksa!', 'Nilai tidak boleh negatif.', 'warning'); 
      return; 
    }
    if(hari > 200 || tugas > 200 || izin > 200 || sakit > 200){ 
      Swal.fire('Periksa!', 'Jumlah tidak wajar (>200).', 'warning'); 
      return; 
    }

    $.ajax({
      url: "{{ url('/kehadiran_struktural/updateRow') }}",
      type: "POST",
      dataType: "json",
      data: {
        _token: '{{ csrf_token() }}',
        id_kehadiran: id,
        id_kehadiran_lembaga: idLembaga,
        jumlah_hadir: hari,
        jumlah_tugas: tugas,
        jumlah_izin: izin,
        jumlah_sakit: sakit
      },
      success: function(res) {
        if (!res || !res.status) {
          Swal.fire('Gagal', (res && res.message) ? res.message : 'Update gagal.', 'error');
          return;
        }

        $('#hari-' + id).text(res.row.jumlah_hadir);
        $('#tugas-' + id).text(res.row.jumlah_tugas);
        $('#izin-' + id).text(res.row.jumlah_izin);
        $('#sakit-' + id).text(res.row.jumlah_sakit);
        $('#jmlhadir-' + id).text(res.row.nominal_kehadiran);

        if(res.row.jumlah_barokah !== undefined) $('#jumlahbarokah-' + id).text(res.row.jumlah_barokah);
        if(res.row.diterima !== undefined) $('#diterima-' + id).text(res.row.diterima);

        if (res.totals) {
          $('#ft-kehadiran').text(res.totals.total_kehadiran.toLocaleString('id-ID'));
          $('#ft-barokah').text(res.totals.total_barokah.toLocaleString('id-ID'));
          $('#ft-grand').text(res.totals.grand_total.toLocaleString('id-ID'));

          $('#sumKehadiran').text($('#ft-kehadiran').text());
          $('#sumGrand').text($('#ft-grand').text());
        }

        $('.btn-edit[data-id="' + id + '"]')
          .attr('data-hari', res.row.jumlah_hadir)
          .attr('data-tugas', res.row.jumlah_tugas)
          .attr('data-izin', res.row.jumlah_izin)
          .attr('data-sakit', res.row.jumlah_sakit);

        $('#modalEdit').modal('hide');

        Swal.fire({
          icon: 'success',
          title: 'Tersimpan',
          timer: 1200,
          showConfirmButton: false
        }).then(() => {
          location.reload();
        });
      },
      error: function(xhr) {
        Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
      }
    });
  });
</script>
</body>
</html>
