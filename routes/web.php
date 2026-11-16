<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('kategoris', \App\Http\Controllers\KategoriController::class)->except(['show']);
// BEGIN auto-crud:alats
Route::resource('alats', \App\Http\Controllers\AlatController::class)->except(['show']);
// END auto-crud:alats
// BEGIN auto-crud:users
Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show']);
// END auto-crud:users
// BEGIN auto-crud:peminjamans
Route::resource('peminjamans', \App\Http\Controllers\PeminjamanController::class)->except(['show']);
// END auto-crud:peminjamans
