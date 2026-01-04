<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Dengan Keamanan RBAC)
|--------------------------------------------------------------------------
*/

// 1. Rute Tamu (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

// 2. Rute Terproteksi (Harus Login)
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard Redirector (Mengarahkan user ke halaman defaultnya jika akses root)
    Route::get('/', function () {
        $role = Auth::user()->role;
        if ($role == 'cashier') {
            return redirect()->route('transactions.create');
        }

        if ($role == 'owner') {
            return redirect()->route('reports.index');
        }

        return redirect()->route('products.index');
    })->name('dashboard');

    // --- AREA ADMIN (Kelola Produk) ---
    // Hanya Admin yang boleh tambah/edit/hapus produk
    Route::middleware('role:admin')->group(function () {
        Route::resource('products', ProductController::class);
    });

    // --- AREA KASIR (Transaksi) ---
    // Admin juga kita beri akses (admin,cashier) supaya bisa memantau/test
    Route::middleware('role:admin,cashier')->group(function () {
        Route::get('/pos', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('/pos', [TransactionController::class, 'store'])->name('transactions.store');
        Route::get('/transactions/{id}/print', [TransactionController::class, 'show'])->name('transactions.show');
    });

    // --- AREA PEMILIK (Laporan) ---
    // Admin dan Owner boleh lihat laporan
    Route::middleware('role:admin,owner')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

});
