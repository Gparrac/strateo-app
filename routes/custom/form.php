<?php

use App\Http\Controllers\ExtraContent\CityServer;
use Illuminate\Support\Facades\Route;
// use App\Http\Middleware\CRUD\CompanyParameterizationMiddleware;
use App\Http\Controllers\CRUD\CompanyParameterization;
use App\Http\Controllers\CRUD\RoleParameterization;
use App\Http\Controllers\CRUD\OfficeParameterization;
use App\Http\Controllers\CRUD\ClientParameterization;
use App\Http\Controllers\CRUD\UserParameterization;
use App\Http\Controllers\ExtraContent\FormServer;
use App\Http\Controllers\ExtraContent\PermissionServer;
use App\Http\Controllers\ExtraContent\TypedocumentUserServer;

Route::group(['middleware' => ['auth:api', 'role.user']], function() {
    Route::match(['get', 'post', 'put', 'delete'],'/company-parameterization', CompanyParameterization::class);
    Route::match(['get', 'post', 'put', 'delete'],'/office', OfficeParameterization::class);
    Route::match(['get', 'post', 'put', 'delete'],'/client', ClientParameterization::class);
});
Route::match(['get', 'post', 'put', 'delete'],'/role-parameterization', RoleParameterization::class);
Route::match(['get', 'post', 'put', 'delete'],'/user-parameterization', UserParameterization::class)->middleware('user.parameterization');;

Route::middleware('validate.name.invoke')->get('/cities', CityServer::class);
Route::get('/type-document-user', TypedocumentUserServer::class);
Route::get('/forms', FormServer::class);
Route::get('/permissions', PermissionServer::class);
