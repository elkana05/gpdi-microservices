<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GatewayController;

// 1. Rute ke User & Account Service (Port 8001)
Route::any('/auth/{path}', function ($path) {
    return app(GatewayController::class)->forwardRequest(request(), 'user');
})->where('path', '.*');

Route::any('/user/{path}', function ($path) {
    return app(GatewayController::class)->forwardRequest(request(), 'user');
})->where('path', '.*');

// 2. Rute Publik (Terbagi ke Event dan Content)
// Arahkan spesifik jadwal ibadah dan kegiatan ke Event Service (Port 8003)
Route::any('/public/worship-schedules', function () {
    return app(GatewayController::class)->forwardRequest(request(), 'event');
});

Route::any('/public/activity-schedules', function () {
    return app(GatewayController::class)->forwardRequest(request(), 'event');
});

// Sisa rute publik lainnya diarahkan ke Content Service (Port 8002)
Route::any('/public/{path}', function ($path) {
    return app(GatewayController::class)->forwardRequest(request(), 'content');
})->where('path', '.*');

// 3. Rute ke Event & Rayon Service (Port 8003)
Route::any('/event/{path}', function ($path) {
    return app(GatewayController::class)->forwardRequest(request(), 'event');
})->where('path', '.*');

// 4. Rute ke Administration & Utility Service (Port 8004)
Route::any('/admin/{path}', function ($path) {
    return app(GatewayController::class)->forwardRequest(request(), 'admin');
})->where('path', '.*');

// Tambahkan ini di API Gateway Anda
Route::any('/content/{path}', function ($path) {
    return app(\App\Http\Controllers\GatewayController::class)->forwardRequest(request(), 'content');
})->where('path', '.*');