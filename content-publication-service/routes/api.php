<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContentController;

/*
|--------------------------------------------------------------------------
| API Routes untuk Content & Publication Service
|--------------------------------------------------------------------------
*/

// =========================================================
// BUNGKUS SEMUA RUTE DENGAN PREFIX 'content'
// (Agar terbaca sebagai /api/content/... oleh API Gateway)
// =========================================================
Route::prefix('content')->group(function () {

    // ---------------------------------------------------------
    // 1. RUTE PUBLIK & JEMAAT (Read-Only)
    // ---------------------------------------------------------
    
    // Rute untuk Jemaat Aktif membaca Renungan Harian
    Route::get('devotionals', [ContentController::class, 'getRenunganJemaat']);
    
    // Rute Publik untuk menampilkan foto di halaman GaleriKegiatanPage.jsx
    Route::get('galeri', [ContentController::class, 'getPublicGaleri']);


    // ---------------------------------------------------------
    // 2. RUTE KHUSUS ADMIN / PENDETA (CRUD)
    // ---------------------------------------------------------
    Route::prefix('admin')->group(function () {
        
        // --- CRUD Pengumuman ---
        Route::get('pengumuman', [ContentController::class, 'getAllPengumuman']);
        Route::post('pengumuman', [ContentController::class, 'storePengumuman']);
        Route::put('pengumuman/{id}', [ContentController::class, 'updatePengumuman']);
        Route::delete('pengumuman/{id}', [ContentController::class, 'deletePengumuman']);

        // --- CRUD Renungan ---
        Route::get('renungan', [ContentController::class, 'getAllRenungan']);
        Route::post('renungan', [ContentController::class, 'storeRenungan']);
        Route::put('renungan/{id}', [ContentController::class, 'updateRenungan']);
        Route::delete('renungan/{id}', [ContentController::class, 'deleteRenungan']);

        // --- CRUD Galeri ---
        Route::get('galeri', [ContentController::class, 'getAllGaleri']);
        Route::post('galeri', [ContentController::class, 'storeGaleri']);
        
        // PENTING: Gunakan POST untuk update agar file gambar (FormData) tidak ditolak oleh Laravel
        Route::post('galeri/{id}', [ContentController::class, 'updateGaleri']); 
        Route::delete('galeri/{id}', [ContentController::class, 'deleteGaleri']);
        
    });

});