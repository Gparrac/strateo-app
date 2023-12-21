<?php

use App\Http\Controllers\ExtraContent\CityServer;
use Illuminate\Support\Facades\Route;
// use App\Http\Middleware\CRUD\CompanyParameterizationMiddleware;
use App\Http\Controllers\CRUD\EnterpriseParameterization;
use App\Http\Controllers\CRUD\RoleParameterization;
use App\Http\Controllers\CRUD\OfficeParameterization;
use App\Http\Controllers\CRUD\ClientParameterization;
use App\Http\Controllers\CRUD\UserParameterization;

Route::group(['middleware' => ['auth:api','role.user']], function() {
    Route::match(['get', 'post', 'put', 'delete'],'/enterprise-parameterization', EnterpriseParameterization::class)->middleware('enterprise.parameterization');
    Route::match(['get', 'post', 'put', 'delete'], '/office-parameterization', OfficeParameterization::class)->middleware('office.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/client-parameterization', ClientParameterization::class);

    Route::match(['get', 'post', 'put', 'delete'],'/role-parameterization', RoleParameterization::class)->middleware('role.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/user-parameterization', UserParameterization::class)->middleware('user.parameterization');
});
