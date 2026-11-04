@extends('layouts.auth')

@section('title', 'Login - E-Barokah')

@push('styles')
<style>
    /* Styling Border Hijau Khas E-Barokah */
    .auth-form .form-control {
        border: 1.5px solid #00a15d !important;
        border-radius: 12px;
        height: 48px;
        padding: 10px 16px;
        font-size: 14px;
        transition: all 0.2s ease;
        background-color: #ffffff;
    }
    .auth-form .form-control:focus {
        border-color: #00874e !important;
        box-shadow: 0 0 0 3px rgba(0, 161, 93, 0.2) !important;
        outline: none;
    }
    
    /* Input Group Password Terpadu dengan Border Hijau */
    .auth-form .input-group {
        border: 1.5px solid #00a15d !important;
        border-radius: 12px !important;
        overflow: hidden;
        transition: all 0.2s ease;
        background-color: #ffffff;
    }
    .auth-form .input-group:focus-within {
        border-color: #00874e !important;
        box-shadow: 0 0 0 3px rgba(0, 161, 93, 0.2) !important;
    }
    .auth-form .input-group .form-control {
        border: none !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        height: 48px;
    }
    .auth-form .input-group .btn-toggle-eye {
        border: none !important;
        background-color: transparent !important;
        color: #64748b;
        padding: 0 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: color 0.2s;
    }
    .auth-form .input-group .btn-toggle-eye:hover {
        color: #00a15d;
    }

    .auth-form .btn-primary {
        background-color: #00a15d !important;
        border-color: #00a15d !important;
        border-radius: 12px !important;
        height: 48px;
        font-size: 15px;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    .auth-form .btn-primary:hover {
        background-color: #00874e !important;
        border-color: #00874e !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 161, 93, 0.3) !important;
    }
    .auth-form .custom-checkbox .form-check-input:checked {
        background-color: #00a15d !important;
        border-color: #00a15d !important;
    }
</style>
@endpush

@section('content')
<div class="col-md-6">
    <div class="authincation-content">
        <div class="row no-gutters">
            <div class="col-xl-12">
                <div class="auth-form">
                    <div class="text-center mb-3">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('images/logo-ebarokah.jpg') }}" alt="Logo E-Barokah" style="max-width: 250px; height: auto;">
                        </a>
                    </div>
                    <h4 class="text-center mb-4">Masuk ke Akun Anda</h4>

                    @if ($errors->any())
                        <div class="alert alert-danger shadow-sm">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ url('/login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="mb-1"><strong>Username</strong></label>
                            <input type="text" class="form-control" name="username" value="{{ old('username') }}" placeholder="Masukkan username">
                        </div>
                        <div class="mb-3">
                            <label class="mb-1"><strong>Password</strong></label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="password" id="inputPassword" placeholder="Masukkan password" required>
                                <button class="btn btn-toggle-eye" type="button" id="btnTogglePassword">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row d-flex justify-content-between mt-4 mb-2">
                            <div class="mb-3">
                                <div class="form-check custom-checkbox ms-1">
                                    <input type="checkbox" class="form-check-input" id="basic_checkbox_1" name="remember">
                                    <label class="form-check-label" for="basic_checkbox_1">Ingat Saya</label>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var btnToggle = document.getElementById('btnTogglePassword');
        var inputPass = document.getElementById('inputPassword');
        var icon = document.getElementById('toggleIcon');

        if (btnToggle && inputPass && icon) {
            btnToggle.addEventListener('click', function () {
                var isPassword = inputPass.getAttribute('type') === 'password';
                if (isPassword) {
                    inputPass.setAttribute('type', 'text');
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    inputPass.setAttribute('type', 'password');
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        }
    });
</script>
@endpush
