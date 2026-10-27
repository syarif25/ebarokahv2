@extends('pwa.template')

@section('content')
<div id="page">
    <div class="header header-fixed header-logo-center header-auto-show">
        <a href="{{ url('/Report/Kehadiran') }}" class="header-title">Ebarokah</a>
        <a href="{{ url('/Report/Kehadiran') }}" data-back-button class="header-icon header-icon-1">
            <i class="fas fa-chevron-left"></i>
        </a>
        <a href="{{ url('/logout') }}" class="header-icon header-icon-2">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>

    <div id="footer-bar" class="footer-bar-6">
        <a href="{{ url('/Report/Kehadiran') }}" class="active-nav">
            <i class="fa fa-file"></i><span>Kehadiran</span>
        </a>
        <a href="{{ url('/Report') }}" class="circle-nav">
            <i class="fa fa-home"></i><span>Home</span>
        </a>
        <a href="#" onclick="showMaintenanceModal()">
            <i class="fa fa-user"></i><span>Data Diri</span>
        </a>
    </div>

    <div class="page-content">
        <div class="page-title d-flex align-items-center">
            <a href="{{ url('/Report/Kehadiran') }}" 
                class="icon icon-xs bg-gray-light rounded-circle me-2 d-flex align-items-center justify-content-center"
                style="width:32px;height:32px;">
                <i class="fa fa-arrow-left color-theme"></i>
            </a>
            <h1 class="m-0 font-20">Barokah — {{ ucfirst($kategori) }}</h1>
        </div>

        <div class="card card-style">
            <div class="content">
                <div class="d-flex align-items-center mb-2">
                    <select id="selBulan" class="form-select form-select-sm" style="max-width:65%">
                        <option>Memuat…</option>
                    </select>
                    <span id="badgeTotal" class="badge bg-primary ms-auto">Rp. 0</span>
                </div>

                <div id="listRinci" class="list-compact">
                    <div class="empty">—</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="modal-rincian" class="menu menu-box-modal rounded-m"
        data-menu-height="420" data-menu-width="360">
        <div class="menu-title">
            <p class="color-highlight mb-0" id="modalSub">Rincian komponen</p>    
            <h1 class="font-19" id="modalTitle">Rincian Barokah</h1>    
            <a href="#" class="close-menu"><i class="fa fa-times-circle"></i></a>
        </div>

        <div class="divider divider-margins mt-2 mb-0"></div>

        <div class="content mt-2 mb-2" id="modalBody">
            <div class="empty">Memuat…</div>
        </div>

        <div class="divider divider-margins mt-0 mb-2"></div>

        <div class="content mt-0"></div>
    </div>

    <!-- Trigger tersembunyi -->
    <a id="openRincianHook" href="#" data-menu="modal-rincian" class="d-none"></a>

    <style>
        .list-compact{border:1px solid #eee;border-radius:16px;overflow:hidden;background:#fff}

        .item-row{
            display:flex;align-items:center;justify-content:space-between;
            padding:.8rem .9rem;border-bottom:1px solid #f5f5f5;
        }
        .item-row:last-child{border-bottom:0}
        .item-row:active{background:#fafafa}

        .item-left{min-width:0}
        .item-left .title{
            font-weight:700;font-size:15px;line-height:1.2;margin-bottom:2px;
            white-space:nowrap;overflow:hidden;text-overflow:ellipsis;
        }
        .item-left .meta{font-size:11px;opacity:.7;display:flex;gap:6px;align-items:center}

        .pill{padding:2px 8px;border-radius:999px;font-weight:600;font-size:11px}
        .pill--pengajar{background:#eef3ff;color:#2f5bff}
        .pill--struktural{background:#e7f7ee;color:#118a4e}
        .pill--satpam{background:#fff4e5;color:#c97a00}

        .item-right{display:flex;align-items:center;gap:8px}
        .amount{font-weight:700}
        .chev{opacity:.35}

        .empty{padding:.6rem .75rem;opacity:.7;background:#fff}
    </style>

    <script>
        // ========= Helpers =========
        function fmt(n){
            if (n === null || n === undefined || n === '') return 'Rp. 0';
            if (typeof n === 'number') return 'Rp. ' + Math.round(n).toLocaleString('id-ID');
            const cleaned = String(n).replace(/[^\d-]/g, '');     // buang "Rp", titik, koma, spasi, dll
            const val = cleaned ? parseInt(cleaned, 10) : 0;      // aman untuk "867.000" -> 867000
            return 'Rp. ' + val.toLocaleString('id-ID');
        }

        // ========= Elemen & state =========
        const K        = '{{ $kategori }}';
        const selBulan = document.getElementById('selBulan');
        const badge    = document.getElementById('badgeTotal');
        const list     = document.getElementById('listRinci');

        // ========= Fetch ringkasan 12 bulan terakhir =========
        function loadRingkasan(){
            fetch('{{ url("Report/barokah_data") }}?k='+K+'&range=12', {
                headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},
                credentials:'same-origin'
            })
            .then(r=>r.json())
            .then(res=>{
                const s = res.series || [];
                selBulan.innerHTML='';
                s.forEach(r=>{
                    const v = `${r.y}-${(''+r.m).padStart(2,'0')}`;
                    selBulan.insertAdjacentHTML('beforeend',
                    `<option value="${v}" data-total="${r.total}">${r.label_bulan} ${r.label_tahun}</option>`);
                });

                if(!s.length){
                    badge.textContent = fmt(0);
                    list.innerHTML = '<div class="empty">Belum ada data.</div>';
                    return;
                }

                selBulan.selectedIndex = s.length-1;
                badge.textContent = fmt(s.at(-1).total);
                const yy = s.at(-1).y, mm = s.at(-1).m;
                loadDetail(yy, mm);
            });
        }

        // ========= Render detail per bulan (list lembaga) =========
        function loadDetail(y,m){
            list.innerHTML='<div class="empty">Memuat…</div>';
            fetch(`{{ url("Report/barokah_detail") }}?k=${K}&y=${y}&m=${m}`, {
                headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},
                credentials:'same-origin'
            })
            .then(r=>r.json())
            .then(res=>{
                const rows = res.rows || [];
                list.innerHTML='';
                if(!rows.length){
                    list.innerHTML='<div class="empty">Tidak ada data.</div>';
                    return;
                }

                rows.forEach(r=>{
                    const jenisRaw = (r.jenis || '').trim().toLowerCase();
                    const label    = r.jenis || '-';
                    const nominal  = fmt(r.nominal);
                    const pillCls  = jenisRaw === 'pengajar'   ? 'pill pill--pengajar'
                                    : jenisRaw === 'struktural' ? 'pill pill--struktural'
                                    : jenisRaw === 'satpam'     ? 'pill pill--satpam' : 'pill';

                    list.insertAdjacentHTML('beforeend', `
                        <div class="item-row open-breakdown"
                            data-y="${y}" data-m="${m}"
                            data-jenis="${jenisRaw}"
                            data-entid="${r.ent_id || ''}"
                            role="button" aria-label="Lihat rincian ${r.lembaga || '-'}">
                        <div class="item-left">
                            <div class="title">${r.lembaga || '-'}</div>
                            <div class="meta"><span class="${pillCls}">${label}</span></div>
                        </div>
                        <div class="item-right">
                            <div class="amount">${nominal}</div>
                            <i class="fa fa-chevron-right chev"></i>
                        </div>
                        </div>
                    `);
                });
            });
        }

        // ========= Dropdown ganti bulan =========
        selBulan.addEventListener('change', e=>{
            const [yy,mm] = e.target.value.split('-');
            const totalOpt = e.target.selectedOptions[0]?.dataset.total;
            badge.textContent = fmt(totalOpt);
            loadDetail(+yy, +mm);
        });

        // ========= Delegasi klik list → buka modal breakdown =========
        list.addEventListener('click', (e)=>{
            const row = e.target.closest('.open-breakdown');
            if (!row) return;
            openBreakdown(
                Number(row.dataset.y),
                Number(row.dataset.m),
                row.dataset.jenis || K,
                row.dataset.entid || ''
            );
        });

        // ========= Modal breakdown =========
        function openBreakdown(y, m, jenis, lid){
            const J = (jenis || K || '').toLowerCase();
            const LID = lid || '';
            const body = document.getElementById('modalBody');
            body.innerHTML = '<div class="empty">Memuat…</div>';

            if (typeof window.menu === 'function') { window.menu('modal-rincian','show'); }
            else { document.getElementById('openRincianHook')?.click(); }

            function rowLine(title, value, subtitle){
                return `
                <div class="row-line" style="display:flex;justify-content:space-between;align-items:center;padding:.5rem .25rem;border-bottom:1px dashed #e2e8f0;">
                    <div>
                    <div class="ttl" style="font-weight:500;color:#1e293b;">${title}</div>
                    ${subtitle ? `<div class="sub" style="font-size:11px;color:#64748b;margin-top:2px">${subtitle}</div>` : ``}
                    </div>
                    <div class="font-600" style="color:#0f172a;">${value}</div>
                </div>`;
            }

            fetch(`{{ url('Report/barokah_breakdown') }}?k=${encodeURIComponent(J)}&y=${y}&m=${m}&lid=${encodeURIComponent(LID)}`, {
                headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},
                credentials:'same-origin'
            })
            .then(r => r.json())
            .then(res => {
                if (!res.ok) { body.innerHTML = '<div class="empty">Gagal memuat.</div>'; return; }

                document.getElementById('modalSub').textContent   = `Kategori: ${(J || K).toUpperCase()}`;
                document.getElementById('modalTitle').textContent = `Rincian Barokah`;

                const d = res.data || {};
                const f = d.f || {};

                if (J === 'pengajar') {
                    const potItems = Array.isArray(d.potongan_items) ? d.potongan_items : [];
                    const potDetailHTML = potItems.length
                    ? `<div class="pt-1 pb-2" style="margin:.25rem .25rem 0 .25rem">
                        ${potItems.map(it => `
                            <div style="font-size:12px;color:#b91c1c;font-weight:500;">~ ${it.nama} : ${fmt(it.nominal).replace('Rp. ','')}</div>
                        `).join('')}
                        </div>`
                    : '';

                    body.innerHTML = `
                    <div class="list-compact-native">
                        ${rowLine('Mengajar', fmt(f.mengajar), d.sks ? `${d.sks} SKS × ${fmt(f.per_sks).replace('Rp. ','')}` : '')}
                        ${rowLine('GTY/DTY', fmt(f.gty_dty))}
                        ${rowLine('Jafung', fmt(f.jafung))}
                        ${rowLine('Kehadiran', fmt(f.kehadiran), (d.jhadir > 0 ? 'Hadir ' + d.jhadir + ' Hari' : ''))}
                        ${rowLine('Kehadiran 15.000', fmt(f.nhadir15), 'Hadir ' + (d.jhadir15||0) + ' × 15.000')}
                        ${rowLine('Kehadiran 10.000', fmt(f.nhadir10), 'Hadir ' + (d.jhadir10||0) + ' × 10.000')}
                        ${rowLine('Wali Kelas', fmt(f.walkes))}
                        ${rowLine('Kehormatan', fmt(f.kehormatan))}
                        ${rowLine('Tambahan', fmt(f.tambahan))}
                        ${rowLine('Piket', fmt(f.piket))}
                        ${rowLine('Tunjangan Anak', fmt(f.tun_anak))}
                        ${rowLine('Tunjangan Keluarga', fmt(f.tunkel))}
                        ${rowLine('Potongan', fmt(f.potongan))}
                        ${potDetailHTML}
                    </div>
                    <div class="divider my-2"></div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="font-700">Total (Rp)</div><div class="font-700">${fmt(f.total)}</div>
                    </div>`;

                } else if (J === 'satpam') {
                    const subK1 = (d.j_hari > 0)
                        ? `${d.j_hari} hari × ${fmt(d.k1_unit).replace('Rp. ','')}` : '';
                    const subK2 = (d.j_shift > 0)
                        ? `${d.j_shift} shift × ${fmt(d.k2_unit).replace('Rp. ','')}` : '';
                    const subK3 = (d.j_dinihari > 0)
                        ? `${d.j_dinihari} kali × ${fmt(d.k3_unit).replace('Rp. ','')}` : '';

                    body.innerHTML = `
                        <div class="list-compact-native">
                        ${rowLine('Kehadiran Hari Kerja', fmt(f.k1_total), subK1)}
                        ${rowLine('Kehadiran Shift', fmt(f.k2_total), subK2)}
                        ${rowLine('Kehadiran Dini Hari', fmt(f.k3_total), subK3)}
                        </div>
                        <div class="divider my-2"></div>
                        <div class="d-flex justify-content-between align-items-center">
                        <div class="font-700">Total (Rp)</div><div class="font-700">${fmt(f.total)}</div>
                        </div>`;
                } else if (J === 'struktural') {
                    const potItems = Array.isArray(d.potongan_items) ? d.potongan_items : [];
                    const potDetailHTML = potItems.length
                        ? `<div class="pt-1 pb-2" style="margin:.25rem .25rem 0 .25rem">
                            ${potItems.map(it => {
                            const name = String(it.nama || '').replace(/[&<>"']/g, s=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[s]));
                            return `<div style="font-size:12px;color:#b91c1c;font-weight:500;">~ ${name} : ${fmt(it.nominal).replace('Rp. ','')}</div>`;
                            }).join('')}
                        </div>`
                        : '';

                    const hadirSub = (typeof d.kehadiran === 'number' && d.kehadiran > 0)
                        ? `Hadir Normal <strong>${d.kehadiran}</strong> Kali` : '';

                    body.innerHTML = `
                        <div class="list-compact-native">
                        ${rowLine('Barokah Pokok', fmt(f.barokah_pokok))}
                        ${rowLine('Kehadiran', fmt(f.kehadiran_nominal), hadirSub)}
                        ${rowLine('Tunjangan Keluarga', fmt(f.tunkel))}
                        ${rowLine('Tunjangan Anak', fmt(f.tun_anak))}
                        ${rowLine('Tunjangan Masa Pengabdian', fmt(f.tmp))}
                        ${rowLine('Tunjangan Beban Kerja', fmt(f.tbk))}
                        ${rowLine('Tunjangan Kehormatan', fmt(f.kehormatan))}
                        ${rowLine('Potongan', fmt(f.potongan))}
                        ${potDetailHTML}
                        </div>
                        <div class="divider my-2"></div>
                        <div class="d-flex justify-content-between align-items-center">
                        <div class="font-700">Total (Rp)</div><div class="font-700">${fmt(f.total)}</div>
                        </div>`;
                }
            })
            .catch(() => { body.innerHTML = '<div class="empty">Gagal memuat.</div>'; });
        }

        // Init
        document.addEventListener('DOMContentLoaded', loadRingkasan);
    </script>
</div>
@endsection
