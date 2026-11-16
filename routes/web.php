<?php

use App\Http\Controllers\AlatController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DetailPeminjamanController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LogAktivitasController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::middleware('role:admin,petugas')->group(function (): void {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        Route::get('/alats', [AlatController::class, 'index'])->name('alats.index');
        Route::get('/alats/create', [AlatController::class, 'create'])->name('alats.create');
        Route::post('/alats', [AlatController::class, 'store'])->name('alats.store');
        Route::get('/alats/{alat}/edit', [AlatController::class, 'edit'])->name('alats.edit');
        Route::match(['put', 'patch'], '/alats/{alat}', [AlatController::class, 'update'])->name('alats.update');
    });

    Route::middleware('role:admin')->group(function (): void {
        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('kategoris', KategoriController::class)->except(['show']);
        Route::delete('/alats/{alat}', [AlatController::class, 'destroy'])->name('alats.destroy');

        Route::get('/peminjamans/create', [PeminjamanController::class, 'create'])->name('peminjamans.create');
        Route::post('/peminjamans', [PeminjamanController::class, 'store'])->name('peminjamans.store');
        Route::get('/peminjamans/{peminjaman}/edit', [PeminjamanController::class, 'edit'])->name('peminjamans.edit');
        Route::match(['put', 'patch'], '/peminjamans/{peminjaman}', [PeminjamanController::class, 'update'])->name('peminjamans.update');
        Route::delete('/peminjamans/{peminjaman}', [PeminjamanController::class, 'destroy'])->name('peminjamans.destroy');

        Route::get('/pengembalians/create', [PengembalianController::class, 'create'])->name('pengembalians.create');
        Route::post('/pengembalians', [PengembalianController::class, 'store'])->name('pengembalians.store');
        Route::get('/pengembalians/{pengembalian}/edit', [PengembalianController::class, 'edit'])->name('pengembalians.edit');
        Route::match(['put', 'patch'], '/pengembalians/{pengembalian}', [PengembalianController::class, 'update'])->name('pengembalians.update');
        Route::delete('/pengembalians/{pengembalian}', [PengembalianController::class, 'destroy'])->name('pengembalians.destroy');

        Route::get('/log-aktivitas', [LogAktivitasController::class, 'index'])->name('log-aktivitas.index');
    });

    Route::middleware('role:admin,petugas')->group(function (): void {
        Route::get('/peminjamans', [PeminjamanController::class, 'index'])->name('peminjamans.index');
        Route::post('/peminjamans/{peminjaman}/keputusan', [PeminjamanController::class, 'decide'])->name('peminjamans.decide');

        Route::get('/pengembalians', [PengembalianController::class, 'index'])->name('pengembalians.index');
        Route::post('/pengembalians/{pengembalian}/verifikasi', [PengembalianController::class, 'verify'])->name('pengembalians.verify');

        Route::get('/detail-peminjamans', [DetailPeminjamanController::class, 'index'])->name('detail-peminjamans.index');
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    });

    Route::middleware('role:peminjam')->group(function (): void {
        Route::get('/katalog-alat', [AlatController::class, 'catalog'])->name('katalog-alat.index');
        Route::get('/ajukan-peminjaman', [PeminjamanController::class, 'requestCreate'])->name('peminjaman-ajukan.create');
        Route::post('/ajukan-peminjaman', [PeminjamanController::class, 'requestStore'])->name('peminjaman-ajukan.store');
        Route::get('/peminjaman-saya', [PeminjamanController::class, 'myIndex'])->name('peminjaman-saya');

        Route::get('/pengembalian-saya', [PengembalianController::class, 'myIndex'])->name('pengembalian-saya');
        Route::post('/pengembalian-saya', [PengembalianController::class, 'storeByPeminjam'])->name('pengembalian-saya.store');
    });
});