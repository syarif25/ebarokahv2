<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan Final Barokah Satpam - Bulan {{ $periode->bulan }} {{ $periode->tahun }}</title>
  <link rel="icon" href="{{ asset('images/p2s2_fav.png') }}" type="image/x-icon">
  <link rel="shortcut icon" href="{{ asset('images/p2s2_fav.png') }}" type="image/x-icon">

  <!-- Bootstrap 4 & FontAwesome -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

  <style>
    body { background: #f4f6f9; font-size: 13px; color: #2d3748; }
    .table thead th { vertical-align: middle !important; text-align: center; font-size: 0.78rem; text-transform: uppercase; }
    .badge-total { 
      font-size: .85rem; 
      font-weight: 500;
      padding: 0.45rem 0.75rem; 
      border-radius: 8px;
      margin-right: .5rem;
      display: inline-flex;
      align-items: center;
      background: #fff;
      border: 1px solid #e2e8f0;
      color: #475569;
      box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .badge-total span {
      font-weight: 700;
      color: #0f172a;
      margin-left: 4px;
    }
    .badge-total.badge-primary-custom {
      background: #00a15d !important;
      border-color: #00a15d !important;
      color: #fff !important;
    }
    .badge-total.badge-primary-custom span {
      color: #fff !important;
    }
    .cell-right { text-align: right; }
    .cell-center { text-align: center; }
    .table-container {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.04);
      padding: 1.25rem;
      border: 1px solid #e2e8f0;
    }
    .btn-rounded-custom {
      border-radius: 20px !important;
      padding: 6px 20px !important;
      font-weight: 600;
    }
  </style>
</head>
<body>

@php
  $sum_transport = 0;
  $sum_barokah   = 0;
  $sum_dini      = 0;
  $sum_danru     = 0;
  $sum_grand     = 0;

  foreach($isilist as $row) {
      $sum_transport += (int)$row->jumlah_transport;
      $sum_barokah   += (int)$row->jumlah_barokah;
      $sum_dini      += (int)$row->jumlah_konsumsi;
      $sum_danru     += (int)$row->nominal_danru;
      $sum_grand     += (int)$row->diterima;
  }
@endphp

<div class="container-fluid my-3 px-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-1 font-weight-bold text-dark">
        <i class="fas fa-file-invoice-dollar text-success mr-2"></i>Laporan Final Barokah Satpam
      </h4>
      <span class="text-muted">Periode Bulan <strong>{{ $periode->bulan }} {{ $periode->tahun }}</strong> &bull; Status: <span class="badge badge-success">{{ $periode->status }}</span></span>
    </div>
    <div>
      <a class="btn btn-warning btn-rounded-custom shadow-sm mr-2 text-dark font-weight-bold" target="_blank" href="{{ url('/kehadiran-satpam/' . $periode->id_kehadiran_lembaga . '/cetak') }}">
        <i class="fas fa-print mr-1"></i> Cetak PDF
      </a>
      <a class="btn btn-secondary btn-rounded-custom shadow-sm" href="{{ url('/kehadiran-satpam') }}">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
      </a>
    </div>
  </div>

  <!-- Ringkasan Badges -->
  <div class="mb-3 d-flex flex-wrap align-items-center">
    <div class="badge-total">Total Transport: <span>{{ number_format($sum_transport, 0, ',', '.') }}</span></div>
    <div class="badge-total">Total Barokah: <span>{{ number_format($sum_barokah, 0, ',', '.') }}</span></div>
    <div class="badge-total">Total Dinihari: <span>{{ number_format($sum_dini, 0, ',', '.') }}</span></div>
    <div class="badge-total">Total Danru: <span class="text-info">{{ number_format($sum_danru, 0, ',', '.') }}</span></div>
    <div class="badge-total badge-primary-custom ml-auto">Grand Total: <span>{{ number_format($sum_grand, 0, ',', '.') }}</span></div>
  </div>

  <div class="table-container mb-4">
    <div class="table-responsive">
      <table id="tabel_laporan" class="table table-bordered table-hover mb-0" style="width:100%">
        <thead class="thead-light">
          <tr>
            <th rowspan="2" style="width:40px">No</th>
            <th rowspan="2">Nama Satpam</th>
            <th colspan="3">Kehadiran 1 (Siang)</th>
            <th colspan="3">Kehadiran 2 (Shift)</th>
            <th colspan="3">Kehadiran 3 (Dinihari)</th>
            <th rowspan="2" style="width:100px">Danru</th>
            <th rowspan="2" style="width:120px">Total</th>
          </tr>
          <tr>
            <th>Hari</th><th>Trans</th><th>Jumlah</th>
            <th>Shift</th><th>Rank</th><th>Jumlah</th>
            <th>Dini</th><th>Kons</th><th>Jumlah</th>
          </tr>
        </thead>
        <tbody>
          @php $no = 1; @endphp
          @foreach($isilist as $d)
            @php
              $namaLengkap = trim(($d->gelar_depan ?? '') . ' ' . $d->nama_lengkap . ' ' . ($d->gelar_belakang ?? ''));
            @endphp
            <tr>
              <td class="cell-center text-muted">{{ $no++ }}</td>
              <td>
                <span class="font-weight-bold text-dark">{{ $namaLengkap }}</span>
                @if(isset($d->is_danru) && $d->is_danru == '1')
                  <span class="badge badge-info ml-1">Danru</span>
                @endif
              </td>

              <!-- Kehadiran 1 (Siang) -->
              <td class="cell-center font-weight-bold">{{ $d->jumlah_hari }}</td>
              <td class="cell-right">{{ number_format($d->nominal_transport, 0, ',', '.') }}</td>
              <td class="cell-right">{{ number_format($d->jumlah_transport, 0, ',', '.') }}</td>

              <!-- Kehadiran 2 (Shift) -->
              <td class="cell-center font-weight-bold">{{ $d->jumlah_shift }}</td>
              <td class="cell-right">{{ number_format($d->rank, 0, ',', '.') }}</td>
              <td class="cell-right">{{ number_format($d->jumlah_barokah, 0, ',', '.') }}</td>

              <!-- Kehadiran 3 (Dinihari) -->
              <td class="cell-center font-weight-bold">{{ $d->jumlah_dinihari }}</td>
              <td class="cell-right">{{ number_format($d->konsumsi, 0, ',', '.') }}</td>
              <td class="cell-right">{{ number_format($d->jumlah_konsumsi, 0, ',', '.') }}</td>

              <!-- Danru -->
              <td class="cell-right">{{ number_format($d->nominal_danru, 0, ',', '.') }}</td>

              <!-- Total baris -->
              <td class="cell-right font-weight-bold text-success">
                {{ number_format($d->diterima, 0, ',', '.') }}
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot class="thead-light font-weight-bold">
          <tr>
            <th colspan="4" class="text-center">TOTAL TRANSPORT</th>
            <th class="cell-right text-dark">{{ number_format($sum_transport, 0, ',', '.') }}</th>
            <th colspan="2" class="text-center">TOTAL BAROKAH</th>
            <th class="cell-right text-dark">{{ number_format($sum_barokah, 0, ',', '.') }}</th>
            <th colspan="2" class="text-center">TOTAL DINIHARI</th>
            <th class="cell-right text-dark">{{ number_format($sum_dini, 0, ',', '.') }}</th>
            <th class="cell-right text-info">{{ number_format($sum_danru, 0, ',', '.') }}</th>
            <th class="cell-right text-success" style="font-size: 1rem;">{{ number_format($sum_grand, 0, ',', '.') }}</th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

</body>
</html>
