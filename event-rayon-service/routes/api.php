<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RayonController;
use App\Http\Controllers\ScheduleController;

// --- RUTE PUBLIK (Dapat diakses Web/Flutter tanpa Token) ---
Route::prefix('public')->group(function () {
    Route::get('worship-schedules', [ScheduleController::class, 'getPublicWorshipSchedules']);
    Route::get('activity-schedules', [ScheduleController::class, 'getPublicActivitySchedules']);
});

// --- RUTE PRIVAT (Wajib Token JWT dari API Gateway) ---
Route::prefix('event')->middleware('auth.jwt')->group(function () {
    
    // Manajemen Master Rayon (Dari RayonController)
    Route::get('rayons', [RayonController::class, 'index']);
    Route::post('rayons', [RayonController::class, 'store']);
    Route::get('rayons/{id}', [RayonController::class, 'show']);
    Route::put('rayons/{id}', [RayonController::class, 'update']);
    Route::delete('rayons/{id}', [RayonController::class, 'destroy']);
    Route::post('rayons/{id}/members', [RayonController::class, 'addMember']);

    // Manajemen Jadwal Ibadah Raya (Tampilan)
    Route::get('worship-schedules', [ScheduleController::class, 'getWorshipSchedules']);
    Route::get('worship-schedules/{id}', [ScheduleController::class, 'getWorshipScheduleById']);

    // Manajemen Jadwal Rayon (Kelola: Ketua Rayon)
    Route::get('rayon-schedules', [ScheduleController::class, 'getRayonSchedules']);
    Route::get('rayon-schedules/{id}', [ScheduleController::class, 'getRayonScheduleById']);
    Route::post('rayon-schedules', [ScheduleController::class, 'storeRayonSchedule']);
    Route::put('rayon-schedules/{id}', [ScheduleController::class, 'updateRayonSchedule']);
    Route::delete('rayon-schedules/{id}', [ScheduleController::class, 'destroyRayonSchedule']);
});