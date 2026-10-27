<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        if (!$user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Sesi login telah berakhir.'], 401);
            }
            return redirect()->guest(route('login'));
        }

        // Normalisasi role user
        $userRole = strtolower(str_replace(' ', '', (string)$user->jabatan));

        // Jika user adalah superadmin, izinkan akses ke seluruh role
        if (in_array($userRole, ['superadmin', 'admin', 'root'])) {
            return $next($request);
        }

        // Normalisasi daftar role yang diperbolehkan
        $allowedRoles = array_map(function ($r) {
            return strtolower(str_replace(' ', '', trim($r)));
        }, $roles);

        if (!in_array($userRole, $allowedRoles)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Akses ditolak. Anda tidak memiliki wewenang untuk tindakan ini.'
                ], 403);
            }

            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
