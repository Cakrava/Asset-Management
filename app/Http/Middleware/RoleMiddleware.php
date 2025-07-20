<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Jangan lupa import Auth

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  ...$roles  // Ini akan menangkap semua parameter role (misal: 'admin', 'master')
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        
        if (!Auth::check()) {
            return $next($request); // Lanjutkan saja, biarkan middleware lain yang bekerja
        }

        // 2. Ambil data pengguna yang sedang login.
        $user = Auth::user();

        if (in_array($user->role, $roles)) {
            // 4. Jika cocok, izinkan pengguna untuk melanjutkan ke request berikutnya.
            return $next($request);
        }

        abort(404, '');
    }
}
