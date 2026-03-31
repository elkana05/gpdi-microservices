<?php

use Illuminate\Support\Facades\Route;
<<<<<<< Updated upstream
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
=======
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\WorshipScheduleController;
use App\Http\Controllers\RayonScheduleController;

/*
|--------------------------------------------------------------------------
| API Routes untuk Event & Rayon Service
|--------------------------------------------------------------------------
| Semua prefix '/api' sudah otomatis ditambahkan oleh Laravel jika
| diletakkan di dalam file routes/api.php ini.
*/

// 1. ENDPOINT PUBLIK (Tanpa perlu Header JWT / Login)
Route::prefix('public')->group(function () {
    Route::get('/worship-schedules', [PublicEventController::class, 'getWorshipSchedules']);
    Route::get('/activity-schedules', [PublicEventController::class, 'getActivitySchedules']);
});

// 2. ENDPOINT PRIVATE / INTERNAL (Setelah Login)
// Catatan: Autentikasi JWT dilakukan di API Gateway.
// Service ini hanya menerima request yang diteruskan oleh Gateway beserta Header X-User-Role.
Route::prefix('event')->group(function () {
    
    // Manajemen Ibadah Raya (Hanya Read)
    Route::get('/worship-schedules', [WorshipScheduleController::class, 'index']);
    Route::get('/worship-schedules/{id}', [WorshipScheduleController::class, 'show']);

    // Manajemen Ibadah Rayon (CRUD)
    Route::get('/rayon-schedules', [RayonScheduleController::class, 'index']);
    Route::get('/rayon-schedules/{id}', [RayonScheduleController::class, 'show']);
    Route::post('/rayon-schedules', [RayonScheduleController::class, 'store']); // Validasi Ketua Rayon di Controller
    Route::put('/rayon-schedules/{id}', [RayonScheduleController::class, 'update']); // Validasi Ketua Rayon di Controller
    Route::delete('/rayon-schedules/{id}', [RayonScheduleController::class, 'destroy']); // Validasi Ketua Rayon di Controller

>>>>>>> Stashed changes
});