<?php

use App\Http\Controllers\Frontend\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
});

/*========================================= Frontend routes ==========================================*/
Route::get('products/{slug}', [Product::class, 'productOverview']);
/*========================================= Frontend routes end ==========================================*/

/*========================================= Admin routes ==========================================*/
Route::prefix('admin')->group(base_path('routes/admin.php'));
/*========================================= Admin routes end ==========================================*/
