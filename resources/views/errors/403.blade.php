<!DOCTYPE html>
<html lang="id" class="h-100">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Dibatasi | E-Barokah P2S3</title>
    
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/p2s2_fav.png') }}" />
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
</head>

<body class="vh-100">
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6 col-lg-5">
                    <div class="form-input-content text-center error-page py-4">
                        <h1 class="error-text fw-bold text-primary mb-2" style="font-size: 6rem; line-height: 1;">403</h1>
                        <h4 class="mb-3 font-weight-bold">
                            <i class="fa fa-times-circle text-danger mr-1"></i> Akses Dibatasi!
                        </h4>
                        <p class="text-muted mb-4 px-2">
                            {{ $exception->getMessage() ?: 'Anda tidak memiliki hak akses untuk membuka atau mengelola data ini.' }}
                        </p>
                        <div class="d-flex justify-content-center" style="gap: 10px;">
                            <a class="btn btn-outline-secondary px-3" href="javascript:history.back()">
                                <i class="fa fa-arrow-left mr-1"></i> Kembali
                            </a>
                            <a class="btn btn-primary px-3" href="{{ url('/dashboard') }}">
                                <i class="fa fa-home mr-1"></i> Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts bawaan tema -->
    <script src="{{ asset('vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('js/custom.min.js') }}"></script>
    <script src="{{ asset('js/dlabnav-init.js') }}"></script>
</body>
</html>
