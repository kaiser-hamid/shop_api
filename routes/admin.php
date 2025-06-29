<?php

use App\Http\Controllers\Auth\AdminAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController; 
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;

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

    /* Orders */
    Route::get('/orders', [OrderController::class, 'index']);

    /* Product variants */
    Route::get('/product-variants/{product_id}', [ProductController::class, 'getProductVariants']);
    Route::post('/product-variants/{product}', [ProductController::class, 'storeProductVariant']);
    Route::patch('/product-variants/{productVariant}', [ProductController::class, 'updateProductVariant']);
    Route::delete('/product-variants/{productVariant}', [ProductController::class, 'deleteProductVariant']);
    
    
});


Route::get('/brands', [BrandController::class, 'index']);

