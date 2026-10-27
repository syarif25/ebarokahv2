@extends('pwa.template')

@section('content')
<div id="page">
    <div class="header header-fixed header-logo-center header-auto-show">
        <a href="#" class="header-title">Ebarokah</a>
        <a href="{{ url('/logout') }}" class="header-icon header-icon-2">
            <i class="fa fa-sign-out"></i>
        </a>
        <a href="#" data-menu="menu-main" class="header-icon header-icon-4">
            <i class="fas fa-bars"></i>
        </a>
    </div>

    <div id="footer-bar" class="footer-bar-6">
        <a href="{{ url('/Report/Kehadiran') }}">
            <i class="fa fa-file"></i><span class="active-nav">Kehadiran</span>
        </a>
        <a href="{{ url('/Report') }}" class="circle-nav">
            <i class="fa fa-home"></i><span>Home</span>
        </a>
        <a href="#" onclick="showMaintenanceModal()">
            <i class="fa fa-user"></i><span>Data Diri</span>
        </a>
    </div>

    <div class="page-title page-title-fixed" style="opacity: 1;">
        <h1>Ebarokah</h1>
        <a href="{{ url('/logout') }}" class="page-title-icon shadow-xl bg-theme color-theme" data-menu="menu-main">
            <i class="fa fa-sign-out"></i>
        </a>
    </div>
    <div class="page-title-clear"></div>

    <div class="page-content">
        <div class="card card-style">
            <div class="content mb-0">
                <p class="mb-4 color-highlight font-700 font-10 mb-n1">Selamat Datang Kembali</p>
                <h4 id="namaGelar">-</h4>
                <p class="mb-1">Lembaga Pengabdian saat ini <b><span id="lembagaAktif">0</span> Lembaga</b></p>

                <div class="divider mb-2"></div>

                <div class="d-flex pb-2">
                    <div>
                        <h6 class="font-11 mb-0 text-success">
                            Barokah Bulan Lalu <br>
                            <small id="periodeLalu" class="text-muted">(—)</small>
                        </h6>
                        <h3 class="font-15" id="barokahLalu">Rp. 0</h3>
                    </div>
                    <div class="ms-auto text-end">
                        <h6 class="font-11 mb-0 text-primary">
                            Barokah Bulan Ini <br>
                            <small id="periodeIni" class="text-muted">(—)</small>
                        </h6>
                        <h3 class="font-15" id="barokahIni">Rp. 0</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-style">
            <div class="content">
                <h5 class="mb-2">Pengabdian Saat ini</h5>

                <h6 class="color-highlight mb-2">Struktural</h6>
                <ul class="list-group" id="listStruktural">
                    <li class="list-group-item">Memuat data...</li>
                </ul>

                <div class="divider my-3"></div>

                <h6 class="color-highlight mb-2">Pengajar</h6>
                <ul class="list-group" id="listPengajar">
                    <li class="list-group-item">Memuat data...</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // ===== Rincian =====
  const ulStruk = document.getElementById('listStruktural');
  const ulPeng  = document.getElementById('listPengajar');
  if (ulStruk && ulPeng) {
    fetch('{{ url("Report/dashboard_rincian") }}', {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    })
    .then(async r => {
      const text = await r.text();
      try { return JSON.parse(text); }
      catch (e) { console.error('RAW RESPONSE (rincian):', text); throw e; }
    })
    .then(res => {
      if (!res.ok) throw new Error(res.message || 'Gagal');

      const render = (ul, rows, emptyText, map) => {
        ul.innerHTML = '';
        if (!rows || !rows.length) {
          ul.innerHTML = `<li class="list-group-item">${emptyText}</li>`;
          return;
        }
        rows.forEach(row => {
          const { title, subtitle } = map(row);
          const li = document.createElement('li');
          li.className = 'list-group-item';
          li.innerHTML = `
           <div class="d-flex align-items-center justify-content-between">
              <div>
                <div class="font-600">${title}</div>
                <div class="font-12 color-gray">${subtitle}</div>
              </div>
              <i class="fa fa-check-circle text-success"></i>
            </div>`;
          ul.appendChild(li);
        });
      };

      render(ulStruk, res.struktural, 'Tidak ada penempatan struktural aktif.', r => ({
        title: r.nama_lembaga || '-',
        subtitle: r.jabatan_lembaga || '—'
      }));

      render(ulPeng, res.pengajar, 'Tidak ada pengajar aktif.', r => ({
        title: r.nama_lembaga || '-',
        subtitle: (r.kategori || '—').toUpperCase()
      }));
    })
    .catch(err => {
      console.error('AJAX rincian error:', err);
      if (ulStruk) ulStruk.innerHTML = '<li class="list-group-item">Gagal memuat data.</li>';
      if (ulPeng)  ulPeng.innerHTML  = '<li class="list-group-item">Gagal memuat data.</li>';
    });
  }

  // ===== Header angka =====
  const elNama    = document.getElementById('namaGelar');
  const elAktif   = document.getElementById('lembagaAktif');
  const elIni     = document.getElementById('barokahIni');
  const elLalu    = document.getElementById('barokahLalu');

  if (elNama && elAktif && elIni && elLalu) {
    fetch('{{ url("Report/dashboard_barokah") }}', {
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    })
    .then(async r => {
      const text = await r.text();
      try { return JSON.parse(text); }
      catch (e) { console.error('RAW RESPONSE (header):', text); throw e; }
    })
    .then(res => {
        if (!res.ok) return;
        elNama.textContent  = res.nama_gelar || '-';
        elAktif.textContent = res.lembaga_aktif ?? 0;
        elIni.textContent   = res.total_bulan_ini_formatted  || 'Rp. 0';
        elLalu.textContent  = res.total_bulan_lalu_formatted || 'Rp. 0';
        document.getElementById('barokahIni').textContent = res.total_bulan_ini_formatted || 'Rp. 0';
        document.getElementById('barokahLalu').textContent = res.total_bulan_lalu_formatted || 'Rp. 0';
        const pIni = document.getElementById('periodeIni');
        const pLalu = document.getElementById('periodeLalu');
        if (pIni)  pIni.textContent  = `${res.label_bulan_ini || '-'} ${res.label_tahun_ini || ''}`;
        if (pLalu) pLalu.textContent = `${res.label_bulan_lalu || '-'} ${res.label_tahun_lalu || ''}`;
    })
    .catch(err => console.error('AJAX header error:', err));
  }
});
</script>
@endsection
