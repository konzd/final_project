<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PelangganDataController;
use App\Http\Controllers\PenyewaanController;
use App\Http\Controllers\PenyewaanDetailController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AdminAuthController::class, 'register']);
Route::post('/login', [AdminAuthController::class, 'login'])->name('login');
Route::post('/reset-password', [AdminAuthController::class, 'resetPassword']);

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('/kategori', KategoriController::class);
    Route::apiResource('/penyewaan', PenyewaanController::class);
    Route::apiResource('/pelanggan', PelangganController::class);
    Route::apiResource('/alat', AlatController::class);
    Route::apiResource('/data/pelanggan', PelangganDataController::class);
    Route::apiResource('/detail/penyewaan', PenyewaanDetailController::class);
    
    Route::get('/me', [AdminAuthController::class, 'me']);
    Route::post('/logout', [AdminAuthController::class, 'logout']);
});

