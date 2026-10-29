<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="theme-color" content="#1ea566">
    <title>Halaman Tidak Ditemukan (404) - E-Barokah P2S3</title>
    
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/p2s2_fav.png') }}" />
    <link rel="icon" type="image/png" href="{{ asset('images/p2s2_fav.png') }}" />
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

    <style>
        :root {
            --primary: #1ea566;
            --primary-dark: #15803d;
            --dark: #0f172a;
            --slate: #475569;
            --accent-blue: #3b82f6;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f0fdf4 0%, #f8fafc 50%, #eff6ff 100%);
            color: var(--dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            overflow-x: hidden;
            position: relative;
        }

        .ambient-glow {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.6;
            pointer-events: none;
            z-index: 1;
        }
        .glow-1 {
            width: 320px;
            height: 320px;
            background: #93c5fd;
            top: -60px;
            left: -60px;
        }
        .glow-2 {
            width: 360px;
            height: 360px;
            background: #86efac;
            bottom: -60px;
            right: -60px;
        }

        .error-card {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(226, 232, 240, 0.9);
            border-radius: 28px;
            box-shadow: 0 20px 45px -10px rgba(15, 23, 42, 0.08);
            max-width: 580px;
            width: 100%;
            padding: 48px 40px;
            text-align: center;
            animation: slideUpFade 0.45s ease-out;
        }

        @keyframes slideUpFade {
            from {
                opacity: 0;
                transform: translateY(24px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .brand-header {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #f1f5f9;
            padding: 8px 18px;
            border-radius: 100px;
            margin-bottom: 28px;
        }
        .brand-header img {
            width: 24px;
            height: 24px;
            object-fit: contain;
        }
        .brand-header span {
            font-size: 13px;
            font-weight: 700;
            color: var(--slate);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .icon-wrapper {
            position: relative;
            width: 104px;
            height: 104px;
            margin: 0 auto 28px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .icon-circle-pulse {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: #dbeafe;
            animation: pulseGlow 2.4s infinite cubic-bezier(0.4, 0, 0.6, 1);
        }
        .icon-circle-main {
            position: relative;
            width: 88px;
            height: 88px;
            border-radius: 50%;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 2px solid #bfdbfe;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 20px -4px rgba(59, 130, 246, 0.2);
        }
        .icon-circle-main i {
            font-size: 38px;
            color: var(--accent-blue);
        }

        @keyframes pulseGlow {
            0%, 100% { transform: scale(1); opacity: 0.7; }
            50% { transform: scale(1.15); opacity: 0.2; }
        }

        .error-badge {
            display: inline-block;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 13px;
            font-weight: 800;
            padding: 4px 14px;
            border-radius: 8px;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }

        .error-title {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 800;
            color: var(--dark);
            line-height: 1.25;
            margin-bottom: 12px;
        }

        .error-desc {
            font-size: 15px;
            line-height: 1.6;
            color: var(--slate);
            margin-bottom: 28px;
        }

        .btn-group-action {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
        }

        .btn-primary-action {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(30, 165, 102, 0.35);
        }
        .btn-primary-action:hover {
            background: linear-gradient(135deg, #168a52 0%, #106631 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(30, 165, 102, 0.45);
        }

        .btn-secondary-action {
            background: #ffffff;
            color: var(--slate);
            border: 1.5px solid #cbd5e1;
        }
        .btn-secondary-action:hover {
            background: #f1f5f9;
            color: var(--dark);
            border-color: #94a3b8;
            transform: translateY(-2px);
        }

        .footer-note {
            margin-top: 30px;
            font-size: 12px;
            color: #94a3b8;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="ambient-glow glow-1"></div>
    <div class="ambient-glow glow-2"></div>

    <div class="error-card">
        <div class="brand-header">
            <img src="{{ asset('images/p2s2_fav.png') }}" alt="Logo P2S3">
            <span>E-Barokah &bull; P2S3 Sukorejo</span>
        </div>

        <div class="icon-wrapper">
            <div class="icon-circle-pulse"></div>
            <div class="icon-circle-main">
                <i class="fas fa-search-location"></i>
            </div>
        </div>

        <span class="error-badge">ERROR 404 &bull; NOT FOUND</span>
        <h1 class="error-title">Halaman Tidak Ditemukan</h1>
        
        <p class="error-desc">
            {{ $exception->getMessage() ?: 'Halaman, data periode, atau tautan yang Anda tuju tidak tersedia atau telah dipindahkan.' }}
        </p>

        <div class="btn-group-action">
            <a href="javascript:history.back()" class="btn-action btn-secondary-action">
                <i class="fas fa-arrow-left"></i> Kembali Sebelumnya
            </a>
            <a href="{{ url('/dashboard') }}" class="btn-action btn-primary-action">
                <i class="fas fa-home"></i> Menuju Dashboard
            </a>
        </div>

        <p class="footer-note">
            &copy; {{ date('Y') }} Pesantren Salafiyah Syafi'iyah Sukorejo. All rights reserved.
        </p>
    </div>
</body>
</html>
