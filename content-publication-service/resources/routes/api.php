<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ContentController;

/*
|--------------------------------------------------------------------------
| Endpoint Publik (Tanpa Login)
|--------------------------------------------------------------------------
*/
Route::prefix('public')->group(function () {
    Route::get('homepage', [PublicController::class, 'homepage']);
    Route::get('church-profile', [PublicController::class, 'churchProfile']);
    Route::get('service-information', [PublicController::class, 'serviceInformation']);
    Route::get('galleries', [PublicController::class, 'galleries']);
    Route::get('contact-location', [PublicController::class, 'contactLocation']);
    
    // Pengumuman yang status=published, visibility=public, author_role=pendeta
    Route::get('announcements', [PublicController::class, 'announcements']);
    Route::get('announcements/{id}', [PublicController::class, 'showAnnouncement']);
});

/*
|--------------------------------------------------------------------------
| Endpoint Privat (Wajib Login via API Gateway)
|--------------------------------------------------------------------------
*/
Route::prefix('content')->middleware('auth.jwt')->group(function () {
    // Manajemen Pengumuman
    Route::get('announcements', [ContentController::class, 'indexAnnouncement']);
    Route::get('announcements/{id}', [ContentController::class, 'showAnnouncement']);
    Route::post('announcements', [ContentController::class, 'storeAnnouncement']);
    Route::put('announcements/{id}', [ContentController::class, 'updateAnnouncement']);
    Route::delete('announcements/{id}', [ContentController::class, 'destroyAnnouncement']);

    // Manajemen Renungan Harian (Khusus Pendeta)
    Route::get('devotionals', [ContentController::class, 'indexDevotional']);
    Route::get('devotionals/{id}', [ContentController::class, 'showDevotional']);
    Route::post('devotionals', [ContentController::class, 'storeDevotional']);
    Route::put('devotionals/{id}', [ContentController::class, 'updateDevotional']);
    Route::delete('devotionals/{id}', [ContentController::class, 'destroyDevotional']);
});