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

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header border-0">
                <h4 class="fs-20 font-w700">Aktivitas Penggajian Terakhir</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    Belum ada aktivitas penggajian yang dilakukan baru-baru ini. Silakan masuk ke menu <strong>Penggajian</strong> untuk memulai.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
