<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="description" content="Sistem Informasi Barokah & Payroll Internal Pesantren Salafiyah Syafi'iyah Sukorejo (P2S3)">
    <meta name="author" content="P2S3">
    <meta name="theme-color" content="#1ea566">

    <!-- Open Graph (WhatsApp / Chat Preview) -->
    <meta property="og:title" content="E-Barokah P2S3">
    <meta property="og:description" content="Sistem Informasi Barokah & Payroll Internal P2S3">
    <meta property="og:type" content="website">
    <meta property="og:image" content="{{ asset('images/p2s2_fav.png') }}">

	<title>@yield('title', 'E-Barokah P2S3 - Sistem Informasi Barokah')</title>
	
	<!-- FAVICONS ICON -->
	<link rel="shortcut icon" type="image/png" href="{{ asset('images/p2s2_fav.png') }}?v={{ filemtime(public_path('images/p2s2_fav.png')) }}" />
	<link rel="icon" type="image/png" href="{{ asset('images/p2s2_fav.png') }}?v={{ filemtime(public_path('images/p2s2_fav.png')) }}" />
    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    @stack('styles')
</head>

<body class="vh-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Required vendors -->
    <script src="{{ asset('vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('js/custom.min.js') }}"></script>
    <script src="{{ asset('js/dlabnav-init.js') }}"></script>
    @stack('scripts')
</body>
</html>
