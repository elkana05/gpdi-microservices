<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\InternalController;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});

Route::prefix('user')->middleware('auth:api')->group(function () {
    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);
    
    Route::apiResource('family-members', FamilyMemberController::class)->except(['show']);
});

Route::get('users/{id}', [InternalController::class, 'showUser']);
Route::get('roles', [InternalController::class, 'getRoles']);