<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Middleware\CRUD\CompanyParameterizationMiddleware;
use App\Http\Controllers\CRUD\CompanyParameterization;
use App\Http\Controllers\CRUD\RoleParameterization;
use App\Http\Controllers\CRUD\OfficeParameterization;
use App\Http\Controllers\CRUD\ClientParameterization;
use App\Http\Controllers\CRUD\UserParameterization;

// Route::group(['middleware' => 'auth:api'], function() {
Route::match(['get', 'post', 'put', 'delete'],'/company-parameterization', CompanyParameterization::class);
Route::match(['get', 'post', 'put', 'delete'],'/office', OfficeParameterization::class);
Route::match(['get', 'post', 'put', 'delete'],'/client', ClientParameterization::class);
// });
Route::match(['get', 'post', 'put', 'delete'],'/user-parameterization', UserParameterization::class);
Route::match(['get', 'post', 'put', 'delete'],'/role-parameterization', RoleParameterization::class);
