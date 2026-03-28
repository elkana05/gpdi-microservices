<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::prefix('admin')->middleware('auth.jwt')->group(function () {
    // Request Surat (Hanya read-only, mengembalikan link WA)
    Route::get('letter-options', [AdminController::class, 'getLetterOptions']);

    // Notifikasi
    Route::get('notifications', [AdminController::class, 'getNotifications']);
    Route::patch('notifications/{id}/read', [AdminController::class, 'markAsRead']);
});