<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Pengguna;
use App\Models\Umana;

class AuthController extends Controller
{
    public function login()
    {
        if (Auth::guard('web')->check()) {
            return redirect('/dashboard');
        }
        if (Auth::guard('umana')->check()) {
            return redirect('/Report');
        }
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // 1. Coba login sebagai Pengguna (SuperAdmin, Evaluasi, AdminLembaga)
        if (Auth::guard('web')->attempt(['username' => $request->username, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        // 2. Coba login sebagai Umana (PWA) via Nomor HP atau NIK
        $cleanInput = trim($request->username);
        $umana = Umana::where('nomor_hp', $cleanInput)
            ->orWhere('nik', $cleanInput)
            ->first();

        if ($umana && Hash::check($request->password, $umana->password)) {
            Auth::guard('umana')->login($umana, $request->boolean('remember'));
            $request->session()->regenerate();
            $request->session()->put([
                'username' => $umana->nama_lengkap,
                'nik'      => $umana->nik,
                'jk'       => $umana->jk,
                'jabatan'  => 'umana',
            ]);
            return redirect()->intended('/Report');
        }

        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('umana')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
