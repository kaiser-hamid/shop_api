<?php

use App\Http\Controllers\Auth\AdminAuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AdminAuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'auth.source:admin'])->group(function () {
    Route::get('/me', [AdminAuthController::class, 'me']);
});

