<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;

Route::group(['prefix' => 'auth'], function() {
    Route::post('/signup', [AuthController::class, 'signup'])->middleware('data_register');
    Route::post('/login', [AuthController::class, 'login'])->middleware('data_login');

    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('/logout', [AuthController::class, 'logout']);
    });
});