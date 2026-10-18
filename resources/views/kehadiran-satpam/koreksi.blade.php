<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Validasi Penggajian Satpam - Bulan {{ $periode->bulan }} {{ $periode->tahun }}</title>
  <link rel="icon" href="{{ asset('images/p2s2_fav.png') }}" type="image/x-icon">
  <link rel="shortcut icon" href="{{ asset('images/p2s2_fav.png') }}" type="image/x-icon">

  <!-- Bootstrap 4 & FontAwesome -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}">

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
    .sticky-actions { 
      position: sticky; 
      bottom: 0; 
      background: #fff; 
      z-index: 100; 
      border-top: 1px solid #e2e8f0; 
      padding: .85rem 1.5rem;
      box-shadow: 0 -4px 12px rgba(0,0,0,0.05);
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

<div class="container-fluid my-3 px-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-1 font-weight-bold text-dark">
        <i class="fas fa-shield-alt text-success mr-2"></i>Validasi Penggajian Satpam
      </h4>
      <span class="text-muted">Periode Bulan <strong>{{ $periode->bulan }} {{ $periode->tahun }}</strong> &bull; Status: <span class="badge badge-warning text-dark">{{ $periode->status }}</span></span>
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
    <div class="badge-total">Total Transport: <span id="sumTransport">{{ number_format($totals['total_transport'], 0, ',', '.') }}</span></div>
    <div class="badge-total">Total Barokah: <span id="sumBarokah">{{ number_format($totals['total_barokah'], 0, ',', '.') }}</span></div>
    <div class="badge-total">Total Dinihari: <span id="sumDini">{{ number_format($totals['total_dinihari'], 0, ',', '.') }}</span></div>
    <div class="badge-total">Total Danru: <span id="sumDanru" class="text-info">{{ number_format($totals['total_danru'], 0, ',', '.') }}</span></div>
    <div class="badge-total badge-primary-custom ml-auto">Grand Total: <span id="sumGrand">{{ number_format($totals['grand_total'], 0, ',', '.') }}</span></div>
  </div>

  <div class="table-container mb-4">
    <div class="table-responsive">
      <table id="tabel_validasi" class="table table-bordered table-hover mb-0" style="width:100%">
        <thead class="thead-light">
          <tr>
            <th rowspan="2" style="width:40px">No</th>
            <th rowspan="2">Nama Satpam</th>
            <th colspan="3">Kehadiran 1 (Siang)</th>
            <th colspan="3">Kehadiran 2 (Shift)</th>
            <th colspan="3">Kehadiran 3 (Dinihari)</th>
            <th rowspan="2" style="width:100px">Danru</th>
            <th rowspan="2" style="width:120px">Total</th>
            <th rowspan="2" style="width:70px">Aksi</th>
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
              $calc = $d->calc;
              $namaLengkap = trim(($d->gelar_depan ?? '') . ' ' . $d->nama_lengkap . ' ' . ($d->gelar_belakang ?? ''));
            @endphp
            <tr id="row-{{ $d->id_kehadiran_satpam }}">
              <td class="cell-center text-muted">{{ $no++ }}</td>
              <td>
                <span class="font-weight-bold text-dark">{{ $namaLengkap }}</span>
                @if(isset($d->is_danru) && $d->is_danru == '1')
                  <span class="badge badge-info ml-1">Danru</span>
                @endif
              </td>

              <!-- Kehadiran 1 (Siang) -->
              <td id="hari-{{ $d->id_kehadiran_satpam }}" class="cell-center font-weight-bold">{{ $calc['jumlah_hari'] }}</td>
              <td class="cell-right">{{ number_format($calc['nominal_transport'], 0, ',', '.') }}</td>
              <td id="jmltrans-{{ $d->id_kehadiran_satpam }}" class="cell-right">{{ number_format($calc['jumlah_transport'], 0, ',', '.') }}</td>

              <!-- Kehadiran 2 (Shift) -->
              <td id="shift-{{ $d->id_kehadiran_satpam }}" class="cell-center font-weight-bold">{{ $calc['jumlah_shift'] }}</td>
              <td class="cell-right">{{ number_format($calc['rank'], 0, ',', '.') }}</td>
              <td id="jmlbar-{{ $d->id_kehadiran_satpam }}" class="cell-right">{{ number_format($calc['jumlah_barokah'], 0, ',', '.') }}</td>

              <!-- Kehadiran 3 (Dinihari) -->
              <td id="dini-{{ $d->id_kehadiran_satpam }}" class="cell-center font-weight-bold">{{ $calc['jumlah_dinihari'] }}</td>
              <td class="cell-right">{{ number_format($calc['konsumsi'], 0, ',', '.') }}</td>
              <td id="jmlkons-{{ $d->id_kehadiran_satpam }}" class="cell-right">{{ number_format($calc['jumlah_konsumsi'], 0, ',', '.') }}</td>

              <!-- Danru -->
              <td id="danru-{{ $d->id_kehadiran_satpam }}" class="cell-right">{{ number_format($calc['nominal_danru'], 0, ',', '.') }}</td>

              <!-- Total baris -->
              <td id="subtotal-{{ $d->id_kehadiran_satpam }}" class="cell-right font-weight-bold text-success">
                {{ number_format($calc['diterima'], 0, ',', '.') }}
              </td>

              <!-- Aksi -->
              <td class="cell-center">
                <button type="button"
                        class="btn btn-xs btn-outline-primary btn-edit rounded-pill px-3"
                        data-id="{{ $d->id_kehadiran_satpam }}"
                        data-lembaga="{{ $d->id_kehadiran_lembaga }}"
                        data-hari="{{ $calc['jumlah_hari'] }}"
                        data-shift="{{ $calc['jumlah_shift'] }}"
                        data-dini="{{ $calc['jumlah_dinihari'] }}"
                        data-nama="{{ $namaLengkap }}">
                  <i class="fas fa-edit mr-1"></i> Edit
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot class="thead-light font-weight-bold">
          <tr>
            <th colspan="4" class="text-center">TOTAL TRANSPORT</th>
            <th id="ft-transport" class="cell-right text-dark">{{ number_format($totals['total_transport'], 0, ',', '.') }}</th>
            <th colspan="2" class="text-center">TOTAL BAROKAH</th>
            <th id="ft-barokah" class="cell-right text-dark">{{ number_format($totals['total_barokah'], 0, ',', '.') }}</th>
            <th colspan="2" class="text-center">TOTAL DINIHARI</th>
            <th id="ft-dini" class="cell-right text-dark">{{ number_format($totals['total_dinihari'], 0, ',', '.') }}</th>
            <th id="ft-danru" class="cell-right text-info">{{ number_format($totals['total_danru'], 0, ',', '.') }}</th>
            <th id="ft-grand" class="cell-right text-success" style="font-size: 1rem;">{{ number_format($totals['grand_total'], 0, ',', '.') }}</th>
            <th></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

<!-- Sticky Bottom Action Bar -->
@php
  $statusPeriode = $periode->status ?? '';
  $user = Auth::user();
  $jabatanUser = $user ? $user->jabatan : 'SuperAdmin';
@endphp

<div class="sticky-actions d-flex justify-content-between align-items-center">
  <div>
    <span class="text-muted small">
      <i class="fas fa-info-circle mr-1"></i>
      Status periode saat ini: <strong>{{ $statusPeriode }}</strong>
    </span>
  </div>
  <div>
    @if($statusPeriode === 'Sudah' && $jabatanUser !== 'SDM')
      <button type="button" id="btnKirim" class="btn btn-primary btn-rounded-custom shadow-sm px-4">
        <i class="fas fa-paper-plane mr-1"></i> Kirim ke Evaluasi
      </button>
    @elseif($statusPeriode === 'Terkirim' && $jabatanUser !== 'AdminLembaga')
      <button type="button" id="btnSetujui" class="btn btn-success btn-rounded-custom shadow-sm px-4" style="background-color: #00a15d; border-color: #00a15d;">
        <i class="fas fa-check-circle mr-1"></i> Setujui & Finalisasi
      </button>
    @endif
  </div>
</div>

<!-- Modal Edit Baris -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
      <div class="modal-header bg-white border-bottom py-3 px-4">
        <h5 class="modal-title font-weight-bold text-dark">
          <i class="fas fa-edit text-success mr-2"></i>Koreksi Kehadiran Satpam
        </h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body p-4 bg-white">
        <input type="hidden" id="edit-id">
        <input type="hidden" id="edit-lembaga">

        <div class="mb-3 p-2 bg-light rounded border">
          <span class="text-muted small">Personil:</span>
          <div id="edit-nama" class="font-weight-bold text-dark"></div>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark">Hadir Hari (Siang)</label>
          <input type="number" min="0" max="31" step="1" id="edit-hari" class="form-control" placeholder="Masukkan jumlah hari">
          <small class="form-text text-muted">Saat ini: <span id="cur-hari" class="font-weight-bold">0</span> hari</small>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark">Hadir Shift (Malam / Sore)</label>
          <input type="number" min="0" step="1" id="edit-shift" class="form-control" placeholder="Masukkan jumlah shift">
          <small class="form-text text-muted">Saat ini: <span id="cur-shift" class="font-weight-bold">0</span> shift</small>
        </div>

        <div class="form-group mb-3">
          <label class="font-weight-bold text-dark">Hadir Dinihari</label>
          <input type="number" min="0" step="1" id="edit-dini" class="form-control" placeholder="Masukkan jumlah dinihari">
          <small class="form-text text-muted">Saat ini: <span id="cur-dini" class="font-weight-bold">0</span> dinihari</small>
        </div>
      </div>

      <div class="modal-footer border-top py-2 px-4 bg-white">
        <button type="button" class="btn btn-secondary btn-rounded-custom" data-dismiss="modal">Batal</button>
        <button type="button" id="btnSimpanEdit" class="btn btn-success btn-rounded-custom" style="background-color: #00a15d; border-color: #00a15d;">
          <i class="fas fa-save mr-1"></i> Simpan Koreksi
        </button>
      </div>
    </div>
  </div>
</div>

<!-- JS Scripts -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

<script>
  const idL = '{{ $periode->id_kehadiran_lembaga }}';
  const csrfToken = $('meta[name="csrf-token"]').attr('content');

  function fmt(n) { 
    n = Number(n || 0); 
    return n.toLocaleString('id-ID'); 
  }

  $(document).ready(function() {
    // Buka Modal Edit
    $(document).on('click', '.btn-edit', function() {
      const id   = $(this).attr('data-id');
      const idKL = $(this).attr('data-lembaga');
      const nama = $(this).attr('data-nama');

      const hari  = parseInt($('#hari-' + id).text(), 10)  || 0;
      const shift = parseInt($('#shift-' + id).text(), 10) || 0;
      const dini  = parseInt($('#dini-' + id).text(), 10)  || 0;

      $('#edit-id').val(id);
      $('#edit-lembaga').val(idKL);
      $('#edit-nama').text(nama);

      $('#cur-hari').text(hari);
      $('#cur-shift').text(shift);
      $('#cur-dini').text(dini);

      $('#edit-hari').val(hari);
      $('#edit-shift').val(shift);
      $('#edit-dini').val(dini);

      $('#modalEdit').modal('show');
    });

    // Simpan Koreksi Baris
    $('#btnSimpanEdit').on('click', function() {
      const id   = $('#edit-id').val();
      const idKL = $('#edit-lembaga').val();

      let hari  = parseInt($('#edit-hari').val(), 10)  || 0;
      let shift = parseInt($('#edit-shift').val(), 10) || 0;
      let dini  = parseInt($('#edit-dini').val(), 10)  || 0;

      if (hari < 0 || shift < 0 || dini < 0) {
        Swal.fire('Periksa!', 'Nilai kehadiran tidak boleh bernilai negatif.', 'warning');
        return;
      }
      if (hari > 31) {
        Swal.fire('Periksa!', 'Jumlah hari hadir melebihi batas 31 hari.', 'warning');
        return;
      }

      var $btn = $(this);
      $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...');

      $.ajax({
        url: "{{ url('/kehadiran-satpam/update-row') }}",
        type: "POST",
        dataType: "json",
        data: {
          _token: csrfToken,
          id_kehadiran_satpam: id,
          id_kehadiran_lembaga: idKL,
          jumlah_hari: hari,
          jumlah_shift: shift,
          jumlah_dinihari: dini
        },
        success: function(res) {
          $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Koreksi');

          if (!res || !res.status) {
            Swal.fire('Gagal', (res && res.message) ? res.message : 'Update gagal.', 'error');
            return;
          }

          // Update baris tabel
          $('#hari-' + id).text(res.row.jumlah_hari);
          $('#shift-' + id).text(res.row.jumlah_shift);
          $('#dini-' + id).text(res.row.jumlah_dinihari);
          $('#jmltrans-' + id).text(fmt(res.row.jumlah_transport));
          $('#jmlbar-' + id).text(fmt(res.row.jumlah_barokah));
          $('#jmlkons-' + id).text(fmt(res.row.jumlah_konsumsi));
          $('#subtotal-' + id).text(fmt(res.row.subtotal));

          // Update footer & ringkasan badges
          $('#ft-transport, #sumTransport').text(fmt(res.totals.total_transport));
          $('#ft-barokah, #sumBarokah').text(fmt(res.totals.total_barokah));
          $('#ft-dini, #sumDini').text(fmt(res.totals.total_dinihari));
          $('#ft-danru, #sumDanru').text(fmt(res.totals.total_danru));
          $('#ft-grand, #sumGrand').text(fmt(res.totals.grand_total));

          // Sync data attributes on edit button
          const $btnEdit = $('.btn-edit[data-id="' + id + '"]');
          $btnEdit.attr('data-hari', res.row.jumlah_hari)
                  .attr('data-shift', res.row.jumlah_shift)
                  .attr('data-dini', res.row.jumlah_dinihari);

          $('#modalEdit').modal('hide');
          Swal.fire({
            icon: 'success', 
            title: 'Tersimpan', 
            timer: 1400, 
            showConfirmButton: false
          });
        },
        error: function(xhr) {
          $btn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Simpan Koreksi');
          console.error(xhr.responseText);
          Swal.fire('Error', 'Terjadi kesalahan pada server.', 'error');
        }
      });
    });

    // KIRIM (Sudah -> Terkirim)
    $(document).on('click', '#btnKirim', function() {
      const $btn = $(this);
      
      Swal.fire({
        title: 'Kirim Koreksi?',
        text: 'Periode kehadiran Satpam ini akan dikirim untuk proses validasi.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kirim Sekarang',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#00a15d'
      }).then((result) => {
        if (result.value || result.isConfirmed) {
          $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Mengirim...');

          $.ajax({
            url: "{{ url('/kehadiran-satpam/kirim') }}",
            type: "POST",
            dataType: "json",
            data: {
              _token: csrfToken,
              id_kehadiran_lembaga: idL
            }
          })
          .done(function(res) {
            if (res && res.status) {
              Swal.fire({
                icon: 'success',
                title: 'Terkirim',
                text: res.message || 'Periode berhasil dikirim.',
                timer: 1500,
                showConfirmButton: false
              }).then(() => {
                window.location.href = "{{ url('/kehadiran-satpam') }}";
              });
            } else {
              Swal.fire('Gagal', (res && res.message) ? res.message : 'Gagal mengirim periode.', 'error');
            }
          })
          .fail(function() {
            Swal.fire('Error', 'Gagal menghubungi server.', 'error');
          })
          .always(function() {
            $btn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-1"></i> Kirim ke Evaluasi');
          });
        }
      });
    });

    // SETUJUI / FINALISASI (Terkirim -> acc)
    $(document).on('click', '#btnSetujui', function() {
      const $btn = $(this);

      Swal.fire({
        title: 'Setujui Barokah Satpam?',
        text: 'Total barokah akan disimpan permanen ke rekap akhir.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Setujui',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#00a15d'
      }).then((result) => {
        if (result.value || result.isConfirmed) {
          $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menyetujui...');

          $.ajax({
            url: "{{ url('/kehadiran-satpam/validasi') }}",
            type: "POST",
            dataType: "json",
            data: {
              _token: csrfToken,
              id_kehadiran_lembaga: idL
            }
          })
          .done(function(res) {
            if (res && res.status) {
              Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: res.message || 'Periode berhasil disetujui & disimpan.',
                timer: 1500,
                showConfirmButton: false
              }).then(() => {
                window.location.href = "{{ url('/kehadiran-satpam') }}";
              });
            } else {
              Swal.fire('Gagal', (res && res.message) ? res.message : 'Validasi gagal.', 'error');
            }
          })
          .fail(function() {
            Swal.fire('Error', 'Gagal menghubungi server.', 'error');
          })
          .always(function() {
            $btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-1"></i> Setujui & Finalisasi');
          });
        }
      });
    });
  });
</script>
</body>
</html>
