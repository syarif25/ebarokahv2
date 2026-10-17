@extends('layouts.app')

@section('title', 'Dashboard - E-Barokah')
@section('page_title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-2 text-nowrap">Total Karyawan</h4>
                    <h2 class="fs-32 font-w700 mb-0 text-primary">124</h2>
                </div>
                <i class="fas fa-users fa-3x text-primary" style="opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-2 text-nowrap">Gaji Bulan Ini</h4>
                    <h2 class="fs-24 font-w700 mb-0 text-success">Rp 450 Jt</h2>
                </div>
                <i class="fas fa-money-bill-wave fa-3x text-success" style="opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-2 text-nowrap">Belum Terbayar</h4>
                    <h2 class="fs-24 font-w700 mb-0 text-danger">0</h2>
                </div>
                <i class="fas fa-exclamation-circle fa-3x text-danger" style="opacity: 0.5;"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="card">
            <div class="card-body d-flex px-4 justify-content-between align-items-center">
                <div>
                    <h4 class="fs-18 font-w600 mb-2 text-nowrap">Departemen</h4>
                    <h2 class="fs-32 font-w700 mb-0 text-warning">8</h2>
                </div>
                <i class="fas fa-building fa-3x text-warning" style="opacity: 0.5;"></i>
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
