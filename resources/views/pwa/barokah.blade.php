@extends('pwa.template')

@section('content')
<div id="page">
    <div id="footer-bar" class="footer-bar-6">
        <a href="{{ url('/Report/Kehadiran') }}" class="active-nav">
            <i class="fa fa-file"></i><span>Kehadiran</span>
        </a>
        <a href="{{ url('/Report') }}" class="circle-nav">
            <i class="fa fa-home"></i><span>Home</span>
        </a>
        <a href="#" onclick="showMaintenanceModal()">
            <i class="fa fa-user"></i><span>Data Diri</span>
        </a>
    </div>

    <div class="page-content">    
        <div class="page-title">
            <h1>Kategori Barokah</h1>
        </div>    
                
        <div class="row mb-0">
            <div class="col pe-2">
                <a href="{{ url('Report/Barokah?k=struktural') }}" class="card card-style rounded-m me-0 p-3 mb-3">
                    <i class="fa fa-sitemap color-red-light font-40 icon-50"></i>
                    <h4 class="pt-3">Struktural</h4>
                    <p class="font-11 mt-n2 mb-0 color-highlight">Klik disini untuk detail</p>
                </a>
            </div>

            <div class="col ps-2">
                <a href="{{ url('Report/Barokah?k=pengajar') }}" class="card card-style rounded-m ms-0 p-3 mb-3">
                    <i class="fa fa-graduation-cap color-blue-light font-40 icon-50"></i>
                    <h4 class="pt-3">Pengajar</h4>
                    <p class="font-11 mt-n2 mb-0 color-highlight">Klik disini untuk detail</p>
                </a>
            </div>

            <div class="w-100"></div>

            <div class="col pe-2">
                <a href="{{ url('Report/Barokah?k=satpam') }}" class="card card-style rounded-m me-0 p-3 mb-3">
                    <i class="fa fa-shield color-magenta-light font-40 icon-50"></i>
                    <h4 class="pt-3">Satpam</h4>
                    <p class="font-11 mt-n2 mb-0 color-highlight">Klik disini untuk detail</p>
                </a>
            </div>

            <div class="col ps-2">
                <a href="#" onclick="showMaintenanceModal()" class="card card-style rounded-m ms-0 p-3 mb-3">
                    <i class="fa fa-users color-yellow-light font-40 icon-50"></i>
                    <h4 class="pt-3">Kepanitiaan</h4>
                    <p class="font-11 mt-n2 mb-0 color-highlight">Klik disini untuk detail</p>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
