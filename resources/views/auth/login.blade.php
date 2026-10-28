@extends('layouts.auth')

@section('title', 'Login - E-Barokah')

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
                                <button class="btn btn-outline-light border" type="button" id="btnTogglePassword" style="background-color: #f8fafc; color: #64748b;">
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
