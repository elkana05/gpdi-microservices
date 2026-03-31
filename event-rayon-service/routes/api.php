<?php

use Illuminate\Support\Facades\Route;
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

});