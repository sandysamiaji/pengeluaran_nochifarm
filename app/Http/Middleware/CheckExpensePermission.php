<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ExpensePermissionService;
use Symfony\Component\HttpFoundation\Response;

class CheckExpensePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $permissionKey = null): Response
    {
        $user = Auth::user();

        // 1. Wajib terautentikasi
        if (!$user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Sesi Anda telah berakhir. Silakan login kembali.'], 401);
            }
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses sistem.');
        }

        // 2. Akun non-aktif langsung ditolak
        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Akun dinonaktifkan oleh Administrator.'], 403);
            }
            return redirect()->route('login')->with('error', 'Akun Anda dinonaktifkan oleh Administrator.');
        }

        // 3. Administrator selalu lolos tanpa hambatan
        if ($user->role === 'admin') {
            return $next($request);
        }

        // 4. Verifikasi izin umum login ke aplikasi pengeluaran
        if (!ExpensePermissionService::canLogin($user)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses Ditolak: Akun Anda belum diizinkan oleh Administrator untuk mengakses Sistem Pengeluaran.'
                ], 403);
            }

            return redirect()->route('login')->with('error', 'Akses Ditolak: Akun Anda belum diizinkan oleh Administrator untuk mengakses Sistem Pengeluaran.');
        }

        // 5. Jika ada permissionKey spesifik yang diperiksa
        if ($permissionKey && !ExpensePermissionService::canAccess($user, $permissionKey)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses Ditolak: Anda tidak memiliki izin untuk fitur/tindakan ini.'
                ], 403);
            }

            // Jika rute saat ini adalah dashboard itu sendiri dan ditolak, fallback aman
            if ($request->routeIs('dashboard')) {
                return response()->view('errors.403', [
                    'message' => 'Anda tidak memiliki hak akses untuk melihat Dashboard. Hubungi Administrator untuk konfigurasi izin.'
                ], 403);
            }

            return redirect()->route('dashboard')->with('error', 'Akses Ditolak: Anda tidak memiliki hak akses untuk tindakan atau halaman tersebut.');
        }

        return $next($request);
    }
}
