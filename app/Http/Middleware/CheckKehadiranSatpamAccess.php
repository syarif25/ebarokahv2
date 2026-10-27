<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckKehadiranSatpamAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['status' => false, 'message' => 'Sesi login telah berakhir.'], 401);
            }
            return redirect()->guest(route('login'));
        }

        if ($user->canAccessKehadiranSatpam()) {
            return $next($request);
        }

        $msg = 'Akses ditolak. Menu kehadiran satpam khusus untuk pengelola Satpam.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => false, 'message' => $msg], 403);
        }

        abort(403, $msg);
    }
}
