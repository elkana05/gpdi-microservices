<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ScheduleController;
use App\Http\Middleware\JwtMiddleware; // PERBAIKAN 1: Wajib diimpor

/*
|--------------------------------------------------------------------------
| API Routes untuk Event Service
|--------------------------------------------------------------------------
*/

Route::prefix('event')->group(function () {

    // ---------------------------------------------------------
    // RUTE PUBLIK (Tanpa Middleware Auth)
    // ---------------------------------------------------------
    Route::get('worship', [ScheduleController::class, 'getPublicWorshipSchedules']);
    Route::get('activity', [ScheduleController::class, 'getPublicActivitySchedules']);

    // ---------------------------------------------------------
    // RUTE KHUSUS JEMAAT (Membutuhkan Login / Token Auth)
    // ---------------------------------------------------------
    // PERBAIKAN 2: Bungkus dengan JwtMiddleware
    Route::prefix('jemaat')->middleware(JwtMiddleware::class)->group(function () {
        Route::get('rayon-schedules', [ScheduleController::class, 'getJadwalRayonJemaat']);
    });

    // ---------------------------------------------------------
    // RUTE KHUSUS KETUA RAYON (Manajemen Ibadah Rayon)
    // ---------------------------------------------------------
    // PERBAIKAN BARU: Rute ini dipanggil oleh ManajemenIbadahPage.jsx
    Route::middleware(JwtMiddleware::class)->group(function () {
        Route::get('rayon-schedules/me', [ScheduleController::class, 'getJadwalByKetuaRayon']);
        Route::post('rayon-schedules', [ScheduleController::class, 'storeRayonSchedule']);
        Route::put('rayon-schedules/{id}', [ScheduleController::class, 'updateRayonSchedule']);
        Route::delete('rayon-schedules/{id}', [ScheduleController::class, 'destroyRayonSchedule']);
    });

    // ---------------------------------------------------------
    // RUTE ADMIN (Digunakan oleh Dasbor Pendeta / Admin / Ketua Rayon)
    // ---------------------------------------------------------
    // PERBAIKAN 3: Bungkus dengan JwtMiddleware
    Route::prefix('admin')->middleware(JwtMiddleware::class)->group(function () {
        
        // --- Manajemen Rayon ---
        Route::get('rayon', [EventController::class, 'getAllRayon']);
        Route::post('rayon', [EventController::class, 'storeRayon']);
        Route::put('rayon/{id}', [EventController::class, 'updateRayon']);
        Route::delete('rayon/{id}', [EventController::class, 'deleteRayon']);

        // --- Manajemen Jadwal Ibadah Rutin ---
        Route::get('worship', [EventController::class, 'getAllWorship']);
        Route::post('worship', [EventController::class, 'storeWorship']);
        Route::put('worship/{id}', [EventController::class, 'updateWorship']);
        Route::delete('worship/{id}', [EventController::class, 'deleteWorship']);

        // --- Manajemen Kegiatan Khusus ---
        Route::get('activity', [EventController::class, 'getAllActivity']);
        Route::post('activity', [EventController::class, 'storeActivity']);
        Route::put('activity/{id}', [EventController::class, 'updateActivity']);
        Route::delete('activity/{id}', [EventController::class, 'deleteActivity']);

        // --- Manajemen Jadwal Rayon ---
        // PERBAIKAN 4: Diarahkan ke ScheduleController dan nama fungsi disesuaikan
        Route::get('rayon-schedule', [ScheduleController::class, 'getRayonSchedules']);
        Route::post('rayon-schedule', [ScheduleController::class, 'storeRayonSchedule']);
        Route::put('rayon-schedule/{id}', [ScheduleController::class, 'updateRayonSchedule']);
        Route::delete('rayon-schedule/{id}', [ScheduleController::class, 'destroyRayonSchedule']);
        
    });

});