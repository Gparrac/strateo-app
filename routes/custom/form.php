<?php

use App\Http\Controllers\ExtraContent\CityServer;
use Illuminate\Support\Facades\Route;
// use App\Http\Middleware\CRUD\CompanyParameterizationMiddleware;
use App\Http\Controllers\CRUD\EnterpriseParameterization;
use App\Http\Controllers\CRUD\RoleParameterization;
use App\Http\Controllers\CRUD\OfficeParameterization;
use App\Http\Controllers\CRUD\ClientParameterization;
use App\Http\Controllers\CRUD\UserParameterization;
use App\Http\Controllers\CRUD\ServiceParameterization;
use App\Http\Controllers\CRUD\FieldParameterization;
use App\Http\Controllers\CRUD\SupplierParameterization;

Route::group(['middleware' => ['auth:api','role.user']], function() {
    Route::match(['get', 'post', 'put', 'delete'],'/enterprise-parameterization', EnterpriseParameterization::class)->middleware('enterprise.parameterization');
    Route::match(['get', 'post', 'put', 'delete'], '/office-parameterization', OfficeParameterization::class)->middleware('office.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/client-parameterization', ClientParameterization::class)->middleware('client.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/user-parameterization', UserParameterization::class)->middleware('user.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/service-parameterization', ServiceParameterization::class)->middleware('service.parameterization');
    //Route::match(['get', 'post', 'put', 'delete'],'/supplier-parameterization', RoleParameterization::class)->middleware('supplier.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/field-parameterization', FieldParameterization::class)->middleware('field.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/supplier-parameterization', SupplierParameterization::class)->middleware('supplier.parameterization');
    Route::match(['get', 'post', 'put', 'delete'],'/role-parameterization', RoleParameterization::class)->middleware('role.parameterization');
});
