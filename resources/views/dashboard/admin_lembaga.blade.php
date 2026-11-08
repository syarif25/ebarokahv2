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
                <i class="fas fa-calendar-check fa-3x text-success" style="opacity: 0.4;"></i>
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
@endsection
