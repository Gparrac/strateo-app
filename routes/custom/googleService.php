<?php

use App\Http\Controllers\Auth\GoogleController;
use Illuminate\Support\Facades\Route;
Route::group(['prefix' => 'google','middleware'=>['web']], function(){
    Route::get('/redirect', [GoogleController::class, 'redirect']);
    Route::get('/callback', [GoogleController::class, 'callback']);
});
