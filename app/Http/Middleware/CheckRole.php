<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2. Privilege Admin: Admin boleh akses ke mana saja (Super User)
        if ($user->role === 'admin') {
            return $next($request);
        }

        // 3. Cek apakah role user ada di dalam daftar yang diizinkan
        // Contoh pemakaian di route: middleware('role:owner,cashier')
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // 4. Jika tidak cocok, tolak akses (403 Forbidden)
        abort(403, 'Akses Ditolak! Anda tidak memiliki izin untuk masuk ke halaman ini.');
    }
}
