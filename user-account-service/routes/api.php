<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FamilyMemberController;
use App\Http\Controllers\InternalController;
use App\Http\Controllers\UserController;

// ==========================================
// 1. ENDPOINT AUTENTIKASI (PUBLIC & PRIVATE)
// ==========================================
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});

// ==========================================
// 2. ENDPOINT INTERNAL (Dipanggil oleh Gateway/Service Lain)
// ==========================================
Route::get('users/{id}', [InternalController::class, 'showUser']);
Route::get('roles', [InternalController::class, 'getRoles']);

// ==========================================
// 3. ENDPOINT USER / JEMAAT (Wajib Login)
// ==========================================
Route::prefix('user')->middleware('auth:api')->group(function () {
    // Profil & Keamanan Akun
    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);
    Route::put('password', [ProfileController::class, 'updatePassword']); // <-- TAMBAHAN: Route Ubah Password
    
    // Anggota Keluarga
    Route::get('family-members', [FamilyMemberController::class, 'index']);
    Route::post('family-members', [FamilyMemberController::class, 'store']);
    Route::put('family-members/{id}', [FamilyMemberController::class, 'update']);
    Route::delete('family-members/{id}', [FamilyMemberController::class, 'destroy']);

    // Manajemen Jemaat (Versi Profil)
    Route::get('jemaat', [ProfileController::class, 'getAllJemaat']);
    Route::post('jemaat', [ProfileController::class, 'storeJemaat']);
    Route::put('jemaat/{id}', [ProfileController::class, 'updateJemaat']);
    Route::delete('jemaat/{id}', [ProfileController::class, 'deleteJemaat']);
});

// ==========================================
// 4. ENDPOINT ADMIN KHUSUS (Wajib Login)
// ==========================================
// PERBAIKAN: Ubah prefix dari 'admin' menjadi 'user/admin' agar Gateway mengarahkannya ke Port 8001
Route::prefix('user/admin')->middleware('auth:api')->group(function () {
    Route::get('users', [UserController::class, 'getAllUsers']);
    Route::put('users/{id}/role', [UserController::class, 'updateUserRole']);
});