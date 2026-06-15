<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureDashboardAuthenticated
 *
 * Middleware ini memproteksi semua route web dashboard.
 * Menggunakan web session guard (session-based auth).
 *
 * Cara kerja:
 *   - Jika user sudah login via sesi (web guard), lanjutkan request.
 *   - Jika tidak, redirect ke halaman login dengan pesan error.
 *
 * Scalable: Middleware ini dapat diperluas untuk menambahkan
 * pengecekan role (misalnya 'admin' atau 'staff') di masa mendatang
 * dengan menambahkan parameter $role pada method handle().
 */
class EnsureDashboardAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  ...$roles  Role yang diizinkan (opsional, untuk pengembangan ke depan)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Cek apakah user sudah terautentikasi via web session guard
        if (! auth('web')->check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu untuk mengakses dashboard.');
        }

        // Jika ada role yang dispesifikasikan, validasi role user
        if (! empty($roles)) {
            $userRole = auth('web')->user()->role ?? null;

            if (! in_array($userRole, $roles)) {
                abort(403, 'Anda tidak memiliki akses ke halaman ini.');
            }
        }

        return $next($request);
    }
}
