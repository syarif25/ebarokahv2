<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta
      name="apple-mobile-web-app-status-bar-style"
      content="black-translucent"
    />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover"
    />
    <meta name="theme-color" content="#185a9d" />
    <title>E-Barokah P2S3</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/styles/bootstrap.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/styles/style.css') }}" />
    <link rel="icon" href="{{ asset('assets/p2s2_fav.png') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ asset('assets/p2s2_fav.png') }}" type="image/x-icon">
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      type="text/css"
      href="{{ asset('assets/fonts/css/fontawesome-all.min.css') }}"
    />
    <link
      rel="manifest"
      href="{{ asset('assets/_manifest.json') }}"
      data-pwa-version="set_in_manifest_and_pwa_js"
    />
    <link
      rel="apple-touch-icon"
      sizes="180x180"
      href="{{ asset('assets/app/icons/icon-192x192.png') }}"
    />
  </head>

  <body class="theme-light">
    <div id="preloader">
      <div class="spinner-border color-highlight" role="status"></div>
    </div>

    @yield('content')

    <div id="modalMaintenance" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered d-flex align-items-center justify-content-center" 
            style="min-height: 100vh; margin: 0;">
            <div class="modal-content shadow-lg border-0" 
                style="border-radius: 18px; text-align: center; padding: 1.5rem; max-width: 320px; margin:auto;">
            <i class="fa fa-tools color-highlight" style="font-size: 42px; margin-bottom: 12px; color:#3366ff;"></i>
            <h5 style="font-weight: 700;">Sedang Dalam Pengembangan</h5>
            <p style="font-size: 14px; opacity: .8; margin-bottom: 1rem;">
                Fitur ini sedang diperbarui.<br>Silakan coba lagi nanti.
            </p>
            <button class="btn btn-primary w-100" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
    <script>
    var baseUrl = "{{ url('/') }}/"; 
    function showMaintenanceModal() {
        const modalEl = document.getElementById('modalMaintenance');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
    </script>
    <script type="text/javascript" src="{{ asset('assets/scripts/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/scripts/custom.js') }}"></script>
    @stack('scripts')
  </body>
</html>
