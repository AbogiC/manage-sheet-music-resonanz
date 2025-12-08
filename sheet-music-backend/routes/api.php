<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SheetMusicController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Sheet music public routes
Route::get('/sheet-music', [SheetMusicController::class, 'index']);
Route::get('/sheet-music/filters', [SheetMusicController::class, 'getFilters']);
Route::get('/sheet-music/stats', [SheetMusicController::class, 'getStats']);
Route::get('/sheet-music/{id}', [SheetMusicController::class, 'show']);
Route::get('/sheet-music/{id}/download', [SheetMusicController::class, 'download']);

// Public category routes (needed for upload form)
Route::get('/categories/grouped', [CategoryController::class, 'getGrouped']);

// Upload routes (public for testing, should be protected in production)
Route::post('/upload/chunk', [UploadController::class, 'uploadChunk']);
Route::post('/upload/cancel', [UploadController::class, 'cancelUpload']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Sheet music protected routes
    Route::post('/sheet-music', [SheetMusicController::class, 'store']);
    Route::put('/sheet-music/{id}', [SheetMusicController::class, 'update']);
    Route::delete('/sheet-music/{id}', [SheetMusicController::class, 'destroy']);
    Route::get('/my-sheets', [SheetMusicController::class, 'mySheets']);
    Route::get('/sheet-music/{id}/events', [SheetMusicController::class, 'getEvents']);

    // Event routes
    Route::apiResource('events', \App\Http\Controllers\Api\EventController::class)->middleware('auth:sanctum');
    Route::post('/events/{eventId}/sheet-music', [\App\Http\Controllers\Api\EventController::class, 'addSheetMusic'])->middleware('auth:sanctum');
    Route::delete('/events/{eventId}/sheet-music/{sheetMusicId}', [\App\Http\Controllers\Api\EventController::class, 'removeSheetMusic'])->middleware('auth:sanctum');
    Route::put('/events/{eventId}/sheet-music/{sheetMusicId}', [\App\Http\Controllers\Api\EventController::class, 'updateSheetMusic'])->middleware('auth:sanctum');

    // Category routes
    Route::apiResource('categories', CategoryController::class);
});
