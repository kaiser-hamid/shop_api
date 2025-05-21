<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
});

//Admin routes
Route::prefix('admin')->group(base_path('routes/admin.php'));
