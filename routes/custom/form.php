<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Middleware\CRUD\CompanyParameterizationMiddleware;
use App\Http\Controllers\CRUD\CompanyParameterization;
use App\Http\Controllers\CRUD\Office;
use App\Http\Controllers\CRUD\UserParameterization;

Route::group(['middleware' => 'auth:api'], function() {
    Route::match(['get', 'post', 'put', 'delete'],'/company-parameterization', CompanyParameterization::class)
        ->middleware('company_parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/office', Office::class)
        ->middleware('office');
});
Route::match(['get', 'post', 'put', 'delete'],'/user-parameterization', UserParameterization::class);
