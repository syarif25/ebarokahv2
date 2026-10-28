@extends('layouts.app')

@section('title', 'Dashboard Lembaga - E-Barokah')
@section('page_title')
<span class="text-danger font-weight-bold">Dashboard</span> / {{ $lembaga ? $lembaga->nama_lembaga : 'Lembaga' }}
@endsection

@section('content')
<!-- 4 KARTU STATISTIK NATIVE THEME -->
<div class="row">
    <!-- Total Struktural -->
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-2 text-nowrap">Staf Struktural</h4>
                    <h2 class="fs-32 font-w700 mb-0 text-primary">{{ $totalStruktural }}</h2>
                    <small class="text-muted">Staf Aktif</small>
                </div>
                <i class="fas fa-user-tie fa-3x text-primary" style="opacity: 0.5;"></i>
            </div>
        </div>
    </div>

    <!-- Total Tenaga Pengajar -->
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-2 text-nowrap">Tenaga Pengajar</h4>
                    <h2 class="fs-32 font-w700 mb-0 text-success">{{ $totalPengajar }}</h2>
                    <small class="text-muted">Guru / Dosen</small>
                </div>
                <i class="fas fa-chalkboard-teacher fa-3x text-success" style="opacity: 0.5;"></i>
            </div>
        </div>
    </div>

    <!-- Status Blanko Terakhir -->
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-2 text-nowrap">Blanko Terakhir</h4>
                    <h2 class="fs-20 font-w700 mb-0 text-info">
                        {{ $kehadiranStrukturalTerbaru ? $kehadiranStrukturalTerbaru->bulan . ' ' . $kehadiranStrukturalTerbaru->tahun : '-' }}
                    </h2>
                    @if($kehadiranStrukturalTerbaru)
                        <span class="badge badge-sm badge-success">{{ ucfirst($kehadiranStrukturalTerbaru->status) }}</span>
                    @else
                        <small class="text-muted">Belum Ada</small>
                    @endif
                </div>
                <i class="fas fa-clipboard-check fa-3x text-info" style="opacity: 0.5;"></i>
            </div>
        </div>
    </div>

    <!-- Total Potongan Lembaga -->
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-2 text-nowrap">Potongan Lembaga</h4>
                    <h2 class="fs-20 font-w700 mb-0 text-danger">Rp {{ number_format($totalNominalPotongan, 0, ',', '.') }}</h2>
                    <small class="text-muted">Struktural & Pengajar</small>
                </div>
                <i class="fas fa-hand-holding-usd fa-3x text-danger" style="opacity: 0.5;"></i>
            </div>
        </div>
    </div>
</div>

<!-- AKSI CEPAT DAN STATUS KEHADIRAN -->
<div class="row">
    <!-- Menu Cepat & Status Blanko -->
    <div class="col-xl-6 col-lg-6">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Aksi & Blanko Kehadiran</h4>
            </div>
            <div class="card-body">
                <div class="basic-list-group">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-1 font-w600">Kehadiran Struktural</h6>
                                <small class="text-muted">
                                    Periode Terakhir: {{ $kehadiranStrukturalTerbaru ? $kehadiranStrukturalTerbaru->bulan . ' ' . $kehadiranStrukturalTerbaru->tahun : 'Belum Ada' }}
                                </small>
                            </div>
                            <div>
                                @if($kehadiranStrukturalTerbaru)
                                    <span class="badge badge-success light me-2">{{ ucfirst($kehadiranStrukturalTerbaru->status) }}</span>
                                @endif
                                <a href="{{ url('/kehadiran_struktural') }}" class="btn btn-outline-primary btn-sm" style="border-radius: 20px; padding: 4px 14px;">
                                    Buka Menu
                                </a>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-1 font-w600">Kehadiran Pengajar</h6>
                                <small class="text-muted">
                                    Periode Terakhir: {{ $kehadiranPengajarTerbaru ? $kehadiranPengajarTerbaru->bulan . ' ' . $kehadiranPengajarTerbaru->tahun : 'Belum Ada' }}
                                </small>
                            </div>
                            <div>
                                @if($kehadiranPengajarTerbaru)
                                    <span class="badge badge-success light me-2">{{ ucfirst($kehadiranPengajarTerbaru->status) }}</span>
                                @endif
                                <a href="{{ url('/kehadiran_pengajar') }}" class="btn btn-outline-primary btn-sm" style="border-radius: 20px; padding: 4px 14px;">
                                    Buka Menu
                                </a>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-1 font-w600">Potongan Umana' (Struktural)</h6>
                                <small class="text-muted">Kelola potongan internal staf</small>
                            </div>
                            <div>
                                <a href="{{ url('/potongan_umana') }}" class="btn btn-outline-danger btn-sm" style="border-radius: 20px; padding: 4px 14px;">
                                    Buka Menu
                                </a>
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-1 font-w600">Potongan Pengajar</h6>
                                <small class="text-muted">Kelola potongan internal pengajar</small>
                            </div>
                            <div>
                                <a href="{{ url('/potongan_pengajar') }}" class="btn btn-outline-danger btn-sm" style="border-radius: 20px; padding: 4px 14px;">
                                    Buka Menu
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Potongan Terbaru Lembaga -->
    <div class="col-xl-6 col-lg-6">
        <div class="card">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="card-title">Potongan Terakhir di Lembaga</h4>
                <a href="{{ url('/potongan_umana') }}" class="btn-link text-primary fs-12">Lihat Semua</a>
            </div>
            <div class="card-body pt-2">
                @if($recentPotongan->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-responsive-sm">
                            <thead>
                                <tr>
                                    <th><strong>NAMA</strong></th>
                                    <th><strong>JENIS</strong></th>
                                    <th class="text-end"><strong>NOMINAL</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPotongan as $pot)
                                <tr>
                                    <td>
                                        <span class="font-w600 text-dark">{{ $pot['nama'] }}</span>
                                        <small class="d-block text-muted">{{ $pot['kategori'] }}</small>
                                    </td>
                                    <td>{{ $pot['jenis'] }}</td>
                                    <td class="text-end font-w600 text-danger">Rp {{ number_format($pot['nominal'], 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-light text-center py-4 text-muted mb-0">
                        Belum ada riwayat potongan yang diinput di lembaga ini.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
