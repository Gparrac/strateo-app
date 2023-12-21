<?php

use App\Http\Controllers\ExtraContent\CheckEnterpriseCreation;
use App\Http\Controllers\ExtraContent\CityServer;
use App\Http\Controllers\ExtraContent\FormsServer;
use App\Http\Controllers\ExtraContent\PermissionServer;
use App\Http\Controllers\ExtraContent\TypedocumentUserServer;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;
Route::middleware('validate.name.invoke')->get('/cities', CityServer::class);
Route::group(['middleware' => ['auth:api']], function() {
    Route::get('/check-enterprise', CheckEnterpriseCreation::class)->Middleware('role.user');
    Route::get('/forms', FormsServer::class);
});
Route::get('/permissions', PermissionServer::class);
Route::get('/type-document-user', TypedocumentUserServer::class);
