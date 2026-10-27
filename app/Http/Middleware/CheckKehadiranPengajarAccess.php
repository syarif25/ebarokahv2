<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckKehadiranPengajarAccess
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

        if ($user->canAccessKehadiranPengajar()) {
            return $next($request);
        }

        $msg = 'Akses ditolak. Lembaga Anda tidak memiliki unit tenaga pengajar (guru/dosen).';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => false, 'message' => $msg], 403);
        }

        abort(403, $msg);
    }
}
