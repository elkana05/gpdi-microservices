<?php

use App\Http\Controllers\GatewayController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (tanpa auth)
|--------------------------------------------------------------------------
*/
Route::prefix('public')->group(function () {
    Route::any('/{any}', [GatewayController::class, 'handle'])
        ->where('any', '.*');
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    // login tanpa token
    Route::post('/login', [GatewayController::class, 'handle']);

    // butuh token
    Route::middleware(['gateway.auth', 'gateway.context'])->group(function () {
        Route::get('/me', [GatewayController::class, 'handle']);
        Route::post('/logout', [GatewayController::class, 'handle']);
    });
});

/*
|--------------------------------------------------------------------------
| Private Routes (butuh token)
|--------------------------------------------------------------------------
*/
Route::middleware(['gateway.auth', 'gateway.context'])->group(function () {

    Route::prefix('user')->group(function () {
        Route::any('/{any}', [GatewayController::class, 'handle'])
            ->where('any', '.*');
    });

    Route::prefix('users')->group(function () {
        Route::any('/{any}', [GatewayController::class, 'handle'])
            ->where('any', '.*');
    });

    Route::prefix('family-members')->group(function () {
        Route::any('/{any}', [GatewayController::class, 'handle'])
            ->where('any', '.*');
    });

    Route::prefix('content')->group(function () {
        Route::any('/{any}', [GatewayController::class, 'handle'])
            ->where('any', '.*');
    });

    Route::prefix('event')->group(function () {
        Route::any('/{any}', [GatewayController::class, 'handle'])
            ->where('any', '.*');
    });

    Route::prefix('admin')->group(function () {
        Route::any('/{any}', [GatewayController::class, 'handle'])
            ->where('any', '.*');
    });
});

/*
|--------------------------------------------------------------------------
| Fallback
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->json([
        'status' => 'error',
        'message' => 'Resource not found',
    ], 404);
});