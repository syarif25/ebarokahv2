@extends('layouts.app')

@section('title', 'Rincian Laporan - ' . ($lembaga->nama_lembaga ?? 'Lembaga') . ' - E-Barokah')

@section('page_title')
<span class="text-danger font-weight-bold">Laporan</span> / <a href="{{ url('/laporan/per-lembaga') }}" class="text-muted">Per Lembaga</a> / Rincian
@endsection

@push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .summary-card {
            border-radius: 14px;
            border: 1px solid #f1f5f9;
            padding: 18px 20px;
            background: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            height: 100%;
            transition: transform 0.2s ease;
        }
        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07);
        }
        .summary-title {
            font-size: 12px;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .summary-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0;
        }
        #tabel_rincian thead th {
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            background-color: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 10px 8px;
            white-space: nowrap;
        }
        #tabel_rincian tbody td {
            font-size: 12.5px;
            padding: 10px 8px;
            vertical-align: middle;
            white-space: nowrap;
        }
        #tabel_rincian tfoot th {
            font-size: 12.5px;
            font-weight: 700;
            padding: 12px 8px;
            background-color: #f1f5f9;
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title font-weight-bold mb-1">{{ $lembaga->nama_lembaga }} - {{ $kategori }}</h4>
                    <p class="text-muted fs-13 mb-0">
                        <i class="fas fa-calendar-alt text-muted me-1"></i> Periode: <strong>{{ $lembaga->bulan }} {{ $lembaga->tahun }}</strong> 
                        <span class="mx-2 text-muted">•</span>
                        <i class="fas fa-tag text-muted me-1"></i> Kategori: <span class="badge bg-light text-dark border">{{ $kategori }}</span>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ url('/laporan/per-lembaga/export-excel/' . $lembaga->id_kehadiran_lembaga) }}" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm font-weight-bold" style="background-color: #00a15d; border-color: #00a15d;">
                        <i class="fas fa-file-excel me-1"></i> Export XLS
                    </a>
                    <a href="{{ url('/laporan/per-lembaga/cetak/' . $lembaga->id_kehadiran_lembaga) }}" target="_blank" class="btn btn-warning text-dark btn-sm rounded-pill px-3 shadow-sm font-weight-bold">
                        <i class="fas fa-print me-1"></i> Cetak Laporan
                    </a>
                    <a href="{{ url('/laporan/per-lembaga') }}" class="btn btn-light btn-sm rounded-pill px-3 shadow-sm">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Summary Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6 col-6">
                        <div class="summary-card">
                            <span class="summary-title"><i class="fas fa-users text-primary"></i> Total Umana'</span>
                            <h4 class="summary-value text-primary">{{ number_format($totalUmana, 0, ',', '.') }} Orang</h4>
                        </div>
                    </div>
                    <div class="col-md-6 col-6">
                        <div class="summary-card">
                            <span class="summary-title"><i class="fas fa-money-bill-wave text-success"></i> Total Barokah</span>
                            <h4 class="summary-value text-success">Rp {{ number_format($totalBarokah, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Tabel Detail Barokah -->
                <div class="table-responsive">
                    <table id="tabel_rincian" class="table table-bordered table-hover align-middle mb-0" style="width: 100%;">
                        @if($kategori === 'Struktural')
                            <thead>
                                <tr class="text-center">
                                    <th rowspan="2" style="width: 40px;">No</th>
                                    <th rowspan="2">Nama Lengkap</th>
                                    <th rowspan="2">Jabatan</th>
                                    <th rowspan="2">TMT</th>
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
                                @forelse($rows as $r)
                                    @php
                                        $namaLengkap = trim(($r->gelar_depan ? $r->gelar_depan . ' ' : '') . $r->nama_lengkap . ($r->gelar_belakang ? ', ' . $r->gelar_belakang : ''));
                                        $totTunjab += (float)$r->barokah;
                                        $totHadirNom += (float)$r->nominal_kehadiran;
                                        $totTunkel += (float)$r->tunkel;
                                        $totTunAnak += (float)$r->tunj_anak;
                                        $totTbk += (float)$r->tbk;
                                        $totTmp += (float)$r->tmp;
                                        $totKehormatan += (float)$r->kehormatan;
                                        $totPotongan += (float)$r->potongan;
                                        $totDiterima += (float)$r->diterima;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td><strong>{{ $namaLengkap }}</strong></td>
                                        <td>{{ $r->nama_jabatan }}</td>
                                        <td class="text-center">{{ substr((string)$r->tmt_struktural, 0, 4) }}</td>
                                        <td class="text-end">{{ number_format((float)$r->barokah, 0, ',', '.') }}</td>
                                        <td class="text-center">{{ $r->mp }}</td>
                                        <td class="text-center">{{ $r->jumlah_hadir }}</td>
                                        <td class="text-end">{{ number_format((float)$r->nominal_kehadiran, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format((float)$r->tunkel, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format((float)$r->tunj_anak, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format((float)$r->tbk, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format((float)$r->tmp, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format((float)$r->kehormatan, 0, ',', '.') }}</td>
                                        <td class="text-end text-danger">{{ number_format((float)$r->potongan, 0, ',', '.') }}</td>
                                        <td class="text-end font-weight-bold" style="color: #16a34a;">{{ number_format((float)$r->diterima, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="15" class="text-center py-4 text-muted">Tidak ada data rincian barokah</td>
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
                                    <th class="text-end font-weight-bold" style="color: #16a34a;">{{ number_format($totDiterima, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        @elseif($kategori === 'Pengajar')
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 40px;">No</th>
                                    <th>Nama Lengkap</th>
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
                                @forelse($rows as $r)
                                    @php
                                        $namaLengkap = trim(($r->gelar_depan ? $r->gelar_depan . ' ' : '') . $r->nama_lengkap . ($r->gelar_belakang ? ', ' . $r->gelar_belakang : ''));
                                        $totTunkelP += (float)$r->tunkel;
                                        $totTunAnakP += (float)$r->tunj_anak;
                                        $totKehormatanP += (float)$r->kehormatan;
                                        $totWalkesP += (float)$r->walkes;
                                        $totTambahanP += (float)$r->khusus;
                                        $totPotonganP += (float)$r->potongan;
                                        $totDiterimaP += (float)$r->diterima;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td><strong>{{ $namaLengkap }}</strong></td>
                                        <td class="text-center">{{ $r->nama_jabatan }}</td>
                                        <td class="text-center">{{ $r->jumlah_sks }} SKS</td>
                                        <td class="text-center">{{ $r->jumlah_hadir }}</td>
                                        <td class="text-center">{{ $r->jumlah_hadir_piket ?? '-' }}</td>
                                        <td class="text-end">{{ number_format((float)$r->tunkel, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format((float)$r->tunj_anak, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format((float)$r->kehormatan, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format((float)$r->walkes, 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format((float)$r->khusus, 0, ',', '.') }}</td>
                                        <td class="text-end text-danger">{{ number_format((float)$r->potongan, 0, ',', '.') }}</td>
                                        <td class="text-end font-weight-bold" style="color: #16a34a;">{{ number_format((float)$r->diterima, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="13" class="text-center py-4 text-muted">Tidak ada data rincian barokah pengajar</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" class="text-center">TOTAL</th>
                                    <th class="text-end">{{ number_format($totTunkelP, 0, ',', '.') }}</th>
                                    <th class="text-end">{{ number_format($totTunAnakP, 0, ',', '.') }}</th>
                                    <th class="text-end">{{ number_format($totKehormatanP, 0, ',', '.') }}</th>
                                    <th class="text-end">{{ number_format($totWalkesP, 0, ',', '.') }}</th>
                                    <th class="text-end">{{ number_format($totTambahanP, 0, ',', '.') }}</th>
                                    <th class="text-end text-danger">{{ number_format($totPotonganP, 0, ',', '.') }}</th>
                                    <th class="text-end font-weight-bold" style="color: #16a34a;">{{ number_format($totDiterimaP, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        @else
                            <thead>
                                <tr class="text-center">
                                    <th style="width: 40px;">No</th>
                                    <th>Nama Anggota</th>
                                    <th>Kehadiran (Hari)</th>
                                    <th class="text-end">Potongan</th>
                                    <th class="text-end">Diterima</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totPotonganS = 0; $totDiterimaS = 0;
                                @endphp
                                @forelse($rows as $r)
                                    @php
                                        $namaLengkap = trim(($r->gelar_depan ? $r->gelar_depan . ' ' : '') . $r->nama_lengkap . ($r->gelar_belakang ? ', ' . $r->gelar_belakang : ''));
                                        $totPotonganS += (float)$r->potongan;
                                        $totDiterimaS += (float)$r->diterima;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td><strong>{{ $namaLengkap }}</strong></td>
                                        <td class="text-center">{{ $r->jumlah_hadir }}</td>
                                        <td class="text-end text-danger">{{ number_format((float)$r->potongan, 0, ',', '.') }}</td>
                                        <td class="text-end font-weight-bold" style="color: #16a34a;">{{ number_format((float)$r->diterima, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Tidak ada data rincian satpam</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-center">TOTAL</th>
                                    <th class="text-end text-danger">{{ number_format($totPotonganS, 0, ',', '.') }}</th>
                                    <th class="text-end font-weight-bold" style="color: #16a34a;">{{ number_format($totDiterimaS, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#tabel_rincian').DataTable({
                paging: false,
                searching: true,
                ordering: true,
                info: true,
                language: {
                    search: "Cari Umana':",
                    info: "Menampilkan _TOTAL_ rincian barokah",
                    infoEmpty: "Tidak ada data",
                    zeroRecords: "Data tidak ditemukan"
                }
            });
        });
    </script>
@endpush
