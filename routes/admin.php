<?php

use App\Http\Controllers\Auth\AdminAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;

Route::post('/login', [AdminAuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'auth.source:admin'])->group(function () {
    Route::get('/me', [AdminAuthController::class, 'me']);
    
    /* Categories */
    Route::get('/categories', [CategoryController::class, 'index']);
    
    /* Brands */
    
    
});

Route::get('/brands', [BrandController::class, 'index']);

