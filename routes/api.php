<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\MainDashboardController; // Tambahkan ini
use App\Http\Controllers\AuthController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::post('login', [AuthController::class, 'login']);


Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    // Dashboard API Routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/lydyly2', [MainDashboardController::class, 'index']);
        
        // Produk API Routes
        Route::prefix('produk')->group(function () {
            Route::get('/', [ProdukController::class, 'index']);
            Route::get('/{ID}', [ProdukController::class, 'show']);
            Route::post('/', [ProdukController::class, 'store']);
            Route::post('/{ID}', [ProdukController::class, 'update']);
            Route::delete('/{ID}', [ProdukController::class, 'destroy']);
        });
        
    });
    
});