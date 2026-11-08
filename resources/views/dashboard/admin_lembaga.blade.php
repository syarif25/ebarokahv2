@extends('layouts.app')

@section('title', 'Dashboard Lembaga - E-Barokah')
@section('page_title')
<span class="text-danger font-weight-bold">Dashboard</span> / {{ $lembaga ? $lembaga->nama_lembaga : 'Lembaga' }}
@endsection

@section('content')
<!-- 4 KARTU STATISTIK UTAMA (JUMLAH STRUKTURAL, JUMLAH PENGAJAR, BAROKAH BULAN INI & BULAN LALU) -->
<div class="row">
    <!-- 1. Total Staf Struktural -->
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-2 text-nowrap">Jumlah Struktural</h4>
                    <h2 class="fs-32 font-w700 mb-0 text-primary">{{ number_format($totalStruktural ?? 0, 0, ',', '.') }}</h2>
                    <small class="text-muted">Staf Aktif</small>
                </div>
                <i class="fas fa-user-tie fa-3x text-primary" style="opacity: 0.5;"></i>
            </div>
        </div>
    </div>

    <!-- 2. Total Tenaga Pengajar -->
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-2 text-nowrap">Jumlah Pengajar</h4>
                    <h2 class="fs-32 font-w700 mb-0 text-warning">{{ number_format($totalPengajar ?? 0, 0, ',', '.') }}</h2>
                    <small class="text-muted">Guru / Dosen Aktif</small>
                </div>
                <i class="fas fa-chalkboard-teacher fa-3x text-warning" style="opacity: 0.5;"></i>
            </div>
        </div>
    </div>

    <!-- 3. Barokah Bulan Ini -->
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-1 text-nowrap">Barokah Bulan Ini</h4>
                    <h2 class="fs-22 font-w700 mb-0 text-success">Rp {{ number_format($barokahBulanIni['total_nominal'] ?? 0, 0, ',', '.') }}</h2>
                    <small class="text-muted">{{ $barokahBulanIni['bulan'] ?? '-' }} {{ $barokahBulanIni['tahun'] ?? '-' }}</small>
                </div>
                <i class="fas fa-money-check-alt fa-3x text-success" style="opacity: 0.4;"></i>
            </div>
        </div>
    </div>

    <!-- 4. Barokah Bulan Lalu -->
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-1 text-nowrap">Barokah Bulan Lalu</h4>
                    <h2 class="fs-22 font-w700 mb-0 text-dark">Rp {{ number_format($barokahBulanLalu['total_nominal'] ?? 0, 0, ',', '.') }}</h2>
                    <small class="text-muted">{{ $barokahBulanLalu['bulan'] ?? '-' }} {{ $barokahBulanLalu['tahun'] ?? '-' }}</small>
                </div>
                <i class="fas fa-calendar-check fa-3x text-primary" style="opacity: 0.4;"></i>
            </div>
        </div>
    </div>
</div>

<!-- AKSI CEPAT DAN STATUS KEHADIRAN -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title font-w700">Aksi & Blanko Kehadiran</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Kehadiran Struktural -->
                    <div class="col-md-6 mb-3">
                        <div class="p-3 rounded border d-flex justify-content-between align-items-center bg-light">
                            <div>
                                <h6 class="mb-1 font-w600 text-dark"><i class="fas fa-user-tie text-primary me-2"></i>Kehadiran Struktural</h6>
                                <small class="text-muted">
                                    Periode Terakhir: {{ $kehadiranStrukturalTerbaru ? $kehadiranStrukturalTerbaru->bulan . ' ' . $kehadiranStrukturalTerbaru->tahun : 'Belum Ada' }}
                                </small>
                            </div>
                            <div class="d-flex align-items-center">
                                @if($kehadiranStrukturalTerbaru)
                                    <span class="badge badge-success light me-2">{{ ucfirst($kehadiranStrukturalTerbaru->status) }}</span>
                                @endif
                                <a href="{{ url('/kehadiran_struktural') }}" class="btn btn-outline-primary btn-sm" style="border-radius: 20px; padding: 4px 14px;">
                                    Buka Menu
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Kehadiran Pengajar -->
                    <div class="col-md-6 mb-3">
                        <div class="p-3 rounded border d-flex justify-content-between align-items-center bg-light">
                            <div>
                                <h6 class="mb-1 font-w600 text-dark"><i class="fas fa-chalkboard-teacher text-warning me-2"></i>Kehadiran Pengajar</h6>
                                <small class="text-muted">
                                    Periode Terakhir: {{ $kehadiranPengajarTerbaru ? $kehadiranPengajarTerbaru->bulan . ' ' . $kehadiranPengajarTerbaru->tahun : 'Belum Ada' }}
                                </small>
                            </div>
                            <div class="d-flex align-items-center">
                                @if($kehadiranPengajarTerbaru)
                                    <span class="badge badge-success light me-2">{{ ucfirst($kehadiranPengajarTerbaru->status) }}</span>
                                @endif
                                <a href="{{ url('/kehadiran_pengajar') }}" class="btn btn-outline-primary btn-sm" style="border-radius: 20px; padding: 4px 14px;">
                                    Buka Menu
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Potongan Umana (Struktural) -->
                    <div class="col-md-6 mb-2">
                        <div class="p-3 rounded border d-flex justify-content-between align-items-center bg-light">
                            <div>
                                <h6 class="mb-1 font-w600 text-dark"><i class="fas fa-cut text-danger me-2"></i>Potongan Umana' (Struktural)</h6>
                                <small class="text-muted">Kelola potongan internal staf lembaga</small>
                            </div>
                            <div>
                                <a href="{{ url('/potongan_umana') }}" class="btn btn-outline-danger btn-sm" style="border-radius: 20px; padding: 4px 14px;">
                                    Buka Menu
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Potongan Pengajar -->
                    <div class="col-md-6 mb-2">
                        <div class="p-3 rounded border d-flex justify-content-between align-items-center bg-light">
                            <div>
                                <h6 class="mb-1 font-w600 text-dark"><i class="fas fa-cut text-danger me-2"></i>Potongan Pengajar</h6>
                                <small class="text-muted">Kelola potongan internal pengajar lembaga</small>
                            </div>
                            <div>
                                <a href="{{ url('/potongan_pengajar') }}" class="btn btn-outline-danger btn-sm" style="border-radius: 20px; padding: 4px 14px;">
                                    Buka Menu
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
