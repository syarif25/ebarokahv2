@extends('layouts.app')

@section('title', 'Dashboard Lembaga - E-Barokah')
@section('page_title', 'Dashboard Lembaga')

@push('styles')
<style>
    .welcome-card {
        background: linear-gradient(135deg, #00a15d 0%, #00874e 100%);
        border-radius: 20px;
        color: #fff;
        padding: 30px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0, 161, 93, 0.2);
    }
    .welcome-card::after {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        pointer-events: none;
    }
    .welcome-card::before {
        content: '';
        position: absolute;
        bottom: -50px;
        right: 80px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        pointer-events: none;
    }
    .stat-card-v2 {
        border-radius: 18px;
        border: none;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
        transition: all 0.25s ease;
        overflow: hidden;
        height: 100%;
    }
    .stat-card-v2:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }
    .stat-icon-circle {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .action-btn-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 22px 18px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        transition: all 0.25s ease;
        text-decoration: none !important;
        color: #334155;
    }
    .action-btn-card:hover {
        transform: translateY(-4px);
        border-color: #00a15d;
        box-shadow: 0 8px 25px rgba(0, 161, 93, 0.15);
        color: #00a15d;
    }
    .action-btn-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: #f0fdf4;
        color: #00a15d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 12px;
        transition: all 0.25s ease;
    }
    .action-btn-card:hover .action-btn-icon {
        background: #00a15d;
        color: #ffffff;
    }
    .status-badge {
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="welcome-card">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-white text-success px-3 py-1 rounded-pill font-w600 me-2">Admin Lembaga</span>
                        <span class="text-white-50 fs-13"><i class="far fa-calendar-alt me-1"></i> {{ date('F Y') }}</span>
                    </div>
                    <h2 class="text-white font-w700 mb-1">
                        Selamat Datang, {{ Auth::user()->username }}! 👋
                    </h2>
                    <p class="text-white mb-0" style="opacity: 0.9; font-size: 15px;">
                        Lembaga: <strong>{{ $lembaga ? $lembaga->nama_lembaga : 'Internal Lembaga' }}</strong>
                        @if($lembaga && $lembaga->nama_pimpinan)
                            &bull; Pimpinan: <em>{{ $lembaga->nama_pimpinan }}</em>
                        @endif
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <span class="badge bg-white text-dark py-2 px-3 rounded-pill shadow-sm">
                        <i class="fas fa-check-circle text-success me-1"></i> Data Terisolasi Lembaga
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 4 KARTU STATISTIK UTAMA -->
<div class="row mb-4">
    <!-- Total Struktural -->
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card stat-card-v2">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <span class="fs-13 font-w500 text-muted d-block mb-1">Staf Struktural</span>
                    <h2 class="fs-32 font-w700 mb-0 text-dark">{{ $totalStruktural }}</h2>
                    <small class="text-success font-w600"><i class="fas fa-user-check me-1"></i>Aktif</small>
                </div>
                <div class="stat-icon-circle" style="background: rgba(0, 161, 93, 0.1); color: #00a15d;">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Tenaga Pengajar -->
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card stat-card-v2">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <span class="fs-13 font-w500 text-muted d-block mb-1">Tenaga Pengajar</span>
                    <h2 class="fs-32 font-w700 mb-0 text-dark">{{ $totalPengajar }}</h2>
                    <small class="text-info font-w600"><i class="fas fa-chalkboard-teacher me-1"></i>Guru / Dosen</small>
                </div>
                <div class="stat-icon-circle" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Blanko Terkini -->
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card stat-card-v2">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <span class="fs-13 font-w500 text-muted d-block mb-1">Blanko Terakhir</span>
                    <h3 class="fs-22 font-w700 mb-0 text-dark">
                        {{ $kehadiranStrukturalTerbaru ? $kehadiranStrukturalTerbaru->bulan . ' ' . $kehadiranStrukturalTerbaru->tahun : '-' }}
                    </h3>
                    <small class="font-w600 {{ $kehadiranStrukturalTerbaru && in_array(strtolower($kehadiranStrukturalTerbaru->status), ['selesai', 'validasi']) ? 'text-success' : 'text-warning' }}">
                        <i class="fas fa-dot-circle me-1"></i>{{ $kehadiranStrukturalTerbaru ? ucfirst($kehadiranStrukturalTerbaru->status) : 'Belum Ada' }}
                    </small>
                </div>
                <div class="stat-icon-circle" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i class="fas fa-clipboard-check"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Potongan Terdata -->
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card stat-card-v2">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <span class="fs-13 font-w500 text-muted d-block mb-1">Total Potongan Lembaga</span>
                    <h3 class="fs-20 font-w700 mb-0 text-danger">Rp {{ number_format($totalNominalPotongan, 0, ',', '.') }}</h3>
                    <small class="text-muted"><i class="fas fa-wallet me-1"></i>Struktural & Pengajar</small>
                </div>
                <div class="stat-icon-circle" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TOMBOL AKSI CEPAT (QUICK ACTIONS) -->
<div class="row mb-4">
    <div class="col-12">
        <h4 class="fs-18 font-w600 text-dark mb-3"><i class="fas fa-bolt text-warning me-2"></i>Aksi Cepat Menu Utama</h4>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <a href="{{ url('/kehadiran_struktural') }}" class="action-btn-card">
            <div class="action-btn-icon">
                <i class="fas fa-user-clock"></i>
            </div>
            <strong class="fs-14">Kehadiran Struktural</strong>
            <small class="text-muted mt-1">Input / Cek Blanko Absensi</small>
        </a>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <a href="{{ url('/kehadiran_pengajar') }}" class="action-btn-card">
            <div class="action-btn-icon" style="background: #e0f2fe; color: #0284c7;">
                <i class="fas fa-chalkboard"></i>
            </div>
            <strong class="fs-14">Kehadiran Pengajar</strong>
            <small class="text-muted mt-1">Input / Cek Jam Mengajar</small>
        </a>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <a href="{{ url('/potongan_umana') }}" class="action-btn-card">
            <div class="action-btn-icon" style="background: #fef2f2; color: #dc2626;">
                <i class="fas fa-receipt"></i>
            </div>
            <strong class="fs-14">Potongan Struktural</strong>
            <small class="text-muted mt-1">Kelola Potongan Staf</small>
        </a>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <a href="{{ url('/potongan_pengajar') }}" class="action-btn-card">
            <div class="action-btn-icon" style="background: #faf5ff; color: #9333ea;">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <strong class="fs-14">Potongan Pengajar</strong>
            <small class="text-muted mt-1">Kelola Potongan Guru</small>
        </a>
    </div>
</div>

<!-- DUA WIDGET ACTIONABLE -->
<div class="row">
    <!-- Status Blanko Kehadiran Lembaga -->
    <div class="col-xl-6 mb-4">
        <div class="card h-100 shadow-sm border-0" style="border-radius: 18px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w700 mb-1 text-dark">Status Blanko Terakhir</h4>
                    <p class="fs-13 text-muted mb-0">Pemantauan blanko kehadiran terkini di lembaga Anda</p>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <!-- Item Struktural -->
                <div class="p-3 mb-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="me-3 p-2 rounded-circle" style="background: #e0fdf4; color: #00a15d; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div>
                                <h5 class="fs-15 font-w600 mb-0">Kehadiran Struktural</h5>
                                <small class="text-muted">
                                    Periode: <strong>{{ $kehadiranStrukturalTerbaru ? $kehadiranStrukturalTerbaru->bulan . ' ' . $kehadiranStrukturalTerbaru->tahun : 'Belum Ada' }}</strong>
                                </small>
                            </div>
                        </div>
                        <div class="text-end">
                            @if($kehadiranStrukturalTerbaru)
                                @php
                                    $st = strtolower($kehadiranStrukturalTerbaru->status);
                                    $bg = $st === 'selesai' ? 'bg-success text-white' : ($st === 'sudah' ? 'bg-primary text-white' : 'bg-warning text-dark');
                                @endphp
                                <span class="status-badge {{ $bg }}">{{ ucfirst($kehadiranStrukturalTerbaru->status) }}</span>
                            @else
                                <span class="status-badge bg-secondary text-white">-</span>
                            @endif
                            <div class="mt-2">
                                <a href="{{ url('/kehadiran_struktural') }}" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 fs-12">
                                    Buka <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Item Pengajar -->
                <div class="p-3 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="me-3 p-2 rounded-circle" style="background: #e0f2fe; color: #0284c7; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div>
                                <h5 class="fs-15 font-w600 mb-0">Kehadiran Pengajar</h5>
                                <small class="text-muted">
                                    Periode: <strong>{{ $kehadiranPengajarTerbaru ? $kehadiranPengajarTerbaru->bulan . ' ' . $kehadiranPengajarTerbaru->tahun : 'Belum Ada' }}</strong>
                                </small>
                            </div>
                        </div>
                        <div class="text-end">
                            @if($kehadiranPengajarTerbaru)
                                @php
                                    $stP = strtolower($kehadiranPengajarTerbaru->status);
                                    $bgP = $stP === 'selesai' ? 'bg-success text-white' : ($stP === 'sudah' ? 'bg-primary text-white' : 'bg-warning text-dark');
                                @endphp
                                <span class="status-badge {{ $bgP }}">{{ ucfirst($kehadiranPengajarTerbaru->status) }}</span>
                            @else
                                <span class="status-badge bg-secondary text-white">-</span>
                            @endif
                            <div class="mt-2">
                                <a href="{{ url('/kehadiran_pengajar') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fs-12">
                                    Buka <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Input Potongan Terbaru -->
    <div class="col-xl-6 mb-4">
        <div class="card h-100 shadow-sm border-0" style="border-radius: 18px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w700 mb-1 text-dark">Potongan Lembaga Terbaru</h4>
                    <p class="fs-13 text-muted mb-0">Catatan potongan yang diinput pada lembaga ini</p>
                </div>
                <a href="{{ url('/potongan_umana') }}" class="btn btn-sm btn-link text-success font-w600">
                    Lihat Semua <i class="fas fa-chevron-right ms-1"></i>
                </a>
            </div>
            <div class="card-body px-4 pb-4">
                @if($recentPotongan->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless align-middle mb-0">
                            <thead>
                                <tr class="text-muted fs-12" style="border-bottom: 1px solid #f1f5f9;">
                                    <th>NAMA</th>
                                    <th>JENIS</th>
                                    <th class="text-end">NOMINAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPotongan as $pot)
                                <tr style="border-bottom: 1px solid #f8fafc;">
                                    <td class="py-2">
                                        <strong class="fs-13 text-dark d-block">{{ $pot['nama'] }}</strong>
                                        <span class="badge badge-xs {{ $pot['kategori'] === 'Struktural' ? 'bg-light text-dark' : 'bg-info-light text-info' }}">{{ $pot['kategori'] }}</span>
                                    </td>
                                    <td class="py-2 fs-13 text-muted">{{ $pot['jenis'] }}</td>
                                    <td class="py-2 text-end font-w600 text-danger fs-13">
                                        Rp {{ number_format($pot['nominal'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-3x mb-2" style="opacity: 0.3;"></i>
                        <p class="mb-0 fs-14">Belum ada riwayat potongan yang diinput di lembaga ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
