<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return redirect()->route('products.index');
})->name('dashboard');

// Route Resource paket lengkap (index, create, store, edit, update, destroy)
Route::resource('products', ProductController::class);


use App\Http\Controllers\TransactionController;

// Route untuk Kasir
Route::get('/pos', [TransactionController::class, 'create'])->name('transactions.create');
Route::post('/pos', [TransactionController::class, 'store'])->name('transactions.store');
Route::get('/transactions/{id}/print', [TransactionController::class, 'show'])->name('transactions.show');


use App\Http\Controllers\ReportController;

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');