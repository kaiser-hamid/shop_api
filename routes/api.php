<?php

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Frontend\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
});

/*========================================= Frontend routes ==========================================*/
Route::get('products/{slug}', [ProductController::class, 'productOverview']);
Route::get('products/additional-info/{id}', [ProductController::class, 'productAdditionalInfo']);

//Checkout APIs
Route::get('data/cities-with-areas', [CommonController::class, 'cityAreaList']);
/*========================================= Frontend routes end ==========================================*/

/*========================================= Admin routes ==========================================*/
Route::prefix('admin')->group(base_path('routes/admin.php'));
/*========================================= Admin routes end ==========================================*/
