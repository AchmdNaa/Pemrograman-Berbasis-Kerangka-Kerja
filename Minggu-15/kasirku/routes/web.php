<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Tamu
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Semua pengguna yang login (admin & kasir)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('/kasir', [PosController::class, 'index'])->name('pos.index');
    Route::post('/kasir', [PosController::class, 'store'])->name('pos.store');

    Route::get('/transaksi', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transaksi/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');

    // Khusus admin
    Route::middleware('role:admin')->group(function () {
        Route::resource('produk', ProductController::class)->except('show')->parameters(['produk' => 'product'])->names('products');
        Route::resource('kategori', CategoryController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['kategori' => 'category'])->names('categories');
        Route::resource('pengguna', UserController::class)->only(['index', 'store', 'update'])->parameters(['pengguna' => 'user'])->names('users');
        Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    });
});
