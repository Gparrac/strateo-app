<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;

Route::group(['prefix' => 'auth'], function() {
    Route::post('/login', [AuthController::class, 'login'])->middleware('data.login');

    Route::group(['middleware' => 'auth:api'], function() {
        Route::get('/logout', [AuthController::class, 'logout']);
    });
});