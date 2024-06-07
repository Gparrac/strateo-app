<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\RecoveryPasswordController;

Route::group(['prefix' => 'password'], function(){
    Route::post('/email', [RecoveryPasswordController::class, 'sendResetLinkEmail'])->middleware('recovery.password.send.email');
    Route::post('/reset', 'Auth\ResetPasswordController@reset')->name('recovery.password.reset');
});
