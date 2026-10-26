<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard - E-Barokah')</title>
    
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/p2s2_fav.png') }}?v={{ filemtime(public_path('images/p2s2_fav.png')) }}" />
    <link rel="icon" type="image/png" href="{{ asset('images/p2s2_fav.png') }}?v={{ filemtime(public_path('images/p2s2_fav.png')) }}" />
    <link href="{{ asset('vendor/jquery-nice-select/css/nice-select.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/owl-carousel/owl.carousel.css') }}" rel="stylesheet">
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div id="preloader">
        <div class="lds-ripple">
            <div></div>
            <div></div>
        </div>
    </div>
    
    <div id="main-wrapper">
        @include('partials.nav-header')
        @include('partials.header')
        @include('partials.sidebar')
        
        <div class="content-body">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
        
        @include('partials.footer')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('vendor/chart.js/Chart.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-nice-select/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('js/custom.min.js') }}"></script>
    <script src="{{ asset('js/dlabnav-init.js') }}"></script>
    @stack('scripts')
</body>
</html>
