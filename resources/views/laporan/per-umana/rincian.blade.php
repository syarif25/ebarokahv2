@extends('layouts.app')

@section('title', 'Rincian Barokah Perumana - ' . $umanaInfo['nama_lengkap'])

@section('page_title')
<span class="text-danger font-weight-bold">Laporan</span> / <a href="{{ url('/laporan/per-umana') }}" class="text-muted">Per Umana</a> / Rincian
@endsection

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .profile-card {
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .profile-table td {
            padding: 6px 10px;
            font-size: 13.5px;
            color: #334155;
        }

        /* Nav Tabs Style V2 */
        .nav-tabs {
            border-bottom: 2px solid #e2e8f0;
        }
        .nav-tabs .nav-link {
            border: none;
            color: #64748b;
            font-weight: 600;
            font-size: 13px;
            padding: 10px 18px;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s;
        }
        .nav-tabs .nav-link:hover {
            color: #00a15d;
        }
        .nav-tabs .nav-link.active {
            color: #00a15d;
            background-color: transparent;
            border-bottom: 3px solid #00a15d;
        }

        .table-custom thead th {
            background-color: #f8fafc !important;
            color: #1e293b !important;
            font-weight: 700 !important;
            font-size: 13px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            vertical-align: middle;
            padding: 12px 14px;
        }
        .table-custom tbody td {
            font-size: 13px !important;
            color: #334155 !important;
            vertical-align: middle;
            padding: 10px 14px;
        }

        .btn-detail-breakdown {
            border: 1px solid #0284c7;
            color: #0284c7;
            background: transparent;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            padding: 4px 14px;
            transition: all 0.2s;
        }
        .btn-detail-breakdown:hover {
            background-color: #0284c7;
            color: #fff;
        }

        /* Modal Breakdown List Design */
        .list-compact-line {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 6px;
            border-bottom: 1px dashed #e2e8f0;
            font-size: 13px;
        }
        .list-compact-line:last-child {
            border-bottom: none;
        }
    </style>
@endpush

@section('content')
<!-- Profil Umana -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card profile-card">
            <div class="card-header border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title font-weight-bold mb-0">Profil Umana</h5>
                </div>
                <div>
                    <a href="{{ url('/laporan/per-umana') }}" class="btn btn-light btn-sm rounded-pill px-3 shadow-sm font-weight-bold">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body px-4 py-3">
                <div class="table-responsive">
                    <table class="table table-borderless table-sm mb-0 profile-table">
                        <tr>
                            <td style="width: 180px;"><strong>Nama Lengkap</strong></td>
                            <td style="width: 10px;">:</td>
                            <td><strong class="text-dark">{{ $umanaInfo['nama_lengkap'] }}</strong> (NIK: {{ $umanaInfo['nik'] }})</td>
                        </tr>
                        <tr>
                            <td><strong>Struktural Aktif</strong></td>
                            <td>:</td>
                            <td>
                                @if($umanaInfo['struktural'] !== 'Tidak Aktif')
                                    <span class="badge bg-success-light text-success fw-bold">{{ $umanaInfo['struktural'] }}</span>
                                @else
                                    <span class="badge bg-light text-muted border">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Pengajar Aktif</strong></td>
                            <td>:</td>
                            <td>
                                @if($umanaInfo['pengajar'] !== 'Tidak Aktif')
                                    <span class="badge bg-info-light text-info fw-bold">{{ $umanaInfo['pengajar'] }}</span>
                                @else
                                    <span class="badge bg-light text-muted border">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Satpam Aktif</strong></td>
                            <td>:</td>
                            <td>
                                @if($umanaInfo['satpam'] === 'Aktif')
                                    <span class="badge bg-warning-light text-warning fw-bold">Aktif</span>
                                @else
                                    <span class="badge bg-light text-muted border">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Riwayat Barokah Tabs -->
<div class="row">
    <div class="col-12">
        <div class="card profile-card">
            <div class="card-header border-bottom py-3 px-4">
                <h5 class="card-title font-weight-bold mb-0">Riwayat Barokah</h5>
            </div>
            <div class="card-body p-4">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs mb-4" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab-rekapitulasi">
                            <i class="fas fa-table me-2"></i> Rekapitulasi Total
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-struktural">
                            <i class="fas fa-user-tie me-2"></i> Struktural
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-pengajar">
                            <i class="fas fa-chalkboard-teacher me-2"></i> Pengajar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#tab-satpam">
                            <i class="fas fa-shield-alt me-2"></i> Satpam
                        </a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <!-- Tab 1: Rekapitulasi Gabungan -->
                    <div class="tab-pane fade show active" id="tab-rekapitulasi" role="tabpanel">
                        <div class="table-responsive">
                            <table id="tabel_rekapitulasi" class="table table-bordered table-hover table-custom w-100">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;" class="text-center">No</th>
                                        <th>Bulan Tahun</th>
                                        <th>Lembaga</th>
                                        <th class="text-end">Total Struktural</th>
                                        <th class="text-end">Total Pengajar</th>
                                        <th class="text-end">Total Satpam</th>
                                        <th class="text-end" style="background-color: #f1f5f9;"><strong>Grand Total</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rekap as $r)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td><strong>{{ $r->bulan }} {{ $r->tahun }}</strong></td>
                                        <td><small class="text-muted">{{ $r->daftar_lembaga ?: '-' }}</small></td>
                                        <td class="text-end">Rp {{ number_format((float)$r->total_struktural, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format((float)$r->total_pengajar, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format((float)$r->total_satpam, 0, ',', '.') }}</td>
                                        <td class="text-end font-weight-bold" style="background-color: #f8fafc; color: #16a34a;">
                                            <strong>Rp {{ number_format((float)$r->grand_total, 0, ',', '.') }}</strong>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat barokah yang tercatat</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 2: Struktural -->
                    <div class="tab-pane fade" id="tab-struktural" role="tabpanel">
                        <div class="table-responsive">
                            <table id="tabel_struktural" class="table table-bordered table-hover table-custom w-100">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;" class="text-center">No</th>
                                        <th>Bulan Tahun</th>
                                        <th>Lembaga</th>
                                        <th class="text-end">Nominal Barokah</th>
                                        <th style="width: 100px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($struktural as $s)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td><strong>{{ $s->bulan }} {{ $s->tahun }}</strong></td>
                                        <td>{{ $s->nama_lembaga }}</td>
                                        <td class="text-end font-weight-bold" style="color: #16a34a;">
                                            Rp {{ number_format((float)$s->diterima, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-detail-breakdown shadow-sm" onclick="openBreakdown('{{ $s->tahun }}', '{{ $s->bulan }}', 'struktural', '{{ $s->id_lembaga }}')">
                                                <i class="fas fa-search-plus me-1"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Data tidak ditemukan / Tidak aktif di Struktural</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 3: Pengajar -->
                    <div class="tab-pane fade" id="tab-pengajar" role="tabpanel">
                        <div class="table-responsive">
                            <table id="tabel_pengajar" class="table table-bordered table-hover table-custom w-100">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;" class="text-center">No</th>
                                        <th>Bulan Tahun</th>
                                        <th>Lembaga</th>
                                        <th class="text-end">Nominal Barokah</th>
                                        <th style="width: 100px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pengajar as $p)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td><strong>{{ $p->bulan }} {{ $p->tahun }}</strong></td>
                                        <td>{{ $p->nama_lembaga }}</td>
                                        <td class="text-end font-weight-bold" style="color: #16a34a;">
                                            Rp {{ number_format((float)$p->diterima, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-detail-breakdown shadow-sm" onclick="openBreakdown('{{ $p->tahun }}', '{{ $p->bulan }}', 'pengajar', '{{ $p->id_lembaga }}')">
                                                <i class="fas fa-search-plus me-1"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Data tidak ditemukan / Tidak aktif sebagai Pengajar</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 4: Satpam -->
                    <div class="tab-pane fade" id="tab-satpam" role="tabpanel">
                        <div class="table-responsive">
                            <table id="tabel_satpam" class="table table-bordered table-hover table-custom w-100">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;" class="text-center">No</th>
                                        <th>Bulan Tahun</th>
                                        <th class="text-end">Nominal Barokah</th>
                                        <th style="width: 100px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($satpam as $st)
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td><strong>{{ $st->bulan }} {{ $st->tahun }}</strong></td>
                                        <td class="text-end font-weight-bold" style="color: #16a34a;">
                                            Rp {{ number_format((float)$st->diterima, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-detail-breakdown shadow-sm" onclick="openBreakdown('{{ $st->tahun }}', '{{ $st->bulan }}', 'satpam', '')">
                                                <i class="fas fa-search-plus me-1"></i> Detail
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Data tidak ditemukan / Tidak aktif sebagai Satpam</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> <!-- End Tab Content -->
            </div>
        </div>
    </div>
</div>

<!-- Modal Breakdown Detail -->
<div class="modal fade" id="modal-rincian" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);">
            <div class="modal-header border-bottom py-3 px-4">
                <div>
                    <h5 class="modal-title font-weight-bold mb-0">Rincian Komponen Barokah</h5>
                    <small class="text-muted" id="modalSub" style="font-size: 12px;"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="modalBody">
                <div class="text-center py-3"><i class="fas fa-spinner fa-spin me-2"></i> Memuat data...</div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="hidden_nik" value="{{ $nik }}">
@endsection

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
    $(document).ready(function() {
        $('#tabel_rekapitulasi').DataTable({
            paging: true,
            searching: true,
            info: true,
            ordering: false,
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ periode",
                paginate: { next: '<i class="fas fa-chevron-right"></i>', previous: '<i class="fas fa-chevron-left"></i>' }
            }
        });
        $('#tabel_struktural').DataTable({
            paging: true,
            searching: true,
            info: true,
            ordering: false,
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: { next: '<i class="fas fa-chevron-right"></i>', previous: '<i class="fas fa-chevron-left"></i>' }
            }
        });
        $('#tabel_pengajar').DataTable({
            paging: true,
            searching: true,
            info: true,
            ordering: false,
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: { next: '<i class="fas fa-chevron-right"></i>', previous: '<i class="fas fa-chevron-left"></i>' }
            }
        });
        $('#tabel_satpam').DataTable({
            paging: true,
            searching: true,
            info: true,
            ordering: false,
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: { next: '<i class="fas fa-chevron-right"></i>', previous: '<i class="fas fa-chevron-left"></i>' }
            }
        });
    });

    function fmt(n) {
        if (n === null || n === undefined || n === '') return 'Rp 0';
        var val = typeof n === 'number' ? n : parseFloat(String(n).replace(/[^\d.-]/g, ''));
        return 'Rp ' + Math.round(val || 0).toLocaleString('id-ID');
    }

    function rowLine(title, value, subtitle) {
        return `
        <div class="list-compact-line">
            <div>
                <div style="font-weight: 500;">${title}</div>
                ${subtitle ? `<div style="font-size: 11px; color: #64748b; margin-top: 2px;">${subtitle}</div>` : ''}
            </div>
            <div style="font-weight: 600; color: #1e293b;">${value}</div>
        </div>`;
    }

    function openBreakdown(tahun, bulan, jenis, idLembaga) {
        var nik = $('#hidden_nik').val();
        var body = document.getElementById('modalBody');
        body.innerHTML = '<div class="text-center py-4 text-muted"><i class="fas fa-spinner fa-spin me-2"></i> Memuat data...</div>';

        var modalEl = document.getElementById('modal-rincian');
        var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();

        var url = "{{ url('/laporan/per-umana/breakdown') }}?k=" + encodeURIComponent(jenis) +
                  "&bulan=" + encodeURIComponent(bulan) +
                  "&tahun=" + encodeURIComponent(tahun) +
                  "&nik=" + encodeURIComponent(nik) +
                  (idLembaga ? "&id_lembaga=" + encodeURIComponent(idLembaga) : "");

        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function(res) {
                if (!res.ok) {
                    body.innerHTML = '<div class="text-center text-danger py-4">' + (res.message || 'Gagal memuat rincian') + '</div>';
                    return;
                }

                document.getElementById('modalSub').textContent = `Kategori: ${jenis.toUpperCase()} | Periode: ${bulan} ${tahun}`;

                var d = res.data || {};
                var f = d.f || {};

                if (jenis === 'pengajar') {
                    var potItems = Array.isArray(d.potongan_items) ? d.potongan_items : [];
                    var potDetailHTML = potItems.length
                        ? `<div class="bg-light p-2 rounded mt-1 mb-2">
                            ${potItems.map(it => `
                                <div style="font-size: 12px; color: #dc2626;">~ ${it.nama}: ${fmt(it.nominal)}</div>
                            `).join('')}
                           </div>`
                        : '';

                    body.innerHTML = `
                        <div class="list-compact-native">
                            ${rowLine('Honor Mengajar', fmt(f.mengajar), d.sks ? `${d.sks} SKS × ${fmt(f.per_sks)}` : '')}
                            ${rowLine('GTY / DTY', fmt(f.gty_dty))}
                            ${rowLine('Jafung', fmt(f.jafung))}
                            ${rowLine('Kehadiran', fmt(f.kehadiran), (d.jhadir > 0 ? 'Hadir ' + d.jhadir + ' Hari' : ''))}
                            ${rowLine('Kehadiran 15.000', fmt(f.nhadir15), 'Hadir ' + (d.jhadir15 || 0) + ' × 15.000')}
                            ${rowLine('Kehadiran 10.000', fmt(f.nhadir10), 'Hadir ' + (d.jhadir10 || 0) + ' × 10.000')}
                            ${rowLine('Wali Kelas', fmt(f.walkes))}
                            ${rowLine('Kehormatan', fmt(f.kehormatan))}
                            ${rowLine('Barokah Tambahan', fmt(f.tambahan))}
                            ${rowLine('Piket', fmt(f.piket))}
                            ${rowLine('Tunjangan Anak', fmt(f.tun_anak))}
                            ${rowLine('Tunjangan Keluarga', fmt(f.tunkel))}
                            ${rowLine('Potongan', fmt(f.potongan))}
                            ${potDetailHTML}
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3 border-top pt-3">
                            <div class="font-weight-bold fs-14">Total Diterima</div>
                            <div class="font-weight-bold text-success fs-16">${fmt(f.total)}</div>
                        </div>`;
                } else if (jenis === 'satpam') {
                    var subK1 = (d.j_hari > 0) ? `${d.j_hari} hari × ${fmt(d.k1_unit)}` : '';
                    var subK2 = (d.j_shift > 0) ? `${d.j_shift} shift × ${fmt(d.k2_unit)}` : '';
                    var subK3 = (d.j_dinihari > 0) ? `${d.j_dinihari} kali × ${fmt(d.k3_unit)}` : '';

                    body.innerHTML = `
                        <div class="list-compact-native">
                            ${rowLine('Kehadiran Hari Kerja', fmt(f.k1_total), subK1)}
                            ${rowLine('Kehadiran Shift', fmt(f.k2_total), subK2)}
                            ${rowLine('Kehadiran Dini Hari', fmt(f.k3_total), subK3)}
                            ${rowLine('Potongan', fmt(f.potongan))}
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3 border-top pt-3">
                            <div class="font-weight-bold fs-14">Total Diterima</div>
                            <div class="font-weight-bold text-success fs-16">${fmt(f.total)}</div>
                        </div>`;
                } else if (jenis === 'struktural') {
                    var potItemsS = Array.isArray(d.potongan_items) ? d.potongan_items : [];
                    var potDetailHTMLS = potItemsS.length
                        ? `<div class="bg-light p-2 rounded mt-1 mb-2">
                            ${potItemsS.map(it => `
                                <div style="font-size: 12px; color: #dc2626;">~ ${it.nama}: ${fmt(it.nominal)}</div>
                            `).join('')}
                           </div>`
                        : '';

                    var hadirSub = (typeof d.kehadiran === 'number' && d.kehadiran > 0)
                        ? `Hadir Normal <strong>${d.kehadiran}</strong> Hari` : '';

                    body.innerHTML = `
                        <div class="list-compact-native">
                            ${rowLine('Barokah Pokok / Tunjab', fmt(f.barokah_pokok))}
                            ${rowLine('Kehadiran', fmt(f.kehadiran_nominal), hadirSub)}
                            ${rowLine('Tunjangan Keluarga', fmt(f.tunkel))}
                            ${rowLine('Tunjangan Anak', fmt(f.tun_anak))}
                            ${rowLine('Tunjangan Masa Pengabdian (TMP)', fmt(f.tmp))}
                            ${rowLine('Tunjangan Beban Kerja (TBK)', fmt(f.tbk))}
                            ${rowLine('Tunjangan Kehormatan', fmt(f.kehormatan))}
                            ${rowLine('Potongan', fmt(f.potongan))}
                            ${potDetailHTMLS}
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3 border-top pt-3">
                            <div class="font-weight-bold fs-14">Total Diterima</div>
                            <div class="font-weight-bold text-success fs-16">${fmt(f.total)}</div>
                        </div>`;
                }
            },
            error: function() {
                body.innerHTML = '<div class="text-center text-danger py-4">Terjadi kesalahan pada server saat mengambil rincian.</div>';
            }
        });
    }
    </script>
@endpush
