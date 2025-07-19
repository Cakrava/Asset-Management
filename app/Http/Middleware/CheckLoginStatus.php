<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckLoginStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah pengguna sudah login
        if (!Auth::check()) {
            // Jika belum login, arahkan ke halaman login
            return redirect()->route('auth.login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        // Jika sudah login, lanjutkan ke permintaan berikutnya
        return $next($request);
    }
}
