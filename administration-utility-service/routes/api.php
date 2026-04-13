<?php

use App\Http\Controllers\AdminController;

Route::prefix('admin')->group(function () {
    // Layanan Surat
    Route::get('surat', [AdminController::class, 'getAllSurat']);
    Route::post('surat', [AdminController::class, 'storeSurat']);
    Route::put('surat/{id}', [AdminController::class, 'updateSurat']);
    Route::delete('surat/{id}', [AdminController::class, 'deleteSurat']);

    // Notifikasi Sistem
    Route::get('notifikasi', [AdminController::class, 'getAllNotifikasi']);
    Route::post('notifikasi', [AdminController::class, 'storeNotifikasi']);
    Route::put('notifikasi/{id}', [AdminController::class, 'updateNotifikasi']);
    Route::delete('notifikasi/{id}', [AdminController::class, 'deleteNotifikasi']);
});
