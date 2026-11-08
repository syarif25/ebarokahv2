@extends('layouts.app')

@section('title', 'Dashboard - E-Barokah')
@section('page_title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-2 text-nowrap">Total Umana</h4>
                    <h2 class="fs-32 font-w700 mb-0 text-primary">{{ number_format($totalUmana ?? 0, 0, ',', '.') }}</h2>
                </div>
                <i class="fas fa-users fa-3x text-primary" style="opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-2 text-nowrap">Total Lembaga</h4>
                    <h2 class="fs-32 font-w700 mb-0 text-success">{{ number_format($totalLembaga ?? 0, 0, ',', '.') }}</h2>
                </div>
                <i class="fas fa-building fa-3x text-success" style="opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-2 text-nowrap">Struktural Aktif</h4>
                    <h2 class="fs-32 font-w700 mb-0 text-info">{{ number_format($totalPenempatan ?? 0, 0, ',', '.') }}</h2>
                </div>
                <i class="fas fa-user-tie fa-3x text-info" style="opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-2 text-nowrap">Pengajar Aktif</h4>
                    <h2 class="fs-32 font-w700 mb-0 text-warning">{{ number_format($totalPengajar ?? 0, 0, ',', '.') }}</h2>
                </div>
                <i class="fas fa-chalkboard-teacher fa-3x text-warning" style="opacity: 0.5;"></i>
            </div>
        </div>
    </div>
</div>

<!-- KARTU RINGKASAN BAROKAH BULAN INI & BULAN LALU (NATIVE THEME) -->
<div class="row">
    <div class="col-xl-6 col-sm-12">
        <div class="card">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-1 text-nowrap">Barokah Bulan Ini ({{ $dataBulanIni['bulan'] ?? '-' }} {{ $dataBulanIni['tahun'] ?? '-' }})</h4>
                    <h2 class="fs-32 font-w700 mb-1 text-success">Rp {{ number_format($dataBulanIni['total_nominal'] ?? 0, 0, ',', '.') }}</h2>
                    <span class="text-muted fs-13"><i class="fas fa-building me-1"></i>{{ $dataBulanIni['total_lembaga'] ?? 0 }} Lembaga Terproses</span>
                </div>
                <i class="fas fa-calendar-check fa-3x text-success" style="opacity: 0.4;"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-sm-12">
        <div class="card">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-1 text-nowrap">Barokah Bulan Lalu ({{ $dataBulanLalu['bulan'] ?? '-' }} {{ $dataBulanLalu['tahun'] ?? '-' }})</h4>
                    <h2 class="fs-32 font-w700 mb-1 text-dark">Rp {{ number_format($dataBulanLalu['total_nominal'] ?? 0, 0, ',', '.') }}</h2>
                    <span class="text-muted fs-13"><i class="fas fa-history me-1"></i>{{ $dataBulanLalu['total_lembaga'] ?? 0 }} Lembaga Terproses</span>
                </div>
                <i class="fas fa-calendar-check fa-3x text-primary" style="opacity: 0.4;"></i>
            </div>
        </div>
    </div>
</div>

<!-- STATISTIK REKENING BANK UMANA (FULL WIDTH KE SAMPING) -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Kategori Rekening Bank Umana</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-responsive-md">
                        <thead>
                            <tr>
                                <th><strong>KETERANGAN</strong></th>
                                <th class="text-center"><strong>BANK BRI</strong></th>
                                <th class="text-center"><strong>BANK BSI</strong></th>
                                <th class="text-center"><strong>BANK JATIM</strong></th>
                                <th class="text-center"><strong>BANK MANDIRI</strong></th>
                                <th class="text-center"><strong>BANK BNI & BCA</strong></th>
                                @if(($bankStats['tanpa_rek'] ?? 0) > 0)
                                    <th class="text-center"><strong>BELUM TERDATA</strong></th>
                                @endif
                                <th class="text-center"><strong>TOTAL UMANA</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-w600 text-dark">Jumlah Rekening</td>
                                <td class="text-center font-w700 text-dark">{{ number_format($bankStats['bri'] ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center font-w700 text-dark">{{ number_format($bankStats['bsi'] ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center font-w600 text-dark">{{ number_format($bankStats['jatim'] ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center font-w600 text-dark">{{ number_format($bankStats['mandiri'] ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center font-w600 text-dark">{{ number_format(($bankStats['bni'] ?? 0) + ($bankStats['bca'] ?? 0), 0, ',', '.') }}</td>
                                @if(($bankStats['tanpa_rek'] ?? 0) > 0)
                                    <td class="text-center font-w600 text-danger">{{ number_format($bankStats['tanpa_rek'] ?? 0, 0, ',', '.') }}</td>
                                @endif
                                <td class="text-center font-w700 text-primary">{{ number_format($totalUmana ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="font-w600 text-dark">Persentase (%)</td>
                                <td class="text-center font-w600 text-success">{{ ($totalUmana ?? 0) > 0 ? round((($bankStats['bri'] ?? 0) / $totalUmana) * 100, 1) : 0 }}%</td>
                                <td class="text-center font-w600 text-success">{{ ($totalUmana ?? 0) > 0 ? round((($bankStats['bsi'] ?? 0) / $totalUmana) * 100, 1) : 0 }}%</td>
                                <td class="text-center text-muted">{{ ($totalUmana ?? 0) > 0 ? round((($bankStats['jatim'] ?? 0) / $totalUmana) * 100, 1) : 0 }}%</td>
                                <td class="text-center text-muted">{{ ($totalUmana ?? 0) > 0 ? round((($bankStats['mandiri'] ?? 0) / $totalUmana) * 100, 1) : 0 }}%</td>
                                <td class="text-center text-muted">{{ ($totalUmana ?? 0) > 0 ? round(((($bankStats['bni'] ?? 0) + ($bankStats['bca'] ?? 0)) / $totalUmana) * 100, 1) : 0 }}%</td>
                                @if(($bankStats['tanpa_rek'] ?? 0) > 0)
                                    <td class="text-center text-danger font-w600">{{ ($totalUmana ?? 0) > 0 ? round((($bankStats['tanpa_rek'] ?? 0) / $totalUmana) * 100, 1) : 0 }}%</td>
                                @endif
                                <td class="text-center font-w700 text-dark">100%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
