<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Validasi Kehadiran Satpam Bulan {{ $periode->bulan }} {{ $periode->tahun }}</title>
  <link rel="icon" href="{{ asset('images/p2s2_fav.png') }}" type="image/x-icon">
  <link rel="shortcut icon" href="{{ asset('images/p2s2_fav.png') }}" type="image/x-icon">

  <!-- Bootstrap 4 & FontAwesome -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.4.0/css/fixedHeader.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
  <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.css') }}">

  <style>
    body { background: #f5f5f5; font-size: 14px; }
    .table thead th { vertical-align: middle !important; text-align: center; }
    .badge-total { font-size: .9rem; margin-right: .5rem; }
    .sticky-actions { 
      position: sticky; 
      bottom: 0; 
      background: #fff; 
      z-index: 100; 
      border-top: 1px solid #e9ecef; 
      padding: .75rem 1.5rem;
      box-shadow: 0 -4px 12px rgba(0,0,0,0.05);
    }
    .cell-right { text-align: right; }
    .cell-center { text-align: center; }
    .font-weight-bold { font-weight: bold; }
  </style>
</head>
<body>

<div class="container-fluid my-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Validasi Kehadiran Satpam Bulan {{ $periode->bulan }} {{ $periode->tahun }}</h4>
    <div>
      <a class="btn btn-warning" target="_blank" href="{{ url('/kehadiran-satpam/' . $periode->id_kehadiran_lembaga . '/cetak') }}">
        <i class="fa fa-print mr-1"></i> Cetak
      </a>
      
      <!-- Potongan -->
      <a class="btn btn-danger mx-1 text-white" target="_blank" href="{{ url('/manajemen-satpam/potongan-satpam') }}">
        <i class="fa fa-cut mr-1"></i> Potongan
      </a>
      
      <!-- Tampilan Kolom -->
      <div class="dropdown d-inline-block mx-1">
          <button class="btn btn-primary dropdown-toggle" type="button" id="btnTampilanKolomSatpam" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fa fa-columns mr-1"></i> Kolom
          </button>
          <div class="dropdown-menu dropdown-menu-right shadow-sm" aria-labelledby="btnTampilanKolomSatpam" style="min-width: 220px;">
              <h6 class="dropdown-header text-uppercase font-weight-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Tampilkan / Sembunyikan</h6>
              <div class="dropdown-item px-3 py-2">
                  <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input toggle-vis-satpam" id="colKeh1" data-columns="2,3,4" checked>
                      <label class="custom-control-label" style="cursor:pointer;" for="colKeh1">Kehadiran 1 (Hari)</label>
                  </div>
              </div>
              <div class="dropdown-item px-3 py-2">
                  <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input toggle-vis-satpam" id="colKeh2" data-columns="5,6,7" checked>
                      <label class="custom-control-label" style="cursor:pointer;" for="colKeh2">Kehadiran 2 (Shift)</label>
                  </div>
              </div>
              <div class="dropdown-item px-3 py-2">
                  <div class="custom-control custom-switch">
                      <input type="checkbox" class="custom-control-input toggle-vis-satpam" id="colKeh3" data-columns="8,9,10" checked>
                      <label class="custom-control-label" style="cursor:pointer;" for="colKeh3">Kehadiran 3 (Dinihari)</label>
                  </div>
              </div>
          </div>
      </div>

      <a class="btn btn-secondary" href="{{ url('/kehadiran-satpam') }}">
        <i class="fa fa-arrow-left mr-1"></i> Kembali
      </a>
    </div>
  </div>

  <!-- Ringkasan di atas -->
  <div class="mb-3">
    <span class="badge badge-secondary badge-total">Total Transport: <span id="sumTransport">{{ number_format($totals['total_transport'], 0, ',', '.') }}</span></span>
    <span class="badge badge-secondary badge-total">Total Barokah: <span id="sumBarokah">{{ number_format($totals['total_barokah'], 0, ',', '.') }}</span></span>
    <span class="badge badge-secondary badge-total">Total Dinihari: <span id="sumDini">{{ number_format($totals['total_dinihari'], 0, ',', '.') }}</span></span>
    <span class="badge badge-secondary badge-total text-white bg-info">Total Danru: <span id="sumDanru">{{ number_format($totals['total_danru'], 0, ',', '.') }}</span></span>
    <span class="badge badge-danger badge-total">Total Potongan: <span id="sumPotongan">{{ number_format($totals['total_potongan'], 0, ',', '.') }}</span></span>
    <span class="badge badge-primary badge-total">Grand Total: <span id="sumGrand">{{ number_format($totals['grand_total'], 0, ',', '.') }}</span></span>
  </div>

  <div class="table-responsive">
    <form id="form">
      <table id="tabel_validasi" class="table table-bordered table-striped" style="width:100%">
        <thead class="thead-light">
          <tr>
            <th rowspan="2" style="width:50px">No</th>
            <th rowspan="2">Nama</th>

            <th colspan="3">Kehadiran 1</th>
            <th colspan="3">Kehadiran 2</th>
            <th colspan="3">Kehadiran 3</th>
            <th rowspan="2" style="width:100px">Danru</th>
            <th rowspan="2" style="width:100px">Potongan</th>

            <th rowspan="2">Total</th>
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
              <!-- hidden fields -->
              <input type="hidden" name="bulan[]" value="{{ $d->bulan }}">
              <input type="hidden" name="tahun[]" value="{{ $d->tahun }}">
              <input type="hidden" name="id_satpam[]" value="{{ $d->id_satpam }}">
              <input type="hidden" name="id_kehadiran_lembaga" value="{{ $d->id_kehadiran_lembaga }}">
              <input type="hidden" name="id_kehadiran_satpam[]" value="{{ $d->id_kehadiran_satpam }}">

              <td class="cell-center">{{ $no++ }}</td>
              <td>{{ $namaLengkap }}</td>

              <!-- Hari -->
              <td id="hari-{{ $d->id_kehadiran_satpam }}" class="cell-center">{{ $calc['jumlah_hari'] }}</td>
              <td class="cell-right">{{ number_format($calc['nominal_transport'], 0, ',', '.') }}</td>
              <td id="jmltrans-{{ $d->id_kehadiran_satpam }}" class="cell-right">{{ number_format($calc['jumlah_transport'], 0, ',', '.') }}</td>

              <!-- Shift -->
              <td id="shift-{{ $d->id_kehadiran_satpam }}" class="cell-center">{{ $calc['jumlah_shift'] }}</td>
              <td class="cell-right">{{ number_format($calc['rank'], 0, ',', '.') }}</td>
              <td id="jmlbar-{{ $d->id_kehadiran_satpam }}" class="cell-right">{{ number_format($calc['jumlah_barokah'], 0, ',', '.') }}</td>

              <!-- Dinihari -->
              <td id="dini-{{ $d->id_kehadiran_satpam }}" class="cell-center">{{ $calc['jumlah_dinihari'] }}</td>
              <td class="cell-right">{{ number_format($calc['konsumsi'], 0, ',', '.') }}</td>
              <td id="jmlkons-{{ $d->id_kehadiran_satpam }}" class="cell-right">{{ number_format($calc['jumlah_konsumsi'], 0, ',', '.') }}</td>

              <!-- Danru -->
              <td class="cell-right">{{ number_format($calc['nominal_danru'], 0, ',', '.') }}</td>

              <!-- Potongan -->
              <td class="cell-right">
                <a href="javascript:void(0)" class="text-danger btn-detail-potongan font-weight-bold" 
                   data-id="{{ $d->id_satpam }}" 
                   data-nama="{{ $namaLengkap }}" 
                   data-periode="{{ $periode->id_kehadiran_lembaga }}" 
                   id="potongan-{{ $d->id_kehadiran_satpam }}">
                   {{ number_format($calc['nominal_potongan'], 0, ',', '.') }}
                </a>
              </td>

              <!-- Total baris -->
              <td id="subtotal-{{ $d->id_kehadiran_satpam }}" class="cell-right font-weight-bold">{{ number_format($calc['diterima'], 0, ',', '.') }}</td>

              <!-- Aksi -->
              <td class="cell-center">
                <button type="button"
                        class="btn btn-sm btn-outline-primary btn-edit"
                        data-id="{{ $d->id_kehadiran_satpam }}"
                        data-lembaga="{{ $d->id_kehadiran_lembaga }}"
                        data-hari="{{ $calc['jumlah_hari'] }}"
                        data-shift="{{ $calc['jumlah_shift'] }}"
                        data-dini="{{ $calc['jumlah_dinihari'] }}"
                        data-nama="{{ $namaLengkap }}">
                  Edit
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>

        <tfoot>
          <tr>
            <th colspan="4">Total Kehadiran</th>
            <th id="ft-transport" class="cell-right">{{ number_format($totals['total_transport'], 0, ',', '.') }}</th>
            <th colspan="2">Total Barokah</th>
            <th id="ft-barokah" class="cell-right">{{ number_format($totals['total_barokah'], 0, ',', '.') }}</th>
            <th colspan="2">Total Dinihari</th>
            <th id="ft-dini" class="cell-right">{{ number_format($totals['total_dinihari'], 0, ',', '.') }}</th>
            <th id="ft-danru" class="cell-right">{{ number_format($totals['total_danru'], 0, ',', '.') }}</th>
            <th id="ft-potongan" class="cell-right text-danger font-weight-bold">{{ number_format($totals['total_potongan'], 0, ',', '.') }}</th>
            <th id="ft-grand" class="cell-right font-weight-bold">{{ number_format($totals['grand_total'], 0, ',', '.') }}</th>
            <th></th>
          </tr>
        </tfoot>
      </table>
    </form>
    
    @php
      $statusPeriode = $periode->status ?? '';
      $user = Auth::user();
      $jabatanUser = $user ? $user->jabatan : 'SuperAdmin';
    @endphp

    <div class="sticky-actions d-flex justify-content-end">
    @if ($statusPeriode === 'Sudah' && $jabatanUser !== 'SDM')
        <button type="button" id="btnKirim" class="btn btn-primary">
          <i class="fa fa-paper-plane mr-1"></i> Kirim
        </button>
    @elseif ($jabatanUser === 'SDM')
        <!-- SDM: tidak tampil tombol apa pun -->
    @elseif ($statusPeriode === 'Terkirim' && $jabatanUser !== 'AdminLembaga')
        <button type="button" id="btnSetujui" class="btn btn-success">
          <i class="fa fa-check mr-1"></i> Setujui
        </button>
    @endif
    </div>
  </div>
</div>

<!-- Modal edit baris -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold text-dark">Koreksi Kehadiran</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
          <input type="hidden" id="edit-id">
          <input type="hidden" id="edit-lembaga">

          <div class="form-group">
              <label>Hadir Hari</label>
              <input type="number" min="0" step="1" id="edit-hari" class="form-control" placeholder="Masukkan jumlah hari">
              <small class="form-text text-muted">Saat ini: <span id="cur-hari">0</span></small>
          </div>

          <div class="form-group">
              <label>Hadir Shift</label>
              <input type="number" min="0" step="1" id="edit-shift" class="form-control" placeholder="Masukkan jumlah shift">
              <small class="form-text text-muted">Saat ini: <span id="cur-shift">0</span></small>
          </div>

          <div class="form-group">
              <label>Hadir Dinihari</label>
              <input type="number" min="0" step="1" id="edit-dini" class="form-control" placeholder="Masukkan jumlah dinihari">
              <small class="form-text text-muted">Saat ini: <span id="cur-dini">0</span></small>
          </div>

          <small class="text-muted d-block">Pastikan angka bulat non-negatif. Batas wajar Hari ≤ 31.</small>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
        <button type="button" id="btnSimpanEdit" class="btn btn-primary">Simpan</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Detail Potongan -->
<div class="modal fade" id="modalPotongan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <form id="formPotongan">
      <div class="modal-header">
        <h5 class="modal-title text-danger font-weight-bold">Detail Potongan</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <h6 id="potonganPegawaiName" class="mb-3 text-primary font-weight-bold"></h6>
        <input type="hidden" name="id_satpam" id="potongan_id_satpam" value="">
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

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<!-- DataTables & Plugins -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/fixedheader/3.4.0/js/dataTables.fixedHeader.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

<script>
  const idL = '{{ $periode->id_kehadiran_lembaga ?? '' }}';
  const csrfToken = $('meta[name="csrf-token"]').attr('content');

  function fmt(n){ n = Number(n||0); return n.toLocaleString('id-ID'); }

$(function(){
  // DataTables
  const dtTblSatpam = $('#tabel_validasi').DataTable({
    paging: false, 
    searching: false, 
    ordering: false, 
    info: false, 
    fixedHeader: true,
    dom: 'Bfrtip'
  });
  
  let potonganOptions = [];
  let potonganIndex = 0;
  
  // Load options once
  $.getJSON('{{ url("/kehadiran-satpam/getPotonganOptions") }}', function(data){
      potonganOptions = data;
  });

  // Format input numeric
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

  // Toggle Column Visibility Logic
  $('.dropdown-menu').on('click', function(e) {
      e.stopPropagation();
  });
  
  $('.toggle-vis-satpam').on('change', function (e) {
      var columnsStr = $(this).attr('data-columns');
      var columns = columnsStr.split(',').map(Number);
      var isVisible = $(this).is(':checked');
      dtTblSatpam.columns(columns).visible(isVisible);
  });

  // BUKA MODAL EDIT
  $(document).on('click', '.btn-edit', function(){
    const id   = $(this).attr('data-id');
    const idKL = $(this).attr('data-lembaga');

    // AMBIL NILAI TERKINI LANGSUNG DARI TABEL (SEL)
    const hari  = parseInt($('#hari-'+id).text(), 10)  || 0;
    const shift = parseInt($('#shift-'+id).text(), 10) || 0;
    const dini  = parseInt($('#dini-'+id).text(), 10)  || 0;

    $('#edit-id').val(id);
    $('#edit-lembaga').val(idKL);

    $('#cur-hari').text(hari);
    $('#cur-shift').text(shift);
    $('#cur-dini').text(dini);

    $('#edit-hari').val('').attr('placeholder', `Saat ini: ${hari}`);
    $('#edit-shift').val('').attr('placeholder', `Saat ini: ${shift}`);
    $('#edit-dini').val('').attr('placeholder', `Saat ini: ${dini}`);

    $('#modalEdit').modal('show');
  });

  $('#modalEdit').on('hidden.bs.modal', function(){
    $('#edit-id,#edit-lembaga').val('');
    $('#edit-hari,#edit-shift,#edit-dini').val('').attr('placeholder','');
    $('#cur-hari,#cur-shift,#cur-dini').text('0');
  });

  // SIMPAN KOREKSI
  $('#btnSimpanEdit').on('click', function(){
    const id   = $('#edit-id').val();
    const idKL = $('#edit-lembaga').val();

    let hari  = $('#edit-hari').val()  === '' ? parseInt($('#cur-hari').text(),10)  : parseInt($('#edit-hari').val(),10);
    let shift = $('#edit-shift').val() === '' ? parseInt($('#cur-shift').text(),10) : parseInt($('#edit-shift').val(),10);
    let dini  = $('#edit-dini').val()  === '' ? parseInt($('#cur-dini').text(),10)  : parseInt($('#edit-dini').val(),10);

    if(hari<0||shift<0||dini<0){ Swal.fire('Periksa!', 'Nilai tidak boleh negatif.', 'warning'); return; }
    if(hari>60){ Swal.fire('Periksa!', 'Jumlah hari tidak wajar (>60).', 'warning'); return; }

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
      success: function(res){
        if(!res || !res.status){
          Swal.fire('Gagal', (res && res.message) ? res.message : 'Update gagal.', 'error');
          return;
        }

        // update sel baris
        $('#hari-'+id).text(res.row.jumlah_hari);
        $('#shift-'+id).text(res.row.jumlah_shift);
        $('#dini-'+id).text(res.row.jumlah_dinihari);
        $('#jmltrans-'+id).text(fmt(res.row.jumlah_transport));
        $('#jmlbar-'+id).text(fmt(res.row.jumlah_barokah));
        $('#jmlkons-'+id).text(fmt(res.row.jumlah_konsumsi));
        $('#potongan-'+id).text(fmt(res.row.potongan));
        $('#subtotal-'+id).text(fmt(res.row.subtotal));

        // update footer & ringkasan
        $('#ft-transport, #sumTransport').text(fmt(res.totals.total_transport));
        $('#ft-barokah, #sumBarokah').text(fmt(res.totals.total_barokah));
        $('#ft-dini, #sumDini').text(fmt(res.totals.total_dinihari));
        $('#ft-danru, #sumDanru').text(fmt(res.totals.total_danru));
        $('#ft-potongan, #sumPotongan').text(fmt(res.totals.total_potongan));
        $('#ft-grand, #sumGrand').text(fmt(res.totals.grand_total));

        const $btn = $('.btn-edit[data-id="'+id+'"]');
        $btn.attr('data-hari',  res.row.jumlah_hari)
            .attr('data-shift', res.row.jumlah_shift)
            .attr('data-dini',  res.row.jumlah_dinihari);

        $('#modalEdit').modal('hide');
        Swal.fire({icon:'success', title:'Tersimpan', timer:1400, showConfirmButton:false});
      },
      error: function(xhr){
        console.error(xhr.responseText);
        Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
      }
    });
  });

  // MODAL DETAIL POTONGAN
  $(document).on('click', '.btn-detail-potongan', function() {
      let id = $(this).data('id');
      let nama = $(this).data('nama');
      
      $('#potonganPegawaiName').text(nama);
      $('#potongan_id_satpam').val(id);
      $('#potonganBody').html('<tr><td colspan="4" class="text-center text-muted">Memuat...</td></tr>');
      $("#modalPotongan").modal("show");
      
      potonganIndex = 0;

      $.getJSON("{{ url('/kehadiran-satpam/detailPotongan') }}/" + id, function(data){
          let rows = "";
          if(!data || data.length === 0){
              rows = "";
          } else {
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
                          <input type="hidden" name="potongan[${potonganIndex}][id_potongan_satpam]" value="${item.id_potongan_satpam}">
                          <select class="form-control form-control-sm select2-potongan" name="potongan[${potonganIndex}][jenis_potongan]" required>
                              ${optionsHtml}
                          </select>
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
          $('#potonganBody').html(rows);
          
          $('.select2-potongan').select2({
              theme: 'bootstrap4',
              dropdownParent: $('#modalPotongan')
          });

      }).fail(function(){
          $('#potonganBody').html('<tr><td colspan="4" class="text-center text-danger">Gagal mengambil data potongan.</td></tr>');
      });
  });

  $('#btnTambahPotongan').on('click', function(){
      let optionsHtml = '<option value="">Pilih Potongan...</option>';
      potonganOptions.forEach(opt => {
          optionsHtml += `<option value="${opt.id_potongan}">${opt.nama_potongan}</option>`;
      });
      
      let newRow = `
          <tr>
          <td>
              <input type="hidden" name="potongan[${potonganIndex}][id_potongan_satpam]" value="">
              <select class="form-control form-control-sm select2-potongan" name="potongan[${potonganIndex}][jenis_potongan]" required>
                  ${optionsHtml}
              </select>
          </td>
          <td>
              <input type="text" class="form-control form-control-sm text-right numeric" name="potongan[${potonganIndex}][nominal_potongan]" value="" placeholder="Nominal" required>
          </td>
          <td>
              <input type="date" class="form-control form-control-sm text-center" name="potongan[${potonganIndex}][min_periode_potongan]" value="">
          </td>
          <td>
              <input type="date" class="form-control form-control-sm text-center" name="potongan[${potonganIndex}][max_periode_potongan]" value="">
          </td>
          </tr>
      `;
      $('#potonganBody').append(newRow);
      
      $('.select2-potongan').select2({
          theme: 'bootstrap4',
          dropdownParent: $('#modalPotongan')
      });
      potonganIndex++;
  });

  // SIMPAN POTONGAN FORM
  $('#formPotongan').on('submit', function(e){
      e.preventDefault();
      var formData = $(this).serialize();
      formData += '&_token=' + csrfToken;

      $('#btnSimpanPotongan').prop('disabled', true).text('Menyimpan...');

      $.ajax({
          url: "{{ url('/kehadiran-satpam/savePotongan') }}",
          type: "POST",
          dataType: "json",
          data: formData,
          success: function(res){
              $('#btnSimpanPotongan').prop('disabled', false).text('Simpan Potongan');
              if(res && res.status){
                  $('#modalPotongan').modal('hide');
                  Swal.fire({icon:'success', title:'Tersimpan', text:res.message || 'Potongan berhasil disimpan.', timer:1400, showConfirmButton:false})
                  .then(() => {
                      location.reload();
                  });
              } else {
                  Swal.fire('Gagal', res.message || 'Gagal menyimpan.', 'error');
              }
          },
          error: function(){
              $('#btnSimpanPotongan').prop('disabled', false).text('Simpan Potongan');
              Swal.fire('Error', 'Gagal menghubungi server.', 'error');
          }
      });
  });

  // KIRIM (ubah status: Sudah -> Terkirim)
  $(document).on('click', '#btnKirim', function(){
      const $btn = $(this);
      if(!idL){ Swal.fire('Error','ID periode tidak ditemukan.','error'); return; }
  
      $btn.prop('disabled', true).text('Mengirim...');
      $.ajax({
          url: "{{ url('/kehadiran-satpam/kirim') }}",
          type: "POST",
          dataType: "json",
          data: { _token: csrfToken, id_kehadiran_lembaga: idL }
      })
      .done(function(res){
          if(res && res.status){
            Swal.fire({icon:'success', title:'Terkirim', text: res.message || 'Periode berhasil dikirim.', timer:1400, showConfirmButton:false})
            .then(()=>{ 
                window.location.href = "{{ url('/kehadiran-satpam') }}";
            });
          } else {
            Swal.fire('Gagal', (res && res.message) ? res.message : 'Gagal mengirim periode.','error');
          }
      })
      .fail(function(xhr){
          console.error(xhr.responseText);
          Swal.fire('Error','Gagal menghubungi server.','error');
      })
      .always(function(){
          $btn.prop('disabled', false).html('<i class="fa fa-paper-plane mr-1"></i> Kirim');
      });
  });

  // SETUJUI (finalisasi: tulis total_barokah + update status acc)
  $(document).on('click', '#btnSetujui', function(){
      const $btn = $(this);
      if(!idL){ Swal.fire('Error','ID periode tidak ditemukan.','error'); return; }
  
      $btn.prop('disabled', true).text('Menyetujui...');
      $.ajax({
          url: "{{ url('/kehadiran-satpam/validasi') }}",
          type: "POST",
          dataType: "json",
          data: { _token: csrfToken, id_kehadiran_lembaga: idL }
      })
      .done(function(res){
          if(res && res.status){
            Swal.fire({icon:'success', title:'Berhasil!', text: res.message || 'Periode disetujui & disimpan.', timer:1500, showConfirmButton:false})
            .then(()=>{ 
                window.location.href = "{{ url('/kehadiran-satpam') }}"; 
            });
          } else {
            Swal.fire('Gagal', (res && res.message) ? res.message : 'Validasi gagal.','error');
          }
      })
      .fail(function(xhr){
          console.error(xhr.responseText);
          Swal.fire('Error','Gagal menghubungi server.','error');
      })
      .always(function(){
          $btn.prop('disabled', false).html('<i class="fa fa-check mr-1"></i> Setujui');
      });
  });
});
</script>
</body>
</html>
