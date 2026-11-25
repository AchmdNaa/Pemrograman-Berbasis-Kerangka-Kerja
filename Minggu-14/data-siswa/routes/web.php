<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

// CRUD 1 — Eloquent: 7 route (index, create, store, show, edit, update, destroy)
Route::resource('siswa', SiswaController::class)->parameters(['siswa' => 'siswa']);

// CRUD 2 — Query Builder
Route::resource('kelas', KelasController::class)->only(['index', 'store', 'update', 'destroy'])->parameters(['kelas' => 'id']);
