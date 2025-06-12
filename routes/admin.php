<?php

use App\Http\Controllers\Auth\AdminAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController; 
use App\Http\Controllers\Admin\ProductController;

Route::post('/login', [AdminAuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'auth.source:admin'])->group(function () {
    Route::get('/me', [AdminAuthController::class, 'me']);
    
    /* Categories */
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/search', [CategoryController::class, 'ayncSelectSearch']);
    
    /* Brands */
    Route::get('/brands/search', [BrandController::class, 'ayncSelectSearch']);

    /* Products */
    Route::get('/products', [ProductController::class, 'index']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::get('/products/{product_id}/edit', [ProductController::class, 'edit']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    
    
});


Route::get('/brands', [BrandController::class, 'index']);

