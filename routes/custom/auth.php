<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;

Route::group(['prefix' => 'auth',], function() {
    Route::post('/login', [AuthController::class, 'login'])->middleware(['recaptcha.v3', 'data.login']);

    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('/user', [AuthController::class, 'user']);
        Route::get('/logout', [AuthController::class, 'logout']);
        Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('data.change.password');
    });
});
