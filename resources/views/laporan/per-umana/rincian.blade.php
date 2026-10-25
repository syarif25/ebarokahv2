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

        /* Table Styling identik dengan standar V2 */
        table.dataTable.display tbody td, 
        table.dataTable.display tbody th, 
        table.dataTable.display thead th {
            border-left: none !important;
            border-right: none !important;
            padding: 1.1rem 1rem !important;
            vertical-align: middle;
        }
        table.dataTable.display thead th {
            border-top: none !important;
            border-bottom: 2px solid #f0f0f0 !important;
            text-transform: capitalize;
            font-weight: 700;
            color: #333;
            background-color: #fff !important;
            font-size: 13px !important;
        }
        table.dataTable.display tbody tr {
            border-bottom: 1px solid #f8f8f8 !important;
        }
        table.dataTable.display tbody tr.odd,
        table.dataTable.display tbody tr.even {
            background-color: transparent !important;
        }
        table.dataTable.display tbody tr:hover {
            background-color: #fafafa !important;
        }
        table.dataTable,
        table.dataTable.no-footer {
            border-bottom: none !important;
        }
        table.dataTable.display tfoot th {
            border-top: 2px solid #eef2f6 !important;
            border-bottom: none !important;
            border-left: none !important;
            border-right: none !important;
            padding: 1rem !important;
            font-weight: 700;
            color: #1e293b;
            background-color: #f8fafc !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0.2em 0.5em !important;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 12px;
            margin-left: 8px;
            outline: none;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #00a15d;
            box-shadow: 0 0 0 3px rgba(0, 161, 93, 0.12);
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 4px 8px;
            margin: 0 4px;
            outline: none;
        }
        .nominal-highlight {
            color: #00a15d !important;
            font-weight: 700 !important;
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
                            <table id="tabel_rekapitulasi" class="display w-100">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;" class="text-center">No</th>
                                        <th>Bulan / Periode</th>
                                        <th>Lembaga</th>
                                        <th class="text-end">Total Struktural</th>
                                        <th class="text-end">Total Pengajar</th>
                                        <th class="text-end">Total Satpam</th>
                                        <th class="text-end">Grand Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rekap as $r)
                                    <tr>
                                        <td class="text-center font-weight-bold">{{ $loop->iteration }}</td>
                                        <td><strong>{{ $r->bulan }} {{ $r->tahun }}</strong></td>
                                        <td><span class="text-muted">{{ $r->daftar_lembaga ?: '-' }}</span></td>
                                        <td class="text-end">Rp {{ number_format((float)$r->total_struktural, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format((float)$r->total_pengajar, 0, ',', '.') }}</td>
                                        <td class="text-end">Rp {{ number_format((float)$r->total_satpam, 0, ',', '.') }}</td>
                                        <td class="text-end nominal-highlight">
                                            Rp {{ number_format((float)$r->grand_total, 0, ',', '.') }}
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

                    <!-- Tab 2: Struktural (Tabel Rincian Lengkap) -->
                    <div class="tab-pane fade" id="tab-struktural" role="tabpanel">
                        <div class="table-responsive">
                            <table id="tabel_struktural" class="display w-100">
                                <thead>
                                    <tr class="text-center">
                                        <th rowspan="2" style="width: 50px;">No</th>
                                        <th rowspan="2">Bulan / Periode</th>
                                        <th rowspan="2">Lembaga</th>
                                        <th rowspan="2">Jabatan</th>
                                        <th rowspan="2" class="text-end">Tunjab</th>
                                        <th rowspan="2">MP</th>
                                        <th colspan="2">Kehadiran</th>
                                        <th rowspan="2" class="text-end">Tunkel</th>
                                        <th rowspan="2" class="text-end">Tunj Anak</th>
                                        <th rowspan="2" class="text-end">TBK</th>
                                        <th rowspan="2" class="text-end">TMP</th>
                                        <th rowspan="2" class="text-end">Kehormatan</th>
                                        <th rowspan="2" class="text-end">Potongan</th>
                                        <th rowspan="2" class="text-end">Diterima</th>
                                    </tr>
                                    <tr class="text-center">
                                        <th>Hari</th>
                                        <th class="text-end">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                         $totTunjab = 0; $totHadirNom = 0; $totTunkel = 0; $totTunAnak = 0;
                                        $totTbk = 0; $totTmp = 0; $totKehormatan = 0; $totPotongan = 0; $totDiterima = 0;
                                    @endphp
                                    @forelse($struktural as $s)
                                        @php
                                            $totTunjab += (float)$s->tunjab;
                                            $totHadirNom += (float)$s->nominal_kehadiran;
                                            $totTunkel += (float)$s->tunkel;
                                            $totTunAnak += (float)$s->tunj_anak;
                                            $totTbk += (float)$s->tbk;
                                            $totTmp += (float)$s->tmp;
                                            $totKehormatan += (float)$s->kehormatan;
                                            $totPotongan += (float)$s->potongan;
                                            $totDiterima += (float)$s->diterima;
                                        @endphp
                                        <tr>
                                            <td class="text-center font-weight-bold">{{ $loop->iteration }}</td>
                                            <td><strong>{{ $s->bulan }} {{ $s->tahun }}</strong></td>
                                            <td>{{ $s->nama_lembaga }}</td>
                                            <td>{{ $s->nama_jabatan ?: '-' }}</td>
                                            <td class="text-end">{{ number_format((float)$s->tunjab, 0, ',', '.') }}</td>
                                            <td class="text-center">{{ $s->mp ?: 0 }}</td>
                                            <td class="text-center">{{ $s->jumlah_hadir ?: 0 }}</td>
                                            <td class="text-end">{{ number_format((float)$s->nominal_kehadiran, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format((float)$s->tunkel, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format((float)$s->tunj_anak, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format((float)$s->tbk, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format((float)$s->tmp, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format((float)$s->kehormatan, 0, ',', '.') }}</td>
                                            <td class="text-end text-danger">{{ number_format((float)$s->potongan, 0, ',', '.') }}</td>
                                            <td class="text-end nominal-highlight">{{ number_format((float)$s->diterima, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="15" class="text-center py-4 text-muted">Data tidak ditemukan / Tidak aktif di Struktural</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-center">TOTAL</th>
                                        <th class="text-end">{{ number_format($totTunjab, 0, ',', '.') }}</th>
                                        <th></th>
                                        <th></th>
                                        <th class="text-end">{{ number_format($totHadirNom, 0, ',', '.') }}</th>
                                        <th class="text-end">{{ number_format($totTunkel, 0, ',', '.') }}</th>
                                        <th class="text-end">{{ number_format($totTunAnak, 0, ',', '.') }}</th>
                                        <th class="text-end">{{ number_format($totTbk, 0, ',', '.') }}</th>
                                        <th class="text-end">{{ number_format($totTmp, 0, ',', '.') }}</th>
                                        <th class="text-end">{{ number_format($totKehormatan, 0, ',', '.') }}</th>
                                        <th class="text-end text-danger">{{ number_format($totPotongan, 0, ',', '.') }}</th>
                                        <th class="text-end nominal-highlight">{{ number_format($totDiterima, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 3: Pengajar (Tabel Rincian Lengkap) -->
                    <div class="tab-pane fade" id="tab-pengajar" role="tabpanel">
                        <div class="table-responsive">
                            <table id="tabel_pengajar" class="display w-100">
                                <thead>
                                    <tr class="text-center">
                                        <th style="width: 50px;">No</th>
                                        <th>Bulan / Periode</th>
                                        <th>Lembaga</th>
                                        <th>Kategori</th>
                                        <th>SKS / Mengajar</th>
                                        <th>Kehadiran</th>
                                        <th>Piket</th>
                                        <th class="text-end">Tunkel</th>
                                        <th class="text-end">Tunj Anak</th>
                                        <th class="text-end">Kehormatan</th>
                                        <th class="text-end">Walkes</th>
                                        <th class="text-end">Barokah Tambahan</th>
                                        <th class="text-end">Potongan</th>
                                        <th class="text-end">Diterima</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totTunkelP = 0; $totTunAnakP = 0; $totKehormatanP = 0;
                                        $totWalkesP = 0; $totTambahanP = 0;
                                        $totPotonganP = 0; $totDiterimaP = 0;
                                    @endphp
                                    @forelse($pengajar as $p)
                                        @php
                                            $totTunkelP += (float)$p->tunkel;
                                            $totTunAnakP += (float)$p->tunj_anak;
                                            $totKehormatanP += (float)$p->kehormatan;
                                            $totWalkesP += (float)$p->walkes;
                                            $totTambahanP += (float)$p->tambahan;
                                            $totPotonganP += (float)$p->potongan;
                                            $totDiterimaP += (float)$p->diterima;
                                        @endphp
                                        <tr>
                                            <td class="text-center font-weight-bold">{{ $loop->iteration }}</td>
                                            <td><strong>{{ $p->bulan }} {{ $p->tahun }}</strong></td>
                                            <td>{{ $p->nama_lembaga }}</td>
                                            <td class="text-center">{{ $p->nama_jabatan ?: '-' }}</td>
                                            <td class="text-center">{{ $p->jumlah_sks ?: 0 }} SKS</td>
                                            <td class="text-center">{{ $p->jumlah_hadir ?: 0 }}</td>
                                            <td class="text-center">{{ $p->jumlah_hadir_piket ?? '-' }}</td>
                                            <td class="text-end">{{ number_format((float)$p->tunkel, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format((float)$p->tunj_anak, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format((float)$p->kehormatan, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format((float)$p->walkes, 0, ',', '.') }}</td>
                                            <td class="text-end">{{ number_format((float)$p->tambahan, 0, ',', '.') }}</td>
                                            <td class="text-end text-danger">{{ number_format((float)$p->potongan, 0, ',', '.') }}</td>
                                            <td class="text-end nominal-highlight">{{ number_format((float)$p->diterima, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="14" class="text-center py-4 text-muted">Data tidak ditemukan / Tidak aktif sebagai Pengajar</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="7" class="text-center">TOTAL</th>
                                        <th class="text-end">{{ number_format($totTunkelP, 0, ',', '.') }}</th>
                                        <th class="text-end">{{ number_format($totTunAnakP, 0, ',', '.') }}</th>
                                        <th class="text-end">{{ number_format($totKehormatanP, 0, ',', '.') }}</th>
                                        <th class="text-end">{{ number_format($totWalkesP, 0, ',', '.') }}</th>
                                        <th class="text-end">{{ number_format($totTambahanP, 0, ',', '.') }}</th>
                                        <th class="text-end text-danger">{{ number_format($totPotonganP, 0, ',', '.') }}</th>
                                        <th class="text-end nominal-highlight">{{ number_format($totDiterimaP, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Tab 4: Satpam (Tabel Rincian Lengkap) -->
                    <div class="tab-pane fade" id="tab-satpam" role="tabpanel">
                        <div class="table-responsive">
                            <table id="tabel_satpam" class="display w-100">
                                <thead>
                                    <tr class="text-center">
                                        <th style="width: 50px;">No</th>
                                        <th>Bulan / Periode</th>
                                        <th>Kehadiran (Hari)</th>
                                        <th class="text-end">Transport</th>
                                        <th>Shift</th>
                                        <th class="text-end">Honor Shift</th>
                                        <th>Dini Hari</th>
                                        <th class="text-end">Konsumsi</th>
                                        <th class="text-end">Potongan</th>
                                        <th class="text-end">Diterima</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totHariS = 0; $totTransS = 0; $totShiftS = 0; $totDiniS = 0;
                                        $totPotonganS = 0; $totDiterimaS = 0;
                                    @endphp
                                    @forelse($satpam as $st)
                                        @php
                                            $totTransport = (float)$st->nominal_transport * (int)$st->jumlah_hadir;
                                            $totShiftNom = (float)$st->nominal_shift * (int)$st->jumlah_shift;
                                            $totDiniNom = (float)$st->nominal_dinihari * (int)$st->jumlah_dinihari;
                                            $totHariS += (int)$st->jumlah_hadir;
                                            $totTransS += $totTransport;
                                            $totShiftS += $totShiftNom;
                                            $totDiniS += $totDiniNom;
                                            $totPotonganS += (float)$st->potongan;
                                            $totDiterimaS += (float)$st->diterima;
                                        @endphp
                                        <tr>
                                            <td class="text-center font-weight-bold">{{ $loop->iteration }}</td>
                                            <td><strong>{{ $st->bulan }} {{ $st->tahun }}</strong></td>
                                            <td class="text-center">{{ $st->jumlah_hadir ?: 0 }}</td>
                                            <td class="text-end">{{ number_format($totTransport, 0, ',', '.') }}</td>
                                            <td class="text-center">{{ $st->jumlah_shift ?: 0 }}</td>
                                            <td class="text-end">{{ number_format($totShiftNom, 0, ',', '.') }}</td>
                                            <td class="text-center">{{ $st->jumlah_dinihari ?: 0 }}</td>
                                            <td class="text-end">{{ number_format($totDiniNom, 0, ',', '.') }}</td>
                                            <td class="text-end text-danger">{{ number_format((float)$st->potongan, 0, ',', '.') }}</td>
                                            <td class="text-end nominal-highlight">{{ number_format((float)$st->diterima, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-4 text-muted">Data tidak ditemukan / Tidak aktif sebagai Satpam</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-center">TOTAL</th>
                                        <th class="text-center">{{ $totHariS }}</th>
                                        <th class="text-end">{{ number_format($totTransS, 0, ',', '.') }}</th>
                                        <th></th>
                                        <th class="text-end">{{ number_format($totShiftS, 0, ',', '.') }}</th>
                                        <th></th>
                                        <th class="text-end">{{ number_format($totDiniS, 0, ',', '.') }}</th>
                                        <th class="text-end text-danger">{{ number_format($totPotonganS, 0, ',', '.') }}</th>
                                        <th class="text-end nominal-highlight">{{ number_format($totDiterimaS, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div> <!-- End Tab Content -->
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
            scrollX: true,
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
            scrollX: true,
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
            scrollX: true,
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
            scrollX: true,
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                paginate: { next: '<i class="fas fa-chevron-right"></i>', previous: '<i class="fas fa-chevron-left"></i>' }
            }
        });

        // Trigger adjust columns saat berpindah tab agar header dan kolom DataTables selaras
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });
    });
    </script>
@endpush
