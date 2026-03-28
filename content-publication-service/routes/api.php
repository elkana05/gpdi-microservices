<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\ProfilGerejaController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\GaleriController;
use App\Http\Controllers\InformasiPelayananController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\RenunganHarianController;

/*
|--------------------------------------------------------------------------
| Public Routes — dapat diakses siapa saja tanpa login
|--------------------------------------------------------------------------
*/
Route::get('/beranda', [BerandaController::class, 'index']);
Route::get('/profil-gereja', [ProfilGerejaController::class, 'index']);
Route::get('/jadwal', [JadwalController::class, 'index']);
Route::get('/jadwal/{id}', [JadwalController::class, 'show']);
Route::get('/galeri', [GaleriController::class, 'index']);
Route::get('/galeri/{id}', [GaleriController::class, 'show']);
Route::get('/informasi-pelayanan', [InformasiPelayananController::class, 'index']);
Route::get('/informasi-pelayanan/{id}', [InformasiPelayananController::class, 'show']);
Route::get('/pengumuman', [PengumumanController::class, 'indexPublik']);

/*
|--------------------------------------------------------------------------
| Jemaat Routes — memerlukan login sebagai jemaat
|--------------------------------------------------------------------------
*/
Route::middleware('role:jemaat,ketua_rayon,pendeta')->group(function () {
    Route::get('/pengumuman/jemaat', [PengumumanController::class, 'indexJemaat']);
    Route::get('/pengumuman/rayon/{rayon_id}', [PengumumanController::class, 'indexRayon']);
    Route::get('/renungan', [RenunganHarianController::class, 'index']);
    Route::get('/renungan/hari-ini', [RenunganHarianController::class, 'hariIni']);
    Route::get('/renungan/{id}', [RenunganHarianController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| Ketua Rayon Routes
|--------------------------------------------------------------------------
*/
Route::middleware('role:ketua_rayon,pendeta')->group(function () {
    Route::post('/pengumuman', [PengumumanController::class, 'store']);
    Route::put('/pengumuman/{id}', [PengumumanController::class, 'update']);
    Route::delete('/pengumuman/{id}', [PengumumanController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Pendeta / Admin Routes — akses penuh
|--------------------------------------------------------------------------
*/
Route::middleware('role:pendeta')->group(function () {
    // Profil Gereja
    Route::put('/profil-gereja', [ProfilGerejaController::class, 'update']);

    // Jadwal
    Route::post('/jadwal', [JadwalController::class, 'store']);
    Route::put('/jadwal/{id}', [JadwalController::class, 'update']);
    Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy']);

    // Galeri
    Route::post('/galeri', [GaleriController::class, 'store']);
    Route::delete('/galeri/{id}', [GaleriController::class, 'destroy']);

    // Informasi Pelayanan
    Route::post('/informasi-pelayanan', [InformasiPelayananController::class, 'store']);
    Route::put('/informasi-pelayanan/{id}', [InformasiPelayananController::class, 'update']);
    Route::delete('/informasi-pelayanan/{id}', [InformasiPelayananController::class, 'destroy']);

    // Pengumuman (semua data termasuk draft)
    Route::get('/pengumuman/semua', [PengumumanController::class, 'indexAll']);
    Route::get('/pengumuman/{id}', [PengumumanController::class, 'show']);

    // Renungan Harian
    Route::get('/renungan/semua', [RenunganHarianController::class, 'indexAll']);
    Route::post('/renungan', [RenunganHarianController::class, 'store']);
    Route::put('/renungan/{id}', [RenunganHarianController::class, 'update']);
    Route::delete('/renungan/{id}', [RenunganHarianController::class, 'destroy']);
});
