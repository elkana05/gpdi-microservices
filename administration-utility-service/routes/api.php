<?php

use App\Http\Controllers\AdminController;

Route::prefix('admin')->middleware(['auth.jwt', 'role:admin,pendeta'])->group(function () {
    // Layanan Surat
    Route::get('surat', [AdminController::class, 'getAllSurat']);
    Route::post('surat', [AdminController::class, 'storeSurat']);
    Route::put('surat/{id}', [AdminController::class, 'updateSurat']);
    Route::delete('surat/{id}', [AdminController::class, 'deleteSurat']);
});
