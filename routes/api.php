<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function(){
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::middleware('auth:api')->group(function(){
        Route::controller(AuthController::class)->group(function(){
            Route::post('logout', 'logout');
        });
    });
});
