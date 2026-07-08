<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TongController;
use App\Http\Controllers\LaporanController;
use App\Livewire\DaftarTong;


// ── Guest (belum login) ───────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login'])->name('login.post');
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// ── Auth (harus login) ────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Riwayat
    Route::get('/riwayat',   [DashboardController::class, 'riwayat'])->name('riwayat');

    // Daftar Tong
Route::get('/daftar-tong', function () {
    return view('daftar-tong-wrapper'); // atau taruh langsung isinya di resources/views/daftar-tong.blade.php
})->name('daftar-tong');
    Route::post('/daftar-tong',                     [TongController::class, 'store'])->name('tambah-tong');
    Route::delete('/daftar-tong/{kode}',            [TongController::class, 'destroy'])->name('hapus-tong');
    Route::post('/daftar-tong/{kode}/angkut',       [TongController::class, 'catat'])->name('catat-pengangkutan');

    // Notifikasi
    Route::get('/notifikasi', [DashboardController::class, 'notifikasi'])->name('notifikasi');

    // Laporan
    Route::get('/laporan',        [LaporanController::class, 'index'])->name('laporan');
    Route::get('/laporan/unduh',  [LaporanController::class, 'download'])->name('laporan.download');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ── ESP32 Sensor Endpoint ────────────────────────────────────
    Route::post('/api/sensor', [TongController::class, 'receiveSensor'])->name('api.sensor');
});
